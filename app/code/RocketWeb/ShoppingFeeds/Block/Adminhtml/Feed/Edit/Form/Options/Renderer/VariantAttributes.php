<?php
/**
 * RocketWeb
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @category  RocketWeb
 * @package   RocketWeb_ShoppingFeeds
 * @copyright Copyright (c) 2016 RocketWeb (http://rocketweb.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author    Rocket Web Inc.
 */

namespace RocketWeb\ShoppingFeeds\Block\Adminhtml\Feed\Edit\Form\Options\Renderer;

use Magento\Framework\View\Element\Template;

/**
 * Adminhtml VariantAttributes directive options renderer
 */
class VariantAttributes extends Template
{
    /**
     * @var \RocketWeb\ShoppingFeeds\Model\Feed\Source\Product\Attributes
     */
    protected $sourceAttributes;

    /**
     * @var string
     */
    protected $_template = 'feed/edit/form/options/renderer/product-variant-attributes.phtml';

    /**
     * VariantAttributes constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \RocketWeb\ShoppingFeeds\Model\Feed\Source\Product\Attributes $sourceAttributes
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \RocketWeb\ShoppingFeeds\Model\Feed\Source\Product\Attributes $sourceAttributes,
        array $data = []
    ) {
        $this->sourceAttributes = $sourceAttributes;
        parent::__construct($context, $data);
    }

    public function getAttributes()
    {
        return $this->sourceAttributes->toOptionArray();
    }
}
