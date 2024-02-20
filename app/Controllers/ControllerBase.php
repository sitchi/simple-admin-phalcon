<?php
declare(strict_types=1);

/**
 * Namespace for PSA Controllers
 */
namespace PSA\Controllers;

use Phalcon\Mvc\Controller;
use Phalcon\Mvc\Dispatcher;

/**
 * Class ControllerBase
 *
 * This class extends Controller and is responsible for handling the base functionality of all controllers.
 */
class ControllerBase extends Controller
{
    /**
     * This method is called before executing the route and checks if the user has access to the requested module.
     * If the user does not have access, it forwards the user to the appropriate page.
     *
     * @param Dispatcher $dispatcher The dispatcher.
     * @return bool Returns true if the user has access, false otherwise.
     */
    public function beforeExecuteRoute(Dispatcher $dispatcher): bool
    {
        $controllerName = $dispatcher->getControllerName();

        if ($this->acl->isPrivate($controllerName)) {
            $identity = $this->auth->getIdentity();

            if (!is_array($identity)) {
                $this->flash->notice('You don\'t have access to this module: private');
                $this->forwardToIndex($dispatcher);
                return false;
            }

            $actionName = $dispatcher->getActionName();

            if (!$this->acl->isAllowed($identity['roles'], $controllerName, $actionName)) {
                $this->flash->notice('You don\'t have access to this module: ' . $controllerName . ':' . $actionName);
                $this->acl->isAllowed($identity['roles'], $controllerName, 'index') ? $this->forwardToIndex($dispatcher, $controllerName) : $this->forwardToUserControl($dispatcher);
                return false;
            }
        }

        return true;
    }

    /**
     * This method forwards the user to the index page of the specified controller.
     *
     * @param Dispatcher $dispatcher The dispatcher.
     * @param string $controllerName The name of the controller to forward to. Defaults to 'index'.
     */
    private function forwardToIndex(Dispatcher $dispatcher, string $controllerName = 'index'): void
    {
        $dispatcher->forward([
            'controller' => $controllerName,
            'action' => 'index',
        ]);
    }

    /**
     * This method forwards the user to the index page of the user control.
     *
     * @param Dispatcher $dispatcher The dispatcher.
     */
    private function forwardToUserControl(Dispatcher $dispatcher): void
    {
        $dispatcher->forward([
            'controller' => 'user_control',
            'action' => 'index',
        ]);
    }
}