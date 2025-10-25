<?php
namespace EWebCartPro\USTariffPlan\Plugin;

class AbstractPdfFix
{
    public function beforeDrawText(
        \Magento\Sales\Model\Order\Pdf\AbstractPdf $subject,
        $text,
        $x = 0,
        $y = 0,
        $encoding = 'UTF-8'
    ) {
        // Ensure $text is always a string
        if ($text === null) {
            $text = '';
        }
        return [$text, $x, $y, $encoding];
    }
}
