<?php
/**
 * Copyright © 2016 FireGento e.V.
 * See LICENSE.md bundled with this module for license details.
 */

namespace FireGento\MageSetup\Plugin\Catalog\Model\Attribute;

use FireGento\MageSetup\Service\GetVisibleCheckoutAttributesServiceInterface;
use Magento\Catalog\Model\Attribute\Config;

/**
 * Plugin to add visible in checkout attributes to the attribute names.
 */
class AddVisibleInCheckoutAttributesToAttributeNamesPlugin
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
     *  Around get attribute names
     *
     * @param Config $config
     * @param array $attributeNames
     * @param string $groupName
     * @return array
     */
    public function afterGetAttributeNames(Config $config, $attributeNames, $groupName)
    {
        if (!is_array($attributeNames)) {
            return $attributeNames;
        }

        if ($groupName === 'quote_item') {
            $attributes = $this->getVisibleCheckoutAttributesService->execute();
            foreach ($attributes as $attributeCode => $attribute) {
                $attributeNames[] = $attributeCode;
            }
        }

        return $attributeNames;
    }
}
