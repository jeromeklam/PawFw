<?php
namespace PhalconFW\I18n;

use \PhalconFW\Tools\Icu;

class Translations
{

    protected static $translations = array();

    public function addTranslations ($p_translations = array())
    {
        if (is_array($p_translations)) {
            self::$translations = array_merge($p_translations, self::$translations);
        }
        return $this;
    }

    public function getTranslations ()
    {
        return self::$translations;
    }

    public function addTranslationFile ($p_moduleName, $p_language = 'fr', $p_basePath = null)
    {
        $trads     = array();
        $cacheFile = APPLICATION_CACHE_PATH . '/' . ucfirst($p_moduleName) . '.' . $p_language . '.php';
        if (!is_file($cacheFile)) {
            $path = $p_basePath . '/config/i18n/';
            if ($p_moduleName == 'app') {
                $path = APP_PATH . 'apps/config/i18n/';
            }
            $file = $path . $p_language . '.res';
            if (is_file($file)) {
                $trads = Icu::getAsArray($p_language, $path, $p_moduleName);
                file_put_contents($cacheFile, '<?php' . PHP_EOL . 'return ' . var_export($trads, true) . ';');
            }
        } else {
            $trads = include $cacheFile;
        }
        $this->addTranslations($trads);
        if (!is_file(APP_PATH . '/public/js/' . strtolower($p_language) . '/locales.js')) {
            file_put_contents(APP_PATH . '/public/js/locales/' . strtolower($p_language) . '.js', 'var translations=' . json_encode($trads, true));
        }
        
        return $this;
    }

}