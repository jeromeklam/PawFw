<?php
namespace PhalconAPI\Application;

use \PhalconAPI\Interfaces\IRun as IRun;
use \PhalconFW\Application\Config;

/**
 *
 * @author jeromeklam
 *
 */
class Micro extends \Phalcon\Mvc\Micro implements IRun
{

    /**
     * Pages that doesn't require authentication
     * @var array
     */
    protected $_noAuthPages;

    /**
     * Config
     * @var Config
     */
    protected $_config = null;

    /**
     *
     * @var unknown
     */
    protected $_prefix = '';

    /**
     * Private key (from broker)
     * @var string
     */
    protected $_privateKey = null;

    /**
     * Max delay
     * @var number
     */
    protected $_maxRequestDelay = 60;

    /**
     * Constructor of the App
     */
    public function __construct ()
    {
        $di = new \Phalcon\DI\FactoryDefault();
        parent::__construct($di);
        $this->_noAuthPages = array();
        /**
         * Get config.
         */
        $this->_config = Config::factory();
        if (!$this->_config->installed) {
            if (!defined('CHECK_REQUIREMENTS')) {
                define('CHECK_REQUIREMENTS', true);
            }
            require_once(APP_PATH . 'public/requirements.php');
        }
        $di->setShared('config', $this->_config);
        /**
         * Events Manager
         */
        $di->setShared('eventsManager', function () {
            return new \Phalcon\Events\Manager();
        });
        /**
         * Cookies
         */
        $di->set('cookies', function () {
            $cookies = new \Phalcon\Http\Response\Cookies();
            $cookies->useEncryption(false);
            return $cookies;
        });
        /**
         * Routes
         */
        $di['routes'] = function() {
            return new \PhalconFW\Router\Routes();
        };
        /**
         * Mailer
         */
        $di['mailer'] = function() use ($di) {
            $config = $di->get('config');
            if (isset($config->mailer->server)) {
                $server = (array)$config->mailer->server;
                
                return new \Phalcon\Mailer\Manager($server);
            }
            
            return false;
        };
    }

    /**
     * Set routes prefix
     *
     * @param string $p_prefix
     *
     * @return \PhalconAPI\Application\Micro
     */
    public function setPrefix ($p_prefix)
    {
        $this->_prefix = $p_prefix;
        
        return $this;
    }

    /**
     * Get Routes prefix
     *
     * @return string
     */
    public function getPrefix ()
    {
        return $this->_prefix;
    }

    /**
     * Private key
     *
     * @return string
     */
    public function getPrivateKey ()
    {
        return $this->_privateKey;
    }

    /**
     * Return max request delay
     *
     * @return integer
     */
    public function getMaxRequestDelay ()
    {
        return $this->_maxRequestDelay;
    }

