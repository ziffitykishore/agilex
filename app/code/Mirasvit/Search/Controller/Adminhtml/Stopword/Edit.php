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
 * @package   mirasvit/module-search
 * @version   1.0.78
 * @copyright Copyright (C) 2018 Mirasvit (https://mirasvit.com/)
 */


namespace Mirasvit\Search\Controller\Adminhtml\Stopword;

use Magento\Framework\Controller\ResultFactory;
use Mirasvit\Search\Api\Data\StopwordInterface;
use Mirasvit\Search\Controller\Adminhtml\Stopword;

class Edit extends Stopword
{
    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        $model = $this->initModel();
        $id = $this->getRequest()->getParam(StopwordInterface::ID);

        if (!$model->getId() && $id) {
            $this->messageManager->addErrorMessage(__('This stopword no longer exists.'));
            $resultRedirect = $this->resultRedirectFactory->create();

            return $resultRedirect->setPath('*/*/');
        }

        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);

        $this->initPage($resultPage)
            ->getConfig()->getTitle()->prepend(
                $model->getId() ? __('Stopword "%1"', $model->getTerm()) : __('New Stopword')
            );

        return $resultPage;
    }
}
