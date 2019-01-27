<?php

namespace App\Controller\Api;

use App\Document\User;
use App\Mailer;
use App\Security\ResetPasswordTokenManager;
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
 * Provide routes for User API.
 *
 * @Route("/api/users", name="api_user_", defaults={"_format": "json"})
 */
class UserController extends AbstractController
{
    /**
     * List users.
     *
     * @Route("", name="index", methods={"GET"})
     * @SWG\Response(
     *     response=200,
     *     description="Returns users",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=User::class, groups={"user_default"}))
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
     * @SWG\Tag(name="users")
     * @Security(name="Bearer")
     *
     * @IsGranted("ROLE_ADMIN")
     */
    public function list(DocumentManager $dm, Request $request): Response
    {
        $page = $request->query->getInt('page', 1);
        $query = $request->query->get('query');

        $rs = [
            'total' => $dm->getRepository('App:User')->count($query),
            'items' => $dm->getRepository('App:User')->search($page, $query, 20),
        ];

        return $this->json($rs, Response::HTTP_OK, [], ['groups' => ['user_default']]);
    }

    /**
     * autocomplete users.
     *
     * @Route("/autocomplete", name="autocomplete", methods={"GET"})
     * @SWG\Response(
     *     response=200,
     *     description="Returns users",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=User::class, groups={"user_autocomplete"}))
     *     )
     * )
     * @SWG\Parameter(
     *     name="query",
     *     in="query",
     *     type="string",
     *     description="Search query",
     * )
     * @SWG\Tag(name="users")
     * @Security(name="Bearer")
     *
     * @IsGranted("ROLE_USER")
     */
    public function autocomplete(DocumentManager $dm, Request $request): Response
    {
        $query = $request->query->get('query');

        $rs = [
            'items' => $dm->getRepository('App:User')->search(1, $query, 20),
        ];

        return $this->json($rs, Response::HTTP_OK, [], ['groups' => ['user_autocomplete']]);
    }

    /**
     * Get user.
     *
     * @Route("/{id}", name="get", methods={"GET"})
     * @SWG\Response(
     *     response=200,
     *     description="Return user",
     *     @SWG\Schema(ref=@Model(type=User::class, groups={"user_full"})
     *     )
     * )
     * @SWG\Tag(name="users")
     * @Security(name="Bearer")
     *
     * @IsGranted("ROLE_ADMIN")
     */
    public function find(User $user): Response
    {
        return $this->json($user, Response::HTTP_OK, [], ['groups' => ['user_full']]);
    }

    /**
     * Create user.
     *
     * @Route("", name="create", methods={"POST"})
     * @SWG\Response(
     *     response=201,
     *     description="Create user",
     *     @SWG\Schema(
     *         type="object",
     *         ref=@Model(type=User::class, groups={"user_full"})
     *     )
     * )
     * @SWG\Response(
     *     response=400,
     *     description="Invalid user"
     * )
     * @SWG\Parameter(
     *     name="body",
     *     in="body",
     *     description="JSON Payload",
     *     required=true,
     *     format="application/json",
     *     @SWG\Schema(
     *         type="object",
     *         ref=@Model(type=User::class, groups={"user_write", "user_create"})
     *     )
     * )
     * @SWG\Tag(name="users")
     * @Security(name="Bearer")
     *
     * @IsGranted("ROLE_ADMIN")
     */
    public function create(
        DocumentManager $dm,
        SerializerInterface $serializer,
        ValidatorInterface $validator,
        Request $request,
        Mailer $mailer,
        ResetPasswordTokenManager $resetPasswordTokenManager): Response
    {
        $data = $request->getContent();

        /** @var User $user */
        $user = $serializer->deserialize($data, User::class, 'json', ['groups' => ['user_write', 'user_create']]);

        $violations = $validator->validate($user);

        if ($violations->count() > 0) {
            return $this->json($violations, Response::HTTP_BAD_REQUEST);
        }

        $user->setResetPasswordToken($resetPasswordTokenManager->generate());
        $mailer->sendWelcomeEmail($user);

        $dm->persist($user);
        $dm->flush();

        return $this->json($user, Response::HTTP_CREATED, [], ['groups' => ['user_full']]);
    }

    /**
     * update user.
     *
     * @Route("/{id}", name="update", methods={"PUT"})
     * @SWG\Response(
     *     response=204,
     *     description="Update user"
     * )
     * @SWG\Response(
     *     response=400,
     *     description="Invalid user"
     * )
     * @SWG\Parameter(
     *     name="body",
     *     in="body",
     *     description="JSON Payload",
     *     required=true,
     *     format="application/json",
     *     @SWG\Schema(
     *         type="object",
     *         ref=@Model(type=User::class, groups={"user_write"})
     *     )
     * )
     * @SWG\Tag(name="users")
     * @Security(name="Bearer")
     *
     * @IsGranted("ROLE_ADMIN")
     */
    public function update(
        User $user,
        DocumentManager $dm,
        SerializerInterface $serializer,
        ValidatorInterface $validator,
        Request $request): Response
    {
        $data = $request->getContent();

        /* @var User $user */
        $serializer->deserialize($data, User::class, 'json', [
            'groups' => ['user_write'],
            'object_to_populate' => $user,
        ]);

        $violations = $validator->validate($user);

        if ($violations->count() > 0) {
            return $this->json($violations, Response::HTTP_BAD_REQUEST);
        }

        $dm->flush();

        return new Response('', Response::HTTP_NO_CONTENT);
    }

    /**
     * Delete user.
     *
     * @Route("/{id}", name="delete", methods={"DELETE"})
     * @SWG\Response(
     *     response=204,
     *     description="Delete user"
     * )
     * @SWG\Tag(name="users")
     * @Security(name="Bearer")
     *
     * @IsGranted("ROLE_ADMIN")
     */
    public function delete(DocumentManager $dm, User $user): Response
    {
        $dm->remove($user);
        $dm->flush();

        return new Response('', Response::HTTP_NO_CONTENT);
    }
}