    /**
     * Add all modules
     *
     * @param array $p_modules
     *
     * @return \PhalconFW\Application
     */
    public function addModules ($p_modules)
    {
        $di     = $this->getDI();
        $routes = $di->get('routes');
        foreach ($p_modules as $name => $properties) {
            $path     = $properties['path'];
            $basePath = dirname($path);
            /**
             * Routes
             */
            $routes->addRoutes($basePath . '/config/routes.php', $name);
        }
        /**
         * Next
         */
        $this->setEventsManager($di->getShared('eventsManager'));
        $di->getShared('eventsManager')->attach(
                "micro",
                function ($event, $micro) use($p_modules, $routes) {
                    /**
                     * Register modules
                     * Add modules rest routes
                     */
                    if ($event->getType() == 'beforeHandleRoute') {
                        $currentModule = $micro;
                        foreach ($p_modules as $key => $config) {
                            if ($key != $currentModule) {
                                $cls = $config['path'];
                                require_once($config['path']);
                                $myModule = new $config['className']();
                                $myModule->registerAutoloaders();
                                $myModule->registerServices($micro->getDI(), false);
                            }
                        }
                        /**
                         * Now, get all routes that are compliant with rest calls and register them
                         */
                        $prefix = $micro->getPrefix();
                        foreach ($routes->getRestRoutes() as $path => $infos) {
                            $ctrl   = $infos['namespace'] . '\\' . $infos['controller'] . 'RestController';
                            $action = $infos['action'] . 'Action';
                            if (class_exists($ctrl)) {
                                switch ($infos['rest']) {
                                    case 'get':
                                        $micro->get(
                                            rtrim($prefix, '/') . '/' . ltrim($path, '/'),
                                            array(new $ctrl(), $action)
                                        );
                                        break;
                                    case 'post':
                                        $micro->post(
                                            rtrim($prefix, '/') . '/' . ltrim($path, '/'),
                                            array(new $ctrl(), $action)
                                        );
                                        break;
                                    case 'put':
                                        $micro->put(
                                            rtrim($prefix, '/') . '/' . ltrim($path, '/'),
                                            array(new $ctrl(), $action)
                                        );
                                        break;
                                    case 'delete':
                                        $micro->delete(
                                            rtrim($prefix, '/') . '/' . ltrim($path, '/'),
                                            array(new $ctrl(), $action)
                                        );
                                        break;
                                }
                            }
                        }
                    }
                    /**
                     * Verify HMAC if needed
                     */
                    if ($event->getType() == 'beforeExecuteRoute') {
                        $allowed = false;
                        $di      = $micro->getDI();
                        $logger  = $di->get('logger');
                        // Public / Private call ??
                        $matchedRoute = $micro->router->getMatchedRoute();
                        //
                        $method = strtolower($matchedRoute->getHttpMethods());
                        $logger->info('Method              : ' . $method);
                        $unAuthenticated = $micro->getUnauthenticated();
                        if (isset($unAuthenticated[$method])) {
                            $unAuthenticated = array_flip($unAuthenticated[$method]);
                            if (isset($unAuthenticated[$matchedRoute->getPattern()])) {
                                $allowed = true;
                            }
                        }
                        if ($micro->router->getMatchedRoute() !== false) {
                            $logger->info('Route               : ' . $matchedRoute->getPattern());
                        }
                        if (!$allowed) {
                            $iRequestTime = $this->_msg->getTime();
                            $msgData      = is_array($this->_msg->getData()) ?
                                            $this->_msg->getDataAsUrl() :
                                            $this->_msg->getData();
                            $data         = $iRequestTime . $this->_msg->getId() . $msgData;
                            $serverHash   = hash_hmac('sha256', $data, $micro->getPrivateKey());
                            $clientHash   = $this->_msg->getHash();
                            $logger->debug('RequestTime         : ' . $iRequestTime);
                            $logger->debug('CurrentTime         : ' . time());
                            $logger->debug('Diff                : ' . (time()-$iRequestTime));
                            $logger->debug('ServerHash          : ' . $serverHash);
                            $logger->debug('ClientHash          : ' . $clientHash);
                            $logger->debug('HashString          : ' . $data);
                            $logger->debug('Key                 : ' . $micro->getPrivateKey());
                            // security checks, deny access by default
                            $allowed = false;
                            if ($clientHash === $serverHash) { // 1st security level - check hashes
                                if ((time() - $iRequestTime) <= $micro->getMaxRequestDelay()) {
                                    $allowed = true; // gain access, everyting ok
                                }
                            }
                        }
                        $logger->debug('Allowed             : ' . ($allowed === true ? 'TRUE' : 'FALSE'));
                        if (!$allowed) { // already authorized skip this part
                            // still not authorized, get out of here
                            $logger->info('Status              : 401');
                            $micro->response->setStatusCode(401, "Unauthorized");
                            $micro->response->send();
                            
                            return false;
                        }
                        
                        return true;
                    }
                });
        
        return $this;
    }

