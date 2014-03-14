<?php

class Altima_Lookbookslider_Model_Config_Source_Slider_Effect extends Mage_Core_Model_Abstract
{
    const EFFECT_SLIDE	     = 'slide';
    const EFFECT_FADE	     = 'fade';
    const EFFECT_SLIDE_FADE	 = 'slide,fade';
    const EFFECT_FADE_SLIDE	 = 'fade,slide';

    static public function toOptionArray()
    {
        return array(
            self::EFFECT_SLIDE          => 'slide',
            self::EFFECT_FADE           => 'fade',
            self::EFFECT_SLIDE_FADE     => 'slide, fade',
            self::EFFECT_FADE_SLIDE     => 'fade, slide'            
        );
    }

}
