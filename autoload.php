<?php

if (!extension_loaded('ds') || PHP_VERSION_ID < 70300) {
	require __DIR__ . '/../../autoload.php';
	return;
}

if (defined('HHVM_VERSION') || (function_exists('zend_loader_file_encoded') && zend_loader_file_encoded())) {
	throw new Exception('Cannot use this autoloader.');
}

require __DIR__ . '/Autoloader.php';

Diskerror\Autoloader::init();
spl_autoload_register('Diskerror\Autoloader::load', false, true);
Diskerror\Autoloader::loadFiles();
