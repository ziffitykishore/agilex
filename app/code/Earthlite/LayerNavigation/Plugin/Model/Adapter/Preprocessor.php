<?php

namespace Earthlite\LayerNavigation\Plugin\Model\Adapter;

use Closure;
use Magento\Framework\App\ProductMetadataInterface;
use Magento\Framework\ObjectManagerInterface;
use Earthlite\LayerNavigation\Helper\Data;


class Preprocessor
{
    protected $_moduleHelper;

    protected $objectManager;

    protected $productMetadata;

    public function __construct(
        Data $moduleHelper,
        ObjectManagerInterface $objectManager,
        ProductMetadataInterface $productMetadata
    ) {
        $this->_moduleHelper   = $moduleHelper;
        $this->objectManager   = $objectManager;
        $this->productMetadata = $productMetadata;
    }

    public function aroundProcess(
        \Magento\CatalogSearch\Model\Adapter\Mysql\Filter\Preprocessor $subject,
        Closure $proceed,
        $filter,
        $isNegation,
        $query
    ) {
        if ($this->_moduleHelper->isEnabled() && ($filter->getField() === 'category_ids')) {
            $filterValue = implode(',', array_map([$this, 'validateCatIds'], explode(',', $filter->getValue())));

            $version = $this->productMetadata->getVersion();
            if (version_compare($version, '2.1.13', '>=') && version_compare($version, '2.1.15', '<=')) {
                return 'category_products_index.category_id IN (' . $filterValue . ')';
            }

            return 'category_ids_index.category_id IN (' . $filterValue . ')';
        }

        return $proceed($filter, $isNegation, $query);
    }

    protected function validateCatIds($catId)
    {
        return (int) $catId;
    }
}
