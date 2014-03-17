<?php

class Lesite_Slider_Model_Fileuploader {

    private $allowedExtensions = array();
    private $sizeLimit = 10485760;   
    private $filemodel;

    function __construct(){
    
    $helper = Mage::helper('lookbookslider');
    $sizeLimit      = $helper->getMaxUploadFilesize();    
    $allowed_extensions = explode(',',$helper->getAllowedExtensions());
                   
        $this->allowedExtensions = array_map("strtolower", $allowed_extensions);
        if ($sizeLimit>0) $this->sizeLimit = $sizeLimit;      

        if (isset($_GET['qqfile'])) {
            $this->filemodel = Mage::getModel('lookbookslider/uploadedfilexhr');
        } elseif (isset($_FILES['qqfile'])) {
            $this->filemodel = Mage::getModel('lookbookslider/uploadedfileform');
        } else {
            $this->filemodel = false; 
        }
                                       
    }
    
    public function checkServerSettings(){        
        $postSize = $this->toBytes(ini_get('post_max_size'));
        $uploadSize = $this->toBytes(ini_get('upload_max_filesize'));        
        
        if ($postSize < $this->sizeLimit || $uploadSize < $this->sizeLimit){
            $size = max(1, $this->sizeLimit / 1024 / 1024) . 'M';             
            return array('error' => Mage::helper('lookbookslider')->__("File can't be uploaded. Increase post_max_size and upload_max_filesize to %s", $size));    
        }
        return true; 
               
    }
    
    private function toBytes($str){
        $val = trim($str);
        $last = strtolower($str[strlen($str)-1]);
        switch($last) {
            case 'g': $val *= 1024;
            case 'm': $val *= 1024;
            case 'k': $val *= 1024;        
        }
        return $val;
    }
        
    /**
     * Returns array('success'=>true) or array('error'=>'error message')
     */
 function handleUpload($uploadDirectory, $slider_dimensions, $replaceOldFile = FALSE){
        if (!is_writable($uploadDirectory)){
            return array('error' => Mage::helper('lookbookslider')->__("File can't be uploaded. Upload directory isn't writable."));
        }
        
        if (!$this->filemodel){
            return array('error' => Mage::helper('lookbookslider')->__("Uploader error. File was not uploaded."));
        }
        
        $size = $this->filemodel->getSize();
        
        if ($size == 0) {
            return array('error' => Mage::helper('lookbookslider')->__("File is empty"));
        }
        
        if ($size > $this->sizeLimit) {
            return array('error' => Mage::helper('lookbookslider')->__("File is too large"));
        }
        
        $pathinfo = pathinfo($this->filemodel->getName());
        $filename = $pathinfo['filename'];
        //$filename = md5(uniqid());
        $filename = uniqid();
        $ext = $pathinfo['extension'];

        if($this->allowedExtensions && !in_array(strtolower($ext), $this->allowedExtensions)){
            $these = implode(', ', $this->allowedExtensions);
            return array('error' => Mage::helper('lookbookslider')->__("File can't be uploaded. It has an invalid extension, it should be one of %s.", $these));
        }
        
        if(!$replaceOldFile){
            /// don't overwrite previous files that were uploaded
            while (file_exists($uploadDirectory . $filename . '.' . $ext)) {
                $filename .= rand(10, 99);
            }
        }

        if ($this->filemodel->save($uploadDirectory . $filename . '.' . $ext)){  
            $imgPathFull = $uploadDirectory . $filename . '.' . $ext;
			
            $image_dimensions = Mage::helper('lookbookslider')->getImageDimensions($imgPathFull);
            if (!isset($image_dimensions['error'])) {                                                       
	                    if ($image_dimensions['width'] > $slider_dimensions['width'] || $image_dimensions['height'] > $slider_dimensions['height'])
	                        {
	                            $resized_image = new Varien_Image($imgPathFull);
	                            $resized_image->constrainOnly(TRUE);
	                            $resized_image->keepAspectRatio(TRUE);
	                            $resized_image->keepTransparency(TRUE);
	                            $resized_image->resize($slider_dimensions['width'],$slider_dimensions['height']);
	                            $resized_image->save($imgPathFull);
	                            $image_dimensions = Mage::helper('lookbookslider')->getImageDimensions($imgPathFull);
	                        }
                         else {
                            $cached_images_dir = $uploadDirectory.$slider_dimensions['width'].'X'.$slider_dimensions['height'];
                            if (!file_exists($cached_images_dir)) {
                              mkdir($cached_images_dir);
                              @chmod($cached_images_dir, 0777);
                            }
                            $file_copy = $cached_images_dir.DS.$filename . '.' . $ext;
                            copy($imgPathFull, $file_copy);
                         }
 			}
			else
			{
 				return array('error'=> Mage::helper('lookbookslider')->__("Could not get uploaded image dimensions."));
			}
            return array('success'=>true, 'filename'=>$filename . '.' . $ext, 'dimensions' => $image_dimensions);
        } else {
            return array('error'=> Mage::helper('lookbookslider')->__("Could not save uploaded file. The upload was cancelled, or server error encountered"));
        }
        
    }        
}
