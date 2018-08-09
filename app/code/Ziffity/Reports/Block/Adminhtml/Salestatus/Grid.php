<?php
namespace Ziffity\Reports\Block\Adminhtml\Salestatus;
use Magento\CatalogInventory\Model\Stock;

class Grid extends \Magento\Reports\Block\Adminhtml\Grid\Shopcart
{
   /**
     * @var \Magento\Reports\Model\ResourceModel\Quote\CollectionFactory
     */
    protected $quoteItemCollectionFactory;

    /**
     * @var \Magento\Quote\Model\QueryResolver
     */
    protected $queryResolver;
    
    protected $productFactory;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Magento\Reports\Model\ResourceModel\Quote\Item\CollectionFactory $quoteItemCollectionFactory
     * @param \Magento\Quote\Model\QueryResolver $queryResolver
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magento\Quote\Model\QueryResolver $queryResolver,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Reports\Model\ResourceModel\Quote\Item\CollectionFactory $quoteItemCollectionFactory,
        array $data = []
    ) {
        $this->quoteItemCollectionFactory = $quoteItemCollectionFactory;
        $this->queryResolver = $queryResolver;
        $this->productFactory = $productFactory;
        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
       // $this->setCountTotals(true);
        $this->setId('catalog_product_entity');
         $this->setDefaultSort('entity_id');
        //$this->setUseAjax(true);
    }

    /**
     * @return \Magento\Backend\Block\Widget\Grid
     */
    protected function _prepareCollection()
    {
        /** @var \Magento\Reports\Model\ResourceModel\Quote\Item\Collection $collection */
        $model = $this->productFactory->create();
        $collection = $model->getCollection();
        $collection->getSelect()->from(['main_table' => $collection->getTable('catalog_product_entity')], '')->columns('main_table.entity_id')
              ->joinLeft(['prod'=>'catalog_product_entity_varchar'],
             'main_table.entity_id = prod.entity_id',
              array('sku' => 'main_table.sku','name'=>'value')
              )->joinLeft(['inventory'=>'cataloginventory_stock_item'],
             'main_table.entity_id = inventory.product_id',
              array('qty'=>'qty','is_in_stock'=>'is_in_stock')
              )->where('prod.attribute_id = ?',65);
        
        $collection->getSelect()->joinLeft(['eav'=>'catalog_product_entity_int'],
              'main_table.entity_id = eav.entity_id',
              array('eavAttribute'=>'attribute_id','isActive'=>'value')
              )->where('eav.attribute_id = ?',89);
        $collection->getSelect()->where('eav.value = ?',1);
        //$collection->getSelect()->where('inventory.is_in_stock IN(?)',[STOCK::STOCK_IN_STOCK,STOCK::STOCK_OUT_OF_STOCK]);
       
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * @return \Magento\Backend\Block\Widget\Grid\Extended
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'sku',
            [
                'header' => __('SKU'),
                'align' => 'right',
                'index' => 'sku',
                'sortable' => false,
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id'
            ]
        );

        $this->addColumn(
            'productname',
            [
                'header' => __('PRODUCT NAME'),
                'index' => 'name',
                'sortable' => false,
                'header_css_class' => 'col-product',
                'column_css_class' => 'col-product'
            ]
        );

       // $currencyCode = $this->getCurrentCurrencyCode();

        $this->addColumn(
            'qty',
            [
                'header' => __('QUANTITY'),
 
                'index' => 'qty',
                'type' => 'number',
                'sortable' => false,
                'header_css_class' => 'col-qty',
                'column_css_class' => 'col-qty'
            ]
        );

        $this->addColumn(
            'stockstatus',
            [
                'header' => __('STOCK STATUS'),
                'align' => 'right',
                'index' => 'is_in_stock',
                'type' => 'options',
                'options' => array(
                        '0'   => 'Out of Stock',
                        '1'   =>'In Stock'),
                'sortable' => false,
                'header_css_class' => 'col-stockstatus',
                'column_css_class' => 'col-stockstatus'
            ]
        );


        $this->setFilterVisibility(true);
        
        //$this->addExportType('*/*/exportProductCsv', __('CSV'));
       // $this->addExportType('*/*/exportProductExcel', __('Excel XML'));

        return parent::_prepareColumns();
    }

    /**
     * @param \Magento\Framework\DataObject $row
     *
     * @return string
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('productreports/salestatus/view', ['id' => $row->getData('entity_id')]);
    }
}
