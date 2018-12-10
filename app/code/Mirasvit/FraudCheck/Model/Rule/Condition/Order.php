<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-fraud-check
 * @version   1.0.33
 * @copyright Copyright (C) 2018 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\FraudCheck\Model\Rule\Condition;

/**
 * @method string getAttribute()
 * @method $this setAttributeOption($attributes)
 * @method array getAttributeOption()
 */
class Order extends AbstractCondition
{
    /**
     * @return $this
     */
    public function loadAttributeOptions()
    {
        $attributes = [
            'discount_amount'   => __('Discount Amount'),
            'subtotal'          => __('Subtotal'),
            'grand_total'       => __('Grand Total'),
            'shipping_amount'   => __('Shipping Amount'),
            'tax_amount'        => __('Tax Amount'),
            'remote_ip'         => __('Placed from IP'),
            'total_item_count'  => __('Items Count'),
            'total_qty_ordered' => __('Items Quantity'),
        ];

        asort($attributes);

        $this->setAttributeOption($attributes);

        return $this;
    }
}
