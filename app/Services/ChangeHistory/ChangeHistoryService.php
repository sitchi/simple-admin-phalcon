<?php

namespace PSA\Services\ChangeHistory;

use PSA\Models\AuditDetail;

class ChangeHistoryService
{
    private $modelsManager;

    public function __construct($modelsManager)
    {
        $this->modelsManager = $modelsManager;
    }

    public function historyDetail($id)
    {
        $auditDetails = $this->getAuditDetails($id);
        return $this->generateHtmlTable($auditDetails);
    }

    private function getAuditDetails($id)
    {
        return AuditDetail::find([
            'conditions' => 'auditID = :id:',
            'bind' => [
                'id' => $id
            ]
        ]);
    }

    private function generateHtmlTable($auditDetails)
    {
        $resData = '<table class="table table-hover"><thead><tr><th>Name</th><th>old value</th><th>new value</th></tr></thead><tbody>';
        foreach ($auditDetails as $value) {
            $resData .= '<tr><th>' . $value->fieldName . '</th><th>' . $value->oldValue . '</th><th>' . $value->newValue . '</th></tr>';
        }
        $resData .= '</tbody></table>';
        return $resData;
    }

    public function dataTablesAjax($request)
    {
        $columns = [
            0 => 'id',
            1 => 'date',
            2 => 'model',
            3 => 'action',
            4 => 'valueID',
            5 => 'ip',
            6 => 'user',
        ];

        $builder = $this->getBuilder();
        $totalRecords = count($builder->getQuery()->execute());

        if (!empty($request['search']['value'])) {
            $builder = $this->applySearchFilters($builder, $request['search']['value']);
            $totalRecords = count($builder->getQuery()->execute());
        }

        $builder->orderBy($columns[$request['order'][0]['column']] . " " . $request['order'][0]['dir']);
        $builder->limit($request['length'], $request['start']);

        $result = $builder->getQuery()->execute();
        $data = $this->formatData($result);

        return [
            "draw" => intval($request['draw']),
            "recordsTotal" => intval($totalRecords),
            "recordsFiltered" => intval($totalRecords),
            "data" => $data
        ];
    }

    private function getBuilder()
    {
        return $this->modelsManager->createBuilder()
            ->columns(['id' => 'au.id', 'adID' => 'ad.id', 'date' => 'au.createdAt', 'model' => 'au.modelName', 'action' => 'au.action', 'valueID' => 'au.valueID', 'ip' => 'au.ipAddress', 'user' => 'user.name'])
            ->addFrom('PSA\Models\Audit', 'au')
            ->leftJoin('PSA\Models\AuditDetail', 'au.id = ad.auditID', 'ad')
            ->leftJoin('PSA\Models\Users', 'au.userID = user.id', 'user')
            ->groupBy('au.id');
    }

    private function applySearchFilters($builder, $searchValue)
    {
        $builder->where('id LIKE :value:', ['value' => '%' . $searchValue . '%'])
            ->orWhere('date LIKE :value:', ['value' => '%' . $searchValue . '%'])
            ->orWhere('model LIKE :value:', ['value' => '%' . $searchValue . '%'])
            ->orWhere('action LIKE :value:', ['value' => '%' . $searchValue . '%'])
            ->orWhere('valueID LIKE :value:', ['value' => '%' . $searchValue . '%'])
            ->orWhere('ip LIKE :value:', ['value' => '%' . $searchValue . '%'])
            ->orWhere('user LIKE :value:', ['value' => '%' . $searchValue . '%']);

        return $builder;
    }

    private function formatData($result)
    {
        $formattedData = [];

        foreach ($result as $value) {
            $noKey = [];
            $noKey[] = $value->id;
            $noKey[] = $value->date;
            $noKey[] = $value->model;
            $noKey[] = $value->action;
            $noKey[] = $value->valueID;
            $noKey[] = $value->ip;
            $noKey[] = ($value->user) ? $value->user : 'System';
            $disabled = ($value->adID) ? null : 'disabled';
            $noKey[] = '<div class="btn-group" role="group" aria-label="">
            <a href="#" data-toggle="modal" data-target="#detail" onclick="detail(' . $value->id . ')" class="btn btn-primary btn-sm ' . $disabled . '"><i class="fas fa-eye"></i></a>
            </div>';
            $formattedData[] = $noKey;
        }

        return $formattedData;
    }
}