<?php
declare(strict_types=1);

namespace PSA\Controllers;

/**
 * ErrorController
 */
class ErrorController extends ControllerBase
{
    public function initialize()
    {
        // check auth users
        if ($this->auth->getIdentity()) {
            $this->view->setTemplateBefore('private');
        } else {
            $this->view->setTemplateBefore('public');
        }

    }

    public function show404Action()
    {
        $this->tag->title()->set('Error 404');
        $this->response->setStatusCode(404, 'Not Found');
    }

    public function show500Action()
    {
        $this->tag->title()->set('Error 500');
        $this->response->setStatusCode(500, 'Internal Server Error');
    }
}