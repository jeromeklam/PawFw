<?php
namespace PhalconSSO\Models;

use \PhalconFW\Mvc\Model;
use \PhalconFW\Tools\Date as MyDate;

class User extends BaseModel
{

    /**
     * User types
     * @var string
     */
    const TYPE_USER      = 'USER';
    const TYPE_IP        = 'IP';
    const TYPE_ANONYMOUS = 'ANONYMOUS';
    const TYPE_UUID      = 'UUID';

    /**
     * User titles
     * @var string
     */
    const TITLE_MISTER = 'MISTER';
    const TITLE_MADAM  = 'MADAM';
    const TITLE_MISS   = 'MISS';
    const TITLE_OTHER  = 'OTHER';

    /**
     * Id
     * @var varchar(255)
     */
    protected $id = null;

    /**
     * Login
     * @var varchar(255)
     */
    protected $login = null;

    /**
     * Encypted password
     * @var varchar(255)
     */
    protected $password = null;

    /**
     * Active ?
     * @var tinyint(1)
     */
    protected $active = null;

    /**
     * Password salt
     * @var varchar(80)
     */
    protected $salt = null;

    /**
     * Email (= login)
     * @var varchar(255)
     */
    protected $email = null;

    /**
     * First name
     * @var varchar(80)
     */
    protected $first_name = null;

    /**
     * Last name
     * @var varchar(80)
     */
    protected $last_name = null;

    /**
     * Title
     * @var enum
     */
    protected $title = self::TITLE_OTHER;

    /**
     * Roles (admin, ...)
     * @var varchar(255)
     */
    protected $roles = null;

    /**
     * Type
     * @var enum
     */
    protected $type = self::TYPE_USER;

    /**
     * Ips, ranges, ...
     * @var text
     */
    protected $ips = null;

    /**
     * Last cache update
     * @var timestamp
     */
    protected $last_update = null;

    /**
     * Preferred language
     * @var varchar(3)
     */
    protected $preferred_language = null;

    /**
     * Avatar
     * @var blob
     */
    protected $avatar = null;

    /**
     * Cache
     * @var text
     */
    protected $cache = null;

    /**
     * Validation string
     * @var varchar(32)
     */
    protected $val_string = null;
    
    /**
     * Validation expiration
     * @var timestamp
     */
    protected $val_end = null;
    
    /**
     * Validation new login
     * @var varchar(255)
     */
    protected $val_login = null;

    /**
     *
     */
    public function initialize ()
    {
        parent::initialize();
        $this->setSource('auth_users');
    }

    /**
     *
     */
    public function columnMap ()
    {
        return array(
            'user_id'                 => 'id',
            'user_login'              => 'login',
            'user_password'           => 'password',
            'user_active'             => 'active',
            'user_salt'               => 'salt',
            'user_email'              => 'email',
            'user_first_name'         => 'first_name',
            'user_last_name'          => 'last_name',
            'user_title'              => 'title',
            'user_roles'              => 'roles',
            'user_type'               => 'type',
            'user_ips'                => 'ips',
            'user_last_update'        => 'last_update',
            'user_preferred_language' => 'preferred_language',
            'user_avatar'             => 'avatar',
            'user_cache'              => 'cache',
            'user_val_string'         => 'val_string',
            'user_val_end'            => 'val_end',
            'user_val_login'          => 'val_login'
        );
    }

    /**
     * Setter for id
     *
     * @param varchar $p_id
     *
     * @return User
     */
    public function setId ($p_id)
    {
        $this->id = $p_id;
        
        return $this;
    }

    /**
     * Getter for id
     *
     * @return varchar
     */
    public function getId ()
    {
        return $this->id;
    }

    /**
     * Setter for login
     *
     * @param varchar $p_login
     *
     * @return User
     */
    public function setLogin ($p_login)
    {
        $this->login = $p_login;
        
        return $this;
    }

    /**
     * Getter for login
     *
     * @return varchar
     */
    public function getLogin ()
    {
        return $this->login;
    }

