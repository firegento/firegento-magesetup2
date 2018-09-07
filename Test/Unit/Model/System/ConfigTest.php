<?php
/**
 * Copyright © 2016 FireGento e.V.
 * See LICENSE.md bundled with this module for license details.
 */
namespace FireGento\MageSetup\Test\Unit\Model\System;

use Magento\Framework\TestFramework\Unit\BaseTestCase;

/**
 * Class Config
 *
 * @package FireGento\MageSetup\Test\Unit\Model\System
 */
class Config extends BaseTestCase
{
    /**
     * @var \FireGento\MageSetup\Model\Config
     */
    private $config;

    public function setUp()
    {
        parent::setUp();

        $scopeConfigMock = $this->getMock(\Magento\Framework\App\Config\ScopeConfigInterface::class);
        $scopeConfigMock->expects($this->any())->method('getValue')
            ->with($this->equalTo('general/country/eu_countries'))
            ->will($this->returnValue('DE,AT'));

        $context = $this->getMock(\Magento\Framework\App\Helper\Context::class, [], [], '', false);
        $context->expects($this->any())->method('getScopeConfig')->will($this->returnValue($scopeConfigMock));
        /** @var \Magento\Framework\App\Helper\Context $context */

        // $this->config = new \FireGento\MageSetup\Model\System\Config($context);
        $this->config = $this->objectManager->getObject(
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
