<?php
/**
 * Copyright © 2016 FireGento e.V.
 * See LICENSE.md bundled with this module for license details.
 */
namespace FireGento\MageSetup\Service;

/**
 * Interface to retrieve the visible in checkout attributes.
 */
interface GetVisibleCheckoutAttributesServiceInterface
{
    /**
     * Get visible checkout attributes
     *
     * @return array
     */
    public function execute();
}
