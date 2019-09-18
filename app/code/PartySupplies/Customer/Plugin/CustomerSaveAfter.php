<?php

namespace PartySupplies\Customer\Plugin;

use Magento\Customer\Controller\Adminhtml\Index\Save;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Mail\Template\SenderResolverInterface;
use Magento\Customer\Model\EmailNotification;
use Magento\Framework\Mail\Template\TransportBuilder;

class CustomerSaveAfter
{
    const XML_COMPANY_APPROVED_TEMPLATE = "customer/create_account/company_approved_email_template";
    
    const XML_COMPANY_DECLINED_TEMPLATE = "customer/create_account/company_declined_email_template";
    
    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;
    
    /**
     * @var SenderResolverInterface
     */
    private $senderResolver;
    
    /**
     * @var TransportBuilder
     */
    private $transportBuilder;
    
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        SenderResolverInterface $senderResolver,
        TransportBuilder $transportBuilder
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->senderResolver = $senderResolver;
        $this->transportBuilder = $transportBuilder;
    }
    
    public function afterExecute(Save $subject, $result)
    {
        $customerData = $subject->getRequest()->getPostValue();
        
        $templateId = $this->scopeConfig->getValue(CustomerSaveAfter::XML_COMPANY_DECLINED_TEMPLATE);
        $storeId = $customerData['customer']['store_id'];
        $email = $customerData['customer']['email'];

        /** @var array $from */
        $from = $this->senderResolver->resolve(
            $this->scopeConfig->getValue(EmailNotification::XML_PATH_REGISTER_EMAIL_IDENTITY, 'store', $storeId),
            $storeId
        );

        $transport = $this->transportBuilder->setTemplateIdentifier($templateId)
            ->setTemplateOptions(['area' => 'adminhtml', 'store' => $storeId])
            ->setTemplateVars($customerData)
            ->setFrom($from)
            ->addTo($email, "Company Name placeholder")
            ->getTransport();

        $transport->sendMessage();

        return $result;
    }
}
