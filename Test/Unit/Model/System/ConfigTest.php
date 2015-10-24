<?php
/**
 * Copyright © 2015 FireGento e.V. - All rights reserved.
 * See LICENSE.md bundled with this module for license details.
 */
namespace FireGento\MageSetup\Test\Unit\Model\System;

/**
 * Class Config
 *
 * @package FireGento\MageSetup\Test\Unit\Model\System
 */
class Config extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \FireGento\MageSetup\Model\Config
     */
    private $config;

    public function setUp()
    {
        parent::setUp();

        $scopeConfigMock = $this->getMock('\Magento\Framework\App\Config\ScopeConfigInterface');
        $scopeConfigMock->expects($this->any())->method('getValue')->with($this->equalTo('general/country/eu_countries'))->will($this->returnValue('DE,AT'));

        $this->config = new \FireGento\MageSetup\Model\System\Config($scopeConfigMock);
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
