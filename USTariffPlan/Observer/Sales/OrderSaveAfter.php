<?php
namespace EWebCartPro\USTariffPlan\Observer\Sales;

    use Magento\Framework\Event\Observer;
    use Magento\Framework\Event\ObserverInterface;

class OrderSaveAfter implements ObserverInterface
{
    public function execute(Observer $observer)
    {
        /** @var \Magento\Sales\Model\Order $order */
        $order = $observer->getEvent()->getOrder();
        $quote = $order->getQuote();
        
        if ($quote) {
            $tariffAmount = $quote->getUsTariff();
            $baseTariffAmount = $quote->getBaseUsTariff();
            
            if ($tariffAmount > 0) {
                $order->setUsTariff($tariffAmount);
                $order->setBaseUsTariff($baseTariffAmount);
                $extensionAttributes = $order->getExtensionAttributes();
                if ($extensionAttributes) {
                    $extensionAttributes->setUsTariff($tariffAmount);
                    $extensionAttributes->setBaseUsTariff($baseTariffAmount);
                    $order->setExtensionAttributes($extensionAttributes);
                }                
                $order->save();
            }
        }
    }
}