<?php
declare(strict_types=1);

/**
 * This file is part of the Vökuró.
 *
 * (c) Phalcon Team <team@phalcon.io>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace PSA\Acl;

use Phalcon\Di\Injectable;
use Phalcon\Acl\Adapter\Memory as AclMemory;
use Phalcon\Acl\Component as AclComponent;
use Phalcon\Acl\Enum as AclEnum;
use Phalcon\Acl\Role as AclRole;
use PSA\Models\Roles;

/**
 * PSA\Acl\Acl
 */
class Acl extends Injectable
{
    const APC_CACHE_VARIABLE_KEY = 'ps-acl';

    /**
     * The ACL Object
     *
     * @var \Phalcon\Acl\Adapter\Memory
     */
    private $acl;

    /**
     * The file path of the ACL cache file.
     *
     * @var string
     */
    private $filePath;

    /**
     * Define the resources that are considered "private". These controller =>
     * actions require authentication.
     *
     * @var array
     */
    private $privateResources = [];

    /**
     * Human-readable descriptions of the actions used in
     * {@see $privateResources}
     *
     * @var array
     */
    private $actionDescriptions = [
        'index' => 'Access',
        'create' => 'Create',
        'edit' => 'Edit',
        'delete' => 'Delete',
    ];

    /**
     * Checks if a controller is private or not
     *
     * @param string $controllerName
     *
     * @return boolean
     */
    public function isPrivate($controllerName): bool
    {
        $controllerName = $controllerName;
        return isset($this->privateResources[$controllerName]);
    }

    /**
     * Checks if the current role is allowed to access a resource
     *
     * @param array $roles
     * @param string $controller
     * @param string $action
     *
     * @return boolean
     */
    public function isAllowed($roles, $controller, $action)
    {
        $result = false;
        foreach ($roles as $role) {
            $allow = $this->getAcl()->isAllowed($role, $controller, $action);
            if ($allow) {
                $result = true;
                break;
            }
        }
        return $result;
    }

    /**
     * Returns the ACL list
     *
     * @return \Phalcon\Acl\Adapter\Memory
     */
    public function getAcl()
    {
        // Check if the ACL is already created
        if (is_object($this->acl)) {
            return $this->acl;
        }

        // Check if the ACL is in APC
        if (function_exists('apc_fetch')) {
            $acl = apc_fetch(self::APC_CACHE_VARIABLE_KEY);
            if ($acl !== false) {
                $this->acl = $acl;

                return $acl;
            }
        }

        $filePath = $this->getFilePath();

        // Check if the ACL is already generated
        if (!file_exists($filePath)) {
            $this->acl = $this->rebuild();
            return $this->acl;
        }

        // Get the ACL from the data file
        $data = file_get_contents($filePath);
        $this->acl = unserialize($data);

        // Store the ACL in APC
        if (function_exists('apc_store')) {
            apc_store(self::APC_CACHE_VARIABLE_KEY, $this->acl);
        }

        return $this->acl;
    }

    /**
     * Returns the permissions assigned to a role
     *
     * @param Roles $role
     *
     * @return array
     */
    public function getPermissions(Roles $role)
    {
        $permissions = [];
        foreach ($role->getPermissions() as $permission) {
            $permissions[$permission->resource . '.' . $permission->action] = true;
        }
        return $permissions;
    }

    /**
     * Returns all the resources and their actions available in the application
     *
     * @return array
     */
    public function getResources(): array
    {
        return $this->privateResources;
    }

    /**
     * Returns the action description according to its simplified name
     *
     * @param string $action
     *
     * @return string
     */
    public function getActionDescription($action): string
    {
        return $this->actionDescriptions[$action] ?? $action;
    }

    /**
     * Rebuilds the access list into a file
     *
     * @return AclMemory
     */
    public function rebuild(): AclMemory
    {
        $acl = new AclMemory();
        $acl->setDefaultAction(AclEnum::DENY);

        $roles = Roles::find([
            'active = :active:',
            'bind' => [
                'active' => 1
            ]
        ]);
        foreach ($roles as $role) {
            $acl->addRole(new AclRole($role->name));
        }

        foreach ($this->privateResources as $resource => $actions) {
            $acl->addComponent(new AclComponent($resource), $actions);
        }

        // Grant access to private area to role Users
        foreach ($roles as $role) {
            // Grant permissions in "permissions" model
            foreach ($role->getPermissions() as $permission) {
                $acl->allow($role->name, $permission->resource, $permission->action);
            }
        }

        $filePath = $this->getFilePath();
        if (touch($filePath) && is_writable($filePath)) {
            file_put_contents($filePath, serialize($acl));

            // Store the ACL in APC
            if (function_exists('apc_store')) {
                apc_store(self::APC_CACHE_VARIABLE_KEY, $acl);
            }
        } else {
            $this->flash->error('The user does not have write permissions to create the ACL list at ' . $filePath);
        }

        return $acl;
    }


    /**
     * Set the acl cache file path
     *
     * @return string
     */
    protected function getFilePath()
    {
        if (!isset($this->filePath)) {
            $this->filePath = rtrim($this->config->application->cacheDir, '\\/') . '/acl/data.txt';
        }

        return $this->filePath;
    }

    /**
     * Adds an array of private resources to the ACL object.
     *
     * @param array $resources
     */
    public function addPrivateResources(array $resources)
    {
        if (empty($resources)) {
            return;
        }

        $this->privateResources = array_merge($this->privateResources, $resources);
        if (is_object($this->acl)) {
            $this->acl = $this->rebuild();
        }
    }
}