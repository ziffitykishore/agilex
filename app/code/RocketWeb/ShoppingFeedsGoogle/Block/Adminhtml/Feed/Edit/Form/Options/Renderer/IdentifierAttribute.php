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

namespace RocketWeb\ShoppingFeedsGoogle\Block\Adminhtml\Feed\Edit\Form\Options\Renderer;

use Magento\Framework\View\Element\Template;

/**
 * Adminhtml IdentifierAttribute directive options renderer
 */
class IdentifierAttribute extends Template
{
    /**
     * @var string
     */
    protected $_template = 'feed/edit/form/options/renderer/identifier-attribute.phtml';

    /**
     * @var \RocketWeb\ShoppingFeeds\Model\Feed\Source\Product\Attributes
     */
    protected $sourceAttributes;
    /**
     * ProductId constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     * @param array $data
     */
    public function __construct(
        \RocketWeb\ShoppingFeeds\Model\Feed\Source\Product\Attributes $sourceAttributes,
        \Magento\Backend\Block\Template\Context $context,
        array $data = []
    ) {
        $this->sourceAttributes = $sourceAttributes;
        parent::__construct($context, $data);
    }

    /**
     * Get attribute options
     *
     * @return array
     */
    public function getAttributeOptions()
    {
        return $this->sourceAttributes->toOptionArray(true);
    }
}
