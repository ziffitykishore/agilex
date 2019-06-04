<?php

namespace Ziffity\Pickupdate\Block\Pickupdate;

/**
 * Pickup Date Edit
 */
class Edit extends \Magento\Framework\View\Element\Template
{
    /**
     * @var string
     */
    protected $_template = 'edit.phtml';

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry = null;

    /**
     * @var \Magento\Framework\Json\EncoderInterface
     */
    private $jsonEncoder;

    /**
     * @var \Ziffity\Pickupdate\Helper\Data
     */
    private $pickupHelper;

    /**
     * @var \Magento\Customer\Model\Session
     */
    private $customerSession;
    /**
     * @var \Ziffity\Pickupdate\Model\PickupdateConfigProvider
     */
    private $configProvider;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * Edit constructor.
     *
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\Registry                      $registry
     * @param \Magento\Framework\Json\EncoderInterface         $jsonEncoder
     * @param \Ziffity\Pickupdate\Helper\Data                  $pickupHelper
     * @param \Magento\Customer\Model\Session                  $customerSession
     * @param array                                            $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        \Ziffity\Pickupdate\Helper\Data $pickupHelper,
        \Magento\Customer\Model\Session $customerSession,
        \Ziffity\Pickupdate\Model\PickupdateConfigProvider $configProvider,
        array $data = []
    ) {
        $this->coreRegistry = $registry;
        parent::__construct($context, $data);
        $this->_isScopePrivate = true;
        $this->jsonEncoder = $jsonEncoder;
        $this->pickupHelper = $pickupHelper;
        $this->customerSession = $customerSession;
        $this->configProvider = $configProvider;
        $this->scopeConfig = $context->getScopeConfig();
    }

    /**
     * @return void
     */
    protected function _prepareLayout()
    {
        $this->pageConfig->getTitle()->set(__('Order # %1', $this->getOrder()->getRealOrderId()));
    }

    /**
     * @return string json
     */
    public function getCalendarJsonConfig()
    {
        return $this->jsonEncoder->encode([
            'minDate' => $this->pickupHelper->getMinDays(),
            'maxDate' => $this->pickupHelper->getStoreScopeValue('general/max_days'),
            'dateFormat' => $this->getCalendarDateFormat(),
        ]);
    }

    /**
     * @return string
     */
    protected function getCalendarDateFormat()
    {
        $format = $this->pickupHelper->getStoreScopeValue('date_field/format');
        return preg_replace(['/D/s','/M/s'], ['d','m'], $format);

    }

    /**
     * @return string json
     */
    public function getZiffityCalendarJsonConfig()
    {
        return $this->jsonEncoder->encode([
            'pickupconf' => $this->configProvider->getPickupDateFieldConfig()
        ]);
    }

    /**
     * Retrieve current order model instance
     *
     * @return \Magento\Sales\Model\Order
     */
    public function getOrder()
    {
        return $this->coreRegistry->registry('current_order');
    }

    /**
     * Retrieve current DD model instance
     *
     * @return \Ziffity\Pickupdate\Model\Pickupdate
     */
    public function getPickupDate()
    {
        return $this->coreRegistry->registry('current_ziffity_pickupdate');
    }

    /**
     * Return url for form
     *
     * @return string
     */
    public function getSaveUrl()
    {
        if ($this->customerSession->isLoggedIn()) {
            return $this->getUrl('ziffity_pickupdate/pickupdate/save', ['order_id' => $this->getOrder()->getId()]);
        }
        return $this->getUrl('ziffity_pickupdate/guest/save', ['order_id' => $this->getOrder()->getId()]);
    }

    /**
     * Return back url for logged in and guest users
     *
     * @return string
     */
    public function getBackUrl()
    {
        if ($this->customerSession->isLoggedIn()) {
            return $this->getUrl('sales/order/view', ['order_id' => $this->getOrder()->getId()]);
        }
        return $this->getUrl('sales/guest/view');
    }

    /**
     * Return back title for logged in and guest users
     *
     * @return \Magento\Framework\Phrase
     */
    public function getBackTitle()
    {
        return __('Back to Order # %1', $this->getOrder()->getRealOrderId());
    }

    /**
     * @param int $currentStore
     *
     * @return array
     */
    public function getTIntervals($currentStore = 0)
    {
        $tIntervals = $this->pickupHelper->getTIntervals($currentStore);

        return $tIntervals;
    }

    /**
     * @return boolean
     */
    public function isEnabledTIntervals()
    {
        $isEnabled = false;
        $availableFields = $this->pickupHelper->whatShow('order_create');
        if ($this->scopeConfig->getValue('pickupdate/time_field/enabled_time')
            && in_array('time', $availableFields)) {
            $isEnabled = true;
        }

        return $isEnabled;
    }

    /**
     * @return boolean
     */
    public function isRequiredTimeInterval()
    {
        return $this->pickupHelper->getStoreScopeValue('time_field/required');
    }
}
