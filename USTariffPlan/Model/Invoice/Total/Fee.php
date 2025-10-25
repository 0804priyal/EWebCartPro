<?php
namespace EWebCartPro\USTariffPlan\Model\Invoice\Total;

use Magento\Sales\Model\Order\Invoice\Total\AbstractTotal;

class Fee extends AbstractTotal
{
    /**
     * @param \Magento\Sales\Model\Order\Invoice $invoice
     * @return $this
     */
    public function collect(\Magento\Sales\Model\Order\Invoice $invoice)
    {
        $invoice->setUsTariff(0);
        $invoice->setBaseUsTariff(0);

        $amount = $invoice->getOrder()->getUsTariff();
        $invoice->setUsTariff($amount);
        $amount = $invoice->getOrder()->getBaseUsTariff();
        $invoice->setBaseUsTariff($amount);

        $invoice->setGrandTotal($invoice->getGrandTotal() + $invoice->getUsTariff());
        $invoice->setBaseGrandTotal($invoice->getBaseGrandTotal() + $invoice->getUsTariff());

        return $this;
    }
}
