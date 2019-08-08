<?php
namespace SomethingDigital\CategoryAttributes\Controller\Attributes;
 
use Magento\Framework\UrlFactory;
 
class View extends \Magento\Framework\App\Action\Action
{
    protected $context;
    protected $pageFactory;
    protected $jsonEncoder;
    protected $categoryRepository;
    protected $storeManager;
    protected $productAttributeRepository;

    /**
     * @param Context                    $context
     * @param EncoderInterface           $encoder
     * @param PageFactory                $pageFactory
     * @param StoreManagerInterface      $storeManager
     * @param CategoryRepository         $categoryRepository
     * @param ProductAttributeRepository $productAttributeRepository
     */
    
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Json\EncoderInterface $encoder,
        \Magento\Framework\View\Result\PageFactory $pageFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Model\CategoryRepository $categoryRepository,
        \Magento\Catalog\Model\Product\Attribute\Repository $productAttributeRepository
    ) {
        $this->context = $context;
        $this->pageFactory = $pageFactory;
        $this->jsonEncoder = $encoder;
        $this->storeManager = $storeManager;
        $this->categoryRepository = $categoryRepository;
        $this->productAttributeRepository = $productAttributeRepository;
        parent::__construct($context);
    }
    
    public function execute() 
    {       
        $cid = $this->getRequest()->getParam('id');
        try {
            $category = $this->categoryRepository->get($cid, $this->storeManager->getStore()->getId());

            $filterAttributes = preg_split('/\s+/', $category->getFilterAttributes());
            foreach ($filterAttributes as $key => $attrCode) {
                $attr = $this->productAttributeRepository->get($attrCode);
                if (!$attr || !$attr->getIsFilterable())
                    unset($filterAttributes[$key]);
            }
            $tableAttributes = preg_split('/\s+/', $category->getTableAttributes());
            foreach ($tableAttributes as $key => $attrCode) {
                $attr = $this->productAttributeRepository->get($attrCode);
                if (!$attr || !$attr->getIncludeInTable())
                    unset($tableAttributes[$key]);
            }
            $listAttributes = preg_split('/\s+/', $category->getListAttributes());
            foreach ($listAttributes as $key => $attrCode) {
                $attr = $this->productAttributeRepository->get($attrCode);
                if (!$attr || !$attr->getIncludeInList())
                    unset($listAttributes[$key]);
            }

            $data = [
                'filter_attributes' => $filterAttributes,
                'table_attributes' => $tableAttributes,
                'list_attributes' => $listAttributes
            ];

            $this->getResponse()->representJson($this->jsonEncoder->encode($data))->setHeader('Cache-Control', 'max-age=86400, public');
        } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
            throw new \Magento\Framework\Exception\NotFoundException(__('Category does not exists.')); 
        }
    }
}