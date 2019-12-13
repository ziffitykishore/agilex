<?php

namespace SomethingDigital\HideIndexer\Plugin;

class IndexerCronPlugin
{
    public function aroundUpdate($subject, callable $proceed)
    {
        if ($subject && $subject->getViewId()) {
            $viewId = $subject->getViewId();

            if ($viewId == "catalogsearch_fulltext") {
                return;
            }
        }
        $proceed();
    }
}
