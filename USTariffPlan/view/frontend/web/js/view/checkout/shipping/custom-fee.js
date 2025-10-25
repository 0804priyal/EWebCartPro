define([
    'ko',
    'uiComponent',
    'Magento_Checkout/js/model/quote',
    'Magento_Catalog/js/price-utils',
    'Magento_Checkout/js/model/totals'
], function (ko, Component, quote, priceUtils, totals) {
    'use strict';

    // Get configuration from window.checkoutConfig
    var config = window.checkoutConfig || {};
    var show_hide_customfee_blockConfig = config.show_hide_customfee_shipblock;
    var fee_label = config.fee_label;
    var custom_fee_amount = config.custom_fee_amount;

    return Component.extend({
        defaults: {
            template: 'EWebCartPro_USTariffPlan/checkout/shipping/custom-fee'
        },

        initialize: function () {
            this._super();
            return this;
        },

        //canVisibleCustomFeeBlock: ko.observable(show_hide_customfee_blockConfig),
        canVisibleCustomFeeBlock: ko.observable(false),

        getFormattedPrice: function () {
            var feeAmount = custom_fee_amount ?? 0;
            return priceUtils.formatPrice(feeAmount, quote.getPriceFormat());
        },

        getFeeLabel: function () {
            return fee_label || 'US Govt. Tariff Collection charge';
        },

        // Optional: Add observable properties for better reactivity
        feeLabel: ko.observable(fee_label),
        feeAmount: ko.observable(custom_fee_amount)
    });
});