<?php
 
namespace Ziffity\Checkout\Model\Data;

use Ziffity\Checkout\Api\Data\OrderInfoInterface;
use Magento\Framework\Api\AbstractSimpleObject;

class OrderInfo extends AbstractSimpleObject implements OrderInfoInterface
{
    const STORE_LOCATION = 'store_location';

    const STORE_ADDRESS = 'store_address';
    
    /**
     * @return string|null
     */
    public function getStoreLocation()
    {
        return $this->_get(static::STORE_LOCATION);
    }

    /**
     * @param string $location
     * @return $this
     */
    public function setStoreLocation($location)
    {
        return $this->setData(static::STORE_LOCATION, $location);
    }

    /**
     * @return string|null
     */
    public function getStoreAddress()
    {
        return $this->_get(static::STORE_ADDRESS);
    }

    /**
     * @param string $address
     * @return $this
     */
    public function setStoreAddress($address)
    {
        return $this->setData(static::STORE_ADDRESS, $address);
    }
}
