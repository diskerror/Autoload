<?php

namespace Diskerror;

use Ds\Map;
use Ds\Vector;
use function basename;
use function count;
use function explode;
use function file_exists;
use function implode;

final class Autoloader
{
	private static $root;
	private static $classmap;
	private static $namespaces;
	private static $psr4;

	public static function init()
	{
		self::$root = (basename('../..') === 'vendor') ? '../..' : 'vendor';

		self::$classmap   = new Map(require self::$root . '/composer/autoload_classmap.php');
		self::$namespaces = new Map(require self::$root . '/composer/autoload_namespaces.php');
		self::$psr4       = new Map(require self::$root . '/composer/autoload_psr4.php');
	}

	public static function loader($class)
	{
		if (self::$classmap->hasKey($class)) {
			require self::$classmap->offsetGet($class);
			return true;
		}

		if (self::$namespaces->hasKey($class)) {
			require self::$namespaces->offsetGet($class);
			return true;
		}

		$classV         = new Vector(explode('\\', $class));
		$requestedClass = '';
		for ($p = 0; $p < $classV->count(); ++$p) {
			$requestedClass .= $classV->get($p) . '\\';
			if (self::$psr4->hasKey($requestedClass)) {
				$workingClassPaths = self::$psr4->get($requestedClass);
				for ($wc = 0; $wc < count($workingClassPaths); ++$wc) {
					$workingClassFile =
						self::$psr4->get($requestedClass)[$wc] . '/' .
						implode('/', $classV->slice($p + 1)->toArray()) . '.php';

					if (file_exists($workingClassFile)) {
						require $workingClassFile;
						return true;
					}
				}
			}
		}

		return false;
	}

	public static function loadFiles()
	{
		foreach (require self::$root . '/composer/autoload_files.php' as $file) {
			require_once $file;
		}
	}
}
