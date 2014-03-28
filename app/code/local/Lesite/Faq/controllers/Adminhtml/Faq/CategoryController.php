<?php

/*
 * FAQ by Lesite
 * Each line should be prefixed with  * 
 */

class Lesite_Faq_Adminhtml_Faq_CategoryController extends Mage_Adminhtml_Controller_Action {

    /**
     * Init actions
     * @return Lesite_Faq_adminhtml_FaqController
     */
    protected function _initAction() {
        
        // load layout, set active menu and breadcrumbs
        $this->loadLayout()
            ->_setActiveMenu('cms/faq')
            ->_addBreadcrumb(
                  Mage::helper('lesite_faq')->__('Faqs'),
                  Mage::helper('lesite_faq')->__('Faqs')
              )
            ->_addBreadcrumb(
                  Mage::helper('lesite_faq')->__('Manage Faqs'),
                  Mage::helper('lesite_faq')->__('Manage Faqs')
              )
        ;
        return $this;
    }

    /**
     * Index action
     */
    public function indexAction() {
        $this->_title($this->__('FAQ'))->_title($this->__('Categories'));
        $this->loadLayout();
        $this->_setActiveMenu('cms/faq');
        $this->renderLayout();
    }

    /**
     * Create new Faq category
     */
    public function newAction() {
        // the same form is used to create and edit
        $this->_forward('edit');
    }

    /**
     * Edit Faq category
     */
    public function editAction() {
        
        $this->_title($this->__('FAQ Categories'))
               ->_title($this->__('Manage FAQ Categories'));

        $id = $this->getRequest()->getParam('id');
        
        // instance faq model
        /* @var $model Lesite_Faq_Model_Category */
        $model = Mage::getModel('faq/category');

        // Initial checking
        if ($id) {
            $model->load($id);
            if (! $model->getId()) {
                Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('lesite_faq')->__('This faq category no longer exists.'));
                $this->_redirect('*/*/');
                return;
            }
        }

        $this->_title($model->getId() ? $model->getTitle() : $this->__('New faq category'));

        // Set entered data if was error when we do save
        $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
        if (!empty($data)) {
            $model->setData($data)->updateStoreSpecificData();
        }


        // Register model to use later in blocks
        Mage::register('faq_category', $model);
       
        // Render layout
        $this->_initAction()
            ->_addBreadcrumb(
                $id ? Mage::helper('lesite_faq')->__('Edit Faq Category')
                    : Mage::helper('lesite_faq')->__('New Faq Category'),
                $id ? Mage::helper('lesite_faq')->__('Edit Faq Category')
                    : Mage::helper('lesite_faq')->__('New Faq Category'));
        $this->renderLayout();
    }

    /**
     * Save action
     */
    public function saveAction() {
        
        // check if data sent
        if ($data = $this->getRequest()->getPost()) {

            //init model and set data
            $model = Mage::getModel('faq/category');

            if ($id = $this->getRequest()->getParam('id')) {
                $model->load($id);
            }

            //store id prepration
            if( isset($data['stores']) ) {
                if( in_array('0', $data['stores']) ){
                    $data['store_id'] = '0';
                } else {
                    $data['store_id'] = join(",", $data['stores']);
                }
                unset($data['stores']);
            }
            
            $model->addData($data);

            // try to save it
            try {
                // save the data
                $model->save();

                // display success message
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('lesite_faq')->__('The Faq category has been saved.'));
                // clear previously saved data from session
                Mage::getSingleton('adminhtml/session')->setFormData(false);
                // check if 'Save and Continue'
                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', array('id' => $model->getId(), '_current'=>true));
                    return;
                }
                // go to grid
                $this->_redirect('*/*/');
                return;

            } catch (Mage_Core_Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            } catch (Exception $e) {
                $this->_getSession()->addException($e,
                    Mage::helper('lesite_faq')->__('An error occurred while saving the page.'));
            }

            $this->_getSession()->setFormData($data);
            $this->_redirect('*/*/edit', array('id' => $id));
            return;
        }
        $this->_redirect('*/*/');
    }

    /**
     * Delete action
     */
    public function deleteAction() {
        // check if we know what should be deleted 
        $categoryId = $this->getRequest()->getParam('id');
        if ($categoryId) {
            try {
                // init model and delete
                /** @var $model Lesite_Faq_Model_Category */
                $model = Mage::getModel('faq/category');
                $model->load($categoryId);
                if (!$model->getId()) {
                    Mage::throwException(Mage::helper('lesite_faq')->__('Unable to find a faq category.'));
                }
                $model->delete();

                // display success message 
                $this->_getSession()->addSuccess(
                        Mage::helper('lesite_faq')->__('The faq category has been deleted.')
                );
            } catch (Mage_Core_Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            } catch (Exception $e) {
                $this->_getSession()->addException($e, Mage::helper('lesite_faq')->__('An error occurred while deleting the faq category.')
                );
            }
        }
        // go to grid
        $this->_redirect('*/*/');
    }

    /**
     * Check the permission to run it
     * @return boolean
     */
    protected function _isAllowed() {
        switch ($this->getRequest()->getActionName()) {
            case 'new':
            case 'save':
                return Mage::getSingleton('Admin/session')->isAllowed('faq/manage/save');
                break;
            case 'delete':
                return Mage::getSingleton('Admin/session')->isAllowed('faq/manage/delete');
                break;
            default:
                return Mage::getSingleton('Admin/session')->isAllowed('faq/manage');
                break;
        }
    }

    /**
     * Filtering posted data. Converting localized data if needed
     * @param array * @return array */
    protected function _filterPostData($data) {
        $data = $this->_filterDates($data, array('time_published'));
        return $data;
    }

    /**
     * Grid ajax action
     */
    public function gridAction() {
        $this->loadLayout();
        $this->renderLayout();
    }

}
