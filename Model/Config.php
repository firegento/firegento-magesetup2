<?php
/**
 * Copyright © 2016 FireGento e.V.
 * See LICENSE.md bundled with this module for license details.
 */
namespace FireGento\MageSetup\Model;

use FireGento\MageSetup\Model\Config\Reader;
use Magento\Framework\Config\CacheInterface;

/**
 * Class Config
 *
 * @package FireGento\MageSetup\Model
 */
class Config implements ConfigInterface
{
    /**
     * Configuration reader
     *
     * @var \Magento\Framework\Config\ReaderInterface
     */
    private $reader;

    /**
     * Configuration cache
     *
     * @var \Magento\Framework\Config\CacheInterface
     */
    private $cache;

    /**
     * @var string
     */
    private $country;

    /**
     * @var array
     */
    private $loadedConfig;

    /**
     * Config constructor.
     *
     * @param Reader $reader
     * @param CacheInterface $cache
     * @param mixed $country
     */
    public function __construct(
        Reader $reader,
        CacheInterface $cache,
        $country
    ) {
        $this->reader = $reader;
        $this->cache = $cache;
        $this->country = $country;

        $this->initialize();
    }

    /**
     * Get country
     *
     * @return string
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * Get allowed countries
     *
     * @return array
     */
    public function getAllowedCountries()
    {
        $countries = array_keys($this->loadedConfig);

        // remove the default country
        $key = array_search('default', $countries);
        if (false !== $key) {
            unset($countries[$key]);
        }

        return $countries;
    }

    /**
     * Get system config
     *
     * @return array
     */
    public function getSystemConfig()
    {
        $countrySystemConfig = [];
        if (isset($this->loadedConfig[$this->getCountry()]['system_config'])) {
            $countrySystemConfig = $this->loadedConfig[$this->getCountry()]['system_config'];
        }

        return array_merge($this->loadedConfig[self::DEFAULT_NODE]['system_config'], $countrySystemConfig);
    }

    /**
     * Get tax classes
     *
     * @return array|bool
     */
    public function getTaxClasses()
    {
        if (isset($this->loadedConfig[$this->getCountry()]['tax']['tax_classes'])) {
            return $this->loadedConfig[$this->getCountry()]['tax']['tax_classes'];
        }

        return false;
    }

    /**
     * Get tax calculation rates
     *
     * @return array|bool
     */
    public function getTaxCalculationRates()
    {
        if (isset($this->loadedConfig[$this->getCountry()]['tax']['tax_calculation_rates'])) {
            return $this->loadedConfig[$this->getCountry()]['tax']['tax_calculation_rates'];
        }

        return false;
    }

    /**
     * Get tax calculation rules
     *
     * @return array|bool
     */
    public function getTaxCalculationRules()
    {
        if (isset($this->loadedConfig[$this->getCountry()]['tax']['tax_calculation_rules'])) {
            return $this->loadedConfig[$this->getCountry()]['tax']['tax_calculation_rules'];
        }

        return false;
    }

    /**
     * Get agreements
     *
     * @return array|bool
     */
    public function getAgreements()
    {
        if (isset($this->loadedConfig[$this->getCountry()]['agreements'])) {
            return $this->loadedConfig[$this->getCountry()]['agreements'];
        }

        return false;
    }

    /**
     * Get cms pages
     *
     * @return array|bool
     */
    public function getCmsPages()
    {
        if (isset($this->loadedConfig[$this->getCountry()]['pages'])) {
            return $this->loadedConfig[$this->getCountry()]['pages'];
        }

        return false;
    }

    /**
     * Get cms blocks
     *
     * @return array|bool
     */
    public function getCmsBlocks()
    {
        if (isset($this->loadedConfig[$this->getCountry()]['blocks'])) {
            return $this->loadedConfig[$this->getCountry()]['blocks'];
        }

        return false;
    }

    /**
     * Initialize the configuration
     */
    private function initialize()
    {
        $data = $this->reader->read();
        $this->loadedConfig = $data;
    }
}
