<?php

$projectRoot = file_exists(__DIR__ . '/vendor/autoload.php') ? __DIR__ : realpath(__DIR__ . '/../../..');

//  This code is not usefull if the ds extension is not installed, even if "php-ds/ds" is installed.
if (!extension_loaded('ds')) {
	require $projectRoot . '/vendor/autoload.php';
	return;
}

if (defined('HHVM_VERSION') || (function_exists('zend_loader_file_encoded') && zend_loader_file_encoded())) {
	throw new Exception('Cannot use this autoloader.');
}

require $projectRoot . '/vendor/composer/platform_check.php';

require __DIR__ . '/Autoloader.php';

ini_set('memory_limit', -1);

Diskerror\Autoloader::init($projectRoot);
spl_autoload_register('Diskerror\Autoloader::load', true, true);
Diskerror\Autoloader::loadFiles();
