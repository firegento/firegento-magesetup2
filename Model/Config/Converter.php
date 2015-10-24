<?php
/**
 * Copyright © 2015 FireGento e.V. - All rights reserved.
 * See LICENSE.md bundled with this module for license details.
 */
namespace FireGento\MageSetup\Model\Config;

/**
 * Class Converter
 *
 * @package FireGento\MageSetup\Model\Config
 */
class Converter implements \Magento\Framework\Config\ConverterInterface
{
    /**
     * @param \DOMDocument $source
     * @return array
     */
    public function convert($source)
    {
        $xpath = new \DOMXPath($source);

        $result = [];
        foreach ($xpath->query('/magesetup/setup') as $setup) {
            /** @var \DOMElement $setup */

            $scope = $setup->attributes->getNamedItem('name')->nodeValue;
            $result[$scope] = [];

            foreach ($setup->childNodes as $childNode) {
                if ($childNode->nodeType != XML_ELEMENT_NODE) {
                    continue;
                }

                $node = $childNode->nodeName;
                switch ($node) {
                    case 'system_config':
                        $result[$scope]['system_config'] = $this->getSystemConfig($childNode);
                        break;
                    case 'tax':
                        $result[$scope]['tax'] = $this->getTaxConfig($childNode);
                        break;
                    case 'agreements':
                        $result[$scope]['agreements'] = $this->getAgreementsConfig($childNode);
                        break;
                }
            }
        }

        return $result;
    }

    /**
     * @param \DOMElement $node
     * @return array
     */
    public function getSystemConfig(\DOMElement $node)
    {
        $data = [];

        foreach ($node->childNodes as $childNode) {
            if ($childNode->nodeType != XML_ELEMENT_NODE) {
                continue;
            }

            $data[$childNode->nodeName] = $childNode->nodeValue;
        }

        return $data;
    }

    /**
     * @param \DOMElement $node
     * @return array
     */
    public function getTaxConfig(\DOMElement $node)
    {
        $data = [];
        foreach ($node->childNodes as $childNode) {
            /** @var \DOMElement $childNode */
            /** @var \DOMElement $subChildNode */
            /** @var \DOMElement $subSubChildNode */
            if ($childNode->nodeType != XML_ELEMENT_NODE) {
                continue;
            }

            $data[$childNode->nodeName] = [];
            foreach ($childNode->childNodes as $subChildNode) {
                if ($subChildNode->nodeType != XML_ELEMENT_NODE) {
                    continue;
                }

                $data[$childNode->nodeName][$subChildNode->nodeName] = [];

                if ($childNode->nodeName == 'tax_calculation_rules') {
                    $attrTaxRate = $subChildNode->attributes->getNamedItem('tax_rate')->nodeValue;
                    $attrCustomerTaxClass = $subChildNode->attributes->getNamedItem('tax_customer_class')->nodeValue;
                    $attrProductTaxClass = $subChildNode->attributes->getNamedItem('tax_product_class')->nodeValue;

                    $data[$childNode->nodeName][$subChildNode->nodeName]['mapping'] = [
                        'tax_rate_ids'           => $attrTaxRate,
                        'customer_tax_class_ids' => explode(',', $attrCustomerTaxClass),
                        'product_tax_class_ids'  => explode(',', $attrProductTaxClass),
                    ];
                }

                foreach ($subChildNode->childNodes as $subSubChildNode) {
                    if ($subSubChildNode->nodeType != XML_ELEMENT_NODE) {
                        continue;
                    }
                    $data[$childNode->nodeName][$subChildNode->nodeName][$subSubChildNode->nodeName] = $subSubChildNode->nodeValue;
                }
            }
        }

        return $data;
    }

    /**
     * @param \DOMElement $node
     * @return array
     */
    public function getAgreementsConfig(\DOMElement $node)
    {
        $data = [];

        foreach ($node->childNodes as $childNode) {
            /** @var \DOMElement $childNode */
            /** @var \DOMElement $subChildNode */

            if ($childNode->nodeType != XML_ELEMENT_NODE) {
                continue;
            }

            $data[$childNode->nodeName] = [];
            foreach ($childNode->childNodes as $subChildNode) {
                if ($subChildNode->nodeType != XML_ELEMENT_NODE) {
                    continue;
                }

                $data[$childNode->nodeName][$subChildNode->nodeName] = $subChildNode->nodeValue;
            }
        }


        return $data;
    }
}
