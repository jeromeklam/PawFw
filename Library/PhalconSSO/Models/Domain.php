<?php
namespace PhalconSSO\Models;

use \PhalconFW\Mvc\Model;
use \PhalconFW\Tools\Date as MyDate;

class Domain extends BaseModel
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
     * @var varchar(80)
     */
    protected $name = null;

    /**
     * Ping ?
     * @var tinyint(1)
     */
    protected $concurrent_user = null;

    /**
     * Maintain seconds time
     * @var tinyint(4)
     */
    protected $maintain_seconds = null;

    /**
     * Session duration
     * @var int(9)
     */
    protected $session_minutes = null;

    /**
     * List of sites
     * @var text
     */
    protected $sites = null;

    /**
     *
     */
    public function initialize ()
    {
        parent::initialize();
        $this->setSource('auth_domains');
    }

    /**
     *
     */
    public function columnMap ()
    {
        return array(
            'dom_id'               => 'id',
            'dom_key'              => 'key',
            'dom_name'             => 'name',
            'dom_concurrent_user'  => 'concurrent_user',
            'dom_maintain_seconds' => 'maintain_seconds',
            'dom_session_minutes'  => 'session_minutes',
            'dom_sites'            => 'sites'
        );
    }

    /**
     * Setter for id
     *
     * @param int $p_id
     *
     * @return Domain
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
     * @return Domain
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
     * @return Domain
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
     * Setter for concurrent_user
     *
     * @param tinyint $p_concurrent_user
     *
     * @return Domain
     */
    public function setConcurrentUser ($p_concurrent_user)
    {
        $this->concurrent_user = $p_concurrent_user;

        return $this;
    }

    /**
     * Getter for concurrent_user
     *
     * @return tinyint
     */
    public function getConcurrentUser ()
    {
        return $this->concurrent_user;
    }

    /**
     * Setter for maintain_seconds
     *
     * @param tinyint $p_maintain_seconds
     *
     * @return Domain
     */
    public function setMaintainSeconds ($p_maintain_seconds)
    {
        $this->maintain_seconds = $p_maintain_seconds;

        return $this;
    }

    /**
     * Getter for maintain_seconds
     *
     * @return tinyint
     */
    public function getMaintainSeconds ()
    {
        return $this->maintain_seconds;
    }

    /**
     * Setter for session_minutes
     *
     * @param int $p_session_minutes
     *
     * @return Domain
     */
    public function setSessionMinutes ($p_session_minutes)
    {
        $this->session_minutes = $p_session_minutes;

        return $this;
    }

    /**
     * Getter for session_minutes
     *
     * @return int
     */
    public function getSessionMinutes ()
    {
        return $this->session_minutes;
    }

    /**
     * Setter for sites
     *
     * @param text $p_sites
     *
     * @return Domain
     */
    public function setSites ($p_sites)
    {
        $this->sites = $p_sites;

        return $this;
    }

    /**
     * Getter for sites
     *
     * @return text
     */
    public function getSites ()
    {
        return $this->sites;
    }

}