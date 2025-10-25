define([
    'ko',
    'uiComponent',
    'Magento_Catalog/js/price-utils'
], function (ko, Component, priceUtils) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'EWebCartPro_USTariffPlan/order/create/totals/fee'
        },

        initialize: function () {
            this._super();
            this.notifySubscribers();
            return this;
        },

        getValue: function () {
            var quote = window.order ? window.order.quote : null;
            return quote ? quote.us_tariff || 0 : 0;
        },

        getFormattedValue: function () {
            var value = this.getValue();
            var priceFormat = window.order ? window.order.priceFormat : {};
            return priceUtils.formatPrice(value, priceFormat);
        },

        getTitle: function () {
            return 'US Tariff Charges';
        },

        isDisplayed: function () {
            return this.getValue() > 0;
        }
    });
});