<?php
/**
 * Copyright © 2020 FireGento e.V.
 * See LICENSE.md bundled with this module for license details.
 */

declare(strict_types=1);

namespace FireGento\MageSetup\Model\Tax\Calculation;

class TotalBaseCalculator extends \Magento\Tax\Model\Calculation\TotalBaseCalculator
{
    use AdvancedCrossBorderCalculator;
}
