<?php

namespace App\Messenger\MessageHandler;

use App\Composer\PackageImporter;
use App\Document\Provider;
use App\Messenger\Message\ProviderImportation;
use Doctrine\ODM\MongoDB\DocumentManager;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class ProviderImportationHandler implements MessageHandlerInterface
{
    private $packageImporter;

    private $dm;

    public function __construct(PackageImporter $packageImporter, DocumentManager $dm)
    {
        $this->packageImporter = $packageImporter;
        $this->dm = $dm;
    }

    public function __invoke(ProviderImportation $message)
    {
        $this->dm->clear();

        /** @var Provider $provider */
        $provider = $this->dm->getRepository('App:Provider')->find($message->getName());

        if (!$provider) {
            return;
        }

        try {
            $rs = $this->packageImporter->import($provider);
            $provider->setHasError(!$rs);
        } catch (\Exception $e) {
            $provider->setHasError(true);
            $provider->setLogs($e->getMessage());
        }

        $provider->setUpdateInProgress(false);
        $this->dm->flush();

        $this->packageImporter->updateReplaceProviderSignature($provider);
        $this->dm->flush();
    }
}
