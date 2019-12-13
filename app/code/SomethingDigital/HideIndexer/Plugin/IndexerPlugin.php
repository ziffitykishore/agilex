<?php

namespace SomethingDigital\HideIndexer\Plugin;

class IndexerPlugin
{
    public function aroundReindexAll($subject, callable $proceed)
    {
        if ($subject && $subject->getIndexerId()) {
            $indexerId = $subject->getIndexerId();

            if ($indexerId == "catalogsearch_fulltext") {
                return;
            }
        }
        $proceed();
    }
}
