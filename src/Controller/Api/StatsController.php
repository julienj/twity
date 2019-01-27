<?php

namespace App\Controller\Api;

use Doctrine\ODM\MongoDB\DocumentManager;
use Nelmio\ApiDocBundle\Annotation\Security;
use Swagger\Annotations as SWG;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Provide routes for stats API.
 *
 * @Route("/api/stats", name="api_stats_", defaults={"_format": "json"})
 */
class StatsController extends AbstractController
{
    /**
     * Profile.
     *
     * @Route("", name="app", methods={"GET"})
     * @SWG\Response(
     *     response=200,
     *     description="Returns stats",
     * )
     * @SWG\Tag(name="stats")
     * @Security(name="Bearer")
     */
    public function stats(DocumentManager $dm): Response
    {
        return $this->json([
            'packages' => $dm->getRepository('App:Provider')->count(),
            'applications' => $dm->getRepository('App:Application')->count($this->getUser()),
            'weekly_downloads' => $dm->getRepository('App:Download')->countLast7Days(),
        ]);
    }
}
