<?php
namespace EWebCartPro\USTariffPlan\Block\Adminhtml\Sales\Order\Invoice;

class Totals extends \Magento\Framework\View\Element\Template
{
    protected $_dataHelper;

    /**
     * Order invoice
     *
     * @var \Magento\Sales\Model\Order\Invoice|null
    */
    protected $_invoice = null;

    /**
     * @var \Magento\Framework\DataObject
    */
    protected $_source;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \EWebCartPro\USTariffPlan\Helper\Data $dataHelper,
        array $data = []
    ) {
        $this->_dataHelper = $dataHelper;
        parent::__construct($context, $data);
    }

    /**
     * Get data (totals) source model
     *
     * @return \Magento\Framework\DataObject
     */
    public function getSource()
    {
        return $this->getParentBlock()->getSource();
    }

    /**
     * @return \Magento\Sales\Model\Order\Invoice
     */
    public function getInvoice()
    {
        return $this->getParentBlock()->getInvoice();
    }

    /**
     * Initialize custom US tariff totals
     *
     * @return $this
     */
    public function initTotals()
    {
        $parent = $this->getParentBlock();
        $invoice = $this->getInvoice();
        $source = $this->getSource();

        if (!$source->getUsTariff()) {
            return $this;
        }

        // US Tariff
        $usTariff = new \Magento\Framework\DataObject([
            'code'  => 'us_tariff_fee',
            'value' => $source->getUsTariff(),
            'label' => $this->_dataHelper->getFeeLabel()
        ]);
        $parent->addTotalBefore($usTariff, 'shipping');
        return $this;
    }

    /**
     * Calculate Product Cost (55% of subtotal)
     */
    public function getProductCost()
    {
        $subtotal = $this->getSource()->getSubtotal();
        return $this->getOrder()->formatPrice($subtotal * 0.55);
    }

    /**
     * Calculate Handling Fee (20% of subtotal)
     */
    public function getHandlingFee()
    {
        $subtotal = $this->getSource()->getSubtotal();
        return $this->getOrder()->formatPrice($subtotal * 0.20);
    }

    /**
     * Calculate Review Fee (25% of subtotal)
     */
    public function getReviewFee()
    {
        $subtotal = $this->getSource()->getSubtotal();
        return $this->getOrder()->formatPrice($subtotal * 0.25);
    }

    /**
     * Shortcut to fetch order from parent block
     */
    public function getOrder()
    {
        return $this->getParentBlock()->getOrder();
    }
}
