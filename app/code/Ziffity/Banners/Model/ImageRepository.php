<?php
/*
 * Ziffity_Banners
 */
namespace Ziffity\Banners\Model;

use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\StateException;
use Magento\Framework\Exception\ValidatorException;
use Magento\Framework\Exception\NoSuchEntityException;
use Ziffity\Banners\Api\ImageRepositoryInterface;
use Ziffity\Banners\Api\Data\ImageInterface;
use Ziffity\Banners\Api\Data\ImageInterfaceFactory;
use Ziffity\Banners\Model\ResourceModel\Image as ResourceImage;

class ImageRepository implements ImageRepositoryInterface
{
    /**
     * @var array
     */
    public $instances = [];
    /**
     * @var ResourceImage
     */
    public $resource;

    /**
     * @var ImageInterfaceFactory
     */
    public $imgInterfaceFactory;

    /**
     * @var DataObjectHelper
     */
    public $dataObjectHelper;

    public function __construct(
        ResourceImage $resource,
        ImageInterfaceFactory $imgInterfaceFactory,
        DataObjectHelper $dataObjectHelper
    ) {
        $this->resource = $resource;
        $this->imgInterfaceFactory = $imgInterfaceFactory;
        $this->dataObjectHelper = $dataObjectHelper;
    }

    /**
     * @param ImageInterface $image
     * @return ImageInterface
     * @throws CouldNotSaveException
     */
    public function save(ImageInterface $image)
    {
        try {
            /** @var ImageInterface|\Magento\Framework\Model\AbstractModel $image */
            $this->resource->save($image);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the image: %1',
                $exception->getMessage()
            ));
        }
        return $image;
    }

    /**
     * Get image record
     *
     * @param $imageId
     * @return mixed
     * @throws NoSuchEntityException
     */
    public function getById($imageId)
    {
        if (!isset($this->instances[$imageId])) {
            $image = $this->imgInterfaceFactory->create();
            $this->resource->load($image, $imageId);
            if (!$image->getId()) {
                throw new NoSuchEntityException(__('Requested image doesn\'t exist'));
            }
            $this->instances[$imageId] = $image;
        }
        return $this->instances[$imageId];
    }

    /**
     * @param ImageInterface $image
     * @return bool
     * @throws CouldNotSaveException
     * @throws StateException
     */
    public function delete(ImageInterface $image)
    {
        /** @var \Ziffity\Banners\Api\Data\ImageInterface|\Magento\Framework\Model\AbstractModel $image */
        $imageId = $image->getId();
        try {
            unset($this->instances[$imageId]);
            $this->resource->delete($image);
        } catch (ValidatorException $e) {
            throw new CouldNotSaveException(__($e->getMessage()));
        } catch (\Exception $e) {
            throw new StateException(
                __('Unable to remove image %1', $imageId)
            );
        }
        unset($this->instances[$imageId]);
        return true;
    }

    /**
     * @param $imageId
     * @return bool
     */
    public function deleteById($imageId)
    {
        $image = $this->getById($imageId);
        return $this->delete($image);
    }
}
