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

namespace Mageplaza\SeoRule\Controller\Adminhtml\Rule;

use Mageplaza\SeoRule\Controller\Adminhtml\Rule;

/**
 * Class Preview
 * @package Mageplaza\SeoRule\Controller\Adminhtml\Rule
 */
class Preview extends Rule
{
    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $resultJsonFactory;

    /**
     * Preview constructor.
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Mageplaza\SeoRule\Model\RuleFactory $seoRuleFactory
     * @param \Mageplaza\SeoRule\Helper\Data $helperData
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Mageplaza\SeoRule\Model\RuleFactory $seoRuleFactory,
        \Mageplaza\SeoRule\Helper\Data $helperData,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
    )
    {
        $this->resultJsonFactory = $resultJsonFactory;

        parent::__construct($context, $coreRegistry, $seoRuleFactory, $helperData);
    }

    /**
     * @return $this|\Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $request = $this->getRequest();
        if ($request->getParam('isAjax')) {
            $params = $request->getParams();
            $data   = $this->helperData->preview(
                $params['metaTitle'],
                $params['metaDescription'],
                $params['metaKeywords'],
                $this->_getSession()->getSeoRuleType()
            );

            return $this->resultJsonFactory->create()->setData($data);
        }
    }
}
