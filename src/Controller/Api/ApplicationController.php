<?php

namespace App\Controller\Api;

use App\Document\Access;
use App\Document\Application;
use App\Document\User;
use Doctrine\ODM\MongoDB\DocumentManager;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Swagger\Annotations as SWG;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Provide routes for application API.
 *
 * @Route("/api/applications", name="api_application_", defaults={"_format": "json"})
 */
class ApplicationController extends AbstractController
{
    /**
     * List applications.
     *
     * @Route("", name="index", methods={"GET"})
     * @SWG\Response(
     *     response=200,
     *     description="Returns the applications",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=Application::class, groups={"application_default"}))
     *     )
     * )
     * @SWG\Parameter(
     *     name="page",
     *     in="query",
     *     type="integer",
     *     description="Page number",
     *     default="1"
     * )
     * @SWG\Parameter(
     *     name="query",
     *     in="query",
     *     type="string",
     *     description="Search query",
     * )
     * @SWG\Tag(name="applications")
     * @Security(name="Bearer")
     */
    public function list(DocumentManager $dm, Request $request): Response
    {
        $page = $request->query->getInt('page', 1);
        $query = $request->query->get('query');

        $rs = [
            'total' => $dm->getRepository('App:Application')->count($this->getUser(), $query),
            'items' => $dm->getRepository('App:Application')->search($this->getUser(), $page, $query, 20),
        ];

        return $this->json($rs, Response::HTTP_OK, [], ['groups' => ['application_default']]);
    }

    /**
     * Get an application.
     *
     * @Route("/{id}", name="get", methods={"GET"})
     * @SWG\Response(
     *     response=200,
     *     description="Returns the application",
     *     @SWG\Schema(ref=@Model(type=Application::class, groups={"application_full"})
     *     )
     * )
     * @SWG\Tag(name="applications")
     * @Security(name="Bearer")
     */
    public function find(Application $application): Response
    {
        $this->denyAccessUnlessGranted(Access::ACCESS_USER, $application);

        if ($this->isGranted([User::ROLE_MANAGER])) {
            $access = Access::ACCESS_OWNER;
        } else {
            $access = $application->getAccess($this->getUser()->getId())->getAccess();
        }

        $data = [
            'item' => $application,
            'access' => $access,
        ];

        return $this->json($data, Response::HTTP_OK, [], ['groups' => ['application_full']]);
    }

    /**
     * Create an application.
     *
     * @Route("", name="create", methods={"POST"})
     * @SWG\Response(
     *     response=201,
     *     description="Create an application",
     *     @SWG\Schema(
     *         type="object",
     *         ref=@Model(type=Application::class, groups={"application_full"})
     *     )
     * )
     * @SWG\Response(
     *     response=400,
     *     description="Invalid application"
     * )
     * @SWG\Parameter(
     *     name="body",
     *     in="body",
     *     description="JSON Payload",
     *     required=true,
     *     format="application/json",
     *     @SWG\Schema(
     *         type="object",
     *         ref=@Model(type=Application::class, groups={"application_write"})
     *     )
     * )
     * @SWG\Tag(name="applications")
     * @Security(name="Bearer")
     *
     * @IsGranted("ROLE_MANAGER")
     */
    public function create(
        DocumentManager $dm,
        SerializerInterface $serializer,
        ValidatorInterface $validator,
        Request $request): Response
    {
        $data = $request->getContent();

        /** @var Application $application */
        $application = $serializer->deserialize($data, Application::class, 'json', ['groups' => ['application_write']]);

        $violations = $validator->validate($application);

        if ($violations->count() > 0) {
            return $this->json($violations, Response::HTTP_BAD_REQUEST);
        }

        $dm->persist($application);
        $dm->flush();

        return $this->json($application, Response::HTTP_CREATED, [], ['groups' => ['application_full']]);
    }

    /**
     * update an application.
     *
     * @Route("/{id}", name="update", methods={"PUT"})
     * @SWG\Response(
     *     response=204,
     *     description="Update an application"
     * )
     * @SWG\Response(
     *     response=400,
     *     description="Invalid application"
     * )
     * @SWG\Parameter(
     *     name="body",
     *     in="body",
     *     description="JSON Payload",
     *     required=true,
     *     format="application/json",
     *     @SWG\Schema(
     *         type="object",
     *         ref=@Model(type=Application::class, groups={"application_write"})
     *     )
     * )
     * @SWG\Tag(name="applications")
     * @Security(name="Bearer")
     */
    public function update(
        Application $application,
        DocumentManager $dm,
        SerializerInterface $serializer,
        ValidatorInterface $validator,
        Request $request): Response
    {
        $this->denyAccessUnlessGranted(Access::ACCESS_OWNER, $application);

        $data = $request->getContent();

        /* @var Application $application */
        $serializer->deserialize($data, Application::class, 'json', [
            'groups' => ['application_write'],
            'object_to_populate' => $application,
        ]);

        $violations = $validator->validate($application);

        if ($violations->count() > 0) {
            return $this->json($violations, Response::HTTP_BAD_REQUEST);
        }

        $dm->flush();

        return new Response('', Response::HTTP_NO_CONTENT);
    }

    /**
     * Delete an application.
     *
     * @Route("/{id}", name="delete", methods={"DELETE"})
     * @SWG\Response(
     *     response=204,
     *     description="Delete an application"
     * )
     * @SWG\Tag(name="applications")
     * @Security(name="Bearer")
     */
    public function delete(DocumentManager $dm, Application $application): Response
    {
        $this->denyAccessUnlessGranted(Access::ACCESS_OWNER, $application);

        $dm->remove($application);
        $dm->flush();

        return new Response('', Response::HTTP_NO_CONTENT);
    }
}
