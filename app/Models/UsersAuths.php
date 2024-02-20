<?php
declare(strict_types=1);

namespace PSA\Models;

use Phalcon\Mvc\Model;

/**
 * UsersAuths
 * This model registers successfully logins registered users have made
 */
class UsersAuths extends Model
{
    /**
     * @var integer
     */
    public $id;

    /**
     * @var integer
     */
    public $userID;

    /**
     * @var string
     */
    public $ipAddress;

    /**
     * @var string
     */
    public $userAgent;

    /**
     * @var dateTime
     */
    public $createdAt;

    public function initialize()
    {
        $this->belongsTo('userID', Users::class, 'id', [
            'alias' => 'user',
        ]);
    }
}
