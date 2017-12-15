<?php

namespace SomethingDigital\AssetOptimizer\Console;

use Magento\Framework\Console\CommandListInterface;
use Magento\Framework\ObjectManagerInterface;

class CommandList implements CommandListInterface
{
    private $objectManager;

    public function __construct(ObjectManagerInterface $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    public function getCommands()
    {
        return [
            $this->objectManager->get(StaticCommand::class),
        ];
    }
}

