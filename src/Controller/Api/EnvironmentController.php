<?php

namespace App\Controller\Api;

use App\Document\Access;
use App\Document\Application;
use App\Document\Environment;
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
 * Provide routes for environment API.
 *
 * @Route("/api/applications/{id}/environments", name="api_environment_", defaults={"_format": "json"})
 */
class EnvironmentController extends AbstractController
{
    /**
     * List environments for a given application.
     *
     * @Route("", name="index", methods={"GET"})
     * @SWG\Response(
     *     response=200,
     *     description="Returns the environments",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=Environment::class, groups={"environment_full"}))
     *     )
     * )
     * @SWG\Tag(name="environments")
     * @Security(name="Bearer")
     */
    public function list(Application $application): Response
    {
        $this->denyAccessUnlessGranted(Access::ACCESS_USER, $application);

        return $this->json($application->getEnvironments(), Response::HTTP_OK, [], ['groups' => ['environment_full']]);
    }

    /**
     * Create environment.
     *
     * @Route("", name="create", methods={"POST"})
     * @SWG\Response(
     *     response=201,
     *     description="Create environment"
     * )
     * @SWG\Response(
     *     response=400,
     *     description="Invalid environment"
     * )
     * @SWG\Parameter(
     *     name="body",
     *     in="body",
     *     description="JSON Payload",
     *     required=true,
     *     format="application/json",
     *     @SWG\Schema(
     *         type="object",
     *         ref=@Model(type=Environment::class, groups={"environment_write"})
     *     )
     * )
     * @SWG\Tag(name="environments")
     * @Security(name="Bearer")
     */
    public function create(
        Application $application,
        DocumentManager $dm,
        SerializerInterface $serializer,
        ValidatorInterface $validator,
        Request $request): Response
    {
        $this->denyAccessUnlessGranted(Access::ACCESS_MASTER, $application);

        $data = $request->getContent();

        /** @var Environment $environment */
        $environment = $serializer->deserialize($data, Environment::class, 'json', ['groups' => ['environment_write']]);

        $violations = $validator->validate($environment);

        if ($violations->count() > 0) {
            return $this->json($violations, Response::HTTP_BAD_REQUEST);
        }

        $environment->setApplication($application);
        $dm->persist($environment);
        $dm->flush();

        return new Response('', Response::HTTP_CREATED);
    }

    /**
     * update environment.
     *
     * @Route("/{environmentId}", name="update", methods={"PUT"})
     * @SWG\Response(
     *     response=204,
     *     description="Update environment"
     * )
     * @SWG\Response(
     *     response=400,
     *     description="Invalid environment"
     * )
     * @SWG\Parameter(
     *     name="body",
     *     in="body",
     *     description="JSON Payload",
     *     required=true,
     *     format="application/json",
     *     @SWG\Schema(
     *         type="object",
     *         ref=@Model(type=Environment::class, groups={"environment_write"})
     *     )
     * )
     * @SWG\Tag(name="environments")
     * @Security(name="Bearer")
     */
    public function update(
        Application $application,
        string $environmentId,
        DocumentManager $dm,
        SerializerInterface $serializer,
        ValidatorInterface $validator,
        Request $request): Response
    {
        $this->denyAccessUnlessGranted(Access::ACCESS_MASTER, $application);

        $data = $request->getContent();

        /** @var Environment $environment */
        $environment = $dm->getRepository('App:Environment')->find($environmentId);

        if (!$environment || $environment->getApplication() !== $application) {
            throw $this->createNotFoundException();
        }

        $serializer->deserialize($data, Environment::class, 'json', [
            'groups' => ['environment_write'],
            'object_to_populate' => $environment,
        ]);

        $violations = $validator->validate($environment);

        if ($violations->count() > 0) {
            return $this->json($violations, Response::HTTP_BAD_REQUEST);
        }

        $dm->flush();

        return new Response('', Response::HTTP_NO_CONTENT);
    }

    /**
     * Delete environment.
     *
     * @Route("/{environmentId}", name="delete", methods={"DELETE"})
     * @SWG\Response(
     *     response=204,
     *     description="Delete environment"
     * )
     * @SWG\Tag(name="environments")
     * @Security(name="Bearer")
     */
    public function delete(DocumentManager $dm, Application $application, string $environmentId): Response
    {
        $this->denyAccessUnlessGranted(Access::ACCESS_MASTER, $application);

        /** @var Environment $environment */
        $environment = $dm->getRepository('App:Environment')->find($environmentId);

        if (!$environment || $environment->getApplication() !== $application) {
            throw $this->createNotFoundException();
        }

        $dm->remove($environment);
        $dm->flush();

        return new Response('', Response::HTTP_NO_CONTENT);
    }

    /**
     * Regenerate environment key.
     *
     * @Route("/{environmentId}/regenerate-token", name="regenerate_token", methods={"POST"})
     * @SWG\Response(
     *     response=201,
     *     description="Regenerate environment key",
     * )
     * @SWG\Tag(name="environments")
     * @Security(name="Bearer")
     */
    public function regenerateToken(Application $application, DocumentManager $dm, string $environmentId): Response
    {
        $this->denyAccessUnlessGranted(Access::ACCESS_MASTER, $application);

        /** @var Environment $environment */
        $environment = $dm->getRepository('App:Environment')->find($environmentId);

        if (!$environment || $environment->getApplication() !== $application) {
            throw $this->createNotFoundException();
        }

        $environment->generateToken();
        $dm->flush();

        return new Response('', Response::HTTP_CREATED);
    }
}
