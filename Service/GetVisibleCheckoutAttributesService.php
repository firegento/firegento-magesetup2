<?php
/**
 * Copyright © 2016 FireGento e.V.
 * See LICENSE.md bundled with this module for license details.
 */
namespace FireGento\MageSetup\Service;

/**
 * Class GetVisibleCheckoutAttributesService
 *
 * @package FireGento\MageSetup\Service
 */
class GetVisibleCheckoutAttributesService implements GetVisibleCheckoutAttributesServiceInterface
{
    /**
     * @var \Magento\Catalog\Api\ProductAttributeRepository
     */
    private $productAttributeRepository;
    /**
     * @var \Magento\Framework\Api\SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * GetVisibleCheckoutAttributesService constructor.
     *
     * @param \Magento\Catalog\Api\ProductAttributeRepositoryInterface $productAttributeRepository
     * @param \Magento\Framework\Api\SearchCriteriaBuilder             $searchCriteriaBuilder
     */
    public function __construct(
        \Magento\Catalog\Api\ProductAttributeRepositoryInterface $productAttributeRepository,
        \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder
    ) {
        $this->productAttributeRepository = $productAttributeRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
    }

    /**
     * @return array|bool
     */
    public function execute()
    {
        $searchCriteria = $this->searchCriteriaBuilder->addFilter('is_visible_on_checkout', 1)->create();
        $attributes = $this->productAttributeRepository->getList($searchCriteria);

        $options = [];
        if (count($attributes->getItems()) > 0) {
            foreach ($attributes->getItems() as $attribute) {
                /** @var \Magento\Catalog\Model\ResourceModel\Eav\Attribute $attribute */

                $options[$attribute->getAttributeCode()] = $attribute;
            }
        }

        return $options;
    }
}
