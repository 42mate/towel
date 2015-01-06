<?php

namespace Towel\Model;

class User extends BaseModel
{

    public $table = 'app_user';

    /**
     * Validates username and password against the DB.
     * If success sets the user info in the object and return true.
     * If fails returns false.
     *
     * @param String $email
     * @param String $password
     *
     * @return Boolean
     */
    public function validateLogin($email, $password)
    {
        $password = md5($password);

        $result = $this->fetchOne("SELECT * FROM {$this->table} WHERE email = ? AND password = ?",
            array($email, $password)
        );

        if (!empty($result)) {
            return true;
        }

        return false;
    }

    /**
     * Finds a User By Name
     *
     * @param String $username
     *
     * @return \Frontend\Model\User
     */
    public function findByName($username)
    {
        return $this->fetchOne("SELECT * from {$this->table} WHERE username = ?",
            array($username)
        );
    }

    /**
     * Finds a User By Email
     *
     * @param String $email
     *
     * @return \Frontend\Model\User or False
     */
    public function findByEmail($email)
    {
        return $this->fetchOne("SELECT * from {$this->table} WHERE email = ?",
            array($email)
        );
    }

    /**
     * Regenerates and sets the new password for a User.
     *
     * @return String password : The new Password.
     */
    public function regeneratePassword()
    {
        $clean_password = generatePassword(6, 4);
        $password = md5($clean_password);
        $this->db()->executeUpdate("UPDATE {$this->table} SET password = ? WHERE ID = ?",
            array($password, $this->getId()));
        return $clean_password;
    }

}