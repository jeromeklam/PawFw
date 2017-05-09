<?php
namespace PhalconFW\Mvc\View\Engine;

use \Phalcon\Mvc\View\Engine\Volt as VoltEngine;

class Volt extends VoltEngine
{

    public function getCompiler()
    {
        if (empty($this->_compiler))
        {
            parent::getCompiler();
        }
    
        return parent::getCompiler();
    }

}