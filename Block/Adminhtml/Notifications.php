<?php
/**
 * Copyright © 2015 FireGento e.V. - All rights reserved.
 * See LICENSE.md bundled with this module for license details.
 */
namespace FireGento\MageSetup\Adminhtml;

use Magento\Framework\View\Element\Template;

/**
 * Class Notifications
 * @package FireGento\MageSetup\Adminhtml
 */
class Notifications extends \Magento\Framework\View\Element\Template
{
    /**
     * Disable the block caching for this block
     */
    public function __construct(Template\Context $context, array $data = [])
    {
        parent::__construct($context, $data);
        $this->addData(array('cache_lifetime' => null));
    }

    /**
     * Returns a value that indicates if some of the magesetup settings have already been initialized.
     *
     * @return bool Flag if MageSetup is already initialized
     */
    public function isInitialized()
    {
        return Mage::getStoreConfigFlag('magesetup/is_initialized');
    }

    /**
     * Get magesetup management url
     *
     * @return string URL for MageSetup form
     */
    public function getManageUrl()
    {
        return $this->getUrl('adminhtml/magesetup');
    }

    /**
     * Get magesetup installation skip action
     *
     * @return string URL for skip action
     */
    public function getSkipUrl()
    {
        return $this->getUrl('adminhtml/magesetup/skip');
    }

    /**
     * ACL validation before html generation
     *
     * @return string Notification content
     */
    protected function _toHtml()
    {
        if (Mage::getSingleton('admin/session')->isAllowed('system/magesetup')) {
            return parent::_toHtml();
        }

        return '';
    }
}
