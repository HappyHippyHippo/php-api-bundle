<?php

namespace Hippy\Api\Controller;

use Hippy\Api\Config\ApiConfigInterface;
use Hippy\Api\Error\ErrorCode;
use Hippy\Error\Error;
use Hippy\Exception\Exception;
use Hippy\Exception\RedirectException;
use Hippy\Model\Envelope;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController as BaseAbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

abstract class AbstractController extends BaseAbstractController
{
    /**
     * @param ApiConfigInterface $config
     * @param int|string $endpointCode
     */
    public function __construct(
        protected ApiConfigInterface $config,
        protected int|string $endpointCode = '',
    ) {
    }

    /**
     * @param int|string $endpointCode
     * @return $this
     */
    protected function setEndpointCode(int|string $endpointCode): AbstractController
    {
        $this->endpointCode = $endpointCode;
        return $this;
    }

    /**
     * @return bool
     */
    protected function isEnabled(): bool
    {
        return true;
    }

    /**
     * @param callable $execute
     * @param int $statusCode
     * @return Response
     */
    protected function envelope(callable $execute, int $statusCode = Response::HTTP_OK): Response
    {
        return $this->execute(function () use ($execute, $statusCode) {
            if (!$this->isEnabled()) {
                throw (new Exception(Response::HTTP_SERVICE_UNAVAILABLE))->addError(
                    new Error(ErrorCode::NOT_ENABLED, ErrorCode::ERROR_TO_MESSAGE[ErrorCode::NOT_ENABLED])
                );
            }

            $data = $execute();
            $envelope = new Envelope();
            if (!is_null($data)) {
                $envelope->setData($data);
            }

            return $this->json($envelope, $statusCode);
        });
    }

    /**
     * @param callable $execute
     * @param int $statusCode
     * @return Response
     */
    protected function respond(callable $execute, int $statusCode = Response::HTTP_OK): Response
    {
        return $this->execute(function () use ($execute, $statusCode) {
            if (!$this->isEnabled()) {
                throw (new Exception(Response::HTTP_SERVICE_UNAVAILABLE))->addError(
                    new Error(ErrorCode::NOT_ENABLED, ErrorCode::ERROR_TO_MESSAGE[ErrorCode::NOT_ENABLED])
                );
            }

            return new Response($execute(), $statusCode);
        });
    }

    /**
     * @param callable $execute
     * @return Response
     */
    protected function execute(callable $execute): Response
    {
        try {
            return $execute();
        } catch (RedirectException $exception) {
            return new RedirectResponse($exception->getURL());
        } catch (Exception $exception) {
            $exception->getErrors()->each(function (Error $error) {
                return $error
                    ->setService((int) $this->config->getAppId())
                    ->setEndpoint($this->endpointCode);
            });
            throw $exception;
        }
    }
}
