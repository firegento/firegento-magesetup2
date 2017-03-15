<?php
/**
 * Copyright © 2016 FireGento e.V.
 * See LICENSE.md bundled with this module for license details.
 */
namespace FireGento\MageSetup\Model\Setup\SubProcessor;

use FireGento\MageSetup\Model\Config;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Tax\Api\TaxRuleRepositoryInterface;
use Magento\Tax\Api\Data\TaxRuleInterfaceFactory;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductCollectionFactory;
use Magento\Customer\Model\ResourceModel\Group\CollectionFactory as CustomerGroupCollectionFactory;
use Magento\Framework\App\Config\Storage\WriterInterface;
use Magento\Catalog\Model\Product;
use Magento\Customer\Api\Data\GroupInterface;

/**
 * Class TaxSubProcessor
 *
 * @package FireGento\MageSetup\Model\Setup\SubProcessor
 */
class TaxSubProcessor extends AbstractSubProcessor
{
    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    private $resource;

    /**
     * @var \Magento\Framework\DB\Adapter\AdapterInterface
     */
    private $connection;

    /**
     * @var \FireGento\MageSetup\Model\Config
     */
    private $config;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var TaxRuleRepositoryInterface
     */
    private $ruleService;

    /**
     * @var TaxRuleInterfaceFactory
     */
    private $taxRuleDataObjectFactory;

    /**
     * @var ProductCollectionFactory
     */
    private $productCollectionFactory;

    /**
     * @var CustomerGroupCollectionFactory
     */
    private $customerGroupCollectionFactory;

    /**
     * @var \FireGento\MageSetup\Model\System\Config
     */
    private $magesetupConfig;

    /**
     * @param WriterInterface                           $configWriter
     * @param \Magento\Framework\App\ResourceConnection $resource
     * @param StoreManagerInterface                     $storeManager
     * @param TaxRuleRepositoryInterface                $ruleService
     * @param TaxRuleInterfaceFactory                   $taxRuleDataObjectFactory
     * @param ProductCollectionFactory                  $productCollectionFactory
     * @param CustomerGroupCollectionFactory            $customerGroupCollectionFactory
     * @param \FireGento\MageSetup\Model\System\Config  $magesetupConfig
     */
    public function __construct(
        WriterInterface $configWriter,
        \Magento\Framework\App\ResourceConnection $resource,
        StoreManagerInterface $storeManager,
        TaxRuleRepositoryInterface $ruleService,
        TaxRuleInterfaceFactory $taxRuleDataObjectFactory,
        ProductCollectionFactory $productCollectionFactory,
        CustomerGroupCollectionFactory $customerGroupCollectionFactory,
        \FireGento\MageSetup\Model\System\Config $magesetupConfig
    ) {
        $this->resource = $resource;
        $this->connection = $resource->getConnection('write');
        $this->storeManager = $storeManager;
        $this->ruleService = $ruleService;
        $this->taxRuleDataObjectFactory = $taxRuleDataObjectFactory;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->customerGroupCollectionFactory = $customerGroupCollectionFactory;
        $this->magesetupConfig = $magesetupConfig;

        parent::__construct($configWriter);
    }

