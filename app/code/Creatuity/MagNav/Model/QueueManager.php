<?php
/**
 * Created by PhpStorm.
 * User: chatfield
 * Date: 5/15/17
 * Time: 10:12 AM
 */

namespace Creatuity\MagNav\Model;


class QueueManager {

    /***
     * @var \Magento\Framework\MessageQueue\PublisherInterface
     */
    private $publisher;

    /***
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    private $coreConfig;

    const PRIORITY_TOPIC = "creatuity.magnav.priority";

    const LAZY_TOPIC = "creatuity.magnav.lazy";

    const CONNECTION_ERROR = "connection-error";

    public function __construct(
        \Magento\Framework\MessageQueue\PublisherInterface $publisher,
        \Magento\Framework\App\Config\ScopeConfigInterface $coreConfig
    ){

        $this->publisher = $publisher;
        $this->coreConfig = $coreConfig;

    }

    public function pushToQueue(\Creatuity\MagNav\Api\Data\MessageInterface $message)
    {

        $message->setTries($message->getTries() + 1);

        if ($message->getTries() > $this->maxTries($message->getTopic())) {
            $this->bumpMessage($message);
        }

        if ($message->getTopic() != self::CONNECTION_ERROR) {
            $this->publisher->publish($message->getTopic(), $message);
        }

    }

    public function bumpMessage(\Creatuity\MagNav\Api\Data\MessageInterface $message){

        switch($message->getTopic()){
            case self::PRIORITY_TOPIC:
                $message->setTopic(self::LAZY_TOPIC);
                break;
            case self::LAZY_TOPIC:
                $message->setTopic(self::CONNECTION_ERROR);
                $this->logMessage($message);
                break;
        }

    }

    public function logMessage(\Creatuity\MagNav\Api\Data\MessageInterface $message){

        //todo: add system to log messages that cannot be sent

    }

    public function maxTries($topic){

        $path = 'creatuity/magnav/';
        switch ($topic){
            case self::PRIORITY_TOPIC:
                $path = $path . 'priority_tries';
                break;

            case self::LAZY_TOPIC:
                $path = $path . 'lazy_tries';
                break;
        }

        return $this->coreConfig->getValue($path, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);

    }

}