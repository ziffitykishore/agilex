<?php
namespace SomethingDigital\CategoryAttributes\Controller\Facets;

use Magento\Framework\App\Action\Context;
use Magento\Framework\Json\EncoderInterface;
use Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Catalog\Model\CategoryRepository;
use Magento\Framework\Exception\NotFoundException;
use Magento\Framework\Exception\NoSuchEntityException;
 
class View extends \Magento\Framework\App\Action\Action
{
    protected $context;
    protected $jsonEncoder;
    protected $productAttributeCollectionFactory;
    protected $storeManager;
    protected $categoryRepository;

    /**
     * @param Context                  $context
     * @param EncoderInterface         $encoder
     * @param CollectionFactory        $productAttributeCollectionFactory
     * @param StoreManagerInterface    $storeManager
     * @param CategoryRepository       $categoryRepository
     */
    
    public function __construct(
        Context $context,
        EncoderInterface $encoder,
        CollectionFactory $productAttributeCollectionFactory,
        StoreManagerInterface $storeManager,
        CategoryRepository $categoryRepository
    ) {
        $this->context = $context;
        $this->jsonEncoder = $encoder;
        $this->productAttributeCollectionFactory = $productAttributeCollectionFactory;
        $this->storeManager = $storeManager;
        $this->categoryRepository = $categoryRepository;
        parent::__construct($context);
    }
    
    public function execute() 
    {
        $cid = $this->getRequest()->getParam('id');
        $filterAttributes = [];
        try {
            $productAttributes = $this->productAttributeCollectionFactory->create();
            if ($cid != 'search') {
                $category = $this->categoryRepository->get($cid, $this->storeManager->getStore()->getId());
                if ($category->getFilterAttributes()) {
                    $filterAttributes = preg_split('/\s+/', trim($category->getFilterAttributes()));
                }
                $collection = $productAttributes->addFieldToFilter('is_filterable', true);
                if (!empty($filterAttributes)) {
                    $collection->addFieldToFilter('attribute_code', array('in' => $filterAttributes));
                }
            } else {
                $collection = $productAttributes->addFieldToFilter('is_filterable_in_search', true);
            }
            $attrOptionsData = [];
            foreach ($collection as $key => $attr) {
                $attrOptions = $attr->getOptions();
                $i = 0;
                foreach ($attrOptions as $option) {
                    if ($option->getValue()) {
                        $attrOptionsData[] = [
                            'attribute_code' => $attr->getAttributeCode(),
                            'option_label' => $option->getLabel(),
                            'sort_order' => $i++
                        ];
                    }
                }
            }
            
            $this->getResponse()->representJson($this->jsonEncoder->encode($attrOptionsData))->setHeader('Cache-Control', 'max-age=86400, public');
        } catch (NoSuchEntityException $e) {
            throw new NotFoundException(__('Category does not exist.')); 
        }
       
    }
}
