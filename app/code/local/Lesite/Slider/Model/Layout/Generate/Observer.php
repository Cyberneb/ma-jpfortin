<?php
class Altima_Lookbookslider_Model_Layout_Generate_Observer {
    
	public function addStylesAndJS($observer) {            
            $helper = Mage::helper('lookbookslider');
            $has_lookbookslider = false;
            if ($helper->getEnabled()) {                                                                        
                       $_head = $this->__getHeadBlock();
                        
                        if ($_head) { 
	                            if ($this->__NeedJSandCSS()) {
	                                $_head->addFirst('skin_css', 'lookbookslider/css/hotspots.css');
	                                if ($helper->getEnableJquery()) {
	                                    $_head->addLast('js', 'jquery/jquery-1.8.2.min.js');
	                                    $_head->addLast('js', 'lookbookslider/slides.min.jquery.js');                           
	                                }
	                                else
	                                {
	                                    $_head->addLast('js','lookbookslider/slides.min.jquery.js');
	                                }                                
                                
                                        $layout = Mage::app()->getLayout();
	                                $content = $layout->getBlock('content');
	                                $block = $layout->createBlock('lookbookslider/valid');
	                                $content->insert($block); 
				     }
                                           
                        }            
        }       
    }
    /*
     * Get head block
     */
    private function __getHeadBlock() {
        return Mage::getSingleton('core/layout')->getBlock('lookbookslider_head');
    }
    
    private function __NeedJSandCSS() {
        
        $top_block = Mage::getSingleton('core/layout')->getBlock('lookbookslider_content_top');
        $bottom_block = Mage::getSingleton('core/layout')->getBlock('lookbookslider_content_bottom');
	if ($top_block) {
        	$top_sliders = $top_block->_getCollection();
	        foreach ($top_sliders as $slider) {
	            $slides = $top_block->_getSlidesCollection($slider->getId());
	            if ($slides->getSize()) return true;
	        }
	}

	if ($bottom_block) {
	        $bottom_sliders = $bottom_block->_getCollection();
	        foreach ($bottom_sliders as $slider) {
	            $slides = $bottom_block->_getSlidesCollection($slider->getId());
	            if ($slides->getSize()) return true;
	        }
	}
                       
        return false; 
    }
}