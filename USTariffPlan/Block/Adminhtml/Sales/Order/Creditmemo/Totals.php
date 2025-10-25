<?php
namespace EWebCartPro\USTariffPlan\Block\Adminhtml\Sales\Order\Creditmemo;

class Totals extends \Magento\Framework\View\Element\Template
{
    protected $_dataHelper;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \EWebCartPro\USTariffPlan\Helper\Data $dataHelper,
        array $data = []
    ) {
        $this->_dataHelper = $dataHelper;
        parent::__construct($context, $data);
    }

    /**
     * Get totals source model (creditmemo)
     *
     * @return \Magento\Framework\DataObject|\Magento\Sales\Model\Order\Creditmemo
     */
    public function getSource()
    {
        return $this->getParentBlock()->getSource();
    }

    /**
     * Get creditmemo
     *
     * @return \Magento\Sales\Model\Order\Creditmemo|null
     */
    public function getCreditmemo()
    {
        return $this->getParentBlock()->getCreditmemo();
    }

    /**
     * Get order from creditmemo safely
     *
     * @return \Magento\Sales\Model\Order|null
     */
    public function getOrder()
    {
        $creditmemo = $this->getCreditmemo();
        if ($creditmemo && $creditmemo->getOrder()) {
            return $creditmemo->getOrder();
        }
        $parent = $this->getParentBlock();
        if ($parent && method_exists($parent, 'getOrder')) {
            return $parent->getOrder();
        }
        return null;
    }

    /**
     * Initialize custom totals
     *
     * @return $this
     */
    public function initTotals()
    {
        $parent = $this->getParentBlock();
        $source = $this->getSource();
        if (!$source || !$source->getUsTariff()) {
            return $this;
        }        
        // US Tariff
        $usTariff = new \Magento\Framework\DataObject([
            'code'  => 'us_tariff_fee',
            'value' => (float) $source->getUsTariff(),
            'label' => $this->_dataHelper->getFeeLabel()
        ]);
        $parent->addTotalBefore($usTariff, 'shipping');
        return $this;
    }

    /**
     * Numeric amount calculations (float)
     */
    public function getProductCostAmount()
    {
        $subtotal = (float) $this->getSource()->getSubtotal() ?: 0.0;
        return $subtotal * 0.55;
    }

    public function getHandlingFeeAmount()
    {
        $subtotal = (float) $this->getSource()->getSubtotal() ?: 0.0;
        return $subtotal * 0.20;
    }

    public function getReviewFeeAmount()
    {
        $subtotal = (float) $this->getSource()->getSubtotal() ?: 0.0;
        return $subtotal * 0.25;
    }

    /**
     * Optional: helper to format price if needed in template
     */
    public function formatPrice($amount)
    {
        $order = $this->getOrder();
        if ($order) {
            return $order->formatPrice($amount);
        }
        return number_format((float) $amount, 2);
    }
}
