<?php

namespace SomethingDigital\URapidFlowNotification\Plugin;

use Unirgy\RapidFlow\Model\Profile;
use SomethingDigital\URapidFlowNotification\Helper\Email;

/**
 * Class ProfileNotification
 */
class ProfileNotification
{
    /**
     * ProfileNotification constructor
     *
     * @param Email $email
     */
    public function __construct(
        Email $email
    ) {
        $this->email = $email;
    }

    /**
     * Send notifiacation
     */
    public function beforeRun(Profile $subject)
    {
        if ($subject->isLocked()) {
            $this->email->sendEmail($subject->getId()); //notify that is still running
        }
    }

}
