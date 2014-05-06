<?php

namespace Towel\MVC\Controller;

use \Towel\MVC\Model\User as ModelUser;

class User extends BaseController
{
    public function profileShow()
    {
        if (!$this->isAuthenticated()) {
            return $this->redirect('/login');
        }

        $userModel = $this->session()->set('user');
        return $this->twig()->render('User\profile.twig', array('user' => $userModel));
    }

    public function loginShow()
    {
        if ($this->isAuthenticated()) {
            $this->setMessage('warning', 'you are already loggedin');
            return $this->redirect('/');
        }

        return $this->twig()->render('user\login.twig');
    }

    public function loginAction($data)
    {
        $userModel = new ModelUser();
        $validUser = $userModel->validateLogin($data['email'], $data['password']);

        if (!$validUser) {
            $this->setMessage('error', 'Not valid user / password combination');
            return $this->redirect('/login');
        }

        $this->session()->set('user', $userModel->record);
        $this->sessionClearMessages();
        return $this->redirect('/');
    }

    public function logoutAction()
    {
        if ($this->isAuthenticated()) {
            $this->session()->set('user', null);
        }

        $this->setMessage('success', 'Bye Bye !');
        return $this->redirect('/');
    }

    public function registerShow()
    {
        return $this->twig()->render('user\register.twig');
    }

    public function registerAction($data)
    {
        $modelUser = new ModelUser();

        if ($this->isAuthenticated()) {
            $this->setMessage('error', 'You are logged in');
            return $this->redirect('/user/register');
        }

        if (!filter_var($data['model']['email'], FILTER_VALIDATE_EMAIL)) {
            $this->setMessage('error', 'That is not an Email');
            return $this->redirect('/user/register');
        }

        if ($modelUser->findByName($data['model']['username'])) {
            $this->setMessage('error', 'Your account exists');
            return $this->redirect('/user/register');
        }

        $modelUser->resetObject();
        $modelUser->username = $data['model']['username'];
        $modelUser->password = md5($data['model']['password']);
        $modelUser->email = $data['model']['email'];
        $modelUser->save();
        $this->setMessage('success', 'Your was created.');
        return $this->twig()->render('user\registerAction.twig');
    }

    public function recoverShow()
    {
        return $this->twig()->render('User\recover.twig');
    }

    public function recoverAction($data)
    {
        $modelUser = new ModelUser;

        if ($this->isAuthenticated()) {
            $this->setMessage('error', 'Your are already authenticated.');
            return $this->redirect('/');
        }

        if (!filter_var($data['model']['email'], FILTER_VALIDATE_EMAIL)) {
            $this->setMessage('error', 'Thats is not an email');
            return $this->redirect('/user/recover');
        }

        if (!$modelUser->findByEmail($data['model']['email'])) {
            $this->setMessage('error', 'Your are not registered');
            return $this->redirect('/user/register');
        }

        //Send the email
        $password = $modelUser->regeneratePassword();
        $to = $modelUser->email;
        $subject = 'Password from Reader';
        $message = 'Your new password is ' . $password;
        $headers = 'From: ' . APP_SYS_EMAIL;
        mail($to, $subject, $message, $headers);

        return $this->twig()->render('user\recoverAction.twig');
    }
}

