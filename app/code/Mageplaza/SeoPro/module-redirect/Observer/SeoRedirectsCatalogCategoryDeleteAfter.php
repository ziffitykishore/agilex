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
 * @package     Mageplaza_Redirects
 * @copyright   Copyright (c) Mageplaza (http://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\Redirects\Observer;

use Magento\Backend\Model\Session;
use Magento\CatalogUrlRewrite\Model\CategoryUrlPathGenerator;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Mageplaza\Redirects\Helper\Data as HelperData;

/**
 * Class SeoRedirectsCatalogCategoryDeleteAfter
 * @package Mageplaza\Redirects\Observer
 */
class SeoRedirectsCatalogCategoryDeleteAfter implements ObserverInterface
{
    /**
     * @var \Magento\Backend\Model\Session
     */
    protected $backendSession;

    /**
     * @var \mageplaza\Redirects\Helper\Data
     */
    protected $helperConfig;

    /**
     * @var \Magento\CatalogUrlRewrite\Model\CategoryUrlPathGenerator
     */
    protected $categoryUrlPathGenerator;

    /**
     * SeoRedirectsCatalogCategoryDeleteAfter constructor.
     * @param \Magento\Backend\Model\Session $backendSession
     * @param \Mageplaza\Redirects\Helper\Data $helperConfig
     * @param \Magento\CatalogUrlRewrite\Model\CategoryUrlPathGenerator $categoryUrlPathGenerator
     */
    public function __construct(
        Session $backendSession,
        HelperData $helperConfig,
        CategoryUrlPathGenerator $categoryUrlPathGenerator
    )
    {
        $this->backendSession           = $backendSession;
        $this->helperConfig             = $helperConfig;
        $this->categoryUrlPathGenerator = $categoryUrlPathGenerator;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(Observer $observer)
    {
        if ($this->helperConfig->isRedirectEnabled()) {
            /** @var $category \Magento\Catalog\Model\Category */
            $category = $observer->getEvent()->getCategory();

            $data = $this->backendSession->getData('category_deleted') ?: [];

            $data[] = $this->categoryUrlPathGenerator->getUrlPathWithSuffix($category);
            $this->backendSession->setData('category_deleted', $data);
        }
    }
}