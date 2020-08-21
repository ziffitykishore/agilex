<?php

namespace SomethingDigital\ProductUrlRewrites\Plugin;

use Magento\CatalogUrlRewrite\Observer\UrlRewriteHandler;

class DisableGenerateProductUrlRewrites
{   
    /**
     * Disable generating URL rewrites for products assigned to category.
     * We don't use category url key for products, so we don't need regenerate products urls
     * when category url_key is changed.
     * It's to allow to save category with many associated priducts and avoid timeout error.
     *
     * @return array
     */
    public function aroundGenerateProductUrlRewrites(UrlRewriteHandler $subject, callable $proceed)
    {
        return [];
    }
}
