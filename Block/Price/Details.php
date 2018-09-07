<?php
/**
 * Copyright © 2016 FireGento e.V.
 * See LICENSE.md bundled with this module for license details.
 */
namespace FireGento\MageSetup\Block\Price;

use Magento\Customer\Model\ResourceModel\GroupRepository;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class Details
 *
 * @package FireGento\MageSetup\Block\Price
 */
class Details extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Magento\Framework\Pricing\SaleableInterface
     */
    private $saleableItem;

    /**
     * @var \FireGento\MageSetup\Model\System\Config
     */
    private $magesetupConfig;

    /**
     * @var \Magento\Customer\Model\Session
     */
    private $customerSession;

    /**
     * @var GroupRepository
     */
    private $groupRepository;

    /**
     * @var \Magento\Tax\Model\Calculation\Proxy
     */
    private $taxCalculation;

    /**
     * @var \Magento\Tax\Helper\Data
     */
    private $taxHelper;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \FireGento\MageSetup\Model\System\Config         $magesetupConfig
     * @param \Magento\Customer\Model\Session                  $customerSession
     * @param GroupRepository                                  $groupRepository
     * @param \Magento\Tax\Model\Calculation\Proxy             $taxCalculation
     * @param \Magento\Tax\Helper\Data                         $taxHelper
     * @param array                                            $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \FireGento\MageSetup\Model\System\Config $magesetupConfig,
        \Magento\Customer\Model\Session $customerSession,
        GroupRepository $groupRepository,
        \Magento\Tax\Model\Calculation\Proxy $taxCalculation,
        \Magento\Tax\Helper\Data $taxHelper,
        array $data = []
    ) {
        $this->storeManager = $context->getStoreManager();
        $this->magesetupConfig = $magesetupConfig;
        $this->customerSession = $customerSession;
        $this->groupRepository = $groupRepository;
        $this->taxCalculation = $taxCalculation;
        $this->taxHelper = $taxHelper;

        parent::__construct($context, $data);
    }

    /**
     * @param \Magento\Framework\Pricing\SaleableInterface $saleableItem
     */
    public function setSaleableItem(\Magento\Framework\Pricing\SaleableInterface $saleableItem)
    {
        $this->saleableItem = $saleableItem;
        $this->unsetData('tax_rate');
    }

    /**
     * @return string
     */
    public function getFormattedTaxRate()
    {
        if (!$this->hasData('tax_rate')) {
            $this->setData('tax_rate', $this->getTaxPercentBySaleableItem());
        }

        return __('%1%', $this->getData('tax_rate'));
    }

    /**
     * @return int
     */
    public function getPriceDisplayType()
    {
        return $this->taxHelper->getPriceDisplayType();
    }

    /**
     * @return bool
     */
    public function isIncludingShippingCosts()
    {
        if (!$this->getData('is_including_shipping_costs')) {
            $this->setData('is_including_shipping_costs', $this->magesetupConfig->isIncludingShippingCosts());
        }

        return $this->getData('is_including_shipping_costs');
    }

    /**
     * @return bool
     */
    public function canShowShippingLink()
    {
        $productTypeId = $this->saleableItem->getTypeId();
        $ignoreTypeIds = array('virtual', 'downloadable');
        if (in_array($productTypeId, $ignoreTypeIds)) {
            return false;
        }

        return true;
    }

    /**
     * @return string|bool
     */
    public function getShippingCostUrl()
    {
        return $this->magesetupConfig->getShippingCostUrl();
    }

    /**
     * @return float|int
     */
    private function getTaxPercentBySaleableItem()
    {
        $taxPercent = $this->saleableItem->getTaxPercent();
        if (is_null($taxPercent)) {
            $productTaxClassId = $this->saleableItem->getTaxClassId();
            if ($productTaxClassId) {
                $store = $this->storeManager->getStore();
                $groupId = $this->customerSession->getCustomerGroupId();
                $group = $this->groupRepository->getById($groupId);
                $customerTaxClassId = $group->getTaxClassId();

                $request = $this->taxCalculation->getRateRequest(null, null, $customerTaxClassId, $store);
                $request->setData('product_class_id', $productTaxClassId);

                $taxPercent = $this->taxCalculation->getRate($request);
            }
        }
        if ($taxPercent) {
            return $taxPercent;
        }

        return 0;
    }
}
