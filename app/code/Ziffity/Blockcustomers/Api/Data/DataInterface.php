<?php

namespace Ziffity\Blockcustomers\Api\Data;

interface DataInterface
{
    /**
     * Constants for keys of data array. Identical to the name of the getter in snake case
     */
    const ID                = 'id';
    const NAME              = 'name';
    const EMAIL             = 'email';
    const REASON            = 'reason';
    const IS_ACTIVE         = 'is_active';
    const CREATED_AT        = 'created_at';
    const UPDATED_AT        = 'updated_at';


    /**
     * Get ID
     *
     * @return int|null
     */
    public function getId();

    /**
     * Set ID
     *
     * @param $id
     * @return DataInterface
     */
    public function setId($id);

    /**
     * Get Name
     *
     * @return string
     */
    public function getName();

    /**
     * Set Name
     *
     * @param $title
     * @return mixed
     */
    public function setName($name);

    /**
     * Get Email
     *
     * @return mixed
     */
    public function getEmail();

    /**
     * Set Email
     *
     * @param $description
     * @return mixed
     */
    public function setEmail($email);

    /**
     * Get Reason
     */
    public function getReason();
    
    /**
     * Set Reason
     */
    public function setReason($reason);

    /**
     * Get is active
     *
     * @return bool|int
     */
    public function getIsActive();

    /**
     * Set is active
     *
     * @param $isActive
     * @return DataInterface
     */
    public function setIsActive($isActive);

    /**
     * Get created at
     *
     * @return string
     */
    public function getCreatedAt();

    /**
     * set created at
     *
     * @param $createdAt
     * @return DataInterface
     */
    public function setCreatedAt($createdAt);

    /**
     * Get updated at
     *
     * @return string
     */
    public function getUpdatedAt();

    /**
     * set updated at
     *
     * @param $updatedAt
     * @return DataInterface
     */
    public function setUpdatedAt($updatedAt);
}
