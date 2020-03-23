<?php

namespace PSA\Controllers;

/**
* ChangeHistoryController
*/
class ChangeHistoryController extends ControllerBase
{

    /**
    * Default action. Set the private (authenticated) layout (layouts/private.volt)
    */
    public function initialize()
    {
        $this->view->setTemplateBefore('private');
        $this->tag->setTitle(' Change History');
    }

    /**
    * Default action
    */
    public function indexAction()
    {
        // css and javascript
        $datatable = new \PSA\Helpers\Datatables;
        $this->view->css = $datatable->css();
        $js =  $datatable->jsAjax('ChangeHistory');
        $js.= "<script type='text/javascript' language='javascript'>
        function detail(id) {
            $.post('/changeHistory', {id: id}, function(data){
                $('#modal-detail').html(data);
            })
        }
        </script>";
        $this->view->js = $js;
        // Breadcrumbs
        $this->view->breadcrumbs = "
        <li class='breadcrumb-item'><a href='/dashboard'><i class='fas fa-fw fa-tachometer-alt'></i> Dashboard</a></li>
        <li class='breadcrumb-item active'><i class='fas fa-history'></i> Change History</li>
        ";

        if ($this->request->isPost() && $this->request->isAjax()) {
            $id =$this->request->getPost('id', 'int');
            $this->view->disable();
            //Create a response instance
            $response = new \Phalcon\Http\Response();
            if ($id!=null) {
                $resData = self::historyDetail($id);
            } else {
                $resData = self::dataTablesAjax($_REQUEST);
            }
            $response->setStatusCode(200);
            //Set the content of the response
            $response->setJsonContent($resData);
            $response->send();
            exit;
        }
    }

    private function dataTablesAjax($request)
    {
        $params = $columns = $totalRecords = $data = [];
        $params = $_REQUEST;
        $columns = array(
            0 => 'id',
            1 => 'date',
            2 => 'model',
            3 => 'action',
            4 => 'valueID',
            5 => 'ip',
            6 => 'user',
        );

        $builder = $this->modelsManager->createBuilder()
        ->columns(['id' => 'au.id', 'adID' => 'ad.id', 'date' => 'au.createdAt', 'model' => 'au.modelName', 'action' => 'au.action', 'valueID' => 'au.valueID', 'ip' => 'au.ipAddress', 'user' => 'user.name'])
        ->addFrom('PSA\Models\Audit', 'au')
        ->leftJoin('PSA\Models\AuditDetail', 'au.id = ad.auditID', 'ad')
        ->leftJoin('PSA\Models\Users', 'au.userID = user.id', 'user')
        ->groupBy('au.id');
        // count total rows
        $totalRecords = count($builder->getQuery()->execute());
        // search value
        if (!empty($params['search']['value'])) {
            $wh = $params['search']['value'];
            $builder->where('ad.id LIKE :id:', ['id' => '%'.$wh.'%'])
            ->orWhere('au.createdAt LIKE :date:', ['date' => '%'.$wh.'%'])
            ->orWhere('au.action LIKE :action:', ['action' => '%'.$wh.'%'])
            ->orWhere('au.modelName LIKE :model:', ['model' => '%'.$wh.'%'])
            ->orWhere('ad.fieldName LIKE :field:', ['field' => '%'.$wh.'%'])
            ->orWhere('ad.oldValue LIKE :oldValue:', ['oldValue' => '%'.$wh.'%'])
            ->orWhere('ad.newValue LIKE :newValue:', ['newValue' => '%'.$wh.'%'])
            ->orWhere('au.ipAddress LIKE :ip:', ['ip' => '%'.$wh.'%'])
            ->orWhere('user.name LIKE :user:', ['user' => '%'.$wh.'%']);
            $totalRecords = count($builder->getQuery()->execute());
        }
        // order by
        $builder->orderBy($columns[$params['order'][0]['column']]."   ".$params['order'][0]['dir']);
        $builder->limit($params['length'], $params['start']);

        $result = $builder->getQuery()->execute();

        foreach ($result as $value) {
            $nokey=[];
            $nokey[] = $value->id;
            $nokey[] = $value->date;
            $nokey[] = $value->model;
            $nokey[] = $value->action;
            $nokey[] = $value->valueID;
            $nokey[] = $value->ip;
            $nokey[] = ($value->user) ? $value->user : 'System';
            $disabled = ($value->adID) ? null : 'disabled';
            $nokey[] = '<div class="btn-group" role="group" aria-label="">
            <a href="#" data-toggle="modal" data-target="#detail" onclick="detail('.$value->id.')" class="btn btn-primary btn-sm '.$disabled.'"><i class="fas fa-eye"></i></a>
            </div>';
            $data[] = $nokey;
        }
        $resData = array(
            "draw"            => intval($params['draw']),
            "recordsTotal"    => intval($totalRecords),
            "recordsFiltered" => intval($totalRecords),
            "data"            => $data
        );
        return $resData;
    }

    private function historyDetail($id)
    {
        $auditDetails = \PSA\Models\AuditDetail::find("auditID = ".$id."");
        $resData= '<table class="table table-hover">';
        $resData.= '<thead>';
        $resData.= '<tr>';
        $resData.= '<th>Name</th>';
        $resData.= '<th>old value</th>';
        $resData.= '<th>new value</th>';
        $resData.= '</tr>';
        $resData.= '</thead>';
        foreach ($auditDetails as $value) {
            $resData.= '<tr>';
            $resData.= '<th>'.$value->fieldName.'</th>';
            $resData.= '<th>'.$value->oldValue.'</th>';
            $resData.= '<th>'.$value->newValue.'</th>';
            $resData.= '</tr>';
        }
        $resData.= '</tbody>';
        $resData.= '</table>';
        return $resData;
    }
}
