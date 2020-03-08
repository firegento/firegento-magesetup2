<?php
/**
 * Copyright © 2016 FireGento e.V.
 * See LICENSE.md bundled with this module for license details.
 */
namespace FireGento\MageSetup\Service;

/**
 * Interface GetVisibleCheckoutAttributesServiceInterface
 *
 * @package FireGento\MageSetup\Service
 */
interface GetVisibleCheckoutAttributesServiceInterface
{
    /**
     * Get visible checkout attributes
     *
     * @return array|bool
     */
    public function execute();
}
