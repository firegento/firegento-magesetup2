<?php
/**
 * Copyright © 2016 FireGento e.V.
 * See LICENSE.md bundled with this module for license details.
 */
namespace FireGento\MageSetup\Test\Unit\Model;

use PHPUnit\Framework\TestCase;

/**
 * Class Config
 *
 * @package FireGento\MageSetup\Test\Unit\Model
 */
class Config extends TestCase
{
    /**
     * @var \FireGento\MageSetup\Model\Config
     */
    private $config;

    public function setUp()
    {
        parent::setUp();

        $readerData = [
            'default' => [
                'system_config' => [
                    'customer__create_account__confirm' => 0
                ]
            ],
            'de'      => [
                'system_config' => [
                    'general__country__default' => 'DE'
                ]
            ],
            'at'      => []
        ];

        $readerMock = $this->getMock('FireGento\MageSetup\Model\Config\Reader', ['read'], [], '', false);
        $readerMock->expects($this->once())->method('read')->will($this->returnValue($readerData));

        $cacheMock = $this->getMock('Magento\Framework\Config\CacheInterface');

        $this->config = new \FireGento\MageSetup\Model\Config(
            $readerMock,
            $cacheMock,
            'de'
        );
    }

    /**
     * @test
     */
    public function getCountry()
    {
        $this->assertEquals('de', $this->config->getCountry());
    }

    /**
     * @test
     */
    public function getAllowedCountries()
    {
        $this->assertEquals([1 => 'de', 2 => 'at'], $this->config->getAllowedCountries());
    }

    /**
     * @test
     */
    public function getSystemConfig()
    {
        $expectedResult = [
            'customer__create_account__confirm' => 0,
            'general__country__default'         => 'DE'
        ];

        $this->assertEquals($expectedResult, $this->config->getSystemConfig());
    }
}
