<?php

namespace Ziffity\Zipcode\Model;

use Magento\Framework\Model\AbstractModel;
use Ziffity\Zipcode\Api\Data\DataInterface;

class Data extends AbstractModel implements DataInterface
{

    const CACHE_TAG = 'ziffity_zipcode_data';

    protected function _construct()
    {
        // @codingStandardsIgnoreEnd
        $this->_init('Ziffity\Zipcode\Model\ResourceModel\Data');
    }

    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    public function getSourceCode()
    {
        return $this->getData(DataInterface::SOURCE_CODE);
    }

    public function setSourceCode($code)
    {
        return $this->setData(DataInterface::SOURCE_CODE, $code);
    }

    public function getAllowedZipcodeList()
    {
        return $this->getData(DataInterface::ALLOWED_ZIPCODE_LIST);
    }

    public function setAllowedZipcodeList($zipcode)
    {
        return $this->setData(DataInterface::ALLOWED_ZIPCODE_LIST, $zipcode);
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
