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

namespace RocketWeb\ShoppingFeeds\Block\Adminhtml\Feed\Edit\Tab\Complex;

use Magento\Framework\Data\Form\Element\Renderer\RendererInterface;
use RocketWeb\ShoppingFeeds\Block\Adminhtml\Feed\Edit\Form\Element\AbstractArrayElement;

/**
 * Adminhtml find and replace renderer
 */
class Inherit extends AbstractArrayElement implements RendererInterface
{
    const DEFAULT_COLUMN = 'id';

    /**
     * @var \RocketWeb\ShoppingFeeds\Model\Feed\Source\Product\Columns
     */
    protected $sourceProductColumns;

    /**
     * @var \RocketWeb\ShoppingFeeds\Model\Feed\Source\Product\Inheritance
     */
    protected $sourceInheritance;

    /**
     * @var string
     */
    protected $_template = 'feed/edit/tab/complex/inherit.phtml';

    /**
     * Columns cache
     *
     * @var array
     */
    protected $columns;

    /**
     * FindReplace constructor.
     *
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \RocketWeb\ShoppingFeeds\Model\Feed\Source\Product\Columns $sourceProductColumns
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \RocketWeb\ShoppingFeeds\Model\Feed\Source\Product\Columns $sourceProductColumns,
        \RocketWeb\ShoppingFeeds\Model\Feed\Source\Product\Inheritance $sourceInheritance,
        array $data = []
    ) {
        $this->sourceProductColumns = $sourceProductColumns;
        $this->sourceInheritance = $sourceInheritance;
        parent::__construct($context, $data);
    }

    /**
     * Sort find and replace rule values callback method
     *
     * @param array $a
     * @param array $b
     * @return int
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    protected function sortValuesCallback($a, $b)
    {
        return 1;
    }

    /**
     * Retrieve allowed columns
     *
     * @return array
     */
    public function getColumns()
    {
        return $this->sourceProductColumns->toOptionArray();
    }

    public function getOptions()
    {
        return $this->sourceInheritance->toOptionArray();
    }

    /**
     * Retrieve default value for column
     *
     * @return int
     */
    public function getDefaultColumn()
    {
        return self::DEFAULT_COLUMN;
    }

    /**
     * Retrieve 'Add Rule' button HTML
     *
     * @return string
     */
    public function getAddButtonHtml()
    {
        $button = $this->getLayout()->createBlock(
            'Magento\Backend\Block\Widget\Button'
        )->setData(
            [
                'label' => __('Add Rule'),
                'onclick' => 'return inheritControl_'.$this->getElement()->getHtmlId().'.addItem()',
                'class' => 'add'
            ]
        );
        $button->setName('add_inheritance_rule_button');

        $this->setChild('add_button', $button);
        return $this->getChildHtml('add_button');
    }
}
