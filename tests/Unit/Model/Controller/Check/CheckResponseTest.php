<?php

namespace Hippy\Api\Tests\Unit\Model\Controller\Check;

use Hippy\Api\Model\Controller\Check\CheckResponse;
use PHPUnit\Framework\TestCase;

/** @coversDefaultClass \Hippy\Api\Model\Controller\Check\CheckResponse */
class CheckResponseTest extends TestCase
{
    /**
     * @return void
     * @covers ::__construct
     */
    public function testConstruct(): void
    {
        $sut = new CheckResponse();
        $this->assertEquals(['checks' => []], $sut->jsonSerialize());
    }

    /**
     * @return void
     * @covers ::addCheck
     */
    public function testAddCheck(): void
    {
        $sut = new CheckResponse();
        $sut->addCheck('check1', true, '__dummy_message_1__');
        $sut->addCheck('check2', false, '__dummy_message_2__');

        $this->assertEquals(['checks' => [
            'check1' => ['success' => true, 'message' => '__dummy_message_1__'],
            'check2' => ['success' => false, 'message' => '__dummy_message_2__'],
        ]], $sut->jsonSerialize());
    }

    /**
     * @return void
     * @covers ::addCheck
     */
    public function testAddCheckWithExtra(): void
    {
        $extra1 = ['field1' => '__dummy_extra_data_1__'];
        $extra2 = ['field2' => '__dummy_extra_data_2__'];

        $sut = new CheckResponse();
        $sut->addCheck('check1', true, '__dummy_message_1__', $extra1);
        $sut->addCheck('check2', false, '__dummy_message_2__', $extra2);

        $this->assertEquals(['checks' => [
            'check1' => ['success' => true, 'message' => '__dummy_message_1__', 'field1' => '__dummy_extra_data_1__'],
            'check2' => ['success' => false, 'message' => '__dummy_message_2__', 'field2' => '__dummy_extra_data_2__'],
        ]], $sut->jsonSerialize());
    }
}
