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
 * @since     1.2.0
 */

/**
 * CMS Source model for configuration dropdown of CMS pages
 *
 * @category FireGento
 * @package  FireGento_MageSetup
 * @author   FireGento Team <team@firegento.com>
 */
class FireGento_MageSetup_Model_Source_Tax_DynamicType
{
    /**
     * Options getter
     *
     * @return array Dynamic types as option array
     */
    public function toOptionArray()
    {
        $helper = Mage::helper('magesetup');

        return array(
            array(
                'value' => 0,
                'label' => $helper->__('No dynamic shipping tax caluclation')
            ),
            array(
                'value' => FireGento_MageSetup_Model_Tax_Config::USE_HIGHEST_TAX_ON_PRODUCTS,
                'label' => $helper->__('Use the highest product tax')
            ),
        );
    }
}
