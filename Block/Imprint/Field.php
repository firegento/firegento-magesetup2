<?php
/**
 * Copyright © 2015 FireGento e.V. - All rights reserved.
 * See LICENSE.md bundled with this module for license details.
 */
namespace FireGento\MageSetup\Block\Imprint;

use Magento\Framework\View\Element\Template;

/**
 * Class Field
 *
 * @package FireGento\MageSetup\Block\Imprint
 */
class Field extends \FireGento\MageSetup\Block\Imprint\Content
{
    /**
     * Retrieve the system configuration value for the given field
     *
     * @return string
     */
    protected function _toHtml()
    {
        if ($this->getField() == 'email') {
            return $this->getEmail(true);
        }

        return $this->getImprintValue($this->getField());
    }
}
