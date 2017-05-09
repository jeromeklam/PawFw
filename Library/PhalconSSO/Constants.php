<?php
namespace PhalconSSO;

class Constants
{

    /**
     * Brokers, ...
     * @var mixed
     */
    const BROKER_KEY              = 'broker-key';
    const BROKER_SECRET           = 'broker-secret';
    const BROKER_SESSION_LIFETIME = 60; // Minutes

    /**
     * Connexions
     * @var unknown
     */
    const SSO_CNX       = 'sso-cnx';

    /**
     * Cookies default values
     * @var mixed
     */
    const COOKIE_DEFAULT_DOMAIN = 'deboeck.com';
    const COOKIE_CDSSO          = 'SSO-ID';
    const COOKIE_APP            = 'SSO-APP';
    const COOKIE_APP_DAYS       = 360; // Days

    /**
     * Other constants
     * @var mixed
     */
    const SESSION_LIFETIME        = 50; // Seconds

}