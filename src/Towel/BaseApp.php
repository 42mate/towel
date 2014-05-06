<?php

namespace Towel;

use Towel\MVC\Model\User;

class BaseApp
{

    public $database = 'default';
    public $app;
    public $appConfig;

    /**
     * Makes available app and config as part of the
     * object to avoid the use of globals.
     */
    public function __construct()
    {
        global $app;
        $this->app = $app;
        global $appConfig;
        $this->appConfig = $appConfig;
    }

    public function config()
    {
        return $this->appConfig;
    }

    /**
     * Gets twig engine.
     * @return \Twig_Environment
     */
    public function twig()
    {
        return $this->app['twig'];
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
        return $this->app['dbs'][$database];
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
        return $this->app['session'];
    }

    /**
     * Gets the current user or false if is not autenticated.
     *
     * @returns \Frontend\Model\User or false
     */
    public function getCurrentUser()
    {
        $userRecord = $this->session()->get('user', false);

        if (!$userRecord) {
            return false;
        }

        $userModel = $this->getInstance('user');
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

    public function getInstance($classMapName, $args = array()) {
        $config = $this->config();
        $className = $config['class_map'][$classMapName];
        $class = new \ReflectionClass($className);
        return $class->newInstance($args);
    }

}