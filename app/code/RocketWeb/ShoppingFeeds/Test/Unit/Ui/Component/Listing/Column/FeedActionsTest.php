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

use RocketWeb\ShoppingFeeds\Ui\Component\Listing\Column\FeedActions;

class FeedActionsTest extends \PHPUnit_Framework_TestCase
{
    public function testPrepareItemsByFeedId()
    {
        $feedId = 1;
        // Create Mocks and SUT
        $objectManager = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);
        /** @var \PHPUnit_Framework_MockObject_MockObject $urlBuilderMock */
        $urlBuilderMock = $this->getMockBuilder('Magento\Framework\UrlInterface')
            ->disableOriginalConstructor()
            ->getMock();
        $contextMock = $this->getMockBuilder('Magento\Framework\View\Element\UiComponent\ContextInterface')
            ->getMockForAbstractClass();
        $processor = $this->getMockBuilder('Magento\Framework\View\Element\UiComponent\Processor')
            ->disableOriginalConstructor()
            ->getMock();
        $contextMock->expects($this->any())->method('getProcessor')->willReturn($processor);

        /** @var \RocketWeb\ShoppingFeeds\Ui\Component\Listing\Column\FeedActions $model */
        $model = $objectManager->getObject(
            'RocketWeb\ShoppingFeeds\Ui\Component\Listing\Column\FeedActions',
            [
                'urlBuilder' => $urlBuilderMock,
                'context' => $contextMock,
            ]
        );

        // Define test input and expectations
        $items = [
            'data' => [
                'items' => [
                    [
                        'id' => $feedId
                    ]
                ]
            ]
        ];
        $name = 'item_name';
        $expectedItems = [
            [
                'id' => $feedId,
                $name => '<a href="test/url/generate">Run Now</a>'. ' / <a href="test/url/edit">Configure</a>'
                        . ' <br /><a popup="1" href="test/url/test" onclick="window.open(this.href,\'feed_test\',\'width=835,height=700,resizable=1,scrollbars=1\');return false;">Test Feed</a>'
                        . ' / <a popup="1" href="test/url/viewlog" onclick="window.open(this.href,\'feed_logs\',\'width=835,height=700,resizable=1,scrollbars=1\');return false;">View Log</a>'
            ]
        ];

        // Configure mocks and object data
        $urlBuilderMock->expects($this->any())
            ->method('getUrl')
            ->willReturnMap(
                [
                    [
                        FeedActions::FEED_URL_PATH_EDIT,
                        [
                            'id' => $feedId
                        ],
                        'test/url/edit',
                    ],
                    [
                        FeedActions::FEED_URL_PATH_GENERATE,
                        [
                            'id' => $feedId
                        ],
                        'test/url/generate',
                    ],
                    [
                        FeedActions::FEED_URL_PATH_TEST,
                        [
                            'id' => $feedId
                        ],
                        'test/url/test',
                    ],
                    [
                        FeedActions::FEED_URL_PATH_VIEWLOG,
                        [
                            'id' => $feedId
                        ],
                        'test/url/viewlog',
                    ],
                ]
            );

        $model->setName($name);
        $items = $model->prepareDataSource($items);
        // Run test
        $this->assertEquals($expectedItems, $items['data']['items']);
    }
}
