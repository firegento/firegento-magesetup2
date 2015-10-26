<?php
/**
 * Copyright © 2015 FireGento e.V. - All rights reserved.
 * See LICENSE.md bundled with this module for license details.
 */
namespace FireGento\MageSetup\Model\Config\Source;

use \Magento\Tax\Model\ResourceModel\TaxClass\CollectionFactory;
use \Magento\Eav\Model\ResourceModel\Entity\Attribute\OptionFactory;
use \Magento\Tax\Api\TaxClassRepositoryInterface;
use \Magento\Framework\Api\SearchCriteriaBuilder;
use \Magento\Framework\Api\FilterBuilder;
use \Magento\Catalog\Api\ProductRepositoryInterface;

/**
 * Class ProductTaxClass
 * @package FireGento\MageSetup\Model\Config\Source
 */
class ProductTaxClass extends \Magento\Tax\Model\TaxClass\Source\Product
{
    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * @param CollectionFactory $classesFactory
     * @param OptionFactory $optionFactory
     * @param TaxClassRepositoryInterface $taxClassRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param FilterBuilder $filterBuilder
     * @param ProductRepositoryInterface $productRepository
     */
    public function __construct(
        CollectionFactory $classesFactory,
        OptionFactory $optionFactory,
        TaxClassRepositoryInterface $taxClassRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        FilterBuilder $filterBuilder,
        ProductRepositoryInterface $productRepository
    ) {
        $this->productRepository = $productRepository;
        parent::__construct($classesFactory, $optionFactory, $taxClassRepository, $searchCriteriaBuilder, $filterBuilder);
    }

    /**
     * @param bool $withEmpty
     * @return array
     */
    public function getAllOptions($withEmpty = false)
    {
        $options = parent::getAllOptions($withEmpty);
        foreach ($options as $optionKey => $option) {
            if (intval($option['value']) <= 0) {
                continue;
            }

            $searchCriteria = $this->_searchCriteriaBuilder->addFilter('tax_class_id', $option['value'], 'like');
            $productCollection = $this->$productRepository->getList($searchCriteria);

            if (!$productCollection->getTotalCount()) {
                unset($options[$optionKey]);
            }
        }

        return $options;
    }
}
