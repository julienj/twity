<?php

namespace App\Composer;

use App\Document\Package;
use App\Document\Provider;
use Composer\Config;
use Composer\IO\IOInterface;
use Composer\Package\Dumper\ArrayDumper;
use Composer\Repository\ComposerRepository;
use Composer\Repository\VcsRepository;
use Doctrine\ODM\MongoDB\DocumentManager;

class PackageImporter
{
    private $factory;
    private $dm;
    private $mirrorUrl;

    public function __construct(DocumentManager $dm, string $mirrorUrl, Factory $factory)
    {
        $this->dm = $dm;
        $this->mirrorUrl = $mirrorUrl;
        $this->factory = $factory;
    }

    public function import(Provider $provider): bool
    {
        $config = $this->factory->createConfig();
        $io = new LogIO();
        $io->loadConfiguration($config);

        $packages = $this->getPackages($provider, $config, $io);

        if (0 == count($packages)) {
            return false;
        }

        $dumper = new ArrayDumper();

        $provider->clearPackages();

        foreach ($packages as $package) {
            $data = $dumper->dump($package);
            $data['uid'] = uniqid();
            unset($data['notification-url']);

            $document = new Package();
            $document
                ->setVersion($package->getPrettyVersion())
                ->setData($data);

            $provider->addPackage($document);
        }

        $provider->setLogs($io->getLogs());

        return true;
    }

    /**
     * @param Provider    $provider
     * @param Config      $config
     * @param IOInterface $io
     *
     * @return \Composer\Package\Package[]
     */
    private function getPackages(Provider $provider, Config $config, IOInterface $io): array
    {
        switch ($provider->getType()) {
            case Provider::TYPE_COMPOSER:
                $repository = new ComposerRepository(['url' => $this->mirrorUrl], $io, $config);
                break;
            case Provider::TYPE_VCS:
                $repository = new VcsRepository(['url' => $provider->getVcsUri()], $io, $config);
                break;
            default:
                throw new \LogicException('Invalid provider type');
        }

        return $repository->findPackages($provider->getName());
    }
}
