<?php

namespace PSA\Services\Roles;

use PSA\Models\Roles;

class RolesService
{
    public function getRoleById($id)
    {
        return Roles::findFirstById($id);
    }

    public function getAllRoles(): \Phalcon\Mvc\Model\ResultsetInterface
    {
        return Roles::find();
    }

    public function createRole($postData): bool
    {
        $role = new Roles([
            'name' => $postData['name'],
            'active' => $postData['active']
        ]);

        if ($role->save()) {
            return true;
        } else {
            return false;
        }
    }

    public function updateRole($role, $postData): bool
    {
        $role->assign($postData);

        if ($role->save()) {
            return true;
        } else {
            return false;
        }
    }

    public function deleteRole($id): bool
    {
        $role = Roles::findFirstById($id);

        if (!$role) {
            return false;
        }

        if ($role->delete()) {
            return true;
        } else {
            return false;
        }
    }
}