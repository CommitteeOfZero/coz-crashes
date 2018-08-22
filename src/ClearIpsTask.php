<?php

require_once __DIR__ . '/init.php';

$container->db->table('reports')
    ->whereNotNull('ip')
    ->where('created_at', '<', $container->db->raw('UTC_TIMESTAMP() - INTERVAL ? HOUR', $container->config['app']['submitLimitHours']))
    ->update([
        'ip' => $container->db->raw('NULL')
]);