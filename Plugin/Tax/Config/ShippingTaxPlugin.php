<?php

declare(strict_types=1);

namespace FireGento\MageSetup\Plugin\Tax\Config;

use Magento\Quote\Api\Data\ShippingAssignmentInterface;
use Magento\Quote\Model\Quote\Address\Total as AddressTotal;
use Magento\Tax\Api\Data\TaxClassKeyInterface;
use Magento\Tax\Api\Data\TaxClassKeyInterfaceFactory;
use Magento\Tax\Model\Sales\Total\Quote\CommonTaxCollector;

/**
 * Class ShippingTaxPlugin
 *
 * FireGento\MageSetup\Plugin\Tax\Config
 */
class ShippingTaxPlugin
{
    /** @var TaxClassKeyInterfaceFactory */
    private $taxClassKeyDataObjectFactory;

    /**
     * @param TaxClassKeyInterfaceFactory $taxClassKeyDataObjectFactory
     */
    public function __construct(
        TaxClassKeyInterfaceFactory $taxClassKeyDataObjectFactory
    ) {
        $this->taxClassKeyDataObjectFactory = $taxClassKeyDataObjectFactory;
    }

    /**
     * After plugin for \Magento\Tax\Model\Sales\Total\Quote\CommonTaxCollector::getShippingDataObject
     *
     * @param CommonTaxCollector                                      $subject
     * @param                                                         $result
     * @param ShippingAssignmentInterface                             $shippingAssignment
     * @param AddressTotal                                            $total
     * @param bool                                                    $useBaseCurrency
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
        AddressTotal $total,
        $useBaseCurrency
    ) {
        if ($result === null) {
            return $result; // no shipping tax calculation
        }

        if (!$total->hasData('highest_product_tax_rate_tax_class_id')) {
            return $result; // product tax class was not set, can't proceed, fallback to default
        }

        $shippingTaxClassId = (int)$total->getData('highest_product_tax_rate_tax_class_id');

        return $result->setTaxClassKey(
            $this->taxClassKeyDataObjectFactory->create()
                ->setType(TaxClassKeyInterface::TYPE_ID)
                ->setValue($shippingTaxClassId)
        );
    }
}
