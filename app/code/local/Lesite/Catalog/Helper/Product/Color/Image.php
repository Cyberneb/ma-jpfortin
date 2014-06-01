<?php

class Lesite_Catalog_Helper_Product_Color_Image extends Mage_Core_Helper_Abstract
{
    protected $_destinationSubdir = 'product_color';

    /**
     * Upload max file size in kilobytes
     *
     * @var int
     */
    protected $_maxFileSize = 0;

    /**
     * Upload image
     *
     * @param Mage_Core_Model_Abstract $object
     * @param string $fieldName
     * @return Lesite_Catalog_Helper_Product_Color_Image
     */
    public function upload($object, $fieldName)
    {
        $file = isset($_FILES[$fieldName]) ? $_FILES[$fieldName] : null;
        if ($file && isset($file['error']) && $file['error'] == 0) {
            try {
                $uploader = new Mage_Core_Model_File_Uploader($file);
                $uploader->setFilesDispersion(true)
                    ->setAllowRenameFiles(true)
                    ->setAllowedExtensions($this->_getAllowedExtensions())
                    ->addValidateCallback('size', $this, 'validateMaxSize');
                $result = $uploader->save($this->getUploadDir());

            } catch (Exception $e) {
                Mage::throwException($e->getMessage());
                return $this;
            }

            $filename = $result['file'];
            if ($filename) {
                $object->setData($fieldName, $filename);
            }
        } else {
            $image = $object->getData($fieldName);
            if (is_array($image) && isset($image['delete']) && $image['delete']) {
                $io = new Varien_Io_File();
                $io->rm($this->getUploadDir() . $object->getOrigData($fieldName));
                $object->setData($fieldName, null);
            } else {
                $object->setData($fieldName, $object->getOrigData($fieldName));
            }
        }
        return $this;
    }

    /**
     * Return path to directory for upload file
     *
     * @return string
     */
    public function getUploadDir()
    {
        return Mage::getBaseDir('media') . DS . $this->_destinationSubdir;
    }

    /**
     * Return path to directory for upload file
     *
     * @return string
     */
    public function getUploadUrl()
    {
        return Mage::getBaseUrl('media') . $this->_destinationSubdir;
    }

    /**
     * Validation callback for checking max file size
     *
     * @param  string $filePath Path to temporary uploaded file
     * @throws Mage_Core_Exception
     */
    public function validateMaxSize($filePath)
    {
        if ($this->_maxFileSize > 0 && filesize($filePath) > ($this->_maxFileSize * 1024)) {
            throw Mage::exception('Mage_Core', Mage::helper('adminhtml')->__('Uploaded file is larger than %.2f kilobytes allowed by server', $this->_maxFileSize));
        }
    }

    /**
     * Return list of allowed extensions
     *
     * @return type
     */
    protected function _getAllowedExtensions()
    {
        return array('png', 'jpg', 'jpeg', 'gif');
    }
}
