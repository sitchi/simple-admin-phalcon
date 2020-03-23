<?php
declare(strict_types=1);

namespace PSA\Models;

use Phalcon\Mvc\Model;

class Audit extends Model
{
    public $id;
    public $valueID;
    public $userID;
    public $modelName;
    public $ipAddress;
    public $action;
    public $createdAt;

    public function initialize()
    {
        $this->hasOne('id', __NAMESPACE__ . '\AuditDetail', 'auditID', array(
            'alias' => 'details'
        ));
    }
}
