<?php
namespace EWebCartPro\USTariffPlan\Model\Quote;

class QuoteToOrder
{
    public function afterConvert(
        \Magento\Quote\Model\Quote\Address\ToOrder $subject,
        $order,
        $quoteAddress
    ) {
        $quote = $quoteAddress->getQuote();
        if ($quote->getUsTariff() > 0) {
            $order->setUsTariff($quote->getUsTariff());
            $order->setBaseUsTariff($quote->getBaseUsTariff());
        }        
        return $order;
    }
}