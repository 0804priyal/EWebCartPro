<?php
namespace EWebCartPro\USTariffPlan\Helper;

    use Magento\Framework\App\Helper\AbstractHelper;
    use Magento\Checkout\Model\Cart;
    use Magento\Checkout\Model\Session as CheckoutSession;
    use Magento\Backend\Model\Session\Quote as AdminQuoteSession;
    use Psr\Log\LoggerInterface;
    use EWebCartPro\USTariffPlan\Helper\Customer as CustomerHelper;

class Data extends AbstractHelper
{
    const CONFIG_CUSTOM_IS_ENABLED = 'tariffplan/tariffplanstatus/status';
    const CONFIG_TARIFF_INDIA = 'tariffplan/content/tariff_amount_india';
    const CONFIG_TARIFF_CANADA = 'tariffplan/content/tariff_amount_canada';
    const CONFIG_TARIFF_UNITED_KINGDOM = 'tariffplan/content/tariff_amount_united_kingdom';
    const CONFIG_TARIFF_TURKEY = 'tariffplan/content/tariff_amount_turkey';
    const CONFIG_TARIFF_AUSTRAILLA = 'tariffplan/content/tariff_amount_austrailla';
    const CONFIG_TARIFF_NEW_ZEALAND = 'tariffplan/content/tariff_amount_new_zealand';
    const CONFIG_TARIFF_USA = 'tariffplan/content/tariff_amount_usa';
    const CONFIG_FEE_LABEL = 'tariffplan/content/name';
    const CONFIG_MINIMUM_ORDER_AMOUNT = 'tariffplan/content/minimum_order_amount';

