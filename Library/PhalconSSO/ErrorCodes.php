<?php
namespace PhalconSSO;

class ErrorCodes
{

    /**
     * 
     */
    const ERROR_GENERAL                          = 66600000;

    /**
     * Options
     * @var number
     */
    const ERROR_OPTIONS_BROKER_KEY_REQUIRED      = 66600001;
    const ERROR_OPTIONS_BROKER_SECRET_REQUIRED   = 66600002;
    const ERROR_OPTIONS_DB_CNX_REQUIRED          = 66600003;

    /**
     * 
     * @var number
     */
    const ERROR_LOGIN_NOTFOUND                   = 66610001;
    const ERROR_PASSWORD_WRONG                   = 66610002;
    const ERROR_USER_DEACTIVATED                 = 66610003;
    const ERROR_TOKEN_NOTFOUND                   = 66610004;
    const ERROR_LOGIN_EXISTS                     = 66610005;

}