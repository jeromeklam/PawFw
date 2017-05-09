<?php
namespace PhalconFW\Mvc;

use \Phalcon\Mvc\Dispatcher as PhalconDispatcher;
use \Phalcon\Mvc\Dispatcher\Exception as DispatcherException;
use \Phalcon\Http\Response;

class Dispatcher extends PhalconDispatcher
{

    public function forward404 ()
    {
        $routes  = $this->getDI()->get('routes');
        $myRoute = $routes->get404();
        if ($myRoute !== false) {
            // Getting a response instance
            $this->forward(array(
                'action'     => 'redirect',
                'params'     => array('redirect' => $myRoute['path'])
            ));
            return false;
        } else {
            // @todo ??
        }
        
        $this->forward(array(
            'controller' => $this->getControllerName(),
            'action'     => 'redirect',
            'params'     => array('redirect' => '/')
        ));
        return false;
    }

    /**
     * Dispatch.
     * Override it to use own logic.
     *
     * @throws \Exception
     * @return object
     */
    public function dispatch()
    {
        try {
            $routes = $this->getDI()->get('routes');
            $route  = $routes->find($this->getModuleName(), $this->getControllerName(), $this->getActionName());
            if (!$route) {
                // @todo : forward 404
                return $this->forward404();
            }
            $this->setDefaultNamespace($route['namespace']);
            $this->setControllerName($route['handler']);
            $this->setActionName($route['action']);
        } catch (\Exception $e) {
            $this->_handleException($e);
            if (APPLICATION_STAGE == APPLICATION_STAGE_DEVELOPMENT) {
                throw $e;
            } else {
                $id = Exception::logError(
                    'Exception',
                    $e->getMessage(),
                    $e->getFile(),
                    $e->getLine(),
                    $e->getTraceAsString()
                );
                $this->getDI()->setShared(
                    'currentErrorCode',
                    function () use ($id) {
                        
                        return $id;
                    }
                );
            }
        }

        return parent::dispatch();
    }

}