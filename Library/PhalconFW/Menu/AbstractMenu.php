<?php
namespace PhalconFW\Menu;

use \PhalconFW\Behaviour\DIBehaviour;
use \PhalconFW\Behaviour\TranslationBehaviour;

/**
 * 
 * @author jeromeklam
 *
 */
abstract class AbstractMenu
{

    use DIBehaviour {
        DIBehaviour::__construct as protected __DIConstruct;
    }
    
    use TranslationBehaviour;

    /**
     * Options
     * @var array
     */
    protected $options = array();

    /**
     * 
     * @param unknown $p_position
     * @param unknown $p_menu
     * @param unknown $p_path
     * @param unknown $p_transCode
     * @param string $p_security
     * @param string $p_glyph
     * @param string $p_active
     * 
     * @return \PhalconFW\Menu\AbstractMenu
     */
    public function addOption ($p_position, $p_menu, $p_path, $p_transCode, $p_security = false, $p_glyph = '', $p_active = false)
    {
        $item = array(
            'path'     => $p_path,
            'position' => $p_position,
            'menu'     => $p_menu,
            'label'    => $this->_($p_transCode),
            'security' => $p_security,
            'glyph'    => $p_glyph,
            'active'   => $p_active
        );
        if ($p_position == '+1') {
            $this->options[] = $item;
        } else {
            if ($p_position == '-1') {
                array_unshift($this->options, $item);
            } else {
                $this->options[] = $item;
            }
        }
         
        return $this;
    }

    /**
     * Abstract method...
     * 
     * @param unknown $p_class
     * 
     * @return string
     */
    abstract function render($p_class) ;

}