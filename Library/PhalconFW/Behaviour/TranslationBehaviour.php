<?php

namespace PhalconFW\Behaviour;

use PhalconFW\Behaviour\DIBehaviour;
use Phalcon\DI;

/**
 *
 * @author jeromeklam
 *
 */
trait TranslationBehaviour
{

    /**
     * Translate message.
     *
     * @param string     $p_msg  Message to translate.
     * @param array|null $p_args Message placeholder values.
     *
     * @return string
     */
    protected function _ ($p_msg, $p_args = null)
    {
        $translator = $this->getDI()->get('i18n');
        
        return $translator->_($p_msg, $p_args);
    }

}