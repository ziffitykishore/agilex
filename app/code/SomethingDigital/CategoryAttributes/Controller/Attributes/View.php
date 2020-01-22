<?php

namespace SomethingDigital\CategoryAttributes\Controller\Attributes;

use Magento\Catalog\Model\CategoryRepository;
use Magento\Catalog\Model\Product\Attribute\Repository as ProductAttributeRepository;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\NotFoundException;
use Magento\Framework\Json\EncoderInterface;
use \Magento\Framework\View\Result\PageFactory;
use Magento\Framework\UrlFactory;
use Magento\Store\Model\StoreManagerInterface;
use SomethingDigital\ReactPlp\Helper\AttributeSorter;

class View extends \Magento\Framework\App\Action\Action
{
    protected $context;
    protected $pageFactory;
    protected $jsonEncoder;
    protected $categoryRepository;
    protected $storeManager;
    protected $productAttributeRepository;
    /**
     * @var AttributeSorter
     */
    private $attributeSorter;

    /**
     * @param Context $context
     * @param EncoderInterface $encoder
     * @param PageFactory $pageFactory
     * @param StoreManagerInterface $storeManager
     * @param CategoryRepository $categoryRepository
     * @param ProductAttributeRepository $productAttributeRepository
     * @param AttributeSorter $attributeSorter
     */
    
    public function __construct(
        Context $context,
        EncoderInterface $encoder,
        PageFactory $pageFactory,
        StoreManagerInterface $storeManager,
        CategoryRepository $categoryRepository,
        ProductAttributeRepository $productAttributeRepository,
        AttributeSorter $attributeSorter
    ) {
        $this->context = $context;
        $this->pageFactory = $pageFactory;
        $this->jsonEncoder = $encoder;
        $this->storeManager = $storeManager;
        $this->categoryRepository = $categoryRepository;
        $this->productAttributeRepository = $productAttributeRepository;
        $this->attributeSorter = $attributeSorter;
        parent::__construct($context);
    }
    
    public function execute() 
    {       
        $cid = $this->getRequest()->getParam('id');
        try {
            $category = $this->categoryRepository->get($cid, $this->storeManager->getStore()->getId());
            $tableAttributes = '';
            $filterAttributes = '';
            $listAttributes = '';

            if ($category->getFilterAttributes()) {
                $filterAttributes = preg_split('/\s+/', $category->getFilterAttributes());
                foreach ($filterAttributes as $key => $attrCode) {
                    $attr = $this->productAttributeRepository->get($attrCode);
                    if (!$attr || !$attr->getIsFilterable())
                        unset($filterAttributes[$key]);
                }
            }
            if ($category->getTableAttributes()) {
                $tableAttributes = preg_split('/\s+/', $category->getTableAttributes());
                foreach ($tableAttributes as $key => $attrCode) {
                    $attr = $this->productAttributeRepository->get($attrCode);
                    if (!$attr || !$attr->getIncludeInTable())
                        unset($tableAttributes[$key]);
                }
                $tableAttributes = $this->attributeSorter->sort($tableAttributes, AttributeSorter::CUSTOM_ATTRIBUTES);
            }
            if ($category->getListAttributes()) {
                $listAttributes = preg_split('/\s+/', $category->getListAttributes());
                foreach ($listAttributes as $key => $attrCode) {
                    $attr = $this->productAttributeRepository->get($attrCode);
                    if (!$attr || !$attr->getIncludeInList())
                        unset($listAttributes[$key]);
                }
            }

            $data = [
                'filter_attributes' => $filterAttributes,
                'table_attributes' => $tableAttributes,
                'list_attributes' => $listAttributes
            ];

            $this->getResponse()->representJson($this->jsonEncoder->encode($data))->setHeader('Cache-Control', 'max-age=86400, public');
        } catch (NoSuchEntityException $e) {
            throw new NotFoundException(__('Category does not exist.'));
        }
    }
}
