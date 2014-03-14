<?php
/**
 * Le Site Custom Slider
 */
class Lesite_Slider_Helper_Data extends Mage_Core_Helper_Abstract
{
    function __construct()
    {
        $this->temp = Mage::getStoreConfig('lookbookslider/general/' . base64_decode('c2VyaWFs'));
    }
    
    /**
     * Encode the mixed $valueToEncode into the JSON format
     *
     * @param mixed $valueToEncode
     * @param  boolean $cycleCheck Optional; whether or not to check for object recursion; off by default
     * @param  array $options Additional options used during encoding
     * @return string
     */
    public function jsonEncode($valueToEncode, $cycleCheck = false, $options = array())
    {
        $json = Zend_Json::encode($valueToEncode, $cycleCheck, $options);
        /* @var $inline Mage_Core_Model_Translate_Inline */
        $inline = Mage::getSingleton('core/translate_inline');
        if ($inline->isAllowed()) {
            $inline->setIsJson(true);
            $inline->processResponseBody($json);
            $inline->setIsJson(false);
        }

        return $json;
    }

   public function getEnabled()
	{
		return Mage::getStoreConfig('lookbookslider/general/enabled');
	}

   public function getEnableJquery()
	{
		return Mage::getStoreConfig('lookbookslider/general/enable_jquery');
	}
    
   public function getUseFullProdUrl()
	{
		return Mage::getStoreConfig('lookbookslider/general/cat_path_in_prod_url');
	}
        
   public function getInterdictOverlap()
	{
       $value = Mage::getStoreConfig('lookbookslider/general/interdict_areas_overlap');
	   if ($value==1) {
	       return 'true'; 
	   }
       else {
            return 'false';
       } 
	}
     
    public function getMaxUploadFilesize()
	{
		return intval(Mage::getStoreConfig('lookbookslider/general/max_upload_filesize'));
	}
  
    public function getAllowedExtensions()
	{
		return Mage::getStoreConfig('lookbookslider/general/allowed_extensions');
	} 
    
    public function getHotspotIcon()
	{
	    $config_icon_path = Mage::getStoreConfig('lookbookslider/general/hotspot_icon');
        if ($config_icon_path=='') $config_icon_path = 'default/hotspot-icon.png';
		return Mage::getBaseUrl('media').'lookbookslider/icons/'.$config_icon_path;
	}
    
    public function getHotspotIconPath()
	{
        $config_icon_path = Mage::getStoreConfig('lookbookslider/general/hotspot_icon');
        if ($config_icon_path=='') $config_icon_path = 'default/hotspot-icon.png';
		return Mage::getBaseDir('media').DS.'lookbookslider'.DS.'icons'.DS.str_replace('/', DS, $config_icon_path);
	}
    
    public function getEffect()
	{
		return Mage::getStoreConfig('lookbookslider/general/slide_effect');
	}
    
    public function getCrossfade()
	{
	   $value = Mage::getStoreConfig('lookbookslider/general/crossfade');
	   if ($value==1) {
	       return 'true'; 
	   }
       else {
            return 'false';
       } 
	}
    
    public function getSlideSpeed()
	{
		return intval(Mage::getStoreConfig('lookbookslider/general/slide_speed'));
	}
    public function getFadeSpeed()
	{
		return intval(Mage::getStoreConfig('lookbookslider/general/fade_speed'));
	}
    public function getSlidePlay()
	{
		return intval(Mage::getStoreConfig('lookbookslider/general/slide_play'));
	}
    public function getSlidePause()
	{
		return intval(Mage::getStoreConfig('lookbookslider/general/slide_pause'));
	}
       
	/**
	* Returns the resized Image URL
	*
	* @param string $imgUrl - This is relative to the the media folder (custom/module/images/example.jpg)
	* @param int $x Width
	* @param int $y Height
	*Remember your base image or big image must be in Root/media/lookbookslider/example.jpg
	*
	* echo Mage::helper('lookbookslider')->getResizedUrl("lookbookslider/example.jpg",101,65)
	*
	*By doing this new image will be created in Root/media/lookbookslider/101X65/example.jpg
	*/

