<?php namespace CoZCrashes\Actions;

class DestroyAction extends \CoZCrashes\Base {
    public function __invoke ($request, $response) {
        $this->c->report_util->destroy($request->getParam('guid'));
        return $response;
    }
}