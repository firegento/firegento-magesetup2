<?php
/**
 * Copyright © 2015 FireGento e.V. - All rights reserved.
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
     * @return string
     */
    public function getCountry();

    /**
     * @return array
     */
    public function getAllowedCountries();

    /**
     * @return array
     */
    public function getSystemConfig();

    /**
     * @return array|bool
     */
    public function getTaxClasses();

    /**
     * @return array|bool
     */
    public function getTaxCalculationRates();

    /**
     * @return array|bool
     */
    public function getTaxCalculationRules();

    /**
     * @return array|bool
     */
    public function getAgreements();
}
