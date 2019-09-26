<?php

namespace Creatuity\Nav\Api\Data;

interface DataInterface
{
    
    const LOG_ID           = 'log_id';
    const LOG_TYPE         = 'log_type';
    const LOG_STATUS       = 'log_status';
    const DESCRIPTION      = 'description';
    const CREATED_AT       = 'created_at';

    /**
     * Get ID
     *
     * @return int|null
     */
    public function getLogId();

    /**
     * Set ID
     *
     * @param $logId
     *
     * @return DataInterface
     */
    public function setLogId($logId);

    /**
     * Get Log Type
     *
     * @return string
     */
    public function getLogType();

    /**
     * Set Log Type
     *
     * @param $type
     *
     * @return mixed
     */
    public function setLogType($type);
    
    /**
     * Get Log status
     *
     * @return string
     */
    public function getLogStatus();

    /**
     * Set Log status
     *
     * @param $status
     *
     * @return mixed
     */
    public function setLogStatus($status);

    /**
     * Get Description
     *
     * @return mixed
     */
    public function getDescription();

    /**
     * Set Description
     *
     * @param $description
     *
     * @return mixed
     */
    public function setDescription($description);

    /**
     * Get created at
     *
     * @return string
     */
    public function getCreatedAt();

    /**
     * Set created at
     *
     * @param $createdAt
     *
     * @return DataInterface
     */
    public function setCreatedAt($createdAt);
}
