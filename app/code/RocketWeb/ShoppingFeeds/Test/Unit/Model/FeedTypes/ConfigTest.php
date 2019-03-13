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

namespace RocketWeb\ShoppingFeeds\Test\Unit\Model\ProductTypes;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager as ObjectManagerHelper;

class ConfigTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $readerMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $cacheMock;

    /**
     * @var \RocketWeb\ShoppingFeeds\Model\FeedTypes\Config
     */
    protected $model;

    /**
     * @var ObjectManagerHelper
     */
    protected $objectManagerHelper;

    protected function setUp()
    {
        $this->objectManagerHelper = new ObjectManagerHelper($this);

        $this->readerMock = $this->getMock(
            'RocketWeb\ShoppingFeeds\Model\FeedTypes\Config\Reader',
            [],
            [],
            '',
            false
        );
        $this->cacheMock = $this->getMock('Magento\Framework\Config\CacheInterface');
    }

    /**
     * @dataProvider getFeedDataProvider
     *
     * @param array $value
     * @param mixed $expected
     */
    public function testGetFeed($value, $expected)
    {
        $this->cacheMock->expects($this->any())->method('load')->will($this->returnValue(serialize($value)));

        $this->model = $this->objectManagerHelper->getObject(
            'RocketWeb\ShoppingFeeds\Model\FeedTypes\Config',
            [
                'reader' => $this->readerMock,
                'cache' => $this->cacheMock,
                'cacheId' => 'cache_id',
            ]
        );

        $this->assertEquals($expected, $this->model->getFeed('google_shoping'));
    }

    public function getFeedDataProvider()
    {
        return [
            'global_key_exist' => [['feed' => ['google_shoping' => 'value']], 'value'],
            'return_default_value' => [['feed' => ['some_key' => 'value']], []]
        ];
    }

    public function testGetAll()
    {
        $expected = ['Expected Data'];
        $this->cacheMock->expects(
            $this->once()
        )->method(
            'load'
        )->will(
            $this->returnValue(serialize(['feed' => $expected]))
        );

        $this->model = $this->objectManagerHelper->getObject(
            'RocketWeb\ShoppingFeeds\Model\FeedTypes\Config',
            [
                'reader' => $this->readerMock,
                'cache' => $this->cacheMock,
                'cacheId' => 'cache_id',
            ]
        );

        $this->assertEquals($expected, $this->model->getAll());
    }

    /**
     * @dataProvider getIsAllowedDirectiveProvider
     *
     * @param array $value
     * @param mixed $expected
     */
    public function testIsAllowedDirective($value, $expected)
    {
        $this->cacheMock->expects($this->once())->method('load')->will($this->returnValue(serialize($value)));

        $this->model = $this->objectManagerHelper->getObject(
            'RocketWeb\ShoppingFeeds\Model\FeedTypes\Config',
            [
                'reader' => $this->readerMock,
                'cache' => $this->cacheMock,
                'cacheId' => 'cache_id',
            ]
        );

        $this->assertEquals($expected, $this->model->isAllowedDirective('generic', 'some_directive'));
    }

    public function getIsAllowedDirectiveProvider()
    {
        return [
            'is_allowed' => [['feed' => ['generic' => ['directives' => ['some_directive' => ['Expected Data']]]]], true],
            'is_not_allowed' => [['feed' => ['generic' => ['directives' => ['some_other_directive' => ['Expected Data']]]]], false],
        ];
    }

    /**
     * @dataProvider getDirectiveProvider
     *
     * @param $value
     * @param $expected
     */
    public function testGetDirective($value, $expected)
    {
        $this->cacheMock->expects($this->once())->method('load')->will($this->returnValue(serialize($value)));

        $this->model = $this->objectManagerHelper->getObject(
            'RocketWeb\ShoppingFeeds\Model\FeedTypes\Config',
            [
                'reader' => $this->readerMock,
                'cache' => $this->cacheMock,
                'cacheId' => 'cache_id',
            ]
        );

        $this->assertEquals($expected, $this->model->getDirective('generic', 'some_directive'));
    }

    public function getDirectiveProvider()
    {
        return [
            'is_allowed' => [['feed' => ['generic' => ['directives' => ['some_directive' => ['Expected Data']]]]], ['Expected Data']]
        ];
    }
}
