<?php
/*
 * Ziffity_Banners
 */
namespace Ziffity\Banners\Ui\DataProvider\Image\Form\Modifier;

use Magento\Ui\DataProvider\Modifier\ModifierInterface;
use Ziffity\Banners\Model\ResourceModel\Image\CollectionFactory;

class ImageData implements ModifierInterface
{
    /**
     * @var \Ziffity\Banners\Model\ResourceModel\Image\Collection
     */
    public $collection;

    /**
     * @param CollectionFactory $imgCollectionFactory
     */
    public function __construct(
        CollectionFactory $imgCollectionFactory
    ) {
        $this->collection = $imgCollectionFactory->create();
    }

    /**
     * @param array $meta
     * @return array
     */
    public function modifyMeta(array $meta)
    {
        return $meta;
    }

    /**
     * @SuppressWarnings(PHPMD.CamelCaseVariableName)
     * @param array $data
     * @return array|mixed
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function modifyData(array $data)
    {
        $items = $this->collection->getItems();
        /** @var $image \Ziffity\Banners\Model\Image */
        foreach ($items as $image) {
            $_data = $image->getData();
            if (isset($_data['image'])) {
                $imageArr = [];
                $imageArr[0]['name'] = 'Image';
                $imageArr[0]['url'] = $image->getImageUrl();
                $_data['image'] = $imageArr;
            }
            $image->setData($_data);
            $data[$image->getId()] = $_data;
        }
        return $data;
    }
}
