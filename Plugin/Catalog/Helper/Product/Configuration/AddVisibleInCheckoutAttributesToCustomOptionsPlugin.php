<?php
/**
 * Copyright © 2016 FireGento e.V.
 * See LICENSE.md bundled with this module for license details.
 */

namespace FireGento\MageSetup\Plugin\Catalog\Helper\Product\Configuration;

use FireGento\MageSetup\Service\GetVisibleCheckoutAttributesServiceInterface;
use Magento\Catalog\Helper\Product\Configuration;
use Magento\Catalog\Model\Product\Configuration\Item\ItemInterface;
use Magento\Quote\Model\Quote\Item\AbstractItem;

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

        $configurableAttributes = $item->getOptionByCode('attributes') ? $item->getOptionByCode('attributes')->getValue() : [];
        $configurableAttributes = $configurableAttributes ? array_keys(json_decode($configurableAttributes, true)) : [];

        $product = $item->getProduct();

        if (!$product) {
            return [];
        }

        foreach ($attributes as $attribute) {
            $attributeFrontend = $attribute->getFrontend();
            $value = $attributeFrontend->getValue($product);

            if ($item instanceof AbstractItem && $product->getTypeId() === 'configurable') {
                if (in_array($attribute->getId(), $configurableAttributes) || !count($item->getChildren())) {
                    // attribute is a configurable attribute. Magento will print it separately
                    // or item has no children (but this should never occur)
                    continue;
                }

                $children = $item->getChildren();
                if ($children[0] instanceof AbstractItem && $children[0]->getProduct()) {
                    // fetch the attribute value of the child
                    $value = $attributeFrontend->getValue($children[0]->getProduct()) ?: $value;
                }
            }

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
