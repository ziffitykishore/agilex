<?php

namespace Ziffity\Blockcustomers\Model;

use Magento\Framework\Model\AbstractModel;
use Ziffity\Blockcustomers\Api\Data\DataInterface;

class Data extends AbstractModel implements DataInterface
{
    
    const CACHE_TAG = 'blocked_customers';

    
    protected function _construct()
    {

        $this->_init('Ziffity\Blockcustomers\Model\ResourceModel\Data');
    }

    
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    public function getId()
    {
        return $this->getData(DataInterface::ID);
    }

    
    public function setId($id)
    {
        return $this->setData(DataInterface::ID, $id);
    }

    
    public function getName()
    {
        return $this->getData(DataInterface::NAME);
    }

    
    public function setName($name)
    {
        return $this->setData(DataInterface::NAME, $name);
    }

    public function getEmail()
    {
        return $this->getData(DataInterface::EMAIL);
    }

    
    public function setEmail($email)
    {
        return $this->setData(DataInterface::EMAIL, $email);
    }
    
    public function getReason()
    {
        return $this->getData(DataInterface::REASON);
    }

    
    public function setReason($reason)
    {
        return $this->setData(DataInterface::REASON, $reason);
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
