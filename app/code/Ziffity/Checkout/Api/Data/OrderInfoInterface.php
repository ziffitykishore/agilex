<?php
 
namespace Ziffity\Checkout\Api\Data;

/**
 * Interface OrderCommentInterface
 * @api
 */
interface OrderInfoInterface
{

    /**
     * @return string|null
     */
    public function getStoreLocation();

    /**
     * @param string $comment
     * @return null
     */
    public function setStoreLocation($location);

    /**
     * @return string|null
     */
    public function getStoreAddress();

    /**
     * @param string $comment
     * @return null
     */
    public function setStoreAddress($comment);
}
