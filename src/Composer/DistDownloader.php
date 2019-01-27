<?php

namespace App\Composer;

use App\Document\Package;
use App\Document\Provider;
use Composer\Downloader\FileDownloader;
use Composer\Package\Loader\ArrayLoader;
use Doctrine\ODM\MongoDB\DocumentManager;

class DistDownloader
{
    private $distPath;
    private $dm;
    private $factory;

    public function __construct(string $distPath, DocumentManager $dm, Factory $factory)
    {
        $this->distPath = $distPath;
        $this->dm = $dm;
        $this->factory = $factory;
    }

    public function download(string $name, string $version, string $reference, string $type): string
    {
        $path = $this->distPath.DIRECTORY_SEPARATOR.$name.DIRECTORY_SEPARATOR.$version;
        $filePath = $path.DIRECTORY_SEPARATOR.$reference;

        $config = $this->factory->createConfig();
        $io = new LogIO();
        $io->loadConfiguration($config);

        if (file_exists($filePath)) {
            return $filePath;
        }

        /** @var Provider $provider */
        $provider = $this->dm->getRepository('App:Provider')->find($name);

        if (!$provider) {
            throw new \LogicException('Invalid provider');
        }

        /** @var Package $package */
        foreach ($provider->getPackages() as $package) {
            $data = $package->getData();
            if (
                isset($data['version_normalized']) && $data['version_normalized'] === $version &&
                isset($data['dist']['reference']) && $data['dist']['reference'] === $reference &&
                isset($data['dist']['type']) && $data['dist']['type'] === $type &&
                isset($data['dist']['url'])
            ) {
                $loader = new ArrayLoader();
                $composerPackage = $loader->load($data);

                $downloader = new FileDownloader($io, $config);
                $fileName = $downloader->download($composerPackage, $path, false);

                rename($fileName, $filePath);

                return $filePath;
            }
        }

        throw new \LogicException('Invalid dist');
    }
}
