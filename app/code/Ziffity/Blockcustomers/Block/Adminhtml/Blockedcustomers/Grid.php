<?php
namespace Ziffity\Blockcustomers\Block\Adminhtml\Blockedcustomers;
/**
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */



/**
 * This is the Summary for this element.
 * 
 * @inheritDoc
 */
class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{
    protected $collectionFactory;
    
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Ziffity\Blockcustomers\Model\ResourceModel\Blockcustomers\Collection $collectionFactory,
        array $data = array()
    )
    {
        $this->_collectionFactory = $collectionFactory;
        parent::__construct($context, $backendHelper, $data);
    }
    
    public function _construct()
    {
        $this->setId('CustomerBlockGrid');
        $this->setDefaultSort('id');
        parent::_construct();
    }
     /**
     * @return Store
     */
    protected function _getStore()
    {
        $storeId = (int)$this->getRequest()->getParam('store', 0);
        return $this->_storeManager->getStore($storeId);
    }

    /**
     * @return $this
     */
    protected function _prepareCollection()
    {
		try
                {            
	            $collection =$this->_collectionFactory->load();
                    $this->setCollection($collection);
                    parent::_prepareCollection();
		    return $this;
		}
		catch(Exception $e)
		{
			echo $e->getMessage();
		}
    }
    
    protected function _prepareColumns()
    {
        $this->addColumn(
            'id',
            [
                'header' => __('ID'),
                'type' => 'number',
                'index' => 'id',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id'
            ]
        );
		$this->addColumn(
            'name',
            [
                'header' => __('name'),
                'index' => 'name',
                'class' => 'name'
            ]
        );
		$this->addColumn(
            'email',
            [
                'header' => __('email'),
                'index' => 'email',
                'class' => 'email'
            ]
        );
		
              
		/*{{CedAddGridColumn}}*/

        $block = $this->getLayout()->getBlock('grid.bottom.links');
        if ($block) {
            $this->setChild('grid.bottom.links', $block);
        }

        return parent::_prepareColumns();
    }
    
     protected function _prepareMassaction()
    {
        $this->setMassactionIdField('id');
        $this->getMassactionBlock()->setFormFieldName('id');

        $this->getMassactionBlock()->addItem(
            'delete',
            array(
                'label' => __('Delete'),
                'url' => $this->getUrl('blockcustomers/*/massDelete'),
                'confirm' => __('Are you sure?')
            )
        );
       
        return $this;
    }

}
