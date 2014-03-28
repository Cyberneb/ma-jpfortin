<?php

/* 
 * FAQ by Lesite
 * Each line should be prefixed with  * 
 */

class Lesite_Faq_Block_Adminhtml_Item_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     * Init Grid default properties
     *
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('faq_item_grid');
        $this->setDefaultSort('created_at');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
    }

    /**
     * Prepare collection for Grid
     *
     * @return Lesite_Faq_Block_Adminhtml_Grid
     */
    protected function _prepareCollection()
    {
        
        $collection = Mage::getModel('faq/item')->getResourceCollection();
 
        foreach ($collection as $view) {
            if ( $view->getStoreId() && $view->getStoreId() != 0 ) {
                $view->setStoreId(explode(',',$view->getStoreId()));
            } else {
                $view->setStoreId(array('0'));
            }
        }

        $this->setCollection($collection);
        
        return parent::_prepareCollection();
    }

    /**
     * Prepare Grid columns
     *
     * @return Mage_Adminhtml_Block_Catalog_Search_Grid
     */
    protected function _prepareColumns()
    {
        $this->addColumn('item_id', array(
            'header'    => Mage::helper('lesite_faq')->__('ID'),
            'width'     => '50px',
            'index'     => 'item_id',
        ));
        
        $this->addColumn('order', array(
            'header'    => Mage::helper('lesite_faq')->__('Order #'),
            'width'     => '50px',
            'index'     => 'order',
        ));
        
        if ( !Mage::app()->isSingleStoreMode() ) {
            $this->addColumn('store_id', array(
                'header' => Mage::helper('lesite_faq')->__('Store View'),
                'index' => 'store_id',
                'type' => 'store',
                'store_all' => true,
                'store_view' => true,
                'sortable' => true,
                'filter_condition_callback' => array($this, '_filterStoreCondition'),
            ));
        }
        
        $this->addColumn('category', array(
            'header'    => Mage::helper('lesite_faq')->__('Category'),
            'index' => 'category', 
            'sortable' => false,
            'filter' => false,
            'renderer' => 'lesite_faq/adminhtml_widget_grid_column_renderer_category',
        ));

        $this->addColumn('question', array(
            'header'    => Mage::helper('lesite_faq')->__('Faq Question'),
            'index'     => 'question',
        ));


        $this->addColumn('created_at', array(
            'header'   => Mage::helper('lesite_faq')->__('Created'),
            'sortable' => true,
            'width'    => '170px',
            'index'    => 'created_at',
            'type'     => 'datetime',
        ));

        $this->addColumn('action',
            array(
                'header'    => Mage::helper('lesite_faq')->__('Action'),
                'width'     => '100px',
                'type'      => 'action',
                'getter'    => 'getId',
                'actions'   => array(array(
                    'caption' => Mage::helper('lesite_faq')->__('Edit'),
                    'url'     => array('base' => '*/*/edit'),
                    'field'   => 'id'
                )),
                'filter'    => false,
                'sortable'  => false,
                'index'     => 'faq',
        ));

        return parent::_prepareColumns();
    }

    /**
     * Return row URL for js event handlers
     *
     * @return string
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('id' => $row->getId()));
    }

    /**
     * Grid url getter
     *
     * @return string current grid url
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', array('_current' => true));
    }
    
    
    /**
     * 
     * This function checks to see if a store filter has been selected and if so calls the function to add the filter to the collection
     * 
     */
     
    protected function _filterStoreCondition($collection, $column){
        if ( !$value = $column->getFilter()->getValue() ) {
            return;
        }
        $this->getCollection()->addStoreFilter($value);
}
    
}