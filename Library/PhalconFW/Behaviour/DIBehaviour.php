<?php
namespace PhalconFW\Behaviour;

use Phalcon\DI;
use Phalcon\DiInterface;

/**
 * 
 * @author jeromeklam
 *
 */
trait DIBehaviour
{

    /**
     * Dependency injection container.
     * @var DIBehaviour|DI
     */
    private $_di;

    /**
     * Create object.
     *
     * @param DiInterface|DIBehaviour $p_di Dependency injection container.
     */
    public function __construct ($p_di = null)
    {
        if ($p_di == null) {
            $p_di = DI::getDefault();
        }
        $this->setDI($p_di);
    }

    /**
     * Set DI.
     *
     * @param DiInterface $p_di
     *
     * @return void
     */
    public function setDI ($p_di)
    {
        $this->_di = $p_di;
    }

    /**
     * Get DI.
     *
     * @return DIBehaviour|DI
     */
    public function getDI ()
    {
        return $this->_di;
    }

    /**
     * static one
     */
    public static function getStaticDI ()
    {
        return DI::getDefault();
    }

}