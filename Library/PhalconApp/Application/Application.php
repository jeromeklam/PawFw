<?php
namespace PhalconApp\Application;

use \Phalcon\Db\Adapter\Pdo\Mysql as MysqlPdo;
use \Phalcon\Mvc\Model\Manager as ModelsManager;
use \PhalconFW\Application\Config;

/**
 *
 * @author jeromeklam
 *
 */
class Application extends \PhalconFW\Application\AbstractApplication
{

    /**
     * Modules
     * @var array
     */
    protected static $modules = array();

    /**
     * Constructor.
     */
    public function __construct()
    {
        /**
         * Create default DI.
         */
        $di = new \Phalcon\DI\FactoryDefault();
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
         * The URL component is used to generate all kind of urls in the application
         */
        $basePath = '/';
        if (isset($this->_config->application->baseUrl)) {
            $basePath = $this->_config->application->baseUrl;
        }
        $di->set('url', function() use ($basePath) {
            $url = new \Phalcon\Mvc\Url();
            $url->setBaseUri($basePath);
            
            return $url;
        });
        /**
         * Menus
         */
        $di->set('headerMenu', function () {
            
            return new \PhalconFW\Menu\HeaderMenu();
        }, true);
        $di->set('sideMenu', function () {
            
            return new \PhalconFW\Menu\SideMenu();
        }, true);
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
        /**
         * Router
         */
        $di['router'] = function() use ($di) {
            $router = new \PhalconFW\Router\Router(false);
            $router->setDI($di);
            $routes = $di->get('routes');
            $header = $di->get('headerMenu');
            $side   = $di->get('sideMenu');
            foreach ($routes->getAll() as $path=>$properties) {
                $router->add(
                    $path,
                    array(
                        'module'     => $properties['module'],
                        'controller' => $properties['handler'],
                        'action'     => $properties['action'],
                        'params'     => (isset($properties['params']) ? $properties['params'] : null)
                    )
                );
                if ($properties['type'] == '404') {
                    $router->notFound(array(
                        'module'     => $properties['module'],
                        'controller' => $properties['handler'],
                        'action'     => $properties['action']
                    ));
                }
                if ($properties['header'] != false) {
                    $header->addOption(
                        $properties['header'],
                        $properties['menu'],
                        $properties['default'],
                        strtolower($properties['module'] . '.' . $properties['handler'] . '.menu.' . $properties['action'] . '.title'),
                        $properties['security'],
                        $properties['glyph']
                    );
                }
                if ($properties['side'] != false) {
                    $side->addOption(
                        $properties['side'],
                        $properties['menu'],
                        $properties['default'],
                        strtolower($properties['module'] . '.' . $properties['handler'] . '.menu.' . $properties['action'] . '.title'),
                        $properties['security'],
                        $properties['glyph']
                    );
                }
            }
            
            return $router;
        };
        /**
         * Cookies
         */
        $di->set('cookies', function () {
            $cookies = new \Phalcon\Http\Response\Cookies();
            $cookies->useEncryption(false);
            
            return $cookies;
        });
        /**
         * Start the session the first time some component request the session service
         */
        $di->set('session', function() use ($di) {
            $config = $di->get('config');
            if(isset($config->application->session) && isset($config->application->session->adapter)) {
                switch ($config->application->session->adapter) {
                    case 'pdo':
                        $dbclass    = '\Phalcon\Db\Adapter\Pdo\\' . $config->application->session->config->adapter;
                        $connection = new $dbclass(array(
                            "host"     => $config->application->session->config->host,
                            "username" => $config->application->session->config->username,
                            "password" => $config->application->session->config->password,
                            "dbname"   => $config->application->session->config->dbname,
                            "options" => array(
                                \PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'
                            )
                        ));
                        $session    = new \Phalcon\Session\Adapter\Database(array(
                            'db'    => $connection,
                            'table' => $config->application->session->config->table
                        ));
                        break;
                    default:
                        $session = new \Phalcon\Session\Adapter\Files();
                        break;
                }
            } else {
                $session = new \Phalcon\Session\Adapter\Files();
            }
            if (!session_id()) {
                $session->start();
            }
            $session->set('dummy', 'dummy');
            
            return $session;
        });
        /**
         * Languages
         */
        $di->set('languages', function () use ($di) {
            $config = $di->get('config');
            $langs  = new \PhalconFW\I18n\Languages($this->request->getBestLanguage());
            if (isset($config->application->languages) && isset($config->application->languages->available)) {
                foreach ($config->application->languages->available as $key => $trad) {
                    $langs->addLanguage($key, $trad);
                }
            }
            
            return $langs;
        });
        /**
         * Translations
         */
        $di->set('translations', function() use ($di) {
            $langs = $di->get('languages');
            $translations = new \PhalconFW\I18n\Translations();
            $translations->addTranslationFile('app', $langs->getCurrent());
            
            return $translations;
        });
        /**
         * I18n
         */
        $di->set('i18n', function() use($di) {
            $trads = $di->get('translations');
            $translator =  new \Phalcon\Translate\Adapter\NativeArray(array(
                'content' => $trads->getTranslations()
            ));
            
            return $translator;
        });
        /**
         * Assets
         */
        $di->set('assets', function () {
            
            return new \PhalconFW\Assets\Manager();
        }, true);
        /**
         * Flash messages
         */
        $di->set('flash', function () {
            $flashSession = new \Phalcon\Flash\Session(array(
                'error'   => 'alert alert-danger flash',
                'success' => 'alert alert-success flash',
                'notice'  => 'alert alert-info flash',
                'warning' => 'alert alert-warning flash'
            ));
            $flashSession->setImplicitFlush(true);
            
            return $flashSession;
        });
        /**
         * DB
         */
        if (isset($this->_config->database)) {
            // This service returns a MySQL database
            $di->set('db', function () use ($di) {
                $config = $di->get('config');
                
                return new MysqlPdo(array(
                    "host"     => $config->database->host,
                    "username" => $config->database->username,
                    "password" => $config->database->password,
                    "dbname"   => $config->database->dbname,
                    "options" => array(
                        \PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'
                    )
                ));
            });
            
        }
        /**
         * ModelsManager
         */
        $di->set('modelsManager', function() {
            
            return new ModelsManager();
        });
        /**
         * Parent call
         */
        parent::__construct($di);
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
        $di = $this->getDI();
        foreach ($p_modules as $name => $properties) {
            $path     = $properties['path'];
            $basePath = dirname($path);
            /**
             * Routes
             */
            $routes = $di->get('routes');
            $routes->addRoutes($basePath . '/config/routes.php', $name);
            /**
             * Translations
             */
            $langs = $di->get('languages');
            $translations = $di->get('translations');
            $translations->addTranslationFile($name, $langs->getCurrent(), $basePath);
        }
        self::$modules = $p_modules;
        $this->registerModules($p_modules);
        $this->setEventsManager($di->getShared('eventsManager'));
        $di->getShared('eventsManager')->attach(
                "application",
                function ($event, $application) use($p_modules) {
                    if ($event->getType() == 'afterStartModule') {
                        //$h=fopen('/tmp/jk', 'a+'); fwrite($h, '1'); fclose($h);
                        $currentModule = $application;
                        foreach ($p_modules as $key => $config) {
                            if ($key != $currentModule) {
                                $cls = $config['path'];
                                require_once($config['path']);
                                $myModule = new $config['className']();
                                $myModule->registerAutoloaders();
                                $myModule->registerServices($application->getDI(), false);
                            }
                        }
                        // Need to load others....
                    };
                });
        
        return $this;
    }

    /**
     *
     * @param unknown $p_plugin
     * @return \PhalconFW\Application
     */
    public function setSecurityPlugin ($p_plugin) {
        $di = $this->getDI();
        $di->set('security', $p_plugin);
        
        return $this;
    }
}