    protected $cart;
    protected $checkoutSession;
    protected $adminQuoteSession;
    protected $logger;
    protected $customerHelper;

    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        Cart $cart,
        CheckoutSession $checkoutSession,
        AdminQuoteSession $adminQuoteSession,
        CustomerHelper $customerHelper,
        LoggerInterface $logger
    ) {
        parent::__construct($context);
        $this->cart = $cart;
        $this->checkoutSession = $checkoutSession;
        $this->adminQuoteSession = $adminQuoteSession;
        $this->customerHelper = $customerHelper;
        $this->logger = $logger;
    }

    public function getCustomFee()
    {
        static $isCalculatingFee = false;
        if ($isCalculatingFee) {
            return 0;
        }
        $isCalculatingFee = true;

        try {
            $products = $this->getCartProductsDetails();
            if (empty($products)) {
                return 0;
            }
            $loggedCustomerId = $this->getCustomerId();
            $customerCountryId = $this->customerHelper->getCustomerCountryId($loggedCustomerId);
            $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
            
            $tariff_india = (float)$this->scopeConfig->getValue(self::CONFIG_TARIFF_INDIA, $storeScope) ?: 0.0;
            $tariff_canada = (float)$this->scopeConfig->getValue(self::CONFIG_TARIFF_CANADA, $storeScope) ?: 0.0;
            $tariff_uk = (float)$this->scopeConfig->getValue(self::CONFIG_TARIFF_UNITED_KINGDOM, $storeScope) ?: 0.0;
            $tariff_turkey = (float)$this->scopeConfig->getValue(self::CONFIG_TARIFF_TURKEY, $storeScope) ?: 0.0;
            $tariff_austrailla = (float)$this->scopeConfig->getValue(self::CONFIG_TARIFF_AUSTRAILLA, $storeScope) ?: 0.0;
            $tariff_newzealand = (float)$this->scopeConfig->getValue(self::CONFIG_TARIFF_NEW_ZEALAND, $storeScope) ?: 0.0;
            $tariff_usa = (float)$this->scopeConfig->getValue(self::CONFIG_TARIFF_USA, $storeScope) ?: 0.0;
            /*
            $this->logger->info('Tariff Config Values - India: ' . $tariff_india . 
                           ', Canada: ' . $tariff_canada . 
                           ', UK: ' . $tariff_uk . 
                           ', Turkey: ' . $tariff_turkey . 
                           ', Australia: ' . $tariff_austrailla . 
                           ', New Zealand: ' . $tariff_newzealand . 
                           ', USA: ' . $tariff_usa);
            */
            $tariff = 0;
            $india = true;
            $canada = true;
            $united_kingdom = true;
            $turkey = true;
            $australia = true;
            $new_zealand = true;
            $new_usa = true;

            if($customerCountryId === 'US') {
                foreach ($products as $product) {
                    $dispensing_country = $product['dispensing_country'] ?? null;
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
            }
            return $tariff;
        } finally {
            $isCalculatingFee = false;
        }
    }

    /**
     * Check if we're in admin area
    */
    protected function isAdminArea()
    {
        $area = \Magento\Framework\App\Area::AREA_ADMINHTML;
        $state = \Magento\Framework\App\ObjectManager::getInstance()
            ->get(\Magento\Framework\App\State::class);
        try {
            return $state->getAreaCode() === $area;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function getCartProductsDetails()
    {
        $quote = $this->getQuote();        
        $items = $quote->getAllVisibleItems();
        $products = [];        
        foreach ($items as $item) {
            $product = $item->getProduct();
            if (!$product->getData('dispensing_country')) {
                $product->load($product->getId());
            }            
            $dispensingCountry = $product->getData('dispensing_country');
            $productPrice = $item->getPrice() ?: $item->getProduct()->getPrice();
            $productQty = (int)$item->getQty();
            $rowTotal = $item->getRowTotal() ?: ($productPrice * $productQty);            
            $products[] = [
                'product_id'      => $product->getId(),
                'sku'             => $product->getSku(),
                'name'            => $product->getName(),
                'dispensing_country' => $dispensingCountry,
                'qty'             => $productQty,
                'price'           => $productPrice,
                'row_total'       => $rowTotal
            ];
        }
        return $products;
    }

    /**
     * Get the appropriate quote based on context (frontend or admin)
    */
    public function getQuote()
    {
        if ($this->isAdminArea()) {
            $adminQuote = $this->adminQuoteSession->getQuote();
            return $adminQuote;
        }        
        $frontendQuote = $this->cart->getQuote();
        return $frontendQuote;
    }

    public function getCustomerId()
    {
        $quote = $this->getQuote();
        $customerId = $quote->getCustomerId();
        return $customerId ?: null;
    }

    public function isModuleEnabled()
    {
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        $isEnabled = $this->scopeConfig->getValue(self::CONFIG_CUSTOM_IS_ENABLED, $storeScope);
        return $isEnabled;
    }
    
    public function getProductCost()
    {
        $quote = $this->getQuote();
        $cartProductSubTotal = $quote->getSubtotal();
        $productCost = $cartProductSubTotal * 0.55;
        return $productCost;
    }

    public function getHandlingFee()
    {
        $quote = $this->getQuote();
        $cartProductSubTotal = $quote->getSubtotal();
        $handlingFee = ($cartProductSubTotal * 20)/100;
        return $handlingFee;
    }

    public function getReviewFee()
    {
        $quote = $this->getQuote();
        $cartProductSubTotal = $quote->getSubtotal();
        $reviewFee = ($cartProductSubTotal * 25)/100;
        return $reviewFee;
    }

    public function getTariffFee()
    {
        return $this->getCustomFee();
    }

    public function getFeeBreakdown()
    {
        return [
            'product_cost' => $this->getProductCost(),
            'handling_fee' => $this->getHandlingFee(),
            'review_fee' => $this->getReviewFee(),
            'tariff_fee' => $this->getTariffFee(),
            'total_fees' => $this->getCustomFee()
        ];
    }
    /**
     * Get custom fee
     *
     * @return mixed
    */
    public function getFeeLabel()
    {
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        $feeLabel = $this->scopeConfig->getValue(self::CONFIG_FEE_LABEL, $storeScope);
        return $feeLabel;
    }
    /**
     * @return mixed
    */
    public function getMinimumOrderAmount()
    {
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        $MinimumOrderAmount = $this->scopeConfig->getValue(self::CONFIG_MINIMUM_ORDER_AMOUNT, $storeScope);
        return $MinimumOrderAmount;
    }
    /**
     * Get customer helper
     */
    public function getCustomerHelper()
    {
        return $this->customerHelper;
    }

    /**
     * Get scope config
     */
    public function getScopeConfig()
    {
        return $this->scopeConfig;
    }
}