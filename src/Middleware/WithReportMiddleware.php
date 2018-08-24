<?php namespace CoZCrashes\Middleware;

// Try to fetch report and put it into $request->getAttribute('report')
// If failed, puts null there instead
class WithReportMiddleware extends \CoZCrashes\Base {
    protected $idTypes;
    protected $passType;

    // $passType 'param' for request parameter, 'arg' for route argument
    // $idTypes can be empty (any type), an array of allowed types, or a single allowed type
    public function __construct($container, $passType, $idTypes = array()) {
        parent::__construct($container);
        $this->idTypes = (array)$idTypes;
        $this->passType = $passType;
    }

    public function __invoke($request, $response, $next) {
        if ($this->passType == 'param') {
            $id = $request->getParam('id');
        } else if ($this->passType == 'arg') {
            $id = $request->getAttribute('route')->getArgument('id');
        }

        $report = null;
        if (empty($this->idTypes) || in_array($this->c->report_util->getIdType($id), $this->idTypes)) {
            $report = $this->c->report_util->getFullReport($id);
        }
        $request = $request->withAttribute('report', $report);

        return $next($request, $response);
    }
}