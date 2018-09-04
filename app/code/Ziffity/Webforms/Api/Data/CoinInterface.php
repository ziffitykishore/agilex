<?php
namespace Ziffity\Webforms\Api\Data;

interface CoinInterface
{

    const CUST_ID           = 'customer_id';
    const CUST_FNAME        = 'customer_fn';
    const CUST_LNAME        = 'customer_ln';
    const CUST_EMAIL        = 'customer_email';
    const CUST_PHONE        = 'customer_phone';
    const CUST_FIND         = 'customer_find';
  

    public function getId();

    public function setId($id);

    public function getCustFname();

    public function setCustFname($fn);

    public function getCustLname();

    public function setCustLname($ln);
    
    public function getCustEmail();

    public function setCustEmail($email);

    public function getCustPhone();

    public function setCustPhone($phone);
    
    public function getCustFind();
    
    public function setCustFind($find);

}
