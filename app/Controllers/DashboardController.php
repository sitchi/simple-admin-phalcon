<?php
declare(strict_types=1);

/**
 * Namespace for PSA Controllers
 */

namespace PSA\Controllers;

/**
 * Class DashboardController
 *
 * This class extends ControllerBase and is responsible for handling the dashboard page.
 */
class DashboardController extends ControllerBase
{
    /**
     * This method is called on every request and sets the template before rendering the view.
     * It sets the template to 'private'.
     */
    public function initialize()
    {
        $this->view->setTemplateBefore('private');
    }

    /**
     * This method is responsible for handling the index action of the dashboard.
     * It sets the title of the page.
     */
    public function indexAction()
    {
        $this->tag->title()->set('Simple Admin');
    }
}