<?php
namespace Ziffity\Webforms\Model;

use Magento\Framework\Model\AbstractModel;
use Ziffity\Webforms\Api\Data\DataInterface;

class Data extends AbstractModel implements DataInterface
{
    
    const CACHE_TAG = 'ziffity_customer_comments';

    
    protected function _construct()
    {

        $this->_init('Ziffity\Webforms\Model\ResourceModel\Data');
    }

    
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
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
    
    
}
