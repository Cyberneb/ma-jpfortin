<?php

class Lesite_Catalog_Block_Adminhtml_Product_Color_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     * Construct grid block
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('productColorGrid');
        $this->setUseAjax(true);
        $this->setSaveParametersInSession(true);
        $this->setDefaultSort('name')
            ->setDefaultDir(Varien_Db_Select::SQL_ASC);
        $this->setColumnRenderers(array(
            'color_image' => 'lesite_catalog/adminhtml_product_color_grid_column_renderer_image',
            'color_background' => 'lesite_catalog/adminhtml_product_color_grid_column_renderer_background'
        ));
    }

    /**
     * Prepare collection
     *
     * @return Lesite_Catalog_Block_Adminhtml_Product_Color_Grid
     */
    protected function _prepareCollection()
    {
        $collection = Mage::getModel('lesite_catalog/product_color')->getCollection();
        $collection->addStoreFilter();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * Prepare columns
     *
     * @return Lesite_Catalog_Block_Adminhtml_Product_Color_Grid
     */
    protected function _prepareColumns()
    {
        /* @var $helper Lesite_Catalog_Helper_Data */
        $helper = Mage::helper('lesite_catalog');

        $this->addColumn('option_id', array(
            'header'    => $helper->__('ID'),
            'index'     => 'option_id',
            'align'     => 'right',
            'width'     => '50px'
        ));

        $this->addColumn('name', array(
            'header'    => $helper->__('Name'),
            'index'     => 'name',
            'escape'    => true
        ));

        $this->addColumn('image', array(
            'header'    => Mage::helper('catalog')->__('Image'),
            'width'     => '100px',
            'index'     => 'image',
            'type'      => 'color_image',
            'filter'    => false,
            'sortable'  => false,
        ));

        $this->addColumn('hex_code', array(
            'header'    => $helper->__('HEX code'),
            'index'     => 'hex_code',
            'type'      => 'color_background',
            'filter'    => false,
            'sortable'  => false,
        ));

        return parent::_prepareColumns();
    }

    /**
     * Get grid URL
     *
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', array('_current' => true));
    }

    /**
     * Get row URL
     *
     * @param Lesite_Catalog_Model_Product_Color $row
     * @return string|null
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('color_id' => $row->getId()));
    }
}
