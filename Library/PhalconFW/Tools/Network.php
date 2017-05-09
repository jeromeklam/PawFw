<?php
namespace PhalconFW\Tools;

/**
 * 
 * @author jeromeklam
 *
 */
class Network
{

    /**
     * Check Ip in a list of cidrs
     * 
     * @param string $p_ip
     * @param array  $p_cidrs array(name => cidr, ...)
     * 
     * @return boolean
     */
    public static function ipCidrsCheck ($p_ip, $p_cidrs)
    {
        foreach ($p_cidrs as $name => $range) {
            if (self::ipCidrCheck($p_ip, $range)) {
                
                return true;
            }
        }
        
        return false;
    }

    /**
     * Check ip in a cidr
     * 
     * @param string $p_ip
     * @param string $p_cidr
     * 
     * @return boolean
     */
    public static function ipCidrCheck ($p_ip, $p_cidr)
    {
        if (strpos($p_cidr, '/') !== false) {
            list ($net, $mask) = split("/", $p_cidr);
            $ip_net            = ip2long($net);
            $ip_mask           = ~((1 << (32 - $mask)) - 1);
            $ip_ip             = ip2long($p_ip);
            $ip_ip_net         = $ip_ip & $ip_mask;
            
            return ($ip_ip_net == $ip_net);
        } else {
            
            return (ip2long($p_ip) == ip2long($p_cidr));
        }
    }

}