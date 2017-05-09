<?php
namespace PhalconSSO;

use \PhalconFW\Behaviour\DIBehaviour;
use \PhalconFW\Behaviour\TranslationBehaviour;
use \PhalconFW\Tools\Date AS FWDate;
use \PhalconSSO\Constants as SSOCST;
use \PhalconSSO\ErrorCodes as SSOERR;
use \PhalconSSO\Models\Broker;
use \PhalconSSO\Models\BrokerSession;
use \PhalconSSO\Models\Domain;
use \PhalconSSO\Models\User;
use \PhalconSSO\Models\PasswordToken;
use \PhalconSSO\Models\Session AS SSOSession;
use \PhalconSSO\Http\Remote;
use \PhalconSSO\PhalconSSOException;
use Phalcon\Mvc\Model\Query;

/**
 *
 * @author jeromeklam
 *
 */
class Server
{

    use DIBehaviour {
        DIBehaviour::__construct as protected __DIConstruct;
    }

    use TranslationBehaviour;

    /**
     * Instance of server
     * @var Server
     */
    protected static $instance = null;

    /**
     * omain instance
     * @var Domain
     */
    protected static $domain = null;

    /**
     * Broker instance
     * @var Broker
     */
    protected static $broker = null;

    /**
     * Session
     * @var Session
     */
    protected static $session = null;

    /**
     * User
     * @var User
     */
    protected static $user = null;

    /**
     * Constructor
     *
     * @param array   $p_options
     */
    protected function __construct (array $p_options = array())
    {
        $this->__DIConstruct();
        if (!array_key_exists(SSOCST::SSO_CNX, $p_options)) {
            throw new \InvalidArgumentException(
                'Configuration error : db connexion required !',
                SSOERR::ERROR_OPTIONS_DB_CNX_REQUIRED
            );
        }
        /**
         * Set connexion for db
         */
        $di = $this->getDI();
        $di->set(SSOCST::SSO_CNX, $di->get($p_options[SSOCST::SSO_CNX]));
        /**
         * Broker if set in config
         */
        if (array_key_exists(SSOCST::BROKER_KEY, $p_options)) {
            $this->setBroker($p_options[SSOCST::BROKER_KEY]);
        }
    }

    /**
     * Get SSOServer instance
     *
     * @param array $p_options
     *
     * @return \DBookSecurityServer\SSO\Server
     */
    public static function getInstance (array $p_options = array())
    {
        if (self::$instance === null) {
            if (is_array($p_options) && count($p_options) > 0) {
                self::$instance = new self($p_options);
            } else {
                throw new \InvalidArgumentException('Configuration error : no options defined !');
            }
        }
        
        return self::$instance;
    }

    /**
     * Set broker
     *
     * @param string $p_brokerKey
     *
     * @throws \Exception
     */
    public function setBroker ($p_brokerKey)
    {
        $myBroker = Broker::findFirst(array(
            "conditions" => "key = ?1",
            "bind"       => array(1 => $p_brokerKey)
        ));
        if ($myBroker instanceof Broker) {
            self::$broker = $myBroker;
            self::$domain = Domain::findFirst(self::$broker->getDomId());
            if (!self::$domain instanceof Domain) {
                throw new \Exception(sprintf('Domain not found !'));
            }
            if (!$myBroker->isActive()) {
                throw new \Exception(sprintf('Broker no more active !'));
            }
        } else {
            throw new \Exception(sprintf('Broker not found !'));
        }
        /**
         * Init the cookies, ....
         */
        $ssoCk = Remote::getSSOCookie(self::$domain->getName());
        $appCk = Remote::getApplicationCookie();
        $cliIp = Remote::getAddr();
        /**
         * Try to get a broker session....
        */
        $this->verifyBrokerSession(self::$broker->getKey(), $ssoCk, $appCk, $cliIp);
    }

