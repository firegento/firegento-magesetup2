<?php declare(strict_types=1);

/**
 * Copyright © 2016 FireGento e.V.
 * See LICENSE.md bundled with this module for license details.
 */

namespace FireGento\MageSetup\Unit\Test\Block\Imprint;

use FireGento\MageSetup\Block\Imprint\Content;
use Magento\Directory\Api\CountryInformationAcquirerInterface;
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

        $this->countryInformationAcquirerMock = $this
            ->getMockBuilder(CountryInformationAcquirerInterface::class)
            ->disableOriginalConstructor()
            ->setMethods([])
            ->getMockForAbstractClass();

        $this->contextMock = $this->createMock(Context::class);

        $this->scopeConfigMock = $this
            ->getMockBuilder(ScopeConfigInterface::class)
            ->disableOriginalConstructor()
            ->setMethods(['getValue'])
            ->getMockForAbstractClass();

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
            ->expects($this->at(0))
            ->method('getValue')
            ->willReturn('DE');

        /** @var CountryInformationAcquirerInterface|MockObject $countryInformationAcquirerInterfaceMock */
        $countryInformationAcquirerInterfaceMock = $this
            ->getMockBuilder(CountryInformationAcquirerInterface::class)
            ->disableOriginalConstructor()
            ->setMethods(['getFullNameLocale'])
            ->getMockForAbstractClass();

        $countryInformationAcquirerInterfaceMock
            ->expects($this->at(0))
            ->method('getFullNameLocale')
            ->willReturn($countryName);

        $this->countryInformationAcquirerMock
            ->expects($this->at(0))
            ->method('getCountryInfo')
            ->willReturn($countryInformationAcquirerInterfaceMock);

        self::assertSame($countryName, $this->sut->getCountry());
    }

    public function testGetWeb(): void
    {
        $baseUrl = "http://www.test.com";
        $this->scopeConfigMock
            ->expects($this->at(0))
            ->method('getValue')
            ->willReturn($baseUrl);

        self::assertSame($baseUrl, $this->sut->getWeb());
    }

    public function testGetEmail(): void
    {
        $email = "max@muster.de";
        # empty email case 1
        self::assertSame('', $this->sut->getEmail());
        # antispam true
        $this->scopeConfigMock
            ->expects(self::at(0))
            ->method('getValue')
            ->willReturn($email);
        self::assertSame($email, $this->sut->getEmail(false));
        $this->scopeConfigMock
            ->expects($this->at(0))
            ->method('getValue')
            ->willReturn($email);

        $expected = '<a href="#" onclick="toRecipient();">max<span class="no-display">nospamplease'
            . '</span>@<span class="no-display">nospamplease</span>muster.de</a><script>function '
            . 'toRecipient(){var m = \'max\';m += \'@\';m += \'muster.de\';location.href= "mailto:"+m;}</script>';
        self::assertSame($expected, $this->sut->getEmail(true));
    }
}
