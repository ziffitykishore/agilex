<?php
namespace Ziffity\Webforms\Model;

use Magento\Framework\Model\AbstractModel;
use Ziffity\Webforms\Api\Data\CatalogInterface;

class Catalog extends AbstractModel implements CatalogInterface
{
    
    const CACHE_TAG = 'catalog_request';

    
    protected function _construct()
    {

        $this->_init('Ziffity\Webforms\Model\ResourceModel\Catalog');
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
    
    
    public function getCustAddrOne()
    {
        return $this->getData(DataInterface::CUST_ADDR_ONE);
    }

    
    public function setCustAddrOne($addr)
    {
        return $this->setData(DataInterface::CUST_ADDR_ONE, $addr);
    }

    public function getCustAddrTwo()
    {
        return $this->getData(DataInterface::CUST_ADDR_TWO);
    }

    
    public function setCustAddrTwo($addr)
    {
        return $this->setData(DataInterface::CUST_ADDR_TWO, $addr);
    }
    
    
    
    public function getCustCity()
    {
        return $this->getData(DataInterface::CUST_CITY);
    }

    
    public function setCustCity($city)
    {
        return $this->setData(DataInterface::CUST_CITY, $city);
    }
    
    public function getCustState()
    {
        return $this->getData(DataInterface::CUST_STATE);
        
    }

    public function setCustState($state)
    {
        return $this->setData(DataInterface::CUST_STATE, $state);
    }

    public function getCustZip()
    {
        return $this->getData(DataInterface::CUST_ZIP);
        
    }

    public function setCustZip($zip)
    {
        return $this->setData(DataInterface::CUST_ZIP, $zip);
    }
    
    
}
