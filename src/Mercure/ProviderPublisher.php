<?php

namespace App\Mercure;


use App\Document\Provider;
use Symfony\Component\Mercure\Publisher;
use Symfony\Component\Mercure\Update;

class ProviderPublisher
{
    private $publisher;

    public function __construct(Publisher $publisher)
    {
        $this->publisher = $publisher;
    }

    public function publishProviderUpdate(Provider $provider)
    {
        $update = new Update(
            'http://twity.io/p/' . $provider->getName(),
            json_encode([
                'provider' => $provider->getName(),
                'updateInProgress' => $provider->getUpdateInProgress()
            ])
        );

        $publisher = $this->publisher;
        $publisher($update);
    }
}
