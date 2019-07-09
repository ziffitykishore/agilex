<?php
/**
 * @category    ClassyLlama
 * @copyright   Copyright (c) 2018 Classy Llama Studios, LLC
 */

namespace Ziffity\Avalara\Plugin;

use Magento\Framework\Event\ObserverInterface;
use Magento\Checkout\Model\Session as QuoteSession;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Customer\Api\CustomerRepositoryInterface as CustomerRepository;
use Magento\Framework\Exception\LocalizedException;
use Magento\Quote\Model\QuoteRepository;
use Magento\Store\Api\StoreRepositoryInterface;

class CalculateVirtualOrder implements ObserverInterface
{
    /**
     * @var QuoteSession
     */
    protected $quoteSession;

    /**
     * @var \Magento\Quote\Model\Quote\TotalsCollector
     */
    protected $totalsCollector;

    /**
     * @var CustomerRepository
     */
    protected $customerRepository;

    /**
     * @var CustomerSession
     */
    protected $customerSession;

    /**
     * @var \Magento\Quote\Api\CartRepositoryInterface
     */
    protected $quoteRepository;

    /**
     * @var \Magento\Customer\Api\AddressRepositoryInterface
     */
    protected $addressRepository;
    
    /**
     * @var \Magento\Quote\Api\Data\AddressInterface
     */
    protected $addressInterface;
    
    /**
     * @var \ClassyLlama\AvaTax\Helper\Config
     */
    protected $config;
    
    /**
     * @var StoreRepositoryInterface
     */
    protected $storeRepository = null;    
    
    /**
     * @var \Magento\Store\Model\Information 
     */
    protected $storeInfo;

    /**
     * @param QuoteSession                                     $quoteSession
     * @param \Magento\Quote\Model\Quote\TotalsCollector       $totalsCollector
     * @param CustomerRepository                               $customerRepository
     * @param CustomerSession                                  $customerSession
     * @param QuoteRepository                                  $quoteRepository
     * @param \Magento\Customer\Api\AddressRepositoryInterface $addressRepository
     */
    public function __construct(
        QuoteSession $quoteSession,
        \Magento\Quote\Model\Quote\TotalsCollector $totalsCollector,
        CustomerRepository $customerRepository,
        CustomerSession $customerSession,
        QuoteRepository $quoteRepository,
        \Magento\Customer\Api\AddressRepositoryInterface $addressRepository,
        \Magento\Quote\Api\Data\AddressInterface $addressInterface,
        \ClassyLlama\AvaTax\Helper\Config $helperConfig,
        StoreRepositoryInterface $storeRepository,
        \Magento\Store\Model\Information $storeInfo
    ) {
        $this->quoteSession = $quoteSession;
        $this->totalsCollector = $totalsCollector;
        $this->customerRepository = $customerRepository;
        $this->customerSession = $customerSession;
        $this->quoteRepository = $quoteRepository;
        $this->addressRepository = $addressRepository;
        $this->addressInterface = $addressInterface;
        $this->config = $helperConfig;
        $this->storeRepository = $storeRepository;
        $this->storeInfo = $storeInfo;
    }

    /**
     * Use Avatax to calculate tax for virtual orders
     *
     * @param \Magento\Framework\Event\Observer $observer
     *
     * @return $this
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /** @var \Magento\Sales\Api\Data\OrderInterface $order */
        $quote = $this->quoteSession->getQuote();

        if($this->customerSession->getCustomerId() === NULL && $quote->isVirtual()){

            $store = $this->storeRepository->getById($quote->getStoreId());            
            $origin = $this->config->getOriginAddress($store);

            $address = $this->addressInterface;
            $address->setFirstname($this->storeInfo->getStoreInformationObject($store)->getName())
                    ->setLastname($this->storeInfo->getStoreInformationObject($store)->getName())
                    ->setCountryId($origin['Country'])
                    ->setRegionId($origin['RegionId'])
                    ->setCity($origin['City'])
                    ->setPostcode($origin['PostalCode'])
                    ->setStreet([$origin['Line1'],$origin['Line2']])
                    ->setTelephone($this->storeInfo->getStoreInformationObject($store)->getPhone());

            $quote->setBillingAddress($address);
            $quote->setDataChanges(true);
            $this->quoteRepository->save($quote);
        }
        if (!is_null($quote) && $quote->isVirtual()) {
            try {                
                $customer = $this->customerRepository->getById($this->customerSession->getCustomerId());
                $addressId = $customer->getDefaultBilling();
                
                /** @var \Magento\Customer\Api\Data\AddressInterface $address */
                $address = $this->addressRepository->getById($addressId);
                $address = $quote->getBillingAddress()->importCustomerAddressData($address);
                
                if ($address !== null) {
                    $quote->setBillingAddress($address);
                    $quote->setDataChanges(true);
                    $this->quoteRepository->save($quote);
                }
            } catch (\Exception $e) {
                return $this;
            } catch (LocalizedException $e) {
                return $this;
            }
        }
        return $this;
    }
}

