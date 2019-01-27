<?php

namespace App\Controller\Api;

use App\Document\Provider;
use App\Messenger\Message\ProviderImportation;
use Doctrine\ODM\MongoDB\DocumentManager;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Swagger\Annotations as SWG;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Provide routes for provider API.
 *
 * @Route("/api/providers", name="api_provider_", defaults={"_format": "json"})
 */
class ProviderController extends AbstractController
{
    /**
     * List the providers.
     *
     * @Route("", name="index", methods={"GET"})
     * @SWG\Response(
     *     response=200,
     *     description="Returns the providers",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=Provider::class, groups={"provider_default"}))
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
     * @SWG\Parameter(
     *     name="type",
     *     in="query",
     *     type="string",
     *     description="type filter",
     * )
     * @SWG\Tag(name="providers")
     * @Security(name="Bearer")
     */
    public function list(DocumentManager $dm, Request $request): Response
    {
        $page = $request->query->getInt('page', 1);
        $query = $request->query->get('query');
        $type = $request->query->get('type');

        $rs = [
            'total' => $dm->getRepository('App:Provider')->count($query),
            'facets' => $dm->getRepository('App:Provider')->facets($query),
            'items' => $dm->getRepository('App:Provider')->search($page, $query, $type, 20),
        ];

        return $this->json($rs, Response::HTTP_OK, [], ['groups' => ['provider_default']]);
    }

    /**
     * Get a providers.
     *
     * @Route("/{name}", name="get", methods={"GET"}, requirements={"name": ".+"})
     * @SWG\Response(
     *     response=200,
     *     description="Returns the provider",
     *     @SWG\Schema(ref=@Model(type=Provider::class, groups={"provider_full", "package_default"})
     *     )
     * )
     * @SWG\Tag(name="providers")
     * @Security(name="Bearer")
     */
    public function find(Provider $provider): Response
    {
        return $this->json($provider, Response::HTTP_OK, [], ['groups' => ['provider_full', 'package_default']]);
    }

    /**
     * Create a provider.
     *
     * @IsGranted("ROLE_MANAGER")
     *
     * @Route("", name="create", methods={"POST"})
     * @SWG\Response(
     *     response=201,
     *     description="Create a provider"
     * )
     * @SWG\Response(
     *     response=400,
     *     description="Invalid provider"
     * )
     * @SWG\Parameter(
     *     name="body",
     *     in="body",
     *     description="JSON Payload",
     *     required=true,
     *     format="application/json",
     *     @SWG\Schema(
     *         type="object",
     *         ref=@Model(type=Provider::class, groups={"provider_write"})
     *     )
     * )
     * @SWG\Tag(name="providers")
     * @Security(name="Bearer")
     */
    public function create(
        DocumentManager $dm,
        SerializerInterface $serializer,
        ValidatorInterface $validator,
        MessageBusInterface $bus,
        Request $request): Response
    {
        $data = $request->getContent();

        /** @var Provider $provider */
        $provider = $serializer->deserialize($data, Provider::class, 'json', ['groups' => ['provider_write']]);

        $violations = $validator->validate($provider);

        if ($violations->count() > 0) {
            return $this->json($violations, Response::HTTP_BAD_REQUEST);
        }

        $provider->setUpdateInProgress(true);

        $dm->persist($provider);
        $dm->flush();

        $bus->dispatch(new ProviderImportation($provider->getName()));

        return new Response('', Response::HTTP_CREATED);
    }

    /**
     * Refresh a provider.
     *
     * @IsGranted("ROLE_MANAGER")
     *
     * @Route("/{name}", name="refresh", methods={"PUT"}, requirements={"name": ".+"})
     * @SWG\Response(
     *     response=204,
     *     description="Refresh a provider"
     * )
     * @SWG\Tag(name="providers")
     * @Security(name="Bearer")
     */
    public function refresh(DocumentManager $dm, Provider $provider, MessageBusInterface $bus): Response
    {
        $provider->setUpdateInProgress(true);
        $dm->flush();
        $bus->dispatch(new ProviderImportation($provider->getName()));

        return new Response('', Response::HTTP_NO_CONTENT);
    }

    /**
     * Delete a provider.
     *
     * @IsGranted("ROLE_MANAGER")
     *
     * @Route("/{name}", name="delete", methods={"DELETE"}, requirements={"name": ".+"})
     * @SWG\Response(
     *     response=204,
     *     description="Delete a provider"
     * )
     * @SWG\Tag(name="providers")
     * @Security(name="Bearer")
     */
    public function delete(DocumentManager $dm, Provider $provider): Response
    {
        $dm->remove($provider);
        $dm->flush();

        return new Response('', Response::HTTP_NO_CONTENT);
    }
}
