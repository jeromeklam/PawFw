<?php
namespace PhalconSSO\Http;

use \PhalconSSO\Constants;
use \PhalconFW\Behaviour\DIBehaviour;

/**
 * 
 * @author jklam
 *
 */
class Remote
{

    use DIBehaviour {
        DIBehaviour::__construct as protected __DIConstruct;
    }

    /**
     * Random value for a cookie
     * @return string
     */
    protected static function getRandomCookieValue ()
    {
        $str = microtime() . '-' . uniqid(rand(1000,9999), true);
        return md5($str);
    }

    /**
     * Get Main CDSSO cookie value
     * 
     * @param string $p_domain
     * 
     * @return string
     */
    public static function getSSOCookie ($p_domain = null)
    {
        $di = self::getStaticDI();
        if ($p_domain === null) {
            // @todo : getdomain from url
            $p_domain = Constants::COOKIE_DEFAULT_DOMAIN;
        }
        $cookies = $di->get('cookies');
        if (!$cookies->has(Constants::COOKIE_CDSSO)) {
            $value = self::getRandomCookieValue();
            $cookies->set(Constants::COOKIE_CDSSO, $value, null, '/', false, $p_domain);
            
            return $value;
        } else {
            $crtCookie = $cookies->get(Constants::COOKIE_CDSSO);
            
            return $crtCookie->getValue();
        }
    }

    /**
     * Get Main Application cookie value
     * 
     * @param string $p_app_name
     * 
     * @return string
     */
    public static function getApplicationCookie ($p_app_name = 'GATE')
    {
        $di       = self::getStaticDI();
        $cookies  = $di->get('cookies');
        $cookName = strtoupper(Constants::COOKIE_APP . '_' . $p_app_name);
        if (!$cookies->has($cookName)) {
            $value = self::getRandomCookieValue();
            $cookies->set($cookName, $value, time() + (86400 * Constants::COOKIE_APP_DAYS), '/', false);
            
            return $value;
        } else {
            $crtCookie = $cookies->get($cookName);
            
            return $crtCookie->getValue();
        }
    }

    /**
     * Return the remote addr
     * 
     * @return string
     */
    public static function getAddr ()
    {
        //Just get the headers if we can or else use the SERVER global
        if ( function_exists('apache_request_headers') ) {
            $headers = apache_request_headers();
        } else {
            $headers = $_SERVER;
        }
        //Get the forwarded IP if it exists
        if ( array_key_exists( 'X-Forwarded-For', $headers ) && filter_var( $headers['X-Forwarded-For'], FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 ) ) {
            $the_ip = $headers['X-Forwarded-For'];
        } elseif ( array_key_exists( 'HTTP_X_FORWARDED_FOR', $headers ) && filter_var( $headers['HTTP_X_FORWARDED_FOR'], FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 )
        ) {
            $the_ip = $headers['HTTP_X_FORWARDED_FOR'];
        } else {
            if (array_key_exists('X-ClientSide', $headers)) {
                $parts  = explode(':', $headers['X-ClientSide']);
                $the_ip = $parts[0];
            } else {
                $the_ip = filter_var( $_SERVER['REMOTE_ADDR'], FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 );
            }
        }
        
        return $the_ip;
    }

}