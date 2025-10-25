<?php
namespace EWebCartPro\USTariffPlan\Observer\Sales;

    use Magento\Framework\Event\Observer;
    use Magento\Framework\Event\ObserverInterface;
    use EWebCartPro\USTariffPlan\Helper\Data as FeeHelper;

class ReorderQuote implements ObserverInterface
{
    protected $feeHelper;

    public function __construct(
        FeeHelper $feeHelper
    ) {
        $this->feeHelper = $feeHelper;
    }

    public function execute(Observer $observer)
    {
        /** @var \Magento\Quote\Model\Quote $quote */
        $quote = $observer->getEvent()->getQuote();
        /** @var \Magento\Sales\Model\Order $order */
        $order = $observer->getEvent()->getOrder();
        
        // Check if original order had tariff fee
        $originalTariffAmount = $order->getUsTariff();
        
        if ($originalTariffAmount > 0 && $this->feeHelper->isModuleEnabled()) {
            $newTariffAmount = $this->feeHelper->getCustomFee();            
            if ($newTariffAmount > 0) {
                $quote->setUsTariff($newTariffAmount);
                $quote->setBaseUsTariff($newTariffAmount);
                $extensionAttributes = $quote->getExtensionAttributes();
                if ($extensionAttributes) {
                    $extensionAttributes->setUsTariff($newTariffAmount);
                    $extensionAttributes->setBaseUsTariff($newTariffAmount);
                    $quote->setExtensionAttributes($extensionAttributes);
                }                
                $quote->setTotalsCollectedFlag(false);
            }
        }
    }
}