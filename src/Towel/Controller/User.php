<?php

namespace Towel\Controller;

use \Towel\Model\User as ModelUser;

class User extends BaseController
{
    /**
     * Shows the profile page.
     *
     * @return string|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function profileShow()
    {
        if (!$this->isAuthenticated()) {
            return $this->redirect('/login');
        }

        $userModel = $this->session()->set('user');
        return $this->twig()->render('User\profile.twig', array('user' => $userModel));
    }

    /**
     * Shows the login form.
     *
     * @return string|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function loginShow()
    {
        if ($this->isAuthenticated()) {
            $this->setMessage('warning', 'you are already logged in');
            return $this->redirect('/');
        }

        return $this->twig()->render('user\login.twig');
    }

    /**
     * Handles the login action.
     *
     * @param $request
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function loginAction($request)
    {
        $data = $request->get('data');
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

    /**
     * Handles the logout actions.
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function logoutAction()
    {
        if ($this->isAuthenticated()) {
            $this->session()->set('user', null);
        }

        $this->setMessage('success', 'Bye Bye !');
        return $this->redirect('/');
    }

    /**
     * Shows the register form.
     *
     * @return string
     */
    public function registerShow()
    {
        return $this->twig()->render('user\register.twig');
    }

    /**
     * Shows register form.
     *
     * @param $request
     *
     * @return string|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function registerAction($request)
    {
        $modelUser = new ModelUser();
        $data = $request->get('data');
        $error = false;

        if ($this->isAuthenticated()) {
            $this->setMessage('error', 'You are logged in');
            $error = true;
        }

        if (!filter_var($data['model']['email'], FILTER_VALIDATE_EMAIL)) {
            $this->setMessage('error', 'That is not an Email');
            $error = true;
        }

        if ($modelUser->findByName($data['model']['username'])) {
            $this->setMessage('error', 'Your account exists');
            $error = true;
        }

        if ($modelUser->findByEmail($data['model']['email'])) {
            $this->setMessage('error', 'Your Email is already registered');
            $error = true;
        }

        if (empty($data['model']['password'])) {
            $this->setMessage('error', 'You need to select a password');
            $error = true;
        }

        if (empty($data['model']['email'])) {
            $this->setMessage('error', 'You need to select an email');
            $error = true;
        }

        if (empty($data['model']['username'])) {
            $this->setMessage('error', 'You need to select an User Name');
            $error = true;
        }

        if (!$error) {
            $modelUser->resetObject();
            $modelUser->username = $data['model']['username'];
            $modelUser->password = md5($data['model']['password']);
            $modelUser->email = $data['model']['email'];
            $modelUser->save();
            $this->setMessage('success', 'Your was created.');
            return $this->twig()->render('user\registerAction.twig');
        } else {
            return $this->twig()->render('user\register.twig', array(
                'data' => $data
            ));
        }

    }

    /**
     * Shows the recover form.
     *
     * @return string
     */
    public function recoverShow()
    {
        return $this->twig()->render('User\recover.twig');
    }

    /**
     * Handles the recover action.
     *
     * @param $request
     *
     * @return string|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function recoverAction($request)
    {
        $data = $request->get('data');
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
        $this->sendMail($to, $subject, $message);

        return $this->twig()->render('user\recoverAction.twig');
    }
}

