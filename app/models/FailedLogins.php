<?php
declare(strict_types=1);

namespace PSA\Models;

use Phalcon\Mvc\Model;

/**
 * This model registers unsuccessfully logins registered and non-registered
 * users have made
 */
class FailedLogins extends Model
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
     * @var integer
     */
    public $attempted;

    public function initialize()
    {
        $this->belongsTo('userID', Users::class, 'id', [
            'alias' => 'user',
        ]);
    }
}
