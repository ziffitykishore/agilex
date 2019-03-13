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

class TypeTest extends \PHPUnit_Framework_TestCase
{
    /** 
     * @var \RocketWeb\ShoppingFeeds\Model\Feed\Source\Type 
     */
    protected $type;

    /**
     * @var \RocketWeb\ShoppingFeeds\Model\FeedTypes\Config|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $feedTypesConfigMock;

    /** @var ObjectManagerHelper */
    protected $objectManagerHelper;

    protected function setUp()
    {
        $this->objectManagerHelper = new ObjectManagerHelper($this);

        $this->feedTypesConfigMock = $this->getMockBuilder(
            'RocketWeb\ShoppingFeeds\Model\FeedTypes\Config'
        )->disableOriginalConstructor()
            ->setMethods(['getAll'])
            ->getMock();

        $this->feedTypesConfigMock->expects($this->once())
            ->method('getAll')
            ->will($this->returnValue([
                0 => ['name' => 'foo', 'label' => 'Foo'],
                1 => ['name' => 'bar', 'label' => 'Bar'],
            ]));

        $this->type = $this->objectManagerHelper->getObject(
            'RocketWeb\ShoppingFeeds\Model\Feed\Source\Type',
            [
                'feedTypesConfig' => $this->feedTypesConfigMock,
            ]
        );
    }

    public function testGetOptionArray()
    {
        $this->assertEquals([
            'foo' => 'Foo',
            'bar' => 'Bar',
        ], $this->type->getOptionArray());
    }

    public function testToOptionArray()
    {
        $this->assertEquals([
            ['value' => 'foo', 'label' => 'Foo'],
            ['value' => 'bar', 'label' => 'Bar'],
        ], $this->type->toOptionArray());
    }
}
