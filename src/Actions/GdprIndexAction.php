<?php namespace CoZCrashes\Actions;

class GdprIndexAction extends \CoZCrashes\Base {
    public function __invoke ($request, $response) {
        return $this->c->view->render($response, 'gdpr_index.twig');
    }
}