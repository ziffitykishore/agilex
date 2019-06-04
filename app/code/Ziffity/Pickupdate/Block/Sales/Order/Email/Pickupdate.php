<?php

namespace Ziffity\Pickupdate\Block\Sales\Order\Email;

use Magento\Framework\View\Element\Template\Context;
use Ziffity\Pickupdate\Model\PickupdateFactory;

class Pickupdate extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Ziffity\Pickupdate\Model\ResourceModel\Pickupdate
     */
    protected $pickupdateResourceModel;

    /**
     * @var PickupdateFactory
     */
    protected $pickupDateFactory;

    /**
     * @var \Ziffity\Pickupdate\Helper\Data
     */
    protected $pickupHelper;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $date;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry;

    /**
     * @var \Magento\Sales\Model\OrderRepository
     */
    protected $orderRepository;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    public function __construct(
        Context $context,
        \Ziffity\Pickupdate\Model\ResourceModel\Pickupdate $pickupdateResourceModel,
        PickupdateFactory $pickupDateFactory,
        \Ziffity\Pickupdate\Helper\Data $pickupHelper,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Sales\Model\OrderRepository $orderRepository,
        \Magento\Customer\Model\Session $customerSession,
        array $data = []
    ) {
        parent::__construct($context, $data);

        $this->pickupdateResourceModel = $pickupdateResourceModel;
        $this->pickupDateFactory = $pickupDateFactory;
        $this->pickupHelper = $pickupHelper;
        $this->date = $date;
        $this->coreRegistry = $coreRegistry;
        $this->orderRepository = $orderRepository;
        $this->customerSession = $customerSession;
    }

    protected function _construct()
    {
        parent::_construct();

        $this->setTemplate('Ziffity_Pickupdate::email.phtml');
    }

    /**
     * @return \Ziffity\Pickupdate\Model\Pickupdate
     */
    public function getPickupDate()
    {
        if ($this->getData('pickup_date') === null) {
            $orderId = $this->getData('order_id');
            /** @var \Ziffity\Pickupdate\Model\Pickupdate $pickupDate */
            $pickupDate = $this->pickupDateFactory->create();
            $this->pickupdateResourceModel->load($pickupDate, $orderId, 'order_id');

            $this->setData('pickup_date', $pickupDate);
        }

        return $this->getData('pickup_date');
    }

    /**
     * @return \Magento\Sales\Api\Data\OrderInterface
     */
    public function getOrder()
    {
        return $this->orderRepository->get($this->getOrderId());
    }

    public function getFields()
    {
        $fields = $this->getData('fields');
        return $fields;
    }

    /**
     * @return int
     */
    public function getOrderId()
    {
        return $this->getData('order_id');
    }

    /**
     * Before rendering html, but after trying to load cache.
     * Prepare variables for output
     *
     * @return $this
     *
     */
    protected function _beforeToHtml()
    {
        $fields = $this->getFields();
        if (is_array($fields) && !empty($fields)) {
            $pickupDate = $this->getPickupDate();
            $label = '';
            $list = [];
            foreach ($fields as $field) {
                $value = $pickupDate->getData($field);
                if (!$value) {
                    continue;
                }

                switch ($field) {
                    case 'date':
                        $label = __('Pickup Date') . ':';
                        $value = $pickupDate->getFormattedDate();
                        break;
                    case 'time':
                        $label = __('Pickup Time Interval') . ':';
                        break;
                    case 'comment':
                        $label = __('Pickup Comments') . ':';
                        $value = $pickupDate->getFormattedComment();
                        break;
                }

                $list[$field] = ['label' => $label, 'value' => $value];
            }
            $this->assign('list', $list);
        }

        return parent::_beforeToHtml();
    }
}
