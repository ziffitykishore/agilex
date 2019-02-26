<?php
namespace SomethingDigital\CategoryAttributes\Controller\Cms;
 
use Magento\Framework\UrlFactory;
 
class View extends \Magento\Framework\App\Action\Action
{
    protected $_context;
    protected $_pageFactory;
    protected $_jsonEncoder;
    
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Json\EncoderInterface $encoder,
        \Magento\Framework\View\Result\PageFactory $pageFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Model\CategoryRepository $categoryRepository,
        \Magento\Cms\Api\BlockRepositoryInterface $blockRepository,
        \Magento\Cms\Model\Template\FilterProvider $filterProvider
    ) {
        $this->_context = $context;
        $this->_pageFactory = $pageFactory;
        $this->_jsonEncoder = $encoder;
        $this->_storeManager = $storeManager;
        $this->categoryRepository = $categoryRepository;
        $this->blockRepository = $blockRepository;
        $this->_filterProvider = $filterProvider;
        parent::__construct($context);
    }
    
    public function execute() 
    {       

        $cid = $this->getRequest()->getParam('id');
        $category = $this->categoryRepository->get($cid, $this->_storeManager->getStore()->getId());

        if($bottomBlockId = $category->getStaticBlockBottom()) {
            $bottomBlock = $this->blockRepository->getById($bottomBlockId);
            $bottomBlockHtml = $this->_filterProvider->getPageFilter()->filter($bottomBlock->getContent());
        } else {
            $bottomBlockHtml = '';
        }

        if($leftBlockId = $category->getStaticBlockLeftBar()) {
            $leftBlock = $this->blockRepository->getById($leftBlockId);
            $leftBlockHtml = $this->_filterProvider->getPageFilter()->filter($leftBlock->getContent());
        } else {
            $leftBlockHtml = '';
        }

        if($cmsBlockId = $category->getLandingPage()) {
            $cmsBlock = $this->blockRepository->getById($cmsBlockId);
            $description = $this->_filterProvider->getPageFilter()->filter($cmsBlock->getContent());
        } else {
            $description = $category->getDescription();
        }

        $data = [
            'description' => $description, 
            'after_sidebar' => $leftBlockHtml, 
            'after_content' => $bottomBlockHtml
        ];
        
        
        $this->getResponse()->representJson($this->_jsonEncoder->encode($data))->setHeader('Cache-Control', 'max-age=86400, public');
        return;
    }
}
?>