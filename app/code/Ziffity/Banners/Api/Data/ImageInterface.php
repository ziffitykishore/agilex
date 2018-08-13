<?php
/*
 * Ziffity_Banners
 */
namespace Ziffity\Banners\Api\Data;

/**
 * @api
 */
interface ImageInterface
{
    const IMAGE_ID          = 'image_id';
    const IMAGE             = 'image';
    const LINK              = "link";
    const POSITION           = "position";
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
     * Get Link
     *
     * @return String
     */
    public function getLink();

    /**
     * Get Position
     *
     * @return Int|null
     */
    public function getPosition();

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

    /**
     * Set Link
     *
     * @param type $link
     */
    public function setLink($link);

    /**
     * Set Position
     *
     * @param type $position
     */
    public function setPosition($position);
}
