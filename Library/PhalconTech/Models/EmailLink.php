<?php
namespace PhalconTech\Models;

use \PhalconFW\Mvc\Model;
use \PhalconFW\Tools\Date as MyDate;

class EmailLink extends BaseModel
{

    /**
     * 
     * @var bigint(20)
     */
    protected $email_id = null;

    /**
     * 
     * @var bigint(20)
     */
    protected $emac_id = null;

    /**
     * 
     * @var text
     */
    protected $cc = null;

    /**
     *
     */
    public function initialize ()
    {
        parent::initialize();
        $this->setSource('tech_emails_links');
    }

    /**
     *
     */
    public function columnMap ()
    {
        return array(
            'email_id' => 'email_id',
            'emac_id'  => 'emac_id',
            'emal_cc'  => 'cc'
        );
    }

    /**
     * Setter for email_id
     *
     * @param bigint $p_email_id
     *
     * @return EmailLink
     */
    public function setEmailId ($p_email_id)
    {
        $this->email_id = $p_email_id;

        return $this;
    }

    /**
     * Getter for email_id
     *
     * @return bigint
     */
    public function getEmailId ()
    {
        return $this->email_id;
    }

    /**
     * Setter for emac_id
     *
     * @param bigint $p_emac_id
     *
     * @return EmailLink
     */
    public function setEmacId ($p_emac_id)
    {
        $this->emac_id = $p_emac_id;

        return $this;
    }

    /**
     * Getter for emac_id
     *
     * @return bigint
     */
    public function getEmacId ()
    {
        return $this->emac_id;
    }

    /**
     * Setter for cc
     *
     * @param text $p_cc
     *
     * @return EmailLink
     */
    public function setCc ($p_cc)
    {
        $this->cc = $p_cc;

        return $this;
    }

    /**
     * Getter for cc
     *
     * @return text
     */
    public function getCc ()
    {
        return $this->cc;
    }

}