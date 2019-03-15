<?php
/**
 * RocketWeb
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @category  RocketWeb
 * @package   RocketWeb_ShoppingFeeds
 * @copyright Copyright (c) 2016 RocketWeb (http://rocketweb.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author    Rocket Web Inc.
 */

namespace RocketWeb\ShoppingFeeds\Model\Product\Mapper\Generic\Simple;

use \RocketWeb\ShoppingFeeds\Model\Product\Mapper\MapperAbstract;

class AdditionalImageLink extends MapperAbstract
{
    protected $galleryHandler;

    public function __construct(
        \RocketWeb\ShoppingFeeds\Model\Logger $logger,
        \Magento\Catalog\Model\Product\Gallery\ReadHandler $galleryHandler
    )
    {
        $this->galleryHandler = $galleryHandler;
        parent::__construct($logger);
    }

    public function map(array $params = array())
    {
        /** @var \Magento\Catalog\Model\Product $product */
        $product = $this->getAdapter()->getProduct();
        $imageType = !empty($params['param']) ? $params['param'] : 'image';

        if (($baseImage = $product->getData($imageType)) != "") {
            $baseImage = $this->getAdapter()->getData('images_url_prefix') . '/' . ltrim($baseImage, '/');
        }

        $this->galleryHandler->execute($product);
        $mediaGalleryImages = $product->getMediaGalleryImages();

        $urls = array();
        $c = 0;
        if (is_array($mediaGalleryImages) || $mediaGalleryImages instanceof \Magento\Framework\Data\Collection) {
            foreach ($mediaGalleryImages as $image) {
                if (++$c > 10) {
                    break;
                }
                if ($image['disabled']) {
                    continue;
                }
                $img = $this->getAdapter()->getData('images_url_prefix') . '/' . ltrim($image['file'], '/');

                if (strcmp($baseImage, $img) == 0) {
                    continue;
                }

                $urls[] = $img;
            }
        }
        $cell = implode(",", $urls);
        $this->getAdapter()->getFilter()->findAndReplace($cell, $params['column']);
        return $cell;
    }
}