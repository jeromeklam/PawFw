<?php
namespace PhalconTech\Models;

/**
 *
 * @author jeromeklam
 *
 */
class BaseModel extends \PhalconFW\Models\BaseModel
{

    public function initialize()
    {
        $this->setConnectionService(\PhalconTech\Constants::TECH_CNX);
    }

}