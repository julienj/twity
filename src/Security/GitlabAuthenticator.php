<?php

namespace App\Security;

use Doctrine\ODM\MongoDB\DocumentManager;
use Omines\OAuth2\Client\Provider\Gitlab;
use Omines\OAuth2\Client\Provider\GitlabResourceOwner;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationCredentialsNotFoundException;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

class GitlabAuthenticator extends AbstractGuardAuthenticator
{
    use TargetPathTrait;

    /**
     * @var Gitlab
     */
    private $gitlab;

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var DocumentManager
     */
    private $manager;

    /**
     * @var string
     */
    private $gitlabDefaultRole;

    public function __construct(RouterInterface $router, DocumentManager $manager, $gitlabDomain, $gitlabClientId, $gitlabClientSecret, $gitlabDefaultRole)
    {
        $this->router = $router;
        $this->manager = $manager;
        $this->gitlabDefaultRole = $gitlabDefaultRole;
        $this->gitlab = new Gitlab([
            'clientId' => $gitlabClientId,
            'clientSecret' => $gitlabClientSecret,
            'redirectUri' => $this->router->generate('gitlab_login', [], RouterInterface::ABSOLUTE_URL),
            'domain' => $gitlabDomain,
        ]);
    }

    public function start(Request $request, AuthenticationException $authException = null)
    {
    }

    public function supports(Request $request)
    {
        return 'gitlab_login' === $request->attributes->get('_route')
            && $this->gitlab->domain;
    }

    public function getCredentials(Request $request)
    {
        $code = $request->query->get('code');

        if (!$code) {
            throw new AuthenticationCredentialsNotFoundException();
        }

        $state = $request->query->get('state');
        if (!$state || $state !== $request->getSession()->get('oauth2state')) {
            throw new CustomUserMessageAuthenticationException('Invalid state.');
        }

        return ['code' => $code];
    }

    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        $token = $this->gitlab->getAccessToken('authorization_code', [
            'code' => $credentials['code'],
        ]);

        try {
            /** @var GitlabResourceOwner $gitlabUser */
            $gitlabUser = $this->gitlab->getResourceOwner($token);
        } catch (\Exception $e) {
            throw new CustomUserMessageAuthenticationException('Gitlab error.');
        }

        try {
            $user = $userProvider->loadUserByUsername($gitlabUser->getUsername());
        } catch (UsernameNotFoundException $e) {
            $user = $this->manager->getRepository('App:User')->createFromGitlab($gitlabUser, $this->gitlabDefaultRole);
        }

        return $user;
    }

    public function checkCredentials($credentials, UserInterface $user)
    {
        return true;
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        if ($targetPath = $this->getTargetPath($request->getSession(), $providerKey)) {
            return new RedirectResponse($targetPath);
        }

        return new RedirectResponse($this->router->generate('home'));
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        if ($exception instanceof AuthenticationCredentialsNotFoundException) {
            $url = $this->gitlab->getAuthorizationUrl();
            $request->getSession()->set('oauth2state', $this->gitlab->getState());

            return new RedirectResponse($url);
        }

        throw $exception;
    }

    public function supportsRememberMe()
    {
        return false;
    }
}
