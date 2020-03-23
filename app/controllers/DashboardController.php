<?php
declare(strict_types=1);

namespace PSA\Controllers;

/**
 * Display the terms and conditions page.
 */
class DashboardController extends ControllerBase
{
    public function initialize()
    {
        $this->view->setTemplateBefore('private');
    }

    /**
     * Default action. Set the public layout (layouts/private.volt)
     */
    public function indexAction()
    {
        $this->tag->setTitle('Simple Admin');
    }
}
