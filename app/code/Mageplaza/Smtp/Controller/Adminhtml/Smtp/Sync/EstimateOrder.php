<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_Smtp
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\Smtp\Controller\Adminhtml\Smtp\Sync;

use Magento\Backend\App\Action\Context;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory as OrderCollectionFactory;
use Mageplaza\Smtp\Helper\EmailMarketing;

/**
 * Class EstimateOrder
 * @package Mageplaza\Smtp\Controller\Adminhtml\Smtp\Sync
 */
class EstimateOrder extends AbstractEstimate
{
    /**
     * @var OrderCollectionFactory
     */
    protected $orderCollectionFactory;

    /**
     * EstimateOrder constructor.
     *
     * @param Context $context
     * @param OrderCollectionFactory $orderCollectionFactory
     * @param EmailMarketing $emailMarketing
     */
    public function __construct(
        Context $context,
        OrderCollectionFactory $orderCollectionFactory,
        EmailMarketing $emailMarketing
    ) {
        $this->orderCollectionFactory = $orderCollectionFactory;
        $this->emailMarketing = $emailMarketing;

        parent::__construct($context, $emailMarketing);
    }

    /**
     * @return \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection|\Magento\Sales\Model\ResourceModel\Order\Collection
     */
    public function prepareCollection()
    {
        return $this->orderCollectionFactory->create();
    }

    /**
     * @return \Magento\Framework\Phrase
     */
    public function getZeroMessage()
    {
        return __('No Orders to synchronize.');
    }
}
