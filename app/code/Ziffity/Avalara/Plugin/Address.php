<?php

namespace Ziffity\Avalara\Plugin;

use ClassyLlama\AvaTax\Framework\Interaction\MetaData\MetaDataObject;
use ClassyLlama\AvaTax\Framework\Interaction\MetaData\MetaDataObjectFactory;
use AvaTax\AddressFactory;
use Magento\Directory\Model\Region;
use Magento\Directory\Model\ResourceModel\Region\Collection as RegionCollection;
use Magento\Directory\Model\ResourceModel\Region\CollectionFactory as RegionCollectionFactory;

class Address
{
 
    /**
     * @var AddressFactory
     */
    protected $addressFactory = null;    
    
    /**
     * @var MetaDataObject
     */
    protected $metaDataObject = null;
    
    /**
     * @var RegionCollection
     */
    protected $regionCollection = null;    
    
    /**
     * Validation based on API documentation found here:
     * http://developer.avalara.com/wp-content/apireference/master/?php#validate-request58
     *
     * @var array
     */
    public static $validFields = [
        /*
         * The AvaTax API defines Line1 as required, however in implementation it is not required. We can't require
         * it here, as we need to be able to calculate taxes from the cart page using Postal Code, Region, and Country.
         */
        'Line1' => ['type' => 'string', 'length' => 50],
        'Line2' => ['type' => 'string', 'length' => 50],
        'Line3' => ['type' => 'string', 'length' => 50],
        'City' => ['type' => 'string', 'length' => 50], // Either city & region are required or postalCode is required.
        'Region' => ['type' => 'string', 'length' => 3], // Making postalCode required is easier but could be modified,
        'PostalCode' => ['type' => 'string', 'required' => true, 'length' => 11], // if necessary.
        'Country' => ['type' => 'string', 'length' => 2],
        'TaxRegionId' => ['type' => 'integer', 'useInCacheKey' => false],
        'Latitude' => ['type' => 'string', 'useInCacheKey' => false],
        'Longitude' => ['type' => 'string', 'useInCacheKey' => false],
    ];    
    
    public function __construct(
        MetaDataObjectFactory $metaDataObjectFactory,
        RegionCollectionFactory $regionCollectionFactory,
        AddressFactory $addressFactory
    ) {
        $this->metaDataObject = $metaDataObjectFactory->create(['metaDataProperties' => $this::$validFields]);
        $this->regionCollection = $regionCollectionFactory->create();
        $this->addressFactory = $addressFactory;
    }    
    
    public function aroundGetAddress(
        \ClassyLlama\AvaTax\Framework\Interaction\Address $subject,
        \Closure $proceed,
        $data
    ) {
        switch (true) {
            case ($data instanceof \Magento\Customer\Api\Data\AddressInterface):
                $data = $subject->convertCustomerAddressToAvaTaxAddress($data);
                break;
            case ($data instanceof \Magento\Quote\Api\Data\AddressInterface):
                $data = $subject->convertQuoteAddressToAvaTaxAddress($data);
                break;
            case ($data instanceof \Magento\Sales\Api\Data\OrderAddressInterface):
                $data = $subject->convertOrderAddressToAvaTaxAddress($data);
                break;
            case (is_array($data)):
                $data = $this->convertOriginAddressToAvaTaxAddress($data);
                break;            
            case (!is_array($data)):
                throw new LocalizedException(__(
                    'Input parameter "$data" was not of a recognized/valid type: "%1".', [
                        gettype($data),
                ]));
        }

        if (isset($data['RegionId'])) {
            $data['Region'] = $subject->getRegionCodeById($data['RegionId']);
            unset($data['RegionId']);
        }

        try {
            $data = $this->metaDataObject->validateData($data);
        } catch (MetaData\ValidationException $e) {
            $subject->avaTaxLogger->error('Error validating address: ' . $e->getMessage(), [
                'data' => var_export($data, true)
            ]);
            // Rethrow exception as if internal validation fails, don't send address to AvaTax
            throw $e;
        }

        $address = $this->addressFactory->create();
        return $this->populateAddress($data, $address);        
    }
    
    public function convertOriginAddressToAvaTaxAddress(array $address)
    {
        return [
            'Line1' => $address['Line1'],
            'Line2' => $address['Line2'],
            'Line3' => isset($address['Line3']) ? $address['Line3'] : '',
            'City' => $address['City'],
            'Region' => $this->getRegionCodeById($address['RegionId']),
            'PostalCode' => $address['PostalCode'],
            'Country' => $address['Country'],
        ];
    }

    /**
     * Map data array to methods in GetTaxRequest object
     *
     * @param array $data
     * @param \AvaTax\Address $address
     * @return \AvaTax\Address
     */
    protected function populateAddress(array $data, \AvaTax\Address $address)
    {
        // Set any data elements that exist on the getTaxRequest
        foreach ($data as $key => $datum) {
            $methodName = 'set' . $key;
            if (method_exists($address, $methodName)) {
                $address->$methodName($datum);
            }
        }
        return $address;
    }   
    
    /**
     * Return region code by id
     *
     * @param $regionId
     * @return string|null
     * @throws LocalizedException
     */
    protected function getRegionCodeById($regionId)
    {
        if (!$regionId) {
            return null;
        }

        /** @var \Magento\Directory\Model\Region $region */
        $region = $this->regionCollection->getItemById($regionId);

        if (!($region instanceof Region)) {
            throw new LocalizedException(__(
                'Region "%1" was not found.', [
                $regionId,
            ]));
        }

        return $region->getCode();
    }    
}