    /**
     * Signin
     *
     * @param string  $p_login
     * @param string  $p_password
     * @param boolean $p_remember
     *
     * @throws \PhalconSSO\PhalconSSOException
     *
     * @return boolean
     */
    public function signinByLoginAndPassword ($p_login, $p_password, $p_remember = false)
    {
        $di = $this->getDI();
        try {
            $di->getShared('eventsManager')->fire('sso:beforeSigninByLoginAndPassword', $this);
        } catch (\Exception $ex) {
        }
        self::$user = false;
        $user       = User::findFirst(array(
            'conditions' => 'login = ?1',
            'bind'       => array(1 => $p_login)
        ));
        if ($user instanceof User) {
            $testPassword = md5($user->getSalt() . $p_password);
            if ($testPassword != $user->getPassword()) {
                throw new PhalconSSOException (sprintf('Wrong password !'), ErrorCodes::ERROR_PASSWORD_WRONG);
            }
            if (!$user->isActive()) {
                throw new PhalconSSOException (sprintf('User is not active !'), ErrorCodes::ERROR_USER_DEACTIVATED);
            }
            // Ok, save to session...
            if (self::$session instanceof SSOSession) {
                self::$session
                    ->setUserId($user->getId())
                    ->setContent($user->serialize())
                ;
                self::$session->update();
                if ($p_remember) {
                    // @todo : set autologin cookie
                }
                self::$user = $user;
            } else {
                throw new PhalconSSOException('General error : can\t retrieve session !', ErrorCodes::ERROR_GENERAL);
            }
        } else {
            throw new PhalconSSOException (sprintf('Login %s doesn\'t exists !', $p_login), ErrorCodes::ERROR_LOGIN_NOTFOUND);
        }
        try {
            $di->getShared('eventsManager')->fire('sso:afterSigninByLoginAndPassword', $this, $user);
        } catch (\Exception $ex) {
        }
        
        return true;
    }

    /**
     * Logout current user
     *
     * @throws PhalconSSOException
     */
    public function logout ()
    {
        if (self::$session instanceof SSOSession) {
            self::$session
                ->setUserId(null)
                ->setContent(null)
            ;
            self::$session->update();
        } else {
            throw new PhalconSSOException('General error : can\t retrieve session !', ErrorCodes::ERROR_GENERAL);
        }
    }

    /**
     * Get current loggedin user
     *
     * @throws PhalconSSOException
     *
     * @return \PhalconSSO\Models\User
     */
    public function getUser ()
    {
        if (self::$user === null) {
            self::$user = false;
            if (self::$session instanceof SSOSession) {
                if (self::$session->getUserId() !== null) {
                    self::$user = User::findFirst(array(
                        'conditions' => 'id = ?1',
                        'bind'       => array(1 => self::$session->getUserId())
                    ));
                    if (self::$user instanceof User) {
                        if (self::$user->getLastUpdate() === null) {
                            $di = $this->getDI();
                            try {
                                $di->getShared('eventsManager')->fire('sso:lastUserUpdateEmpty', $this, self::$user);
                            } catch (\Exception $ex) {
                                self::$user = false;
                            }
                        }
                    } else {
                        self::$user = false;
                    }
                }
            } else {
                throw new PhalconSSOException('General error : can\t retrieve session !', ErrorCodes::ERROR_GENERAL);
            }
        }
        
        return self::$user;
    }

    /**
     * Get Broker
     *
     * @return \PhalconSSO\Models\Broker
     */
    protected function getBroker ()
    {
        return self::$broker;
    }

    /**
     *
     * @param unknown $p_sess_id
     */
    protected function touchSession ($p_sess_id)
    {
        $mySession = SSOSession::findFirst($p_sess_id);
        if ($mySession instanceof SSOSession) {
            $mySession
                ->setTouch(FWDate::getServerDatetime())
                ->setEnd(FWDate::getServerDatetime(60*24))
            ;
            $mySession->update();
        }
        $sql = 'DELETE FROM \PhalconSSO\Models\Session WHERE [end] < :end:';
        $query = new Query($sql, $this->getDI());
        $query->execute(array(
            'end' => FWDate::getServerDatetime()
        ));
    }

