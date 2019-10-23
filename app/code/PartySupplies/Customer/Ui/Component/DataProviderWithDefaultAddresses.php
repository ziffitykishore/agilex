<?php

namespace PartySupplies\Customer\Ui\Component;

use Magento\Customer\Model\Address;
use Magento\Customer\Model\Customer;
use Magento\Customer\Model\ResourceModel\Customer\CollectionFactory as CustomerCollectionFactory;
use Magento\Eav\Model\Config;
use Magento\Eav\Model\Entity\Attribute\AbstractAttribute;
use Magento\Eav\Model\Entity\Type;
use Magento\Framework\Session\SessionManagerInterface;
use Magento\Customer\Model\FileUploaderDataResolver;
use Magento\Customer\Model\AttributeMetadataResolver;
use Magento\Framework\App\Response\RedirectInterface;

/**
 * Refactored version of Magento\Customer\Model\Customer\DataProvider with eliminated usage of addresses collection.
 */
class DataProviderWithDefaultAddresses extends \Magento\Ui\DataProvider\AbstractDataProvider
{
    /**
     * @var array
     */
    private $loadedData = [];

    /**
     * @var SessionManagerInterface
     */
    private $session;

    /**
     * Customer fields that must be removed
     *
     * @var array
     */
    private static $forbiddenCustomerFields = [
        'password_hash',
        'rp_token',
        'confirmation',
    ];

    /**
     * Allow to manage attributes, even they are hidden on storefront
     *
     * @var bool
     */
    private $allowToShowHiddenAttributes;

    /**
     * @var \Magento\Directory\Model\CountryFactory
     */
    private $countryFactory;

    /**
     * @var FileUploaderDataResolver
     */
    private $fileUploaderDataResolver;

    /**
     * @var AttributeMetadataResolver
     */
    private $attributeMetadataResolver;

    /**
     * @var RedirectInterface
     */
    protected $redirect;

    /**
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param CustomerCollectionFactory $customerCollectionFactory
     * @param Config $eavConfig
     * @param \Magento\Directory\Model\CountryFactory $countryFactory
     * @param SessionManagerInterface $session
     * @param FileUploaderDataResolver $fileUploaderDataResolver
     * @param AttributeMetadataResolver $attributeMetadataResolver
     * @param bool $allowToShowHiddenAttributes
     * @param array $meta
     * @param array $data
     * @throws \Magento\Framework\Exception\LocalizedException
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        string $name,
        string $primaryFieldName,
        string $requestFieldName,
        CustomerCollectionFactory $customerCollectionFactory,
        Config $eavConfig,
        \Magento\Directory\Model\CountryFactory $countryFactory,
        SessionManagerInterface $session,
        FileUploaderDataResolver $fileUploaderDataResolver,
        AttributeMetadataResolver $attributeMetadataResolver,
        RedirectInterface $redirect,
        $allowToShowHiddenAttributes = true,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->collection = $customerCollectionFactory->create();
        $this->collection->addAttributeToSelect('*');
        $this->allowToShowHiddenAttributes = $allowToShowHiddenAttributes;
        $this->session = $session;
        $this->countryFactory = $countryFactory;
        $this->fileUploaderDataResolver = $fileUploaderDataResolver;
        $this->attributeMetadataResolver = $attributeMetadataResolver;
        $this->redirect = $redirect;
        $this->meta['customer']['children'] = $this->getAttributesMeta(
            $eavConfig->getEntityType('customer')
        );
    }

    /**
     * Get data
     *
     * @return array
     */
    public function getData(): array
    {
        if (!empty($this->loadedData)) {
            return $this->loadedData;
        }
        $items = $this->collection->getItems();
        /** @var Customer $customer */
        foreach ($items as $customer) {
            $result['customer'] = $customer->getData();

            $this->fileUploaderDataResolver->overrideFileUploaderData($customer, $result['customer']);

            $result['customer'] = array_diff_key(
                $result['customer'],
                array_flip(self::$forbiddenCustomerFields)
            );
            unset($result['address']);

            $result['default_billing_address'] = $this->prepareDefaultAddress(
                $customer->getDefaultBillingAddress()
            );
            $result['default_shipping_address'] = $this->prepareDefaultAddress(
                $customer->getDefaultShippingAddress()
            );
            $result['customer_id'] = $customer->getId();

            $this->loadedData[$customer->getId()] = $result;
        }

        $data = $this->session->getCustomerFormData();
        if (!empty($data)) {
            $customerId = $data['customer']['entity_id'] ?? null;
            $this->loadedData[$customerId] = $data;
            $this->session->unsCustomerFormData();
        }

        return $this->loadedData;
    }

    /**
     * Prepare default address data.
     *
     * @param Address|false $address
     * @return array
     */
    private function prepareDefaultAddress($address): array
    {
        $addressData = [];

        if (!empty($address)) {
            $addressData = $address->getData();
            if (isset($addressData['street']) && !\is_array($address['street'])) {
                $addressData['street'] = explode("\n", $addressData['street']);
            }
            $addressData['country'] = $this->countryFactory->create()
                ->loadByCode($addressData['country_id'])->getName();
        }

        return $addressData;
    }

    /**
     * Get attributes meta
     *
     * @param Type $entityType
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function getAttributesMeta(Type $entityType): array
    {
        $meta = [];
        $attributes = $entityType->getAttributeCollection();
        /* @var AbstractAttribute $attribute */
        foreach ($attributes as $attribute) {
            $meta[$attribute->getAttributeCode()] = $this->attributeMetadataResolver->getAttributesMeta(
                $attribute,
                $entityType,
                $this->allowToShowHiddenAttributes,
                $this->getRequestFieldName()
            );
        }
        $this->attributeMetadataResolver->processWebsiteMeta($meta);

        return $meta;
    }

    /**
     * Get meta data for customer admin form
     *
     * @return array
     */
    public function getMeta()
    {
        $meta = parent::getMeta();

        if(strpos($this->redirect->getRefererUrl(), 'account_type/company')) {
            $meta['customer']['children']['pay_on_account_approval']['arguments']['data']['config']['visible'] = 0;
        }

        if (strpos($this->redirect->getRefererUrl(), 'account_type/customer')) {
            $meta['customer']['children']['reseller_certificate']['arguments']['data']['config']['visible'] = 0;
            $meta['customer']['children']['is_certificate_approved']['arguments']['data']['config']['visible'] = 0;
        }
        return $meta;
    }
}