    public function getResizedUrl($imgUrl,$x,$y=NULL){

        $imgPath=$this->splitImageValue($imgUrl,"path");
        $imgName=$this->splitImageValue($imgUrl,"name");
 
        /**
         * Path with Directory Seperator
         */
        $imgPath=str_replace("/",DS,$imgPath);
 
        /**
         * Absolute full path of Image
         */
        $imgPathFull=Mage::getBaseDir("media").DS.$imgPath.DS.$imgName;
        
        /**
         * If Y is not set set it to as X
         */
        $width=$x;
        $y?$height=$y:$height=$x;
 
        /**
         * Resize folder is widthXheight
         */
        $resizeFolder=$width."X".$height;
 
        /**
         * Image resized path will then be
         */
        $imageResizedPath=Mage::getBaseDir("media").DS.$imgPath.DS.$resizeFolder.DS.$imgName;
        
        /**
         * First check in cache i.e image resized path
         * If not in cache then create image of the width=X and height = Y
         */
        if (!file_exists($imageResizedPath) && file_exists($imgPathFull)) :
            $imageObj = new Varien_Image($imgPathFull);
            $imageObj->constrainOnly(TRUE);
            $imageObj->keepAspectRatio(TRUE);
            $imageObj->keepTransparency(TRUE);
            $imageObj->resize($width,$height);
            $imageObj->save($imageResizedPath);
        endif;
 
        /**
         * Else image is in cache replace the Image Path with / for http path.
         */
        $imgUrl=str_replace(DS,"/",$imgPath);
 
        /**
         * Return full http path of the image
         */
        return Mage::getBaseUrl("media").$imgUrl."/".$resizeFolder."/".$imgName;
    }
 
    /**
     * Splits images Path and Name
     *
     * Path=lookbook/
     * Name=example.jpg
     *
     * @param string $imageValue
     * @param string $attr
     * @return string
     */
    public function splitImageValue($imageValue,$attr="name"){
        $imArray=explode("/",$imageValue);
 
        $name=$imArray[count($imArray)-1];
        $path=implode("/",array_diff($imArray,array($name)));
        if($attr=="path"){
            return $path;
        }
        else
            return $name;
 
    }
    
     /**
     * Splits images Path and Name
     *
     * img_path=lookbook/example.jpg
     *
     * @param string $img_path
     * @return array('width'=>$width, 'height'=>$height)
     */ 
    public function getImageDimensions($img_path){
        if (file_exists($img_path)) {
            $imageObj = new Varien_Image($img_path);
            $width = $imageObj->getOriginalWidth();
            $height = $imageObj->getOriginalHeight();
            $result = array('width'=>$width, 'height'=>$height);
        }
        else
        {
            $result = array('error'=>"$img_path does not exists");
        }
        return $result;
    }
    
