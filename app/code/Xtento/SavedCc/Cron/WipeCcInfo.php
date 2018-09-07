<?php

/**
 * Product:       Xtento_SavedCc (1.0.6)
 * ID:            NZWbKguR/Yb8QYk68QaZWfj7V5pl/BlDdubJ/+3MKvg=
 * Packaged:      2018-08-15T13:47:06+00:00
 * Last Modified: 2018-05-09T10:38:59+00:00
 * File:          app/code/Xtento/SavedCc/Cron/WipeCcInfo.php
 * Copyright:     Copyright (c) 2018 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

namespace Xtento\SavedCc\Cron;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Xtento\SavedCc\Logger\Logger;

class WipeCcInfo
{
    /**
     * @var \Xtento\SavedCc\Helper\Module
     */
    protected $moduleHelper;

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var Logger
     */
    protected $logger;

    /**
     * @var \Magento\Sales\Api\OrderRepositoryInterface
     */
    protected $orderRepository;

    /**
     * @var \Magento\Framework\Api\SearchCriteriaBuilder
     */
    protected $searchCriteriaBuilder;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    protected $localeDate;

    /**
     * WipeCcInfo constructor.
     *
     * @param \Xtento\SavedCc\Helper\Module $moduleHelper
     * @param ScopeConfigInterface $scopeConfig
     * @param Logger $logger
     * @param \Magento\Sales\Api\OrderRepositoryInterface $orderRepository
     * @param \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate
     */
    public function __construct(
        \Xtento\SavedCc\Helper\Module $moduleHelper,
        ScopeConfigInterface $scopeConfig,
        Logger $logger,
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository,
        \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate
    ) {
        $this->moduleHelper = $moduleHelper;
        $this->scopeConfig = $scopeConfig;
        $this->logger = $logger;
        $this->orderRepository = $orderRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->localeDate = $localeDate;
    }

    /**
     * Wipe old CCs
     *
     * @param $schedule
     */
    public function execute($schedule)
    {
        try {
            if (!$this->moduleHelper->isModuleEnabled()) {
                return;
            }

            $clearAfterDays = (int)$this->scopeConfig->getValue('xtsavedcc/general/wipe_cc_after');
            if ($clearAfterDays > 0) {
                $this->logger->notice(__('Starting CC wiping process, credit card information will be wiped for orders older than %1 days.', $clearAfterDays));

                $dateFrom = $this->localeDate->date();
                $dateFrom->sub(new \DateInterval('P' . ($clearAfterDays + 5) . 'D'));
                $dateFrom->setTime(0, 0, 0);
                $dateFrom->setTimezone(new \DateTimeZone('UTC'));
                $dateFrom = $dateFrom->format(\Magento\Framework\Stdlib\DateTime::DATE_PHP_FORMAT);
                $dateTo = $this->localeDate->date();
                $dateTo->sub(new \DateInterval('P' . $clearAfterDays . 'D'));
                $dateTo->setTime(0, 0, 0);
                $dateTo->setTimezone(new \DateTimeZone('UTC'));
                $dateTo = $dateTo->format(\Magento\Framework\Stdlib\DateTime::DATE_PHP_FORMAT);
                $searchCriteria = $this->searchCriteriaBuilder
                    ->addFilter('created_at', $dateFrom, 'gt')
                    ->addFilter('created_at', $dateTo, 'lt')
                    ->create();

                $orderList = $this->orderRepository->getList($searchCriteria);
                foreach ($orderList->getItems() as $order) {
                    if ($order->getPayment()->getMethod() == \Xtento\SavedCc\Model\Ui\ConfigProvider::CODE) {
                        $this->moduleHelper->wipeCreditCardInfo($order);
                    }
                };

                $this->logger->notice(__('Wiping process finished.'));
            }
        } catch (\Exception $e) {
            $this->logger->critical(__('Cronjob exception: %1'), $e->getMessage());
        }
    }
}
