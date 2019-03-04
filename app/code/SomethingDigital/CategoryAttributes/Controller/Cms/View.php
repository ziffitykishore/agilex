<?php
namespace SomethingDigital\CategoryAttributes\Controller\Cms;
 
use Magento\Framework\UrlFactory;
 
class View extends \Magento\Framework\App\Action\Action
{
    protected $context;
    protected $pageFactory;
    protected $jsonEncoder;
    protected $categoryRepository;
    protected $storeManager;
    protected $blockRepository;
    protected $filterProvider;

    /**
     * @param Context                  $context
     * @param EncoderInterface         $encoder
     * @param PageFactory              $pageFactory
     * @param StoreManagerInterface    $storeManager
     * @param CategoryRepository       $categoryRepository
     * @param BlockRepositoryInterface $blockRepository
     * @param FilterProvider           $filterProvider
     */
    
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Json\EncoderInterface $encoder,
        \Magento\Framework\View\Result\PageFactory $pageFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Model\CategoryRepository $categoryRepository,
        \Magento\Cms\Api\BlockRepositoryInterface $blockRepository,
        \Magento\Cms\Model\Template\FilterProvider $filterProvider
    ) {
        $this->context = $context;
        $this->pageFactory = $pageFactory;
        $this->jsonEncoder = $encoder;
        $this->storeManager = $storeManager;
        $this->categoryRepository = $categoryRepository;
        $this->blockRepository = $blockRepository;
        $this->filterProvider = $filterProvider;
        parent::__construct($context);
    }
    
    public function execute() 
    {       
        $cid = $this->getRequest()->getParam('id');
        if ($cid != 'search') {
            $category = $this->categoryRepository->get($cid, $this->storeManager->getStore()->getId());

            if ($bottomBlockId = $category->getStaticBlockBottom()) {
                $bottomBlock = $this->blockRepository->getById($bottomBlockId);
                $bottomBlockHtml = $this->filterProvider->getPageFilter()->filter($bottomBlock->getContent());
            } else {
                $bottomBlockHtml = '';
            }

            if ($leftBlockId = $category->getStaticBlockLeftBar()) {
                $leftBlock = $this->blockRepository->getById($leftBlockId);
                $leftBlockHtml = $this->filterProvider->getPageFilter()->filter($leftBlock->getContent());
            } else {
                $leftBlockHtml = '';
            }

            if ($cmsBlockId = $category->getLandingPage()) {
                $cmsBlock = $this->blockRepository->getById($cmsBlockId);
                $descriptionHtml = $this->filterProvider->getPageFilter()->filter($cmsBlock->getContent());
            } else {
                $descriptionHtml = $category->getDescription();
            }
        } else {
            $descriptionHtml = $this->blockRepository->getById('catalogsearch_description');
            $descriptionHtml = $this->filterProvider->getPageFilter()->filter($descriptionHtml->getContent());
            $leftBlockHtml = $this->blockRepository->getById('catalogsearch_after_sidebar');
            $leftBlockHtml = $this->filterProvider->getPageFilter()->filter($leftBlockHtml->getContent());
            $bottomBlockHtml = $this->blockRepository->getById('catalogsearch_after_content');
            $bottomBlockHtml = $this->filterProvider->getPageFilter()->filter($bottomBlockHtml->getContent());
        }

        $data = [
            'description' => $descriptionHtml, 
            'after_sidebar' => $leftBlockHtml, 
            'after_content' => $bottomBlockHtml
        ];
        
        $this->getResponse()->representJson($this->jsonEncoder->encode($data))->setHeader('Cache-Control', 'max-age=86400, public');
    }
}