<?php
declare(strict_types = 1);
namespace Earthlite\OrderComments\Plugin\Quote;

use Magento\Quote\Model\QuoteRepository;

/**
 * class SaveToQuote
 */
class SaveToQuote
{
   /**
    *
    * @var QuoteRepository
    */
   protected $quoteRepository;
   
   /**
    * 
    * @param QuoteRepository $quoteRepository
    */
    public function __construct(
        QuoteRepository $quoteRepository
    ) {
        $this->quoteRepository = $quoteRepository;
    }
    
    /**
     * 
     * @param \Magento\Checkout\Model\ShippingInformationManagement $subject
     * @param type $cartId
     * @param \Magento\Checkout\Api\Data\ShippingInformationInterface $addressInformation
     * @return null|void
     */
    public function beforeSaveAddressInformation(
        \Magento\Checkout\Model\ShippingInformationManagement $subject,
        $cartId,
        \Magento\Checkout\Api\Data\ShippingInformationInterface $addressInformation
    ) {        
        if(!$extensionAttributes = $addressInformation->getExtensionAttributes())
        {
            return;
        }
        $quote = $this->quoteRepository->getActive($cartId);
        $quote->setOrderComments($extensionAttributes->getOrderComments());
    }
 }