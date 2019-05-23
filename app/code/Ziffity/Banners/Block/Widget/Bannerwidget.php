<?php

namespace Ziffity\Banners\Block\Widget;

use Magento\Framework\View\Element\Template;
use Magento\Widget\Block\BlockInterface;
use Magento\Backend\Block\Widget\Context;
use Ziffity\Banners\Model\ResourceModel\Image\Collection as BannerCollection;
use Ziffity\Banners\Model\ResourceModel\Image\CollectionFactory as BannerCollectionFactory;

class Bannerwidget extends Template implements BlockInterface
{

    /**
     *
     * @var Banner slider template
     */
    public $_template = "widget/banners.phtml";

    /**
     * @var Context
     */
    public $context;

    /**
     * @var BannerCollection
     */
    public $bannercollection;

    /**
     * @param Context $context
     * @param ImageRepositoryInterface $imageRepository
     */
    public function __construct(
        Context $context,
        BannerCollectionFactory $bannercollection,
        array $data = []
    ) {
        $this->context = $context;
        $this->bannercollection = $bannercollection;
        parent::__construct($context, $data);
    }

    /**
     *
     * @return Array
     */
    public function getBanners()
    {
        $bannerCollection = $this->bannercollection->create();
        $bannerCollection->addFieldToSelect('*')
                        ->addFieldToFilter('image_code', array('eq' => 'banner'))
                        ->setOrder('position', 'ASC')->load();
        return $bannerCollection->getItems();
    }
}
