<?php
namespace PhalconTech\Models;

use \PhalconFW\Mvc\Model;
use \PhalconFW\Tools\Date as MyDate;

class EmailConfig extends BaseModel
{

    /**
     * 
     * @var bigint(20)
     */
    protected $id = null;

    /**
     * 
     * @var varchar(80)
     */
    protected $name = null;

    /**
     * 
     * @var varchar(255)
     */
    protected $from_name = null;

    /**
     * 
     * @var varchar(255)
     */
    protected $from_email = null;

    /**
     *
     */
    public function initialize ()
    {
        parent::initialize();
        $this->setSource('tech_emails_configs');
    }

    /**
     *
     */
    public function columnMap ()
    {
        return array(
            'emac_id'         => 'id',
            'emac_name'       => 'name',
            'emac_from_name'  => 'from_name',
            'emac_from_email' => 'from_email'
        );
    }

    /**
     * Setter for id
     *
     * @param bigint $p_id
     *
     * @return EmailConfig
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
     * Setter for name
     *
     * @param varchar $p_name
     *
     * @return EmailConfig
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
     * Setter for from_name
     *
     * @param varchar $p_from_name
     *
     * @return EmailConfig
     */
    public function setFromName ($p_from_name)
    {
        $this->from_name = $p_from_name;

        return $this;
    }

    /**
     * Getter for from_name
     *
     * @return varchar
     */
    public function getFromName ()
    {
        return $this->from_name;
    }

    /**
     * Setter for from_email
     *
     * @param varchar $p_from_email
     *
     * @return EmailConfig
     */
    public function setFromEmail ($p_from_email)
    {
        $this->from_email = $p_from_email;

        return $this;
    }

    /**
     * Getter for from_email
     *
     * @return varchar
     */
    public function getFromEmail ()
    {
        return $this->from_email;
    }

}