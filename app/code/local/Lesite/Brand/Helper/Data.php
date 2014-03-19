<?php

/*
 * Brand by Lesite
 * @copyright   Copyright (c) 2014 Lesite http://www.lesite.ca/ *
 */

class Lesite_Brand_Helper_Data extends Mage_Core_Helper_Abstract {

    const XML_FRONTEND_LINK = 'brand/frontend/link';

    public function refineUrlKey($urlKey) {
        for($i=0;$i<5;$i++)
		{
			$urlKey = str_replace("  "," ",$urlKey);
		}
		$chars = array(
			'Š'=>'S', 'š'=>'s', 'Đ'=>'Dj', 'đ'=>'dj', 'Ž'=>'Z', 'ž'=>'z', 'Č'=>'C', 'č'=>'c', 'Ć'=>'C', 'ć'=>'c',
			'À'=>'A', 'Á'=>'A', 'Â'=>'A', 'Ã'=>'A', 'Ä'=>'A', 'Å'=>'A', 'Æ'=>'A', 'Ç'=>'C', 'È'=>'E', 'É'=>'E',
			'Ê'=>'E', 'Ë'=>'E', 'Ì'=>'I', 'Í'=>'I', 'Î'=>'I', 'Ï'=>'I', 'Ñ'=>'N', 'Ò'=>'O', 'Ó'=>'O', 'Ô'=>'O',
			'Õ'=>'O', 'Ö'=>'O', 'Ø'=>'O', 'Ù'=>'U', 'Ú'=>'U', 'Û'=>'U', 'Ü'=>'U', 'Ý'=>'Y', 'Þ'=>'B', 'ß'=>'Ss',
			'à'=>'a', 'á'=>'a', 'â'=>'a', 'ã'=>'a', 'ä'=>'a', 'å'=>'a', 'æ'=>'a', 'ç'=>'c', 'è'=>'e', 'é'=>'e',
			'ê'=>'e', 'ë'=>'e', 'ì'=>'i', 'í'=>'i', 'î'=>'i', 'ï'=>'i', 'ð'=>'o', 'ñ'=>'n', 'ò'=>'o', 'ó'=>'o',
			'ô'=>'o', 'õ'=>'o', 'ö'=>'o', 'ø'=>'o', 'ù'=>'u', 'ú'=>'u', 'û'=>'u', 'ý'=>'y', 'ý'=>'y', 'þ'=>'b',
			'ÿ'=>'y', 'Ŕ'=>'R', 'ŕ'=>'r',
		);
		$newUrlKey = strtr($urlKey, $chars);
		$newUrlKey = str_replace(" ","-",$newUrlKey);
		$newUrlKey = htmlspecialchars(strtolower($newUrlKey));
		$newUrlKey = preg_replace('/[^a-zA-Z0-9_ %\[\]\.\(\)%&-]/s', '', $newUrlKey);
		return $newUrlKey;
    }

    public function refineLogoName($logoName) {
        for ($i = 0; $i < 5; $i++) {
            $logoName = str_replace("  ", " ", $logoName);
        }
        $logoName = str_replace(" ", "-", $logoName);
        $logoName = strtolower($logoName);

        return $logoName;
    }

    public function getLogoPath($brandId) {
        //$brandLogoPath = Mage::getBaseDir('media') . DS .'brand' .DS. strtolower(substr($brandName,0,1)).substr(md5($brandName),0,10). $this->refineUrlKey($brandName);
        $brandLogoPath = Mage::getBaseDir('media') . DS . 'brand' . DS . $brandId;
        return $brandLogoPath;
    }

    public function getLogoPathCache($brandId) {
        //$brandLogoPathCache = Mage::getBaseDir('media') . DS .'brand' . DS .'cache'. DS . strtolower(substr($brandName,0,1)). substr(md5($brandName),0,10). $this->refineUrlKey($brandName);	
        $brandLogoPathCache = Mage::getBaseDir('media') . DS . 'brand' . DS . 'cache' . DS . $brandId;
        return $brandLogoPathCache;
    }

    public function getBannerPath($brandId) {
        //$brandBannerPath = Mage::getBaseDir('media') . DS .'brand' . DS .'banner'. DS . strtolower(substr($brandName,0,1)). substr(md5($brandName),0,10). $this->refineUrlKey($brandName);	
        $brandBannerPath = Mage::getBaseDir('media') . DS . 'brand' . DS . 'banner' . DS . $brandId;
        return $brandBannerPath;
    }

