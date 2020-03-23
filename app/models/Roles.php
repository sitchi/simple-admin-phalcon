<?php
declare(strict_types=1);

namespace PSA\Models;

use Phalcon\Mvc\Model;
use Phalcon\Mvc\Model\Relation;

/**
 * Roles
 * All the roles levels in the application. Used in conjenction with ACL lists
 */
class Roles extends Model
{
    /**
     * ID
     *
     * @var integer
     */
    public $id;

    /**
     * Name
     *
     * @var string
     */
    public $name;

    /**
     * Active
     *
     * @var int
     */
    public $active;

    /**
     * Define relationships to Users and Permissions
     */
    public function initialize()
    {
        // Audit
        $this->keepSnapshots(true);
        $this->addBehavior(new \PSA\Models\Blameable());

        $this->hasMany('id', Users::class, 'roleID', [
            'alias'      => 'users',
            'foreignKey' => [
                'message' => 'Profile cannot be deleted because it\'s used on Users',
            ],
        ]);

        $this->hasMany('id', Permissions::class, 'roleID', [
            'alias'      => 'permissions',
            'foreignKey' => [
                'action' => Relation::ACTION_CASCADE,
            ],
        ]);
    }
}
