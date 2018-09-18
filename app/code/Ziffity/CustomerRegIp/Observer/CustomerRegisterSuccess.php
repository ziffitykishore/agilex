<?php
namespace Ziffity\CustomerRegIp\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\HTTP\PhpEnvironment\RemoteAddress;

class CustomerRegisterSuccess implements ObserverInterface
{
    /**
     *  @var CustomerRepositoryInterface
     */
    protected $customerRepository;

    /**
     *  @var RemoteAddress
     */
    protected $remoteAddress;
    
    /**
     * @param CustomerRepositoryInterface $customerRepository
     */
    public function __construct(
        CustomerRepositoryInterface $customerRepository,
        RemoteAddress $remoteAddress
    ) {
        $this->customerRepository = $customerRepository;
        $this->remoteAddress = $remoteAddress;
    }

    /**
     * Manages redirect
     */
    public function execute(Observer $observer)
    {
        $customer = $observer->getCustomer();
        $customer->setCustomAttribute('registration_remote_ip', $this->getIpAddress());
        $this->customerRepository->save($customer);
    }

    /**
     * To get Ip address
     * 
     * @return string
     */
    public function getIpAddress()
    {
        $ipAddress  = $this->remoteAddress->getRemoteHost();
        if ('127.0.0.1' == $ipAddress) {
            return 'localhost';
        }
        return $ipAddress;
    }
}