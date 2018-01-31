<?php
namespace SomethingDigital\ProductCompareDisabler\Plugin;
class ViewAddToCompare
{
    public function afterToHtml($subject, $result)
    {
        //return empty addToCompare template content
        return '';
    }
}
