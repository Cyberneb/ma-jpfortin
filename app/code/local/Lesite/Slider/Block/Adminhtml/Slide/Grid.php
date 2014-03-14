<?php
/**
 * Le Site custom slider
 */
class Lesite_Slider_Block_Adminhtml_Slide_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
  public function __construct()
  {
      parent::__construct();
      $this->setId('slideGrid');
      $this->setDefaultSort('slide_id');
      $this->setDefaultDir('ASC');
      $this->setSaveParametersInSession(true);
                  
  }

  protected function _prepareCollection()
  {
      $slider_id = $this->getRequest()->getParam('slider_id');
      $collection = Mage::getModel('slider/slide')->getCollection()->addFieldToFilter('slider_id', $slider_id);
      $this->setCollection($collection);
      return parent::_prepareCollection();
  }

  protected function _prepareColumns()
  {
      $this->addColumn('slide_id', array(
          'header'    => Mage::helper('slider')->__('ID'),
          'align'     =>'right',
          'width'     => '50px',
          'index'     => 'slide_id',
      ));
      
//      $this->addColumn( 'image_path', array(
//          'header' => Mage::helper( 'slider' )->__( 'Image' ), 
//          'type' => 'image', 
//          'width' => '75px', 
//          'index' => 'image_path',
//          'filter'    => false,
//          'sortable'  => false,
//          'renderer' => 'slider/adminhtml_template_grid_renderer_image',
//      ));
      
      $this->addColumn('name', array(
          'header'    => Mage::helper('slider')->__('Name'),
          'align'     =>'left',
          'index'     => 'name',
      ));
      
      $this->addColumn('redirect_to', array(
          'header'    => Mage::helper('slider')->__('Redirect To'),
          'align'     =>'left',
          'index'     => 'redirect_to',
      ));

      $this->addColumn('position', array(
          'header'    => Mage::helper('slider')->__('Order'),
          'align'     =>'left',
          'width'     => '50px',
          'index'     => 'position',
      ));

      $this->addColumn('status', array(
          'header'    => Mage::helper('slider')->__('Status'),
          'align'     => 'left',
          'width'     => '80px',
          'index'     => 'status',
          'type'      => 'options',
          'options'   => array(
              1 => Mage::helper('slider')->__('Enabled'),
              2 => Mage::helper('slider')->__('Disabled'),
          ),
      ));
	  
        $this->addColumn('action',
            array(
                'header'    =>  Mage::helper('slider')->__('Action'),
                'width'     => '100',
                'type'      => 'action',
                'getter'    => 'getId',
                'actions'   => array(
                    array(
                        'caption'   => Mage::helper('slider')->__('Edit'),
                        'url'       => array('base'=> '*/*/edit'),
                        'field'     => 'id'
                    )
                ),
                'filter'    => false,
                'sortable'  => false,
                'index'     => 'stores',
                'is_system' => true,
        ));
	  
      return parent::_prepareColumns();
  }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('slide_id');
        $this->getMassactionBlock()->setFormFieldName('slide');

        $this->getMassactionBlock()->addItem('delete', array(
             'label'    => Mage::helper('slider')->__('Delete'),
             'url'      => $this->getUrl('*/*/massDelete'),
             'confirm'  => Mage::helper('slider')->__('Are you sure?')
        ));

        $statuses = Mage::getSingleton('slider/status')->getOptionArray();

        array_unshift($statuses, array('label'=>'', 'value'=>''));
        $this->getMassactionBlock()->addItem('status', array(
             'label'=> Mage::helper('slider')->__('Change status'),
             'url'  => $this->getUrl('*/*/massStatus', array('_current'=>true)),
             'additional' => array(
                    'visibility' => array(
                         'name' => 'status',
                         'type' => 'select',
                         'class' => 'required-entry',
                         'label' => Mage::helper('slider')->__('Status'),
                         'values' => $statuses
                     )
             )
        ));
        return $this;
    }

  public function getRowUrl($row)
  {
      $slider_id = $this->getRequest()->getParam('slider_id');
      return $this->getUrl('*/*/edit', array('id' => $row->getId(), 'slider_id'=>$slider_id));
  }

}