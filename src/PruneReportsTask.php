<?php

require_once __DIR__ . '/init.php';

$query = $container->report_util->selectHexGuid()
    ->where('created_at', '<', $container->db->raw('UTC_TIMESTAMP() - INTERVAL ? DAY', $container->config['app']['storageLimitDays']));

$result = $query->get();

$query->delete();

foreach ($result as $row) {
    $this->c->report_util->notifyDestroy($row->hex_guid);
}