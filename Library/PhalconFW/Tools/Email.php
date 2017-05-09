<?php
namespace PhalconFW\Tools;

/**
 *
 * @author jeromeklam
 *
 */
class Email
{

    /**
     * Verify email
     *
     * @param string $p_address
     *
     * @return boolean
     */
    public static function verify ($p_address)
    {
        if (self::verifyFormatting($p_address)) {
            
            return self::verifyDomain($p_address);
        }
        
        return false;
    }

    /**
     * Verify DNS
     *
     * @param string $p_address
     *
     * @return boolean
     */
    public static function verifyDomain ($p_address)
    {
        $record              = 'MX';
        list($user, $domain) = explode('@', $p_address);
        
        return checkdnsrr($domain, $record);
    }

    /**
     * Verify email global format
     *
     * @param string $p_address
     *
     * @return boolean
     */
    public static function verifyFormatting ($p_address)
    {
        if (strstr($p_address, "@") == false) {
            
            return false;
        } else {
            list($user, $domain) = explode('@', $p_address);
            if (strstr($domain, '.') == false){
                
                return false;
            } else {
                
                return true;
            }
        }
    }

}