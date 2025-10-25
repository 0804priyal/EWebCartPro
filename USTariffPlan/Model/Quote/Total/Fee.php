<?php
namespace EWebCartPro\USTariffPlan\Model\Quote\Total;

    use Magento\Quote\Model\Quote;
    use Magento\Quote\Model\Quote\Address\Total;
    use Magento\Quote\Api\Data\ShippingAssignmentInterface;
    use Magento\Quote\Model\Quote\Address\Total\AbstractTotal;

class Fee extends AbstractTotal
{
    protected $helperData;
    protected $checkoutSession;
    
    public function __construct(
        \EWebCartPro\USTariffPlan\Helper\Data $helperData,
        \Magento\Checkout\Model\Session $checkoutSession
    ) {
        $this->setCode('us_tariff');
        $this->helperData = $helperData;
        $this->checkoutSession = $checkoutSession;
    }

    public function collect(
        Quote $quote,
        ShippingAssignmentInterface $shippingAssignment,
        Total $total
    ) {
        parent::collect($quote, $shippingAssignment, $total);
        if (!count($shippingAssignment->getItems())) {
            return $this;
        }        
        $tariffFee = $this->calculateTariffFromQuote($quote);        
        if ($tariffFee > 0) {
            $total->setTotalAmount('us_tariff', $tariffFee);
            $total->setBaseTotalAmount('us_tariff', $tariffFee);            
            $quote->setUsTariff($tariffFee);
        } else {
            $total->setTotalAmount('us_tariff', 0);
            $total->setBaseTotalAmount('us_tariff', 0);
            $quote->setUsTariff(0);
        }
        return $this;
    }

    /**
     * Calculate tariff directly from quote items to avoid infinite loop
    */
    protected function calculateTariffFromQuote(Quote $quote)
    {
        $items = $quote->getAllVisibleItems();        
        // Check if we're on checkout page by looking at shipping address
        $shippingAddress = $quote->getShippingAddress();
        $shippingCountryId = $shippingAddress ? $shippingAddress->getCountryId() : null;        
        // If on cart page (no shipping country set), calculate tariff for all customers
        // If on checkout page, only calculate for US shipping
        if ($shippingCountryId && $shippingCountryId !== 'US') {
            return 0;
        }
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        $tariff_india = (float)$this->helperData->getScopeConfig()->getValue(\EWebCartPro\USTariffPlan\Helper\Data::CONFIG_TARIFF_INDIA, $storeScope) ?: 0.0;
        $tariff_canada = (float)$this->helperData->getScopeConfig()->getValue(\EWebCartPro\USTariffPlan\Helper\Data::CONFIG_TARIFF_CANADA, $storeScope) ?: 0.0;
        $tariff_uk = (float)$this->helperData->getScopeConfig()->getValue(\EWebCartPro\USTariffPlan\Helper\Data::CONFIG_TARIFF_UNITED_KINGDOM, $storeScope) ?: 0.0;
        $tariff_turkey = (float)$this->helperData->getScopeConfig()->getValue(\EWebCartPro\USTariffPlan\Helper\Data::CONFIG_TARIFF_TURKEY, $storeScope) ?: 0.0;
        $tariff_austrailla = (float)$this->helperData->getScopeConfig()->getValue(\EWebCartPro\USTariffPlan\Helper\Data::CONFIG_TARIFF_AUSTRAILLA, $storeScope) ?: 0.0;
        $tariff_newzealand = (float)$this->helperData->getScopeConfig()->getValue(\EWebCartPro\USTariffPlan\Helper\Data::CONFIG_TARIFF_NEW_ZEALAND, $storeScope) ?: 0.0;
        $tariff_usa = (float)$this->helperData->getScopeConfig()->getValue(\EWebCartPro\USTariffPlan\Helper\Data::CONFIG_TARIFF_USA, $storeScope) ?: 0.0;

        $tariff = 0;
        $india = true;
        $canada = true;
        $united_kingdom = true;
        $turkey = true;
        $australia = true;
        $new_zealand = true;
        $new_usa = true;

        foreach ($items as $item) {
            $product = $item->getProduct();
            if (!$product->getData('dispensing_country')) {
                $product->load($product->getId());
            }            
            $dispensing_country = $product->getData('dispensing_country');
            $productPrice = $item->getPrice();
            $productQty = (int)$item->getQty();
            $productTotal = $productPrice * $productQty;
            if (($dispensing_country === 'India') && $india) {
                $tariff += $tariff_india;
                $india = false;
            } elseif (($dispensing_country === 'Canada') && $canada) {
                $tariff += $tariff_canada;
                $canada = false;
            } elseif (($dispensing_country === 'United Kingdom') && $united_kingdom) {
                $tariff += $tariff_uk;
                $united_kingdom = false;
            } elseif (($dispensing_country === 'Turkey') && $turkey) {
                $tariff += $tariff_turkey;
                $turkey = false;
            } elseif (($dispensing_country === 'Austrailla' || $dispensing_country === 'Australia') && $australia) {
                $tariff += $tariff_austrailla;
                $australia = false;
            } elseif (($dispensing_country === 'New Zealand') && $new_zealand) {
                $tariff += $tariff_newzealand;
                $new_zealand = false;
            } elseif (($dispensing_country === 'United States') && $new_usa) {
                $tariff += $tariff_usa;
                $new_usa = false;
            }
        }
        return $tariff;
    }

    public function fetch(Quote $quote, Total $total)
    {
        $tariffFee = $quote->getUsTariff();
        if ($tariffFee > 0) {
            return [
                'code'  => 'us_tariff',
                'title' => __('US Govt. Tariff Collection charge'),
                'value' => $tariffFee
            ];
        }
        return null;
    }
    public function getLabel()
    {
        return __('US Govt. Tariff Collection charge');
    }
}