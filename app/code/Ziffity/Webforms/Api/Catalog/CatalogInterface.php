<?php
namespace Ziffity\Webforms\Api\Catalog;

interface CatalogInterface
{

    const CUST_ID           = 'customer_id';
    const CUST_FNAME        = 'customer_fn';
    const CUST_LNAME        = 'customer_ln';
    const CUST_ADDR_ONE     = 'customer_addr_one';
    const CUST_ADDR_TWO     = 'customer_addr_two';    
    const CUST_CITY         = 'customer_city';
    const CUST_STATE        = 'customer_state';
    const CUST_ZIP          = 'customer_zip';
  

    public function getId();

    public function setId($id);

    public function getCustFname();

    public function setCustFname($fn);

    public function getCustLname();

    public function setCustLname($ln);
    
    public function getCustAddrOne();
    
    public function setCustAddrOne($addr);

    public function getCustAddrTwo();
    
    public function setCustAddrTwo($addr);
    
    public function getCustCity();

    public function setCustCity($city);

    public function getCustState();

    public function setCustState($state);
    
    public function getCustZip();
    
    public function setCustZip($zip);

}
