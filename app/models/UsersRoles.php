<?php
declare(strict_types=1);

namespace PSA\Models;

use Phalcon\Mvc\Model;

class UsersRoles extends Model
{

    /**
     *
     * @var integer
     */
    public $id;

    /**
     *
     * @var integer
     */
    public $roleID;

    /**
     *
     * @var integer
     */
    public $userID;

    /**
     * Initialize method for model.
     */
    public function initialize()
    {
        // Audit
        $this->keepSnapshots(true);
        $this->addBehavior(new \PSA\Models\Blameable());

        $this->hasOne('roleID', Roles::class, 'id', [
            'alias' => 'role',
            'reusable' => true,
        ]);

        $this->hasOne('userID', Users::class, 'id', [
            'alias'    => 'user',
            'reusable' => true,
        ]);
    }


}