    /**
     *
     * @param unknown $p_key
     * @param unknown $p_ssoId
     * @param unknown $p_appId
     * @param unknown $p_ip
     */
    protected function verifyBrokerSession ($p_key, $p_ssoId, $p_appId, $p_ip)
    {
        $addNewBrokerSession = true;
        // Read if application session exists
        $myBrokerSession = BrokerSession::findFirst(array(
            'conditions' => 'token = ?1',
            'bind'       => array(1 => $p_appId)
        ));
        if ($myBrokerSession instanceof BrokerSession) {
            $addNewBrokerSession = false;
            // Must be for the same IP
            if ($myBrokerSession->getClientAddress() != $p_ip) {
                $myBrokerSession->delete();
                $addNewBrokerSession = true;
            } else {
                // Must be the same SSO id
                if ($myBrokerSession->getSessionId() != $p_ssoId) {
                    $myBrokerSession->delete();
                    $addNewBrokerSession = true;
                } else {
                    // Expired, delete ?
                    if (strtotime($myBrokerSession->getEnd()) < time()) {
                        $myBrokerSession->delete();
                        $addNewBrokerSession = true;
                    } else {
                        $myBrokerSession->setEnd(FWDate::getServerDatetime(SSOCST::BROKER_SESSION_LIFETIME));
                        $myBrokerSession->update();
                        // Need to touch the session too...
                        $this->touchSession($myBrokerSession->getSessId());
                        self::$session = SSOSession::findFirst($myBrokerSession->getSessId());
                    }
                }
            }
        }
        if ($addNewBrokerSession) {
            // First, is there a session for the same SSO id ?
            $myBrokerSession = BrokerSession::findFirst(array(
                'conditions' => 'session_id = ?1',
                'bind'       => array(1 => $p_ssoId)
            ));
            self::$session = null;
            if ($myBrokerSession instanceof BrokerSession) {
                if ($myBrokerSession->getClientAddress() == $p_ip) {
                    // We share the same session
                    self::$session = SSOSession::findFirst($myBrokerSession->getSessId());
                }
            }
            if (!self::$session instanceof SSOSession) {
                self::$session = new SSOSession();
                self::$session
                    ->setStart(FWDate::getServerDatetime())
                    ->setClientAddr($p_ip)
                ;
                self::$session->create();
            }
            $myBrokerSession = new BrokerSession();
            $myBrokerSession
                ->setBrkKey($p_key)
                ->setToken($p_appId)
                ->setSessionId($p_ssoId)
                ->setClientAddress($p_ip)
                ->setSessId(self::$session->getId())
                ->setEnd(FWDate::getServerDatetime(SSOCST::BROKER_SESSION_LIFETIME))
            ;
            $myBrokerSession->create();
            $sql = 'DELETE FROM \PhalconSSO\Models\BrokerSession WHERE [end] < :end:';
            $query = new Query($sql, $this->getDI());
            $query->execute(array(
                'end' => FWDate::getServerDatetime()
            ));
        }
    }

    /**
     * Return private key
     *
     * @return string | boolean
     */
    public function getPrivateKey ()
    {
        if (self::$broker instanceof \PhalconSSO\Models\Broker) {
            return self::$broker->getCertificate();
        }
        
        return false;
    }

