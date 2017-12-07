/**
 * Copyright © 2017 FireGento e.V.
 * See LICENSE.md bundled with this module for license details.
 */
define(['jquery'], function ($) {
    'use strict';

    return function (target) {

        target.prototype._displayTierPriceBlock = function (optionId) {
            var options, tierPriceHtml;

            if (typeof optionId != 'undefined' &&
                this.options.spConfig.optionPrices[optionId].tierPrices != []
            ) {
                options = this.options.spConfig.optionPrices[optionId];

                if (this.options.tierPriceTemplate) {
                    tierPriceHtml = mageTemplate(this.options.tierPriceTemplate, {
                        'tierPrices': options.tierPrices,
                        '$t': $t,
                        'currencyFormat': this.options.spConfig.currencyFormat,
                        'priceUtils': priceUtils
                    });
                    $(this.options.tierPriceBlockSelector).html(tierPriceHtml).show();
                    //show the tax
                    $(this.options.tierPriceBlockSelector).parent().next('.price-details').show();
                }
            } else {
                $(this.options.tierPriceBlockSelector).hide();
                // hide the tax also
                $(this.options.tierPriceBlockSelector).parent().next('.price-details').hide();
            }
        }
        return target;
    };
});


