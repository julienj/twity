<?php

namespace App\Controller;

use App\Document\User;
use App\Form\ResetPasswordType;
use App\Mailer;
use App\Security\ResetPasswordTokenManager;
use Doctrine\ODM\MongoDB\DocumentManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    /**
     * @Route("/login", name="login")
     */
    public function login(AuthenticationUtils $authenticationUtils, $gitlabDomain): Response
    {
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', [
            'hasGitlab' => null !== $gitlabDomain,
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }

    /**
     * @Route("/password", name="forget_password")
     */
    public function forgetPassword(
        Request $request,
        DocumentManager $dm,
        Mailer $mailer,
        ResetPasswordTokenManager $passwordTokenManager): Response
    {
        if ('POST' === $request->getMethod()) {
            /** @var User $user */
            $user = $dm->getRepository('App:User')->findOneBy([
                'email' => $request->request->get('email'),
            ]);

            if ($user) {
                $user->setResetPasswordToken($passwordTokenManager->generate());
                $dm->flush();
                $mailer->sendResetPasswordEmail($user);
            }

            $this->addFlash('info', 'If your email address exists in our database, you will receive a password recovery link at your email address in a few minutes.');

            return $this->redirectToRoute('login');
        }

        return $this->render('security/forgetPassword.html.twig');
    }

    /**
     * @Route("/password/{token}", name="reset_password")
     */
    public function resetPassword(
        Request $request,
        $token,
        DocumentManager $dm,
        ResetPasswordTokenManager $passwordTokenManager,
        UserPasswordEncoderInterface $passwordEncoder): Response
    {
        /** @var User $user */
        $user = $dm->getRepository('App:User')->findOneBy([
            'resetPasswordToken' => $token,
        ]);

        if (!$user) {
            $this->addFlash('danger', 'Invalid token.');

            return $this->redirectToRoute('login');
        }

        if (!$passwordTokenManager->isValid($token)) {
            $this->addFlash('danger', 'Expired token.');

            return $this->redirectToRoute('login');
        }

        $form = $this->createForm(ResetPasswordType::class, $user);

        if ('POST' === $request->getMethod()) {
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $user->setPassword($passwordEncoder->encodePassword($user, $user->getPlainPassword()));
                $dm->flush();

                $this->addFlash('info', 'Your password has been changed.');

                return $this->redirectToRoute('login');
            }
        }

        return $this->render('security/resetPassword.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
