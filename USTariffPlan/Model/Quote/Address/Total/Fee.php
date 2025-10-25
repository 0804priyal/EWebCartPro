<?php
namespace EWebCartPro\USTariffPlan\Model\Quote\Address\Total;

use Magento\Quote\Model\Quote;
use Magento\Quote\Model\Quote\Address\Total;
use Magento\Quote\Model\Quote\Address\Total\AbstractTotal;
use EWebCartPro\USTariffPlan\Helper\Data as FeeHelper;

class Fee extends AbstractTotal
{
    protected $feeHelper;
    public function __construct(
        FeeHelper $feeHelper
    ) {
        $this->feeHelper = $feeHelper;
    }

    public function collect(
        Quote $quote,
        \Magento\Quote\Api\Data\ShippingAssignmentInterface $shippingAssignment,
        Total $total
    ) {
        parent::collect($quote, $shippingAssignment, $total);
        // Apply in admin area
        if (!$this->isAdminArea() && !$quote->getIsAdmin()) {
            return $this;
        }
        $feeAmount = $this->feeHelper->getCustomFee();        
        if ($feeAmount > 0) {
            $total->setTotalAmount('us_tariff', $feeAmount);
            $total->setBaseTotalAmount('us_tariff', $feeAmount);
            $total->setUsTariff($feeAmount);
            $total->setBaseUsTariff($feeAmount);
            $total->setGrandTotal($total->getGrandTotal() + $feeAmount);
            $total->setBaseGrandTotal($total->getBaseGrandTotal() + $feeAmount);            
            // Set fee in quote with correct field names
            $quote->setUsTariff($feeAmount);
            $quote->setBaseUsTariff($feeAmount);
        }
        return $this;
    }

    public function fetch(Quote $quote, Total $total)
    {
        $feeAmount = $quote->getUsTariff();
        if ($feeAmount > 0) {
            return [
                'code' => 'us_tariff',
                'title' => $this->feeHelper->getFeeLabel(),
                'value' => $feeAmount
            ];
        }        
        return [];
    }

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
}