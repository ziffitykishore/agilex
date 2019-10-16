<?php


namespace SomethingDigital\CategoryMenu\Block\Html;

use Magento\Theme\Block\Html\Topmenu;
use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Framework\View\Element\Template;
use Magento\Framework\Data\TreeFactory;
use Magento\Framework\Data\Tree\Node;
use Magento\Framework\Data\Tree\NodeFactory;

class TopMenuFlyout extends Topmenu
{

    /**
     * @var NodeFactory
     */
     private $nodeFactory;
     
    /**
    * @var TreeFactory
    */
    private $treeFactory;

    protected $categoryRepositoryInterface;

    public function __construct(
        Template\Context $context,
        NodeFactory $nodeFactory,
        TreeFactory $treeFactory,
        array $data = [],
        CategoryRepositoryInterface $categoryRepositoryInterface
    ) {
        parent::__construct($context, $nodeFactory, $treeFactory, $data);
        $this->nodeFactory = $nodeFactory;
        $this->treeFactory = $treeFactory;
        $this->categoryRepositoryInterface = $categoryRepositoryInterface;
    }

    protected function _addSubMenu($child, $childLevel, $childrenWrapClass, $limit)
    {
        $html = '';
        $menuStaticBlock = '';
        $mobileMenuStaticBlock = '';
        $node = $child->getId();
        $categoryId = preg_replace('/[^0-9]/', '', $node);

        if (!empty($categoryId) && strpos($node, 'category-node') !== false) {
            $categoryInfo = $this->categoryRepositoryInterface->get($categoryId);
            $categoryData = $categoryInfo->getData();
            if (isset($categoryData["menu_static_block"])) {
                $menuStaticBlock = $this->getLayout()->createBlock('Magento\Cms\Block\Block')->setBlockId($categoryData["menu_static_block"])->toHtml();
            }
            if (isset($categoryData["mobile_menu_static_block"])) {
                $mobileMenuStaticBlock = $this->getLayout()->createBlock('Magento\Cms\Block\Block')->setBlockId($categoryData["mobile_menu_static_block"])->toHtml();
            }
        }

        $colStops = null;
        if ($childLevel == 0 && $limit) {
            $colStops = $this->_columnBrake($child->getChildren(), $limit);
        }

        if (!empty($menuStaticBlock)) {
            $html .= '<div class="level' . $childLevel . ' ' . $childrenWrapClass . '">';
            $html .= $menuStaticBlock;
            $html .= '</div>';
        }
        if (!empty($mobileMenuStaticBlock)) {
            $html .= '<div class="level' . $childLevel . ' ' . $childrenWrapClass . ' mobileSubMenu">';
            $html .= $mobileMenuStaticBlock;
            $html .= '</div>';
        }

        if (empty($menuStaticBlock) && empty($mobileMenuStaticBlock)) {
            $colStops = [];
            if ($childLevel == 0 && $limit) {
                $colStops = $this->_columnBrake($child->getChildren(), $limit);
            }

            $html .= '<ul class="level' . $childLevel . ' ' . $childrenWrapClass . '">';
            $html .= $this->_getHtml($child, $childrenWrapClass, $limit, $colStops);
            $html .= '</ul>';
        }
   
        return $html;
    }
}
