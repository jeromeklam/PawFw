<?php
namespace PhalconTech\Models;

use \PhalconFW\Mvc\Model;
use \PhalconFW\Tools\Date as MyDate;

class Email extends BaseModel
{

    /**
     * Email Id
     * @var bigint(20)
     */
    protected $id = null;

    /**
     * Email Type
     * @var varchar(80)
     */
    protected $type = null;

    /**
     * Language
     * @var varchar(3)
     */
    protected $lang = null;

    /**
     * Is default ?
     * @var tinyint(1)
     */
    protected $default = null;

    /**
     * Subject
     * @var varchar(255)
     */
    protected $subject = null;

    /**
     * Html body
     * @var text
     */
    protected $html = null;

    /**
     * Text body
     * @var text
     */
    protected $text = null;

    /**
     *
     */
    public function initialize ()
    {
        parent::initialize();
        $this->setSource('tech_emails');
    }

    /**
     *
     */
    public function columnMap ()
    {
        return array(
            'email_id'      => 'id',
            'email_type'    => 'type',
            'email_lang'    => 'lang',
            'email_default' => 'default',
            'email_subject' => 'subject',
            'email_html'    => 'html',
            'email_text'    => 'text'
        );
    }

    /**
     * Setter for id
     *
     * @param bigint $p_id
     *
     * @return Email
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
     * Setter for type
     *
     * @param varchar $p_type
     *
     * @return Email
     */
    public function setType ($p_type)
    {
        $this->type = $p_type;

        return $this;
    }

    /**
     * Getter for type
     *
     * @return varchar
     */
    public function getType ()
    {
        return $this->type;
    }

    /**
     * Setter for lang
     *
     * @param varchar $p_lang
     *
     * @return Email
     */
    public function setLang ($p_lang)
    {
        $this->lang = $p_lang;

        return $this;
    }

    /**
     * Getter for lang
     *
     * @return varchar
     */
    public function getLang ()
    {
        return $this->lang;
    }

    /**
     * Setter for default
     *
     * @param tinyint $p_default
     *
     * @return Email
     */
    public function setDefault ($p_default)
    {
        $this->default = $p_default;

        return $this;
    }

    /**
     * Getter for default
     *
     * @return tinyint
     */
    public function getDefault ()
    {
        return $this->default;
    }

    /**
     * Setter for subject
     *
     * @param varchar $p_subject
     *
     * @return Email
     */
    public function setSubject ($p_subject)
    {
        $this->subject = $p_subject;

        return $this;
    }

    /**
     * Getter for subject
     *
     * @return varchar
     */
    public function getSubject ()
    {
        return $this->subject;
    }

    /**
     * Setter for html
     *
     * @param text $p_html
     *
     * @return Email
     */
    public function setHtml ($p_html)
    {
        $this->html = $p_html;

        return $this;
    }

    /**
     * Getter for html
     *
     * @return text
     */
    public function getHtml ()
    {
        return $this->html;
    }

    /**
     * Setter for text
     *
     * @param text $p_text
     *
     * @return Email
     */
    public function setText ($p_text)
    {
        $this->text = $p_text;

        return $this;
    }

    /**
     * Getter for text
     *
     * @return text
     */
    public function getText ()
    {
        return $this->text;
    }

}