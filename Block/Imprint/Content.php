<?php
/**
 * Copyright © 2016 FireGento e.V.
 * See LICENSE.md bundled with this module for license details.
 */
namespace FireGento\MageSetup\Block\Imprint;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Directory\Api\CountryInformationAcquirerInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\ScopeInterface;

/**
 * Class Content
 *
 * @package FireGento\MageSetup\Block\Imprint
 */
class Content extends \Magento\Framework\View\Element\Template
{
    const XML_PATH_IMPRINT = 'general/imprint/';

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var CountryInformationAcquirerInterface
     */
    private $countryInformationAcquirer;

    /**
     * @param CountryInformationAcquirerInterface $countryInformationAcquirer
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\Directory\Api\CountryInformationAcquirerInterface $countryInformationAcquirer,
        \Magento\Framework\View\Element\Template\Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->scopeConfig = $context->getScopeConfig();
        $this->countryInformationAcquirer = $countryInformationAcquirer;
    }

    /**
     * Retrieve the specific country name by the selected country code
     *
     * @return string Country
     */
    public function getCountry()
    {
        $countryCode = $this->getImprintValue('country');
        if (!$countryCode) {
            return '';
        }

        try {
            $countryInfo = $this->countryInformationAcquirer->getCountryInfo($countryCode);
            $countryName = $countryInfo->getFullNameLocale();
        } catch (NoSuchEntityException $e) {
            $countryName = '';
        }

        return $countryName;
    }

    /**
     * Retrieve the setting "website". If parameter checkForProtocol is true,
     * check if there is a valid protocol given, otherwise add http:// manually.
     *
     * @param  bool $checkForProtocol Flag if website url should be checked for http(s) protocol
     * @return string Website URL
     */
    public function getWeb($checkForProtocol = false)
    {
        $web = $this->getImprintValue('web');
        if ($checkForProtocol && strlen(trim($web))) {
            if (strpos($web, 'http://') === false
                && strpos($web, 'https://') === false
            ) {
                $web = 'http://' . $web;
            }
        }

        return $web;
    }

    /**
     * Try to limit spam by generating a javascript email link
     *
     * @param boolean true
     * @return string
     */
    public function getEmail($antispam = false)
    {
        $email = $this->getImprintValue('email');
        if (!$email) {
            return '';
        }

        if (!$antispam) {
            return $email;
        }

        $parts = explode('@', $email);
        if (count($parts) != 2) {
            return $email;
        }

        $html = '<a href="#" onclick="toRecipient();">';
        $html .= $parts[0] . '<span class="no-display">nospamplease</span>@<span class="no-display">nospamplease</span>' . $parts[1];
        $html .= '</a>';
        $html .= $this->getEmailJs($parts);

        return $html;
    }

    /**
     * Generate JS code
     *
     * @param $parts
     * @return string
     */
    public function getEmailJs($parts)
    {
        $js = <<<JS
<script>function toRecipient(){var m = '$parts[0]';m += '@';m += '$parts[1]';location.href= "mailto:"+m;}</script>
JS;

        return $js;
    }

    /**
     * @param string $field
     * @return mixed
     */
    public function getImprintValue($field)
    {
        return $this->scopeConfig->getValue(self::XML_PATH_IMPRINT . $field, ScopeInterface::SCOPE_STORES);
    }
}
