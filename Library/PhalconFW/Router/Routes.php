<?php
namespace PhalconFW\Router;

/**
 * 
 * @author jeromeklam
 *
 */
class Routes
{

    /**
     * 
     * @var unknown
     */
    protected static $routes = array();
    protected static $ids    = array();
    protected static $cache  = array();
    protected static $idx404 = null;

    /**
     * Add a route
     * 
     * @param mixed $p_key
     * @param mixed $p_path
     * @param mixed $p_default
     * @param mixed $p_defaults
     * @param mixed $p_module
     * @param mixed $p_namespace
     * @param mixed $p_controller
     * @param mixed $p_handler
     * @param mixed $p_action
     * @param mixed $p_params
     * @param mixed $p_type
     * @param mixed $p_security
     * @param mixed $p_header
     * @param mixed $p_parent
     * @param mixed $p_side
     * @param mixed $p_menu
     * @param mixed $p_glyph
     * @param mixed $p_label
     * 
     * @return \PhalconFW\Router\Routes
     */
    public function add ($p_key, $p_path, $p_default, $p_defaults, $p_module, $p_namespace, $p_controller, 
                         $p_handler, $p_action, $p_params, $p_type, $p_security,
                         $p_header, $p_parent, $p_side, $p_menu, $p_glyph, $p_label, $p_rest)
    {
        if ($p_label == '') {
            $p_label = strtolower($p_module . '.' . $p_controller . '.' . $p_action . '.label');
        }
        self::$routes[$p_path] = array(
            'path'       => $p_path,
            'default'    => $p_default,
            'defaults'   => $p_defaults,
            'module'     => $p_module,
            'namespace'  => $p_namespace,
            'controller' => $p_controller,
            'handler'    => $p_handler,
            'action'     => $p_action,
            'params'     => $p_params,
            'type'       => $p_type,
            'security'   => $p_security,
            'header'     => $p_header,
            'parent'     => $p_parent,
            'side'       => $p_side,
            'menu'       => $p_menu,
            'glyph'      => $p_glyph,
            'label'      => $p_label,
            'rest'       => $p_rest
        );
        self::$cache[$p_module . '##' . $p_handler . '##' . $p_action] = $p_path;
        self::$ids[$p_key]                                             = $p_path;
        if ($p_type == '404') {
            self::$idx404 = $p_path;
        }
        
        return $this;
    }

    /**
     * 
     * @return Ambigous <multitype:, multitype:unknown Ambigous <unknown, string> >
     */
    public function getAll ()
    {
        return self::$routes;
    }

    /**
     * 
     * @return Ambigous <multitype:, multitype:unknown Ambigous <unknown, string> >|boolean
     */
    public function get404 ()
    {
        if (self::$idx404 !== null && array_key_exists(self::$idx404, self::$routes)) {
            
            return self::$routes[self::$idx404];
        }
        
        return false;
    }

    /**
     * 
     * @param unknown $p_key
     */
    public function getByKey ($p_key)
    {
        if (array_key_exists($p_key, self::$ids)) {
            if (array_key_exists(self::$ids[$p_key], self::$routes)) {
                return self::$routes[self::$ids[$p_key]];
            }
        }
        
        return false;
    }

    /**
     * 
     * @param unknown $p_module
     * @param unknown $p_handler
     * @param unknown $p_action
     * @return Ambigous <multitype:, multitype:unknown Ambigous <unknown, string> >|boolean
     */
    public function find ($p_module, $p_handler, $p_action)
    {
        $key = $p_module . '##' . $p_handler . '##' . $p_action;
        if (array_key_exists($key, self::$cache)) {
            if (array_key_exists(self::$cache[$key], self::$routes)) {
                
                return self::$routes[self::$cache[$key]];
            }
        }
        
        return false;
    }

    /**
     * 
     * @param unknown $p_type
     * @return Ambigous <unknown, Ambigous <unknown, string>>|boolean
     */
    public function findPathByType ($p_type)
    {
        foreach (self::$routes as $oneRoute) {
            if ($oneRoute['type'] == $p_type) {
                
                return $oneRoute['path'];
            }
        }
        
        return false;
    }

