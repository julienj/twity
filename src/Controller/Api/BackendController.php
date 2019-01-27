<?php

namespace App\Controller\Api;

use App\Document\Backend;
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
 * Provide routes for backend API.
 *
 * @Route("/api/backends", name="api_backend_", defaults={"_format": "json"})
 * @IsGranted("ROLE_ADMIN")
 */
class BackendController extends AbstractController
{
    /**
     * List backend.
     *
     * @Route("", name="index", methods={"GET"})
     * @SWG\Response(
     *     response=200,
     *     description="Returns the backend",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=Backend::class, groups={"backend_default"}))
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
     * @SWG\Tag(name="backend")
     * @Security(name="Bearer")
     */
    public function list(DocumentManager $dm, Request $request): Response
    {
        $page = $request->query->getInt('page', 1);
        $query = $request->query->get('query');

        $rs = [
            'total' => $dm->getRepository('App:Backend')->count($query),
            'items' => $dm->getRepository('App:Backend')->search($page, $query, 20),
        ];

        return $this->json($rs, Response::HTTP_OK, [], ['groups' => ['backend_default']]);
    }

    /**
     * Get a backend.
     *
     * @Route("/{id}", name="get", methods={"GET"})
     * @SWG\Response(
     *     response=200,
     *     description="Returns the backend",
     *     @SWG\Schema(ref=@Model(type=Backend::class, groups={"backend_full"})
     *     )
     * )
     * @SWG\Tag(name="backend")
     * @Security(name="Bearer")
     */
    public function find(Backend $backend): Response
    {
        return $this->json($backend, Response::HTTP_OK, [], ['groups' => ['backend_full']]);
    }

    /**
     * Create a backend.
     *
     * @Route("", name="create", methods={"POST"})
     * @SWG\Response(
     *     response=201,
     *     description="Create a backend"
     * )
     * @SWG\Response(
     *     response=400,
     *     description="Invalid backend",
     *     @SWG\Schema(
     *         type="object",
     *         ref=@Model(type=Backend::class, groups={"backend_full"})
     *     )
     * )
     * @SWG\Parameter(
     *     name="body",
     *     in="body",
     *     description="JSON Payload",
     *     required=true,
     *     format="application/json",
     *     @SWG\Schema(
     *         type="object",
     *         ref=@Model(type=Backend::class, groups={"backend_write"})
     *     )
     * )
     * @SWG\Tag(name="backend")
     * @Security(name="Bearer")
     */
    public function create(
        DocumentManager $dm,
        SerializerInterface $serializer,
        ValidatorInterface $validator,
        Request $request): Response
    {
        $data = $request->getContent();

        /** @var Backend $backend */
        $backend = $serializer->deserialize($data, Backend::class, 'json', ['groups' => ['backend_write']]);

        $violations = $validator->validate($backend);

        if ($violations->count() > 0) {
            return $this->json($violations, Response::HTTP_BAD_REQUEST);
        }

        $dm->persist($backend);
        $dm->flush();

        return $this->json($backend, Response::HTTP_CREATED, [], ['groups' => ['backend_full']]);
    }

    /**
     * update a backend.
     *
     * @Route("/{id}", name="update", methods={"PUT"})
     * @SWG\Response(
     *     response=204,
     *     description="Update a backend"
     * )
     * @SWG\Response(
     *     response=400,
     *     description="Invalid backend"
     * )
     * @SWG\Parameter(
     *     name="body",
     *     in="body",
     *     description="JSON Payload",
     *     required=true,
     *     format="application/json",
     *     @SWG\Schema(
     *         type="object",
     *         ref=@Model(type=Backend::class, groups={"backend_write"})
     *     )
     * )
     * @SWG\Tag(name="backend")
     * @Security(name="Bearer")
     */
    public function update(
        Backend $backend,
        DocumentManager $dm,
        SerializerInterface $serializer,
        ValidatorInterface $validator,
        Request $request): Response
    {
        $data = $request->getContent();

        /* @var Backend $backend */
        $serializer->deserialize($data, Backend::class, 'json', [
            'groups' => ['backend_write'],
            'object_to_populate' => $backend,
        ]);

        $violations = $validator->validate($backend);

        if ($violations->count() > 0) {
            return $this->json($violations, Response::HTTP_BAD_REQUEST);
        }

        $dm->flush();

        return new Response('', Response::HTTP_NO_CONTENT);
    }

    /**
     * Delete a backend.
     *
     * @Route("/{id}", name="delete", methods={"DELETE"})
     * @SWG\Response(
     *     response=204,
     *     description="Delete a backend"
     * )
     * @SWG\Tag(name="backend")
     * @Security(name="Bearer")
     */
    public function delete(DocumentManager $dm, Backend $backend): Response
    {
        $dm->remove($backend);
        $dm->flush();

        return new Response('', Response::HTTP_NO_CONTENT);
    }
}
