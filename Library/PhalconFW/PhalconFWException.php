<?php
namespace PhalconFW;

class PhalconFWException extends \Exception
{

    /**
     * Translations
     * @var array
     */
    protected static $translations = null;

    /**
     * 
     */
    public function translateCode ()
    {
        if (self::$translations === null) {
            $arr   = explode('\\', get_called_class());
            $class = array_pop($arr);
            $cacheFile = APPLICATION_CACHE_PATH . '/' . $class . '.php';
            if (!is_file($cacheFile)) {
                $errors = str_replace($class, 'ErrorCodes', get_called_class());
                if (class_exists($errors)) {
                    self::$translations = array();
                    // get constants
                    $oClass    = new \ReflectionClass($errors);
                    $constants = $oClass->getConstants();
                    foreach ($constants as $key => $value) {
                        self::$translations[$value] = str_replace('_', '.', strtolower($key));
                    }
                    file_put_contents($cacheFile, '<?php' . PHP_EOL . 'return ' . var_export(self::$translations, true) . ';');
                }
            } else {
                self::$translations = include $cacheFile;
            }
        }
        if (is_array(self::$translations) && array_key_exists($this->getCode(), self::$translations)) {
            
            return self::$translations[$this->getCode()];
        }
        
        return $this->getCode();
    }

}