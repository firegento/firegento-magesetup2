<?php

declare(strict_types=1);

namespace FireGento\MageSetup\Test\Integration\Observer;

use Magento\Catalog\Api\ProductAttributeRepositoryInterface;
use Magento\TestFramework\TestCase\AbstractBackendController;

class AddProductAttributeVisibleCheckoutObserverTest extends AbstractBackendController
{
    protected $resource = 'Magento_Catalog::attributes_attributes';

    protected $uri = 'backend/catalog/product_attribute/edit';

    public function testExecute()
    {
        /** @var ProductAttributeRepositoryInterface $productAttributeRepository */
        $productAttributeRepository = $this->_objectManager->create(ProductAttributeRepositoryInterface::class);
        $attribute                  = $productAttributeRepository->get('name');
        $this->getRequest()->setParam('attribute_id', $attribute->getAttributeId());
        $this->dispatch($this->uri);
        // TODO switch to assertStringContainsString completely when we drop 2.3-support
        if (method_exists(__CLASS__, 'assertStringContainsString')) {
            self::assertStringContainsString('Visible in Checkout', $this->getResponse()->getBody());
        } else {
            self::assertContains('Visible in Checkout', $this->getResponse()->getBody());
        }
    }
}
