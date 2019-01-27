<?php

namespace App\Composer;

use App\Document\Backend;
use Composer\Config;
use Doctrine\ODM\MongoDB\DocumentManager;

class Factory
{
    protected $composerDir;
    protected $dm;

    public function __construct(DocumentManager $dm, string $composerDir)
    {
        $this->composerDir = $composerDir;
        $this->dm = $dm;
    }

    public function createConfig(): Config
    {
        $config = new Config(false, $this->composerDir);

        $config->merge(['config' => [
            'home' => $this->composerDir,
            'cache-dir' => $this->composerDir.'/cache',
            'data-dir' => $this->composerDir.'/data',
        ]]);

        $backend = $this->dm->getRepository('App:Backend')->findAll();

        $gitlabDomains = ['gitlab.com'];
        $gitlabTokens = [];
        $githubDomains = ['github.com'];
        $githubOauth = [];

        /** @var Backend $conf */
        foreach ($backend as $conf) {
            if (Backend::TYPE_GITLAB === $conf->getType()) {
                $gitlabDomains[] = $conf->getDomain();
                $gitlabTokens[$conf->getDomain()] = $conf->getToken();
            }
            if (Backend::TYPE_GITHUB === $conf->getType()) {
                $githubDomains[] = $conf->getDomain();
                $githubOauth[$conf->getDomain()] = $conf->getToken();
            }
        }

        $config->merge(['config' => [
            'gitlab-domains' => array_unique($gitlabDomains),
            'gitlab-token' => $gitlabTokens,
            'github-domains' => array_unique($githubDomains),
            'github-oauth' => $githubOauth,
        ]]);

        return $config;
    }
}
