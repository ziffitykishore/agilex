<?php
/**
 * Copyright © 2018 Vantiv, LLC. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Vantiv\Payment\Observer;

use Magento\Framework\Event\ObserverInterface;

class ProductSaveAfterActionObserver implements ObserverInterface
{
    /**
     * @var \Vantiv\Payment\Model\ResourceModel\Recurring\Plan\CollectionFactory
     */
    private $plansCollectionFactory;

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    private $messageManager;

    /**
     * @param \Vantiv\Payment\Model\ResourceModel\Recurring\Plan\CollectionFactory $plansCollection
     */
    public function __construct(
        \Vantiv\Payment\Model\ResourceModel\Recurring\Plan\CollectionFactory $plansCollectionFactory,
        \Magento\Framework\Message\ManagerInterface $messageManager
    ) {
        $this->plansCollectionFactory = $plansCollectionFactory;
        $this->messageManager = $messageManager;
    }

    /**
     * Save plans data
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return \Vantiv\Payment\Observer\ProductSaveAfterActionObserver
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $request = $observer->getEvent()->getController()->getRequest();
        $plansData = $request->getPost('vantiv_recurring_plans');
        if (is_array($plansData) && isset($plansData['plans']) && is_array($plansData['plans'])) {
            $plansData = $plansData['plans'];
            $hashedArray = [];
            foreach ($plansData as $planData) {
                if (!is_array($planData) || !isset($planData['plan_id'])) {
                    continue;
                }
                $hashedArray[$planData['plan_id']] = $planData;
            }

            $plansData = $hashedArray;
        } else {
            $plansData = [];
        }

        if (!$plansData) {
            return $this;
        }

        $planCollection = $this->plansCollectionFactory->create()
            ->addFieldToFilter('plan_id', ['in' => array_keys($plansData)]);
        foreach ($planCollection as $plan) {
            $id = $plan->getId();
            $save = false;

            if ((isset($plansData[$id]['sort_order']) && $plansData[$id]['sort_order'] != $plan->getSortOrder())) {
                $plan->setSortOrder($plansData[$id]['sort_order']);
                $save = true;
            }

            if ((isset($plansData[$id]['website_id']) && $plansData[$id]['website_id'] != $plan->getWebsiteId())) {
                $plan->setWebsiteId($plansData[$id]['website_id']);
                $save = true;
            }

            if ((isset($plansData[$id]['active']) && $plansData[$id]['active'] != $plan->getActive())) {
                $plan->setActive($plansData[$id]['active']);
                $save = true;
            }

            if ($save) {
                try {
                    $plan->save();
                } catch (\Exception $e) {
                    $this->messageManager->addError(
                        __('Unable to update subscription plan with code %1: %2', $plan->getCode(), $e->getMessage())
                    );
                }
            }
        }

        return $this;
    }
}
