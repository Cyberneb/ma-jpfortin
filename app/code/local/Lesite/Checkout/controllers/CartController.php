<?php

if (Mage::helper('core')->isModuleEnabled('Idev_OneStepCheckout')) {
    require_once  Mage::getModuleDir('controllers', 'Idev_OneStepCheckout').DS.'CartController.php';
    class Lesite_Checkout_CartController_Abstract extends Idev_OneStepCheckout_CartController {}
} else {
    require_once  Mage::getModuleDir('controllers', 'Mage_Checkout').DS.'CartController.php';
    class Lesite_Checkout_CartController_Abstract extends Mage_Checkout_CartController {}
}

class Lesite_Checkout_CartController extends Lesite_Checkout_CartController_Abstract
{
    /**
     * Add product to shopping cart action
     *
     * @return Mage_Core_Controller_Varien_Action
     * @throws Exception
     */
    public function addAction()
    {
        if (!$this->_validateFormKey()) {
            $this->_goBack();
            return;
        }
        $cart   = $this->_getCart();
        $params = $this->getRequest()->getParams();
        try {
            if (isset($params['qty'])) {
                $filter = new Zend_Filter_LocalizedToNormalized(
                    array('locale' => Mage::app()->getLocale()->getLocaleCode())
                );
                $params['qty'] = $filter->filter($params['qty']);
            }

            $product = $this->_initProduct();
            $related = $this->getRequest()->getParam('related_product');

            /**
             * Check product availability
             */
            if (!$product) {
                $this->_goBack();
                return;
            }

            $cart->addProduct($product, $params);
            if (!empty($related)) {
                $cart->addProductsByIds(explode(',', $related));
            }

            $cart->save();

            $this->_getSession()->setCartWasUpdated(true);

            /**
             * @todo remove wishlist observer processAddToCart
             */
            Mage::dispatchEvent('checkout_cart_add_product_complete',
                array('product' => $product, 'request' => $this->getRequest(), 'response' => $this->getResponse())
            );

            if (!$this->_getSession()->getNoCartRedirect(true)) {
                if (!$cart->getQuote()->getHasError()) {
                    $message = $this->__('%s was added to your shopping cart.', Mage::helper('core')->escapeHtml($product->getName()));
                    $this->_getSession()->addSuccess($message);
                }
                // CHANGES START
                $showConfirmationPage = $this->getRequest()->getParam('confirm');
                if ($showConfirmationPage) {
                    $this->_redirect('*/*/confirmation');
                } else {
                    $this->_goBack();
                }
                // CHANGES END
            }
        } catch (Mage_Core_Exception $e) {
            if ($this->_getSession()->getUseNotice(true)) {
                $this->_getSession()->addNotice(Mage::helper('core')->escapeHtml($e->getMessage()));
            } else {
                $messages = array_unique(explode("\n", $e->getMessage()));
                foreach ($messages as $message) {
                    $this->_getSession()->addError(Mage::helper('core')->escapeHtml($message));
                }
            }

            $url = $this->_getSession()->getRedirectUrl(true);
            if ($url) {
                $this->getResponse()->setRedirect($url);
            } else {
                $this->_redirectReferer(Mage::helper('checkout/cart')->getCartUrl());
            }
        } catch (Exception $e) {
            $this->_getSession()->addException($e, $this->__('Cannot add the item to shopping cart.'));
            Mage::logException($e);
            $this->_goBack();
        }
    }

    public function confirmationAction()
    {
        $lastAddedProductId = Mage::getSingleton('checkout/session')->getLastAddedProductId(true);
        $product = Mage::getModel('catalog/product')->load($lastAddedProductId);

        if (!$product->getId()) {
            $this->_redirect('checkout/cart');
            return;
        }
        Mage::register('product', $product);
        $this->loadLayout()
            ->_initLayoutMessages('checkout/session')
            ->_initLayoutMessages('catalog/session')
            ->renderLayout();
    }
}