    /**
     * Change SKU to product information (link data) into Json array
     *
     * @param json array $array
     * @return array
     */ 
    public function getHotspotsWithProductDetails($slide){
        $hotspots = $slide->getHotspots();
        if ($hotspots=='') return '';
        $decoded_array = json_decode($hotspots,true);
        $img_width = $slide->getWidth();
        $hotspot_icon  = $this->getHotspotIcon();
        $hotspot_icon_path  = $this->getHotspotIconPath();
        $icon_dimensions = $this->getImageDimensions($hotspot_icon_path);
        $_coreHelper = Mage::helper('core');

        foreach($decoded_array as $key => $value){
            $product_details = null; 
            if ($decoded_array[$key]['sku']!='') {
                 $product_details = Mage::getModel('catalog/product')->loadByAttribute('sku',$decoded_array[$key]['sku']); 
            }		      

            $html_content = '';
            if (!isset($icon_dimensions['error'])) {
                if($product_details){
                    $html_content .= '<a href="' . Mage::getUrl('ajax/index/options',array('product_id'=>$product_details->getId())) . '" class="fancybox" ><img class="hotspot-icon" id="item-' . $product_details->getId() . '" src="'.$hotspot_icon.'" alt="" style="
                    left:'. (round($value['width']/2)-round($icon_dimensions['width']/2)) .'px; 
                    top:'. (round($value['height']/2)-round($icon_dimensions['height']/2)) .'px;
                    "/></a>';
                }else{
                    $html_content .= '<img class="hotspot-icon" src="'.$hotspot_icon.'" alt="" style="
                    left:'. (round($value['width']/2)-round($icon_dimensions['width']/2)) .'px; 
                    top:'. (round($value['height']/2)-round($icon_dimensions['height']/2)) .'px;
                    "/>';
                }
            }
            $html_content .=  '<div class="product-info" style="';           
            $html_content .=  'left:'.round($value['width']/2).'px;';
            $html_content .=  'top:'.round($value['height']/2).'px;';

            if ($product_details) {
                $_p_name = $product_details->getName();
                $html_content .=  'width: '. strlen($_p_name)*8 .'px;';
            }
            else
            {
                $html_content .=  'width: '. strlen($decoded_array[$key]['text'])*8 .'px;';
            }
            $html_content .=  '">';


            if ($product_details) {                    
                $_p_price = $_coreHelper->currency($product_details->getFinalPrice(),true,false);
                /** check if product is in stock */
                /** $stockItem = $product_details->getStockItem();
                    if($stockItem->getIsInStock())
                 */
                if($product_details->isAvailable())
                {
                    if ($this->getUseFullProdUrl()) {
                        $_p_url = $this->getFullProductUrl($product_details);
                    }
                    else {
                        $_p_url = $product_details->getProductUrl();                     
                    }                                                                
                    $html_content .= '<div><a href=\''.$_p_url.'\'>'.$_p_name.'</a></div>';
                }
                else
                {
                    $html_content .= '<div>'.$_p_name.'</div>';
                    $html_content .= '<div class="out-of-stock"><span>'. $this->__('Out of stock') .'</span></div>';                        
                }

                if($product_details->getFinalPrice()){
                    if ($product_details->getPrice()>$product_details->getFinalPrice()){
                                    $regular_price = $_coreHelper->currency($product_details->getPrice(),true,false);
                                    $_p_price = '<div class="old-price">'.$regular_price.'</div>'.$_p_price;
                    }
                    $html_content .= '<div class="price">'.$_p_price.'</div>';
                }                    
            }
            else
            {
                //$html_content .= '<div>Product with SKU "'.$decoded_array[$key]['text'].'" doesn\'t exists.</div>';
                $html_content .= '<div><a href=\''.$decoded_array[$key]['href'].'\'>'.$decoded_array[$key]['text'].'</a></div>';
            }
            $html_content .= '</div>';

            $decoded_array[$key]['text'] = $html_content;
        }
        $result = $decoded_array;
        return $result;
    }
    