    /**
     * Get password token and email
     *
     * @param string $p_login
     *
     * @throws PhalconSSOException
     */
    public function getUserPasswordToken ($p_login)
    {
        $user = User::findFirst(array(
            'conditions' => 'login = ?1',
            'bind'       => array(1 => $p_login)
        ));
        if ($user instanceof User) {
            // First delete olders
            $olders = PasswordToken::find(array(
                'conditions' => 'user_id = ?1 AND used = 0',
                'bind'       => array(1 => $user->getId())
            ));
            foreach ($olders as $oneToken) {
                if (!$oneToken->delete()) {
                    
                    return false;
                }
            }
            // New one
            $data          = array();
            $token         = md5(uniqid(microtime(true)));
            $data['email'] = $user->getEmail();
            $data['token'] = $token;
            $pToken        = new PasswordToken();
            $pToken
                ->setToken($token)
                ->setEmail($user->getEmail())
                ->setUserId($user->getId())
                ->setRequestIp('')
                ->setEnd(FWDate::getServerDatetime(60))
            ;
            if ($pToken->create()) {
                
                return $data;
            }
            
        } else {
            throw new PhalconSSOException (sprintf('Login %s doesn\'t exists !', $p_login), ErrorCodes::ERROR_LOGIN_NOTFOUND);
        }
        
        return false;
    }

    /**
     * Verify password token and return user
     *
     * @param string $p_token
     *
     * @return false | User
     */
    public function getUserFromPasswordToken ($p_token)
    {
        $token = PasswordToken::findFirst(array(
            'conditions' => 'token = ?1 AND used = 0 AND end > ?2',
            'bind'       => array(1 => $p_token, 2 => FWDate::getServerDatetime())
        ));
        if ($token instanceof PasswordToken) {
            $user = User::findFirst(array(
                'conditions' => 'id = ?1',
                'bind'       => array(1 => $token->getUserId())
            ));
            if ($user instanceof User) {
                
                return $user;
            }
        }
        
        return false;
    }

    /**
     * Get a user with its id
     *
     * @param string $p_id
     *
     * @return \PhalconSSO\Models\User|boolean
     */
    public function getUserById ($p_id)
    {
        $user = User::findFirst(array(
            'conditions' => 'id = ?1',
            'bind'       => array(1 => $p_id)
        ));
        if ($user instanceof User) {
            if ($user->getLastUpdate() === null) {
                $di = $this->getDI();
                try {
                    $di->getShared('eventsManager')->fire('sso:lastUserUpdateEmpty', $this, $user);
                } catch (\Exception $ex) {
                    $user = false;
                }
            }
        }
        
        return false;
    }

    /**
     * Get a user with its login
     *
     * @param string $p_login
     *
     * @return \PhalconSSO\Models\User|boolean
     */
    public function getUserByLogin ($p_login)
    {
        $user = User::findFirst(array(
            'conditions' => 'login = ?1',
            'bind'       => array(1 => $p_login)
        ));
        if ($user instanceof User) {
            if ($user->getLastUpdate() === null) {
                $di = $this->getDI();
                try {
                    $di->getShared('eventsManager')->fire('sso:lastUserUpdateEmpty', $this, $user);
                } catch (\Exception $ex) {
                    $user = false;
                }
            }
        }
        
        return false;
    }

    /**
     * Change user password with its password token
     *
     * @param string $p_token
     * @param string $p_password
     *
     * @throws PhalconSSOException
     */
    public function changeUserPasswordFromToken ($p_token, $p_password)
    {
        if (false !== ($user = $this->getUserFromPasswordToken($p_token))) {
            $user->setPassword(md5($user->getSalt() . $p_password));
            $user->update();
            $token = PasswordToken::findFirst(array(
                'conditions' => 'token = ?1',
                'bind'       => array(1 => $p_token)
            ));
            if ($token instanceof PasswordToken) {
                $token->setUsed(1);
                
                return $token->update();
            }
        }
        throw new PhalconSSOException (sprintf('Token %s doesn\'t exists !', $p_token), ErrorCodes::ERROR_TOKEN_NOTFOUND);
    }

    /**
     * Change user password
     *
     * @param User   $p_user
     * @param string $p_old
     * @param string $p_password
     *
     * @throws PhalconSSOException
     */
    public function changeUserPassword ($p_user, $p_old, $p_password)
    {
        $oldCalc = md5($p_user->getSalt() . $p_old);
        if ($p_user->getPassword() == $oldCalc) {
            $p_user->setPassword(md5($user->getSalt() . $p_password));
            $p_user->update();
            
            return $p_user;
        } else {
            
        }
        throw new PhalconSSOException ('Wrong old password !', ErrorCodes::ERROR_PASSWORD_WRONG);
    }

