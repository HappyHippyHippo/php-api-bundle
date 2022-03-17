<?php

namespace Hippy\Api\Controller\Base;

use Hippy\Api\Config\ApiConfig;
use Hippy\Api\Controller\AbstractController;
use Hippy\Api\Service\Base\ConfigService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ConfigController extends AbstractController
{
    /** @var int */
    private const ENDPOINT_CODE = 4;

    /**
     * @param ApiConfig $config
     * @param ConfigService $service
     */
    public function __construct(
        ApiConfig $config,
        protected ConfigService $service,
    ) {
        parent::__construct($config, self::ENDPOINT_CODE);
    }

    /**
     * @return Response
     * @Route("/__config", name="base.config.preflight", methods="OPTIONS")
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
     * @Route("/__config", name="base.config", methods="GET")
     */
    public function config(): Response
    {
        return $this->envelope(
            function () {
                return $this->service->process();
            }
        );
    }

    /**
     * @return bool
     */
    protected function isEnabled(): bool
    {
        return !!$this->config->isEndpointConfigEnabled();
    }
}