    public function uploadBrandLogo($brandId, $logoFile) {
        $this->createLogoFolder($brandId);

        $brandLogoPath = $this->getLogoPath($brandId);
        $brandLogoPathCache = $this->getLogoPathCache($brandId);

        $logoName = "";
        $newLogoName = "";
        if (isset($logoFile['name']) && $logoFile['name'] != '') {
            try {
                /* Starting upload */
                $logoName = $logoFile['name'];
                $uploader = new Varien_File_Uploader('logo');
                $newLogoName = $this->refineLogoName($logoName);
                // Any extention would work
                $uploader->setAllowedExtensions(array('jpg', 'jpeg', 'gif', 'png'));
                $uploader->setAllowRenameFiles(true);

                $uploader->setFilesDispersion(false);
                $uploader->save($brandLogoPath, $newLogoName);
                $newLogoName = $uploader->getUploadedFileName();
                $fileImg = new Varien_Image($brandLogoPath . DS . $newLogoName);
                $fileImg->keepAspectRatio(true);
                $fileImg->keepFrame(true);
                $fileImg->keepTransparency(true);
                $fileImg->constrainOnly(false);
                $fileImg->backgroundColor(array(255, 255, 255));

                $fileImg->resize(60, 60);
                $fileImg->save($brandLogoPathCache . DS . $newLogoName, null);

                /* if($newLogoName != $logoName){
                  copy($brandLogoPath .DS. $logoName,$brandLogoPath .DS.$newLogoName);
                  unlink($brandLogoPath.DS.$logoName);
                  } */
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }

            $logoName = $newLogoName;
        }
        return $logoName;
    }

    public function uploadBanner($brandId, $bannerFile) {
        $this->createBannerFolder($brandId);
        $brandBannerPath = $this->getBannerPath($brandId);
        $logoName = "";
        $newLogoName = "";
        if (isset($bannerFile['name']) && $bannerFile['name'] != '') {

            try {
                // Starting upload 
                $logoName = $bannerFile['name'];
                $uploader = new Varien_File_Uploader('banner');

                $newLogoName = $this->refineLogoName($logoName);

                // Any extention would work
                $uploader->setAllowedExtensions(array('jpg', 'jpeg', 'gif', 'png'));
                $uploader->setAllowRenameFiles(true);

                $uploader->setFilesDispersion(false);

                $uploader->save($brandBannerPath, $bannerFile['name']);
                $newLogoName = $uploader->getUploadedFileName();
                /* if($newLogoName != $logoName){
                  copy($brandBannerPath .DS. $logoName,$brandBannerPath .DS.$newLogoName);
                  unlink($brandBannerPath.DS.$logoName);
                  } */
            } catch (Exception $e) {

                Mage::getSingleton('adminhtml/session')->addError($this->getErrorMessage() . $e->getMessage());
            }

            $logoName = $newLogoName;
        }
        return $logoName;
    }

    /**
     * delete logo file of brand
     * @param type $brandName
     * @param type $logo
     * @return type
     */
    public function deleteLogoFile($brandName, $logo) {

        if (!$logo) {
            return;
        }
        $brandLogoPath = $this->getLogoPath($brandName) . DS . $logo;
        $brandLogoPathCache = $this->getLogoPathCache($brandName) . DS . $logo;



        if (file_exists($brandLogoPath)) {
            try {
                unlink($brandLogoPath);
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($this->getErrorMessage() . $e->getMessage());
            }
        }

        if (file_exists($brandLogoPathCache)) {
            try {
                unlink($brandLogoPathCache);
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($this->getErrorMessage() . $e->getMessage());
            }
        }
    }

    /**
     * delete banner file of brand
     * @param type $brandName
     * @param type $logo
     * @return type
     */
    public function deleteBannerFile($brandName, $logo) {
        if (!$logo) {
            return;
        }

        $brandBannerPath = $this->getBannerPath($brandName) . DS . $logo;
        if (file_exists($brandBannerPath)) {
            try {
                unlink($brandBannerPath);
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($this->getErrorMessage() . $e->getMessage());
            }
        }
    }

    public function createLogoFolder($brandName) {
        $brandPath = Mage::getBaseDir('media') . DS . 'brand';
        $brandPathCache = Mage::getBaseDir('media') . DS . 'brand' . DS . 'cache';

        $brandLogoPath = $this->getLogoPath($brandName);
        $brandLogoPathCache = $this->getLogoPathCache($brandName);

        if (!is_dir($brandPath)) {
            try {

                chmod(Mage::getBaseDir('media'), 0777);

                mkdir($brandPath);

                chmod($brandPath, 0777);
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($this->getErrorMessage() . $e->getMessage());
            }
        }

        if (!is_dir($brandPathCache)) {
            try {

                chmod($brandPath, 0777);

                mkdir($brandPathCache);

                chmod($brandPathCache, 0777);
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($this->getErrorMessage() . $e->getMessage());
            }
        }

        if (!is_dir($brandLogoPath)) {
            try {
                chmod($brandPath, 0777);

                mkdir($brandLogoPath);

                chmod($brandLogoPath, 0777);
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($this->getErrorMessage() . $e->getMessage());
            }
        }

        if (!is_dir($brandLogoPathCache)) {
            try {

                mkdir($brandLogoPathCache);

                chmod($brandLogoPathCache, 0777);
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($this->getErrorMessage() . $e->getMessage());
            }
        }
    }

