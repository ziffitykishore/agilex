<?php

namespace SomethingDigital\HideIndexer\Plugin;


class IndexerPlugin
{
    public function aroundReindexAll($plugin, callable $proceed) {

        if ($plugin && $plugin->getIndexerId()) {
            $indexerId = $plugin->getIndexerId();

            if ($indexerId == "catalogsearch_fulltext") {
                return;
            }
        }
        $proceed();
    }
}
