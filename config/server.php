<?php
return 
[
    'host' => '127.0.0.1', 
    'port' => 9500,
    'mode' => SWOOLE_PROCESS,
    'sockType' => SWOOLE_SOCK_TCP,
    'app' => require __DIR__ . '/swoole.php', // your original web.php 
    'options' => [ // options for swoole server
        'pid_file' => __DIR__ .'/../runtime/swoole.pid',
        'log_file' => '/tmp/swoole_http_server_core.log',
        'worker_num' => (int)(swoole_cpu_num()*0.8),
        'daemonize' => true,
        'enable_static_handler' => false,
        'enable_coroutine' => false,
        'open_mqtt_protocol' => false,
        'only_simple_http' => true,
        'open_tcp_nodelay' => true,
        'max_request' => 10000,
        'dispatch_mode' => 2,
        'log_level'=> 0,
        'task_worker_num' => 2,
        'user' => 'www',
        'group' => 'www',
    ]
];