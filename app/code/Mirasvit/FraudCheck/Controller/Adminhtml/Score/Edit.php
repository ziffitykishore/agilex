<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-fraud-check
 * @version   1.0.33
 * @copyright Copyright (C) 2018 Mirasvit (https://mirasvit.com/)
 */


namespace Mirasvit\FraudCheck\Controller\Adminhtml\Score;

use Magento\Framework\Controller\ResultFactory;
use Mirasvit\FraudCheck\Controller\Adminhtml\Score;

class Edit extends Score
{
    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);

        $this->initModel();

        $this->initPage($resultPage)->getConfig()->getTitle()
            ->prepend(__('Fraud Risk Score Configuration'));

        return $resultPage;
    }
}
