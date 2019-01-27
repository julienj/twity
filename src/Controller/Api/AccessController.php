<?php

namespace App\Controller\Api;

use App\Document\Access;
use App\Document\Application;
use Doctrine\ODM\MongoDB\DocumentManager;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use Swagger\Annotations as SWG;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Provide routes for access API.
 *
 * @Route("/api/applications/{id}/access", name="api_access_", defaults={"_format": "json"})
 */
class AccessController extends AbstractController
{
    /**
     * List access.
     *
     * @Route("", name="index", methods={"GET"})
     * @SWG\Response(
     *     response=200,
     *     description="Returns access",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=Access::class, groups={"access_default", "user_default"}))
     *     )
     * )
     * @SWG\Tag(name="applications")
     * @Security(name="Bearer")
     */
    public function getAll(Application $application): Response
    {
        $this->denyAccessUnlessGranted(Access::ACCESS_OWNER, $application);

        return $this->json($application->getAccesses(), Response::HTTP_OK, [], ['groups' => ['access_default', 'user_default']]);
    }

    /**
     * Create an access.
     *
     * @Route("/{userId}", name="create", methods={"POST"})
     * @SWG\Response(
     *     response=201,
     *     description="Create an access",
     *     @SWG\Schema(
     *         type="object",
     *         ref=@Model(type=Access::class, groups={"access_full", "user_default"})
     *     )
     * )
     * @SWG\Response(
     *     response=400,
     *     description="Invalid access"
     * )
     * @SWG\Parameter(
     *     name="body",
     *     in="body",
     *     description="JSON Payload",
     *     required=true,
     *     format="application/json",
     *     @SWG\Schema(
     *         type="object",
     *         ref=@Model(type=Access::class, groups={"access_write"})
     *     )
     * )
     * @SWG\Tag(name="applications")
     * @Security(name="Bearer")
     */
    public function create(
        Application $application,
        DocumentManager $dm,
        SerializerInterface $serializer,
        ValidatorInterface $validator,
        Request $request,
        string $userId): Response
    {
        $this->denyAccessUnlessGranted(Access::ACCESS_OWNER, $application);

        $data = $request->getContent();

        /** @var Access $access */
        $access = $serializer->deserialize($data, Access::class, 'json', ['groups' => ['access_write']]);

        $user = $dm->getRepository('App:User')->find($userId);

        if (!$user) {
            throw $this->createNotFoundException('invalid User');
        }

        $access->setUser($user);

        $violations = $validator->validate($access);

        if ($violations->count() > 0) {
            return $this->json($violations, Response::HTTP_BAD_REQUEST);
        }

        $application->addAccess($access);
        $dm->flush();

        return $this->json($access, Response::HTTP_CREATED, [], ['groups' => ['access_full', 'user_default']]);
    }

    /**
     * Delete an access.
     *
     * @Route("/{userId}", name="delete", methods={"DELETE"})
     * @SWG\Response(
     *     response=204,
     *     description="Delete an access"
     * )
     * @SWG\Tag(name="applications")
     * @Security(name="Bearer")
     */
    public function delete(DocumentManager $dm, Application $application, string $userId): Response
    {
        $this->denyAccessUnlessGranted(Access::ACCESS_OWNER, $application);

        $application->removeAccess($userId);
        $dm->flush();

        return new Response('', Response::HTTP_NO_CONTENT);
    }
}
