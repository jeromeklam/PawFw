<?php
namespace PhalconSSO\Models;

use \PhalconFW\Mvc\Model;
use \PhalconFW\Tools\Date as MyDate;

class Broker extends BaseModel
{

    /**
     * Id
     * @var int(11)
     */
    protected $id = null;

    /**
     * Key
     * @var varchar(50)
     */
    protected $key = null;

    /**
     * Name
     * @var varchar(50)
     */
    protected $name = null;

    /**
     * Certificate / secret
     * @var char(32)
     */
    protected $certificate = null;

    /**
     * Active
     * @var tinyint(1)
     */
    protected $active = null;

    /**
     * TS
     * @var timestamp
     */
    protected $ts = null;

    /**
     * Domain
     * @var int(11)
     */
    protected $dom_id = null;

    /**
     * API scope
     * @var text
     */
    protected $api_scope = null;

    /**
     * User scope
     * @var text
     */
    protected $users_scope = null;

    /**
     * Ips, range
     * @var text
     */
    protected $ips = null;

    /**
     * partner code
     * @var varchar(80)
     */
    protected $part_name = null;

    /**
     *
     */
    public function initialize ()
    {
        parent::initialize();
        $this->setSource('auth_brokers');
    }

    /**
     *
     */
    public function columnMap ()
    {
        return array(
            'brk_id'          => 'id',
            'brk_key'         => 'key',
            'brk_name'        => 'name',
            'brk_certificate' => 'certificate',
            'brk_active'      => 'active',
            'brk_ts'          => 'ts',
            'dom_id'          => 'dom_id',
            'brk_api_scope'   => 'api_scope',
            'brk_users_scope' => 'users_scope',
            'brk_ips'         => 'ips',
            'brk_part_name'   => 'part_name'
        );
    }

    /**
     * Setter for id
     *
     * @param int $p_id
     *
     * @return Broker
     */
    public function setId ($p_id)
    {
        $this->id = $p_id;

        return $this;
    }

    /**
     * Getter for id
     *
     * @return int
     */
    public function getId ()
    {
        return $this->id;
    }

    /**
     * Setter for key
     *
     * @param varchar $p_key
     *
     * @return Broker
     */
    public function setKey ($p_key)
    {
        $this->key = $p_key;

        return $this;
    }

    /**
     * Getter for key
     *
     * @return varchar
     */
    public function getKey ()
    {
        return $this->key;
    }

    /**
     * Setter for name
     *
     * @param varchar $p_name
     *
     * @return Broker
     */
    public function setName ($p_name)
    {
        $this->name = $p_name;

        return $this;
    }

    /**
     * Getter for name
     *
     * @return varchar
     */
    public function getName ()
    {
        return $this->name;
    }

    /**
     * Setter for certificate
     *
     * @param char $p_certificate
     *
     * @return Broker
     */
    public function setCertificate ($p_certificate)
    {
        $this->certificate = $p_certificate;

        return $this;
    }

    /**
     * Getter for certificate
     *
     * @return char
     */
    public function getCertificate ()
    {
        return $this->certificate;
    }

    /**
     * Setter for active
     *
     * @param tinyint $p_active
     *
     * @return Broker
     */
    public function setActive ($p_active)
    {
        $this->active = $p_active;

        return $this;
    }

    /**
     * Getter for active
     *
     * @return tinyint
     */
    public function getActive ()
    {
        return $this->active;
    }

    /**
     * Setter for ts
     *
     * @param timestamp $p_ts
     *
     * @return Broker
     */
    public function setTs ($p_ts)
    {
        if ($p_ts !== null && $p_ts != '' && strpos($p_ts, '/') !== false ) {
            $this->ts = MyDate::ddmmyyyyToMysql($p_ts);
        } else {
            $this->ts = $p_ts;
        }

        return $this;
    }

    /**
     * Getter for ts
     *
     * @return timestamp
     */
    public function getTs ()
    {
        return $this->ts;
    }

    /**
     * Setter for dom_id
     *
     * @param int $p_dom_id
     *
     * @return Broker
     */
    public function setDomId ($p_dom_id)
    {
        $this->dom_id = $p_dom_id;

        return $this;
    }

    /**
     * Getter for dom_id
     *
     * @return int
     */
    public function getDomId ()
    {
        return $this->dom_id;
    }

    /**
     * Setter for api_scope
     *
     * @param text $p_api_scope
     *
     * @return Broker
     */
    public function setApiScope ($p_api_scope)
    {
        $this->api_scope = $p_api_scope;

        return $this;
    }

    /**
     * Getter for api_scope
     *
     * @return text
     */
    public function getApiScope ()
    {
        return $this->api_scope;
    }

    /**
     * Setter for users_scope
     *
     * @param text $p_users_scope
     *
     * @return Broker
     */
    public function setUsersScope ($p_users_scope)
    {
        $this->users_scope = $p_users_scope;

        return $this;
    }

    /**
     * Getter for users_scope
     *
     * @return text
     */
    public function getUsersScope ()
    {
        return $this->users_scope;
    }

    /**
     * Setter for ips
     *
     * @param text $p_ips
     *
     * @return Broker
     */
    public function setIps ($p_ips)
    {
        $this->ips = $p_ips;

        return $this;
    }

    /**
     * Getter for ips
     *
     * @return text
     */
    public function getIps ()
    {
        return $this->ips;
    }

    /**
     * Setter for part_name
     *
     * @param varchar $p_part_name
     *
     * @return Broker
     */
    public function setPartName ($p_part_name)
    {
        $this->part_name = $p_part_name;

        return $this;
    }

    /**
     * Getter for part_name
     *
     * @return varchar
     */
    public function getPartName ()
    {
        return $this->part_name;
    }

    /**
     * Is active ?
     *
     * @return boolean
     */
    public function isActive ()
    {
        if (isset($this->active) && ($this->active == 1 || in_array(strtoupper($this->active), array('O', 'Y', '1')))) {
            
            return true;
        }
        
        return false;
    }

}