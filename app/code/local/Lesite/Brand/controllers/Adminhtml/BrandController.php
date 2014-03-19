<?php

/*
 * Brand by Lesite
 * @copyright   Copyright (c) 2014 Lesite http://www.lesite.ca/ *
 * 
 */


class Lesite_Brand_Adminhtml_BrandController extends Mage_Adminhtml_Controller_Action {

    /**
     * Init actions
     * @return Lesite_Brand_adminhtml_BrandController
     */
    protected function _initAction() {
        // load layout, set active menu and breadcrumbs
        $this->loadLayout()
            ->_setActiveMenu('cms/brand')
            ->_addBreadcrumb(
                  Mage::helper('lesite_brand')->__('Brands'),
                  Mage::helper('lesite_brand')->__('Brands')
              )
            ->_addBreadcrumb(
                  Mage::helper('lesite_brand')->__('Manage Brands'),
                  Mage::helper('lesite_brand')->__('Manage Brands')
              )
        ;
        return $this;
    }

    /**
     * Index action
     */
    public function indexAction() {
        $this->_title($this->__('Brand'))->_title($this->__('Brands'));
        $this->loadLayout();
        $this->_setActiveMenu('cms/brand');
        $this->renderLayout();
    }

    /**
     * Create new Brand
     */
    public function newAction() {
        // the same form is used to create and edit
        $this->_forward('edit');
    }

    /**
     * Edit Brand
     */
    public function editAction() {
        
        $brandId = $this->getRequest()->getParam('id');
        $store = $this->getRequest()->getParam('store');
        $model = Mage::getModel('brand/brand')->setStoreId($store)
                ->load($brandId);
        if ($model->getId() || $brandId == 0) {
            $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
            if (!empty($data)) {
                $model->setData($data);
            }
            Mage::register('brand_data', $model);
            
            $this->loadLayout();
            $this->_setActiveMenu('cms/brand');

            $this->_addBreadcrumb(
                    Mage::helper('adminhtml')->__('Brand Manager'), Mage::helper('adminhtml')->__('Brand Manager')
            );
            
            $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
            $this->renderLayout();
            
            
            } else {
                Mage::getSingleton('adminhtml/session')->addError(
                        Mage::helper('lesite_brand')->__('Brand does not exist')
                );
                $this->_redirect('*/*/');
        }
    }

