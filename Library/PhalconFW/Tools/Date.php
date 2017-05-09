<?php
namespace PhalconFW\Tools;

class Date
{

    /**
     * Return a datetime
     *
     * @param number $lifeTime
     *
     * @return string
     */
    public static function getServerDatetime ($p_plus = null)
    {
        $datetime = new \Datetime();
        if ($p_plus !== null) {
            $datetime->add(new \DateInterval('PT'.$p_plus.'M'));
        }
        
        return $datetime->format('Y-m-d H:i:sP');
    }

    /**
     * 
     * @param unknown $p_date
     */
    public static function ddmmyyyyToMysql ($p_date)
    {
        if ($p_date !== null && $p_date != '') {
            $format = 'd/m/Y H:i:s';
            $date = \DateTime::createFromFormat($format, $p_date);
            if ($date === false) {
                $format = 'd/m/Y H:i';
                $date = \DateTime::createFromFormat($format, $p_date);
            }
            if ($date !== false) {
                
                return $date->format('Y-m-d H:i:s');
            }
        }
        
        return null;
    }

    /**
     * 
     * @param unknown $p_date
     * @return NULL
     */
    public static function mysqlToddmmyyyy ($p_date)
    {
        if ($p_date !== null && $p_date != '') {
            $format = 'Y-m-d H:i:s';
            $date = \DateTime::createFromFormat($format, $p_date);
            if ($date === false) {
                $format = 'Y-m-d H:i:s';
                $date = \DateTime::createFromFormat($format, $p_date);
            }
            if ($date !== false) {
        
                return $date->format('d/m/Y H:i');
            }
        }
        
        return null;
    }
}