    /**
     * Setter for password
     *
     * @param varchar $p_password
     *
     * @return User
     */
    public function setPassword ($p_password)
    {
        $this->password = $p_password;
        
        return $this;
    }

    /**
     * Getter for password
     *
     * @return varchar
     */
    public function getPassword ()
    {
        return $this->password;
    }

    /**
     * Setter for active
     *
     * @param tinyint $p_active
     *
     * @return User
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
     * Setter for salt
     *
     * @param varchar $p_salt
     *
     * @return User
     */
    public function setSalt ($p_salt)
    {
        $this->salt = $p_salt;
        
        return $this;
    }

    /**
     * Getter for salt
     *
     * @return varchar
     */
    public function getSalt ()
    {
        return $this->salt;
    }

    /**
     * Setter for email
     *
     * @param varchar $p_email
     *
     * @return User
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
     * Setter for first_name
     *
     * @param varchar $p_first_name
     *
     * @return User
     */
    public function setFirstName ($p_first_name)
    {
        $this->first_name = $p_first_name;
        
        return $this;
    }

    /**
     * Getter for first_name
     *
     * @return varchar
     */
    public function getFirstName ()
    {
        return $this->first_name;
    }

    /**
     * Setter for last_name
     *
     * @param varchar $p_last_name
     *
     * @return User
     */
    public function setLastName ($p_last_name)
    {
        $this->last_name = $p_last_name;
        
        return $this;
    }

    /**
     * Getter for last_name
     *
     * @return varchar
     */
    public function getLastName ()
    {
        return $this->last_name;
    }

    /**
     * Setter for title
     *
     * @param enum $p_title
     *
     * @return User
     */
    public function setTitle ($p_title)
    {
        if (in_array($p_title, self::getTitles())) {
            $this->title = $p_title;
        } else {
            $this->title = self::TITLE_OTHER;
        }
        
        return $this;
    }

    /**
     * Getter for title
     *
     * @param array $p_titles
     *
     * @return enum
     */
    public function getTitle ($p_titles = array())
    {
        if (array_key_exists($this->title, $p_titles)) {
            
            return $p_titles[$this->title];
        }
        
        return $this->title;
    }

    /**
     * Setter for roles
     *
     * @param varchar $p_roles
     *
     * @return User
     */
    public function setRoles ($p_roles)
    {
        $this->roles = $p_roles;
        
        return $this;
    }

    /**
     * Getter for roles
     *
     * @return varchar
     */
    public function getRoles ()
    {
        return $this->roles;
    }

    /**
     * Setter for type
     *
     * @param enum $p_type
     *
     * @return User
     */
    public function setType ($p_type)
    {
        if (in_array($p_type, self::getTypes())) {
            $this->type = $p_type;
        } else {
            $this->type = self::TYPE_USER;
        }
        
        return $this;
    }

    /**
     * Getter for type
     *
     * @return enum
     */
    public function getType ()
    {
        return $this->type;
    }

    /**
     * Setter for ips
     *
     * @param text $p_ips
     *
     * @return User
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
     * Setter for last_update
     *
     * @param timestamp $p_last_update
     *
     * @return User
     */
    public function setLastUpdate ($p_last_update)
    {
        if ($p_last_update !== null && $p_last_update != '' && strpos($p_last_update, '/') !== false ) {
            $this->last_update = MyDate::ddmmyyyyToMysql($p_last_update);
        } else {
            $this->last_update = $p_last_update;
        }
        
        return $this;
    }

    /**
     * Getter for last_update
     *
     * @return timestamp
     */
    public function getLastUpdate ()
    {
        return $this->last_update;
    }

    /**
     * Setter for preferred_language
     *
     * @param varchar $p_preferred_language
     *
     * @return User
     */
    public function setPreferredLanguage ($p_preferred_language)
    {
        $this->preferred_language = $p_preferred_language;
        
        return $this;
    }

    /**
     * Getter for preferred_language
     *
     * @return varchar
     */
    public function getPreferredLanguage ()
    {
        return $this->preferred_language;
    }

    /**
     * Setter for avatar
     *
     * @param blob $p_avatar
     *
     * @return User
     */
    public function setAvatar ($p_avatar)
    {
        $this->avatar = $p_avatar;
        
        return $this;
    }

