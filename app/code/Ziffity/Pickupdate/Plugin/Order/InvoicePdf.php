<?php

namespace Ziffity\Pickupdate\Plugin\Order;

use Ziffity\Pickupdate\Model\Config\Source\IncludeInto;

/**
 * Insert Pickup Date information Block to Invoice Admin PDF
 */
class InvoicePdf extends \Ziffity\Pickupdate\Plugin\AbstractPdf
{
    /**
     * @param \Magento\Sales\Model\Order\Pdf\Invoice  $subject
     * @param \Magento\Sales\Model\Order\Invoice[] $shipments
     *
     * @return array
     */
    public function beforeGetPdf($subject, $shipments = [])
    {
        // just for note. All code content in abstract.
        return parent::beforeGetPdf($subject, $shipments);
    }

    /**
     * @param \Magento\Sales\Model\Order\Pdf\Invoice $subject
     * @param \Zend_Pdf_Page                          $page
     * @param string                                  $text
     *
     * @return array
     */
    public function beforeInsertDocumentNumber($subject, $page, $text)
    {
        // just for note. All code content in abstract
        return parent::beforeInsertDocumentNumber($subject, $page, $text);
    }

    protected function getWhatShow()
    {
        return $this->pickupHelper->whatShow(IncludeInto::INVOICE_PDF, 'include');
    }

    /**
     * {@inheritdoc}
     */
    protected function getPhrasePrefix()
    {
        return __('Invoice # ');
    }
}
