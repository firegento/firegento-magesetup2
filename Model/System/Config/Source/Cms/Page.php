<?php
/**
 * Copyright © 2016 FireGento e.V.
 * See LICENSE.md bundled with this module for license details.
 */
namespace FireGento\MageSetup\Model\System\Config\Source\Cms;

use Magento\Cms\Model\ResourceModel\Page\CollectionFactory;

/**
 * Class Page
 *
 * @package FireGento\MageSetup\Model\System\Config\Source\Cms
 */
class Page implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @var array
     */
    protected $options = null;

    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(CollectionFactory $collectionFactory)
    {
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * To option array
     *
     * @return array
     */
    public function toOptionArray()
    {
        if (null === $this->options) {
            $collection = $this->collectionFactory->create();
            $collection->addFieldToFilter('is_active', 1);
            $collection->setOrder('identifier', \Magento\Framework\Data\Collection::SORT_ORDER_ASC);

            $options = [
                [
                    'value' => '',
                    'label' => __('-- No Page --')
                ]
            ];

            foreach ($collection as $item) {
                /** @var \Magento\Cms\Model\Page $item */
                $options[] = [
                    'value' => $item->getIdentifier(),
                    'label' => $item->getTitle()
                ];
            }

            $this->options = $options;
        }

        return $this->options;
    }
}
