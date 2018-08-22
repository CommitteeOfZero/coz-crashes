<?php

require_once __DIR__ . '/init.php';

$query = $container->db->table('reports')
    ->select($container->db->raw('HEX(guid) AS hex_guid'))
    ->where('created_at', '<', $container->db->raw('UTC_TIMESTAMP() - INTERVAL ? DAY', $container->config['app']['storageLimitDays']));

$result = $query->get();

$query->delete();

foreach ($result as $row) {
    $guid = hex2guid($row->hex_guid);
    $container->webhook->send([
        'content' => "Report `$guid` has expired, please delete!"
    ]);
}