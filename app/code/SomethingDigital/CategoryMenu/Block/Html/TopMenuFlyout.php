<?php


namespace SomethingDigital\CategoryMenu\Block\Html;

use Magento\Theme\Block\Html\Topmenu;
use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Framework\View\Element\Template;
use Magento\Framework\Data\TreeFactory;
use Magento\Framework\Data\Tree\Node;
use Magento\Framework\Data\Tree\NodeFactory;
use Magento\Cms\Api\BlockRepositoryInterface;
use Magento\Cms\Model\Template\FilterProvider;

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
    protected $blockRepository;
    protected $filterProvider;

    public function __construct(
        Template\Context $context,
        NodeFactory $nodeFactory,
        TreeFactory $treeFactory,
        array $data = [],
        CategoryRepositoryInterface $categoryRepositoryInterface,
        BlockRepositoryInterface $blockRepository,
        FilterProvider $filterProvider
    ) {
        parent::__construct($context, $nodeFactory, $treeFactory, $data);
        $this->nodeFactory = $nodeFactory;
        $this->treeFactory = $treeFactory;
        $this->categoryRepositoryInterface = $categoryRepositoryInterface;
        $this->blockRepository = $blockRepository;
        $this->filterProvider = $filterProvider;
    }

    protected function _addSubMenu($child, $childLevel, $childrenWrapClass, $limit)
    {
        $html = '';
        $menuStaticBlockHtml = '';
        $mobileMenuStaticBlockHtml = '';
        $node = $child->getId();
        $categoryId = preg_replace('/[^0-9]/', '', $node);

        if (!empty($categoryId) && strpos($node, 'category-node') !== false) {
            $categoryInfo = $this->categoryRepositoryInterface->get($categoryId);
            $categoryData = $categoryInfo->getData();
            if (isset($categoryData["menu_static_block"])) {
                $menuStaticBlock = $this->blockRepository->getById($categoryData["menu_static_block"]);
                $menuStaticBlockHtml = $this->filterProvider->getPageFilter()->filter($menuStaticBlock->getContent());
            }
            if (isset($categoryData["mobile_menu_static_block"])) {
                $mobileMenuStaticBlock = $this->blockRepository->getById($categoryData["mobile_menu_static_block"]);
                $mobileMenuStaticBlockHtml = $this->filterProvider->getPageFilter()->filter($mobileMenuStaticBlock->getContent());
            }
        }

        $colStops = null;
        if ($childLevel == 0 && $limit) {
            $colStops = $this->_columnBrake($child->getChildren(), $limit);
        }

        if (!empty($menuStaticBlockHtml)) {
            $html .= '<div class="level' . $childLevel . ' ' . $childrenWrapClass . ' static-block-submenu">';
            $html .= $menuStaticBlockHtml;
            $html .= '</div>';
        }
        if (!empty($mobileMenuStaticBlockHtml)) {
            $html .= '<div class="level' . $childLevel . ' ' . $childrenWrapClass . ' mobile-sub-menu static-block-submenu">';
            $html .= $mobileMenuStaticBlockHtml;
            $html .= '</div>';
        }

        if (empty($menuStaticBlockHtml) && empty($mobileMenuStaticBlockHtml)) {
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
