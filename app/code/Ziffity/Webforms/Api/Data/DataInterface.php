<?php
namespace Ziffity\Webforms\Api\Data;

interface DataInterface
{

    const CUST_ID           = 'customer_id';
    const CUST_NAME        = 'customer_name';
    const CUST_EMAIL        = 'customer_email';
    const CUST_PHONE         = 'customer_phone';
    const CUST_COMMENTS        ='customer_comments';
  

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

}
