<?php

namespace SomethingDigital\CustomerStoreRedirection\Plugin;

use SomethingDigital\CustomerStoreRedirection\Model\Redirection;
use Magento\Framework\Controller\ResultFactory;
use Magento\Customer\Controller\Account\LoginPost;

class LoginRedirect
{
    protected $redirection;
    protected $result;

    public function __construct(
        Redirection $redirection,
        ResultFactory $result
    ) {
        $this->redirection = $redirection;
        $this->result = $result;
    }

    public function afterExecute(LoginPost $subject, $result)
    {
        if ($this->redirection->url != '') {
            $resultRedirect = $this->result->create(ResultFactory::TYPE_REDIRECT);
            $resultRedirect->setUrl($this->redirection->url);
            return $resultRedirect;
        }
        return $result;
    }
}
