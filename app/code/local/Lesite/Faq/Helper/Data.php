<?php

/*
 * FAQ by Lesite
 * Each line should be prefixed with  * 
 */

class Lesite_Faq_Helper_Data extends Mage_Core_Helper_Abstract {

    /**
     * Path to store config if frontend output is enabled
     * @var string
     */
    const XML_PATH_ENABLED = 'faq/view/enabled';

    /**
     * Path to store config where count of faq posts per page is stored
     * @var string
     */
    const XML_PATH_ITEMS_PER_PAGE = 'faq/view/items_per_page';

    /**
     * Faq Item instance for lazy loading
     * @var Lesite_Faq_Model_Faq
     */
    protected $_faqItemInstance;
    protected $_faqCategoryInstance;

    /**
     * Checks whether faq can be displayed in the frontend
     * @param integer|string|Mage_Core_Model_Store $store * @return boolean
     */
    public function isEnabled($store = null) {
        return Mage::getStoreConfigFlag(self::XML_PATH_ENABLED, $store);
    }

    /**
     * Return the number of items per page
     * @param integer|string|Mage_Core_Model_Store $store * @return int
     */
    public function getFaqPerPage($store = null) {
        return abs((int) Mage::getStoreConfig(self::XML_PATH_ITEMS_PER_PAGE, $store));
    }

    
    /**
     * Return current faq item instance from the Registry
     * @return Lesite_Faq_Model_Item
     */
    public function getFaqItemInstance() {
        if (!$this->_faqItemInstance) {
            $this->_faqItemInstance = Mage::registry('faq_item');
            if (!$this->_faqItemInstance) {
                Mage::throwException($this->__('Faq item instance does not exist in Registry'));
            }
        }
        return $this->_faqItemInstance;
    }
    
    /**
     * Return current faq item instance from the Registry
     * @return Lesite_Faq_Model_Category
     */
    public function getFaqCategoryInstance() {
        if (!$this->_faqCategoryInstance) {
            $this->_faqCategoryInstance = Mage::registry('faq_category');
            if (!$this->_faqCategoryInstance) {
                Mage::throwException($this->__('Faq category instance does not exist in Registry'));
            }
        }
        return $this->_faqCategoryInstance;
    }

}
