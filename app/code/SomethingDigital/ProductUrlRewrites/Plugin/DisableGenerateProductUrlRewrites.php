<?php

namespace SomethingDigital\ProductUrlRewrites\Plugin;

use Magento\CatalogUrlRewrite\Observer\UrlRewriteHandler;

class DisableGenerateProductUrlRewrites
{   
    public function aroundGenerateProductUrlRewrites(UrlRewriteHandler $subject, callable $proceed)
    {
        return [];
    }
}
