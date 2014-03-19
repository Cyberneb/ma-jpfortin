<?php

/* 
 * Brand by Lesite
 * @copyright   Copyright (c) 2014 Lesite http://www.lesite.ca/ *
 */

class Lesite_Brand_Block_Adminhtml_Brand_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     * Init Grid default properties
     *
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('brand_grid');
        $this->setDefaultSort('created_at');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
    }

    /**
     * Prepare collection for Grid
     *
     * @return Lesite_Brand_Block_Adminhtml_Grid
     */
    protected function _prepareCollection()
    {
        
        $storeId = $this->getRequest()->getParam('store',0);
        $collection = Mage::getModel('brand/brand')->getCollection()->setStoreId($storeId);
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
        $this->addColumn('brand_id', array(
            'header'    => Mage::helper('lesite_brand')->__('ID'),
            'width'     => '50px',
            'index'     => 'brand_id',
        ));
        
        $this->addColumn('logo', array(
            'header'    => Mage::helper('lesite_brand')->__('Logo'),
            'width'     => Lesite_Brand_Model_Brand::SMALL_THUMBNAIL_SIZE . 'px',
            'align'     => 'center',
            'index'     => 'logo',
            'renderer'  => 'lesite_brand/adminhtml_brand_renderer_logo',
        ));
        
                
        $this->addColumn('title', array(
            'header'    => Mage::helper('lesite_brand')->__('Brand Title'),
            'index'     => 'title',
        ));
        
        $this->addColumn('url_key', array(
            'header'    => Mage::helper('lesite_brand')->__('URL Key'),
            'width'     => '250px',
            'index'     => 'url_key',
        ));

        $this->addColumn('created_at', array(
            'header'   => Mage::helper('lesite_brand')->__('Created'),
            'sortable' => true,
            'width'    => '170px',
            'index'    => 'created_at',
            'type'     => 'datetime',
        ));
        
        $this->addColumn('status', array(
            'header'    => Mage::helper('lesite_brand')->__('Status'),
            'align'     => 'left',
            'width'     => '80px',
            'index'     => 'status',
            'type'      => 'options',
            'options'   => array(
                1 => 'Active',
                0 => 'Inactive',
            ),
        ));
 
        $this->addColumn('featured', array(
            'header'    => Mage::helper('lesite_brand')->__('Featured'),
            'align'     => 'left',
            'width'     => '80px',
            'index'     => 'featured',
            'type'      => 'options',
            'options'   => array(
                1 => 'Yes',
                0 => 'No',
            ),
        ));

        $this->addColumn('action',
            array(
                'header'    => Mage::helper('lesite_brand')->__('Action'),
                'width'     => '100px',
                'type'      => 'action',
                'getter'    => 'getId',
                'actions'   => array(array(
                    'caption' => Mage::helper('lesite_brand')->__('Edit'),
                    'url'     => array('base' => '*/*/edit'),
                    'field'   => 'id'
                )),
                'filter'    => false,
                'sortable'  => false,
                'index'     => 'brand',
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