    /**
     * Set events to be triggered before/after certain stages in Micro App
     *
     * @param object $p_events
     */
    public function setEvents (\Phalcon\Events\Manager $p_events)
    {
        $this->setEventsManager($p_events);
    }

    /**
     * Get not authenticated routes
     *
     * @return array
     */
    public function getUnauthenticated ()
    {
        return $this->_noAuthPages;
    }

    /**
     * Main run block that executes the micro application
     */
    public function run ()
    {
        // Handle any routes not found
        $this->notFound(function () {
            $response = new \Phalcon\Http\Response();
            $response->setStatusCode(404, 'Not Found')->sendHeaders();
            $response->setContent('Page doesn\'t exist.');
            $response->send();
        });
        $logger = $this->getDI()->get('logger');
        // CORS calls first send an OPTIONS method. Allways return ok
        if ($this->request->getMethod() == 'OPTIONS') {
            $this->response->setStatusCode(200, "OK");
            $this->response->setContent('ok');
            $this->response->send();
            exit(0);
        } else {
            // Get Authentication Headers
            $clientId = $this->request->getHeader('API_ID');
            $time     = $this->request->getHeader('API_TIME');
            $hash     = $this->request->getHeader('API_HASH');
            $lang     = $this->request->getHeader('API_LANG');
            // Try get get from one param...
            if ($clientId == '') {
                $clientId = $this->request->get('API_ID');
                $time     = $this->request->get('API_TIME');
                $hash     = $this->request->get('API_HASH');
                $lang     = $this->request->get('API_LANG');
            }
            if ($clientId == '') {
                $arr = getallheaders();
                if (array_key_exists('API_ID', $arr)) {
                    $clientId = $arr['API_ID'];
                }
                if (array_key_exists('API_TIME', $arr)) {
                    $time = $arr['API_TIME'];
                }
                if (array_key_exists('API_HASH', $arr)) {
                    $hash = $arr['API_HASH'];
                }
                if (array_key_exists('API_LANG', $arr)) {
                    $lang = $arr['API_LANG'];
                }
            }
            $logger->debug('API_ID              : ' . $clientId);
            $logger->debug('API_TIME            : ' . $time);
            $logger->debug('API_HASH            : ' . $hash);
            $logger->debug('API_LANG            : ' . $lang);
            // First verify broker, ...
            $di = $this->getDI();
            $authService = $di->get('authentication');
            if (!$authService) {
                // @todo OUPS
            }
            try {
                $authService->setBroker($clientId);
                $this->_privateKey = $authService->getPrivateKey();
            } catch (\Exception $ex) {
                $logger->info('Status              : 412');
                $this->response->setStatusCode(412, $ex->getMessage());
                $this->response->setContent($ex->getMessage());
                $this->response->send();
                
                return;
            }
            // Next
            $data = array();
            switch ($_SERVER['REQUEST_METHOD']) {
                case 'GET':
                    $data = $_GET;
                    unset($data['_url']); // clean for hashes comparison
                    break;
                case 'POST':
                    if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
                        ($_SERVER['HTTP_X_REQUESTED_WITH'] == "XMLHttpRequest" ||
                         $_SERVER['HTTP_X_REQUESTED_WITH'] == 'fr.jeromeklam.api')) {
                        $data = $_POST;
                        if (!is_array($data) || count($data) <= 0) {
                            $data = json_decode(file_get_contents('php://input'), true);
                        }
                    } else {
                        if (count($_POST) <= 0) {
                            $data = json_decode(file_get_contents('php://input'), true);
                        } else {
                            $data = $_POST;
                        }
                    }
                    break;
                default: // PUT AND DELETE
                    $data = json_decode(file_get_contents('php://input'), true);
                    break;
            }
            $this->_msg = new \PhalconAPI\Micro\Messages\Auth($clientId, $time, $hash, $lang, $data);
        }
        $this->handle();
    }

}