<?php

namespace Ziffity\Banners\Api\Data;

/**
 * @api
 */
interface ImageInterface
{
    const IMAGE_ID          = 'image_id';
    const IMAGE             = 'image';
    const IMAGE_CODE        = 'image_code';
    const IMAGE_POSITION    = 'position';
    const LINK              = 'link';

    /**
     * Get ID
     *
     * @return int|null
     */
    public function getId();

    /**
     * Get image
     *
     * @return string
     */
    public function getImage();


    /**
     * Set ID
     *
     * @param $id
     * @return ImageInterface
     */
    public function setId($id);

    /**
     * Set image
     *
     * @param $image
     * @return ImageInterface
     */
    public function setImage($image);


    public function getImageCode();

    public function setImageCode($imageCode);
    
    public function getPosition();
    
    public function setPosition($position);
    
    public function getLink();
    
    public function setLink($link);
}
