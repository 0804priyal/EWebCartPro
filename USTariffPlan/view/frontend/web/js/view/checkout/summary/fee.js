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

        // Individual fee amounts from PHP
        feeBreakdown: fee_breakdown,

        isDisplayed: function () {
            return this.getValue() != 0;
        },

        getValue: function () {
            var price = 0;
            if (this.totals() && totals.getSegment('us_tariff')) {
                price = totals.getSegment('us_tariff').value;
            }
            return price;
        },

        // Get cart subtotal (product cost) - using PHP calculated value
        getProductCost: function () {
            var productCost = this.feeBreakdown.product_cost || 0;
            return priceUtils.formatPrice(productCost, quote.getPriceFormat());
        },

        getHandlingFee: function () {
            var handlingFee = this.feeBreakdown.handling_fee || 0;
            return priceUtils.formatPrice(handlingFee, quote.getPriceFormat());
        },

        getReviewFee: function () {
            var reviewFee = this.feeBreakdown.review_fee || 0;
            return priceUtils.formatPrice(reviewFee, quote.getPriceFormat());
        },

        getTariffFee: function () {
            // This now uses the actual getCustomFee() value from PHP
            var tariffFee = this.feeBreakdown.tariff_fee || 0;
            return priceUtils.formatPrice(tariffFee, quote.getPriceFormat());
        },

        // Get formatted value for summary - uses the actual custom fee
        getFormattedValue: function () {
            var totalFees = this.feeBreakdown.total_fees || this.getValue();
            return priceUtils.formatPrice(totalFees, quote.getPriceFormat());
        },

        // Utility function to format prices
        formatPrice: function (amount) {
            return priceUtils.formatPrice(amount, quote.getPriceFormat());
        }
    });
});