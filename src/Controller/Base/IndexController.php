<?php

namespace Hippy\Api\Controller\Base;

use Hippy\Api\Config\ApiConfig;
use Hippy\Api\Controller\AbstractController;
use Hippy\Api\Service\Base\IndexService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class IndexController extends AbstractController
{
    /** @var int */
    private const ENDPOINT_CODE = 1;

    /**
     * @param ApiConfig $config
     * @param IndexService $service
     */
    public function __construct(
        ApiConfig $config,
        protected IndexService $service,
    ) {
        parent::__construct($config, self::ENDPOINT_CODE);
    }

    /**
     * @return Response
     * @Route("/", name="base.index.preflight", methods="OPTIONS")
     */
    public function preflight(): Response
    {
        $response = new Response(null, Response::HTTP_NO_CONTENT);
        $response->headers->add(['Access-Control-Allow-Methods' => 'HEAD, GET']);
        $response->headers->add(['Access-Control-Allow-Origin' => $this->config->getCorsOrigin()]);
        $response->headers->add(['Access-Control-Allow-Headers' => 'Content-Type']);

        return $response;
    }

    /**
     * @return Response
     * @Route("/", name="base.index", methods="GET")
     */
    public function index(): Response
    {
        return $this->envelope(
            function () {
                return $this->service->process();
            }
        );
    }
}
