<?php
namespace PhalconSSO\Models;

use \PhalconFW\Mvc\Model;
use \PhalconFW\Tools\Date as MyDate;

class BrokerSession extends BaseModel
{

    /**
     * Broker Session Id
     * @var bigint(20)
     */
    protected $id = null;

    /**
     * Key
     * @var varchar(50)
     */
    protected $brk_key = null;

    /**
     * Token
     * @var varchar(40)
     */
    protected $token = null;

    /**
     * Session Id
     * @var varchar(40)
     */
    protected $session_id = null;

    /**
     * Client Addr
     * @var varchar(50)
     */
    protected $client_address = null;

    /**
     * Start TS
     * @var timestamp
     */
    protected $date_created = null;

    /**
     * End
     * @var timestamp
     */
    protected $end = null;

    /**
     * Session Id
     * @var bigint(20)
     */
    protected $sess_id = null;

    /**
     *
     */
    public function initialize ()
    {
        parent::initialize();
        $this->setSource('auth_brokers_sessions');
    }

    /**
     *
     */
    public function columnMap ()
    {
        return array(
            'brs_id'             => 'id',
            'brk_key'            => 'brk_key',
            'brs_token'          => 'token',
            'brs_session_id'     => 'session_id',
            'brs_client_address' => 'client_address',
            'brs_date_created'   => 'date_created',
            'brs_end'            => 'end',
            'sess_id'            => 'sess_id'
        );
    }

    /**
     * Setter for id
     *
     * @param bigint $p_id
     *
     * @return BrokerSession
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
     * Setter for brk_key
     *
     * @param varchar $p_brk_key
     *
     * @return BrokerSession
     */
    public function setBrkKey ($p_brk_key)
    {
        $this->brk_key = $p_brk_key;

        return $this;
    }

    /**
     * Getter for brk_key
     *
     * @return varchar
     */
    public function getBrkKey ()
    {
        return $this->brk_key;
    }

    /**
     * Setter for token
     *
     * @param varchar $p_token
     *
     * @return BrokerSession
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
     * Setter for session_id
     *
     * @param varchar $p_session_id
     *
     * @return BrokerSession
     */
    public function setSessionId ($p_session_id)
    {
        $this->session_id = $p_session_id;

        return $this;
    }

    /**
     * Getter for session_id
     *
     * @return varchar
     */
    public function getSessionId ()
    {
        return $this->session_id;
    }

    /**
     * Setter for client_address
     *
     * @param varchar $p_client_address
     *
     * @return BrokerSession
     */
    public function setClientAddress ($p_client_address)
    {
        $this->client_address = $p_client_address;

        return $this;
    }

    /**
     * Getter for client_address
     *
     * @return varchar
     */
    public function getClientAddress ()
    {
        return $this->client_address;
    }

    /**
     * Setter for date_created
     *
     * @param timestamp $p_date_created
     *
     * @return BrokerSession
     */
    public function setDateCreated ($p_date_created)
    {
        if ($p_date_created !== null && $p_date_created != '' && strpos($p_date_created, '/') !== false ) {
            $this->date_created = MyDate::ddmmyyyyToMysql($p_date_created);
        } else {
            $this->date_created = $p_date_created;
        }

        return $this;
    }

    /**
     * Getter for date_created
     *
     * @return timestamp
     */
    public function getDateCreated ()
    {
        return $this->date_created;
    }

    /**
     * Setter for end
     *
     * @param timestamp $p_end
     *
     * @return BrokerSession
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

    /**
     * Setter for sess_id
     *
     * @param bigint $p_sess_id
     *
     * @return BrokerSession
     */
    public function setSessId ($p_sess_id)
    {
        $this->sess_id = $p_sess_id;

        return $this;
    }

    /**
     * Getter for sess_id
     *
     * @return bigint
     */
    public function getSessId ()
    {
        return $this->sess_id;
    }

}