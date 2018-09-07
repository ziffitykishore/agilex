<?php

/**
 * Product:       Xtento_SavedCc (1.0.6)
 * ID:            NZWbKguR/Yb8QYk68QaZWfj7V5pl/BlDdubJ/+3MKvg=
 * Packaged:      2018-08-15T13:47:06+00:00
 * Last Modified: 2017-08-16T16:58:54+00:00
 * File:          app/code/Xtento/SavedCc/Controller/Adminhtml/Payment/Wipe.php
 * Copyright:     Copyright (c) 2018 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

namespace Xtento\SavedCc\Controller\Adminhtml\Payment;

use Magento\Backend\App\Action;
use Magento\Framework\Exception\LocalizedException;
use Xtento\SavedCc\Helper\Module;

class Wipe extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\Sales\Api\OrderRepositoryInterface
     */
    protected $orderRepository;

    /**
     * @var Module
     */
    protected $moduleHelper;

    /**
     * Wipe constructor.
     *
     * @param Action\Context $context
     * @param \Magento\Sales\Api\OrderRepositoryInterface $orderRepository
     */
    public function __construct(
        Action\Context $context,
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository,
        Module $moduleHelper
    ) {
        parent::__construct($context);
        $this->orderRepository = $orderRepository;
        $this->moduleHelper = $moduleHelper;
    }

    /**
     * Mass delete action
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        try {
            $order = $this->orderRepository->get($this->getRequest()->getParam('order_id', false));
            if (!$order->getId()) {
                throw new LocalizedException(__('Order couldn\'t be loaded.'));
            }
            $this->moduleHelper->wipeCreditCardInfo($order);
            $this->messageManager->addSuccessMessage(
                __('Credit card information has been wiped from database.')
            );
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        }

        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(\Magento\Framework\Controller\ResultFactory::TYPE_REDIRECT);
        $resultRedirect->setRefererUrl();
        return $resultRedirect;
    }

    /**
     * Check if user has enough privileges
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Xtento_SavedCc::wipeCcInfo');
    }
}
