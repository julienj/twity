<?php

namespace App\Controller;

use App\Composer\DistDownloader;
use App\Composer\PackageImporter;
use App\Document\Download;
use App\Document\Provider;
use Doctrine\ODM\MongoDB\DocumentManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Provide routes for composer.
 *
 * @Route("", name="repo_", defaults={"_format": "json"})
 */
class RepoController extends AbstractController
{
    /**
     * @Route("/packages.json", name="index")
     */
    public function index(DocumentManager $dm): Response
    {
        $rs = [
            'packages' => [],
            'providers' => $dm->getRepository('App:Provider')->getProvidersAndSha256(),
            'providers-url' => $this->generateCleanUrl('repo_provider', ['provider' => '%package%', 'hash' => '%hash%']),
            'search' => $this->generateCleanUrl('repo_search', ['q' => '%query%']),
            'notify-batch' => $this->generateCleanUrl('repo_notify_batch'),
            'mirrors' => [
                [
                    'dist-url' => $this->generateCleanUrl('repo_mirror', [
                        'package' => '%package%',
                        'version' => '%version%',
                        'reference' => '%reference%',
                        'type' => '%type%',
                    ]),
                    'preferred' => true,
                ],
            ],
        ];

        if ($this->isGranted('ROLE_MANAGER')) {
            $rs['providers-lazy-url'] = $this->generateCleanUrl('repo_lazy', ['package' => '%package%']);
        }

        return $this->json($rs);
    }

    /**
     * @Route("/p/search.json", name="search")
     */
    public function search(Request $request, DocumentManager $dm): Response
    {
        $query = $request->get('q');
        $providers = $dm->getRepository('App:Provider')->search(1, $query);

        $rs = [];

        /** @var \App\Document\Provider $provider */
        foreach ($providers as $provider) {
            $rs[] = [
                'name' => $provider->getName(),
                'description' => $provider->getDescription(),
                'downloads' => $provider->getDownloads(),
            ];
        }

        return $this->json(['results' => $rs]);
    }

    /**
     * @Route("/p/downloads", name="notify_batch")
     */
    public function notifyBatch(Request $request, DocumentManager $dm): Response
    {
        $downloads = json_decode($request->getContent(), true);

        if (isset($downloads['downloads'])) {
            foreach ($downloads['downloads'] as $package) {
                /** @var Provider $provider */
                $provider = $dm->getRepository('App:Provider')->find($package['name']);

                if (!$provider) {
                    continue;
                }
                $provider->setDownloads($provider->getDownloads() + 1);

                if ($package['name'] && $package['version']) {
                    $download = new Download();
                    $download
                        ->setDate(new \DateTime())
                        ->setProvider($package['name'])
                        ->setVersion($package['version']);

                    $dm->persist($download);
                }
            }

            $dm->flush();
        }

        return new Response('', Response::HTTP_OK);
    }

    /**
     * @Route("/p/dist/{package}/{version}/{reference}.{type}", name="mirror", requirements={"package": ".+"})
     */
    public function mirror(string $package, string $version, string $reference, string $type, DistDownloader $distDownloader): Response
    {
        $file = $distDownloader->download($package, $version, $reference, $type);

        return new BinaryFileResponse($file);
    }

    /**
     * @Route("/p/{provider}${hash}.json", name="provider", requirements={"provider": ".+"})
     */
    public function provider(Provider $provider, $hash): Response
    {
        return new Response(json_encode($provider->getData()));
    }

    /**
     * @Route("/p/{package}.json", name="lazy", requirements={"package": ".+"})
     * @IsGranted("ROLE_MANAGER")
     */
    public function providerLazy($package, PackageImporter $packageImporter, DocumentManager $dm): Response
    {
        $provider = new Provider();
        $provider->setType(Provider::TYPE_COMPOSER);
        $provider->setName($package);

        if (!$packageImporter->import($provider)) {
            throw $this->createNotFoundException();
        }

        $dm->persist($provider);
        $dm->flush();

        return $this->json($provider->getData());
    }

    private function generateCleanUrl(string $route, array $parameters = [], int $referenceType = UrlGeneratorInterface::ABSOLUTE_URL): string
    {
        $url = $this->generateUrl($route, $parameters, $referenceType);

        return strtr($url, ['%25' => '%', '%24' => '$']);
    }
}
