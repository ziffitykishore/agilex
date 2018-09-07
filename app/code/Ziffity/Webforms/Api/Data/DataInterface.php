<?php

namespace Ziffity\Webforms\Api\Data;

interface DataInterface
{
    const CUST_ID          = 'cust_id';
    const CUST_NAME        = 'cust_name';
    const CUST_EMAIL       = 'cust_email';
    const CUST_PHONE       = 'cust_phone';
    const CUST_COMMENTS    = 'cust_comments';
    const CUST_FN          = 'cust_fn';
    const CUST_LN          = 'cust_ln';
    const CUST_FIND        = 'cust_find';
    const CUST_ADDR_ONE    = 'cust_addr_one';
    const CUST_ADDR_TWO    = 'cust_addr_two';
    const CUST_CITY        = 'cust_city';
    const CUST_STATE       = 'cust_state';
    const CUST_ZIP         = 'cust_zip';
    const FORM_TYPE        = 'form_type';
    const IS_ACTIVE        = 'is_active';
    const CREATED_AT       = 'created_at';
    const UPDATED_AT       = 'updated_at';


    public function getId();

    public function setId($id);

    public function getCustName();

    public function setCustName($name);
    
    public function getCustEmail();

    public function setCustEmail($email);

    public function getCustPhone();

    public function setCustPhone($phone);
    
    public function getCustComments();

    public function setCustComments($comments);

    public function getCustFn();

    public function setCustFn($fn);

    public function getCustLn();

    public function setCustLn($ln);
    
    public function getCustFind();

    public function setCustFind($find);
    
    public function getCustAddrOne();

    public function setCustAddrOne($addrOne);
    
    public function getCustAddrTwo();

    public function setCustAddrTwo($addrTwo);
    
    public function getCustCity();

    public function setCustCity($city);
    
    public function getCustState();

    public function setCustState($state);
    
    public function getCustZip();

    public function setCustZip($zip);

    public function getFormType();

    public function setFormType($type);
    
    public function getIsActive();

    public function setIsActive($isActive);

    public function getCreatedAt();

    public function setCreatedAt($createdAt);

    public function getUpdatedAt();

    public function setUpdatedAt($updatedAt);
}
