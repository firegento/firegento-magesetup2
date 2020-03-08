<?php
/**
 * Copyright © 2016 FireGento e.V.
 * See LICENSE.md bundled with this module for license details.
 */
namespace FireGento\MageSetup\Plugin\Agreements;

use Magento\Cms\Model\Template\Filter as TemplateFilter;

/**
 * Class AfterGetContent
 *
 * @package FireGento\MageSetup\Model\Plugin
 */
class AfterGetContent
{
    /**
     * @var TemplateFilter
     */
    private $templateFilter;

    /**
     * AfterGetContent constructor.
     *
     * @param TemplateFilter $templateFilter
     */
    public function __construct(TemplateFilter $templateFilter)
    {
        $this->templateFilter = $templateFilter;
    }

    /**
     * After get content
     *
     * @param \Magento\CheckoutAgreements\Model\Agreement $agreement
     * @param string $result
     * @return mixed
     */
    public function afterGetContent(\Magento\CheckoutAgreements\Model\Agreement $agreement, $result)
    {
        return $this->templateFilter->filter($result);
    }
}
