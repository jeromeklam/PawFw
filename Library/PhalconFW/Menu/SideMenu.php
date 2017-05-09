<?php
namespace PhalconFW\Menu;

use \PhalconFW\Behaviour\DIBehaviour;
use \PhalconFW\Behaviour\TranslationBehaviour;

/**
 * 
 * @author jeromeklam
 *
 */
class Sidemenu extends AbstractMenu
{

    /**
     * 
     * @var unknown
     */
    protected $menu = array();
    
    /**
     * 
     * @var unknown
     */
    protected $menuopt = array();

    /**
     * 
     * @param unknown $p_path
     */
    protected function addPath ($p_path, $p_option)
    {
        $options = $this->menu;
        $active  = false;
        if ($p_option['class'] == 'active') {
            $active = true;
        }
        while ($dir = array_shift($p_path)) {
            $found = array_key_exists($dir, $options);
            if (!$found) {
                $this->menu[$dir] = array(
                    'position'   => 0,
                    'calculated' => false,
                    'label'      => $dir,
                    'title'      => $this->_('app.menu.' . $dir . '.title'),
                    'movable'    => true,
                    'active'     => $active,
                    'options'    => array(),
                    'type'       => 'menu'
                );
            } else {
                if ($active) {
                    $this->menu[$dir]['active'] = $active;
                }
            }
            $index   = $dir;
            $options = $this->menu[$dir]['options'][$p_option['label']] = array(
                    'position'   => 1,
                    'calculated' => true,
                    'label'      => $p_option['label'],
                    'title'      => $this->_('app.menu.' . $p_option['label'] . '.title'),
                    'movable'    => true,
                    'active'     => $active,
                    'type'       => 'option',
                    'option'     => $p_option
                );;
        }
        
        return $options;
    }

    /**
     * 
     */
    protected function generateMenu ()
    {
        // Standard first level options
        $this->menu['general'] = array(
            'position'   => 2,
            'calculated' => false,
            'movable'    => false,
            'label'      => 'general',
            'title'      => $this->_('app.menu.general.title'),
            'active'     => false,
            'options'    => array(),
            'type'       => 'menu'
        );
        $this->menu['settings'] = array(
            'position'   => 9999,
            'calculated' => false,
            'movable'    => false,
            'label'      => 'settings',
            'title'      => $this->_('app.menu.settings.title'),
            'active'     => false,
            'options'    => array(),
            'type'       => 'menu'
        );
        foreach ($this->menuopt as $option) {
            if ($option['path'] == 'home') {
                $active = false;
                if ($option['class'] == 'active') {
                    $active = true;
                }
                $this->menu['home'] = array(
                    'position'   => 1,
                    'calculated' => false,
                    'movable'    => false,
                    'label'      => 'home',
                    'title'      => $this->_('app.menu.home.title'),
                    'active'     => $active,
                    'type'       => 'option',
                    'option'     => $option
                );
            } else {
                if ($option['path'] == '') {
                    $option['path'] = 'other';
                }
                $path = explode('/', $option['path']);
                $this->addPath($path, $option);
            }
        }
    }

