<?php
/**
 * Copyright © 2016 FireGento e.V.
 * See LICENSE.md bundled with this module for license details.
 */

namespace FireGento\MageSetup\Plugin\Catalog\Helper\Product\Configuration;

use FireGento\MageSetup\Service\GetVisibleCheckoutAttributesServiceInterface;
use Magento\Catalog\Helper\Product\Configuration;
use Magento\Catalog\Model\Product\Configuration\Item\ItemInterface;

/**
 * Plugin to add visible in checkout attributes to the custom option list.
 */
class AddVisibleInCheckoutAttributesToCustomOptionsPlugin
{
    /**
     * @var GetVisibleCheckoutAttributesServiceInterface
     */
    private $getVisibleCheckoutAttributesService;

    /**
     * AroundGetCustomOptionsPlugin constructor.
     *
     * @param GetVisibleCheckoutAttributesServiceInterface $getVisibleCheckoutAttributesService
     */
    public function __construct(GetVisibleCheckoutAttributesServiceInterface $getVisibleCheckoutAttributesService)
    {
        $this->getVisibleCheckoutAttributesService = $getVisibleCheckoutAttributesService;
    }

    /**
     * Adds visible in checkout attribute to the custom options.
     *
     * @param Configuration $configuration
     * @param array $customOptions
     * @param ItemInterface $item
     *
     * @return array
     */
    public function afterGetCustomOptions(Configuration $configuration, array $customOptions, ItemInterface $item)
    {
        $attributes = $this->getVisibleCheckoutAttributesService->execute();
        foreach ($attributes as $attribute) {
            $value = $attribute->getFrontend()->getValue($item->getProduct());
            if (!$value) {
                continue;
            }

            $customOptions[] = [
                'label'       => $attribute->getStoreLabel(),
                'value'       => $value,
                'print_value' => $value
            ];
        }

        return $customOptions;
    }
}
