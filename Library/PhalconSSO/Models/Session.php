<?php
namespace PhalconSSO\Models;

use \PhalconFW\Mvc\Model;
use \PhalconFW\Tools\Date as MyDate;

class Session extends BaseModel
{

    /**
     * Session Id
     * @var bigint(20)
     */
    protected $id = null;

    /**
     * User Id
     * @var varchar(255)
     */
    protected $user_id = null;

    /**
     * End
     * @var timestamp
     */
    protected $end = null;

    /**
     * Content (json)
     * @var text
     */
    protected $content = null;

    /**
     * Client Addr
     * @var varchar(32)
     */
    protected $client_addr = null;

    /**
     * Start
     * @var timestamp
     */
    protected $start = null;

    /**
     * Touch
     * @var timestamp
     */
    protected $touch = null;

    /**
     *
     */
    public function initialize ()
    {
        parent::initialize();
        $this->setSource('auth_sessions');
    }

    /**
     *
     */
    public function columnMap ()
    {
        return array(
            'sess_id'          => 'id',
            'user_id'          => 'user_id',
            'sess_end'         => 'end',
            'sess_content'     => 'content',
            'sess_client_addr' => 'client_addr',
            'sess_start'       => 'start',
            'sess_touch'       => 'touch'
        );
    }

    /**
     * Setter for id
     *
     * @param bigint $p_id
     *
     * @return Session
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
     * Setter for user_id
     *
     * @param varchar $p_user_id
     *
     * @return Session
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
     * Setter for end
     *
     * @param timestamp $p_end
     *
     * @return Session
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
     * Setter for content
     *
     * @param text $p_content
     *
     * @return Session
     */
    public function setContent ($p_content)
    {
        $this->content = $p_content;

        return $this;
    }

    /**
     * Getter for content
     *
     * @return text
     */
    public function getContent ()
    {
        return $this->content;
    }

    /**
     * Setter for client_addr
     *
     * @param varchar $p_client_addr
     *
     * @return Session
     */
    public function setClientAddr ($p_client_addr)
    {
        $this->client_addr = $p_client_addr;

        return $this;
    }

    /**
     * Getter for client_addr
     *
     * @return varchar
     */
    public function getClientAddr ()
    {
        return $this->client_addr;
    }

    /**
     * Setter for start
     *
     * @param timestamp $p_start
     *
     * @return Session
     */
    public function setStart ($p_start)
    {
        if ($p_start !== null && $p_start != '' && strpos($p_start, '/') !== false ) {
            $this->start = MyDate::ddmmyyyyToMysql($p_start);
        } else {
            $this->start = $p_start;
        }

        return $this;
    }

    /**
     * Getter for start
     *
     * @return timestamp
     */
    public function getStart ()
    {
        return $this->start;
    }

    /**
     * Setter for touch
     *
     * @param timestamp $p_touch
     *
     * @return Session
     */
    public function setTouch ($p_touch)
    {
        if ($p_touch !== null && $p_touch != '' && strpos($p_touch, '/') !== false ) {
            $this->touch = MyDate::ddmmyyyyToMysql($p_touch);
        } else {
            $this->touch = $p_touch;
        }

        return $this;
    }

    /**
     * Getter for touch
     *
     * @return timestamp
     */
    public function getTouch ()
    {
        return $this->touch;
    }

}