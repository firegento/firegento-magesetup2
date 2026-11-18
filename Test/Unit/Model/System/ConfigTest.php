<?php
/**
 * Copyright © 2016 FireGento e.V.
 * See LICENSE.md bundled with this module for license details.
 */
namespace FireGento\MageSetup\Test\Unit\Model\System;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\TestCase;

class ConfigTest extends TestCase
{
    /**
     * @var \FireGento\MageSetup\Model\System\Config
     */
    private $config;

    public function setUp(): void
    {
        parent::setUp();

        $scopeConfigMock = $this->createMock(ScopeConfigInterface::class);
        $scopeConfigMock->method('getValue')
            ->with(self::equalTo('general/country/eu_countries'))
            ->willReturn('DE,AT');

        $context = $this->createMock(Context::class);
        $context->method('getScopeConfig')->willReturn($scopeConfigMock);
        /** @var Context $context */

        $objectManager = new ObjectManager($this);

        $this->config = $objectManager->getObject(
            \FireGento\MageSetup\Model\System\Config::class,
            [
                'context' => $context
            ]
        );
    }

    public function testIsCountryInEu()
    {
        self::assertTrue($this->config->isCountryInEu('DE'));
        self::assertFalse($this->config->isCountryInEu('CH'));
    }

    public function testGetEuCountries()
    {
        self::assertEquals(['DE', 'AT'], $this->config->getEuCountries());
    }
}
