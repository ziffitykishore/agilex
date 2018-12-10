<?php
/*
 * Ziffity_Banners
 */
namespace Ziffity\Banners\Controller\Adminhtml;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Stdlib\DateTime\Filter\Date;
use Ziffity\Banners\Api\ImageRepositoryInterface;

abstract class Image extends Action
{
    /**
     * @var string
     */
    const ACTION_RESOURCE = 'Ziffity_Banners::image';

    /**
     * Image repository
     *
     * @var ImageRepositoryInterface
     */
    public $imageRepository;

    /**
     * Core registry
     *
     * @var Registry
     */
    public $coreRegistry;

    /**
     * Result Page Factory
     *
     * @var PageFactory
     */
    public $resultPageFactory;

    /**
     * Date filter
     *
     * @var Date
     */
    public $dateFilter;

    /**
     * Sliders constructor.
     *
     * @param Registry $registry
     * @param ImageRepositoryInterface $imageRepository
     * @param PageFactory $resultPageFactory
     * @param Date $dateFilter
     * @param Context $context
     */
    public function __construct(
        Registry $registry,
        ImageRepositoryInterface $imageRepository,
        PageFactory $resultPageFactory,
        Date $dateFilter,
        Context $context
    ) {
        parent::__construct($context);
        $this->coreRegistry         = $registry;
        $this->imageRepository      = $imageRepository;
        $this->resultPageFactory    = $resultPageFactory;
        $this->dateFilter = $dateFilter;
    }
}
