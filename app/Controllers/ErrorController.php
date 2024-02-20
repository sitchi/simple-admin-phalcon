<?php
declare(strict_types=1);

/**
 * Namespace for PSA Controllers
 */

namespace PSA\Controllers;

/**
 * Class ErrorController
 *
 * This class extends ControllerBase and is responsible for handling error pages.
 */
class ErrorController extends ControllerBase
{
    /**
     * This method is called on every request and sets the template before rendering the view.
     * It checks if the user is authenticated and sets the template accordingly.
     */
    public function initialize()
    {
        $this->view->setTemplateBefore($this->auth->getIdentity() ? 'private' : 'public');
    }

    /**
     * This method is responsible for handling 404 errors.
     * It sets the title of the page and the HTTP status code.
     */
    public function show404Action()
    {
        $this->tag->title()->set('Error 404');
        $this->response->setStatusCode(404, 'Not Found');
    }

    /**
     * This method is responsible for handling 500 errors.
     * It sets the title of the page and the HTTP status code.
     */
    public function show500Action()
    {
        $this->tag->title()->set('Error 500');
        $this->response->setStatusCode(500, 'Internal Server Error');
    }
}