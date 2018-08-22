<?php

require_once __DIR__ . '/init.php';

$container->db->table('reports')
    ->where('created_at', '<', $container->db->raw('UTC_TIMESTAMP() - INTERVAL ? DAY', $container->config['app']['storageLimitDays']))
    ->delete();