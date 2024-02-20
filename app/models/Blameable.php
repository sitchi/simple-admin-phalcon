<?php
declare(strict_types=1);

namespace PSA\Models;

use Phalcon\Mvc\Model\Transaction\Manager;
use Phalcon\Mvc\ModelInterface;
use Phalcon\Mvc\Model\Behavior;
use PSA\Auth\Auth;

class Blameable extends Behavior
{
    public function notify(string $eventType, ModelInterface $model)
    {
        if (method_exists($this, $method = 'audit' . ucfirst($eventType))) {
            return $this->$method($model);
        }
    }

    private function deleteElement($element, &$array)
    {
        $index = array_search($element, $array);
        if ($index !== false) {
            unset($array[$index]);
        }
    }

    private function createAudit(ModelInterface $model, $eventType, $changedFields)
    {
        $manager = new Manager();
        $transaction = $manager->get();

        $audit = new Audit();
        $audit->setTransaction($transaction);
        $request = $model->getDI()->getRequest();

        $audit->valueID = array_values(get_object_vars($model))[0];
        $user = new Auth();
        $audit->userID = $user->getIdentity()->id;
        $audit->modelName = str_replace('PSA\Models\\', '', get_class($model));
        $audit->ipAddress = $request->getClientAddress();
        $audit->action = str_replace('before', '', $eventType);
        $audit->createdAt = date('Y-m-d H:i:s');

        if (!$audit->save()) {
            $transaction->rollback();
            return false;
        }

        $auditID = $audit->id;
        $originalData = $model->getSnapshotData();

        foreach ($changedFields as $field) {
            if ($model->$field != null) {
                $auditDetail = new AuditDetail();
                $auditDetail->setTransaction($transaction);
                $auditDetail->auditID = $auditID;
                $auditDetail->fieldName = $field;
                $auditDetail->oldValue = !isset($originalData[$field]) ? null : $originalData[$field];
                $auditDetail->newValue = $model->$field;
                if (!$auditDetail->save()) {
                    $transaction->rollback();
                    return false;
                }
            }
        }
        $transaction->commit();
        return true;
    }

    public function auditBeforeCreate(ModelInterface $model)
    {
        $metaData = $model->getModelsMetaData();
        $changedFields = $metaData->getAttributes($model);
        $this->deleteElement('createdAt', $changedFields);
        $this->deleteElement('updatedAt', $changedFields);
        return count($changedFields) ? $this->createAudit($model, 'beforeCreate', $changedFields) : null;
    }

    public function auditBeforeUpdate(ModelInterface $model)
    {
        $changedFields = $model->getChangedFields();
        $this->deleteElement('updatedAt', $changedFields);
        return count($changedFields) ? $this->createAudit($model, 'beforeUpdate', $changedFields) : null;
    }

    public function auditBeforeDelete(ModelInterface $model)
    {
        $audit = new Audit();
        $request = $model->getDI()->getRequest();

        $audit->valueID = array_values(get_object_vars($model))[0];
        $user = new Auth();
        $audit->userID = $user->getIdentity()->id;
        $audit->modelName = str_replace('PSA\Models\\', '', get_class($model));
        $audit->ipAddress = $request->getClientAddress();
        $audit->action = str_replace('before', '', 'beforeDelete');
        $audit->createdAt = date('Y-m-d H:i:s');

        return $audit->save();
    }
}