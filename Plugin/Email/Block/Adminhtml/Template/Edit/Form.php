<?php
/**
 * Copyright © 2016 FireGento e.V.
 * See LICENSE.md bundled with this module for license details.
 */

namespace FireGento\MageSetup\Plugin\Email\Block\Adminhtml\Template\Edit;

/**
 * Plugin to add additional config variables to be inserted into emails.
 */
class Form
{
    /**
     * @var array[]
     */
    private $additionalConfigVariables;

    public function __construct()
    {
        $this->additionalConfigVariables = [
            ['value' => 'general/imprint/shop_name', 'label' => __('Shop Name')],
            ['value' => 'general/imprint/company_first', 'label' => __('Company First')],
            ['value' => 'general/imprint/company_second', 'label' => __('Company Second')],
            ['value' => 'general/imprint/street', 'label' => __('Street')],
            ['value' => 'general/imprint/zip', 'label' => __('Zip')],
            ['value' => 'general/imprint/city', 'label' => __('City')],
            ['value' => 'general/imprint/country', 'label' => __('Default Country')],
            ['value' => 'general/imprint/telephone', 'label' => __('Telephone')],
            [
                'value' => 'general/imprint/telephone_additional',
                'label' => __('Supplementary Information for Telephone'),
            ],
            ['value' => 'general/imprint/fax', 'label' => __('Fax')],
            ['value' => 'general/imprint/email', 'label' => __('Email')],
            ['value' => 'general/imprint/web', 'label' => __('Website')],
            ['value' => 'general/imprint/tax_number', 'label' => __('Tax number')],
            ['value' => 'general/imprint/vat_id', 'label' => __('VAT-ID')],
            ['value' => 'general/imprint/court', 'label' => __('Register Court')],
            ['value' => 'general/imprint/financial_office', 'label' => __('Financial Office')],
            ['value' => 'general/imprint/ceo', 'label' => __('CEO')],
            ['value' => 'general/imprint/owner', 'label' => __('Owner')],
            [
                'value' => 'general/imprint/content_responsable_name',
                'label' => __('Responsible for content'),
            ],
            [
                'value' => 'general/imprint/content_responsable_address',
                'label' => __('Responsible for content address'),
            ],
            [
                'value' => 'general/imprint/content_responsable_press_law',
                'label' => __('Responsible in the interests of the press law'),
            ],
            ['value' => 'general/imprint/register_number', 'label' => __('Register Number')],
            [
                'value' => 'general/imprint/business_rules',
                'label' => __('Reference for business rules (physician, ...)'),
            ],
            ['value' => 'general/imprint/authority', 'label' => __('Authority (ECG)')],
            ['value' => 'general/imprint/shareholdings', 'label' => __('Shareholdings')],
            [
                'value' => 'general/imprint/editorial_concept',
                'label' => __('Editorial Concept'),
            ],
            ['value' => 'general/imprint/bank_account_owner', 'label' => __('Account owner')],
            ['value' => 'general/imprint/bank_account', 'label' => __('Account')],
            ['value' => 'general/imprint/bank_code_number', 'label' => __('Bank number')],
            ['value' => 'general/imprint/bank_name', 'label' => __('Bank name')],
            ['value' => 'general/imprint/swift', 'label' => __('BIC/Swift-Code')],
            ['value' => 'general/imprint/iban', 'label' => __('IBAN')],
            ['value' => 'general/imprint/clearing', 'label' => __('Clearing')],
        ];
    }

    public function afterGetVariables(\Magento\Email\Block\Adminhtml\Template\Edit\Form $subject, array $result): array
    {
        $optionArray = [];
        foreach ($this->additionalConfigVariables as $variable) {
            $optionArray[] = [
                'value' => '{{config path="' . $variable['value'] . '"}}',
                'label' => $variable['label'],
            ];
        }
        if ($optionArray) {
            $result[] = [
                'label' => __('Imprint'),
                'value' => $optionArray,
            ];
        }

        return $result;
    }
}
