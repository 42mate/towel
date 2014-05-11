<?php

namespace Towel;

class BaseApp
{

    public $database = 'default';
    public $silex;
    public $appConfig;
    static $instance = null;

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
     * @returns \Frontend\Model\User or false
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
    public function getInstance($classMapName, $args = array(), $singleton = false) {
        static $objects = array();

        if (empty($objects[md5($classMapName)])) {
            $config = $this->config();
            $className = $config['class_map'][$classMapName];
            $class = new \ReflectionClass($className);
            $object = $class->newInstance($args);

            if ($singleton) {
                $objects[$classMapName] = $object;
            }
        } else {
            $object = $objects[md5($classMapName)];
        }

        return $object;
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
    public function sendMail($to, $subject = '', $message = '', $headers = '') {
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
        return $this['url_generator']->generate($route, $parameters, \Symfony\Component\Routing\Generator\UrlGeneratorInterface::ABSOLUTE_URL);
    }

}