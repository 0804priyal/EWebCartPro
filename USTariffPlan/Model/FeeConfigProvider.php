<?php
namespace EWebCartPro\USTariffPlan\Model;

    use Magento\Checkout\Model\ConfigProviderInterface;
    use EWebCartPro\USTariffPlan\Helper\Data as FeeHelper;

class FeeConfigProvider implements ConfigProviderInterface
{
    protected $feeHelper;

    public function __construct(
        FeeHelper $feeHelper
    ) {
        $this->feeHelper = $feeHelper;
    }

    public function getConfig()
    {
        $feeBreakdown = $this->feeHelper->getFeeBreakdown();
        $customFee = $this->feeHelper->getCustomFee() ?: 0.0;
        $isEnabled = $this->feeHelper->isModuleEnabled();        
        $config = [];
        $config['show_hide_customfee_block'] = $isEnabled;
        $config['show_hide_customfee_shipblock'] = $isEnabled;
        $config['fee_label'] = $this->feeHelper->getFeeLabel();
        $config['custom_fee_amount'] = $customFee;        
        // Add individual fee components to config
        $config['fee_breakdown'] = [
            'product_cost' => $feeBreakdown['product_cost'],
            'handling_fee' => $feeBreakdown['handling_fee'],
            'review_fee' => $feeBreakdown['review_fee'],
            'tariff_fee' => $feeBreakdown['tariff_fee'],
            'total_fees' => $feeBreakdown['total_fees']
        ];
        return $config;
    }
}