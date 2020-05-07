<?php
declare(strict_types=1);

namespace PSA\Controllers;

use PSA\Models\Users;

/**
 * UsersController
 * CRUD to manage users
 */
class UsersController extends ControllerBase
{
    public function initialize()
    {
        $this->view->setTemplateBefore('private');
        $this->tag->setTitle('Users');
    }

    /**
     * Default action
     */
    public function indexAction()
    {
        // css and javascript
        $datatable = new \PSA\Helpers\Datatables;
        $this->view->css = $datatable->css();
        $js = $datatable->jsData();
        $js .= "<script type='text/javascript' language='javascript'>
        function deleteUser(id) {
            $.post('/users/delete/' + id, function(data){
                $('#modal-delete').html(data);
            })
        }
        </script>";
        $this->view->js = $js;
        // Breadcrumbs
        $this->view->breadcrumbs = "
        <li class='breadcrumb-item'><a href='/dashboard'><i class='fas fa-fw fa-tachometer-alt'></i> Dashboard</a></li>
        <li class='breadcrumb-item active'><i class='fas fa-user-secret'></i> Users</li>
        ";
        $users = Users::find();
        $this->view->users = $users;
    }


    /**
     * Default action
     */
    public function authorizationAction($id)
    {
        $user = Users::findFirstById($id);
        if (!$user) {
            $this->flash->error("User was not found.");
            return $this->dispatcher->forward([
                'action' => 'index'
            ]);
        }
        // css and javascript
        $datatable = new \PSA\Helpers\Datatables;
        $this->view->css = $datatable->css();
        $this->view->js = $datatable->jsData();
        // Breadcrumbs
        $this->view->breadcrumbs = "
        <li class='breadcrumb-item'><a href='/dashboard'><i class='fas fa-fw fa-tachometer-alt'></i> Dashboard</a></li>
        <li class='breadcrumb-item'><a href='/users'><i class='fas fa-user-secret'></i> Users</a></li>
        <li class='breadcrumb-item active'><i class='fas fa-sign-in-alt'></i> Authorizations</li>
        ";
        $this->view->user = $user;
    }

    /**
     * Creates a Admin
     */
    public function createAction()
    {
        $form = new \PSA\Forms\UsersForm();

        if ($this->request->isPost()) {
            if ($form->isValid($this->request->getPost()) == false) {
                foreach ($form->getMessages() as $message) {
                    $this->flashSession->error((string)$message);
                }
                return $this->response->redirect('/users/create');
            } else {
                $this->db->begin();
                // save user info
                $user = new Users([
                    'name' => $this->request->getPost('name', 'striptags'),
                    'email' => $this->request->getPost('email', 'email'),
                    'password' => $this->security->hash($this->request->getPost('password')),
                ]);

                if (!$user->save()) {
                    $this->db->rollback();
                    foreach ($user->getMessages() as $message) {
                        $this->flashSession->error((string)$message);
                    }
                    return $this->response->redirect('/users/create');
                }
                $userID = $user->id;

                // create role
                $rolesID = $this->request->getPost('rolesID', 'int');
                foreach ($rolesID as $value) {
                    $UsersRoles = new \PSA\Models\UsersRoles;
                    $UsersRoles->roleID = $value;
                    $UsersRoles->userID = $userID;
                    if (!$UsersRoles->save()) {
                        $this->db->rollback();
                        foreach ($UsersRoles->getMessages() as $message) {
                            $this->flashSession->error((string)$message);
                        }
                        return $this->response->redirect('/users/create');
                    }
                }
                $this->db->commit();
                $this->flashSession->success("User was created successfully");
            }
            return $this->response->redirect('/users');
        }
        $cssBefore = "<link href='/css/select2.min.css' rel='stylesheet' type='text/css'>";
        $js = "<script src='/js/select2.full.min.js'></script>";
        $js .= "<script type='text/javascript' language='javascript'>
        $('.select2').select2();
        </script>";
        $this->view->cssBefore = $cssBefore;
        $this->view->js = $js;
        // Breadcrumbs
        $this->view->breadcrumbs = "
        <li class='breadcrumb-item'><a href='/dashboard'><i class='fas fa-fw fa-tachometer-alt'></i> Dashboard</a></li>
        <li class='breadcrumb-item'><a href='/users'><i class='fas fa-user-secret'></i> Users</a></li>
        <li class='breadcrumb-item active'><i class='fas fa-plus-circle'></i> Create</li>
        ";
        $this->view->form = $form;
    }

