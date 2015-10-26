<?php
/**
 * This file is part of a FireGento e.V. module.
 *
 * This FireGento e.V. module is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License version 3 as
 * published by the Free Software Foundation.
 *
 * This script is distributed in the hope that it will be useful, but WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS
 * FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 *
 * PHP version 5
 *
 * @category  FireGento
 * @package   FireGento_MageSetup
 * @author    FireGento Team <team@firegento.com>
 * @copyright 2013-2015 FireGento Team (http://www.firegento.com)
 * @license   http://opensource.org/licenses/gpl-3.0 GNU General Public License, version 3 (GPLv3)
 * @version   2.2.2
 * @since     0.1.0
 */

/**
 * Tax Source model for new product tax classes, possibly not created yet
 *
 * @category FireGento
 * @package  FireGento_MageSetup
 * @author   FireGento Team <team@firegento.com>
 */
class FireGento_MageSetup_Model_Source_Tax_NewProductTaxClass
    extends Mage_Eav_Model_Entity_Attribute_Source_Abstract
{
    /**
     * @var array $_options cached options
     */
    protected $_options = array();

    /**
     * @var string
     */
    protected $_defaultOption = 0;

    /**
     * Return option array
     *
     * @return array New product tax classes as option array
     */
    public function toOptionArray()
    {
        if (!sizeof($this->_options)) {
            $taxClasses = $this->_getConfigNode('tax_classes', 'default');
            foreach ($taxClasses as $identifier => $taxClass) {
                if ($taxClass['class_type'] != 'PRODUCT'
                    || $taxClass['execute'] != 1
                    || strpos($identifier, 'shipping') === 0
                ) {
                    continue;
                }

                $this->_options[] = array(
                    'value' => $taxClass['class_id'],
                    'label' => $taxClass['class_name'],
                );
            }
            array_unshift($this->_options, array('value' => '', 'label' => ''));
        }

        return $this->_options;
    }

    /**
     * Get all options as array
     *
     * @return array New product tax classes as option array
     */
    public function getAllOptions()
    {
        return $this->toOptionArray();
    }

    /**
     * Get default tax class
     *
     * @return int Default tax class option
     */
    public function getDefaultOption()
    {
        if (!$this->_defaultOption) {
            $taxClasses = $this->_getConfigNode('tax_classes', 'default');
            foreach ($taxClasses as $identifier => $taxClass) {
                if ($taxClass['class_type'] != 'PRODUCT'
                    || $taxClass['execute'] != 1
                    || strpos($identifier, 'shipping') === 0
                ) {
                    continue;
                }

                if ($taxClass['default'] == 1) {
                    $this->_defaultOption = $taxClass['class_id'];
                    break;
                }
            }
        }

        return $this->_defaultOption;

    }

    /**
     * Get config.xml data
     *
     * @param  string      $node      xml node
     * @param  string|null $childNode if set, child node of the first node
     * @return array Config node
     */
    protected function _getConfigNode($node, $childNode = null)
    {
        $configData = $this->getConfigData();
        if ($childNode) {
            return $configData[$node][$childNode];
        } else {
            return $configData[$node];
        }
    }

    /**
     * Get config.xml data
     *
     * @return array Config data
     */
    public function getConfigData()
    {
        $configData = Mage::getSingleton('magesetup/config')
            ->getNode('default/magesetup')
            ->asArray();

        return $configData;
    }
}
