<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace SomethingDigital\Order\Controller\Adminhtml\Order;

use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\Result\RedirectFactory;
use Magento\Sales\Api\OrderRepositoryInterface;
use SomethingDigital\Order\Model\OrderPlaceApi;
use Magento\Framework\App\RequestInterface;

class Index extends \Magento\Backend\App\Action
{

    protected $resultRedirectFactory;
    protected $orderRepository;
    protected $orderPlaceApi;
    protected $request;
 
    public function __construct(
        Context $context,
        RedirectFactory $resultRedirectFactory,
        OrderRepositoryInterface $orderRepository,
        OrderPlaceApi $orderPlaceApi,
        RequestInterface $request
    ) {
        parent::__construct($context);
        $this->resultRedirectFactory = $resultRedirectFactory;
        $this->orderRepository = $orderRepository;
        $this->orderPlaceApi = $orderPlaceApi;
        $this->request = $request;
    }

    /**
     * Send Order to API
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        $orderId = $this->request->getParam('order_id');

        $resultRedirect = $this->resultRedirectFactory->create();

        $order = $this->orderRepository->get($orderId);

        $resultRedirect->setPath('sales/order/view', ['order_id' => $order->getId()]);

        if ($order->getSxIntegrationStatus() == 'processing') {
            $this->messageManager->addErrorMessage(__('Order was already sent to API.'));
            return $resultRedirect;
        }
        if ($order) {
            try {
                $response = $this->orderPlaceApi->sendOrder($order);
                if ($response['status']) {
                    $this->messageManager->addSuccessMessage(__('Order has been sent to API.'));
                } else {
                    $this->messageManager->addErrorMessage(__('Order has not been sent to API.'));
                    if (isset($response['error'])) {
                       $this->messageManager->addErrorMessage($response['error']);
                    }
                }
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            }
            return $resultRedirect;
        }
        $resultRedirect->setPath('sales/*/');
        return $resultRedirect;
    }
}
