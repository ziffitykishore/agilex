<?php

namespace Earthlite\PartFinder\Plugin;

use Magento\Catalog\Controller\Category\View as CategoryView;
use Magento\Framework\Registry;

/**
 * This class will save the list of part finder SKU's
 * in registry.
 */
class View
{
    /**
     * @var Registry
     */
    protected $_registry;

    /**
     * @param Registry $registry
     */
    public function __construct(
        Registry $registry
    ) {
        $this->_registry = $registry;
    }

    /**
     * Save the partfinder param in the registry.
     *
     * @param CategoryView $subject
     */
    public function beforeExecute(
        CategoryView $subject
    ) {
        if ($subject->getRequest()->getParam('partfinder')) {
            $this->_registry->register(
                'partfinder',
                $subject->getRequest()->getParam('partfinder')
            );
        }
    }
}