    /**
     * Saves the user from the 'edit' action
     */
    public function editAction($id)
    {
        $user = Users::findFirstById($id);

        if (!$user) {
            $this->flash->error("User was not found.");
            return $this->dispatcher->forward([
                'action' => 'index'
            ]);
        }
        $newPassword = $this->request->getPost('newPassword');
        $form = new \PSA\Forms\UsersForm($user, ['edit' => 1, 'newPassword' => $newPassword]);
        if ($this->request->isPost()) {
            if ($form->isValid($this->request->getPost()) == false) {
                foreach ($form->getMessages() as $message) {
                    $this->flashSession->error((string)$message);
                }
            } else {
                $this->db->begin();
                // Save Roles
                $rolesID = $this->request->getPost('rolesID', 'int');
                $currentRoles = $user->userRolesID($id);

                // delete old role
                $deleteData = array_diff($currentRoles, $rolesID);
                foreach ($deleteData as $value) {
                    $UsersRoles = \PSA\Models\UsersRoles::findFirst("roleID = '$value' AND userID='$id'");
                    if (!$UsersRoles->delete()) {
                        $this->db->rollback();
                        foreach ($UsersRoles->getMessages() as $message) {
                            $this->flashSession->error((string)$message);
                        }
                        return $this->response->redirect('/users/edit/' . $id);
                    }
                }
                // create role
                foreach ($rolesID as $value) {
                    $UsersRoles = \PSA\Models\UsersRoles::findFirst("roleID = '$value' AND userID='$id'");
                    if (!$UsersRoles) {
                        $UsersRoles = new \PSA\Models\UsersRoles;
                        $UsersRoles->roleID = $value;
                        $UsersRoles->userID = $id;
                    }
                    if (!$UsersRoles->save()) {
                        $this->db->rollback();
                        foreach ($UsersRoles->getMessages() as $message) {
                            $this->flashSession->error((string)$message);
                        }
                        return $this->response->redirect('/users/edit/' . $id);
                    }
                }
                // save user info
                $user->name = $this->request->getPost('name', 'striptags');
                $user->email = $this->request->getPost('email', 'email');
                if ($newPassword) {
                    $user->password = $this->security->hash($newPassword);
                }
                $user->active = $this->request->getPost('active');
                if ($user->save()) {
                    $this->db->commit();
                    $this->flashSession->success("User was updated successfully.");
                    return $this->response->redirect('/users/edit/' . $id);
                } else {
                    $this->db->rollback();
                    foreach ($user->getMessages() as $message) {
                        $this->flashSession->error((string)$message);
                    }
                    return $this->response->redirect('/users/edit/' . $id);
                }
            }
            return $this->response->redirect('/users/edit/' . $id);
        }

        $cssBefore = "<link href='/css/select2.min.css' rel='stylesheet' type='text/css'>";
        $js = "<script src='/js/select2.full.min.js'></script>";
        $js .= "<script type='text/javascript' language='javascript'>
        $('.select2').select2({
            tags: true
        });
        </script>";
        $this->view->cssBefore = $cssBefore;
        $this->view->js = $js;
        // Breadcrumbs
        $this->view->breadcrumbs = "
        <li class='breadcrumb-item'><a href='/dashboard'><i class='fas fa-fw fa-tachometer-alt'></i> Dashboard</a></li>
        <li class='breadcrumb-item'><a href='/users'><i class='fas fa-user-secret'></i> Users</a></li>
        <li class='breadcrumb-item active'><i class='fas fa-edit'></i> Edit</li>
        ";
        $this->view->user = $user;
        $this->view->form = $form;
    }

    /**
     * Deletes a Admin
     *
     * @param int $id
     */
    public function deleteAction($id)
    {
        if ($this->request->getPost('delete')) {
            $user = Users::findFirstById($id);
            if (!$user) {
                $this->flashSession->error("Role was not found");
                return $this->response->redirect('/roles');
            }
            // check csrf
            if (!$this->security->checkToken($this->security->getTokenKey(), $this->request->getPost('csrf'))) {
                $this->flashSession->error('CSRF validation failed');
                return $this->response->redirect('/roles');
            }

            if (!$user->delete()) {
                if ($user->getMessages()) {
                    foreach ($user->getMessages() as $message) {
                        $this->flashSession->error((string)$message);
                    }
                } else {
                    $this->flashSession->error("An error has occurred");
                }
            } else {
                $this->flashSession->success("User was deleted");
            }
            return $this->response->redirect('/users');
        }

        $this->view->disable();
        $resData = "Oops! Something went wrong. Please try again later.";
        //Create a response instance
        $response = new \Phalcon\Http\Response();
        $response->setStatusCode(400, "Bad Request");

        if ($this->request->isPost() && $this->request->isAjax()) {
            $form = new \PSA\Forms\UsersForm();
            $resData = '<form method="post" action="/users/delete/' . $id . '">';
            $resData .= '<div class="modal-body">';
            $resData .= '<label>Are you sure you want to delete the user?!</label>';
            $resData .= '</div>';
            $resData .= '<div class="modal-footer">';
            $resData .= \Phalcon\Tag::submitButton(['name' => 'delete', 'class' => 'btn btn btn-danger btn-sm', 'value' => 'Delete']);
            $resData .= $form->render('id');
            $resData .= $form->render('csrf', ['value' => $form->getCsrf()]);
            $resData .= '</div>';
            $resData .= '</form>';
            $response->setStatusCode(200);
        }
        //Set the content of the response
        $response->setJsonContent($resData);
        $response->send();
        exit;
    }


}
