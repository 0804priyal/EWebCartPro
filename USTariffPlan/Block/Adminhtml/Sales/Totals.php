<?php
namespace EWebCartPro\USTariffPlan\Block\Adminhtml\Sales;

class Totals extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \EWebCartPro\USTariffPlan\Helper\Data
    */
    protected $_dataHelper;

    /**
     * @var \Magento\Directory\Model\Currency
    */
    protected $_currency;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \EWebCartPro\USTariffPlan\Helper\Data $dataHelper,
        \Magento\Directory\Model\Currency $currency,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_dataHelper = $dataHelper;
        $this->_currency = $currency;
    }

    /**
     * Retrieve current order model instance
     *
     * @return \Magento\Sales\Model\Order
    */
    public function getOrder()
    {
        return $this->getParentBlock()->getOrder();
    }

    /**
     * @return mixed
    */
    public function getSource()
    {
        return $this->getParentBlock()->getSource();
    }

    /**
     * @return string
    */
    public function getCurrencySymbol()
    {
        return $this->_currency->getCurrencySymbol();
    }

    /**
     *
     *
     * @return $this
    */    
    public function initTotals()
    {
        $parent = $this->getParentBlock();
        $order = $this->getOrder();
        $source = $this->getSource();

        if (!$source->getUsTariff()) {
            return $this;
        }

        // US Tariff (final one)
        $usTariff = new \Magento\Framework\DataObject([
            'code'  => 'us_tariff_fee',
            'value' => $source->getUsTariff(),
            'label' => $this->_dataHelper->getFeeLabel()
        ]);
        $parent->addTotalBefore($usTariff, 'shipping');
        return $this;
    }
    public function getProductCost()
    {
        $productCost = $this->getSource()->getSubtotal();
        return $this->getOrder()->formatPrice($productCost * 0.55);
    }
    public function getHandlingFee()
    {
        $handlingFee = $this->getSource()->getSubtotal();
        return $this->getOrder()->formatPrice($handlingFee * 0.20);
    }
    public function getReviewFee()
    {
        $reviewFee = $this->getSource()->getSubtotal();
        return $this->getOrder()->formatPrice($reviewFee * 0.25);
    }
}
