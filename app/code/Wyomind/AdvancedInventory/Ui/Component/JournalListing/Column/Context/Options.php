<?php

/**
 * Copyright © 2017 Wyomind. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Wyomind\AdvancedInventory\Ui\Component\JournalListing\Column\Context;

class Options implements \Magento\Framework\Data\OptionSourceInterface
{

    /**
     * @var array
     */
    protected $options = null;

    /**
     * @var \Wyomind\CronScheduler\Model\ResourceModel\Task\Collection
     */
    public $_modelJournal = null;

    /**
     * Class constructor
     * @param \Wyomind\AdvancedInventory\Model\Journal $modelJournal
     */
    public function __construct(
    \Wyomind\AdvancedInventory\Model\Journal $modelJournal
    )
    {
        $this->_modelJournal = $modelJournal;
    }

    /**
     * Get all options available
     * @return array
     */
    public function toOptionArray()
    {
        if ($this->options === null) {
            $this->options = [];
            $users = $this->_modelJournal->getContexts();
            foreach ($users as $value => $label) {
                $this->options[] = ["label" => $label, "value" => $value];
            }
            
        }
        return $this->options;
    }

}
