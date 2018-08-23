<?php namespace CoZCrashes\Actions;

class AdminHomeAction extends \CoZCrashes\Base {
    public function __invoke ($request, $response) {
        $data = [];
        $result = $this->c->db->table('reports')
            ->select('*')
            ->select($this->c->db->raw('HEX(guid) as hex_guid'))
            ->orderBy('created_at', 'DESC')
            ->get();
        foreach ($result as $row) {
            if (!isset($data[$row->product])) $data[$row->product] = [];
            if (!isset($data[$row->product][$row->version])) $data[$row->product][$row->version] = [];
            $data[$row->product][$row->version][] = [
                'date' => date($this->c->config['app']['timestampFormat'], strtotime($row->created_at)),
                'id' => $row->id,
                'guid' => hex2guid($row->hex_guid),
                'hasEmail' => !empty($row->email),
                'hasUserComment' => !empty($row->user_comment),
                'hasAdminComment' => !empty($row->admin_comment),
                'rva' => $row->exception_rva,
                'filesize' => filesizeFormat($row->filesize)
            ];
        }
        return $this->c->view->render($response, 'admin_home.twig', ['data' => $data]);
    }
}