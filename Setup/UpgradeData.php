<?php
/**
 * Copyright © 2016 FireGento e.V. - All rights reserved.
 * See LICENSE.md bundled with this module for license details.
 */

namespace FireGento\MageSetup\Setup;

use Magento\Catalog\Model\Product;

use Magento\Catalog\Model\Product\Type;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable;
use Magento\Downloadable\Model\Product\Type as Downloadable;
use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;

use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\GroupedProduct\Model\Product\Type\Grouped;

/**
 * Class UpgradeData
 *
 * @package FireGento\MageSetup\Setup
 */
class UpgradeData implements UpgradeDataInterface
{
    /**
     * @var EavSetupFactory
     */
    private $eavSetupFactory;

    /**
     * UpgradeData constructor.
     *
     * @param EavSetupFactory $eavSetupFactory
     */
    public function __construct(EavSetupFactory $eavSetupFactory)
    {
        $this->eavSetupFactory = $eavSetupFactory;
    }

    /**
     * Upgrade method
     *
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface $context
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        /** @var EavSetup $eavSetup */
        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);

        $productTypes = [
            Type::TYPE_SIMPLE,
            Type::TYPE_VIRTUAL,
            Configurable::TYPE_CODE,
            Downloadable::TYPE_DOWNLOADABLE,
            Grouped::TYPE_CODE
        ];
        $productTypes = join(',', $productTypes);

        $eavSetup->addAttribute(
            Product::ENTITY,
            'delivery_time',
            [
                'type'                    => 'text',
                'label'                   => 'Lieferzeit',
                'input'                   => 'text',
                'sort_order'              => 100,
                'global'                  => ScopedAttributeInterface::SCOPE_STORE,
                'user_defined'            => true,
                'required'                => false,
                'used_in_product_listing' => true,
                'apply_to'                => $productTypes,
                'group'                   => 'General',
                'unique'                  => false,
                'is_html_allowed_on_front'=> true,
                'visible_on_front'        => true,
                'is_visible_on_checkout'  => true,
                'visible_in_advanced_search' => true,
                'searchable'              => false,
                'filterable'              => false,
                'comparable'              => true,
                'visible'                 => true,
                'backend'                 => '',
                'frontend'                => '',
                'class'                   => '',
                'source'                  => '',
                'default'                 => '2-3 Tage',
            ]
        );

        $setup->endSetup();
    }
}
