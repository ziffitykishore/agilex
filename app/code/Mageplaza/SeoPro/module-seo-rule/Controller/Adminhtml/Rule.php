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

namespace Mageplaza\SeoRule\Controller\Adminhtml;

use Magento\Backend\App\Action;

/**
 * Class Rule
 * @package Mageplaza\SeoRule\Controller\Adminhtml
 */
abstract class Rule extends Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Mageplaza_SeoRule::rule';

    /**
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry;

    /**
     * @var \Mageplaza\SeoRule\Model\Rule
     */
    protected $seoRuleFactory;

    /**
     * @var \Mageplaza\SeoRule\Helper\Data
     */
    protected $helperData;

    /**
     * Rule constructor.
     * @param Action\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Mageplaza\SeoRule\Model\RuleFactory $seoRuleFactory
     * @param \Mageplaza\SeoRule\Helper\Data $helperData
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Mageplaza\SeoRule\Model\RuleFactory $seoRuleFactory,
        \Mageplaza\SeoRule\Helper\Data $helperData
    )
    {
        parent::__construct($context);

        $this->coreRegistry      = $coreRegistry;
        $this->seoRuleFactory    = $seoRuleFactory;
        $this->helperData        = $helperData;
    }

    /**
     * Init action
     * @return $this
     */
    protected function _initAction()
    {
        $this->_view->loadLayout();
        $this->_setActiveMenu('Mageplaza_SeoRule::rule')->_addBreadcrumb(__('SeoRule'), __('Manage Rules'));

        return $this;
    }
}
