<?php
declare(strict_types=1);

namespace PSA\Controllers;

use PSA\Models\EmailConfirmations;
use PSA\Models\PasswordChanges;
use PSA\Models\ResetPasswords;
use PSA\Models\Users;

/**
 * UserControlController
 * Provides help to users to confirm their passwords or reset them
 */
class UserControlController extends ControllerBase
{
    public function initialize(): void
    {
        if ($this->session->has('auth-identity')) {
            $this->view->setTemplateBefore('private');
        }
    }

    public function indexAction()
    {
        // If there is no identity available the user is redirected to index/index
        if (!$this->auth->getIdentity()) {
            $this->flash->notice('You don\'t have access to this module: private');

            return $this->dispatcher->forward([
                'controller' => 'index',
                'action' => 'index',
            ]);
        }
        $this->tag->setTitle('Profile');
    }

    /**
     * Confirms an e-mail, if the user must change their password then changes
     * it
     */
    public function confirmEmailAction()
    {
        $code = $this->dispatcher->getParam('code');

        /** @var EmailConfirmations|false $confirmation */
        $confirmation = EmailConfirmations::findFirstByCode($code);
        if (!$confirmation instanceof EmailConfirmations) {
            return $this->dispatcher->forward([
                'controller' => 'index',
                'action' => 'index',
            ]);
        }

        if ($confirmation->confirmed != 0) {
            return $this->dispatcher->forward([
                'controller' => 'dashboard',
                'action' => 'index',
            ]);
        }

        /**
         * Activate user
         */
        $user = Users::findFirst($confirmation->user->id);
        $user->active = 1;
        if (!$user->save()) {
            foreach ($confirmation->user->getMessages() as $message) {
                $this->flash->error((string)$message);
            }

            return $this->dispatcher->forward([
                'controller' => 'index',
                'action' => 'index',
            ]);
        }

        /**
         * Change the confirmation to 'confirmed' and update the user to 'active'
         */
        $confirmation->confirmed = 1;
        if (!$confirmation->save()) {
            foreach ($confirmation->getMessages() as $message) {
                $this->flash->error((string)$message);
            }

            return $this->dispatcher->forward([
                'controller' => 'index',
                'action' => 'index',
            ]);
        }

        /**
         * Identify the user in the application
         */
        $this->auth->authUserById($confirmation->user->id);

        /**
         * Check if the user must change his/her password
         */
        if ($confirmation->user->mustChangePassword == 1) {
            $this->flash->success('The email was successfully confirmed. Now you must change your password');

            return $this->dispatcher->forward([
                'controller' => 'users',
                'action' => 'changePassword',
            ]);
        }

        $this->flash->success('The email was successfully confirmed');

        return $this->dispatcher->forward([
            'controller' => 'users',
            'action' => 'index',
        ]);
    }

    public function resetPasswordAction()
    {
        $code = $this->dispatcher->getParam('code');

        /** @var ResetPasswords|false $resetPassword */
        $resetPassword = ResetPasswords::findFirstByCode($code);
        if (!$resetPassword instanceof ResetPasswords) {
            return $this->dispatcher->forward([
                'controller' => 'index',
                'action' => 'index',
            ]);
        }

        if ($resetPassword->reset != 0) {
            return $this->dispatcher->forward([
                'controller' => 'dashboard',
                'action' => 'index',
            ]);
        }

        $resetPassword->reset = 1;

        /**
         * Change the confirmation to 'reset'
         */
        if (!$resetPassword->save()) {
            foreach ($resetPassword->getMessages() as $message) {
                $this->flash->error((string)$message);
            }

            return $this->dispatcher->forward([
                'controller' => 'index',
                'action' => 'index',
            ]);
        }

        /**
         * Identify the user in the application
         */
        $this->auth->authUserById($resetPassword->userID);

        $this->flash->success('Please reset your password');

        return $this->dispatcher->forward([
            'controller' => 'users',
            'action' => 'changePassword',
        ]);
    }

    /**
     * Users must use this action to change its password
     */
    public function changePasswordAction()
    {
        // If there is no identity available the user is redirected to index/index
        if (!$this->auth->getIdentity()) {
            $this->flash->notice('You don\'t have access to this module: private');

            return $this->dispatcher->forward([
                'controller' => 'index',
                'action' => 'index',
            ]);
        }

        $form = new \PSA\Forms\ChangePasswordForm();

        if ($this->request->isPost()) {
            if (!$form->isValid($this->request->getPost())) {
                foreach ($form->getMessages() as $message) {
                    $this->flash->error((string)$message);
                }
            } else {
                $user = $this->auth->getUser();

                $user->password = $this->security->hash($this->request->getPost('password'));
                $user->mustChangePassword = 0;

                $passwordChange = new PasswordChanges();
                $passwordChange->userID = $user->id;
                $passwordChange->ipAddress = $this->request->getClientAddress();
                $passwordChange->userAgent = $this->request->getUserAgent();

                if (!$passwordChange->save() or !$user->save()) {
                    $this->flash->error((string)$passwordChange->getMessages());
                } else {
                    $this->flashSession->success("Your password was successfully changed");
                    return $this->response->redirect('/');
                }
            }
        }
        // Breadcrumbs
        $this->view->breadcrumbs = "
        <li class='breadcrumb-item'><a href='/dashboard'><i class='fas fa-fw fa-tachometer-alt'></i> Dashboard</a></li>
        <li class='breadcrumb-item active'><i class='fas fa-key'></i> Change Password</li>
        ";
        $this->view->form = $form;

        $this->tag->setTitle('Change Password');
    }
}
