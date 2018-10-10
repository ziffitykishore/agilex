<?php
namespace Ziffity\Dataencryption\Magento\Mail\Template;
class TransportBuilder extends \Magento\Framework\Mail\Template\TransportBuilder {

    public function addAttachment(
    $body, $mimeType = \Zend_Mime::TYPE_OCTETSTREAM, $disposition = \Zend_Mime::DISPOSITION_ATTACHMENT, $encoding = \Zend_Mime::ENCODING_BASE64, $filename = null
    ) {
        $attachedFile = $this->message->createAttachment($body, $mimeType, $disposition, $encoding, $filename);
        $attachedFile->type = 'text/csv';
        $attachedFile->filename = basename($filename);
        return $this;
    }

}
