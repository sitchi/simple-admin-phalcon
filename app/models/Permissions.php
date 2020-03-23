<?php
declare(strict_types=1);

namespace PSA\Models;

use Phalcon\Mvc\Model;

/**
 * Permissions
 * Stores the permissions by roles
 */
class Permissions extends Model
{
    /**
     * @var integer
     */
    public $id;

    /**
     * @var integer
     */
    public $roleID;

    /**
     * @var string
     */
    public $resource;

    /**
     * @var string
     */
    public $action;

    public function initialize()
    {
        // Audit
        $this->keepSnapshots(true);
        $this->addBehavior(new \PSA\Models\Blameable());

        $this->belongsTo('roleID', Profiles::class, 'id', [
            'alias' => 'role',
        ]);
    }
}
