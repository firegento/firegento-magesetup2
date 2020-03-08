<?php
/**
 * Copyright © 2018 FireGento e.V.
 * See LICENSE.md bundled with this module for license details.
 */

namespace FireGento\MageSetup\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\ScopeInterface;

/**
 * Class AroundRenderPlugin
 *
 * @package FireGento\MageSetup\Helper
 */
class Data extends AbstractHelper
{
    /**
     * Get config value
     *
     * @param mixed $field
     * @param mixed $storeId
     * @return mixed
     */
    public function getConfigValue($field, $storeId = null)
    {
        return $this->scopeConfig->getValue(
            $field,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }
}
