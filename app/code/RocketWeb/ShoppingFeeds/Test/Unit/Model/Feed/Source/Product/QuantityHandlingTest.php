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

// @codingStandardsIgnoreFile

namespace RocketWeb\ShoppingFeeds\Test\Unit\Model\Feed\Source\Product;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager as ObjectManagerHelper;

class QuantityHandlingTest extends \PHPUnit_Framework_TestCase
{
    /** @var \RocketWeb\ShoppingFeeds\Model\Feed\Source\Product\QuantityHandling */
    protected $sourceQuantityHandling;

    /** @var ObjectManagerHelper */
    protected $objectManagerHelper;

    protected function setUp()
    {
        $this->objectManagerHelper = new ObjectManagerHelper($this);

        $this->sourceQuantityHandling = $this->objectManagerHelper->getObject(
            'RocketWeb\ShoppingFeeds\Model\Feed\Source\Product\QuantityHandling'
        );
    }

    public function testGetOptionArray()
    {
        $this->assertEquals([
            0 => 'Item\'s qty',
            1 => 'Sum of associated items qty',
        ], $this->sourceQuantityHandling->getOptionArray());
    }

    public function testGetToOptionArray()
    {
        $this->assertEquals([
            ['value' => 0, 'label' => 'Item\'s qty'],
            ['value' => 1, 'label' => 'Sum of associated items qty'],
        ], $this->sourceQuantityHandling->toOptionArray());
    }
}