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


namespace RocketWeb\ShoppingFeeds\Test\Unit\Model\Product\Category;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager as ObjectManagerHelper;
use RocketWeb\ShoppingFeeds\Test\Unit\Model\ModelFramework;

/**
 * Class CollectionProviderTest
 */
class CollectionProviderTest extends ModelFramework
{
    /**
     * @var \RocketWeb\ShoppingFeeds\Model\Product\Category\CollectionProvider
     */
    protected $model;


    /**
     * @inheritdoc
     */
    protected function setUp()
    {
        $this->objectManagerHelper = new ObjectManagerHelper($this);
        parent::setUp();

        $storeManager = $this->getModelMock('Magento\Store\Model\StoreManager', ['getStore']);
        $this->expectReturn($storeManager, 'getStore', $this->storeMock);

        $this->model = $this->objectManagerHelper->getObject(
            'RocketWeb\ShoppingFeeds\Model\Product\Category\CollectionProvider',
            [
                'categoryCollectionFactory' => $this->categoryCollectionFactoryMock,
                'storeManager' => $storeManager
            ]
        );
    }

    public function testGetCategories()
    {
        $this->expectReturn($this->feedMock, 'getStoreId', 1);
        $this->expectReturn($this->feedMock, 'getConfig', true);

        $categoryMock = $this->getModelMock('Magento\Catalog\Model\Category',
            ['getName', 'getId', 'getPath', 'getParentId', 'getLevel', 'getIsActive', ]
        );
        $this->expectAdvencedReturn($categoryMock, 'getId', $this->onConsecutiveCalls(1, 1, 2, 2));
        $this->expectAdvencedReturn($categoryMock, 'getParentId', $this->onConsecutiveCalls(0, 0 ,1, 1, 1));
        $this->expectAdvencedReturn($categoryMock, 'getLevel', $this->onConsecutiveCalls(1, 2));
        $this->expectAdvencedReturn($categoryMock, 'getName', $this->onConsecutiveCalls('A', 'B'));
        $this->expectAdvencedReturn($categoryMock, 'getPath', $this->onConsecutiveCalls('1/1','1/1/2'));

        $collection = $this->objectManagerHelper->getCollectionMock(
        'Magento\Catalog\Model\ResourceModel\Category\Collection',
            [$categoryMock, $categoryMock]
        );
        $this->expectSelf($collection,
            ['addAttributeToSelect', 'setStoreId', 'addPathFilter', 'addLevelFilter', 'addAttributeToSort']);

        $this->expectReturn($this->categoryCollectionFactoryMock, 'create', $collection);

        $categories = $this->model->getCategories($this->feedMock);
        $expected = [[
                'name' => 'A',
                'id' => 1,
                'path' => '1/1',
                'parent_id' => 0,
                'level' => 1,
                'store_active' => NULL,
                'children' => 1,
            ],[
                'name' => 'B',
                'id' => 2,
                'path' => '1/1/2',
                'parent_id' => 1,
                'level' => 2,
                'store_active' => NULL,
                'children' => 0
        ]];
        $this->assertEquals($expected, $categories);
    }
}
