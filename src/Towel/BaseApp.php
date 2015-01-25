<?php

namespace Towel;

use Towel\Cache\Cache as TowelCache;

class BaseApp
{

    public $database = 'default';
    public $silex;
    public $appConfig;
    static $instance = null;
    static $cache = null;
    /**
     * Makes available app and config as part of the
     * object to avoid the use of globals.
     */
    public function __construct()
    {
        global $silex;
        $this->silex = $silex;
        global $appConfig;
        $this->appConfig = $appConfig;
    }

    /**
     * Gets the config Array.
     *
     * @return array
     */
    public function config()
    {
        return $this->appConfig;
    }

    /**
     * Gets twig engine.
     *
     * @return \Twig_Environment
     */
    public function twig()
    {
        return $this->silex['twig'];
    }

    /**
     * Gets dbal engine.
     *
     * @param string $database :  Database to use, check config to see available sources.
     *                    default is the default source.
     *
     * @return \Doctrine\DBAL\Connection
     */
    public function db($database = null)
    {
        if (empty($database)) {
            $database = $this->database;
        }
        return $this->silex['dbs'][$database];
    }

    /**
     * Gets the silex application.
     *
     * @return \Silex\Application
     */
    public function silex() {
        return $this->silex;
    }

    /**
     * Sets in which database is going to be executed the query.
     *
     * @param $database
     */
    public function setTargetDatabase($database)
    {
        $this->database = $database;
    }

    /**
     * Gets the Session.
     *
     * @return mixed
     */
    public function session()
    {
        return $this->silex['session'];
    }

    /**
     * Gets the current user or false if is not authenticated.
     *
     * @returns \Towel\Model\BaseModel
     */
    public function getCurrentUser()
    {
        $userRecord = $this->session()->get('user', false);
        if (!$userRecord) {
            return false;
        }

        $userModel = $this->getInstance('user_model');
        $userModel->setRecord($userRecord);

        return $userModel;
    }

    /**
     * Checks if the User is authenticated.
     *
     * @return Boolean
     */
    public function isAuthenticated()
    {
        $user = $this->getCurrentUser();
        return !empty($user);
    }

    /**
     * Gets an instance of the given class name.
     *
     * @param $classMapName : Class name.
     * @param array $args : Args for the constructor
     * @param $singleton : To get Singleton Classes.
     *
     * @return object
     */
    public function getInstance($classMapName, $args = array(), $singleton = false)
    {
        return get_instance($classMapName, $args, $singleton);
    }

    /**
     * Run the application.
     */
    public function run() {
        $this->silex()->run();
    }

    /**
     * Sends an Email.
     *
     * @param $to
     * @param string $subject
     * @param string $message
     * @param string $headers
     */
    public function sendMail($to, $subject = '', $message = '', $headers = '')
    {
        if (empty($headers)) {
            $headers = 'From: ' . APP_SYS_EMAIL;
        }
        mail($to, $subject, $message, $headers);
    }

    /**
     * Generates a path from the given parameters.
     *
     * @param string $route      The name of the route
     * @param mixed  $parameters An array of parameters
     *
     * @return string The generated path
     */
    public function path($route, $parameters = array())
    {
        return $this->silex['url_generator']->generate($route, $parameters, \Symfony\Component\Routing\Generator\UrlGeneratorInterface::ABSOLUTE_PATH);
    }

    /**
     * Generates an absolute URL from the given parameters.
     *
     * @param string $route      The name of the route
     * @param mixed  $parameters An array of parameters
     *
     * @return string The generated URL
     */
    public function url($route, $parameters = array())
    {
        return $this->silex['url_generator']->generate($route, $parameters, \Symfony\Component\Routing\Generator\UrlGeneratorInterface::ABSOLUTE_URL);
    }

    /**
     * Instantiate Cache driver
     * @return \Towel\Cache\CacheInterface
     */
    public function getCache()
    {
        if (null === self::$cache) {
            if (!empty($this->appConfig['cache']['driver'])) {
                // Options definition.
                if (empty($this->appConfig['cache']['options'])) {
                    $options = array();
                } else {
                    $options = $this->appConfig['cache']['options'];
                }

                // Driver definition.
                if ('memcached' !== $this->appConfig['cache']['driver']) {
                    $className = $this->appConfig['cache']['driver'];
                } else {
                    $className = '\Towel\Cache\TowelMemcached';
                }

                // Driver instantiation
                $cacheDriver = new $className();
                $cacheDriver->setOptions($options);
            }

            self::$cache = new TowelCache($cacheDriver);
        }

        return self::$cache;
    }

    /**
     * Returns the Silex File System Loader.
     *
     * return Twig_Loader_Filesystem
     */
    public function getTwigLoader()
    {
        return $this->silex['twig.loader.filesystem'];
    }

    /**
     * Generates a machine readable slug of the string.
     *
     * @param $string
     *
     * @return String A machine readable string.
     */
    public function sluggify($string)
    {
        $human_name = strtolower($string);
        $human_name = str_replace('á', 'a', $human_name);
        $human_name = str_replace('é', 'e', $human_name);
        $human_name = str_replace('í', 'i', $human_name);
        $human_name = str_replace('ó', 'o', $human_name);
        $human_name = str_replace('ú', 'u', $human_name);
        $human_name = str_replace('ñ', 'n', $human_name);
        return preg_replace(array(
                '/[^a-zA-Z0-9]+/',
                '/-+/',
                '/^-+/',
                '/-+$/',
            ), array('-', '-', '', ''), $human_name);
    }
}