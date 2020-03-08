<?php
/**
 * Copyright © 2016 FireGento e.V.
 * See LICENSE.md bundled with this module for license details.
 */
namespace FireGento\MageSetup\Observer;

use Magento\Config\Model\Config\Source\Yesno;
use Magento\Framework\Event\ObserverInterface;

/**
 * Class AddProductAttributeVisibleCheckoutObserver
 *
 * @package FireGento\MageSetup\Observer
 */
class AddProductAttributeVisibleCheckoutObserver implements ObserverInterface
{
    /**
     * @var Yesno
     */
    private $yesNo;

    /**
     * AddProductAttributeVisibleCheckoutObserver constructor.
     *
     * @param Yesno $yesNo
     */
    public function __construct(Yesno $yesNo)
    {
        $this->yesNo = $yesNo;
    }

    /**
     * Add product attribute visible checkout observer
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /** @var \Magento\Framework\Data\Form $form */
        $form = $observer->getEvent()->getData('form');

        /** @var \Magento\Framework\Data\Form\Element\Fieldset $fieldset */
        $fieldset = $form->getElement('front_fieldset');

        $fieldset->addField(
            'is_visible_on_checkout',
            'select',
            [
                'name'   => 'is_visible_on_checkout',
                'label'  => __('Visible in Checkout'),
                'title'  => __('Visible in Checkout'),
                'values' => $this->yesNo->toOptionArray(),
            ]
        );
    }
}
