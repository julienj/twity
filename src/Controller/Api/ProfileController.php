<?php

namespace App\Controller\Api;

use App\Document\Provider;
use Doctrine\ODM\MongoDB\DocumentManager;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use Swagger\Annotations as SWG;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Provide routes for Profile API.
 *
 * @Route("/api/me", name="api_profile_", defaults={"_format": "json"})
 */
class ProfileController extends AbstractController
{
    /**
     * Profile.
     *
     * @Route("", name="me", methods={"GET"})
     * @SWG\Response(
     *     response=200,
     *     description="Returns my profile",
     *     @SWG\Schema(@SWG\Items(ref=@Model(type=Provider::class, groups={"user_profile", "token_default"})))
     * )
     * @SWG\Tag(name="profile")
     * @Security(name="Bearer")
     */
    public function me(): Response
    {
        return $this->json($this->getUser(), Response::HTTP_OK, [], ['groups' => ['user_profile', 'token_default']]);
    }

    /**
     * Regenerate personal key.
     *
     * @Route("/regenerate-token", name="regenerate_token", methods={"POST"})
     * @SWG\Response(
     *     response=201,
     *     description="Regenerate personal key",
     * )
     * @SWG\Tag(name="profile")
     * @Security(name="Bearer")
     */
    public function regenerateToken(DocumentManager $dm): Response
    {
        $this->getUser()->generateToken();
        $dm->flush();

        return new Response('', Response::HTTP_CREATED);
    }
}
