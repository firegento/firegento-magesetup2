<?php declare(strict_types=1);
/**
 * Copyright © FireGento e.V.
 * See LICENSE.md bundled with this module for license details.
 */

namespace FireGento\MageSetup\Test\Unit\Block\Price;

use FireGento\MageSetup\Block\Price\Details;
use FireGento\MageSetup\Model\System\Config;
use Magento\Customer\Model\ResourceModel\GroupRepository;
use Magento\Customer\Model\Session;
use Magento\Framework\Phrase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Framework\View\Element\Template\Context;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Tax\Model\Calculation;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Class DetailsTest
 *
 * Unit tests for price details block
 */
class DetailsTest extends TestCase
{
    /** @var \FireGento\MageSetup\Block\Price\Details|MockObject */
    protected $sut;

    /** @var \Magento\Framework\View\Element\Template\Context|MockObject */
    protected $contextMock;

    /** @var \FireGento\MageSetup\Model\System\Config|MockObject */
    protected $magesetupConfigMock;

    /** @var \Magento\Customer\Model\Session|MockObject */
    protected $customerSessionMock;

    /** @var \Magento\Customer\Model\ResourceModel\GroupRepository|MockObject */
    protected $groupRepositoryMock;

    /** @var \Magento\Tax\Model\Calculation|MockObject */
    protected $taxCalculationMock;

    /** @var \Magento\Tax\Helper\Data|MockObject */
    protected $taxHelperMock;

    /** @var \Magento\Store\Model\StoreManagerInterface|MockObject */
    protected $storeManagerMock;

    public function setUp(): void
    {
        parent::setUp();

        $this->storeManagerMock = $this->createMock(StoreManagerInterface::class);

        $this->contextMock = $this->createMock(Context::class);

        $this->contextMock
            ->expects(self::atLeastOnce())
            ->method('getStoreManager')
            ->willReturn($this->storeManagerMock);

        $this->magesetupConfigMock = $this->createMock(Config::class);

        $this->customerSessionMock = $this->createMock(Session::class);

        $this->groupRepositoryMock = $this->createMock(GroupRepository::class);

        $this->taxCalculationMock = $this->createMock(Calculation::class);

        $this->taxHelperMock = $this->createMock(\Magento\Tax\Helper\Data::class);

        $objectManager = new ObjectManager($this);

        $this->sut = $objectManager->getObject(Details::class, [
            'context' => $this->contextMock,
            'magesetupConfig' => $this->magesetupConfigMock,
            'customerSession' => $this->customerSessionMock,
            'groupRepository' => $this->groupRepositoryMock,
            'taxCalculation' => $this->taxCalculationMock,
            'taxHelper' => $this->taxHelperMock
        ]);
    }

    public function testSetSaleableItem(): void
    {
        $this->sut->setData('tax_rate', '19');
        $saleableItemMock = $this->createMock(SaleableItem::class);
        $this->sut->setSaleableItem($saleableItemMock);
        self::assertNull($this->sut->getData('tax_rate'));
    }

    public function testGetFormattedTaxRate(): void
    {
        $this->sut->setData('tax_rate', '19');
        $expected = new Phrase('%1%', ['19']);
        self::assertEquals($expected, $this->sut->getFormattedTaxRate());
    }

    public function testGetFormattedTaxRateIsZero(): void
    {
        $saleableItemMock = $this->createMock(SaleableItem::class);
        $saleableItemMock
            ->expects($this->once())
            ->method('getTaxPercent')
            ->willReturn(7);

        $this->sut->setSaleableItem($saleableItemMock);
        $expected = new Phrase('%1%', ['7']);
        self::assertEquals($expected, $this->sut->getFormattedTaxRate());
    }

    public function testGetFormattedTaxRateIsFive(): void
    {
        $saleableItem2Mock = $this->createMock(SaleableItem::class);
        $saleableItem2Mock
            ->expects($this->once())
            ->method('getTaxPercent')
            ->willReturn(null);

        $saleableItem2Mock
            ->expects($this->once())
            ->method('getTaxClassId')
            ->willReturn('simple');

        $storeMock = $this->createMock(\Magento\Store\Api\Data\StoreInterface::class);

        $this->storeManagerMock
            ->expects($this->once())
            ->method('getStore')
            ->willReturn($storeMock);

        $this->customerSessionMock
            ->expects($this->once())
            ->method('getCustomerGroupId')
            ->willReturn(10);
        $groupMock = $this->createMock(\Magento\Customer\Model\Data\Group::class);

        $groupMock
            ->expects($this->once())
            ->method('getTaxClassId')
            ->willReturn(20);

        $this->groupRepositoryMock
            ->expects($this->once())
            ->method('getById')
            ->with(10)
            ->willReturn($groupMock);

        $dataMock = $this->createMock(\Magento\Framework\DataObject::class);

        $dataMock
            ->expects($this->once())
            ->method('setData')
            ->willReturn($dataMock);

        $this->taxCalculationMock
            ->expects($this->once())
            ->method('getRateRequest')
            ->willReturn($dataMock);

        $this->taxCalculationMock
            ->expects($this->once())
            ->method('getRate')
            ->willReturn(5);

        $this->sut->setSaleableItem($saleableItem2Mock);
        $expected = new Phrase('%1%', ['5']);
        self::assertEquals($expected, $this->sut->getFormattedTaxRate());
    }

    public function testGetPriceDisplayType(): void
    {
        $this->taxHelperMock
            ->expects($this->once())
            ->method('getPriceDisplayType')
            ->willReturn(4);
        self::assertSame(4, $this->sut->getPriceDisplayType());
    }

    public function testIsIncludingShippingCosts(): void
    {
        $this->magesetupConfigMock->expects($this->exactly(4))
            ->method('isIncludingShippingCosts')
            ->willReturnOnConsecutiveCalls(false, false, false, true);

        self::assertFalse($this->sut->isIncludingShippingCosts());
        $this->sut->setData('is_including_shipping_costs', null);
        self::assertFalse($this->sut->isIncludingShippingCosts());
        $this->sut->setData('is_including_shipping_costs', 1);
        self::assertTrue($this->sut->isIncludingShippingCosts());
        $this->sut->unsetData('is_including_shipping_costs');
        self::assertFalse($this->sut->isIncludingShippingCosts());
        $this->sut->unsetData('is_including_shipping_costs');
        self::assertTrue($this->sut->isIncludingShippingCosts());
    }

    public function testCanShowShippingLink(): void
    {
        $saleableItemMock = $this->createMock(SaleableItem::class);
        $saleableItemMock->expects($this->exactly(2))
            ->method('getTypeId')
            ->willReturnOnConsecutiveCalls('virtual', 'configurable');

        $this->sut->setSaleableItem($saleableItemMock);
        self::assertFalse($this->sut->canShowShippingLink());
        self::assertTrue($this->sut->canShowShippingLink());
    }

    public function testGetShippingCostUrl()
    {
        $shippingCostUrl = "http://shop.firegento.com/shipping";
        $this->magesetupConfigMock
            ->expects($this->once())
            ->method('getShippingCostUrl')
            ->willReturn($shippingCostUrl);
        self::assertSame($shippingCostUrl, $this->sut->getShippingCostUrl());
    }
}
