<?php
/**
 * Diglin GmbH - Switzerland
 *
 * @author      Sylvain Rayé <support at diglin.com>
 * @category    Diglin
 * @package     Indianershop_Base
 * @copyright   Copyright (c) 2011-2015 Diglin (http://www.diglin.com)
 */

namespace FireGento\MageSetup\Model\Config\Source;

use \Magento\Customer\Api\CustomerRepositoryInterface;

class CustomerTaxClass extends \Magento\Tax\Model\TaxClass\Source\Customer
{
    /**
     * @var CustomerRepositoryInterface
     */
    protected $customerRepository;

    /**
     * @param \Magento\Tax\Api\TaxClassRepositoryInterface $taxClassRepository
     * @param \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder
     * @param \Magento\Framework\Api\FilterBuilder $filterBuilder
     * @param CustomerRepositoryInterface $customerRepository
     */
    public function __construct(
        \Magento\Tax\Api\TaxClassRepositoryInterface $taxClassRepository,
        \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder,
        \Magento\Framework\Api\FilterBuilder $filterBuilder,
        CustomerRepositoryInterface $customerRepository
    ) {
        $this->customerRepository = $customerRepository;
        parent::__construct($taxClassRepository, $searchCriteriaBuilder, $filterBuilder);
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

            $searchCriteria = $this->searchCriteriaBuilder->addFilter('tax_class_id', $option['value'], 'eq');
            $customerGroupCollection = $this->customerRepository->getList($searchCriteria);

            if (!$customerGroupCollection->getTotalCount()) {
                unset($options[$optionKey]);
            }
        }

        return $options;
    }
}