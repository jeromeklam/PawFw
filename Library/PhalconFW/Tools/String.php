<?php
namespace PhalconFW\Tools;

/**
 * 
 * @author jklam
 *
 */
class String
{

    /**
     *
     * Enter description here ...
     * @var unknown_type
     */
    const REGEX_PARAM_PLACEHOLDER = '#\[\[:(.*?):\]\]#sim';

    /**
     * Parse string
     *
     * @param string $p_string
     * @param array  $p_data
     *
     * @return string
     */
    public static function parse ($p_string, $p_data = array())
    {
        if (0 < preg_match_all(self::REGEX_PARAM_PLACEHOLDER, $p_string, $matches, PREG_SET_ORDER)) {
            foreach ($matches as $match) {
                $replace = '';
                if (array_key_exists($match[1], $p_data)) {
                    $replace = $p_data[$match[1]];
                }
                $p_string = str_replace(
                    $match[0],
                    $replace,
                    $p_string
                );
            }
            
            return self::parse($p_string, $p_data);
        }
        
        return $p_string;
    }

    /**
     * Convert string to camelCase
     *
     * @param string  $p_str
     * @param boolean $p_first
     * @param string  $p_glue
     *
     * @return string
     */
    public static function toCamelCase ($p_str, $p_first = false, $p_glue = '_')
    {
        if ($p_first) {
            $p_str[0] = strtoupper($p_str[0]);
        }
        
        return preg_replace("/{$p_glue}([a-z])/e", "strtoupper('\\1')", $p_str);
    }

}