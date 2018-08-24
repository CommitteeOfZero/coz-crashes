<?php namespace CoZCrashes\Actions;

class GdprLookupAction extends \CoZCrashes\Base {
    public function __invoke ($request, $response) {
        $guidsRaw = [];

        $listText = $request->getParam('listText');
        if (!empty($listText) && is_string($listText)) {
            $guidsRaw = array_merge($guidsRaw, preg_split('/\s+/', $listText));
        }

        if (!empty($request->getUploadedFiles()['listFile'])) {
            $listFile = $request->getUploadedFiles()['listFile'];
            if ($listFile->getSize() < 4*1024 && $listFile->getSize() > 0) {
                $listFileText = (string)$listFile->getStream();
                $guidsRaw = array_merge($guidsRaw, preg_split('/\s+/', $listFileText));
            }
        }

        if (count($guidsRaw) > 50) {
            $this->c->flash->addMessage('error', 'Too many IDs.');
            return $response->withRedirect($this->c->router->pathFor('home'));
        }

        $_SESSION['search_hex'] = [''];
        foreach ($guidsRaw as $guid) {
            if ($this->c->report_util->getIdType($guid) == 'guid') $_SESSION['search_hex'][] = $this->c->report_util->guid2hex($guid);
        }
    }
}