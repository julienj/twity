<?php

namespace App\Controller\Api;

use App\Document\Provider;
use Doctrine\ODM\MongoDB\DocumentManager;
use Nelmio\ApiDocBundle\Annotation\Security;
use Swagger\Annotations as SWG;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Provide routes for stats API.
 *
 * @Route("/api/downloads", name="api_downloads_", defaults={"_format": "json"})
 */
class DownloadsController extends AbstractController
{
    /**
     * Profile.
     *
     * @Route("/{name}/{version}", name="provider", methods={"GET"}, requirements={"name": ".+"})
     * @SWG\Response(
     *     response=200,
     *     description="Returns provider downloads",
     * )
     * @SWG\Tag(name="downloads")
     * @Security(name="Bearer")
     */
    public function provider(Provider $provider, $version, DocumentManager $dm): Response
    {
        $all = $dm->getRepository('App:Download')->getProviderStats($provider);
        $version = $dm->getRepository('App:Download')->getProviderStats($provider, $version);

        $data = [];

        foreach ($all as $item) {
            $data[$item['date']]['all'] = $item['downloads'];
        }

        foreach ($version as $item) {
            $data[$item['date']]['version'] = $item['downloads'];
        }

        return $this->json($data);
    }
}
