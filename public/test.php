<?php
require __DIR__ . '/vendor/autoload.php';

$options = array(
    'cluster' => 'ap1',
    'encrypted' => true
);
$pusher = new Pusher\Pusher(
    'dabe138a09a82b3ddea2',
    'b9f16a58e6852774605c',
    '432291',
    $options
);

$data['info'] = 'hello world';
$pusher->trigger('my-channel', 'my-event', $data);
