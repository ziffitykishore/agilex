<?php
/**
 * Created by PhpStorm.
 * User: chatfield
 * Date: 5/22/17
 * Time: 10:01 AM
 */

namespace Creatuity\MagNav\Model;


class RabbitMessage implements \Creatuity\MagNav\Api\Data\MessageInterface {

    /**
     * @var \Creatuity\MagNav\Api\ConnectionInterface
     */
    private $connection;

    /**
     * @var int
     */
    private $tries = 0;

    /**
     * @var string
     */
    private $topic;

    /**
     * @var array
     */
    private $parameters;

    public function __construct(
        \Creatuity\MagNav\Api\ConnectionInterface $connection,
        $topic,
        array $parameters
    ){

        $this->connection = $connection;
        $this->topic = $topic;
        $this->parameters = $parameters;

    }

    /**
     * @return string
     */
    public function getTopic() {

        return $this->topic;

    }

    /**
     * @return int
     */
    public function getTries(){

        return $this->tries;

    }

    /**
     * @return array
     */
    public function getParameters(){

        return $this->parameters;

    }

    /**
     * @return \Creatuity\MagNav\Api\ConnectionInterface
     */
    public function getConnection(){

        return $this->connection;

    }

    /**
     * @param $topic
     * @return string
     */
    public function setTopic($topic){

        $this->topic = $topic;

    }

    /**
     * @param $tries
     * @return null
     */
    public function setTries($tries){

        $this->tries = $tries;

    }

}