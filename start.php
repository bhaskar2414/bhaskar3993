<?php
require_once __DIR__ . '/vendor/autoload.php';

use Workerman\Worker;
use Workerman\WebServer;
use Workerman\Autoloader;
use PHPSocketIO\SocketIO;
use Illuminate\Http\Request;


define('GLOBAL_START', true);
$context = array(
    'ssl' => array(
        'local_cert'  => __DIR__.'/server.pem',
        'local_pk'    => __DIR__.'/server.key',
        'verify_peer' => false,
        
    )
);
$io = new SocketIO(8888,$context);
$io->on('connection', function($socket){
	echo 'connected';
    // when the client emits 'new message', this listens and executes
	$socket->on('message', function ($data) use($socket){
		//print_r($data['receiver_id']);
        // we tell the client to execute 'new message'
		echo '<pre>';
        print_r($data);
		$socket->broadcast->emit('newfromserver_'.$data['receiver_id'].'_'.$data['sender_id'],$data);
	});
	

});
if (!defined('GLOBAL_START')) {
	Worker::runAll();
}

Worker::runAll();
