<?php
namespace EWebCartPro\USTariffPlan\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Psr\Log\LoggerInterface;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\AddressRepositoryInterface;

class Customer extends AbstractHelper
{
    protected $customerSession;
    protected $logger;
    protected $customerRepository;
    protected $addressRepository;

    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        CustomerSession $customerSession,
        CustomerRepositoryInterface $customerRepository,
        AddressRepositoryInterface $addressRepository,
        LoggerInterface $logger
    ) {
        $this->customerSession = $customerSession;
        $this->logger = $logger;
        $this->customerRepository = $customerRepository;
        $this->addressRepository = $addressRepository;
        parent::__construct($context);
    }
    
    /**
     * Get customer ID
     *
     * @return int|null
    */
    public function getCustomerId()
    {
        if ($this->customerSession->isLoggedIn()) {
            return $this->customerSession->getCustomerId();
        }
        return null;
    }
    public function getCustomerCountryId($customerId)
    {
        try {
            $customer = $this->customerRepository->getById($customerId);
            if ($customer->getDefaultBilling()) {
                $billingAddress = $this->addressRepository->getById($customer->getDefaultBilling());
                return $billingAddress->getCountryId();
            }
            // Fallback: default shipping
            if ($customer->getDefaultShipping()) {
                $shippingAddress = $this->addressRepository->getById($customer->getDefaultShipping());
                return $shippingAddress->getCountryId();
            }
        } catch (\Exception $e) {
            return null;
        }
        return null;
    }    
}
