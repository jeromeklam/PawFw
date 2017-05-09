<?php
namespace PhalconSSO\Models;

use \PhalconFW\Mvc\Model;
use \PhalconFW\Tools\Date as MyDate;

class AutologinCookie extends BaseModel
{

    /**
     * User Id
     * @var varchar(32)
     */
    protected $user_id = null;

    /**
     * Cookie
     * @var varchar(32)
     */
    protected $cookie = null;

    /**
     * Ip
     * @var varchar(32)
     */
    protected $ip = null;

    /**
     * Password
     * @var varchar(255)
     */
    protected $paswd = null;

    /**
     *
     */
    public function initialize ()
    {
        parent::initialize();
        $this->setSource('auth_autologin_cookies');
    }

    /**
     *
     */
    public function columnMap ()
    {
        return array(
            'user_id'     => 'user_id',
            'auto_cookie' => 'cookie',
            'auto_ip'     => 'ip',
            'auto_paswd'  => 'paswd'
        );
    }

    /**
     * Setter for user_id
     *
     * @param varchar $p_user_id
     *
     * @return AutologinCookie
     */
    public function setUserId ($p_user_id)
    {
        $this->user_id = $p_user_id;

        return $this;
    }

    /**
     * Getter for user_id
     *
     * @return varchar
     */
    public function getUserId ()
    {
        return $this->user_id;
    }

    /**
     * Setter for cookie
     *
     * @param varchar $p_cookie
     *
     * @return AutologinCookie
     */
    public function setCookie ($p_cookie)
    {
        $this->cookie = $p_cookie;

        return $this;
    }

    /**
     * Getter for cookie
     *
     * @return varchar
     */
    public function getCookie ()
    {
        return $this->cookie;
    }

    /**
     * Setter for ip
     *
     * @param varchar $p_ip
     *
     * @return AutologinCookie
     */
    public function setIp ($p_ip)
    {
        $this->ip = $p_ip;

        return $this;
    }

    /**
     * Getter for ip
     *
     * @return varchar
     */
    public function getIp ()
    {
        return $this->ip;
    }

    /**
     * Setter for paswd
     *
     * @param varchar $p_paswd
     *
     * @return AutologinCookie
     */
    public function setPaswd ($p_paswd)
    {
        $this->paswd = $p_paswd;

        return $this;
    }

    /**
     * Getter for paswd
     *
     * @return varchar
     */
    public function getPaswd ()
    {
        return $this->paswd;
    }

}