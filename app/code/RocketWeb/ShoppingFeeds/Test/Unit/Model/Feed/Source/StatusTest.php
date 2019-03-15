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

namespace RocketWeb\ShoppingFeeds\Test\Unit\Model\Feed\Source;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager as ObjectManagerHelper;

class StatusTest extends \PHPUnit_Framework_TestCase
{
    /** @var \RocketWeb\ShoppingFeeds\Model\Feed\Source\Status */
    protected $status;

    /** @var ObjectManagerHelper */
    protected $objectManagerHelper;

    protected function setUp()
    {
        $this->objectManagerHelper = new ObjectManagerHelper($this);

        $this->status = $this->objectManagerHelper->getObject(
            'RocketWeb\ShoppingFeeds\Model\Feed\Source\Status'
        );
    }

    public function testGetOptionArray()
    {
        $this->assertEquals([
            0 => 'Disabled',
            1 => 'Scheduled',
            2 => 'Pending',
            3 => 'Processing',
            4 => 'Completed',
            5 => 'Error',
        ], $this->status->getOptionArray());
    }

    public function testToOptionArray()
    {
        $this->assertEquals([
            ['value' => 0, 'label' => 'Disabled'],
            ['value' => 1, 'label' => 'Scheduled'],
            ['value' => 2, 'label' => 'Pending'],
            ['value' => 3, 'label' => 'Processing'],
            ['value' => 4, 'label' => 'Completed'],
            ['value' => 5, 'label' => 'Error'],
        ], $this->status->toOptionArray());
    }
}
