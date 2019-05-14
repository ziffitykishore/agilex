<?php

namespace Ziffity\AjaxLogin\Block\Form;

use Magento\Customer\Model\AccountManagement;

class Register extends \Magento\Directory\Block\Data
{
    
    /**
     * @var \Magento\Framework\Module\Manager
     */
    protected $moduleManager;
    
    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;
    
    /**
     * @var \Ziffity\AjaxLogin\Helper\Data $blockHelper 
     */
    protected $blockHelper;
    
    /**
     * Constructor
     *
     * @param \Magento\Framework\View\Element\Template\Context                 $context
     * @param \Magento\Directory\Helper\Data                                   $directoryHelper
     * @param \Magento\Framework\Json\EncoderInterface                         $jsonEncoder
     * @param \Magento\Framework\App\Cache\Type\Config                         $configCacheType
     * @param \Magento\Directory\Model\ResourceModel\Region\CollectionFactory  $regionCollectionFactory
     * @param \Magento\Directory\Model\ResourceModel\Country\CollectionFactory $countryCollectionFactory
     * @param \Magento\Framework\Module\Manager                                $moduleManager
     * @param \Magento\Customer\Model\Session                                  $customerSession
     * @param \Ziffity\AjaxLogin\Helper\Data                                   $blockHelper
     * @param array                                                            $data
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Directory\Helper\Data $directoryHelper,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        \Magento\Framework\App\Cache\Type\Config $configCacheType,
        \Magento\Directory\Model\ResourceModel\Region\CollectionFactory $regionCollectionFactory,
        \Magento\Directory\Model\ResourceModel\Country\CollectionFactory $countryCollectionFactory,
        \Magento\Framework\Module\Manager $moduleManager,
        \Magento\Customer\Model\Session $customerSession,
        \Ziffity\AjaxLogin\Helper\Data $blockHelper,
        array $data = []
    ) {
        $this->moduleManager = $moduleManager;
        $this->customerSession = $customerSession;
        $this->blockHelper = $blockHelper;
        parent::__construct(
            $context,
            $directoryHelper,
            $jsonEncoder,
            $configCacheType,
            $regionCollectionFactory,
            $countryCollectionFactory,
            $data
        );
    }

    /**
     * Retrieve the form posting URL
     *
     * @return string
     */
    public function getPostActionUrl()
    {
        return $this->getUrl('ajaxlogin/customer_ajax/register',['_secure' => true]);
    }


    /**
     * Retrieve form data
     *
     * @return mixed
     */
    public function getFormData()
    {
        $data = $this->getData('form_data');
        if ($data === null) {
            $formData = $this->customerSession->getCustomerFormData(true);
            $data = new \Magento\Framework\DataObject();
            if ($formData) {
                $data->addData($formData);
                $data->setCustomerData(1);
            }
            if (isset($data['region_id'])) {
                $data['region_id'] = (int)$data['region_id'];
            }
            $this->setData('form_data', $data);
        }
        return $data;
    }

    /**
     * Newsletter module availability
     *
     * @return bool
     */
    public function isNewsletterEnabled()
    {
        return $this->moduleManager->isOutputEnabled('Magento_Newsletter');
    }

    /**
     * Get minimum password length
     *
     * @return string
     */
    public function getMinimumPasswordLength()
    {
        return $this->_scopeConfig->getValue(
            AccountManagement::XML_PATH_MINIMUM_PASSWORD_LENGTH
        );
    }

    /**
     * Get number of password required character classes
     *
     * @return string
     */
    public function getRequiredCharacterClassesNumber()
    {
        return $this->_scopeConfig->getValue(
            AccountManagement::XML_PATH_REQUIRED_CHARACTER_CLASSES_NUMBER
        );
    }

    /**
     * Returns the helper class
     * 
     * @return Ziffity\AjaxLogin\Helper\Data
     */
    public function getHelper() {
        return $this->blockHelper;
    }
}
