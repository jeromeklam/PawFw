<?php
namespace PhalconSSO\Models;

/**
 *
 * @author jeromeklam
 *
 */
class BaseModel extends \PhalconFW\Models\BaseModel
{

    public function initialize()
    {
        $this->setConnectionService(\PhalconSSO\Constants::SSO_CNX);
    }

}