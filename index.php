<?php

if (!session_id()){
	@session_start();
}

error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER['REQUEST_METHOD'] === 'GET' && !(
		(isset($_SERVER['REDIRECT_URL']) && $_SERVER['REDIRECT_URL'] === '/login') ||
		(isset($_SERVER['PATH_INFO']) && $_SERVER['PATH_INFO'] === '/login')
	)) {
	// header('refresh:2');
}

function vd(...$v) {
	echo '<pre style="white-space: pre-wrap; word-break: break-all;">';
	var_dump(...$v);
	echo '</pre>';
}

function pr(...$vs) {
	echo '<pre style="white-space: pre-wrap; word-break: break-all;">';
	foreach ($vs as $v) print_r($v);
	echo '</pre>';
}

include __DIR__ . '/vendors/fw/load.php';

$loader->addNamespace('App', __DIR__ . '/app');

include __DIR__ . '/app/config.php';

use \FW\FW;

$fw = FW::getInstance();

$fw->scanComponents(
	__DIR__ . '/app/controllers',
	__DIR__ . '/app/services',
	__DIR__ . '/app/repositories');

$fw->run();
