<?php

declare(strict_types = 1);

namespace Earthlite\Category\Block\Adminhtml\Category\Helper\Form;

use Magento\Framework\Registry;
use Earthlite\Category\Model\ResourceModel\CategoryGallery\CollectionFactory;
use Magento\Framework\View\Element\Context;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Data\Form;
use Magento\Framework\View\Element\AbstractBlock;
/**
 * class Gallery
 */
class Gallery extends AbstractBlock
{
    /**
     *
     * @var string
     */
    protected $fieldNameSuffix = 'earthlite';
    
    /**
     *
     * @var string
     */
    protected $htmlId = 'media_gallery';
    
    /**
     *
     * @var string
     */
    protected $name = 'photo[media_gallery]';
    
    /**
     *
     * @var string
     */
    protected $image = 'image';
    
    /**
     *
     * @var string
     */
    protected $formName = 'category_form';
    
    /**
     *
     * @var StoreManagerInterface
     */
    protected $storeManager;
    
    /**
     *
     * @var type 
     */
    protected $form;
    
    /**
     *
     * @var Registry
     */
    protected $registry;
    
    /**
     *
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * Gallery constructor
     * 
     * @param Context $context
     * @param StoreManagerInterface $storeManager
     * @param Registry $registry
     * @param Form $form
     * @param CollectionFactory $collectionFactory
     * @param array $data
     */
    public function __construct(
        Context $context, 
        StoreManagerInterface $storeManager, 
        Registry $registry, 
        Form $form, 
        CollectionFactory $collectionFactory,
        $data = []
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->storeManager = $storeManager;
        $this->registry = $registry;
        $this->form = $form;
        parent::__construct($context, $data);
    }

    /**
     * 
     * @return $this
     */
    protected function _prepareLayout() {
        $this->getChildBlock('content');
        return parent::_prepareLayout();
    }

    /**
     * 
     * @return string
     */
    public function getElementHtml()
    {
        return $this->getContentHtml();
    }
    
    /**
     * 
     * @return []
     */
    public function getImages()
    {
        $categoryId = $this->getDataObject()->getId();
        /* \Earthlite\Category\Model\ResourceModel\CategoryGallery\Collection $categoryMediaCollecion */
        $categoryMediaCollection = $this->collectionFactory->create()
                ->addFieldToFilter('category_id', $categoryId);
        $images = [];
        foreach ($categoryMediaCollection as $categoryMedia) {
            $images['images'][] = [
                'value_id' => $categoryMedia->getValueId(),
                'file' => $categoryMedia->getValue(),
                'media_type' => $categoryMedia->getMediaType(),
                'entity_id' => $categoryMedia->getCategoryId(),
                'label' => $categoryMedia->getLabel(),
                'position' => $categoryMedia->getPosition(),
                'disabled' => $categoryMedia->getDisabled(),
                'image_alt' => $categoryMedia->getLabel()
            ];
        }
        return $images;
    
    }
    
    /**
     * 
     * @return string
     */
    public function getContentHtml()
    {
        $content = $this->getChildBlock('content');
        $content->setId($this->getHtmlId() . '_content')->setElement($this);
        $content->setFormName($this->formName);
        $galleryJs = $content->getJsObjectName();
        $content->getUploader()->getConfig()->setMediaGallery($galleryJs);
        return $content->toHtml();
    }
    
    /**
     * 
     * @return string
     */
    protected function getHtmlId() 
    {
        return $this->htmlId;
    }
    
    /**
     * 
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
    
    /**
     * 
     * @return string
     */
    public function getFieldNameSuffix()
    {
        return $this->fieldNameSuffix;
    }
    
    /**
     * 
     * @return string
     */
    public function getDataScopeHtmlId()
    {
        return $this->image;
    }
    
    /**
     * 
     * @return string
     */
    public function getDataObject() 
    {
        return $this->registry->registry('current_category');
    }
    
    /**
     * 
     * @return string
     */
    public function toHtml() 
    {
        return $this->getElementHtml();
    }

}
