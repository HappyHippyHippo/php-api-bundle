<?php

namespace Hippy\Api\Controller\Base;

use Hippy\Api\Config\ApiConfigInterface;
use Hippy\Api\Controller\AbstractController;
use Hippy\Api\Service\Base\OpenapiService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OpenapiController extends AbstractController
{
    /** @var int */
    private const ENDPOINT_CODE = 3;

    /**
     * @param ApiConfigInterface $config
     * @param OpenapiService $service
     */
    public function __construct(
        ApiConfigInterface $config,
        protected OpenapiService $service,
    ) {
        parent::__construct($config, self::ENDPOINT_CODE);
    }

    /**
     * @return Response
     * @Route("/__openapi", name="base.openapi.preflight", methods="OPTIONS")
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
     * @Route("/__openapi", name="base.openapi", methods="GET")
     */
    public function openapi(): Response
    {
        $response = $this->respond(
            function () {
                return $this->service->process();
            }
        );
        $response->headers->add(['Content-Type' => 'text/vnd.yaml']);

        return $response;
    }

    /**
     * @return bool
     */
    protected function isEnabled(): bool
    {
        return !!$this->config->isEndpointOpenApiEnabled();
    }
}
