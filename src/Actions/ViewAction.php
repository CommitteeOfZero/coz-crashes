<?php namespace CoZCrashes\Actions;

class ViewAction extends \CoZCrashes\Base {
    public function __invoke ($request, $response) {
        $report = $request->getAttribute('report');
        if (is_null($report)) {
            throw new \Slim\Exception\NotFoundException($request, $response);
        }
        $data = [
            'date' => date($this->c->config['app']['timestampFormat'], strtotime($report->created_at)),
            'id' => $report->id,
            'guid' => $this->c->report_util->hex2guid($report->hex_guid),
            'email' => $report->email,
            'userComment' => $report->user_comment,
            'adminComment' => $report->admin_comment,
            'rva' => $report->exception_rva,
            'filesize' => filesizeFormat($report->filesize),
            'ip' => $report->ip,
            'product' => $report->product,
            'version' => $report->version,
            'exceptionModule' => $report->exception_module
        ];
        return $this->c->view->render($response, 'view.twig', ['report' => $data]);
    }
}