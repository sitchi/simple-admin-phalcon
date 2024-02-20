<?php
declare(strict_types=1);

/**
 * Namespace for PSA Controllers
 */

namespace PSA\Controllers;

use PSA\Forms\LoginForm;
use PSA\Forms\SignUpForm;
use PSA\Forms\ForgotPasswordForm;
use PSA\Auth\Exception as AuthException;
use PSA\Models\Users;
use PSA\Models\ResetPasswords;
use PSA\Models\UsersRoles;

/**
 * Class IndexController
 *
 * This class extends ControllerBase and is responsible for handling the index page.
 */
class IndexController extends ControllerBase
{
    /**
     * This method is called on every request and sets the template before rendering the view.
     * It sets the template to 'public' and the title of the page to 'Simple Admin'.
     * If the user is already logged in, it redirects them to the dashboard.
     */
    public function initialize()
    {
        $this->view->setTemplateBefore('public');
        $this->tag->title()->set('Simple Admin');
        if ($this->auth->getIdentity()) {
            $this->response->redirect('dashboard');
        }
    }

    /**
     * This method is responsible for handling the index action of the index page.
     * It creates a new login form and checks if the form is valid when a post request is made.
     * If the form is valid, it attempts to authenticate the user and redirects them to the previous page.
     * If the authentication fails, it displays an error message.
     */
    public function indexAction()
    {
        $form = new LoginForm();
        if ($this->request->isPost() && $form->isValid($this->request->getPost())) {
            try {
                $this->auth->check([
                    'email' => $this->request->getPost('email'),
                    'password' => $this->request->getPost('password'),
                    'remember' => $this->request->getPost('remember')
                ]);
                $this->response->redirect($this->request->getHTTPReferer());
            } catch (AuthException $e) {
                $this->flash->error($e->getMessage());
            }
        }
        $this->view->form = $form;
    }

    /**
     * This method is responsible for handling the signup action of the index page.
     * It creates a new signup form and checks if the form is valid when a post request is made.
     * If the form is valid, it creates a new user and saves it to the database.
     * If the user is saved successfully, it creates a new user role and saves it to the database.
     * If the user role is saved successfully, it displays a success message and redirects the user to the index page.
     * If the user or user role is not saved successfully, it displays an error message.
     */
    public function signupAction()
    {
        $form = new SignUpForm();
        if ($this->request->isPost() && $form->isValid($this->request->getPost())) {
            $user = new Users([
                'name' => $this->request->getPost('name', 'striptags'),
                'email' => $this->request->getPost('email'),
                'password' => $this->security->hash($this->request->getPost('password')),
            ]);
            if ($user->save()) {
                $usersRoles = new UsersRoles;
                $usersRoles->roleID = $user->id == 1 ? 1 : 2;
                $usersRoles->userID = $user->id;
                if ($usersRoles->save()) {
                    $this->flashSession->success("User was created successfully");
                    return $this->response->redirect('/');
                }
            }
            $this->flashSession->error((string)$user->getMessages()[0]);
        }
        $this->view->setVar('form', $form);
    }

    /**
     * This method is responsible for handling the forgot password action of the index page.
     * It creates a new forgot password form and checks if the form is valid when a post request is made.
     * If the form is valid, it finds the user by email and creates a new reset password record for the user.
     * If the reset password record is saved successfully, it displays a success message.
     * If the reset password record is not saved successfully, it displays an error message.
     * If the user is not found, it displays a success message.
     */
    public function forgotPasswordAction(): void
    {
        $form = new ForgotPasswordForm();
        if ($this->request->isPost() && $form->isValid($this->request->getPost())) {
            $user = Users::findFirstByEmail($this->request->getPost('email'));
            if ($user) {
                $resetPassword = new ResetPasswords();
                $resetPassword->userID = $user->id;
                if ($resetPassword->save()) {
                    $this->flash->success('Success! Please check your messages for an email reset password');
                } else {
                    $this->flash->error((string)$resetPassword->getMessages()[0]);
                }
            } else {
                $this->flash->success('There is no account associated to this email');
            }
        }
        $this->view->setVar('form', $form);
    }

    /**
     * This method is responsible for handling the logout action of the index page.
     * It removes the user's authentication and redirects them to the index page.
     */
    public function logoutAction()
    {
        $this->auth->remove();
        return $this->response->redirect('/');
    }
}