<?php namespace CoZCrashes\Actions;

class AdminUpdateAction extends \CoZCrashes\Base {
    public function __invoke ($request, $response) {
        $report = $request->getAttribute('report');
        if (is_null($report)) {
            throw new \Slim\Exception\NotFoundException($request, $response);
        }

        $this->c->report_util->selectAllIn($report->id)
            ->update(['admin_comment' => $request->getParam('adminComment')]);
        $this->c->flash->addMessage('success', 'Report updated.');
        return $response;
    }
}