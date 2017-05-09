<?php
namespace PhalconFW\Tools;

class Icu {
    
    protected static function _parseResourceBundle ($p_rb, $p_prefix = '') {
        $p_prefix = rtrim($p_prefix, '.');
        $values = array();
        if ($p_rb instanceof \ResourceBundle)
        foreach($p_rb as $k => $v) {
            if(is_object($v)) {
                $temp   = self::_parseResourceBundle($v, ($p_prefix == '' ? '' : $p_prefix . '.') . print_r($k, true) . '.');
                $values = array_merge($values, $temp);
            } else {
                $values[$p_prefix . '.' . $k] = $v;
            }
        }
        return $values;
    }

    public static function getAsArray ($p_name, $p_path, $p_prefix = '')
    {
        $ressource = new \ResourceBundle($p_name, $p_path);
        $arr       = self::_parseResourceBundle($ressource, $p_prefix);
        return $arr;
    }

}