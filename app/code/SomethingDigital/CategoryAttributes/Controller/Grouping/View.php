<?php
namespace SomethingDigital\CategoryAttributes\Controller\Grouping;
 
use Magento\Framework\UrlFactory;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\NotFoundException;
 
class View extends \Magento\Framework\App\Action\Action
{
    protected $context;
    protected $pageFactory;
    protected $jsonEncoder;
    protected $categoryRepository;
    protected $storeManager;
    protected $blockRepository;
    protected $filterGroup;
    protected $filterBuilder;
    protected $filterProvider;
    protected $productAttributeRepository;
    protected $searchCriteriaBuilder;

    /**
     * @param Context                    $context
     * @param EncoderInterface           $encoder
     * @param PageFactory                $pageFactory
     * @param StoreManagerInterface      $storeManager
     * @param CategoryRepository         $categoryRepository
     * @param BlockRepositoryInterface   $blockRepository
     * @param FilterGroup             $filterGroup
     * @param FilterBuilder             $filterBuilder
     * @param FilterProvider             $filterProvider
     * @param ProductAttributeRepository $productAttributeRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     */
    
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Json\EncoderInterface $encoder,
        \Magento\Framework\View\Result\PageFactory $pageFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Model\CategoryRepository $categoryRepository,
        \Magento\Cms\Api\BlockRepositoryInterface $blockRepository,
        \Magento\Framework\Api\Search\FilterGroup $filterGroup,
        \Magento\Framework\Api\FilterBuilder $filterBuilder,
        \Magento\Cms\Model\Template\FilterProvider $filterProvider,
        \Magento\Catalog\Model\Product\Attribute\Repository $productAttributeRepository,
        \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder
    ) {
        $this->context = $context;
        $this->pageFactory = $pageFactory;
        $this->jsonEncoder = $encoder;
        $this->storeManager = $storeManager;
        $this->categoryRepository = $categoryRepository;
        $this->blockRepository = $blockRepository;
        $this->filterGroup = $filterGroup;
        $this->filterBuilder = $filterBuilder;
        $this->filterProvider = $filterProvider;
        $this->productAttributeRepository = $productAttributeRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        parent::__construct($context);
    }
    
    public function execute() 
    {       
        $cid = $this->getRequest()->getParam('id');
        try {
            $category = $this->categoryRepository->get($cid, $this->storeManager->getStore()->getId());
        } catch (NoSuchEntityException $e) {
            throw new NotFoundException(__('Category does not exists.'));
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

                $filter = $this->filterBuilder
                    ->setField('identifier')
                    ->setConditionType('like')
                    ->setValue('grouping_' . $cid . '_' . $attrCode . '_%')
                    ->create();

                $this->searchCriteriaBuilder->addFilters([$filter]);
                $searchCriteria = $this->searchCriteriaBuilder->create();

                $blocks = $this->blockRepository
                    ->getList($searchCriteria)
                    ->getItems();

                if (!$blocks) {
                    $filter = $this->filterBuilder
                        ->setField('identifier')
                        ->setConditionType('like')
                        ->setValue('grouping_' . $attrCode . '_%')
                        ->create();

                    $this->searchCriteriaBuilder->addFilters([$filter]);
                    $searchCriteria = $this->searchCriteriaBuilder->create();

                    $blocks = $this->blockRepository
                        ->getList($searchCriteria)
                        ->getItems();
                }

                foreach ($blocks as $key => $block) {
                    $filteredOptionHtml = $this->filterProvider->getPageFilter()->filter($block->getContent());
                    $data[$attrCode][$block->getIdentifier()] = $filteredOptionHtml;
                }
            }
        }

        $this->getResponse()->representJson($this->jsonEncoder->encode($data))->setHeader('Cache-Control', 'max-age=86400, public');
    }
}