    /**
     * @param Config $config
     * @return void
     */
    public function process(Config $config)
    {
        $this->config = $config;

        $configTaxClasses = $config->getTaxClasses();
        $configTaxCalculationRates = $config->getTaxCalculationRates();
        $configTaxCalculationRules = $config->getTaxCalculationRules();

        if ($configTaxClasses && $configTaxCalculationRates && $configTaxCalculationRules) {
            $this->truncateTable('tax_class');
            $this->truncateTable('tax_calculation_rule');
            $this->truncateTable('tax_calculation_rate');
            $this->truncateTable('tax_calculation_rate_title');
            $this->truncateTable('tax_calculation');

            $taxClasses = [];
            foreach ($configTaxClasses as $identifier => $data) {
                $this->insertIntoTable('tax_class', $data);
                $taxClasses[$identifier] = $this->getLastInsertId('tax_class');
            }

            $taxRates = [];
            foreach ($configTaxCalculationRates as $identifier => $data) {
                $taxRates[$identifier] = [];

                foreach ($this->getCountries() as $country) {
                    $data['tax_country_id'] = $country;
                    $data['code'] = $country . ' - ' . $data['label'];
                    $taxRates[$identifier][] = $this->createTaxCalculationRate($data);
                }
            }

            foreach ($configTaxCalculationRules as $calculationRuleData) {
                $mapping = $calculationRuleData['mapping'];
                unset($calculationRuleData['mapping']);

                $rule = $this->taxRuleDataObjectFactory->create();
                $rule->setCode($calculationRuleData['code']);
                $rule->setPriority($calculationRuleData['priority']);
                $rule->setPosition($calculationRuleData['position']);
                $rule->setCalculateSubtotal($calculationRuleData['calculate_subtotal']);

                foreach ($mapping as $mappingKey => $mappingValues) {
                    if (is_array($mappingValues)) {
                        $classes = array();
                        foreach ($mappingValues as $value) {
                            if (isset($taxClasses[$value])) {
                                $classes[] = $taxClasses[$value];
                            }
                        }
                        $rule->setData($mappingKey, $classes);
                    } else {
                        if (isset($taxRates[$mappingValues])) {
                            $rule->setData($mappingKey, $taxRates[$mappingValues]);
                        }
                    }
                }

                $this->ruleService->save($rule);
            }

            $this->saveTaxClassRelations($taxClasses);
        }
    }

    /**
     * @param $tableAlias
     * @return string
     */
    private function getTable($tableAlias)
    {
        return $this->resource->getTableName($tableAlias);
    }

    /**
     * @param $table
     */
    private function truncateTable($table)
    {
        $tableName = $this->getTable($table);
        $this->connection->delete($tableName);
    }

    /**
     * @param $table
     * @param $data
     */
    private function insertIntoTable($table, $data)
    {
        $tableName = $this->getTable($table);
        $this->connection->insert($tableName, $data);
    }

    /**
     * @param $table
     * @return mixed
     */
    private function getLastInsertId($table)
    {
        $tableName = $this->getTable($table);

        return $this->connection->lastInsertId($tableName);
    }

    /**
     * @return array|string
     */
    private function getCountries()
    {
        if ($this->magesetupConfig->isCountryInEu($this->config->getCountry())) {
            return $this->magesetupConfig->getEuCountries();
        }

        return [$this->config->getCountry()];
    }

    /**
     * @param $rateData
     * @return mixed
     */
    private function createTaxCalculationRate($rateData)
    {
        $label = '';
        if (isset($rateData['label'])) {
            $label = $rateData['label'];
            unset($rateData['label']);
        }

        // base tax rate db entry
        $this->insertIntoTable('tax_calculation_rate', $rateData);
        $rateId = $this->getLastInsertId('tax_calculation_rate');

        // add labels to all store views
        if ($label) {
            foreach ($this->storeManager->getStores() as $storeId => $store) {
                $bind = array(
                    'tax_calculation_rate_id' => $rateId,
                    'store_id'                => $storeId,
                    'value'                   => $label,
                );
                $this->insertIntoTable('tax_calculation_rate_title', $bind);
            }
        }

        return $rateId;
    }

    /**
     * @param $taxClasses
     */
    private function saveTaxClassRelations($taxClasses)
    {
        if (isset($taxClasses['products_rate_1'])) {
            $productTaxClassId = $taxClasses['products_rate_1'];
            $this->saveConfigValue('tax/classes/default_product_tax_class', $productTaxClassId);

            $productCollection = $this->productCollectionFactory->create();
            foreach ($productCollection as $product) {
                /** @var Product $product */

                $product->setData('tax_class_id', $productTaxClassId);
                $product->save();
            }
        }

        if (isset($taxClasses['customers_end_users'])) {
            $customerTaxClassId = $taxClasses['customers_end_users'];
            $this->saveConfigValue('tax/classes/default_customer_tax_class', $customerTaxClassId);

            $customerGroupGollection = $this->customerGroupCollectionFactory->create();
            foreach ($customerGroupGollection as $customerGroup) {
                /** @var GroupInterface $customerGroup */

                $customerGroup->setTaxClassId($customerTaxClassId);
                $customerGroup->save();
            }
        }

        if (isset($taxClasses['shipping_rate_1'])) {
            $this->saveConfigValue('tax/classes/shipping_tax_class', $taxClasses['shipping_rate_1']);
        }
    }
}