    /**
     * Save action
     */
    public function saveAction() {
        
        // check if data sent
        if ($data = $this->getRequest()->getPost()) {

            $store = $this->getRequest()->getParam('store',0);
            
            //if (!$this->getRequest()->getParam('id')) {
                if (isset($data['url_key'])) {
                    $data['url_key'] = Mage::helper('lesite_brand')->refineUrlKey($data['url_key']);
                    $urlRewrite = Mage::getModel('brand/urlrewrite')->loadByRequestPath($data['url_key'], $store);
                    if ($urlRewrite->getId()) {
                        if(!$this->getRequest()->getParam('id')){
                            Mage::getSingleton('adminhtml/session')->addError('Url key has existed. Please fill out a valid one.');
                            $this->_redirect('*/*/new', array('store' => $store));
                            return;
                        }elseif($this->getRequest()->getParam('id') && $urlRewrite->getIdPath() != 'brand/'.$this->getRequest()->getParam('id')){
                            Mage::getSingleton('adminhtml/session')->addError('Url key has existed. Please fill out a valid one.');
                            $this->_redirect('*/*/edit', array('store' => $store,'id'=>$this->getRequest()->getParam('id')));
                            return;
                        }
                    }
                }
            //}
            
            if (isset($data['logo']['delete'])) {
                Mage::helper('lesite_brand')->deleteLogoFile($data['name'], $data['old_logo']);
                unset($data['old_logo']);
            }
            $data['logo'] = "";
            if (isset($_FILES['logo']))
                $data['logo'] = Mage::helper('lesite_brand')->refineLogoName($_FILES['logo']['name']);

            if (!$data['logo'] && isset($data['old_logo'])) {
                $data['logo'] = $data['old_logo'];
            }
            if (isset($data['banner']['delete'])) {

                Mage::helper('lesite_brand')->deleteBannerFile($data['name'], $data['old_banner']);
                unset($data['old_banner']);
            }
            $data['banner'] = "";

            if (isset($_FILES['banner']))
                $data['banner'] = Mage::helper('lesite_brand')->refineLogoName($_FILES['banner']['name']);


            if (!$data['banner'] && isset($data['old_banner'])) {
                $data['banner'] = $data['old_banner'];
            }
            
            //init model and set data
            $model = Mage::getModel('brand/brand');

            if ($id = $this->getRequest()->getParam('id')) {
                $model->load($id);
            }
            
            $model->addData($data);

            // try to save it
            try {
                
                $model->setStoreId($store);
                try {
                    $model->save();
                    
                } catch (Exception $e) {
                    Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                }

                //upload logo
                $image = $model->getLogo();
                if (isset($_FILES['logo'])) {
                    if (isset($_FILES['logo']['name']) && $_FILES['logo']['name'])
                        $image = Mage::helper('lesite_brand')->uploadBrandLogo($model->getId(), $_FILES['logo']);
                }
                $banner = $model->getBanner();
                if (isset($_FILES['banner'])) {
                    if (isset($_FILES['banner']['name']) && $_FILES['banner']['name'])
                        $banner = Mage::helper('lesite_brand')->uploadBanner($model->getId(), $_FILES['banner']);
                }
                if ($image != $model->getImage() || $banner != $model->getBanner()) {
                    if ($image != $model->getImage())
                        $model->setImage($image);
                    if ($banner != $model->getBanner())
                        $model->setBanner($banner);
                    //$model->save();
                }
                
                $brandModel = Mage::getModel('brand/brand')
                        ->setStoreId($store)
                        ->load($model->getId());
                $brandModel->updateUrlKey();
                
                //$optionId = Mage::getResourceModel('brand/brand')->addOption($model->load($model->getId()));
                //if($optionId)
                    //$brandModel->setOptionId($optionId)->save();

                // display success message
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('lesite_brand')->__('The Brand has been saved.'));
                // clear previously saved data from session
                Mage::getSingleton('adminhtml/session')->setFormData(false);
                
                // check if 'Save and Continue'
                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', array('id' => $model->getId(), 'store' => $store));
                    return;
                }
                // go to grid
                $this->_redirect('*/*/');
                return;

            } catch (Mage_Core_Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            } catch (Exception $e) {
                
                $this->_getSession()->addException($e,
                    Mage::helper('lesite_brand')->__('An error occurred while saving the page.'));
            }

            $this->_getSession()->setFormData($data);
            $this->_redirect('*/*/edit', array('id' => $id));
            return;
        }
        
        Mage::getSingleton('adminhtml/session')->addError(
                Mage::helper('lesite_brand')->__('Unable to find the brand to save')
        );
        
        $this->_redirect('*/*/');
    }

    /**
     * Delete action
     */
    public function deleteAction() {
        // check if we know what should be deleted 
        $brandId = $this->getRequest()->getParam('id');
        if ($brandId) {
            try {
                // init model and delete
                $stores = Mage::getModel('core/store')->getCollection()
                    ->addFieldToFilter('is_active',1)
                    ->addFieldToFilter('store_id',array('neq'=>0))
                    ;
                foreach ($stores as $store){
                    $urlRewrite = Mage::getModel('brand/urlrewrite')->loadByIdPath('brand/'.$this->getRequest()->getParam('id'), $store->getId());
                    if($urlRewrite->getId())
                        $urlRewrite->delete();
                }
                
                /** @var $model Lesite_Brand_Model_Brand */
                $model = Mage::getModel('brand/brand');
                $model->load($brandId);
                if (!$model->getId()) {
                    Mage::throwException(Mage::helper('lesite_brand')->__('Unable to find a brand.'));
                }
                Mage::getResourceModel('brand/brand')->removeOption($model);
                $model->delete();

                // display success message 
                $this->_getSession()->addSuccess(
                        Mage::helper('lesite_brand')->__('The brand has been deleted.')
                );
            } catch (Mage_Core_Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            } catch (Exception $e) {
                $this->_getSession()->addException($e, Mage::helper('lesite_brand')->__('An error occurred while deleting the brand.')
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
                return Mage::getSingleton('Admin/session')->isAllowed('brand/manage/save');
                break;
            case 'delete':
                return Mage::getSingleton('Admin/session')->isAllowed('brand/manage/delete');
                break;
            default:
                return Mage::getSingleton('Admin/session')->isAllowed('brand/manage');
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
