<?php
/**
 * Copyright © 2016 FireGento e.V.
 * See LICENSE.md bundled with this module for license details.
 */

namespace FireGento\MageSetup\Test\Unit\Plugin\Catalog\Block\Product\ListProduct;

use FireGento\MageSetup\Model\System\Config;
use FireGento\MageSetup\Plugin\Catalog\Block\Product\ListProduct\AddDeliveryTimePlugin;
use Magento\Catalog\Block\Product\ListProduct;
use Magento\Catalog\Model\Product;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\View\LayoutInterface;
use PHPUnit\Framework\TestCase;

class AddDeliveryTimePluginTest extends TestCase
{
    /** @var ScopeConfigInterface | \PHPUnit\Framework\MockObject\MockObject */
    protected $scopeConfigMock;

    /** @var LayoutInterface | \PHPUnit\Framework\MockObject\MockObject */
    protected $layoutMock;

    /** @var ListProduct | \PHPUnit\Framework\MockObject\MockObject */
    protected $listProductMock;

    /** @var \Closure */
    protected $proceedMock;

    /** @var  AddDeliveryTimePlugin */
    protected $plugin;

    /** @var Product | \PHPUnit\Framework\MockObject\MockObject */
    protected $productMock;

    public function setUp(): void
    {
        $this->scopeConfigMock = $this->getMockBuilder(Config::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->plugin = new AddDeliveryTimePlugin($this->scopeConfigMock);

        $this->layoutMock = $this->createMock(LayoutInterface::class);

        $this->listProductMock = $this->getMockBuilder(ListProduct::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getLayout'])
            ->getMock();

        $this->listProductMock->method('getLayout')
            ->willReturn($this->layoutMock);

        $this->proceedMock = function () {
            return "HTML";
        };

        $this->productMock = $this->getMockBuilder(Product::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * Ensures that the plugin does not do anything stupid if the delivery
     * info block is not present in the layout.
     *
     * @return void
     */
    public function testAfterGetProductDetailsHtmlDoesNotDoAnythingIfBlockDoesNotExist()
    {
        // Simulate the situation when the block does not exist.
        $this->layoutMock->expects($this->atLeastOnce())
            ->method('getBlock')
            ->with('product.info.delivery')
            ->willReturn(false);

        $result = $this->plugin->afterGetProductDetailsHtml($this->listProductMock, '', $this->productMock);

        // Make sure that the plugin does not modify the result.
        self::assertSame('', $result);
    }
}
