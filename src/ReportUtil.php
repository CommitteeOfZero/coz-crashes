<?php namespace CoZCrashes;

class ReportUtil extends Base {
    public function hex2guid($hex) {
        return strtolower(substr($hex, 0, 8)
            . '-' . substr($hex, 8, 4)
            . '-' . substr($hex, 12, 4)
            . '-' . substr($hex, 16, 4)
            . '-' . substr($hex, 20, 12));
    }

    public function guid2hex($guid) {
        return str_replace('-', '', $guid);
    }

    public function getIdType($id) {
        if (is_int($id) || is_numeric($id)) return 'id';
        if (preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i', $id)) return 'guid';
        if (preg_match('/^[0-9a-f]{32}$/i', $id)) return 'hex';
        return 'invalid';
    }

    public function selectHexGuid($query = null) {
        if (is_null($query)) $query = $this->c->db->table('reports');
        return $query->select($this->c->db->raw('HEX(guid) AS hex_guid'));
    }

    public function selectAll($query = null) {
        if (is_null($query)) $query = $this->c->db->table('reports');
        // order is important, * has to go first
        $query = $query->select('*');
        return $this->selectHexGuid($query);
    }

    public function selectAllIn($id, $query = null) {
        $query = $this->selectAll($query);
        switch ($this->getIdType($id)) {
            case 'id':
                return $query->where('id', '=', $id);
                break;
            case 'hex':
                return $query->where('guid', '=', $this->c->db->raw('UNHEX(?)', $id));
                break;
            case 'guid':
                return $query->where('guid', '=', $this->c->db->raw('UNHEX(?)', $this->guid2hex($id)));
                break;
        }
        return null;
    }

    public function getFullReport($id) {
        $query = $this->selectAllIn($id);
        if (is_null($query)) return null;
        return $query->first();
    }

    public function convertId($id, $requested) {
        switch ($this->getIdType($id)) {
            case 'id':
                if ($requested == 'id') return $id;
                $row = $this->getFullReport($id);
                if (is_null($row)) throw new Exception("Report $id not found");
                if ($requested == 'hex') return $row->hex_guid;
                if ($requested == 'guid') return $this->hex2guid($row->hex_guid);
                break;
            case 'hex':
                if ($requested == 'hex') return $id;
                if ($requested == 'guid') return $this->hex2guid($id);
                if ($requested == 'id') return $this->getFullReport($id)->hex_guid;
                break;
            case 'guid':
                if ($requested == 'guid') return strtolower($id); // regex allows uppercase, we want to render lowercase
                if ($requested == 'hex') return $this->guid2hex($id);
                if ($requested == 'id') return $this->getFullReport($id)->id;
                break;
            default:
                throw new Exception('Invalid report ID.');
        }
        throw new Exception('Invalid target type');
    }

    public function notifyDestroy($id) {
        $guid = $this->convertId($id, 'guid');
        $this->c->webhook->send([
            'content' => "Report `$guid` has expired, please delete!"
        ]);
    }

    public function destroy($id) {
        $guid = $this->convertId($id, 'guid'); // may read from DB, have to get this before deleting record
        unlink(COZCRASHES_BASE . '/public/uploads/' . $guid . '.zip');
        $this->selectAllIn($id)->delete();
        $this->notifyDestroy($guid);
    }
}