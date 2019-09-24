<?php

namespace Creatuity\Nav\Model;

use Magento\Framework\Model\AbstractModel;
use Creatuity\Nav\Api\Data\DataInterface;

class Data extends AbstractModel implements DataInterface
{

    const CACHE_TAG = 'navision_log';

    protected function _construct()
    {
        $this->_init(\Creatuity\Nav\Model\ResourceModel\Data::class);
    }

    /**
     * Get cache identities
     *
     * @return array
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    /**
     * Get log id
     *
     * @return string
     */
    public function getLogId()
    {
        return $this->getData(DataInterface::LOG_ID);
    }

    /**
     * Set log id
     *
     * @param $logId
     *
     * @return $this
     */
    public function setLogId($logId)
    {
        return $this->setData(DataInterface::LOG_ID, $id);
    }
    
    /**
     * Get log type
     *
     * @return string
     */
    public function getLogType()
    {
        return $this->getData(DataInterface::LOG_TYPE);
    }
    
    /**
     * Set log type
     *
     * @param $type
     *
     * @return $this
     */
    public function setLogType($type)
    {
        return $this->setData(DataInterface::LOG_TYPE, $type);
    }

    /**
     * Get lot status
     *
     * @return string
     */
    public function getLogStatus()
    {
        return $this->getData(DataInterface::LOG_STATUS);
    }
    
    /**
     * Set log status
     *
     * @param $status
     *
     * @return $this
     */
    public function setLogStatus($status)
    {
        return $this->setData(DataInterface::LOG_STATUS, $status);
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->getData(DataInterface::DESCRIPTION);
    }

    /**
     * Set description
     *
     * @param $description
     *
     * @return $this
     */
    public function setDescription($description)
    {
        return $this->setData(DataInterface::DESCRIPTION, $description);
    }

    /**
     * Get created at
     *
     * @return string
     */
    public function getCreatedAt()
    {
        return $this->getData(DataInterface::CREATED_AT);
    }

    /**
     * Set created at
     *
     * @param $createdAt
     *
     * @return $this
     */
    public function setCreatedAt($createdAt)
    {
        return $this->setData(DataInterface::CREATED_AT, $createdAt);
    }
}
