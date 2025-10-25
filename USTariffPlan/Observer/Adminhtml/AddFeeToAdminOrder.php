<?php
namespace EWebCartPro\USTariffPlan\Observer\Adminhtml;

    use Magento\Framework\Event\Observer;
    use Magento\Framework\Event\ObserverInterface;
    use Magento\Backend\Model\Session\Quote as SessionQuote;
    use EWebCartPro\USTariffPlan\Helper\Data as FeeHelper;

class AddFeeToAdminOrder implements ObserverInterface
{
    protected $sessionQuote;
    protected $feeHelper;

    public function __construct(
        SessionQuote $sessionQuote,
        FeeHelper $feeHelper
    ) {
        $this->sessionQuote = $sessionQuote;
        $this->feeHelper = $feeHelper;
    }

    public function execute(Observer $observer)
    {
        /** @var \Magento\Quote\Model\Quote $quote */
        $quote = $this->sessionQuote->getQuote();        
        // Check if quote has items
        if ($quote->getItemsCount() == 0) {
            return $this;
        }
        if (!$this->feeHelper->isModuleEnabled()) {
            return $this;
        }
        $feeAmount = $this->feeHelper->getCustomFee();
        
        if ($feeAmount > 0) {
            $quote->setUsTariff(0);
            $quote->setBaseUsTariff(0);
            $quote->setUsTariff($feeAmount);
            $quote->setBaseUsTariff($feeAmount);
            $quote->setTotalsCollectedFlag(false);
            $quote->collectTotals();
            $quote->save();
        }        
        return $this;
    }
}