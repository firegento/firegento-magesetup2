<?php
/**
 * Copyright © 2016 FireGento e.V.
 * See LICENSE.md bundled with this module for license details.
 */
namespace FireGento\MageSetup\Test\Unit\Model;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
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

        $readerMock = $this->createMock(\FireGento\MageSetup\Model\Config\Reader::class);
        $readerMock->expects($this->once())->method('read')->will($this->returnValue($readerData));

        $cacheMock = $this->getMockBuilder(\Magento\Framework\Config\CacheInterface::class)
            ->disableOriginalConstructor()->getMockForAbstractClass();

        $objectManager = new ObjectManager($this);

        $this->config = $objectManager->getObject(\FireGento\MageSetup\Model\Config::class, [
            'reader' => $readerMock,
            'cache' => $cacheMock,
            'country' => 'de'
        ]);
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
