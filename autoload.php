<?php

//  This code is not usefull if the ds extension is not installed, even if "php-ds/ds" is installed.
if (!extension_loaded('ds') || PHP_VERSION_ID < 70400) {
    require __DIR__ . '/../../autoload.php';
    return;
}

if (defined('HHVM_VERSION') || (function_exists('zend_loader_file_encoded') && zend_loader_file_encoded())) {
    throw new Exception('Cannot use this autoloader.');
}

require __DIR__ . '/../../composer/platform_check.php';

require __DIR__ . '/Autoloader.php';

ini_set('memory_limit', -1);

Diskerror\Autoloader::init();
spl_autoload_register('Diskerror\Autoloader::load', true, true);
Diskerror\Autoloader::loadFiles();
