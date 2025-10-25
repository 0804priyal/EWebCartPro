<?php
namespace EWebCartPro\USTariffPlan\Observer;

    use Magento\Framework\Event\Observer as EventObserver;
    use Magento\Framework\Event\ObserverInterface;

class AddFeeToOrderObserver implements ObserverInterface
{
    /**
     * Set payment US Tariff to order
     *
     * @param EventObserver $observer
     * @return $this
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $quote = $observer->getQuote();
        $CustomFeeFee = $quote->getUsTariff();
        $CustomFeeBaseFee = $quote->getBaseUsTariff();
        if (!$CustomFeeFee || !$CustomFeeBaseFee) {
            return $this;
        }
        //Set US Tariff data to order
        $order = $observer->getOrder();
        $order->setData('us_tariff', $CustomFeeFee);
        $order->setData('base_us_tariff', $CustomFeeBaseFee);
        return $this;
    }
}
