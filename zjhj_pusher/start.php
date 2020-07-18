<?php
use Workerman\Worker;
use Workerman\Lib\Timer;

// composer autoload
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/src/Pusher.php';
require_once __DIR__ . '/config.php';


$pusher = new Pusher\Pusher("websocket://0.0.0.0:$websocket_port");

$pusher->apiListen = "http://0.0.0.0:$api_port";
$pusher->appInfo = array(
    $app_key => array(
        'channel_hook' => "{$domain}/admin/event",
        'app_secret'   => $app_secret,
    ),
);

// 只能是1
$pusher->count = 1;

Worker::runAll();
