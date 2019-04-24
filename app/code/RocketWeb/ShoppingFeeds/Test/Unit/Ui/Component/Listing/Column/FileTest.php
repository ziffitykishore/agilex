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

namespace RocketWeb\ShoppingFeeds\Test\Unit\Ui\Component\Listing\Column;

use RocketWeb\ShoppingFeeds\Ui\Component\Listing\Column\File;

class FileTest extends \PHPUnit_Framework_TestCase
{
    public function testPrepareItemsForNotExistingFile()
    {
        $feedId = 1;

        // Create Mocks and SUT
        $objectManager = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);
        $contextMock = $this->getMockBuilder('Magento\Framework\View\Element\UiComponent\ContextInterface')
            ->getMockForAbstractClass();
        $processor = $this->getMockBuilder('Magento\Framework\View\Element\UiComponent\Processor')
            ->disableOriginalConstructor()
            ->getMock();
        $feedMock = $this->getMockBuilder('RocketWeb\ShoppingFeeds\Model\Feed')
            ->disableOriginalConstructor()
            ->getMock();
        $feedFactoryMock = $this->getMock('RocketWeb\ShoppingFeeds\Model\FeedFactory', ['create'], [], '', false);

        $feedMock->expects($this->once())
            ->method('setData')
            ->willReturn($feedMock);

        $feedFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($feedMock);

        $contextMock->expects($this->any())
            ->method('getProcessor')
            ->willReturn($processor);

        /** @var \RocketWeb\ShoppingFeeds\Ui\Component\Listing\Column\File $model */
        $model = $objectManager->getObject(
            'RocketWeb\ShoppingFeeds\Ui\Component\Listing\Column\File',
            [
                'context' => $contextMock,
                'feedFactory' => $feedFactoryMock,
            ]
        );

        // Define test input and expectations
        $items = [
            'data' => [
                'items' => [
                    [
                        'id' => $feedId,
                    ]
                ]
            ]
        ];
        $name = 'item_name';
        $expectedItems = [
            [
                'id' => $feedId,
                $name => 'Feed file not ready.',
            ]
        ];

        $model->setName($name);
        $items = $model->prepareDataSource($items);
        // Run test
        $this->assertEquals($expectedItems, $items['data']['items']);
    }
}