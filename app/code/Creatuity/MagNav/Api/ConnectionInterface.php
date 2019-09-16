<?php
/**
 * Created by PhpStorm.
 * User: chatfield
 * Date: 5/18/17
 * Time: 2:10 PM
 */

namespace Creatuity\MagNav\Api;


interface ConnectionInterface {

    /**
     * @param array $parameters
     * @return string
     */
    public function getServiceUri(array $parameters = []);

    /**
     * @return string
     */
    public function getTitle();

    /**
     * @return string
     */
    public function getId();

    /**
     * @param $baseUri
     * @return null
     */
    public function setBaseUri($baseUri);

    /**
     * @param $response
     * @param array $parameters
     * @return null
     */
    public function processResponse($response, array $parameters = []);

}