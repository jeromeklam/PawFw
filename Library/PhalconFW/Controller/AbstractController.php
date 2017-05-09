<?php
namespace PhalconFW\Controller;

use Phalcon\Mvc\Controller AS PhalconController;
use PhalconFW\Behaviour\TranslationBehaviour;

/**
 *
 * @author jeromeklam
 *
 */
abstract class AbstractController extends PhalconController
{

    use TranslationBehaviour;

    /**
     * 
     * @param unknown $p_type
     */
    public function redirectToRouteByType ($p_type) {
        $path = $this->routes->findPathByType($p_type);
        if ($path !== false) {
            $this->redirectAction($path);
        } else {
            $this->redirectAction('/');
        }
    }

    /**
     * 
     * @param string $p_id
     * @param array  $p_params
     */
    public function redirectToRouteById ($p_id, $p_params = array()) {
        $myRoute = $this->routes->getByKey($p_id);
        if (is_array($myRoute)) {
            $this->redirectAction($this->routes->getUrl($myRoute['path'], $p_params));
        } else {
            $this->redirectAction('/');
        }
    }

    /**
     * 
     * @param string $redirect
     * @param number $code
     */
    public function redirectAction ($redirect, $code = 301)
    {
        $this->view->disable();
        $this->response->redirect($redirect, true, $code);
    }

    /**
     * 
     * @param string $p_routeId
     * @param array  $p_params
     */
    public function getUrl ($p_routeId, $p_params= array())
    {
        return $this->routes->getUrlByKey($p_routeId, $p_params);
    }

}