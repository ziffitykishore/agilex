<?php
/**
 * Created by PhpStorm.
 * User: chatfield
 * Date: 5/22/17
 * Time: 10:32 AM
 */

namespace Creatuity\MagNav\Model;


class MessageFactory {

    /***
     * @var \Magento\Framework\ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @var string
     */
    private $messageClass;

    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        $messageClass
    ){

        $this->objectManager = $objectManager;
        $this->messageClass = $messageClass;

    }

    public function create(\Creatuity\MagNav\Api\ConnectionInterface $connection, array $parameters = []){

        $messageProperties = [
            'connection' => $connection,
            'topic' => \Creatuity\MagNav\Model\QueueManager::PRIORITY_TOPIC,
            'parameters' => $parameters
        ];

        $message = $this->objectManager->create($this->messageClass, $messageProperties);

        return $message;

    }

}