<?php
declare(strict_types=1);

use Phalcon\Config;

return new Config([
    'privateResources' => [
        'dashboard' => ['index'],
        'changeHistory' => ['index'],
        'users' => ['index', 'create', 'edit', 'delete', 'authorization'],
        'roles' => ['index', 'create', 'edit', 'delete', 'editPermission']
    ]
]);