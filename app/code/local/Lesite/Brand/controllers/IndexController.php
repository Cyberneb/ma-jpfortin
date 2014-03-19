<?php

/*
 * Brand by Lesite
 * @copyright   Copyright (c) 2014 Lesite http://www.lesite.ca/ *
 */

class Lesite_Brand_IndexController extends Mage_Core_Controller_Front_Action {

        
        protected function _initAction() {
            $store = Mage::app()->getStore()->getId();
        }
    
       /**
       * Displays the Brand list.
       */
       public function indexAction()
       {
               
               $this->_initAction();
        $this->loadLayout();
        $this->getLayout()->getBlock('head')->setTitle($this->__('Brands'));
        $this->getLayout()->getBlock('breadcrumbs')
                ->addCrumb('home', array('label' => $this->__('Home'),
                    'title' => $this->__('Go to Home Page'),
                    'link' => Mage::getBaseUrl()))
                ->addCrumb('socialvoice', array('label' => $this->__('Brands'),
                    'title' => $this->__('Brands'),
                ))
        ;
        $this->renderLayout();
               
       }

       /**
        * Displays the current Brand's detail view
        */
       public function viewAction() {
       
        $this->_initAction();
        $this->loadLayout();
        
        $brandId = $this->getRequest()->getParam('id');
        $storeId = Mage::app()->getStore()->getId();
        $brand = Mage::getModel('brand/brand')->setStoreId($storeId)
                ->load($brandId);
        $head = $this->getLayout()->getBlock('head');
        $head->setTitle($brand->getMetaTitle());
        $head->setKeywords($brand->getMetaKeywords());
        $head->setDescription($brand->getMetaDescription());
        $this->getLayout()->getBlock('breadcrumbs')
                ->addCrumb('home', array('label' => $this->__('Home'),
                    'title' => $this->__('Go to Home Page'),
                    'link' => Mage::getBaseUrl()
                ))
                ->addCrumb('brand', array('label' => $this->__('Brands'),
                    'title' => $this->__('Brand'),
                    'link' => Mage::getUrl('brand')
                ))
                ->addCrumb('view', array('label' => $brand->getTitle(),
                    'title' => $this->__('Brand')
                ))
        ;
        $this->renderLayout();
    }
    
     /**
        * Displays the current Brand's products nav
        */
       public function navAction() {
       
        $this->_initAction();
        $this->loadLayout();
        
        $brandId = $this->getRequest()->getParam('id');
        $storeId = Mage::app()->getStore()->getId();
        $brand = Mage::getModel('brand/brand')->setStoreId($storeId)
                ->load($brandId);
        $head = $this->getLayout()->getBlock('head');
        $head->setTitle($brand->getMetaTitle());
        $head->setKeywords($brand->getMetaKeywords());
        $head->setDescription($brand->getMetaDescription());
        $this->getLayout()->getBlock('breadcrumbs')
                ->addCrumb('home', array('label' => $this->__('Home'),
                    'title' => $this->__('Go to Home Page'),
                    'link' => Mage::getBaseUrl()
                ))
                ->addCrumb('brand', array('label' => $this->__('Brands'),
                    'title' => $this->__('Brand'),
                    'link' => Mage::getUrl('brand')
                ))
                ->addCrumb('view', array('label' => $brand->getTitle(),
                    'title' => $this->__('Brand')
                ))
        ;
        $this->renderLayout();
    }
    
}
