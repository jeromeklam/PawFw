<?php
namespace PhalconFW\Application;

use Phalcon\Config as PhalconConfig;

/**
 * 
 * @author jeromeklam
 *
 */
class Config extends PhalconConfig
{
    const
        /**
         * System config location.
         */
        CONFIG_PATH = '/apps/config/',

        /**
         * System config location.
         */
        CONFIG_CACHE_PATH = '/apps/var/cache/',

        /**
         * Default language if there is no default selected.
         */
        CONFIG_DEFAULT_LANGUAGE = 'en',

        /**
         * Default locale if there no default language selected.
         */
        CONFIG_DEFAULT_LOCALE = 'en_US',

        /**
         * Application metadata.
         */
        CONFIG_METADATA_APP = '/apps/var/data/app.php',

        /**
         * Packages metadata location.
         */
        CONFIG_METADATA_PACKAGES = '/apps/var/data/packages',

        /**
         * Default configuration section.
         */
        CONFIG_DEFAULT_SECTION = 'application';

    /**
     * Current config stage.
     *
     * @var string
     */
    private $_currentStage;

    /**
     * Create configuration object.
     *
     * @param array|null  $arrayConfig Configuration data.
     * @param string|null $stage       Configuration stage.
     */
    public function __construct($arrayConfig = null, $stage = null)
    {
        $this->_currentStage = $stage;
        if ($arrayConfig !== null) {
            parent::__construct($arrayConfig);
        } else {
            parent::__construct();
        }
    }

    /**
     * Load configuration according to selected stage.
     *
     * @param string $stage Configuration stage.
     *
     * @return Config
     */
    public static function factory($stage = null)
    {
        if (!$stage) {
            $stage = APPLICATION_STAGE;
        }
        if ($stage == APPLICATION_STAGE_DEVELOPMENT) {
            $config = self::_getConfiguration($stage);
        } else {
            if (file_exists(self::CONFIG_CACHE_PATH)) {
                $config = new Config(include_once(self::CONFIG_CACHE_PATH), $stage);
            } else {
                $config = self::_getConfiguration($stage);
                $config->refreshCache();
            }
        }
        
        return $config;
    }

    /**
     * Save config file into cached config file.
     *
     * @return void
     */
    public function refreshCache()
    {
        file_put_contents(APP_PATH . self::CONFIG_CACHE_PATH, $this->_toConfigurationString());
    }

    /**
     * Save config.
     *
     * @param string|array $sections Config section name to save. By default: Config::CONFIG_DEFAULT_SECTION.
     *
     * @return void
     */
    public function save($sections = self::CONFIG_DEFAULT_SECTION)
    {
        if (!$this->_currentStage) {
            
            return;
        }
        $configDirectory = APP_PATH . self::CONFIG_PATH . $this->_currentStage;
        if (!is_array($sections)) {
            $sections = array($sections);
        }
        foreach ($sections as $section) {
            file_put_contents(
                $configDirectory . '/' . $section . '.php',
                $this->_toConfigurationString($this->get($section)->toArray())
            );
        }
        $this->refreshCache();
    }

    /**
     * Load configuration from all files.
     *
     * @param string $stage Configuration stage.
     *
     * @throws Exception
     * @return Config
     */
    protected static function _getConfiguration($stage)
    {
        $config = new Config(null, $stage);
        $configDirectory = APP_PATH . self::CONFIG_PATH . $stage;
        $configFiles = glob($configDirectory .'/*.php');
        // create config files from .dist
        if (!$configFiles) {
            foreach (glob($configDirectory .'/*.dist') as $file) {
                $configFile = substr($file, 0, -5);
                copy($file, $configFile);
                $configFiles[] = $configFile;
            }
        }
        foreach ($configFiles as $file) {
            $data = include_once($file);
            $config->offsetSet(basename($file, ".php"), $data);
        }
        $appPath = APP_PATH . self::CONFIG_METADATA_APP;
        if (!file_exists($appPath)) {
            $config->offsetSet('installed', false);
            $config->offsetSet('events', array());
            $config->offsetSet('modules', array());
            return $config;
        }
        $data = include_once($appPath);
        $config->merge(new Config($data));
        
        return $config;
    }

    /**
     * Save application config to file.
     *
     * @param array|null $data Configuration data.
     *
     * @return void
     */
    protected function _toConfigurationString($data = null)
    {
        if (!$data) {
            $data = $this->toArray();
        }
        $configText = var_export($data, true);
        // Fix pathes. This related to windows directory separator.
        $configText = str_replace('\\\\', DS, $configText);
        $configText = str_replace("'" . PUBLIC_PATH, "PUBLIC_PATH . '", $configText);
        $configText = str_replace("'" . APP_PATH, "APP_PATH . '", $configText);
        
        return $headerText . $configText . ';';
    }

}