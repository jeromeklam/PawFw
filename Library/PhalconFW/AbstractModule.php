<?php
namespace PhalconFW;

use \Phalcon\Loader;
use \Phalcon\Mvc\View;
use \Phalcon\DiInterface;
use \Phalcon\Mvc\Model\Metadata\Memory as MetaData;
use \PhalconFW\Mvc\Dispatcher;
use \PhalconFW\Tools\ICU;
use \Phalcon\Events\Manager as EventsManager;
use \PhalconFW\Mvc\View\Engine\Volt as VoltEngine;

/**
 *
 * @author jeromeklam
 *
 */
class AbstractModule
{

    /**
     *
     * @param DiInterface $di
     * @param string      $p_moduleName
     * @param string      $p_basePath
     */
    public function _registerServices (DiInterface $di, $p_moduleName, $p_basePath)
    {
        /**
         * Read configuration
         */
        $di['dispatcher'] = function() use ($p_moduleName, $di) {
            $eventsManager = new EventsManager();
            /**
             * Before dispatch
             */
            $eventsManager->attach("dispatch:beforeDispatch", function ($event, $dispatcher) use ($di, $p_moduleName) {
                $module    = $p_moduleName;
                $contoller = $dispatcher->getControllerName();
                $action    = $dispatcher->getActionName();
                $routes    = $di->get('routes');
                $myRoute   = $routes->find($module, $contoller, $action);
                $params    = $dispatcher->getParams();
                $newParams = array();
                $odd       = true;
                foreach ($dispatcher->getParams() as $paramKey => $paramValue) {
                    if (is_int($paramKey)) {
                        if ($odd) {
                            $myKey             = $paramValue;
                            $newParams[$myKey] = null;
                        } else {
                            $newParams[$myKey] = $paramValue;
                        }
                    }
                    if ($odd) {
                        $odd = false;
                    } else {
                        $odd = true;
                    }
                }
                $dispatcher->setParams(array_merge($newParams, $params));
                // @launch security plugin...
                if ($di->has('security')) {
                    $security  = $di->get('security');
                    // Verify if route is public or private...
                    $secure  = false;
                    if ($myRoute) {
                        if (isset($myRoute['security']) && $myRoute['security'] !== false) {
                            $secure = $myRoute['security'];
                        }
                        // HOME && PRIVATE -> forward dashboard... ??
                        if ($myRoute['type'] == 'HOME' && $security->getUser() !== false) {
                            $myRoute  = $routes->findPathByType('DASHBOARD');
                            if ($myRoute !== false) {
                                $dispatcher->forward(array(
                                    'action' => 'redirect',
                                    'params' => array('redirect' => $myRoute, 'code' => 301)
                                ));
                                
                                return false;
                            }
                        }
                    }
                    // Can I be here ??
                    if ($secure == 'PRIVATE' && $security->getUser() === false) {
                        $myRoute  = $routes->findPathByType('HOME');
                        if ($myRoute === false) {
                            $myRoute = '/';
                        }
                        $dispatcher->forward(array(
                            'action' => 'redirect',
                            'params' => array('redirect' => $myRoute, 'code' => 401)
                        ));
                        
                        return false;
                    }
                    
                }
            });
            /**
             * Exception in dispatcher
             */
            $eventsManager->attach("dispatch:beforeException", function ($event, $dispatcher, $exception) use ($di) {
                // Handle 404 exceptions
                if ($exception instanceof DispatchException) {
                    $routes   = $di->get('routes');
                    $myRoute  = $routes->get404();
                    $redirect = '/';
                    if ($myRoute !== false) {
                        $redirect = $myRoute['path'];
                    }
                    $dispatcher->forward(array(
                        'action' => 'redirect',
                        'params' => array('redirect' => $redirect, 'code' => 404)
                    ));
                    return false;
                }
        
                // Alternative way, controller or action doesn't exist
                if ($event->getType() == 'beforeException') {
                    switch ($exception->getCode()) {
                        case Dispatcher::EXCEPTION_HANDLER_NOT_FOUND:
                        case Dispatcher::EXCEPTION_ACTION_NOT_FOUND:
                            $routes   = $di->get('routes');
                            $myRoute  = $routes->get404();
                            $redirect = '/';
                            if ($myRoute !== false) {
                                $redirect = $myRoute['path'];
                            }
                            $dispatcher->forward(array(
                                'action' => 'redirect',
                                'params' => array('redirect' => $redirect, 'code' => 404)
                            ));
                            return false;
                    }
                }
            });
            $dispatcher = new Dispatcher();
            $dispatcher->setEventsManager($eventsManager);
            $dispatcher->setDefaultNamespace('Modules\\' . ucfirst($p_moduleName) . '\\Controllers');
            return $dispatcher;
        };
        /**
         * Setting up the view component
         */
        $di['view'] = function() use ($p_moduleName, $p_basePath, $di) {
            $templateAfter = 'public';
            if ($di->has('security')) {
                $security  = $di->get('security');
                if ($security->getUser() !== false) {
                    $templateAfter = 'private';
                }
            }
            $view = new View();
            $view->setViewsDir($p_basePath . '/views/');
            $view->setLayoutsDir('../../common/layouts/');
            $view->setPartialsDir( '../../common/partials/' );
            if (!$di->get('request')->isAjax()) {
                $view->setTemplateAfter($templateAfter);
            }
            $view->registerEngines(array(
                '.volt' => function ($view, $di) use (&$config, $templateAfter) {
                    $languages = $di->get('languages');
                    $language  = $languages->getCurrent();
                    $volt = new VoltEngine($view, $di);
                    $volt->setOptions(array(
                        'compiledPath' => APP_PATH . 'apps/var/cache/volt/' . $language . '.' . $templateAfter . '/',
                        'compiledSeparator' => '_',
                        'compileAlways' => true
                    ));
                    $volt->getCompiler()->addFunction('_',function($resolvedArgs, $exprArgs) {
                        return '$this->i18n->_(' . $resolvedArgs . ')';
                    });
                    $volt->getCompiler()->addFunction('df', function($resolvedArgs, $exprArgs) {
                        return '$'.$exprArgs[0]['expr']['value'].'->{$'.$exprArgs[1]['expr']['value'].'}';
                    });
                    $volt->getCompiler()->addFunction('strtotime', 'strtotime');
                    $volt->getCompiler()->addFunction('base64_encode', 'base64_encode');
                    return $volt;
                },
                '.phtml' => 'Phalcon\Mvc\View\Engine\Php'
            ));

            return $view;
        };
    }

}