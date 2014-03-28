<?php

/*
 * FAQ by Lesite
 * Each line should be prefixed with  * 
 */

class Lesite_Faq_Block_List extends Mage_Core_Block_Template {

    /**
     * Faq collection
     *
     * @var Lesite_Faq_Model_Resource_Faq_Collection
     */
    protected $_faqCollection = null;

    /**
     * Retrieve faq collection
     * @return Lesite_Faq_Model_Resource_Faq_Collection
     */
    protected function _getCollection() {
        return Mage::getResourceModel('faq/item_collection');
    }

    /**
     * Retrieve prepared faq collection
     * @return Lesite_Faq_Model_Resource_Faq_Collection
     */
    public function getCollection($pageSize = null) {
        if (!$this->_faqCollection || (intval($pageSize) > 0
			&& $this->_faqCollection->getSize() != intval($pageSize))
		) {
			$this->_faqCollection = Mage :: getModel('faq/item')
				->getCollection()
				//->addStoreFilter(Mage :: app()->getStore())
				->addIsActiveFilter();
			
			if (isset($pageSize) && intval($pageSize) && intval($pageSize) > 0) {
				$this->_faqCollection->setPageSize(intval($pageSize));
			}
		}
		
		return $this->_faqCollection;
    }
    
    /**
	 * Returns all active categories
	 * 
	 * @return Lesite_Faq_Model_Resource_Category_Collection
	 */
	public function getCategoryCollection()
	{
	    $categories = $this->getData('category_collection');
	    if (is_null($categories)) {
    	    $categories =  Mage::getModel('faq/category')
                    ->getCollection()
                    ->addStoreFilter(Mage::app()->getStore())
                    ->addIsActiveFilter();
            
                $this->setData('category_collection', $categories);
	    }
	    return $categories;
	}
    
    /**
    * Returns the item collection for the given category 
    * 
    * @param Lesite_Faq_Model_Category $category
    * @return Lesite_Faq_Model_Resource_Faq_Collection
    */
    public function getItemCollectionByCategory(Lesite_Faq_Model_Category $category)
    {
            $pageSize = null;
            $catQuestions =  Mage::getModel('faq/item')
                    ->getCollection()
                    ->addCategoryFilter($category)
                    ->addStoreFilter(Mage::app()->getStore())
                    ->addIsActiveFilter();
            
            if (isset($pageSize) && intval($pageSize) && intval($pageSize) > 0) {
                            $catQuestions->setPageSize(intval($pageSize));
            }
            return $catQuestions;
    }

    /**
     * Return URL to item's view page
     * @param Lesite_Faq_Model_Faq $faqItem * @return string
     */
    public function getItemUrl($faqItem) {
        return $this->getUrl('*/*/view', array('id' => $faqItem->getId()));
    }

    /**
     * Fetch the current page for the faq list
     * @return int
     */
    public function getCurrentPage() {
        return $this->getData('current_page') ? $this->getData('current_page') : 1;
    }

    /**
     * Get a pager
     * @return string|null
     */
    public function getPager() {
        $pager = $this->getChild('faq_list_pager');
        if ($pager) {
            $faqPerPage = Mage::helper('lesite_faq')->getFaqPerPage();
            $pager->setAvailableLimit(array($faqPerPage => $faqPerPage));
            $pager->setTotalNum($this->getCollection()->getSize());
            $pager->setCollection($this->getCollection());
            $pager->setShowPerPage(true);
            return $pager->toHtml();
        }
        return null;
    }

}
