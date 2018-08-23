<?php namespace CoZCrashes\Actions;

class SubmitAction extends \CoZCrashes\Base {
    public function __invoke ($request, $response, $args) {
        // Rate limit
        $ip = $request->getServerParam('REMOTE_ADDR');
        // IPs get cleared automatically, so we don't need to check validity here
        if ($this->c->db->table('reports')
            ->where('ip', '=', $ip)
            ->count() >= $this->c->config['app']['submitLimitSubmissions']) {
            return $response->withStatus(429)->write("You can only submit {$this->c->config['app']['submitLimitSubmissions']} reports in {$this->c->config['app']['submitLimitHours']} hours");
        }

        // Ensure we're talking to CrashSender
        if (!$request->hasHeader('User-Agent') || $request->getHeader('User-Agent')[0] !== 'CrashRpt') {
            return $response->withStatus(400)->write('This endpoint only accepts crash submissions by the crash reporter application.');
        }

        // Validate parameters
        $params = $request->getParams() + $_FILES; // $_FILES is only for validation
        $validation = $this->c->validator->validate($params, [
            'md5'                   =>  'required|regex:/^[0-9a-f]{32}$/',
            'appname'               =>  'required|max:191',
            'appversion'            =>  'required|max:191',
            'crashguid'             =>  'required|regex:/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/',
            'emailfrom'             =>  'max:1024',
            'description'           =>  'max:1024',
            'exceptionaddress'      =>  'required|numeric',
            'exceptionmodulebase'   =>  'required|numeric',
            'exceptionmodule'       =>  'required|max:1024',
            'crashrpt'              =>  "required|uploaded_file:0,{$this->c->config['app']['maxFileSize']},zip"
        ]);
        if ($validation->fails()) {
            return $response->withStatus(400)->write(implode($validation->errors()->all(), "\n"));
        }
        // don't have uint64 in php, so keep it a hex string here
        $dbguid = $this->c->report_util->guid2hex($params['crashguid']);
        // Validate GUID is new
        if ($this->c->db->table('reports')
            ->where('guid', '=', $this->c->db->raw('UNHEX(?)', $dbguid))
            ->count() > 0) {
            return $response->withStatus(400)->write('Report already uploaded');
        }
        // All clear

        // Extract module base name, e.g. 'C:\some/path\buggy.dll' => 'buggy:$'
        // exception RVA in module is appended in query
        if (preg_match('/[\\/\\\\](\\w+)\\.\\w+$/', $params['exceptionmodule'], $moduleMatches)) {
            $rvaStart = "$moduleMatches[1]:\$";
        } else {
            $rvaStart = '$';
        }

        // Insert into database
        $data = [
            'guid' => $this->c->db->raw('UNHEX(?)', $dbguid),
            'product' => $params['appname'],
            'version' => $params['appversion'],
            'created_at' => $this->c->db->raw('UTC_TIMESTAMP()'),
            'ip' => $ip,
            'exception_module' => $params['exceptionmodule'],
            // params get submitted as strings but
            // a single cast makes the subtraction 64-bit unsigned arithmetic
            // example result: buggy:$10f0
            'exception_rva' => $this->c->db->raw('CONCAT(?, LOWER(HEX(CAST(? AS UNSIGNED) - ?)))', array(
                                                $rvaStart,
                                                $params['exceptionaddress'],
                                                $params['exceptionmodulebase'])),
            'filesize' => $request->getUploadedFiles()['crashrpt']->getSize()
        ];
        if (!empty($params['emailfrom'])) $data['email'] = $params['emailfrom'];
        if (!empty($params['description'])) $data['usercomment'] = $params['description'];
        $id = $this->c->db->table('reports')->insert($data);

        // Save file
        $request->getUploadedFiles()['crashrpt']->moveTo(COZCRASHES_BASE . '/public/uploads/' . $params['crashguid'] . '.zip');

        // Post Discord notification
        // First get RVA from DB again...
        $exceptionRvaStr = $this->c->db->table('reports')
            ->select('exception_rva')
            ->find($id)
            ->exception_rva;
        $this->c->webhook->send([
            'embeds' => [
                [
                    'title' => 'Crash in ' . $params['appname'],
                    'description' => $params['appversion'],
                    'url' => $this->c->router->pathFor('adminView', ['id' => $id]),
                    'fields' => [
                        [
                            'name' => 'RVA',
                            'value' => $exceptionRvaStr
                        ],
                        [
                            'name' => 'Reported at',
                            'value' => date($this->c->config['app']['timestampFormat'])
                        ]
                    ]
                ]
            ]
        ]);

        return $response->withStatus(200);
    }
}