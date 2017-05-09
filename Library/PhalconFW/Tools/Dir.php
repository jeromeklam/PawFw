<?php
namespace PhalconFW\Tools;

class Dir
{

    /**
     * 
     * @param unknown $path
     * @return boolean
     */
    public static function mkpath ($path)
    {
        if (@mkdir($path) || file_exists($path)) {
            return true;
        }
        
        return (self::mkpath(dirname($path)) && mkdir($path));
    }

    /**
     * Remove a directory
     * 
     * @param string $dir
     * 
     * @todo return
     */
    public static function remove ($target)
    {
        if (is_dir($target)){
            $files = glob($target . '*', GLOB_MARK);
            foreach ($files as $file) {
                self::remove($file);
            }
            @rmdir($target);
        } else {
            if (is_file($target)) {
                @unlink($target);
            }
        }
    }

}