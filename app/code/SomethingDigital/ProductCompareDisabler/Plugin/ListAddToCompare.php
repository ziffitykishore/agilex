<?php
namespace SomethingDigital\ProductCompareDisabler\Plugin;
class ListAddToCompare
{
    public function afterToHtml($subject, $result)
    {
        //return empty addToCompare template content
        return '';
    }
}
