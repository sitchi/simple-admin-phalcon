<?php
declare(strict_types=1);

namespace PSA\Models;

use Phalcon\Mvc\Model;

class AuditDetail extends Model
{
    public $id;
    public $auditID;
    public $fieldName;
    public $oldValue;
    public $newValue;

    public function initialize()
    {
        $this->hasOne('auditID', __NAMESPACE__ . '\Audit', 'id', [
            'alias' => 'audit',
            'reusable' => true
        ]);
    }
}
