<?php

namespace PSA\Controllers;

use Phalcon\Http\Response;
use PSA\Helpers\Datatables;
use PSA\Services\ChangeHistoryService;

class ChangeHistoryController extends ControllerBase
{
    private $changeHistoryService;

    public function initialize()
    {
        $this->view->setTemplateBefore('private');
        $this->tag->title()->set('Change History');
        $this->changeHistoryService = new ChangeHistoryService($this->modelsManager);
    }

    public function indexAction()
    {
        if ($this->request->isAjax()) {
            $this->view->disable();
            $response = new Response();
            $resData = $this->request->getPost('id', 'int') ? $this->changeHistoryService->historyDetail($this->request->getPost('id', 'int')) : $this->changeHistoryService->dataTablesAjax($_REQUEST);
            $response->setJsonContent($resData)->setStatusCode(200)->send();
        }

        $this->view->css = (new Datatables)->css();
        $this->view->js = (new Datatables)->jsAjax('ChangeHistory');
        $this->assets->addJs('js/ChangeHistory/index.js');
        $this->view->breadcrumbs = "
        <li class='breadcrumb-item'><a href='/dashboard'><i class='fas fa-fw fa-tachometer-alt'></i> Dashboard</a></li>
        <li class='breadcrumb-item active'><i class='fas fa-history'></i> Change History</li>
        ";
    }
}