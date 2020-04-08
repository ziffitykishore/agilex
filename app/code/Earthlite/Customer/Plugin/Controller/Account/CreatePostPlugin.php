<?php
declare(strict_types=1);

namespace Earthlite\Customer\Plugin\Controller\Account;

/**
 * class CreatePostPlugin
 */
class CreatePostPlugin extends \Magento\Customer\Controller\Account\CreatePost
{
    /**
     * @inheritdoc    
     */
    public function beforeExecute(\Magento\Customer\Controller\Account\CreatePost $createPost)
    {
        $password = $createPost->getRequest()->getParam('password');
        $createPost->getRequest()->setParam('password_confirmation', $password);
    }
}