    /**
     * 
     * @param unknown $p_file
     * @param unknown $p_moduleName
     * @return \PhalconFW\Router\Routes
     */
    public function addRoutes ($p_file, $p_moduleName)
    {
        if (is_file($p_file)) {
            $myRoutes = include $p_file;
            if (is_array($myRoutes)) {
                foreach ($myRoutes as $key => $oneRoute) {
                    if (!isset($oneRoute['default'])) {
                        $oneRoute['default'] = $oneRoute['path'];
                    }
                    if (!isset($oneRoute['defaults'])) {
                        $oneRoute['defaults'] = array();
                    }
                    $this->add(
                        $key,
                        $oneRoute['path'],
                        $oneRoute['default'],
                        $oneRoute['defaults'],
                        $p_moduleName,
                        $oneRoute['namespace'],
                        $oneRoute['controller'],
                        $oneRoute['handler'],
                        $oneRoute['action'],
                        (isset($oneRoute['params']) ? $oneRoute['params'] : null),
                        (isset($oneRoute['type']) ? $oneRoute['type'] : 'STD'),
                        (isset($oneRoute['security']) ? $oneRoute['security'] : false),
                        (isset($oneRoute['header']) ? $oneRoute['header'] : false),
                        (isset($oneRoute['parent']) ? $oneRoute['parent'] : false),
                        (isset($oneRoute['side']) ? $oneRoute['side'] : false),
                        (isset($oneRoute['menu']) ? $oneRoute['menu'] : null),
                        (isset($oneRoute['glyph']) ? $oneRoute['glyph'] : ''),
                        (isset($oneRoute['label']) ? $oneRoute['label'] : ''),
                        (isset($oneRoute['rest']) ? $oneRoute['rest'] : false)
                    );
                }
            }
        }
        
        return $this;
    }

    /**
     * 
     * @param unknown $p_path
     * @param unknown $p_params
     * @return mixed
     */
    public function getUrl ($p_path, $p_params = array())
    {
        $url = $p_path;
        preg_match_all('/\{(?P<name>\w+)\:([^\{]+)\}/', $p_path, $matches);
        if (is_array($matches) && count($matches[0]) > 0) {
            foreach ($matches[0] as $key => $value) {
                if (isset($p_params[$matches['name'][$key]])) {
                    $url = str_replace($value, $p_params[$matches['name'][$key]], $url);
                } else {
                    $url = str_replace($value, '', $url);
                }
            }
        }
        $url = str_replace('[/]?', '/', $url);
        
        return $url;
    }

    /**
     * 
     * @param unknown $p_key
     * @param unknown $p_params
     * @param unknown $p_params1
     * @param unknown $p_params2
     * @return string
     */
    public function getUrlByKey ($p_key, $p_params = array(), $p_params1 = array(), $p_params2 = array())
    {
        $p_params = array_merge($p_params, $p_params1, $p_params2);
        $route    = $this->getByKey($p_key);
        $url      = '/';
        if ($route !== false) {
            $url = $route['path'];
            preg_match_all('/\{(?P<name>\w+)\:([^\{]+)\}/', $url, $matches);
            if (is_array($matches) && count($matches[0]) > 0) {
                foreach ($matches[0] as $key => $value) {
                    if (isset($p_params[$matches['name'][$key]])) {
                        $url = str_replace($value, $p_params[$matches['name'][$key]], $url);
                    } else {
                        if (array_key_exists($matches['name'][$key], $route['defaults'])) {
                            $url = str_replace($value, $route['defaults'][$matches['name'][$key]], $url);
                        } else {
                            $url = str_replace($value, '', $url);
                        }
                    }
                }
            }
            $url = str_replace('[/]?', '/', $url);
        }
        
        return rtrim($url, '/');
    }

    /**
     * Get all rest routes
     * 
     * @return array
     */
    public function getRestRoutes ()
    {
        $routes = array();
        foreach (self::$routes as $path => $data) {
            if ($data['rest'] !== false) {
                $routes[$path] = array(
                    'rest'       => $data['rest'],
                    'path'       => $data['path'],
                    'namespace'  => $data['namespace'],
                    'controller' => $data['controller'],
                    'action'     => $data['action']
                );
            }
        }
        
        return $routes;
    }

}