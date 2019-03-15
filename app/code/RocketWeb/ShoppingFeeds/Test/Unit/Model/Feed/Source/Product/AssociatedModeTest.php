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

class AssociatedModeTest extends \PHPUnit_Framework_TestCase
{
    /** @var \RocketWeb\ShoppingFeeds\Model\Feed\Source\Product\AssociatedMode */
    protected $sourceMode;

    /** @var ObjectManagerHelper */
    protected $objectManagerHelper;

    protected function setUp()
    {
        $this->objectManagerHelper = new ObjectManagerHelper($this);

        $this->sourceMode = $this->objectManagerHelper->getObject(
            'RocketWeb\ShoppingFeeds\Model\Feed\Source\Product\AssociatedMode'
        );
    }

    public function testGetOptionArray()
    {
        $this->assertEquals([
            1 => 'No parent product / Only associated products',
            0 => 'Only parent / No associated products',
            2 => 'Both types - parent product and associated products',
        ], $this->sourceMode->getOptionArray());
    }

    public function testGetToOptionArray()
    {
        $this->assertEquals([
            ['value' => 1, 'label' => 'No parent product / Only associated products'],
            ['value' => 0, 'label' => 'Only parent / No associated products'],
            ['value' => 2, 'label' => 'Both types - parent product and associated products'],
        ], $this->sourceMode->toOptionArray());
    }
}
