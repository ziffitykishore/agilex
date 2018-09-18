<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Ziffity\Core\Controller\Adminhtml\Order;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Magento\Backend\App\Action\Context;
use Magento\Ui\Component\MassAction\Filter;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory;
use Magento\Sales\Api\OrderManagementInterface;
use Magento\Sales\Model\Order;

/**
 * Class MassHold
 */
class MassStatus extends \Magento\Sales\Controller\Adminhtml\Order\AbstractMassAction
{
    /**
     * Authorization level of a basic admin session
     */
    const ADMIN_RESOURCE = 'Magento_Sales::status1';

    /**
     * @var OrderManagementInterface
     */
    protected $orderManagement;
    public $order;
    public $statusCollectionFactory;
    public $orderstatus;

    /**
     * @param Context $context
     * @param Filter $filter
     * @param CollectionFactory $collectionFactory
     * @param OrderManagementInterface $orderManagement
     */
    public function __construct(
        Context $context,
        Filter $filter,
        CollectionFactory $collectionFactory,
        OrderManagementInterface $orderManagement,
        \Magento\Sales\Model\Order $order,
        \Magento\Sales\Model\ResourceModel\Order\Status\CollectionFactory $statusCollectionFactory
    ) {
        parent::__construct($context, $filter);
        $this->collectionFactory = $collectionFactory;
        $this->orderManagement = $orderManagement;
        $this->order = $order;
        $this->statusCollectionFactory = $statusCollectionFactory;
    }

    /**
     * Hold selected orders
     *
     * @param AbstractCollection $collection
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    protected function massAction(AbstractCollection $collection)
    {
        $param      = $this->getRequest()->getParams();
        $statusCode = $param['status_code'];
        $stateCode  = $this->getStateByStatus($statusCode);
        $selected   = $param['selected'];
        $orderCount = 0;
        foreach($selected as $select) {
            $this->statusChanger($select, $stateCode, $statusCode);
            $orderCount++;
        }
        $this->messageManager->addSuccess(__('Total of %1 order(s) have been successfully updated.', $orderCount));
        $resultRedirect = $this->resultRedirectFactory->create();
        $resultRedirect->setPath($this->getComponentRefererUrl());
        return $resultRedirect;
    }
    public function statusChanger($id, $state, $status) {
        $this->order->load($id)
            ->setState($state)
            ->setStatus($status)
            ->save();
    }
    /**
     * Get order state by it's status
     * 
     * @param string $status
     * @return string
     */
    public function getStateByStatus($status)
    {
        $collection = $this->statusCollectionFactory->create()->joinStates();
        if( empty($this->orderstatus) ) {
            foreach($collection->getData() as $statusst) {
                $this->orderstatus[$statusst['status']] = $statusst['state'];
            }   
        }
        return $this->orderstatus[$status] ? $this->orderstatus[$status] : '';
    }
}
