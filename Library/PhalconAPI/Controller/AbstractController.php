<?php
namespace PhalconAPI\Controller;

use Phalcon\Mvc\Controller AS PhalconController;
use Phalcon\Mvc\View\Simple as SimpleView;
use Phalcon\Mvc\View;
use Phalcon\Http\Response;

/**
 *
 * @author jklam
 *
 */
abstract class AbstractController extends PhalconController
{

    /**
     * Params
     * @var array
     */
    protected static $params = null;

    /**
     * Send a 301 response
     *
     * @param string $p_redirect
     *
     * @return \Phalcon\Http\Response
     */
    protected function send301 ($p_redirect = null)
    {
        return $this->sendCode($p_redirect, 301, 'Moved Permanently');
    }

    /**
     * Send a 401 response
     *
     * @param string $p_redirect
     * @param mixed  $p_message
     *
     * @return \Phalcon\Http\Response
     */
    protected function send401 ($p_redirect = null, $p_message = null)
    {
        $json = null;
        if (is_array($p_message)) {
            $json      = $p_message;
            $p_message = null;
        }
        if ($p_message == '') {
            $p_message = 'Unauthorized';
        }
        
        return $this->sendCode($p_redirect, 401, $p_message, $json);
    }

    /**
     * Send a 404 response
     *
     * @param string $p_redirect
     * @param mixed  $p_message
     *
     * @return \Phalcon\Http\Response
     */
    protected function send404 ($p_redirect = null, $p_message = null)
    {
        $json = null;
        if (is_array($p_message)) {
            $json      = $p_message;
            $p_message = null;
        }
        if ($p_message == '') {
            $p_message = 'Not Found';
        }
        
        return $this->sendCode($p_redirect, 404, $p_message, $json);
    }

    /**
     * Send a 412 response
     *
     * @param string $p_redirect
     * @param mixed  $p_message
     *
     * @return \Phalcon\Http\Response
     */
    protected function send412 ($p_redirect = null, $p_message = null)
    {
        $json = null;
        if (is_array($p_message)) {
            $json      = $p_message;
            $p_message = null;
        }
        if ($p_message == '') {
            $p_message = 'Precondition Failed';
        }
        
        return $this->sendCode($p_redirect, 412, $p_message, $json);
    }

    /**
     * Send a 500 response
     *
     * @param string $p_redirect
     * @param mixed  $p_message
     *
     * @return \Phalcon\Http\Response
     */
    protected function send500 ($p_redirect = null, $p_message = null)
    {
        $json = null;
        if (is_array($p_message)) {
            $json      = $p_message;
            $p_message = null;
        }
        if ($p_message == '') {
            $p_message = 'Internal server error';
        }
        
        return $this->sendCode($p_redirect, 500, $p_message, $json);
    }

    /**
     * Send a code response
     *
     * @param string $p_redirect
     * @param string $p_code
     * @param string $p_message
     * @param mixed  $p_json
     *
     * @return \Phalcon\Http\Response
     */
    private function sendCode ($p_redirect, $p_code, $p_message = null, $p_json = null)
    {
        // Getting a response instance
        $response = $this->response;
        if ($p_redirect) {
            //Redirect specifyng the HTTP status code
            $response->redirect($p_redirect, true, $p_code);
        } else {
            $response->setStatusCode($p_code, $p_message);
            if ($p_json !== null) {
                $response->setJsonContent($p_json);
            }
        }
        $this->getDI()->get('logger')->info('Status              : ' . $p_code);
        
        return $response;
    }

    /**
     * Return a json response
     *
     * @param mixed $p_content
     *
     * @return \Phalcon\Http\Response
     */
    protected function sendJson ($p_content = null)
    {
        // Getting a response instance
        $response = $this->response;
        if ($p_content !== null) {
            // Send user infos
            $response->setJsonContent($p_content);
        } else {
            $response->setJsonContent(array());
        }
        $this->getDI()->get('logger')->info('Status              : 200');
        
        return $response;
    }

    /**
     * Get all params, specific for ajax calls
     *
     * @throws Exception
     *
     * @return array
     */
    protected function getParams ()
    {
        if (self::$params === null) {
            $request = $this->request;
            $contentType = $request->getHeader('CONTENT_TYPE');
            switch ($contentType) {
                case 'application/json':
                case 'application/json; charset=UTF-8':
                    $jsonRawBody = $request->getJsonRawBody(true);
                    if ($request->getRawBody() && !$jsonRawBody) {
                        throw new \Exception("Invalid JSON syntax");
                    }
                    self::$params = $jsonRawBody;
                    break;
                default:
                    self::$params = array();
                    break;
            }
        }
        
        return self::$params;
    }

    /**
     * Has one param ?
     *
     * @param string $p_key
     *
     * @return boolean
     */
    protected function hasParam ($p_key)
    {
        return $this->request->has($p_key) || array_key_exists($p_key, $this->getParams());
    }

    /**
     * Get one param
     *
     * @param string $p_key
     *
     * @return mixed
     */
    protected function getParam ($p_key)
    {
        if ($this->request->has($p_key)) {
            
            return $this->request->get($p_key);
        }
        if (array_key_exists($p_key, $this->getParams())) {
            
            return $this->getParams()[$p_key];
        }
        
        return null;
    }

}