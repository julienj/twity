<?php

namespace App\EventSubscriber;

use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;

class MercureCookieInjectorSubscriber implements EventSubscriberInterface
{

    private $mercureJwtKey;

    public static function getSubscribedEvents()
    {
        return [
           'kernel.response' => 'onKernelResponse',
        ];
    }

    public function __construct(string $mercureJwtKey)
    {
        $this->mercureJwtKey = $mercureJwtKey;
    }

    public function onKernelResponse(FilterResponseEvent $event)
    {

        $token = (new Builder())
            ->set('mercure', [
                'subscribe' => [
                    'http://twity.io/user'
                ]
            ])
            ->sign(new Sha256(), $this->mercureJwtKey)
            ->getToken();


        $event->getResponse()->headers->set(
            'set-cookie',
            sprintf('mercureAuthorization=%s; path=/hub; httponly; ', $token)

        );
    }

}
