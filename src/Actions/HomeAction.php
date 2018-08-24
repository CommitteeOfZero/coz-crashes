<?php namespace CoZCrashes\Actions;

class HomeAction extends \CoZCrashes\Base {
    public function __invoke ($request, $response) {
        $data = [];

        $query = $this->c->report_util->selectAll();

        $isAdminArea = $request->getAttribute('isAdminArea');
        if (is_null($isAdminArea) || !$isAdminArea) {
            $search_hex = [''];
            if (isset($_SESSION['search_hex'])) $search_hex = $_SESSION['search_hex'];
            $query = $query->whereIn($this->c->db->raw('HEX(guid)'), $search_hex);
        }

        $result = $query
            ->orderBy('created_at', 'DESC')
            ->get();
        $totalReports = count($result);
        foreach ($result as $row) {
            if (!isset($data[$row->product])) $data[$row->product] = [];
            if (!isset($data[$row->product][$row->version])) $data[$row->product][$row->version] = [];
            $data[$row->product][$row->version][] = [
                'date' => date($this->c->config['app']['timestampFormat'], strtotime($row->created_at)),
                'id' => $row->id,
                'guid' => $this->c->report_util->hex2guid($row->hex_guid),
                'hasEmail' => !empty($row->email),
                'hasUserComment' => !empty($row->user_comment),
                'hasAdminComment' => !empty($row->admin_comment),
                'rva' => $row->exception_rva,
                'filesize' => filesizeFormat($row->filesize)
            ];
        }
        return $this->c->view->render($response, 'home.twig', [
            'data' => $data,
            'totalReports' => $totalReports
        ]);
    }
}