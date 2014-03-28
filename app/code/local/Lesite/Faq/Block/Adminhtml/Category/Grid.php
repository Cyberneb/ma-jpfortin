<?php

/* 
 * FAQ by Lesite
 * Each line should be prefixed with  * 
 */

class Lesite_Faq_Block_Adminhtml_Category_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     * Init Grid default properties
     *
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('faq_category_grid');
        $this->setDefaultSort('category_id');
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
        $collection = Mage::getModel('faq/category')->getResourceCollection();
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
        $this->addColumn('category_id', array(
            'header'    => Mage::helper('lesite_faq')->__('Category ID'),
            'width'     => '50px',
            'index'     => 'category_id',
        ));
        
        $this->addColumn('order', array(
            'header'    => Mage::helper('lesite_faq')->__('Category Order #'),
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

        $this->addColumn('title', array(
            'header'    => Mage::helper('lesite_faq')->__('Category Title'),
            'index'     => 'title',
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