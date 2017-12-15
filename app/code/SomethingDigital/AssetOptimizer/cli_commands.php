<?php

use Magento\Framework\Console\CommandLocator;

if (PHP_SAPI == 'cli') {
    CommandLocator::register(\SomethingDigital\AssetOptimizer\Console\CommandList::class);
}
