<?php

namespace SomethingDigital\MigrationProject\Migration\Data;

use Magento\Framework\Setup\SetupInterface;
use SomethingDigital\Migration\Api\MigrationInterface;
use SomethingDigital\Migration\Helper\Cms\Page as PageHelper;
use SomethingDigital\Migration\Helper\Cms\Block as BlockHelper;
use SomethingDigital\Migration\Helper\Email\Template as EmailHelper;
use Magento\Config\Model\ResourceModel\Config as ResourceConfig;
use Magento\Customer\Model\CustomerFactory;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\Encryption\Encryptor;
use Magento\Customer\Model\AddressFactory;
use Magento\Framework\App\State;

class M20190312144328CreateTestAddresses implements MigrationInterface
{
    protected $page;
    protected $block;
    protected $email;
    protected $resourceConfig;
    protected $customerFactory;
    protected $customerRepoInterface;
    protected $encryptor;
    protected $addressFactory;
    protected $state;

    public function __construct(
        PageHelper $page, 
        BlockHelper $block, 
        EmailHelper $email, 
        ResourceConfig $resourceConfig,
        CustomerFactory $customerFactory,
        CustomerRepositoryInterface $customerRepoInterface,
        Encryptor $encryptor,
        AddressFactory $addressFactory,
        State $state
    ) {
        $this->page = $page;
        $this->block = $block;
        $this->email = $email;
        $this->resourceConfig = $resourceConfig;
        $this->customerFactory = $customerFactory;
        $this->customerRepoInterface = $customerRepoInterface;
        $this->encryptor = $encryptor;
        $this->addressFactory = $addressFactory;
        $this->state = $state;
    }

