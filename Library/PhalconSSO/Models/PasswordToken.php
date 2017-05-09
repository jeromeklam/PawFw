<?php
namespace PhalconSSO\Models;

use \PhalconFW\Mvc\Model;
use \PhalconFW\Tools\Date as MyDate;

class PasswordToken extends BaseModel
{

    /**
     * Password Token Id
     * @var bigint(20)
     */
    protected $id = null;

    /**
     * Token
     * @var varchar(80)
     */
    protected $token = null;

    /**
     * Token used
     * @var tinyint(1)
     */
    protected $used = null;

    /**
     * User email
     * @var varchar(255)
     */
    protected $email = null;

    /**
     * User Id
     * @var varchar(255)
     */
    protected $user_id = null;

    /**
     * Request Ip
     * @var varchar(20)
     */
    protected $request_ip = null;

    /**
     * Resolve Ip
     * @var varchar(20)
     */
    protected $resolve_ip = null;

    /**
     * End of use
     * @var timestamp
     */
    protected $end = null;

    /**
     *
     */
    public function initialize ()
    {
        parent::initialize();
        $this->setSource('auth_passwords_tokens');
    }

    /**
     *
     */
    public function columnMap ()
    {
        return array(
            'ptok_id'         => 'id',
            'ptok_token'      => 'token',
            'ptok_used'       => 'used',
            'ptok_email'      => 'email',
            'user_id'         => 'user_id',
            'ptok_request_ip' => 'request_ip',
            'ptok_resolve_ip' => 'resolve_ip',
            'ptok_end'        => 'end'
        );
    }

    /**
     * Setter for id
     *
     * @param bigint $p_id
     *
     * @return PasswordToken
     */
    public function setId ($p_id)
    {
        $this->id = $p_id;

        return $this;
    }

    /**
     * Getter for id
     *
     * @return bigint
     */
    public function getId ()
    {
        return $this->id;
    }

    /**
     * Setter for token
     *
     * @param varchar $p_token
     *
     * @return PasswordToken
     */
    public function setToken ($p_token)
    {
        $this->token = $p_token;

        return $this;
    }

    /**
     * Getter for token
     *
     * @return varchar
     */
    public function getToken ()
    {
        return $this->token;
    }

    /**
     * Setter for used
     *
     * @param tinyint $p_used
     *
     * @return PasswordToken
     */
    public function setUsed ($p_used)
    {
        $this->used = $p_used;

        return $this;
    }

    /**
     * Getter for used
     *
     * @return tinyint
     */
    public function getUsed ()
    {
        return $this->used;
    }

    /**
     * Setter for email
     *
     * @param varchar $p_email
     *
     * @return PasswordToken
     */
    public function setEmail ($p_email)
    {
        $this->email = $p_email;

        return $this;
    }

    /**
     * Getter for email
     *
     * @return varchar
     */
    public function getEmail ()
    {
        return $this->email;
    }

    /**
     * Setter for user_id
     *
     * @param varchar $p_user_id
     *
     * @return PasswordToken
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
     * Setter for request_ip
     *
     * @param varchar $p_request_ip
     *
     * @return PasswordToken
     */
    public function setRequestIp ($p_request_ip)
    {
        $this->request_ip = $p_request_ip;

        return $this;
    }

    /**
     * Getter for request_ip
     *
     * @return varchar
     */
    public function getRequestIp ()
    {
        return $this->request_ip;
    }

    /**
     * Setter for resolve_ip
     *
     * @param varchar $p_resolve_ip
     *
     * @return PasswordToken
     */
    public function setResolveIp ($p_resolve_ip)
    {
        $this->resolve_ip = $p_resolve_ip;

        return $this;
    }

    /**
     * Getter for resolve_ip
     *
     * @return varchar
     */
    public function getResolveIp ()
    {
        return $this->resolve_ip;
    }

    /**
     * Setter for end
     *
     * @param timestamp $p_end
     *
     * @return PasswordToken
     */
    public function setEnd ($p_end)
    {
        if ($p_end !== null && $p_end != '' && strpos($p_end, '/') !== false ) {
            $this->end = MyDate::ddmmyyyyToMysql($p_end);
        } else {
            $this->end = $p_end;
        }

        return $this;
    }

    /**
     * Getter for end
     *
     * @return timestamp
     */
    public function getEnd ()
    {
        return $this->end;
    }

}