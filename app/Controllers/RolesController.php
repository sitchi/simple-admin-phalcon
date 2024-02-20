<?php
declare(strict_types=1);

namespace PSA\Controllers;

use Phalcon\Http\Response;
use Phalcon\Tag;
use PSA\Forms\RolesForm;
use PSA\Helpers\Datatables;
use PSA\Services\BreadcrumbService;
use PSA\Services\Roles\RolesService;

class RolesController extends ControllerBase
{
    protected RolesService $rolesService;
    protected BreadcrumbService $breadcrumbService;

    public function initialize(): void
    {
        $this->view->setTemplateBefore('private');
        $this->tag->title()->set('Roles');
        $this->rolesService = $this->getDI()->get(RolesService::class);
        $this->breadcrumbService = $this->getDI()->get(BreadcrumbService::class);
    }

    public function indexAction(): void
    {
        $this->view->css = (new Datatables)->css();
        $this->view->js = (new Datatables)->jsData();
        $this->assets->addJs('js/roles/index.js');

        $this->view->breadcrumbs = $this->breadcrumbService->generate([
            ['url' => '/dashboard', 'icon' => 'fas fa-fw fa-tachometer-alt', 'title' => 'Dashboard'],
            ['url' => '/', 'icon' => 'fas fa-layer-group', 'title' => 'Roles'],
        ]);

        $this->view->roles = $this->rolesService->getAllRoles();
    }

    /**
     * Creates a new role.
     *
     * @return \Phalcon\Http\ResponseInterface The response object.
     */
    public function createAction(): \Phalcon\Http\ResponseInterface
    {
        $form = new RolesForm(null);
        if ($this->request->isPost()) {
            if (!$form->isValid($this->request->getPost())) {
                foreach ($form->getMessages() as $message) {
                    $this->flashSession->error((string)$message);
                }
                return $this->response->redirect('/roles/create');
            }

            $postData = [
                'name' => $this->request->getPost('name', 'striptags'),
                'active' => $this->request->getPost('active')
            ];

            if ($this->rolesService->createRole($postData)) {
                $this->acl->rebuild();
                $this->flashSession->success("Role was created successfully");
            } else {
                $this->flashSession->error("An error has occurred");
            }
            return $this->response->redirect('/roles');
        }

        $this->view->breadcrumbs = $this->breadcrumbService->generate([
            ['url' => '/dashboard', 'icon' => 'fas fa-fw fa-tachometer-alt', 'title' => 'Dashboard'],
            ['url' => '/roles', 'icon' => 'fas fa-layer-group', 'title' => 'Roles'],
        ]);

        $this->view->form = $form;
    }

    /**
     * Edits a role.
     *
     * @param int $id The ID of the role to edit.
     * @return \Phalcon\Http\ResponseInterface The response object.
     */
    public function editAction($id): \Phalcon\Http\ResponseInterface
    {
        $role = $this->rolesService->getRoleById($id);
        if (!$role) {
            $this->flashSession->error("Role was not found");
            return $this->response->redirect('/roles');
        }
        $form = new RolesForm($role);
        if ($this->request->isPost()) {
            if (!$form->isValid($this->request->getPost())) {
                foreach ($form->getMessages() as $message) {
                    $this->flashSession->error((string)$message);
                }
            } else {
                $postData = [
                    'name' => $this->request->getPost('name', 'striptags'),
                    'active' => $this->request->getPost('active')
                ];
                if ($this->rolesService->updateRole($role, $postData)) {
                    $this->acl->rebuild();
                    $this->flashSession->success("Role was updated successfully");
                    return $this->response->redirect('/roles');
                } else {
                    $this->flashSession->error("An error has occurred");
                }
            }
            return $this->response->redirect('/roles/edit/' . $id);
        }
        // Breadcrumbs
        $this->view->breadcrumbs = $this->breadcrumbService->generate([
            ['url' => '/dashboard', 'icon' => 'fas fa-fw fa-tachometer-alt', 'title' => 'Dashboard'],
            ['url' => '/roles', 'icon' => 'fas fa-layer-group', 'title' => 'Roles'],
            ['url' => '/roles/edit/' . $id, 'icon' => 'fas fa-edit', 'title' => 'Edit']
        ]);
        $this->view->form = $form;
        $this->view->role = $role;
    }

    public function editPermissionAction($id)
    {
        $role = $this->rolesService->getRoleById($id);
        if (!$role) {
            $this->flashSession->error("Role was not found");
            return $this->response->redirect('/roles');
        }

        if ($this->request->isPost()) {
            // Deletes the current permissions
            $role->getPermissions()->delete();
            // Save the new permissions
            foreach ($this->request->getPost('permissions') as $permission) {
                $parts = explode('.', $permission);
                $permission = new Permissions();
                $permission->roleID = $role->id;
                $permission->resource = $parts[0];
                $permission->action = $parts[1];
                $permission->save();
            }
            $this->acl->rebuild();
            $this->flashSession->success('Permissions were updated with success');
            return $this->response->redirect('/roles');
        }
        // Breadcrumbs
        $this->view->breadcrumbs = "
        <li class='breadcrumb-item'><a href='/dashboard'><i class='fas fa-fw fa-tachometer-alt'></i> Dashboard</a></li>
        <li class='breadcrumb-item'><a href='/roles'><i class='fas fa-layer-group'></i> roles</a></li>
        <li class='breadcrumb-item active'><i class='fas fa-edit'></i> Edit</li>
        ";
        // Rebuild the ACL with
        $this->acl->rebuild();
        // Pass the current permissions to the view
        $this->view->permissions = $this->acl->getPermissions($role);
        $this->view->role = $role;
    }

    /**
     * Deletes a Role
     *
     * @param int $id
     */
    public function deleteAction($id)
    {
        if ($this->request->getPost('delete')) {
            $role = Roles::findFirstById($id);
            if (!$role) {
                $this->flashSession->error("Role was not found");
                return $this->response->redirect('/roles');
            }
            // check csrf
            if (!$this->security->checkToken($this->security->getTokenKey(), $this->request->getPost('csrf'))) {
                $this->flashSession->error('CSRF validation failed');
                return $this->response->redirect('/roles');
            }

            if (!$role->delete()) {
                if ($role->getMessages()) {
                    foreach ($role->getMessages() as $message) {
                        $this->flashSession->error((string)$message);
                    }
                } else {
                    $this->flashSession->error("An error has occurred");
                }
            } else {
                $this->acl->rebuild();
                $this->flashSession->success("Role was deleted");
            }
            return $this->response->redirect('/roles');
        }

        $this->view->disable();
        $resData = "Oops! Something went wrong. Please try again later.";
        //Create a response instance
        $response = new Response();
        $response->setStatusCode(400, "Bad Request");

        if ($this->request->isPost() && $this->request->isAjax()) {
            $form = new RolesForm();
            $resData = '<form method="post" action="/roles/delete/' . $id . '">';
            $resData .= '<div class="modal-body">';
            $resData .= '<label>Are you sure you want to delete the role?!</label>';
            $resData .= '</div>';
            $resData .= '<div class="modal-footer">';
            $resData .= Tag::submitButton(['name' => 'delete', 'class' => 'btn btn btn-danger btn-sm', 'value' => 'Delete']);
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
