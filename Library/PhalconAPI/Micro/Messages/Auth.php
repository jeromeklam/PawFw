<?php
namespace PhalconAPI\Micro\Messages;

/**
 *
 * @author jeromeklam
 *
 */
class Auth
{

    /**
     * Id of the Client
     * @var int
     */
    protected $_id;

    /**
     * Unix timestamp
     * @var string
     */
    protected $_time;

    /**
     * Data/Content of the Message
     * @var string
     */
    protected $_data;

    /**
     * Hash of the Message
     * @var string
     */
    protected $_hash;

    /**
     * Lang of the message
     * @var string
     */
    protected $_lang;

    /**
     * Constructor
     *
     * @param string  $p_id
     * @param integer $p_time
     * @param string  $p_hash
     * @param string  $p_lang
     * @param array   $p_data
     */
    public function __construct ($p_id, $p_time, $p_hash, $p_lang, $p_data)
    {
        $this->_id   = $p_id;
        $this->_hash = $p_hash;
        $this->_time = $p_time;
        $this->_lang = $p_lang;
        $this->_data = $p_data;
        if (is_array($this->_data)) {
            foreach (array('API_ID', 'API_TIME', 'API_HASH', 'API_LANG', '_') as $key) {
                if (array_key_exists($key, $this->_data)) {
                    unset($this->_data[$key]);
                }
            }
        }
    }

    /**
     * Get the hash of the Message
     *
     * @return string
     */
    public function getHash ()
    {
        return $this->_hash;
    }

    /**
     * Get the id of the broker
     *
     * @return string
     */
    public function getId ()
    {
        return $this->_id;
    }

    /**
     * Get the datas
     *
     * @parans boolean $p_transformEmpty
     *
     * @return array
     */
    public function getData ($p_transformEmpty = false)
    {
        if ($p_transformEmpty && is_array($this->_data)) {
            $ret = array_merge(array(), $this->_data);
            foreach ($ret as $key => $value) {
                if ($value === null) {
                    $ret[$key] = '';
                }
            }
            
            return $ret;
        }
        
        return $this->_data;
    }

    /**
     * Datas as url
     *
     * @param mixed  $a
     * @param number $b
     * @param number $c
     *
     * @return string
     */
    public function getDataAsUrl ($p_data = null, $p_key = 0, $p_cnt = 0)
    {
        if ($p_data === null) {
            $p_data = $this->_data;
        }
        if (!is_array($p_data)) {
            
            return false;
        }
        $r = array();
        foreach ((array)$p_data as $k=>$v) {
            if ($v === true) {
                $v = 1;
            }
            if ($v === false) {
                $v = 0;
            }
            if ($p_cnt) {
                $k = $p_key . '[]';
            } else {
                if (is_int($k)) {
                    $k = $p_key . $k;
                }
            }
            if (is_array($v) || is_object($v)) {
                $r[] = $this->getDataAsUrl($v, $k, 1);
                continue;
            }
            $r[] = urlencode($k) . '=' . urlencode($v);
        }
        
        return implode('&', $r);
    }

    /**
     * Get the time of the message
     *
     * @return integer
     */
    public function getTime ()
    {
        return $this->_time;
    }

    /**
     * Get the lang of the message
     *
     * @return string
     */
    public function getLang ()
    {
        return $this->_lang;
    }

}