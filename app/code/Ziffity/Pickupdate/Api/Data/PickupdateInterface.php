<?php

namespace Ziffity\Pickupdate\Api\Data;

interface PickupdateInterface
{
    /**#@+
     * Constants defined for keys of data array
     */
    const PICKUPDATE_ID = 'pickupdate_id';
    const ORDER_ID = 'order_id';
    const INCREMENT_ID = 'increment_id';
    const DATE = 'date';
    const TIME = 'time';
    const COMMENT = 'comment';
    const REMINDER = 'reminder';
    const TINTERVAL_ID = 'tinterval_id';
    const ACTIVE = 'active';
    /**#@-*/

    /**
     * Returns Pickupdate ID
     *
     * @return int
     */
    public function getPickupdateId();

    /**
     * @param int $pickupdateId
     *
     * @return $this
     */
    public function setPickupdateId($pickupdateId);

    /**
     * Returns Order ID
     *
     * @return int
     */
    public function getOrderId();

    /**
     * @param int $orderId
     *
     * @return $this
     */
    public function setOrderId($orderId);

    /**
     * Returns Order IncrementId
     *
     * @return string
     */
    public function getIncrementId();

    /**
     * @param string $incrementId
     *
     * @return $this
     */
    public function setIncrementId($incrementId);

    /**
     * Returns date
     *
     * @return string
     */
    public function getDate();

    /**
     * @param string $date
     *
     * @return $this
     */
    public function setDate($date);

    /**
     * Returns Time
     *
     * @return string
     */
    public function getTime();

    /**
     * @param string $time
     *
     * @return $this
     */
    public function setTime($time);

    /**
     * Returns comment
     *
     * @return string
     */
    public function getComment();

    /**
     * @param string $comment
     *
     * @return $this
     */
    public function setComment($comment);

    /**
     * Returns reminder
     *
     * @return int
     */
    public function getReminder();

    /**
     * @param int $reminder
     *
     * @return $this
     */
    public function setReminder($reminder);

    /**
     * Returns time interval ID
     *
     * @return int
     */
    public function getTintervalId();

    /**
     * @param int $tintervalId
     *
     * @return $this
     */
    public function setTintervalId($tintervalId);

    /**
     * Returns is active
     *
     * @return int
     */
    public function getActive();

    /**
     * @param int $active
     *
     * @return $this
     */
    public function setActive($active);
}
