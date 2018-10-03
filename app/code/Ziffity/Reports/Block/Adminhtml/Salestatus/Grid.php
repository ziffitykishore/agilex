<?php
namespace Ziffity\Reports\Block\Adminhtml\Salestatus;
use Magento\CatalogInventory\Api\Data\StockStatusInterface;
class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
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
    
    protected  $moduleManager;

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
        \Magento\Framework\Module\Manager $moduleManager,
        \Magento\Reports\Model\ResourceModel\Quote\Item\CollectionFactory $quoteItemCollectionFactory,
        array $data = []
    ) {
        $this->quoteItemCollectionFactory = $quoteItemCollectionFactory;
        $this->queryResolver = $queryResolver;
        $this->productFactory = $productFactory;
        $this->moduleManager = $moduleManager;
        parent::__construct($context, $backendHelper, $data);
    }
    
    

    /**
     * @return void
     */
    protected function _construct()
    {
         parent::_construct();		
         $this->setId('sku');
         $this->setDefaultSort('sku');
         $this->setDefaultDir('asc');
         $this->setSaveParametersInSession(true);
         $this->setUseAjax(false);
    }

    /**
     * @return \Magento\Backend\Block\Widget\Grid
     */
    protected function _prepareCollection()
    {
        $model = $this->productFactory->create();
        $collection = $model->getCollection();
      //  $collection->addFieldToSelect(['sku']);
        $collection->getSelect()
              ->joinLeft(['prod'=>'catalog_product_entity_varchar'],
             'e.entity_id = prod.entity_id',
              array('name'=>'value')
              )->joinLeft(['inventory'=>'cataloginventory_stock_item'],
             'prod.entity_id = inventory.product_id',
              array('quantity'=>'qty','stock_status'=>'is_in_stock')
              )->where('prod.attribute_id = ?',65);
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
    //     if ($this->moduleManager->isEnabled('Magento_CatalogInventory')) {
        $this->addColumn(
            'qty',
            [
                'header' => __('QUANTITY'),
                'index' => 'quantity',
                'type' => 'number',
                'sortable' => false,
               
            ]
        );
     //    }

        $this->addColumn(
            'stock_status',
            [
                'header' => __('STOCK STATUS'),
                'index' => 'stock_status',
                'type' => 'options',
                'options' => [ 
                    StockStatusInterface::STATUS_OUT_OF_STOCK => 'Out Of Stock',
                    StockStatusInterface::STATUS_IN_STOCK => 'In Stock'
                    ],
                'sortable' => false,
            ]
        );


       // $this->setFilterVisibility(true);


        return parent::_prepareColumns();
    }

    /**
     * @param \Magento\Framework\DataObject $row
     *
     * @return string
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('productreports/salestatus/view',  ['id' => $row->getData('entity_id'), 'sku' => $row->getData('sku'), 'qty' => $row->getData('quantity'), 'stock_status' =>$row->getData('stock_status'),  'name' =>$row->getData('name')]);
    }
}
