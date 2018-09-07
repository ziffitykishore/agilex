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

namespace Mageplaza\SeoRule\Block\Adminhtml\Rule\Edit;

use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget;
use Magento\Backend\Model\Auth\Session;
use Magento\Framework\Json\EncoderInterface;
use Magento\Framework\Registry;

/**
 * Class Tabs
 * @package Mageplaza\SeoRule\Block\Adminhtml\Rule\Edit
 */
class Tabs extends Widget\Tabs
{
    /**
     * @var \Magento\Framework\Registry
     */
    private $registry;

    /**
     * Tabs constructor.
     * @param Context $context
     * @param EncoderInterface $jsonEncoder
     * @param Session $authSession
     * @param Registry $registry
     * @param array $data
     */
    public function __construct(
        Context $context,
        EncoderInterface $jsonEncoder,
        Session $authSession,
        Registry $registry,
        array $data = []
    )
    {
        parent::__construct($context, $jsonEncoder, $authSession, $data);

        $this->registry = $registry;
    }

    /**
     * @inheritdoc
     */
    protected function _construct()
    {
        parent::_construct();

        $this->setId('rule_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Rule Information'));
    }

    /**
     * @inheritdoc
     */
    protected function _beforeToHtml()
    {
        $defaultRule = false;
        $this->addTab('main', [
            'label'   => __('Rule Information'),
            'title'   => __('Rule Information'),
            'content' => $this->getChildHtml('mageplaza_seorule_rule_edit_tab_general'),
            'active'  => true
        ]);

        if ($this->getRequest()->getParam('type') == 'page') {
            $this->addTab('labels', [
                'label' => __('Conditions'),
                'title' => __('Conditions'),
                'url'   => $this->getUrl('*/*/pages', ['_current' => true]),
                'class' => 'ajax'
            ]);
        } else if ($this->getRequest()->getParam('type') == 'category') {
            $this->addTab('labels', [
                'label'   => __('Conditions'),
                'title'   => __('Conditions'),
                'content' => $this->getChildHtml('category')
            ]);
        } else if (!$defaultRule && !$this->checkDefaultRuleForLayer()) {
            $this->addTab('labels', [
                'label'   => __('Conditions'),
                'title'   => __('Conditions'),
                'content' => $this->getChildHtml('conditions')
            ]);
        }

        $this->addTab('actions', [
            'label'   => __('Actions'),
            'title'   => __('Actions'),
            'content' => $this->getChildHtml('mageplaza_seorule_rule_edit_tab_action')
        ]);

        if ($this->_backendSession->getSeoRuleType() != \Mageplaza\SeoRule\Model\Rule\Source\Type::LAYERED_NAVIGATION) {
            $this->addTab('preview', [
                'label'   => __('Preview'),
                'title'   => __('Preview'),
                'content' => $this->getChildHtml('mageplaza_seorule_rule_edit_tab_preview')
            ]);
        }

        return parent::_beforeToHtml();
    }

    /**
     * Check default rule for layer navigation
     * @return bool
     */
    public function checkDefaultRuleForLayer()
    {
        $rule = $this->registry->registry('mageplaza_seorule_rule');
        if ($rule && $rule->getRuleId() == 1) {
            return true;
        }

        return false;
    }
}
