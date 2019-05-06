<?php


namespace App\Composer;


use App\Document\Package;
use App\Document\Provider;
use Doctrine\ODM\MongoDB\DocumentManager;

class PackageDumper
{
    private $dm;

    public function __construct(DocumentManager $dm)
    {
        $this->dm = $dm;
    }

    public function dump(Provider $provider)
    {
        $data = [];

        /** @var Package $package */
        foreach ($provider->getPackages() as $package) {
            $data[$provider->getName()][$package->getVersion()] = $package->getData();
        }

        /** @var Provider[] $replacements */
        $replacements = $this->dm->getRepository('App:Provider')->getReplacements($provider);
        foreach ($replacements as $replacement) {
            foreach ($replacement->getPackages() as $package) {
                if(in_array($provider->getName(), $package->getReplace())) {
                    $data[$replacement->getName()][$package->getVersion()] = $package->getData();
                }

            }
        }

        return ['packages' => $data];
    }
}
