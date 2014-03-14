<?php

class Lesite_Core_Helper_Js extends Mage_Core_Helper_Js
{
	/**
     * Retrieve framed javascript
     *
     * @param   string $script
     * @return  script
     */
    public function getScript($script)
    {
        return "<script>\n{$script}\n</script>\n";
    }

    /**
     * Retrieve javascript include code
     *
     * @param   string $file
     * @return  string
     */
    public function includeScript($file)
    {
        return '<script src="'.$this->getJsUrl($file).'"></script>'."\n";
    }

    /**
     * Retrieve
     *
     * @param   string $file
     * @return  string
     */
    public function includeSkinScript($file)
    {
        return '<script src="'.$this->getJsSkinUrl($file).'"></script>'."\n";
    }
}