<?php

namespace Ziffity\Pickupdate\Block\Adminhtml\Sales\Order\View;

use Ziffity\Pickupdate\Model\PickupdateFactory;
use Magento\Framework\View\Element\Template\Context;

class Pickupdate extends \Magento\Framework\View\Element\Template
{

    /**
     * @var PickupdateFactory
     */
    protected $pickupDateFactory;
    /**
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry;
    /**
     * @var \Ziffity\Pickupdate\Helper\Data
     */
    protected $pickupHelper;
    /**
     * @var \Ziffity\Pickupdate\Model\ResourceModel\Pickupdate
     */
    protected $pickupdateResourceModel;
    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $date;

    public function __construct(
        Context $context,
        PickupdateFactory $pickupDateFactory,
        \Magento\Framework\Registry $coreRegistry,
        \Ziffity\Pickupdate\Helper\Data $pickupHelper,
        \Ziffity\Pickupdate\Model\ResourceModel\Pickupdate $pickupdateResourceModel,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        array $data = []
    ) {
        $this->pickupDateFactory = $pickupDateFactory;
        $this->coreRegistry = $coreRegistry;
        $this->pickupHelper = $pickupHelper;
        $this->pickupdateResourceModel = $pickupdateResourceModel;
        $this->date = $date;

        parent::__construct($context, $data);
    }

    protected function _construct()
    {
        parent::_construct();

        $this->setTemplate('Ziffity_Pickupdate::pickup_view.phtml');
        $pickupDate = $this->pickupDateFactory->create();
        $this->pickupdateResourceModel->load($pickupDate, $this->_getOrderId(), 'order_id');
        $this->coreRegistry->register('current_pickupdate', $pickupDate);
    }

    protected function _getOrderId()
    {
        if ($this->coreRegistry->registry('current_order')) {
            return $this->coreRegistry->registry('current_order')->getId();
        }
        if ($this->coreRegistry->registry('current_invoice')) {
            return $this->coreRegistry->registry('current_invoice')->getOrderId();
        }
        if ($this->coreRegistry->registry('current_shipment')) {
            return $this->coreRegistry->registry('current_shipment')->getOrderId();
        }
    }

    public function isOrderViewPage() {
        if ($this->coreRegistry->registry('current_order')) {
            return true;
        }
        return false;
    }

    public function getPickupDateFields($place = 'order')
    {
        $fields = array();
        if ($this->coreRegistry->registry('current_pickupdate_place')) {
            $place = $this->coreRegistry->registry('current_pickupdate_place');
        }
        $pickupDate = $this->coreRegistry->registry('current_pickupdate');
        $show = $this->pickupHelper->whatShow($place . '_view');
        foreach ($show as $key) {
            switch ($key) {
                case 'date':
                    if (!$pickupDate['date']
                        || '0000-00-00' == $pickupDate['date']
                        || '1970-01-01' == $pickupDate['date']) {
                        $date = '';
                    } else {
                        $date = $this->date->date(
                            $this->pickupHelper->getPhpFormat(),
                            $pickupDate['date']
                        );
                    }
                    $fields[] = array('code' => 'date',
                        'label' => __('Pickup Date'),
                        'value' => $date
                    );
                    break;
                case 'time':
                    $fields[] = array('code' => 'time',
                        'label' => __('Pickup Time Interval'),
                        'value' => $pickupDate['time']);
                    break;
                case 'comment':
                    $fields[] = array('code' => 'comment',
                        'label' => __('Pickup Comments'),
                        'value' => $pickupDate['comment']);
                    break;
            }
        }
        return $fields;
    }

    public function getEditUrl() {
        return $this->getUrl('ziffity_pickupdate/pickupdate/edit', ['order_id' => $this->_getOrderId()]);
    }

}