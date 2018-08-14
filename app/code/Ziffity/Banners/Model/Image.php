<?php
/*
 * Ziffity_Banners
 */
namespace Ziffity\Banners\Model;

use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;
use Magento\Framework\Exception\LocalizedException;
use Ziffity\Banners\Api\Data\ImageInterface;

class Image extends AbstractModel implements ImageInterface
{
    /**
     * Cache tag
     */
    const CACHE_TAG = 'ziffity_banners_image';

    /**
     * @var UploaderPool
     */
    public $uploaderPool;

    /**
     * Sliders constructor.
     * @param Context $context
     * @param Registry $registry
     * @param UploaderPool $uploaderPool
     * @param AbstractResource|null $resource
     * @param AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        UploaderPool $uploaderPool,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
        $this->uploaderPool    = $uploaderPool;
    }

    /**
     * Initialise resource model
     * @SuppressWarnings(PHPMD.CamelCaseMethodName)
     * @codingStandardsIgnoreStart
     */
    protected function _construct()
    {
        // @codingStandardsIgnoreEnd
        $this->_init('Ziffity\Banners\Model\ResourceModel\Image');
    }

    /**
     * Get cache identities
     *
     * @return array
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    /**
     * Get image
     *
     * @return string
     */
    public function getImage()
    {
        return $this->getData(ImageInterface::IMAGE);
    }

    /**
     * Set image
     *
     * @param $image
     * @return $this
     */
    public function setImage($image)
    {
        return $this->setData(ImageInterface::IMAGE, $image);
    }

    /**
     * Get link
     *
     * @return string
     */
    public function getLink()
    {
        return $this->getData(ImageInterface::LINK);
    }

    /**
     * Get Position
     *
     * @return int
     */
    public function getPosition()
    {
        return $this->getData(ImageInterface::POSITION);
    }

    /**
     * Set link
     *
     * @param $link
     * @return $this
     */
    public function setLink($link)
    {
        return $this->setData(ImageInterface::LINK, $link);
    }

    /**
     * Set Position
     *
     * @param $position
     * @return $this
     */
    public function setPosition($position)
    {
        return $this->setData(ImageInterface::POSITION, $position);
    }

    /**
     * Get image URL
     *
     * @return bool|string
     * @throws LocalizedException
     */
    public function getImageUrl()
    {
        $url = false;
        $image = $this->getImage();
        if ($image) {
            if (is_string($image)) {
                $uploader = $this->uploaderPool->getUploader('image');
                $url = $uploader->getBaseUrl().$uploader->getBasePath().$image;
            } else {
                throw new LocalizedException(
                    __('Something went wrong while getting the image url.')
                );
            }
        }
        return $url;
    }
}