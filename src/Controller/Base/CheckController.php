<?php

namespace Hippy\Api\Controller\Base;

use Hippy\Api\Config\ApiConfig;
use Hippy\Api\Controller\AbstractController;
use Hippy\Api\Model\Controller\Check\CheckRequest;
use Hippy\Api\Service\Base\CheckService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CheckController extends AbstractController
{
    /** @var int */
    private const ENDPOINT_CODE = 2;

    /**
     * @param ApiConfig $config
     * @param CheckService $service
     */
    public function __construct(
        ApiConfig $config,
        protected CheckService $service,
    ) {
        parent::__construct($config, self::ENDPOINT_CODE);
    }

    /**
     * @return Response
     * @Route("/__check", name="base.check.preflight", methods="OPTIONS")
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
     * @param Request $request
     * @return Response
     * @Route("/__check", name="base.check", methods="GET")
     */
    public function check(Request $request): Response
    {
        return $this->envelope(
            function () use ($request) {
                return $this->service->check(new CheckRequest($request));
            }
        );
    }
}
