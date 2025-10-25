<?php
namespace EWebCartPro\USTariffPlan\Observer;

    use Magento\Framework\Event\Observer;
    use Magento\Framework\Event\ObserverInterface;

class AddFeeToInvoiceObserver implements ObserverInterface
{
    /**
     * Copy US Tariff from Order to Invoice
     *
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        /** @var \Magento\Sales\Model\Order\Invoice $invoice */
        $invoice = $observer->getEvent()->getInvoice();
        $order   = $invoice->getOrder();

        if ($order->getData('us_tariff') !== null) {
            $invoice->setData('us_tariff', $order->getData('us_tariff'));
        }
        if ($order->getData('base_us_tariff') !== null) {
            $invoice->setData('base_us_tariff', $order->getData('base_us_tariff'));
        }
    }
}
