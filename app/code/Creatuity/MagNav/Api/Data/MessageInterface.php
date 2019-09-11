<?php

namespace Creatuity\MagNav\Api\Data;

interface MessageInterface {

    /**
     * @return string
     */
    public function getTopic();

    /**
     * @return int
     */
    public function getTries();

    /**
     * @return array
     */
    public function getParameters();

    /**
     * @return \Creatuity\MagNav\Api\ConnectionInterface
     */
    public function getConnection();

    /**
     * @param $topic
     * @return string
     */
    public function setTopic($topic);

    /**
     * @param $tries
     * @return null
     */
    public function setTries($tries);

}