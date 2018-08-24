<?php namespace CoZCrashes\Actions;

class DestroyAction extends \CoZCrashes\Base {
    public function __invoke ($request, $response) {
        $report = $request->getAttribute('report');
        if (is_null($report)) {
            throw new \Slim\Exception\NotFoundException($request, $response);
        }
        $this->c->flash->addMessage('success', 'Report deleted.');
        $this->c->report_util->destroy($report->id);
        return $response;
    }
}