define([
    'ko',
    'uiComponent',
    'Magento_Checkout/js/model/quote',
    'Magento_Catalog/js/price-utils',
    'Magento_Checkout/js/model/totals'
], function (ko, Component, quote, priceUtils, totals) {
    'use strict';

    var show_hide_customfee_blockConfig = window.checkoutConfig.show_hide_customfee_block;
    var fee_label = window.checkoutConfig.fee_label;
    var custom_fee_amount = window.checkoutConfig.custom_fee_amount;
    var fee_breakdown = window.checkoutConfig.fee_breakdown || {};

    return Component.extend({

        totals: quote.getTotals(),
        canVisibleCustomFeeBlock: show_hide_customfee_blockConfig,
        getFormattedPrice: ko.observable(priceUtils.formatPrice(custom_fee_amount, quote.getPriceFormat())),
        getFeeLabel: ko.observable(fee_label),

        feeBreakdown: fee_breakdown,

        isDisplayed: function () {
            return this.getPureValue() > 0;
        },

        getPureValue: function () {
            var price = 0;
            if (this.totals() && totals.getSegment('us_tariff')) {
                price = totals.getSegment('us_tariff').value;
            }
            return price;
        },

        getValue: function () {
            return this.getPureValue();
        },

        // Check if shipping country is US
        isUSShipping: function () {
            var shippingAddress = quote.shippingAddress();
            // In cart page, if no shipping address set, show tariff
            if (!shippingAddress || !shippingAddress.countryId) {
                return true;
            }
            return shippingAddress.countryId === 'US';
        },

        getTariffFee: function () {
            var tariffFee = 0;

            if (this.isUSShipping()) {
                if (this.totals() && totals.getSegment('us_tariff') && totals.getSegment('us_tariff').value !== null) {
                    tariffFee = totals.getSegment('us_tariff').value;
                }
            }

            return priceUtils.formatPrice(tariffFee, quote.getPriceFormat());
        },

        // Get formatted value for display
        getFormattedValue: function () {
            return priceUtils.formatPrice(this.getPureValue(), quote.getPriceFormat());
        },

        // Utility function to format prices
        formatPrice: function (amount) {
            return priceUtils.formatPrice(amount, quote.getPriceFormat());
        }
    });
});