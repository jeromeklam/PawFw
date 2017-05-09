<?php
namespace PhalconFW\Menu;

use \PhalconFW\Behaviour\DIBehaviour;
use \PhalconFW\Behaviour\TranslationBehaviour;

class HeaderMenu extends AbstractMenu
{

    public function render ($p_class)
    {
        $di       = $this->getDI();
        $security = $di->get('security');
        $user     = $security->getUser();
        $str  = '<ul class="' . $p_class . '">' . PHP_EOL;
        foreach ($this->options as $option) {
            $active = '';
            if ($option['active']) {
                $active = 'active';
            }
            if ($user === false && ($option['security'] == 'PUBLIC' || $option['security'] == '')||
                $user !== false && ($option['security'] == 'PRIVATE' || $option['security'] == '')) {
                $str .= '<li class="' . $active . '"><a href="' . $option['path'] . '">' . $option['label'] . '</a></li>';
            }
        }
        $str .= '</ul>';
        
        return $str;
    }

}