<?php
/**
 * Copyright © 2016 FireGento e.V.
 * See LICENSE.md bundled with this module for license details.
 */
namespace FireGento\MageSetup\Model;

/**
 * Interface ConfigInterface
 *
 * @package FireGento\MageSetup\Model
 */
interface ConfigInterface
{
    /**
     * @type string
     */
    const CACHE_ID = 'magesetup_config';

    /**
     * @type string
     */
    const DEFAULT_NODE = 'default';

    /**
     * Get country
     *
     * @return string
     */
    public function getCountry();

    /**
     * Get allowed countries
     *
     * @return array
     */
    public function getAllowedCountries();

    /**
     * Get system config
     *
     * @return array
     */
    public function getSystemConfig();

    /**
     * Get tax classes
     *
     * @return array|bool
     */
    public function getTaxClasses();

    /**
     * Get tax calculation rates
     *
     * @return array|bool
     */
    public function getTaxCalculationRates();

    /**
     * Get tax calculation rules
     *
     * @return array|bool
     */
    public function getTaxCalculationRules();

    /**
     * Get agreements
     *
     * @return array|bool
     */
    public function getAgreements();
}
