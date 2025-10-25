<?php
namespace EWebCartPro\USTariffPlan\Block\Sales\Totals;

class Fee extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \EWebCartPro\USTariffPlan\Helper\Data
    */
    protected $_dataHelper;

    /**
     * @var Order
    */
    protected $_order;

    /**
     * @var \Magento\Framework\DataObject
    */
    protected $_source;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param array $data
    */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
         \EWebCartPro\USTariffPlan\Helper\Data $dataHelper,
        array $data = []
    )
    {
        $this->_dataHelper = $dataHelper;
        parent::__construct($context, $data);
    }

    /**
     * Check if we nedd display full tax total info
     *
     * @return bool
    */
    public function displayFullSummary()
    {
        return true;
    }

    /**
     * Get data (totals) source model
     *
     * @return \Magento\Framework\DataObject
    */
    public function getSource()
    {
        return $this->_source;
    }

    public function getStore()
    {
        return $this->_order->getStore();
    }

    /**
     * @return Order
     */
    public function getOrder()
    {
        return $this->_order;
    }

    /**
     * @return array
     */
    public function getLabelProperties()
    {
        return $this->getParentBlock()->getLabelProperties();
    }

    /**
     * @return array
     */
    public function getValueProperties()
    {
        return $this->getParentBlock()->getValueProperties();
    }

    /**
     * @return $this
     */
    public function initTotals()
    {
        $parent = $this->getParentBlock();
        $this->_order = $parent->getOrder();
        $this->_source = $parent->getSource();

        // US Tariff (final sum or extra line)
        $usTariff = new \Magento\Framework\DataObject([
            'code'   => 'us_tariff_fee',
            'strong' => false,
            'value'  => $this->_source->getUsTariff(),
            'label'  => $this->_dataHelper->getFeeLabel()
        ]);
        $parent->addTotalBefore($usTariff, 'shipping');
        return $this;
    }

    public function getProductCost()
    {
        $productCost = $this->_source->getSubtotal();
        return $this->getOrder()->formatPrice($productCost * 0.55);
    }
    public function getHandlingFee()
    {
        $handlingFee = $this->_source->getSubtotal();
        return $this->getOrder()->formatPrice($handlingFee * 0.20);
    }
    public function getReviewFee()
    {
        $reviewFee = $this->_source->getSubtotal();
        return $this->getOrder()->formatPrice($reviewFee * 0.25);
    }
}
