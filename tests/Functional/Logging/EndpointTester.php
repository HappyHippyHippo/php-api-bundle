<?php

namespace Hippy\Api\Tests\Functional\Logging;

use Hippy\Api\Tests\Functional\EndpointTester as BaseEndpointTester;

abstract class EndpointTester extends BaseEndpointTester
{
    /**
     * @param array<string, mixed> $record
     * @return void
     */
    protected function checkRequest(array $record): void
    {
        $this->assertEquals('INFO', $record['level_name']);
        $this->assertEquals('app', $record['channel']);
        $this->assertEquals('Request', $record['message']);
        $this->assertArrayHasKey('request', $record['context']);
        $this->assertArrayHasKey('uri', $record['context']['request']);
        $this->assertArrayHasKey('method', $record['context']['request']);
        $this->assertArrayHasKey('clientIp', $record['context']['request']);
        $this->assertArrayHasKey('headers', $record['context']['request']);
        $this->assertArrayHasKey('query', $record['context']['request']);
        $this->assertArrayHasKey('request', $record['context']['request']);
        $this->assertArrayHasKey('attributes', $record['context']['request']);
    }

    /**
     * @param array<string, mixed> $record
     * @return void
     */
    protected function checkResponse(array $record): void
    {
        $this->assertEquals('INFO', $record['level_name']);
        $this->assertEquals('app', $record['channel']);
        $this->assertEquals('Response', $record['message']);
        $this->assertArrayHasKey('request', $record['context']);
        $this->assertArrayHasKey('headers', $record['context']['request']);
        $this->assertArrayHasKey('query', $record['context']['request']);
        $this->assertArrayHasKey('request', $record['context']['request']);
        $this->assertArrayHasKey('attributes', $record['context']['request']);
        $this->assertArrayHasKey('response', $record['context']);
        $this->assertArrayHasKey('status', $record['context']['response']);
        $this->assertArrayHasKey('headers', $record['context']['response']);
    }

    /**
     * @param array<string, mixed> $record
     * @param string $field
     * @param mixed $value
     * @return void
     */
    protected function checkRequestQuery(array $record, string $field, mixed $value): void
    {
        $this->assertArrayHasKey($field, $record['context']['request']['query']);
        $this->assertEquals($value, $record['context']['request']['query'][$field]);
    }

    /**
     * @param array<string, mixed> $record
     * @param string $field
     * @param mixed $value
     * @return void
     */
    protected function checkRequestAttr(array $record, string $field, mixed $value): void
    {
        $this->assertArrayHasKey($field, $record['context']['request']['attributes']);
        $this->assertEquals($value, $record['context']['request']['attributes'][$field]);
    }

    /**
     * @param array<string, mixed> $record
     * @param int $statusCode
     * @return void
     */
    protected function checkResponseStatus(array $record, int $statusCode): void
    {
        $this->assertEquals($statusCode, $record['context']['response']['status']);
    }

    /**
     * @param array<string, mixed> $record
     * @param string $body
     * @return void
     */
    protected function checkResponseBody(array $record, string $body): void
    {
        $this->assertArrayHasKey('body', $record['context']['response']);
        $this->assertEquals($body, $record['context']['response']['body']);
    }

    /**
     * @param array<string, mixed> $record
     * @return void
     */
    protected function checkResponseNoBody(array $record): void
    {
        $this->assertArrayNotHasKey('body', $record['context']['response']);
    }
}
