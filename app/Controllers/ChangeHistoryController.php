<?php

/**
 * Namespace for PSA Controllers
 */
namespace PSA\Controllers;

use Phalcon\Http\Response;
use PSA\Helpers\Datatables;
use PSA\Services\ChangeHistory\ChangeHistoryService;

/**
 * Class ChangeHistoryController
 *
 * This class extends ControllerBase and is responsible for handling the change history page.
 */
class ChangeHistoryController extends ControllerBase
{
    /**
     * @var ChangeHistoryService
     * An instance of the ChangeHistoryService.
     */
    private $changeHistoryService;

    /**
     * This method is called on every request and sets the template before rendering the view.
     * It sets the template to 'private' and the title of the page to 'Change History'.
     * It also initializes the ChangeHistoryService.
     */
    public function initialize()
    {
        $this->view->setTemplateBefore('private');
        $this->tag->title()->set('Change History');
        $this->changeHistoryService = new ChangeHistoryService($this->modelsManager);
    }

    /**
     * This method is responsible for handling the index action of the change history page.
     * If the request is an AJAX request, it disables the view and sends a JSON response with the change history data.
     * Otherwise, it sets the CSS and JS for the page and adds the breadcrumbs.
     */
    public function indexAction()
    {
        if ($this->request->isAjax()) {
            $this->view->disable();
            $response = new Response();
            $resData = $this->request->getPost('id', 'int') ? $this->changeHistoryService->historyDetail($this->request->getPost('id', 'int')) : $this->changeHistoryService->dataTablesAjax($_REQUEST);
            $response->setJsonContent($resData)->setStatusCode(200)->send();
        }

        $this->view->css = (new Datatables)->css();
        $this->view->js = (new Datatables)->jsAjax('changeHistory');
        $this->assets->addJs('js/changeHistory/index.js');
        $this->view->breadcrumbs = "
        <li class='breadcrumb-item'><a href='/dashboard'><i class='fas fa-fw fa-tachometer-alt'></i> Dashboard</a></li>
        <li class='breadcrumb-item active'><i class='fas fa-history'></i> Change History</li>
        ";
    }
}