<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-search-elastic
 * @version   1.2.13
 * @copyright Copyright (C) 2018 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\SearchElastic\Model\Indexer;

use Magento\Framework\Search\Request\Dimension;
use Magento\CatalogSearch\Model\Indexer\Scope\State;

class ScopeProxy implements \Magento\Framework\Search\Request\IndexScopeResolverInterface
{
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @var array
     */
    private $states = [];

    /**
     * @var State
     */
    private $scopeState;

    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        State $scopeState,
        array $states
    ) {
        $this->objectManager = $objectManager;
        $this->scopeState = $scopeState;
        $this->states = $states;
    }

    /**
     * Creates class instance with specified parameters
     *
     * @param string $state
     * @return \Magento\Framework\Search\Request\IndexScopeResolverInterface
     */
    private function create($state)
    {
        return $this->objectManager->create($this->states[$state]);
    }

    /**
     * @param string $index
     * @param Dimension[] $dimensions
     * @return string
     */
    public function resolve($index, array $dimensions)
    {
        return $this->create($this->scopeState->getState())->resolve($index, $dimensions);
    }
}
