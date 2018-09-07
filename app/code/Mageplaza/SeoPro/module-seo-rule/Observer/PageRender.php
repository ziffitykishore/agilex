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
 * @package     Mageplaza_SeoRule
 * @copyright   Copyright (c) Mageplaza (http://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\SeoRule\Observer;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;
use Mageplaza\SeoRule\Model\Rule\Source\Type;

/**
 * Class PageRender
 * @package Mageplaza\SeoRule\Observer
 */
class PageRender extends SeoRuleAbstract implements ObserverInterface
{
    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function execute(EventObserver $observer)
    {
        if ($this->helperData->isEnableSeoRule() && in_array($this->request->getFullActionName(), ['cms_page_view', 'cms_index_index'])) {
            $page = $observer->getEvent()->getPage();
            $this->setMetaData($page, 'page_id', Type::PAGES);
        }

        return $this;
    }
}