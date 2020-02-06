<?php
namespace SomethingDigital\CategoryAttributes\Controller\Grouping;
 
use Magento\Framework\UrlFactory;
use Magento\Framework\Exception\NoSuchEntityException;
 
class View extends \Magento\Framework\App\Action\Action
{
    protected $context;
    protected $pageFactory;
    protected $jsonEncoder;
    protected $categoryRepository;
    protected $storeManager;
    protected $blockRepository;
    protected $filterProvider;
    protected $productAttributeRepository;

    /**
     * @param Context                    $context
     * @param EncoderInterface           $encoder
     * @param PageFactory                $pageFactory
     * @param StoreManagerInterface      $storeManager
     * @param CategoryRepository         $categoryRepository
     * @param BlockRepositoryInterface   $blockRepository
     * @param FilterProvider             $filterProvider
     * @param ProductAttributeRepository $productAttributeRepository
     */
    
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Json\EncoderInterface $encoder,
        \Magento\Framework\View\Result\PageFactory $pageFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Model\CategoryRepository $categoryRepository,
        \Magento\Cms\Api\BlockRepositoryInterface $blockRepository,
        \Magento\Cms\Model\Template\FilterProvider $filterProvider,
        \Magento\Catalog\Model\Product\Attribute\Repository $productAttributeRepository
    ) {
        $this->context = $context;
        $this->pageFactory = $pageFactory;
        $this->jsonEncoder = $encoder;
        $this->storeManager = $storeManager;
        $this->categoryRepository = $categoryRepository;
        $this->blockRepository = $blockRepository;
        $this->filterProvider = $filterProvider;
        $this->productAttributeRepository = $productAttributeRepository;
        parent::__construct($context);
    }
    
    public function execute() 
    {       
        $cid = $this->getRequest()->getParam('id');
        try {
            $category = $this->categoryRepository->get($cid, $this->storeManager->getStore()->getId());
        } catch (NoSuchEntityException $e) {
            throw new \Magento\Framework\Exception\NotFoundException(__('Category does not exists.'));
        }
        
        $attrArray = [
            $category->getGroupingAttribute1(),
            $category->getGroupingAttribute2(),
            $category->getGroupingAttribute3()
        ];
        $data = [];

        foreach ($attrArray as $key => $attrCode) {
            if (!empty($attrCode)) {
                try {
                    $attrOptions = $this->productAttributeRepository->get($attrCode)->getOptions();
                } catch (NoSuchEntityException $e) {
                    continue;
                }

                foreach ($attrOptions as $option) {
                    if (trim($option->getLabel()) != '') {
                        $optionCode = strtolower(str_replace(' ', '-', $option->getLabel()));
                        $optionExists = true;
                        try {
                            $optionHtml = $this->blockRepository->getById('grouping_' . $attrCode . '_' . $optionCode);
                        } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
                            $optionExists = false;
                        }
                        if ($optionExists) {
                            $filteredOptionHtml = $this->filterProvider->getPageFilter()->filter($optionHtml->getContent());
                            $data[$attrCode][$optionCode] = $filteredOptionHtml;
                        }
                    }
                }
            }
        }

        $this->getResponse()->representJson($this->jsonEncoder->encode($data))->setHeader('Cache-Control', 'max-age=86400, public');
    }
}