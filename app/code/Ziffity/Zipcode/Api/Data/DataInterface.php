<?php

namespace Ziffity\Zipcode\Api\Data;

interface DataInterface
{

    const DATA_ID           = 'data_id';
    const SOURCE_CODE        = 'source_code';
    const ALLOWED_ZIPCODE_LIST  = 'allowed_zipcode_list';
    const IS_ACTIVE         = 'is_active';
    const CREATED_AT        = 'created_at';
    const UPDATED_AT        = 'updated_at';


    public function getId();

    public function setId($id);

    public function getSourceCode();

    public function setSourceCode($code);

    public function getAllowedZipcodeList();

    public function setAllowedZipcodeList($zipcode);

    public function getIsActive();

    public function setIsActive($isActive);

    public function getCreatedAt();

    public function setCreatedAt($createdAt);

    public function getUpdatedAt();

    public function setUpdatedAt($updatedAt);
}