    /**
     * Getter for avatar
     *
     * @return blob
     */
    public function getAvatar ()
    {
        return $this->avatar;
    }

    /**
     * Setter for cache
     *
     * @param text $p_cache
     *
     * @return User
     */
    public function setCache ($p_cache)
    {
        $this->cache = $p_cache;
        
        return $this;
    }

    /**
     * Getter for cache
     *
     * @return text
     */
    public function getCache ()
    {
        return $this->cache;
    }

    /**
     * Setter for val_string
     *
     * @param varchar $p_val_string
     *
     * @return User
     */
    public function setValString ($p_val_string)
    {
        $this->val_string = $p_val_string;
        
        return $this;
    }

    /**
     * Getter for val_string
     *
     * @return varchar
     */
    public function getValString ()
    {
        return $this->val_string;
    }

    /**
     * Setter for val_end
     *
     * @param timestamp $p_val_end
     *
     * @return User
     */
    public function setValEnd ($p_val_end)
    {
        if ($p_val_end !== null && $p_val_end != '' && strpos($p_val_end, '/') !== false ) {
            $this->val_end = MyDate::ddmmyyyyToMysql($p_val_end);
        } else {
            $this->val_end = $p_val_end;
        }
        
        return $this;
    }

    /**
     * Getter for val_end
     *
     * @return timestamp
     */
    public function getValEnd ()
    {
        return $this->val_end;
    }

    /**
     * Setter for val_login
     *
     * @param varchar $p_val_login
     *
     * @return User
     */
    public function setValLogin ($p_val_login)
    {
        $this->val_login = $p_val_login;
        
        return $this;
    }

    /**
     * Getter for val_login
     *
     * @return varchar
     */
    public function getValLogin ()
    {
        return $this->val_login;
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

    /**
     * Titles
     *
     * @return array
     */
    public static function getTitles ()
    {
        return array(self::TITLE_MISTER, self::TITLE_MADAM, self::TITLE_MISS, self::TITLE_OTHER);
    }

    /**
     * Types
     *
     * @return array
     */
    public static function getTypes ()
    {
        return array(self::TYPE_USER, self::TYPE_IP, self::TYPE_ANONYMOUS, self::TYPE_UUID);
    }

    /**
     * Get full name
     *
     * @return string
     */
    public function getFullName ()
    {
        return trim($this->getFirstName() . ' ' . $this->getLastName());
    }

    /**
     * Add key to cache
     *
     * @param unknown $p_key
     * @param unknown $p_value
     *
     * @return User
     */
    public function putInCache ($p_key, $p_value)
    {
        if ($this->cache === null) {
            $cache = array();
        } else {
            $cache = json_decode($this->cache, true);
        }
        $cache[$p_key] = $p_value;
        $this->cache   = json_encode($cache);
        
        return $this;
    }

    /**
     * Get value from cache
     *
     * @param string $p_key
     *
     * @return false | mixed
     */
    public function getFromCache ($p_key)
    {
        if ($this->cache !== null) {
            $cache = json_decode($this->cache, true);
            if (array_key_exists($p_key, $cache)) {
                
                return $cache[$p_key];
            }
        }
        
        return false;
    }

    /**
     * Get cache as array
     *
     * @return array
     */
    public function getCacheAsArray ()
    {
        if ($this->cache !== null) {
            $cache = json_decode($this->cache, true);
            
            return $cache;
        }
        
        return array();
    }

    /**
     * return user as array
     *
     * @param array $p_titles
     *
     * @return array
     */
    public function asArray ($p_titles = array())
    {
        $arr = array(
            'id'        => $this->getId(),
            'title'     => $this->getTitle($p_titles),
            'firstname' => $this->getFirstName(),
            'lastname'  => $this->getLastName(),
            'email'     => $this->getEmail(),
            'login'     => $this->getLogin(),
            'active'    => $this->isActive(),
            'actToken'  => $this->getValString()
        );
        
        return array_merge($arr, $this->getCacheAsArray());
    }

}