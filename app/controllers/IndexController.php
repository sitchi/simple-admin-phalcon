<?php
declare(strict_types=1);

namespace PSA\Controllers;

use PSA\Controllers\ControllerBase;
use PSA\Forms\LoginForm;
use PSA\Forms\SignUpForm;
use PSA\Forms\ForgotPasswordForm;
use PSA\Auth\Exception as AuthException;
use PSA\Models\Users;
use PSA\Models\ResetPasswords;

/**
 * Display the default index page.
 */
class IndexController extends ControllerBase
{

    /**
     * Default action. Set the public layout (layouts/public.volt)
     */
    public function initialize()
    {
        $this->view->setTemplateBefore('public');
        $this->tag->setTitle('Simple Admin');
        // check auth users
        if ($this->auth->getIdentity()) {
            $this->response->redirect('dashboard');
        }
    }

    public function indexAction()
    {
        $form = new LoginForm();
        try {
            if (!$this->request->isPost()) {
                if ($this->auth->hasRememberMe()) {
                    return $this->auth->loginWithRememberMe();
                }
            } else {
                if ($form->isValid($this->request->getPost()) == false) {
                    foreach ($form->getMessages() as $message) {
                        $this->flash->error((string)$message);
                    }
                } else {
                    $this->auth->check([
                        'email' => $this->request->getPost('email'),
                        'password' => $this->request->getPost('password'),
                        'remember' => $this->request->getPost('remember')
                    ]);

                    $this->response->redirect($this->request->getHTTPReferer());
                }
            }
        } catch (AuthException $e) {
            $this->flash->error($e->getMessage());
        }

        $this->view->form = $form;
    }

    /**
     * Allow a user to signup to the system
     */
    public function signupAction()
    {
        $form = new \PSA\Forms\SignUpForm();

        if ($this->request->isPost()) {
            if ($form->isValid($this->request->getPost()) == false) {
                foreach ($form->getMessages() as $message) {
                    $this->flash->error((string)$message);
                }
            } else {
                $this->db->begin();
                // create user
                $user = new Users([
                    'name' => $this->request->getPost('name', 'striptags'),
                    'email' => $this->request->getPost('email'),
                    'password' => $this->security->hash($this->request->getPost('password')),
                ]);

                if (!$user->save()) {
                    $this->db->rollback();
                    foreach ($user->getMessages() as $message) {
                        $this->flashSession->error((string)$message);
                    }
                    return $this->response->redirect('/admins/create');
                }
                $userID = $user->id;

                // create role
                $usersRoles = new \PSA\Models\UsersRoles;
                if ($userID == 1) {
                    $usersRoles->roleID = 1;
                } else {
                    $usersRoles->roleID = 2;
                }
                $usersRoles->userID = $userID;
                if (!$usersRoles->save()) {
                    $this->db->rollback();
                    foreach ($usersRoles->getMessages() as $message) {
                        $this->flashSession->error((string)$message);
                    }
                }
                $this->db->commit();
                $this->flashSession->success("User was created successfully");
                return $this->response->redirect('/');
            }
        }

        $this->view->setVar('form', $form);
    }

    /**
     * Shows the forgot password form
     */
    public function forgotPasswordAction(): void
    {
        $form = new ForgotPasswordForm();

        if ($this->request->isPost()) {
            // Send emails only is config value is set to true
            if ($this->getDI()->get('config')->useMail) {
                if ($form->isValid($this->request->getPost()) == false) {
                    foreach ($form->getMessages() as $message) {
                        $this->flash->error((string)$message);
                    }
                } else {
                    $user = Users::findFirstByEmail($this->request->getPost('email'));
                    if (!$user) {
                        $this->flash->success('There is no account associated to this email');
                    } else {
                        $resetPassword = new ResetPasswords();
                        $resetPassword->userID = $user->id;
                        if ($resetPassword->save()) {
                            $this->flash->success('Success! Please check your messages for an email reset password');
                        } else {
                            foreach ($resetPassword->getMessages() as $message) {
                                $this->flash->error((string)$message);
                            }
                        }
                    }
                }
            } else {
                $this->flash->warning(
                    'Emails are currently disabled. Change config key "useMail" to true to enable emails.'
                );
            }
        }

        $this->view->setVar('form', $form);
    }


    /**
     * Closes the session
     */
    public function logoutAction()
    {
        $this->auth->remove();

        return $this->response->redirect('/');
    }
}
