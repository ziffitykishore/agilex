<?php

/*
 * Copyright © 2016 Wyomind. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Wyomind\AdvancedInventory\Controller\Adminhtml;

/**
 * Controller for Profile items
 */
abstract class Sales extends \Magento\Backend\App\Action {

    protected $_session = null;
    
    public function __construct(
    \Magento\Backend\App\Action\Context $context
    ) {

        /* Object assignation */
        $this->_context = $context;
        $this->_session = $context->getSession();
        parent::__construct($context);
    }

    /**
     *
     * @return boolean
     */
    protected function _isAllowed() {
        return true;
    }

    /**
     *
     * @param type $data
     * @return boolean
     */
    protected function _validatePostData($data) {
        $errorNo = true;
        if (!empty($data['layout_update_xml']) || !empty($data['custom_layout_update_xml'])) {
            /** @var $validatorCustomLayout \Magento\Core\Model\Layout\Update\Validator */
            $validatorCustomLayout = $this->_objectManager->create('Magento\Core\Model\Layout\Update\Validator');
            if (!empty($data['layout_update_xml']) && !$validatorCustomLayout->isValid($data['layout_update_xml'])) {
                $errorNo = false;
            }
            if (!empty($data['custom_layout_update_xml']) && !$validatorCustomLayout->isValid(
                            $data['custom_layout_update_xml']
                    )
            ) {
                $errorNo = false;
            }
            foreach ($validatorCustomLayout->getMessages() as $message) {
                $this->messageManager->addError($message);
            }
        }
        return $errorNo;
    }

    abstract public function execute();
}
