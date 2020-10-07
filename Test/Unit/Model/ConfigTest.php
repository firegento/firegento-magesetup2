<?php
/**
 * Copyright © FireGento e.V.
 * See LICENSE.md bundled with this module for license details.
 */
namespace FireGento\MageSetup\Test\Unit\Model;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\TestCase;

/**
 * Class Config
 *
 * Unit tests for imprint config block
 */
class ConfigTest extends TestCase
{
    /**
     * @var \FireGento\MageSetup\Model\Config
     */
    private $config;

    public function setUp(): void
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
        $readerMock->expects(self::once())->method('read')->willReturn($readerData);

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
        self::assertEquals('de', $this->config->getCountry());
    }

    /**
     * @test
     */
    public function getAllowedCountries()
    {
        self::assertEquals([1 => 'de', 2 => 'at'], $this->config->getAllowedCountries());
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

        self::assertEquals($expectedResult, $this->config->getSystemConfig());
    }
}
