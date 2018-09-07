<?php
/**
 * Copyright © 2016 FireGento e.V.
 * See LICENSE.md bundled with this module for license details.
 */
namespace FireGento\MageSetup\Plugin\Catalog\Model\Attribute;

use \FireGento\MageSetup\Service\GetVisibleCheckoutAttributesServiceInterface;

/**
 * Class AroundGetAttributeNamesPlugin
 *
 * @package FireGento\MageSetup\Plugin\Catalog\Model\Attribute
 */
class AroundGetAttributeNamesPlugin
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
     * @param \Magento\Catalog\Model\Attribute\Config $subject
     * @param \Closure                                $proceed
     * @param  string                                 $groupName
     * @return array
     */
    public function aroundGetAttributeNames(\Magento\Catalog\Model\Attribute\Config $subject, \Closure $proceed, $groupName)
    {
        $attributeNames = $proceed($groupName);

        if ($groupName == 'quote_item') {
            $attributes = $this->getVisibleCheckoutAttributesService->execute();
            foreach ($attributes as $attributeCode => $attribute) {
                $attributeNames[] = $attributeCode;
            }
        }

        return $attributeNames;
    }
}
