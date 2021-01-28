<?php
declare(strict_types=1);

namespace Travers\CustomerLinking\Model;

use Magento\Framework\MessageQueue\MergerInterface;

/**
 * Class ReTransMerger
 * @package Earthlite\ReTrans\Model
 */
class ReTransMerger implements MergerInterface
{
    /**
     * @inheritDoc
     */
    public function merge(array $messages)
    {
        return $messages;
    }
}