    /**
     * 
     */
    protected function sortMenu ()
    {
        $newMenu = array();
        /**
         * First
         */
        foreach ($this->menu as $key => $oneOption) {
            if ($oneOption['movable'] === false && $oneOption['position'] < 9000) {
                $newMenu[$key]           = $oneOption;
                $oneOption['calculated'] = true;
            }
        }
        /**
         * Alpha
         */
        $crt = 1000;
        foreach ($this->menu as $key => $oneOption) {
            if ($oneOption['movable'] === true) {
                $pos = 0;
                foreach ($newMenu as $nKey => $nOption) {
                    if ($nOption['position'] >= 1000 && $nOption['position'] < 9000) {
                        if ($nOption['title'] > $oneOption['title']) {
                            $pos = $nOption['position'];
                            break;
                        }
                    }
                }
                if ($pos == 0) {
                    $pos = $crt;
                }
                foreach ($newMenu as $nKey => $nOption) {
                    if ($nOption['position'] >= $pos) {
                        $newMenu[$nKey]['position']++;
                    }
                }
                $oneOption['calculated'] = true;
                $oneOption['position']   = $pos;
                $crt++;
                $newMenu[$key]           = $oneOption;
            }
        }
        /**
         * Last
         */
        foreach ($this->menu as $key => $oneOption) {
            if ($oneOption['movable'] === false && $oneOption['position'] > 9000) {
                $newMenu[$key]           = $oneOption;
                $oneOption['calculated'] = true;
            }
        }
        /**
         * We can sort with the position field...
         */
        $sort = array();
        foreach ($newMenu as $key => $oneOption) {
            $sort[] = $oneOption['position'];
        }
        array_multisort($sort, SORT_ASC, SORT_NUMERIC, $newMenu);
        /**
         * Finished
         * @var unknown
         */
        $this->menu = $newMenu;
    }

    /**
     * (non-PHPdoc)
     * @see \PhalconFW\Menu\AbstractMenu::render()
     */
    public function render ($p_class)
    {
        // Init menu
        $this->menu    = array();
        $this->menuopt = array();
        // Other stuff...
        $di       = $this->getDI();
        $security = $di->get('security');
        $user     = $security->getUser();
        $str      = '';
        $dispatch = $di->get('dispatcher');
        $request  = $di->get('request');
        $uri      = $request->getUri();
        foreach ($this->options as $option) {
            if ($user === false && ($option['security'] == 'PUBLIC' || $option['security'] == '')||
                $user !== false && ($option['security'] == 'PRIVATE' || $option['security'] == '')) {
                $icon = '';
                if ($option['glyph'] != '') {
                    $icon = '<span class="fa fa-' . $option['glyph'] . '" aria-hidden="true"></span>&nbsp;';
                }
                $class = '';
                if (strpos($uri, $option['path']) !== false) {
                    $class = 'active';
                }
                $this->menuopt[] = array(
                    'class' => $class,
                    'href'  => $option['path'],
                    'path'  => $option['menu'],
                    'icon'  => $icon,
                    'label' => $option['label']
                );
                //$str .= '<li class="' . $class . '"><a href="' . $option['path'] . '">' . $icon . '<span class="sidebar-title">' . $option['label'] . '</span></a></li>';
            }
        }
        $this->generateMenu();
        $this->sortMenu();
        foreach ($this->menu as $key => $oneOption) {
            if ($oneOption['type'] == 'option') {
                $option = $oneOption['option'];
                $class  = '';
                if ($oneOption['active'] === true) {
                    $class = 'active';
                }
                $str .= '<li class="' . $class . '"><a href="' . $option['href'] . '">' . $option['icon'] . '<span class="sidebar-title">' . $option['label'] . '</span></a></li>';
            } else {
                if (count($oneOption['options']) > 0) {
                    $class  = '';
                    if ($oneOption['active'] === true) {
                        $class = 'menu-open';
                    }
                    $str .= '<li>
                                <a class="accordion-toggle ' . $class . '" href="#">
                                  <span class="fa fa-' . $this->_('app.menu.' . $oneOption['label'] . '.icon') . '"></span>
                                  <span class="sidebar-title">' . $oneOption['title'] . '</span>
                                  <span class="caret"></span>
                                </a>
                                <ul class="nav sub-nav" style="">
                                ';
                    foreach ($oneOption['options'] as $oneOption2) {
                        $option = $oneOption2['option'];
                        $class  = '';
                        if ($oneOption2['active'] === true) {
                            $class = 'active';
                        }
                        $str .= '<li class="' . $class . '"><a href="' . $option['href'] . '">' . $option['icon'] . '<span class="sidebar-title-2">' . $option['label'] . '</span></a></li>';
                    }
                    $str .= '</ul></li>';
                }
            }
        }
        
        return $str;
    }

}
