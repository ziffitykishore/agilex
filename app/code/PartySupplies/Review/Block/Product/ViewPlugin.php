<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace PartySupplies\Review\Block\Product;

class ViewPlugin
{
    
    public function afterGetReviewsCollection(\Magento\Review\Block\Product\View $subject, $result)
    {
        return $result->setPageSize(2);
    }
}
