<?php

declare(strict_types=1);

namespace FireGento\MageSetup\Plugin\Tax\Config;

use FireGento\MageSetup\Model\System\Config\Source\Tax\Dynamic as FireGentoSource;
use Magento\Customer\Model\ResourceModel\GroupRepository;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Quote\Api\Data\ShippingAssignmentInterface;
use Magento\Quote\Model\Quote\Item;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\Store;
use Magento\Tax\Api\Data\TaxClassKeyInterface;
use Magento\Tax\Model\Calculation\Proxy;

/**
 * Class ShippingTaxPlugin
 *
 * FireGento\MageSetup\Plugin\Tax\Config
 */
class ShippingTaxPlugin
{

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var Session
     */
    private $customerSession;

    /**
     * @var GroupRepository
     */
    private $groupRepository;

    /**
     * @var Proxy
     */
    private $taxCalculation;

    /**
     * @var \Magento\Tax\Api\Data\TaxClassKeyInterfaceFactory
     */
    private $taxClassKeyDataObjectFactory;

    /**
     * Constructor class
     *
     * @param ScopeConfigInterface                              $scopeConfig
     * @param Session                                           $customerSession
     * @param GroupRepository                                   $groupRepository
     * @param \Magento\Tax\Api\Data\TaxClassKeyInterfaceFactory $taxClassKeyDataObjectFactory
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        Session $customerSession,
        GroupRepository $groupRepository,
        \Magento\Tax\Api\Data\TaxClassKeyInterfaceFactory $taxClassKeyDataObjectFactory
    ) {
        $this->scopeConfig                  = $scopeConfig;
        $this->customerSession              = $customerSession;
        $this->groupRepository              = $groupRepository;
        $this->taxClassKeyDataObjectFactory = $taxClassKeyDataObjectFactory;
    }

    /**
     * After plugin for \Magento\Tax\Model\Sales\Total\Quote\CommonTaxCollector::getShippingDataObject
     *
     * @param                                          $subject
     * @param                                          $result
     * @param ShippingAssignmentInterface              $shippingAssignment
     * @param \Magento\Quote\Model\Quote\Address\Total $total
     * @param                                          $useBaseCurrency
     *
     * @return null
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function afterGetShippingDataObject(
        $subject,
        $result,
        ShippingAssignmentInterface $shippingAssignment,
        \Magento\Quote\Model\Quote\Address\Total $total,
        $useBaseCurrency
    ) {
        if ($result === null) {
            return $result; // no shipping tax calculation
        }

        $quoteItems = $shippingAssignment->getItems();
        if (count($quoteItems) === 0) {
            return $result;  // no products -> no shipping -> nothing to do
        }

        $store       = $quoteItems[0]->getQuote()->getStore();
        $dynamicType = $this->getDynamicShippingConfigPath($store);

        if ($dynamicType === FireGentoSource::DYNAMIC_TYPE_SHIPPING_TAX_DEFAULT) {
            return $result;  // If the default behaviour was configured -> use default tax class id
        }

        $taxClassId = false;
        if ($dynamicType === FireGentoSource::DYNAMIC_TYPE_HIGHEST_PRODUCT_TAX) {
            $taxClassId = $this->getHighestProductTaxClassId($quoteItems, $store);
        }

        // If no tax class id was found, use default one
        if (!$taxClassId) {
            return $result;
        }

        return $result->setTaxClassKey(
            $this->taxClassKeyDataObjectFactory->create()
                ->setType(TaxClassKeyInterface::TYPE_ID)
                ->setValue($taxClassId)
        );
    }

    /**
     * Method for getting highest product tax class id
     *
     * @param                            $quoteItems
     * @param null|string|bool|int|Store $store
     *
     * @return bool|int
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function getHighestProductTaxClassId($quoteItems, $store)
    {
        $taxClassIds       = [];
        $highestTaxClassId = false;

        foreach ($quoteItems as $quoteItem) {

            /** @var $quoteItem Item */
            if ($quoteItem->getParentItem()) {
                continue;
            }

            // Retrieve the tax percent
            $taxPercent = $quoteItem->getTaxPercent();
            if (!$taxPercent) {
                $taxPercent = $this->getTaxPercent($quoteItem->getTaxClassId(), $store);
            }

            // Add the tax class
            if (($taxPercent) && !in_array($taxPercent, $taxClassIds)) {
                $taxClassIds[$taxPercent] = (int)$quoteItem->getTaxClassId();
            }
        }

        /**
         * Fetch the highest tax rate
         */
        ksort($taxClassIds);
        if (count($taxClassIds) > 0) {
            $highestTaxClassId = array_pop($taxClassIds);
        }
        if (!$highestTaxClassId || $highestTaxClassId === null) {
            return false;
        }

        return $highestTaxClassId;
    }

    /**
     * Method for getting tax prcentage
     *
     * @param int                        $productTaxClassId
     * @param null|string|bool|int|Store $store
     *
     * @return int
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function getTaxPercent(int $productTaxClassId, $store): int
    {
        $groupId            = $this->customerSession->getCustomerGroupId();
        $group              = $this->groupRepository->getById($groupId);
        $customerTaxClassId = $group->getTaxClassId();

        $request = $this->taxCalculation->getRateRequest(null, null, $customerTaxClassId, $store);
        $request->setData('product_class_id', $productTaxClassId);

        $taxPercent = $this->taxCalculation->getRate($request);
        if (!$taxPercent) {
            $taxPercent = 0;
        }

        return $taxPercent;
    }

    /**
     * Method for retrieving dynamic config shipping path
     *
     * @param null|string|bool|int|Store $store
     *
     * @return int
     */
    private function getDynamicShippingConfigPath($store = null): int
    {
        return (int)$this->scopeConfig->getValue(
            'tax/classes/dynamic_shipping_tax_class',
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }
}
