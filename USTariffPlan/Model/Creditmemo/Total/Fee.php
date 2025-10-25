<?php
namespace EWebCartPro\USTariffPlan\Model\Creditmemo\Total;

use Magento\Sales\Model\Order\Creditmemo\Total\AbstractTotal;

class Fee extends AbstractTotal
{
    /**
     * @param \Magento\Sales\Model\Order\Creditmemo $creditmemo
     * @return $this
     */
    public function collect(\Magento\Sales\Model\Order\Creditmemo $creditmemo)
    {
        $creditmemo->setUsTariff(0);
        $creditmemo->setBaseUsTariff(0);

        $amount = $creditmemo->getOrder()->getUsTariff();
        $creditmemo->setUsTariff($amount);

        $amount = $creditmemo->getOrder()->getBaseUsTariff();
        $creditmemo->setBaseUsTariff($amount);

        $creditmemo->setGrandTotal($creditmemo->getGrandTotal() + $creditmemo->getUsTariff());
        $creditmemo->setBaseGrandTotal($creditmemo->getBaseGrandTotal() + $creditmemo->getBaseUsTariff());

        return $this;
    }
}