    /**
     * Register new user
     *
     * @param string  $p_login
     * @param string  $p_email
     * @param string  $p_password
     * @param array   $p_datas
     * @param boolean $p_withValidation
     *
     * @throws PhalconSSOException
     *
     * @return \PhalconSSO\Models\User|boolean
     */
    public function registerNewUser ($p_login, $p_email, $p_password, $p_datas = array(), $p_withValidation = true)
    {
        $user = User::findFirst(array(
            'conditions' => 'login = ?1 OR val_login = ?2',
            'bind'       => array(1 => $p_login, 2 => $p_login)
        ));
        if ($user instanceof User) {
            throw new PhalconSSOException (sprintf('User with login %s allready exists !', $p_login), ErrorCodes::ERROR_LOGIN_EXISTS);
        }
        $user = new User();
        $user
            ->setId(md5(uniqid()))
            ->setLogin($p_login)
            ->setEmail($p_email)
            ->setSalt(md5(uniqid()))
            ->setPassword($user->getSalt() . $p_password)
            ->setActive(1)
            ->setType(User::TYPE_USER)
        ;
        $user = $this->updateUserFields($p_user, $p_datas);
        if ($user->save()) {
            $di = $this->getDI();
            try {
                $di->getShared('eventsManager')->fire('sso:afterRegisterNewUser', $this, $user);
            } catch (\Exception $ex) {
                var_dump($ex);die;
            }
            
            return $user;
        }
        
        return false;
    }

    /**
     *
     * @param unknown $p_user
     * @param unknown $p_datas
     */
    public function updateUser ($p_user, $p_datas = array())
    {
        $p_user = $this->updateUserFields($p_user, $p_datas);
        if ($p_user->save()) {
            $di = $this->getDI();
            try {
                $di->getShared('eventsManager')->fire('sso:afterUpdateUser', $this, $p_user);
            } catch (\Exception $ex) {
                var_dump($ex);die;
            }
        
            return $p_user;
        }
        
        return false;
    }

    /**
     * Send a validation email
     *
     * @param PhalconSOO\Models\User $p_user
     *
     * @return boolean
     */
    public function sendValidationEmail ($p_user)
    {
        $p_user
            ->setValString(md5(uniqid('valid')))
            ->setValEnd(FWDate::getServerDatetime(60*24*2))
        ;
        if ($p_user->save()) {
            // Send email
            
            return true;
        }
        
        return false;
    }

    /**
     * Sets users avatar
     *
     * @param unknown $p_userId
     * @param unknown $p_avatar
     *
     * @throws PhalconSSOException
     *
     * @return \PhalconSSO\Models\User|boolean
     */
    public function setUserAvatar ($p_userId, $p_avatar)
    {
        $user = User::findFirst(array(
            'conditions' => 'id = ?1',
            'bind'       => array(1 => $p_userId)
        ));
        if (!$user instanceof User) {
            throw new PhalconSSOException (sprintf('User with login %s allready exists !', $p_login), ErrorCodes::ERROR_LOGIN_EXISTS);
        }
        $user->setAvatar($p_avatar);
        if ($user->save()) {
            
            return $user;
        }
        
        return false;
    }

    /**
     * Update user fields
     *
     * @param User  $p_user
     * @param array $p_datas
     *
     * @return User
     */
    protected function updateUserFields ($p_user, $p_datas = array())
    {
        foreach ($p_datas as $key => $value) {
            switch (strtolower($key)) {
                case 'title' :
                    $p_user->setTitle($value);
                    break;
                case 'firstname' :
                    $p_user->setFirstName($value);
                    break;
                case 'lastname' :
                    $p_user->setLastName($value);
                    break;
                default: {
                    $p_user->putInCache($key, $value);
                    break;
                }
            }
        }
        
        return $p_user;
    }

}