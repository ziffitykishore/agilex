<?php
/**
 * Override In stock option builder
 *
 */
declare(strict_types=1);

namespace Ziffity\StockStatus\Plugin\Model\ResourceModel\Attribute;

use Magento\CatalogInventory\Api\StockConfigurationInterface;
use Magento\CatalogInventory\Model\ResourceModel\Stock\Status;
use Magento\ConfigurableProduct\Model\ResourceModel\Attribute\OptionSelectBuilderInterface;
use Magento\ConfigurableProduct\Plugin\Model\ResourceModel\Attribute\InStockOptionSelectBuilder as CoreInStockOptionSelectBuilder;
use Magento\Framework\DB\Select;
use Ziffity\Core\Helper\Data;

class InStockOptionSelectBuilder extends CoreInStockOptionSelectBuilder
{
    /**
     * @var StockConfigurationInterface
     */
    private $stockConfiguration;

    /**
     * InStockOptionSelectBuilder constructor
     *
     * @param Status $stockStatusResource
     * @param StockConfigurationInterface $stockConfiguration
     */
    public function __construct(
        Status $stockStatusResource,
        StockConfigurationInterface $stockConfiguration,
        Data $helper
    ) {
        parent::__construct($stockStatusResource);
        $this->stockConfiguration = $stockConfiguration;
        $this->helper = $helper;
    }

    /**
     * Only Add In stock Filter if Show Out Of Stock Products is set to No
     *
     * @param OptionSelectBuilderInterface $subject
     * @param Select $select
     * @return Select
     */
    public function afterGetSelect(
        OptionSelectBuilderInterface $subject,
        Select $select
    ) {
        $outofStockStatus = $this->helper->getOutOfStockStatus('configure_detail_page');
        if (!$this->stockConfiguration->isShowOutOfStock() || $outofStockStatus == 0) {
            return parent::afterGetSelect($subject, $select);
        }
        return $select;
    }
}
