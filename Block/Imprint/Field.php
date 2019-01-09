<?php
/**
 * Copyright © 2016 FireGento e.V.
 * See LICENSE.md bundled with this module for license details.
 */
namespace FireGento\MageSetup\Block\Imprint;

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
    // phpcs:ignore
    protected function _toHtml()
    {
        if ($this->getField() == 'email') {
            return $this->getEmail(true);
        }

        return $this->getImprintValue($this->getField());
    }
}