    public function execute(SetupInterface $setup)
    {
        try {
            $this->state->setAreaCode(\Magento\Framework\App\Area::AREA_ADMINHTML);
        } catch (\Exception $e) {
            //do nothing. area code already set
        }
        
        //addresses are randomly generated
        $addresses = [
            [
                'firstname' => 'Firstname1',
                'lastname' => 'Lastname1',
                'street' => '537 Deer Haven Drive',
                'city' => 'Greenville',
                'region_id' => 'SC',
                'postcode' => '29601',
                'phone' => '812-610-3138'
            ],
            [
                'firstname' => 'Firstname2',
                'lastname' => 'Lastname2',
                'street' => '391 Neville Street',
                'city' => 'Vincennes',
                'region_id' => 'IN',
                'postcode' => '47591',
                'phone' => '812-396-7533'
            ],
            [
                'firstname' => 'Firstname3',
                'lastname' => 'Lastname3',
                'street' => '4169 Forest Avenue',
                'city' => 'New York',
                'region_id' => 'NY',
                'postcode' => '10004',
                'phone' => '646-982-1149'
            ],
            [
                'firstname' => 'Firstname4',
                'lastname' => 'Lastname4',
                'street' => '2636 Ben Street',
                'city' => 'Albany',
                'region_id' => 'NY',
                'postcode' => '12207',
                'phone' => '518-207-0484'
            ],
            [
                'firstname' => 'Firstname5',
                'lastname' => 'Lastname5',
                'street' => '4718 Harter Street',
                'city' => 'Mansfield',
                'region_id' => 'OH',
                'postcode' => '44907',
                'phone' => '419-566-5194'
            ],
            [
                'firstname' => 'Firstname6',
                'lastname' => 'Lastname6',
                'street' => '1838 Thompson Street',
                'city' => 'Los Angeles',
                'region_id' => 'CA',
                'postcode' => '90017',
                'phone' => '213-494-4537'
            ],
            [
                'firstname' => 'Firstname7',
                'lastname' => 'Lastname7',
                'street' => '3440 Ersel Street',
                'city' => 'Dallas',
                'region_id' => 'TX',
                'postcode' => '75217',
                'phone' => '214-391-0648'
            ],
            [
                'firstname' => 'Firstname8',
                'lastname' => 'Lastname8',
                'street' => '4877 Trouser Leg Road',
                'city' => 'Springfield',
                'region_id' => 'MA',
                'postcode' => '01103',
                'phone' => '413-297-9709'
            ],
            [
                'firstname' => 'Firstname9',
                'lastname' => 'Lastname9',
                'street' => '3509 Jett Lane',
                'city' => 'Los Angeles',
                'region_id' => 'CA',
                'postcode' => '90017',
                'phone' => '310-742-2135'
            ],
            [
                'firstname' => 'Firstname10',
                'lastname' => 'Lastname10',
                'street' => '236 Todds Lane',
                'city' => 'San Antonio',
                'region_id' => 'TX',
                'postcode' => '78205',
                'phone' => '210-507-4559'
            ],
            [
                'firstname' => 'Firstname11',
                'lastname' => 'Lastname11',
                'street' => '1383 Pooz Street',
                'city' => 'Newark',
                'region_id' => 'NJ',
                'postcode' => '07102',
                'phone' => '732-218-7680'
            ],
            [
                'firstname' => 'Firstname12',
                'lastname' => 'Lastname12',
                'street' => '2790 Taylor Street',
                'city' => 'New York',
                'region_id' => 'NY',
                'postcode' => '10004',
                'phone' => '646-434-0485'
            ],
            [
                'firstname' => 'Firstname13',
                'lastname' => 'Lastname13',
                'street' => '122 Geneva Street',
                'city' => 'Mineola',
                'region_id' => 'NY',
                'postcode' => '11501',
                'phone' => '917-509-3002'
            ],
            [
                'firstname' => 'Firstname14',
                'lastname' => 'Lastname14',
                'street' => '2442 Thompson Drive',
                'city' => 'San Jose',
                'region_id' => 'CA',
                'postcode' => '95131',
                'phone' => '408-932-2291'
            ],
            [
                'firstname' => 'Firstname15',
                'lastname' => 'Lastname15',
                'street' => '3984 Masonic Drive',
                'city' => 'South Malta',
                'region_id' => 'MT',
                'postcode' => '59538',
                'phone' => '406-658-1312'
            ],
            [
                'firstname' => 'Firstname16',
                'lastname' => 'Lastname16',
                'street' => '1975 Fannie Street',
                'city' => 'Freeport',
                'region_id' => 'TX',
                'postcode' => '77541',
                'phone' => '979-237-9009'
            ],
            [
                'firstname' => 'Firstname17',
                'lastname' => 'Lastname17',
                'street' => '4016 Carolyns Circle',
                'city' => 'Dallas',
                'region_id' => 'TX',
                'postcode' => '75234',
                'phone' => '214-682-8391'
            ],
            [
                'firstname' => 'Firstname18',
                'lastname' => 'Lastname18',
                'street' => '3336 Stroop Hill Road',
                'city' => 'Atlanta',
                'region_id' => 'GA',
                'postcode' => '30303',
                'phone' => '404-818-5909'
            ],
            [
                'firstname' => 'Firstname19',
                'lastname' => 'Lastname19',
                'street' => '1846 Karen Lane',
                'city' => 'Rose Terrace',
                'region_id' => 'KY',
                'postcode' => '40177',
                'phone' => '502-943-2292'
            ],
            [
                'firstname' => 'Firstname20',
                'lastname' => 'Lastname20',
                'street' => '1804 Alfred Drive',
                'city' => 'Brooklyn',
                'region_id' => 'NY',
                'postcode' => '11230',
                'phone' => '718-252-9111'
            ],
        ];

        $customer = $this->customerFactory->create();
        $customer->setWebsiteId(1);
        $customer->setEmail("asobkowski@somethingdigital.com");
        $customer->setFirstname('Customer');
        $customer->setLastname('QA');
        $customer->save();

        $customer = $this->customerRepoInterface->getById($customer->getId());
        $this->customerRepoInterface->save($customer, $this->encryptor->getHash('5shp2A2F', true));

        foreach ($addresses as $key => $address) {
            $address = $this->addressFactory->create()
                ->setCustomerId($customer->getId())
                ->setFirstname($address['firstname'])
                ->setLastname($address['lastname'])
                ->setCountryId('US')
                ->setPostcode($address['postcode'])
                ->setCity($address['city'])
                ->setRegionId($address['region_id'])
                ->setTelephone($address['phone'])
                ->setStreet($address['street'])
                ->setSaveInAddressBook('1')
                ->setIsDefaultBilling('1')
                ->setIsDefaultShipping('1')
                ->save();
        }
    }
}
