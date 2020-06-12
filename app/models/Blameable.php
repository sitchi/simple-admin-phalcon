<?php
declare(strict_types=1);

namespace PSA\Models;

use Phalcon\Mvc\ModelInterface;
use Phalcon\Mvc\Model\Behavior;

class Blameable extends Behavior
{
    public function notify(string $eventType, ModelInterface $model)
    {
        switch ($eventType) {
            case 'beforeCreate':
                return $this->auditBeforeCreate($model, $eventType);
                break;
            case 'beforeUpdate':
                return $this->auditBeforeUpdate($model, $eventType);
                break;
            case 'beforeDelete':
                return $this->auditBeforeDelete($model, $eventType);
                break;
            default:
                /* ignore the rest of events */
        }
    }

    private function deleteElement($element, &$array)
    {
        $index = array_search($element, $array);
        if ($index !== false) {
            unset($array[$index]);
        }
    }

    public function auditBeforeCreate(ModelInterface $model, $eventType)
    {
        $metaData = $model->getModelsMetaData();
        $changedFields = $metaData->getAttributes($model);
        $this->deleteElement('createdAt', $changedFields);
        $this->deleteElement('updatedAt', $changedFields);
        if (count($changedFields)) {
            //Transactions
            $manager = new \Phalcon\Mvc\Model\Transaction\Manager();
            $transaction = $manager->get();

            $audit = new Audit();
            // Set Transactions
            $audit->setTransaction($transaction);
            // Get the request service
            $request = $model->getDI()->getRequest();

            $audit->valueID = reset($model);

            // Get the user id
            $user = new \PSA\Auth\Auth();
            $audit->userID = $user->getIdentity()->id;

            // The model who performed the action
            $audit->modelName = str_replace('PSA\Models\\', '', get_class($model));

            // The client IP address
            $audit->ipAddress = $request->getClientAddress();

            // Action is an update
            $audit->action = str_replace('before', '', $eventType);

            // Current datetime
            $audit->createdAt = date('Y-m-d H:i:s');

            // save audit
            if (!$audit->save()) {
                $transaction->rollback();
                return false;
                exit;
            }

            // get audit id
            $auditID = $audit->id;

            // Get the original data before modification
            $originalData = $model->getSnapshotData();

            $details = [];
            foreach ($changedFields as $field) {
                if ($model->$field != null) {
                    $auditDetail = new AuditDetail();
                    // Set Transactions
                    $auditDetail->setTransaction($transaction);
                    $auditDetail->auditID = $auditID;
                    $auditDetail->fieldName = $field;
                    $auditDetail->oldValue = !isset($originalData[$field]) ? null : $originalData[$field];
                    $auditDetail->newValue = $model->$field;
                    if (!$auditDetail->save()) {
                        $transaction->rollback();
                        return false;
                        exit;
                    }
                }
            }
            $transaction->commit();
            return true;
        }

        return null;
    }

    public function auditBeforeUpdate(ModelInterface $model, $eventType)
    {
        // Get the name of the fields that have changed
        $changedFields = $model->getChangedFields();
        $this->deleteElement('updatedAt', $changedFields);
        if (count($changedFields)) {
            //Transactions
            $manager = new \Phalcon\Mvc\Model\Transaction\Manager();
            $transaction = $manager->get();

            $audit = new Audit();
            // Set Transactions
            $audit->setTransaction($transaction);
            // Get the request service
            $request = $model->getDI()->getRequest();

            $audit->valueID = reset($model);

            // Get the user id
            $user = new \PSA\Auth\Auth();
            $audit->userID = $user->getIdentity()->id;

            // The model who performed the action
            $audit->modelName = str_replace('PSA\Models\\', '', get_class($model));

            // The client IP address
            $audit->ipAddress = $request->getClientAddress();

            // Action is an update
            $audit->action = str_replace('before', '', $eventType);

            // Current datetime
            $audit->createdAt = date('Y-m-d H:i:s');

            // save audit
            if (!$audit->save()) {
                $transaction->rollback();
                return false;
                exit;
            }

            // get audit id
            $auditID = $audit->id;

            // Get the original data before modification
            $originalData = $model->getSnapshotData();

            $details = [];
            foreach ($changedFields as $field) {
                if ($originalData[$field] != $model->$field) {
                    $auditDetail = new AuditDetail();
                    // Set Transactions
                    $auditDetail->setTransaction($transaction);
                    $auditDetail->auditID = $auditID;
                    $auditDetail->fieldName = $field;
                    $auditDetail->oldValue = $originalData[$field];
                    $auditDetail->newValue = $model->$field;
                    if (!$auditDetail->save()) {
                        $transaction->rollback();
                        return false;
                        exit;
                    }
                }
            }
            $transaction->commit();
            return true;
        }

        return null;
    }

    public function auditBeforeDelete(ModelInterface $model, $eventType)
    {
        $audit = new Audit();
        // Get the request service
        $request = $model->getDI()->getRequest();

        $audit->valueID = reset($model);

        // Get the user id
        $user = new \PSA\Auth\Auth();
        $audit->userID = $user->getIdentity()->id;

        // The model who performed the action
        $audit->modelName = str_replace('PSA\Models\\', '', get_class($model));

        // The client IP address
        $audit->ipAddress = $request->getClientAddress();

        // Action is an update
        $audit->action = str_replace('before', '', $eventType);

        // Current datetime
        $audit->createdAt = date('Y-m-d H:i:s');

        // save audit
        return $audit->save();
    }
}
