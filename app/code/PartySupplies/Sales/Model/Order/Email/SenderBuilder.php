<?php

namespace PartySupplies\Sales\Model\Order\Email;

/**
 * Sender Builder
 */
class SenderBuilder extends \Magento\Sales\Model\Order\Email\SenderBuilder
{
    /**
     * Prepare and send email message
     *
     * @return void
     */
    public function send()
    {
        $this->configureEmailTemplate();

        $this->transportBuilder->addTo(
            $this->identityContainer->getCustomerEmail(),
            $this->identityContainer->getCustomerName()
        );

        $copyTo = $this->identityContainer->getEmailCopyTo();
        
        if (!empty($copyTo) && $this->identityContainer->getCopyMethod() == 'cc') {
            foreach ($copyTo as $email) {
                $this->transportBuilder->addCc($email);
            }
        } elseif (!empty($copyTo) && $this->identityContainer->getCopyMethod() == 'bcc') {
            foreach ($copyTo as $email) {
                $this->transportBuilder->addBcc($email);
            }
        }

        $transport = $this->transportBuilder->getTransport();
        $transport->sendMessage();
    }
}
