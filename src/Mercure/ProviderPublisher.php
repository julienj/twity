<?php

namespace App\Mercure;


use App\Document\Provider;
use Symfony\Component\Mercure\Publisher;
use Symfony\Component\Mercure\Update;

class ProviderPublisher
{
    private $publisher;
    private $mercureEnabled;

    public function __construct(Publisher $publisher, bool $mercureEnabled = true)
    {
        $this->publisher = $publisher;
        $this->mercureEnabled = $mercureEnabled;
    }

    public function publishProviderUpdate(Provider $provider)
    {

        if($this->mercureEnabled) {
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
}
