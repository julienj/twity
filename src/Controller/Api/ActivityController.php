<?php

namespace App\Controller\Api;

use Doctrine\ODM\MongoDB\DocumentManager;
use Fig\Link\Link;
use Nelmio\ApiDocBundle\Annotation\Security;
use Swagger\Annotations as SWG;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Provide routes for activity API.
 *
 * @Route("/api/activity", name="api_activity_", defaults={"_format": "json"})
 */
class ActivityController extends AbstractController
{
    /**
     * List activity.
     *
     * @Route("", name="index", methods={"GET"})
     * @SWG\Response(
     *     response=200,
     *     description="Returns activity",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(type="string")
     *     )
     * )
     * @SWG\Tag(name="activity")
     * @Security(name="Bearer")
     */
    public function index(DocumentManager $dm, Request $request, string $mercurePublicUrl, bool $mercureEnabled = true): Response
    {
        if($mercureEnabled) {
            $this->addLink($request, new Link('mercure', $mercurePublicUrl));
        }

        $data = $dm->getRepository('App:Provider')->findUpdateInProgress();
        return $this->json($data);
    }

}
