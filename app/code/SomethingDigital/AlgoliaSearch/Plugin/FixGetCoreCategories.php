<?php

namespace SomethingDigital\AlgoliaSearch\Plugin;

class FixGetCoreCategories
{
    /**
     * Plugin to fix issue in Algolia module.
     * It changes default value of filterNotIncludedCategories parameter
     *
     * @param \Algolia\AlgoliaSearch\Helper\Entity\CategoryHelper $subject
     * @param $filterNotIncludedCategories
     * @return array
     */
    public function beforeGetCoreCategories(\Algolia\AlgoliaSearch\Helper\Entity\CategoryHelper $subject, $filterNotIncludedCategories = false)
    {
        return [$filterNotIncludedCategories];
    }
}
