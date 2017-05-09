<?php
namespace PhalconFW\I18n;

class Languages {
    
    const LANG_ENGLISH = 'en';
    
    protected $languages = array();
    
    protected $default = self::LANG_ENGLISH;
    
    protected $current = null;
    
    public function __construct ($p_default = self::LANG_ENGLISH, $p_translateCode = null)
    {
        $this->default = substr($p_default, 0, 2);
        $this->current = $this->default;
        $this->addLanguage($p_default, $p_translateCode);
    }
    public function addLanguage ($p_code, $p_translateCode = null)
    {
        if ($p_translateCode == '') {
            $p_translateCode = $p_code;
        }
        $this->languages[strtolower($p_code)] = $p_translateCode;
        
        return $this;
    }
    public function getCurrent ()
    {
        return $this->current;
    }
    public function getDefault ()
    {
        return $this->default;
    }
}