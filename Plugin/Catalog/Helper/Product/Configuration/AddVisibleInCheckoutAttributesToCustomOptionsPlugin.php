<?php
/**
 * Copyright © 2016 FireGento e.V.
 * See LICENSE.md bundled with this module for license details.
 */

namespace FireGento\MageSetup\Plugin\Catalog\Helper\Product\Configuration;

use FireGento\MageSetup\Service\GetVisibleCheckoutAttributesServiceInterface;
use Magento\Catalog\Helper\Product\Configuration;
use Magento\Catalog\Model\Product\Configuration\Item\ItemInterface;
use Magento\Framework\Serialize\Serializer\Json;
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
     * @var Json
     */
    private $json;

    /**
     * AroundGetCustomOptionsPlugin constructor.
     *
     * @param GetVisibleCheckoutAttributesServiceInterface $getVisibleCheckoutAttributesService
     * @param Json $json
     */
    public function __construct(
        GetVisibleCheckoutAttributesServiceInterface $getVisibleCheckoutAttributesService,
        Json $json
    ) {
        $this->getVisibleCheckoutAttributesService = $getVisibleCheckoutAttributesService;
        $this->json = $json;
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
        $product = $item->getProduct();

        if (!$product) {
            return [];
        }

        $configurableAttributes = $item->getOptionByCode('attributes')
            ? $item->getOptionByCode('attributes')->getValue()
            : [];
        $configurableAttributes = is_string($configurableAttributes)
            ? array_keys($this->json->unserialize($configurableAttributes) ?: [])
            : [];

        $attributes = $this->getVisibleCheckoutAttributesService->execute();
        foreach ($attributes as $attributeCode => $attribute) {
            $attributeFrontend = $attribute->getFrontend();
            if ($attributeCode === 'sku') {
                $value = $product->getSku();
            } else {
                $value = $attributeFrontend->getValue($product);
            }

            if ($item instanceof AbstractItem && $product->getTypeId() === 'configurable') {
                if (in_array($attribute->getId(), $configurableAttributes) || !count($item->getChildren())) {
                    // attribute is a configurable attribute. Magento will output it separately
                    // or item has no children (but this should never occur)
                    continue;
                }

                $children = $item->getChildren();
                if ($children[0] instanceof AbstractItem && $children[0]->getProduct()) {
                    // fetch the attribute value of the child
                    $childValue = $attributeFrontend->getValue($children[0]->getProduct());
                    // Be careful: "0" is a valid value for setting a yes/no attribute to false, so the check must be
                    // against explicit null
                    $value = $childValue === null ? $value : $childValue;
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
