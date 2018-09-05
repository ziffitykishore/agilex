<?php
namespace Ziffity\Webforms\Model;

use Magento\Framework\Model\AbstractModel;
use Ziffity\Webforms\Api\Coin\CoinInterface;

class Coin extends AbstractModel implements CoinInterface
{
    
    const CACHE_TAG = 'find_your_coin';

    
    protected function _construct()
    {

        $this->_init('Ziffity\Webforms\Model\ResourceModel\Coin');
    }

    
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    public function getCustFname()
    {
        return $this->getData(DataInterface::CUST_FNAME);
    }

    
    public function setCustFname($fn)
    {
        return $this->setData(DataInterface::CUST_FNAME, $fn);
    }

    public function getCustLname()
    {
        return $this->getData(DataInterface::CUST_LNAME);
    }

    
    public function setCustLname($ln)
    {
        return $this->setData(DataInterface::CUST_LNAME, $ln);
    }
    
    
    public function getCustEmail()
    {
        return $this->getData(DataInterface::CUST_EMAIL);
    }

    
    public function setCustEmail($email)
    {
        return $this->setData(DataInterface::CUST_EMAIL, $email);
    }

    public function getCustPhone()
    {
        return $this->getData(DataInterface::CUST_PHONE);
    }

    
    public function setCustPhone($phone)
    {
        return $this->setData(DataInterface::CUST_PHONE, $phone);
    }
    
    public function getCustFind()
    {
        return $this->getData(DataInterface::CUST_FIND);
        
    }

    public function setCustFind($find)
    {
        return $this->setData(DataInterface::CUST_FIND, $find);
    }

    
}
