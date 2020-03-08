<?php
/**
 * Copyright © 2016 FireGento e.V.
 * See LICENSE.md bundled with this module for license details.
 */
namespace FireGento\MageSetup\Test\Unit\Model\System;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\TestCase;

/**
 * Class Config
 *
 * @package FireGento\MageSetup\Test\Unit\Model\System
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

        $scopeConfigMock = $this->createMock(\Magento\Framework\App\Config\ScopeConfigInterface::class);
        $scopeConfigMock->expects($this->any())->method('getValue')
            ->with($this->equalTo('general/country/eu_countries'))
            ->will($this->returnValue('DE,AT'));

        $context = $this->createMock(\Magento\Framework\App\Helper\Context::class, [], [], '', false);
        $context->expects($this->any())->method('getScopeConfig')->will($this->returnValue($scopeConfigMock));
        /** @var \Magento\Framework\App\Helper\Context $context */

        $objectManager = new ObjectManager($this);

        $this->config = $objectManager->getObject(
            \FireGento\MageSetup\Model\System\Config::class,
            [
                'context' => $context
            ]
        );
    }

    /**
     * @test
     */
    public function isCountryInEu()
    {
        echo PHP_VERSION;
        $this->assertTrue($this->config->isCountryInEu('DE'));
        $this->assertFalse($this->config->isCountryInEu('CH'));
    }

    /**
     * @test
     */
    public function getEuCountries()
    {
        $this->assertEquals(['DE', 'AT'], $this->config->getEuCountries());
    }
}
