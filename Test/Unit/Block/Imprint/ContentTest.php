<?php declare(strict_types=1);

/**
 * Copyright © 2016 FireGento e.V.
 * See LICENSE.md bundled with this module for license details.
 */

namespace FireGento\MageSetup\Test\Unit\Block\Imprint;

use FireGento\MageSetup\Block\Imprint\Content;
use Magento\Directory\Api\CountryInformationAcquirerInterface;
use Magento\Directory\Api\Data\CountryInformationInterface;
use Magento\Framework\Escaper;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Framework\View\Element\Template\Context;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class ContentTest extends TestCase
{
    /** @var \FireGento\MageSetup\Block\Imprint\Content|MockObject */
    protected $sut;

    /** @var \Magento\Directory\Api\CountryInformationAcquirerInterface|MockObject */
    protected $countryInformationAcquirerMock;

    /** @var \Magento\Framework\View\Element\Template\Context|MockObject */
    protected $contextMock;

    /** @var  ScopeConfigInterface|MockObject */
    protected $scopeConfigMock;

    public function setUp(): void
    {
        parent::setUp();

        $this->countryInformationAcquirerMock = $this->createMock(CountryInformationAcquirerInterface::class);

        $this->contextMock = $this->createMock(Context::class);
        $this->contextMock->method('getEscaper')->willReturn(new Escaper());

        $this->scopeConfigMock = $this->createMock(ScopeConfigInterface::class);

        $this->contextMock
            ->expects($this->atLeastOnce())
            ->method('getScopeConfig')
            ->willReturn($this->scopeConfigMock);

        $objectManager = new ObjectManager($this);

        $this->sut = $objectManager->getObject(Content::class, [
            'countryInformationAcquirer' => $this->countryInformationAcquirerMock,
            'context' => $this->contextMock
        ]);
    }

    public function testGetCountry(): void
    {
        $countryName = "Germany";

        $this->scopeConfigMock
            ->expects($this->once())
            ->method('getValue')
            ->willReturn('DE');

        /** @var CountryInformationInterface|MockObject $countryInformationAcquirerInterfaceMock */
        $countryInformationAcquirerInterfaceMock = $this->createMock(CountryInformationInterface::class);

        $countryInformationAcquirerInterfaceMock
            ->expects($this->once())
            ->method('getFullNameLocale')
            ->willReturn($countryName);

        $this->countryInformationAcquirerMock
            ->expects($this->once())
            ->method('getCountryInfo')
            ->willReturn($countryInformationAcquirerInterfaceMock);

        self::assertSame($countryName, $this->sut->getCountry());
    }

    public function testGetWeb(): void
    {
        $baseUrl = "http://www.test.com";
        $this->scopeConfigMock
            ->expects($this->once())
            ->method('getValue')
            ->willReturn($baseUrl);

        self::assertSame($baseUrl, $this->sut->getWeb());
    }

    public function testGetEmail(): void
    {
        $email = "max@muster.de";
        $this->scopeConfigMock->expects($this->exactly(3))
            ->method('getValue')
            ->with('general/imprint/email', \Magento\Store\Model\ScopeInterface::SCOPE_STORE)
            ->willReturnOnConsecutiveCalls('', $email, $email);

        self::assertSame('', $this->sut->getEmail());
        self::assertSame($email, $this->sut->getEmail(false));

        $expected = '<a href="#" onclick="toRecipient();">max<span class="no-display">nospamplease'
            . '</span>@<span class="no-display">nospamplease</span>muster.de</a><script>function '
            . 'toRecipient(){var m = \'max\';m += \'@\';m += \'muster.de\';location.href= "mailto:"+m;}</script>';
        self::assertSame($expected, $this->sut->getEmail(true));
    }
}
