<?php
/**
 * Created by PhpStorm.
 * User: chatfield
 * Date: 4/19/17
 * Time: 1:18 PM
 */

namespace Creatuity\MagNav\Model;

class ConnectionManager {

    /***
     * @var \Creatuity\MagNav\Api\ConnectionInterface[]
     */
    private $connections = [];

    /***
     * @var \Creatuity\MagNav\Model\QueueManager
     */
    private $queueManager;

    /***
     * @var \Creatuity\MagNav\Model\RabbitMessageFactory
     */
    private $messageFactory;

    /***
     * @var \Creatuity\MagNav\Model\Connector\SoapClientFactory
     */
    private $soapClientFactory;

    public function __construct(
        \Creatuity\MagNav\Model\QueueManager $queueManager,
        \Creatuity\MagNav\Model\MessageFactory $messageFactory,
        \Creatuity\MagNav\Model\Connector\SoapClientFactory $soapClientFactory,
        array $connections
    ){

        $this->queueManager = $queueManager;
        $this->messageFactory = $messageFactory;
        $this->soapClientFactory = $soapClientFactory;
        $this->connections = $connections;

    }

    public function activateConnection($connectionId, $parameters = []) {

        $connection = $this->connections[$connectionId];

        if(!isset($connection)) {
            return;
        }

        $connection->setBaseUri($this->baseUri());

        $message = $this->messageFactory->create($connection, $parameters);
        $this->queueManager->pushToQueue($message);

    }

    public function consumeMessage(\Creatuity\MagNav\Api\Data\MessageInterface $message){

        //todo : initialize connector code here

        $connection = $message->getConnection();
        $parameters = $message->getParameters();
        $uri = $connection->getServiceUri($parameters);

        //todo : activate connector here
        //$response = $connector->activate($uri);

        $response = null;

        if(!$this->responseSuccess($response)){
            $this->queueManager->pushToQueue($message);
            return;
        }

        $connection->processResponse($response, $parameters);

    }

    public function responseSuccess($response){

        //todo : check if response is success or failure message
        return true;

    }

    public function baseUri(){

        //todo : get base url out of database

        return 'example.com';

    }

}