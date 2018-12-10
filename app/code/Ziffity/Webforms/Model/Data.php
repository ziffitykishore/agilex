<?php

namespace Ziffity\Webforms\Model;

use Magento\Framework\Model\AbstractModel;
use Ziffity\Webforms\Api\Data\DataInterface;

class Data extends AbstractModel implements DataInterface
{
    
    const CACHE_TAG = 'webforms_customer_data';

    
    protected function _construct()
    {

        $this->_init('Ziffity\Webforms\Model\ResourceModel\Data');
    }

    
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    public function getId()
    {
        return $this->getData(DataInterface::CUST_ID);
    }

    
    public function setId($id)
    {
        return $this->setData(DataInterface::CUST_ID, $id);
    }

    
    public function getCustName()
    {
        return $this->getData(DataInterface::CUST_NAME);
    }

    
    public function setCustName($name)
    {
        return $this->setData(DataInterface::CUST_NAME, $name);
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

    public function getCustComments()
    {
        return $this->getData(DataInterface::CUST_COMMENTS);
    }

    
    public function setCustComments($comments)
    {
        return $this->setData(DataInterface::CUST_COMMENTS, $comments);
    }
    
    public function getCustFn()
    {
        return $this->getData(DataInterface::CUST_FN);
    }

    
    public function setCustFn($fn)
    {
        return $this->setData(DataInterface::CUST_FN, $fn);
    }

    public function getCustLn()
    {
        return $this->getData(DataInterface::CUST_LN);
    }

    
    public function setCustLn($ln)
    {
        return $this->setData(DataInterface::CUST_LN, $ln);
    }
    
    public function getCustFind()
    {
        return $this->getData(DataInterface::CUST_FIND);
    }

    
    public function setCustFind($find)
    {
        return $this->setData(DataInterface::CUST_FIND, $find);
    }

    public function getCustAddrOne()
    {
        return $this->getData(DataInterface::CUST_ADDR_ONE);
    }

    
    public function setCustAddrOne($addrOne)
    {
        return $this->setData(DataInterface::CUST_ADDR_ONE, $addrOne);
    }
    
    public function getCustAddrTwo()
    {
        return $this->getData(DataInterface::CUST_ADDR_TWO);
    }

    
    public function setCustAddrTwo($addrTwo)
    {
        return $this->setData(DataInterface::CUST_ADDR_TWO, $addrTwo);
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

    public function getFormType()
    {
        return $this->getData(DataInterface::FORM_TYPE);
    }

    
    public function setFormType($type)
    {
        return $this->setData(DataInterface::FORM_TYPE, $type);
    }
    
    
    public function getIsActive()
    {
        return $this->getData(DataInterface::IS_ACTIVE);
    }

    
    public function setIsActive($isActive)
    {
        return $this->setData(DataInterface::IS_ACTIVE, $isActive);
    }

    
    public function getCreatedAt()
    {
        return $this->getData(DataInterface::CREATED_AT);
    }

    
    public function setCreatedAt($createdAt)
    {
        return $this->setData(DataInterface::CREATED_AT, $createdAt);
    }

   
    public function getUpdatedAt()
    {
        return $this->getData(DataInterface::UPDATED_AT);
    }

    
    public function setUpdatedAt($updatedAt)
    {
        return $this->setData(DataInterface::UPDATED_AT, $updatedAt);
    }

    public function setStoreId($storeId)
    {
        return $this->setData(DataInterface::STORE_ID, $storeId);
    }

    public function getStoreId()
    {
        return $this->getData(DataInterface::STORE_ID);
    }

    public function setCustomerId($customerId)
    {
        return $this->setData(DataInterface::CUSTOMER_ID, $customerId);
    }

    public function getCustomerId()
    {
        return $this->getData(DataInterface::CUSTOMER_ID);
    }

    public function setCustomerIp($customerIp)
    {
        return $this->setData(DataInterface::CUSTOMER_IP, $customerIp);
    }

    public function getCustomerIp()
    {
        return $this->getData(DataInterface::UPDATED_AT);
    }
}