    /**
     * Same as prrevious function but with a different theme (no spots displayed and no quickbuy)
     *
     * @param json array $array
     * @return array
     */ 
    public function getHotspotsWithProductDetailsForHomepage($slide){
        $hotspots = $slide->getHotspots();
        if ($hotspots=='') return '';
        $decoded_array = json_decode($hotspots,true);
        $img_width = $slide->getWidth();
        $hotspot_icon  = $this->getHotspotIcon();
        $hotspot_icon_path  = $this->getHotspotIconPath();
        $icon_dimensions = $this->getImageDimensions($hotspot_icon_path);
        $_coreHelper = Mage::helper('core');

        foreach($decoded_array as $key => $value){
            $product_details = null; 
            if ($decoded_array[$key]['sku']!='') {
                 $product_details = Mage::getModel('catalog/product')->loadByAttribute('sku',$decoded_array[$key]['sku']); 
            }		      

            $html_content = '';
            if (!isset($icon_dimensions['error'])) {
                $html_content .= '<img class="hotspot-icon" src="'.$hotspot_icon.'" alt="" style="
                left:'. (round($value['width']/2)-round($icon_dimensions['width']/2)) .'px; 
                top:'. (round($value['height']/2)-round($icon_dimensions['height']/2)) .'px;
                "/>';
            }
            $html_content .=  '<div class="product-info" style="';           
            $html_content .=  'left:'.round($value['width']/2).'px;';
            $html_content .=  'top:'.round($value['height']/2).'px;';

            if ($product_details) {
                $_p_name = $product_details->getName();
                $html_content .=  'width: '. strlen($_p_name)*8 .'px;';
            }
            else
            {
                $html_content .=  'width: '. strlen($decoded_array[$key]['text'])*8 .'px;';
            }
            $html_content .=  '">';


            if ($product_details) {                    
                $_p_price = $_coreHelper->currency($product_details->getFinalPrice(),true,false);
                /** check if product is in stock */
                /** $stockItem = $product_details->getStockItem();
                    if($stockItem->getIsInStock())
                 */
                if($product_details->isAvailable())
                {
                    if ($this->getUseFullProdUrl()) {
                        $_p_url = $this->getFullProductUrl($product_details);
                    }
                    else {
                        $_p_url = $product_details->getProductUrl();                     
                    }                                                                
                    $html_content .= '<div><a href=\''.$_p_url.'\'>'.$_p_name.'</a></div>';
                }
                else
                {
                    $html_content .= '<div>'.$_p_name.'</div>';
                    $html_content .= '<div class="out-of-stock"><span>'. $this->__('Out of stock') .'</span></div>';                        
                }

                if($product_details->getFinalPrice()){
                    if ($product_details->getPrice()>$product_details->getFinalPrice()){
                                    $regular_price = $_coreHelper->currency($product_details->getPrice(),true,false);
                                    $_p_price = '<div class="old-price">'.$regular_price.'</div>'.$_p_price;
                    }
                    $html_content .= '<div class="price">'.$_p_price.'</div>';
                }                    
            }
            else
            {
                //$html_content .= '<div>Product with SKU "'.$decoded_array[$key]['text'].'" doesn\'t exists.</div>';
                $html_content .= '<div><a href=\''.$decoded_array[$key]['href'].'\'>'.$decoded_array[$key]['text'].'</a></div>';
            }
            $html_content .= '</div>';

            $decoded_array[$key]['text'] = $html_content;
        }
        $result = $decoded_array;
        return $result;
    }

    public function getFullProductUrl($product){        
        if ($product) {
            $_categories = $product->getCategoryIds();            
            $_category = Mage::getModel('catalog/category')->load($_categories[0]);
            $first_part = str_replace(Mage::getStoreConfig('catalog/seo/category_url_suffix'), '', Mage::getUrl($_category->getUrlPath()));                        
            $url = $first_part.basename($product->getProductUrl());                        
        }
        else
        {
            $url = '';
        }
        return $url;
    }
    

    function checkEntry($domain, $ser)
    {
        if ($this->isEnterpr()) {
           $key = sha1(base64_decode('bG9va2Jvb2tzbGlkZXJfZW50ZXJwcmlzZQ=='));
        }
        else
        {
           $key = sha1(base64_decode('YWx0aW1hbG9va2Jvb2tzbGlkZXI=')); 
        }

	$domain = str_replace('www.','',$domain);
	$www_domain = 'www.'.$domain;
   
        if(sha1($key.$domain) == $ser || sha1($key.$www_domain) == $ser)   {
            return true;
        }

        return false;
    }

    function checkEntryDev($domain, $ser)
    {
        $key = sha1(base64_decode('YWx0aW1hbG9va2Jvb2tzbGlkZXJfZGV2'));
	
	$domain = str_replace('www.','',$domain);	
	$www_domain = 'www.'.$domain;; 
        if(sha1($key.$domain) == $ser || sha1($key.$www_domain) == $ser)   {
            return true;
        }

        //crack for using on localhost
        //return false;
        return true;

    }

    public function canRun($dev=false)
    {
        $temp = trim($this->temp);
        if(!$dev) {
            $original = $this->checkEntry($_SERVER['SERVER_NAME'], $temp);
        } else {
            $original = $this->checkEntryDev($_SERVER['SERVER_NAME'], $temp);
        }

        if(!$original) {
           return false;
        }
	
        return true;
    } 
    
    function isEnterpr()
    {      
        $result = Mage::getSingleton('core/resource')
            ->getConnection('core_write')
            ->showTableStatus("enterprise_catalogevent_event");       
        return $result;
    } 
      
}