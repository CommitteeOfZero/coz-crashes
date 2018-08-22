<?php namespace CoZCrashes\Actions;

class AdminLoginAction extends \CoZCrashes\Base {
    public function __invoke ($request, $response) {
        return $this->c->view->render($response, 'admin_login.twig');
    }
}