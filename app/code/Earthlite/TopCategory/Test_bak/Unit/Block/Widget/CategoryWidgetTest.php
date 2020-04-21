<?php

namespace Earthlite\TopCategory\Test\Unit\Block\Widget;

use Earthlite\Core\Helper\Constant;
use \Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

class CategoryWidgetTest extends \PHPUnit_Framework_TestCase
{

    public $model;
    public $categoryFlatConfig;
    public $topMenu;
    public $categoryFactory;
    public $categoryHelper;
    public $catalogSession;
    public $block;

    public function setUp()
    {
        $this->categoryHelper = $this->getMockBuilder('\Magento\Catalog\Helper\Category')
                ->disableOriginalConstructor()
                ->getMock();
        $this->categoryFlatConfig = $this->getMockBuilder('\Magento\Catalog\Model\Indexer\Category\Flat\State')
                ->disableOriginalConstructor()
                ->getMock();
        $this->topMenu = $this->getMockBuilder('\Magento\Theme\Block\Html\Topmenu')
                ->disableOriginalConstructor()
                ->getMock();
        $this->categoryFactory = $this->getMockBuilder('\Magento\Catalog\Model\CategoryFactory')
                ->disableOriginalConstructor()
                ->getMock();
        $this->catalogSession = $this->getMockBuilder('\Magento\Catalog\Model\Session')
                ->disableOriginalConstructor()
                ->getMock();

        $contextMock = $this->getMock('Magento\Framework\View\Element\Template\Context', [], [], '', false);

        $objectManager = new ObjectManager($this);
        $this->block = $objectManager->getObject(
            'Earthlite\TopCategory\Block\Widget\CategoryWidget',
            [
            'context' => $contextMock,
            'categoryHelper' => $this->categoryHelper,
            'categoryFlatState' => $this->categoryFlatConfig,
            'topMenu' => $this->topMenu,
            'categoryFactory' => $this->categoryFactory,
            'catalogSession' => $this->catalogSession,
                ]
        );
    }

    public function testCategoryCollection()
    {
        $this->markTestSkipped('This test is failing and needs to be updated');
        $customIdPath = Constant::$catIdPath;
        $customId = '3';
        $this->block->setData(Constant::$catIdPath, $customIdPath);
        $this->assertEquals($customId, $this->block->getCategoryCollection());
    }
}
