<?php

require_once __DIR__ . '/init.php';

$query = $container->report_util->selectHexGuid()
    ->where('created_at', '<', $container->db->raw('UTC_TIMESTAMP() - INTERVAL ? DAY', $container->config['app']['storageLimitDays']));

$result = $query->get();

$query->delete();

foreach ($result as $row) {
    $guid = $container->hex2guid($row->hex_guid);
    unlink(COZCRASHES_BASE . '/public/uploads/' . $guid . '.zip');
    $container->report_util->notifyDestroy($guid);
}