<?php
declare(strict_types=1);

namespace PSA\Models;

use Phalcon\Mvc\Model;

/**
 * PasswordChanges
 * Register when a user changes his/her password
 */
class PasswordChanges extends Model
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
    public $userID;

    /**
     *
     * @var string
     */
    public $ipAddress;

    /**
     *
     * @var string
     */
    public $userAgent;

    /**
     *
     * @var integer
     */
    public $createdAt;

    public function initialize()
    {
        // Audit
        $this->keepSnapshots(true);
        $this->addBehavior(new \PSA\Models\Blameable());

        $this->belongsTo('userID', Users::class, 'id', [
            'alias' => 'user',
        ]);
    }
}
