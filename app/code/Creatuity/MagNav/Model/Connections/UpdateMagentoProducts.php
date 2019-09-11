<?php
/**
 * Created by PhpStorm.
 * User: chatfield
 * Date: 5/18/17
 * Time: 3:02 PM
 */

namespace Creatuity\MagNav\Model\Connections;


class UpdateMagentoProducts implements \Creatuity\MagNav\Api\ConnectionInterface {

    const SERVICE_URI = 'Page/Item';

    const TITLE = 'Product Items';

    const CONNECTION_ID = 'PRODUCT_CONNECTION';

    private $baseUri;


    public function __construct(){

    }

    /**
     * @param array $parameters
     * @return string
     */
    public function getServiceUri(array $parameters = []) {

        return self::SERVICE_URI;

    }

    /**
     * @return string
     */
    public function getTitle() {

        return self::TITLE;

    }

    /**
     * @return string
     */
    public function getId(){

        return self::CONNECTION_ID;

    }

    /**
     * @param $baseUri
     * @return null
     */
    public function setBaseUri($baseUri) {

        $this->baseUri = $baseUri;

    }

    /**
     * @param $response
     * @param array $parameters
     * @return null
     */
    public function processResponse($response, array $parameters = [])
    {
        // TODO: Implement processResponse() method.
    }
}