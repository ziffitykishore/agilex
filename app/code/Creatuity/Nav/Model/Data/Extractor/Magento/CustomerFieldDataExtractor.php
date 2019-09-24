<?php
/**
 * Extract customer data
 */
namespace Creatuity\Nav\Model\Data\Extractor\Magento;

/**
 * CustomerFieldDataExtractor
 */
class CustomerFieldDataExtractor
{
    /**
     * @var string
     */
    protected $accessorMethod;

    /**
     * 
     * @param string $accessorMethod 
     */
    public function __construct(
        $accessorMethod
    ) {
        $this->accessorMethod = $accessorMethod;
    }

    /**
     * 
     * @param array $customerData 
     * 
     * @return string
     */
    public function extract(array $customerData)
    {
        return $this->{$this->accessorMethod}($customerData);
    }
    
    /**
     * 
     * @param array $customerData 
     * 
     * @return string
     */
    public function getCompanyName(array $customerData)
    {
        return $customerData['default_billing_address']['company'];
    }
    
    /**
     * 
     * @param array $customerData 
     * 
     * @return string
     */
    public function getCustomerContact(array $customerData)
    {
        return $customerData['customer']['firstname']
            .' '.$customerData['customer']['lastname'];
    }

    /**
     * 
     * @param array $customerData 
     * 
     * @return string
     */
    protected function getCustomerKey(array $customerData)
    {
        return $customerData['Key'];
    }    
    
    /**
     * 
     * @param array $customerData 
     * 
     * @return string
     */
    protected function getCustomerName(array $customerData)
    {
        return $customerData['customer']['firstname']
            .' '.$customerData['customer']['lastname'];
    }
    
    /**
     * 
     * @param array $customerData 
     * 
     * @return string
     */
    protected function getCustomerEmail(array $customerData)
    {
        return $customerData['customer']['email'];
    }
    
    /**
     * 
     * @param array $customerData 
     * 
     * @return string
     */
    protected function getCustomerStreetFirst(array $customerData)
    {
        return $customerData['default_billing_address']['street'][0];
    }
    
    /**
     * 
     * @param array $customerData 
     * 
     * @return string
     */
    protected function getCustomerStreetSecond(array $customerData)
    {
        return $customerData['default_billing_address']['street'][1];
    }
    
    /**
     * 
     * @param array $customerData 
     * 
     * @return string
     */
    protected function getCustomerCity(array $customerData)
    {
        return $customerData['default_billing_address']['city'];
    }
    
    /**
     * 
     * @param array $customerData 
     * 
     * @return string
     */
    protected function getCustomerRegionCode(array $customerData)
    {
        return $customerData['default_billing_address']['region'];
    }
    
    /**
     * 
     * @param array $customerData 
     * 
     * @return string
     */
    protected function getCustomerPostcode(array $customerData)
    {
        return $customerData['default_billing_address']['postcode'];
    }
    
    /**
     * 
     * @param array $customerData 
     * 
     * @return string
     */
    protected function getCustomerNavId(array $customerData)
    {
        return $customerData['No'];
    }
    
    /**
     * 
     * @param array $customerData 
     * 
     * @return string
     */
    protected function getCustomerPhoneNo(array $customerData)
    {
        return $customerData['default_billing_address']['telephone'];
    }
}