    public function createBannerFolder($brandId) {
        $brandPath = Mage::getBaseDir('media') . DS . 'brand';
        $brandBannerPath = Mage::getBaseDir('media') . DS . 'brand' . DS . 'banner';

        $brandLogoPath = $this->getLogoPath($brandId);
        $brandBannerPath = $this->getBannerPath($brandId);

        if (!is_dir($brandPath)) {
            try {

                chmod(Mage::getBaseDir('media'), 0777);

                mkdir($brandPath);

                chmod($brandPath, 0777);
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($this->getErrorMessage() . $e->getMessage());
            }
        }

        if (!is_dir($brandBannerPath)) {
            try {

                chmod($brandPath, 0777);

                mkdir($brandBannerPath);

                chmod($brandBannerPath, 0777);
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($this->getErrorMessage() . $e->getMessage());
            }
        }

        if (!is_dir($brandLogoPath)) {
            try {
                chmod($brandPath, 0777);

                mkdir($brandLogoPath);

                chmod($brandLogoPath, 0777);
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($this->getErrorMessage() . $e->getMessage());
            }
        }

        if (!is_dir($brandBannerPath)) {
            try {

                mkdir($brandBannerPath);

                chmod($brandBannerPath, 0777);
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($this->getErrorMessage() . $e->getMessage());
            }
        }
    }

    public function getUrlLogo($brandId) {
        $brandLogoPathUrl = Mage::getBaseUrl('media') . 'brand/' . $brandId;
        return $brandLogoPathUrl;
    }

    public function getUrlBanner($brandId) {
        //$brandLogoPathUrl = Mage::getBaseUrl('media') .'brand/banner/'. strtolower(substr($brandName,0,1)). substr(md5($brandName),0,10) . $this->refineUrlKey($brandName);
        $brandLogoPathUrl = Mage::getBaseUrl('media') . 'brand/banner/' . $brandId;
        return $brandLogoPathUrl;
    }

    public function getUrlLogoPath($brandId) {
        //$brandLogoPathUrl = Mage::getBaseUrl('media') .'brand/cache/'. strtolower(substr($brandName,0,1)). substr(md5($brandName),0,10) . $this->refineUrlKey($brandName);	
        $brandLogoPathUrl = Mage::getBaseUrl('media') . 'brand/cache/' . $brandId;
        return $brandLogoPathUrl;
    }

    public function getUrlBannerPath($brandId) {
        //$brandBannerPathUrl = Mage::getBaseUrl('media') .'brand/banner/'. strtolower(substr($brandName,0,1)). substr(md5($brandName),0,10) . $this->refineUrlKey($brandName);	
        $brandBannerPathUrl = Mage::getBaseUrl('media') . 'brand/banner/' . $brandId;
        return $brandBannerPathUrl;
    }

    public function getTablePrefix() {
        $tableName = Mage::getResourceModel('brand/brand')->getTable('brand');

        $prefix = str_replace('brand', '', $tableName);

        return $prefix;
    }

    public function getBrandUrl() {
        $setlink = Mage::getStoreConfig('brand/general/router');
        $url = $this->_getUrl($setlink, array());
        return $url;
    }

    

    public function getFeaturedBrands() {
        $storeId = Mage::app()->getStore()->getId();
        $brandCollection = Mage::getResourceModel('brand/brand_collection')
                ->setStoreId($storeId)
                ->addFieldToFilter('is_featured', array('eq' => 1))
                ->addFieldToFilter('status', array('eq' => 1));
        //$brandCollection->setStoreId($storeId);
        return $brandCollection;
    }

    public function copyLogo($fromName, $fromLogo, $toName, $toLogo) {
        $this->createLogoFolder($toName);
        $newPath = $this->getLogoPath($toName) . DS . $toLogo;
        $oldPath = $this->getLogoPath($fromName) . DS . $fromLogo;
        $newPathCache = $this->getLogoPathCache($toName) . DS . $toLogo;
        $oldPathCache = $this->getLogoPathCache($fromName) . DS . $fromLogo;
        copy($oldPath, $newPath);
        copy($oldPathCache, $newPathCache);
    }

    public function copyBanner($fromName, $fromLogo, $toName, $toLogo) {
        $this->createBannerFolder($toName);
        $newPath = $this->getBannerPath($toName) . DS . $toLogo;
        $oldPath = $this->getBannerPath($fromName) . DS . $fromLogo;
        copy($oldPath, $newPath);
    }

    public function getFeatureModeCode() {
        return 1;
    }

    

}
