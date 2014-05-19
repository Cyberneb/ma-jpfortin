<?php

class Lesite_ProductAutocategory_Model_System_Config_Source_Category
{
    /**
     * Retrieve Category Option array
     *
     * @return array
     */
    public function toOptionArray($addEmpty = true)
    {
        $collection = Mage::getResourceModel('catalog/category_collection');
        $collection->addAttributeToSelect('name')
            ->addFieldToFilter('level', array('in' => array(1,2)))
            ->setOrder('level')
            ->load();

        $options = array();

        if ($addEmpty) {
            $options[] = array(
                'label' => Mage::helper('adminhtml')->__('-- Please Select a Category --'),
                'value' => ''
            );
        }

        foreach ($this->_prepareOptionTree($collection) as $node) {
            $options[] = $node;
        }

        return $options;
    }

    /**
     * Prepare option data for select
     *
     * @param Mage_Catalog_Model_Resource_Collection $collection
     * @return array
     */
    protected function _prepareOptionTree($collection)
    {
        $tree = array();
        foreach ($collection as $category) {
            if ($category->getLevel() == 1) {
                if (!isset($tree[$category->getId()])) {
                    $tree[$category->getId()] = array();
                }
                $tree[$category->getId()]['label'] = $category->getName();
            } elseif ($category->getLevel() == 2) {
                if (!isset($tree[$category->getParentId()])) {
                    $tree[$category->getParentId()] = array('value' => array());
                }

                $tree[$category->getParentId()]['value'][] = array(
                    'label' => $category->getName(), 'value' => $category->getId()
                );
            }
        }
        return $tree;
    }
}
