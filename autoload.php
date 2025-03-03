<?php

namespace Diskerror;

use Ds\Map;

//  This code is not usefull if the ds extension is not installed, even if "php-ds/ds" is installed.
if (!extension_loaded('ds') || PHP_VERSION_ID < 70400) {
    require __DIR__ . '/../../autoload.php';
    return;
}

if (defined('HHVM_VERSION') || (function_exists('zend_loader_file_encoded') && zend_loader_file_encoded())) {
    throw new Exception('Cannot use this autoloader.');
}

require __DIR__ . '/../../composer/platform_check.php';

$classmapFile   = __DIR__ . '/../../composer/autoload_classmap.php';
$namespacesFile = __DIR__ . '/../../composer/autoload_namespaces.php';
$psr4File       = __DIR__ . '/../../composer/autoload_psr4.php';
$filesFile      = __DIR__ . '/../../composer/autoload_files.php';
$autoloadCache  = __DIR__ . '/../../autoload.cache';

$maps;  //  Ds\Map
if (file_exists($autoloadCache)) {
    $maps  = unserialize(file_get_contents($autoloadCache));
    $mtime = filemtime($autoloadCache);
}
else {
    $maps  = new Map();
    $mtime = 0;
}
$changed = false;

if (filemtime($classmapFile) > $mtime) {
    $maps['classes'] = file_exists($classmapFile) ? new Map(require $classmapFile) : new Map();
    $changed         = true;
}

if (filemtime($namespacesFile) > $mtime) {
    $maps['namespaces'] = file_exists($namespacesFile) ? new Map(require $namespacesFile) : new Map();
    $changed            = true;
}

if (filemtime($psr4File) > $mtime) {
    $maps['psr4'] = file_exists($psr4File) ? new Map(require $psr4File) : new Map();
    $changed      = true;
}


if (file_exists($filesFile)) {
    if (filemtime($filesFile) > $mtime) {
        $maps['files'] = new Map(require $filesFile);
        $changed       = true;
    }
}
elseif (!isset($maps['files']) || !empty($maps['files'])) {
    $maps['files'] = [];
    $changed       = true;
}

if ($changed) {
    file_put_contents($autoloadCache, serialize($maps));
}

/**
 * Loader.
 * Function to be registered with autoloader.
 *
 * @param string $class
 *
 * @return bool
 */
$autoload = function (string $class) use ($maps) {
    //	Classes mapped directly to files.
    if ($maps['classes']->hasKey($class)) {
        $classFile = $maps['classes']->get($class);
        if (file_exists($classFile)) {
            require $classFile;
            return true;
        }
    }

    $classArr   = explode('\\', $class);
    $classDepth = count($classArr);

    $reqClass = '';
    for ($cd = 0; $cd < $classDepth; ++$cd) {
        $reqClass .= $classArr[$cd] . '\\';

        //	PSR-4
        if ($maps['psr4']->hasKey($reqClass)) {
            $workingClassPaths = $maps['psr4']->get($reqClass);
            $pathCount         = count($workingClassPaths);
            for ($wc = 0; $wc < $pathCount; ++$wc) {
                $workingClassFile =
                    $workingClassPaths[$wc] . ' / ' . implode(' / ', array_slice($classArr, $cd + 1)) . ' . php';

                if (file_exists($workingClassFile)) {
                    require $workingClassFile;
                    return true;
                }
            }
        }

        //	Namespaces. TODO: Need sample data to test.
        if ($maps['namespaces']->hasKey($reqClass)) {
            $workingClassFile =
                $maps['namespaces']->get($reqClass) . ' / ' . implode(' / ', array_slice($classArr, $cd + 1)) . ' . php';

            if (file_exists($workingClassFile)) {
                require $workingClassFile;
                return true;
            }
        }
    }

    return false;
};

/**
 * Include files.
 */
foreach ($maps['files'] as $file) {
    require_once $file;
}

spl_autoload_register($autoload, false, true);
