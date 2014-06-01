<?php

class Lesite_Catalog_Adminhtml_Product_ColorController extends Mage_Adminhtml_Controller_Action
{
    /**
     * Init actions
     *
     * @return Lesite_Catalog_Adminhtml_Product_ColorController
     */
    protected function _initAction()
    {
        // load layout, set active menu and breadcrumbs
        $this->loadLayout()
            ->_setActiveMenu('catalog_product_color/index')
            ->_addBreadcrumb(
                  Mage::helper('lesite_catalog')->__('Manage Product Colors'),
                  Mage::helper('lesite_catalog')->__('Manage Product Colors')
              )
            ->_addBreadcrumb(
                  Mage::helper('lesite_catalog')->__('Manage Product Colors'),
                  Mage::helper('lesite_catalog')->__('Manage Product Colors')
              )
        ;
        return $this;
    }

    /**
     * Check the permission to run it
     *
     * @return boolean
     */
    protected function _isAllowed()
    {
        switch ($this->getRequest()->getActionName()) {
            case 'save':
                return Mage::getSingleton('admin/session')->isAllowed('catalog/product_color/save');
                break;
            default:
                return Mage::getSingleton('admin/session')->isAllowed('catalog/product_color');
                break;
        }
    }

    /**
     * Index action
     */
    public function indexAction()
    {
        $this->_title($this->__('Products'))
             ->_title($this->__('Manage Product Colors'));

        $this->_initAction();
        $this->renderLayout();
    }

    /**
     * Product Colors grid for AJAX request
     */
    public function gridAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     * Edit Product Color
     */
    public function editAction()
    {
        $this->_title($this->__('Product Color'))
             ->_title($this->__('Manage Content'));

        // 1. Get ID and create model
        $id = $this->getRequest()->getParam('color_id');
        $model = Mage::getModel('lesite_catalog/product_color');

        // 2. Initial checking
        if ($id) {
            $model->load($id);
            if (!$model->getId()) {
                Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('lesite_catalog')->__('Product color no longer exists.'));
                $this->_redirect('*/*/');
                return;
            }
        } else {
            Mage::getSingleton('adminhtml/session')->addError(
                Mage::helper('lesite_catalog')->__('Please add new color option before.'));
            $this->_redirect('*/*/');
            return;
        }

        $this->_title($model->getId() ? $model->getName() : $this->__('New Product Color'));

        // 3. Set entered data if was error when we do save
        $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
        if (! empty($data)) {
            $model->setData($data);
        }

        // 4. Register model to use later in blocks
        Mage::register('product_color', $model);

        // 5. Build edit form
        $this->_initAction()
            ->_addBreadcrumb(
                $id ? Mage::helper('lesite_catalog')->__('Edit Product Color')
                    : Mage::helper('lesite_catalog')->__('New Product Color'),
                $id ? Mage::helper('lesite_catalog')->__('Edit Product Color')
                    : Mage::helper('lesite_catalog')->__('New Product Color'));

        $this->renderLayout();
    }

    /**
     * Save action
     */
    public function saveAction()
    {
        // check if data sent
        if ($data = $this->getRequest()->getPost()) {

            //init model and set data
            $model = Mage::getModel('lesite_catalog/product_color');

            if ($id = $this->getRequest()->getParam('color_id')) {
                $model->load($id);
            } else {
                Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('lesite_catalog')->__('Please add new color option before.'));
                $this->_redirect('*/*/');
                return;
            }

            $model->addData($data);
            $model->setData('attribute_id', $model->getAttribute()->getId());
            $model->setData('sort_order', 0);

            // try to save it
            try {
                // save the data
                $model->save();

                // display success message
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('lesite_catalog')->__('Product color has been saved.'));
                // clear previously saved data from session
                Mage::getSingleton('adminhtml/session')->setFormData(false);
                // check if 'Save and Continue'
                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', array('color_id' => $model->getId(), '_current'=>true));
                    return;
                }
                // go to brand edit form
                $this->_redirect('*/*/');
                return;

            } catch (Mage_Core_Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
            catch (Exception $e) {
                $this->_getSession()->addException($e,
                    Mage::helper('lesite_catalog')->__('An error occurred while saving the product color.'));
            }

            $this->_getSession()->setFormData($data);
            $this->_redirect('*/*/edit', array('color_id' => $id));
            return;
        }
        $this->_redirect('*/*/');
    }
}
