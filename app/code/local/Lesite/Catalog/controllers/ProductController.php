<?php

require_once Mage::getModuleDir('controllers', 'Mage_Catalog') . DS . 'ProductController.php';
class Lesite_Catalog_ProductController extends Mage_Catalog_ProductController
{
    public function checkavailbilityAction()
    {
        $productId = (int) $this->getRequest()->getParam('product');
        $attributes = $this->getRequest()->getParam('super_attribute');

        $result = array('success' => false);
        try {
            $product = Mage::getModel('catalog/product')->setStoreId(Mage::app()->getStore()->getId())->load($productId);
            if (!$product->getId()) {
                throw new Exception('Product is not found');
            }
            if ($product->isConfigurable()) {
                $simpleProduct = $product->getTypeInstance()->getProductByAttributes($attributes);
                if (!$simpleProduct || !$simpleProduct->getId()) {
                    throw new Exception('Product with specified options is not found');
                }
            } else {
                $simpleProduct = $product;
            }

            $result['stock'] = $this->_getProductStockStatus($simpleProduct);
            $result['success'] = true;
        } catch (Exception $e) {
            $result['error'] = $e->getMessage();
        }
        $this->getResponse()->setHeader('Content-Type', 'application/json');
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
    }

    /**
     * Get prepared array with stock status
     * array(
     *     'in_stock' => 1,
     *     'label' => 'In stock | only 5 item(s) left'
     * )
     *
     * @param Mage_Catalog_Model_Product $product
     * @return array
     */
    protected function _getProductStockStatus($product)
    {
        $inStock = $product->getStockItem()->getIsInStock();
        $qty = $product->getStockItem()->getQty();
        $minQty = $product->getStockItem()->getMinQty();
        if ($product->getStockItem()->getUseConfigMinQty()) {
            $minQty = Mage::getStoreConfig('cataloginventory/item_options/min_qty');
        }

        $availableQty = 0;
        if ($qty - $minQty > 0) {
            $availableQty = $qty - $minQty;
        }
        if ($availableQty == 0) {
            $inStock = 0;
        }
        $stockData = array(
            'in_stock' => $inStock
        );

        $inStockLeftQty = Mage::helper('lesite_catalog')->getInStockLeftQty();
        if ($inStock) {
            $stockData['label'] = $this->__('In Stock');
            if ($inStockLeftQty && $inStockLeftQty >= $availableQty) {
                $stockData['label'] .= ' | ' . $this->__('only %s item(s) left', $availableQty);
            }
        } else {
            $stockData['label'] = $this->__('Out Of Stock');
        }
        return $stockData;
    }
}