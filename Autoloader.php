<?php

namespace Diskerror;

use Ds\Map;
use Ds\Vector;
use function basename;
use function count;
use function explode;
use function file_exists;
use function implode;

/**
 * Scope wrapper for autoload functions.
 */
final class Autoloader
{
	/**
	 * @var Map
	 */
	private static $root;

	/**
	 * @var Map
	 */
	private static $classmap;

	/**
	 * @var Map
	 */
	private static $namespaces;

	/**
	 * @var string
	 */
	private static $psr4;

	/**
	 * Initialize.
	 * Gather Composer generated files.
	 *
	 * @return void
	 */
	public static function init()
	{
		self::$root = (basename('../..') === 'vendor') ? '../..' : 'vendor';

		self::$classmap   = new Map(require self::$root . '/composer/autoload_classmap.php');
		self::$namespaces = new Map(require self::$root . '/composer/autoload_namespaces.php');
		self::$psr4       = new Map(require self::$root . '/composer/autoload_psr4.php');
	}

	/**
	 * Loader.
	 * Method to be registered with autoloader.
	 *
	 * @param string $class
	 *
	 * @return bool
	 */
	public static function load(string $class)
	{
		//	Classes mapped directly to files.
		if (self::$classmap->hasKey($class)) {
			require self::$classmap->offsetGet($class);
			return true;
		}

		$classV     = new Vector(explode('\\', $class));
		$classDepth = $classV->count();

		//	TODO: Need sample data to test.
		$requestedClass = '';
		for ($cd = 0; $cd < $classDepth; ++$cd) {
			$requestedClass .= $classV->get($cd) . '\\';
			if (self::$namespaces->hasKey($requestedClass)) {
				$workingClassFile =
					self::$namespaces->get($requestedClass) . '/' .
					implode('/', $classV->slice($cd + 1)->toArray()) . '.php';

				if (file_exists($workingClassFile)) {
					require $workingClassFile;
					return true;
				}
			}
		}

		$requestedClass = '';
		for ($cd = 0; $cd < $classDepth; ++$cd) {
			$requestedClass .= $classV->get($cd) . '\\';
			if (self::$psr4->hasKey($requestedClass)) {
				$workingClassPaths = self::$psr4->get($requestedClass);
				$pathCount         = count($workingClassPaths);
				for ($wc = 0; $wc < $pathCount; ++$wc) {
					$workingClassFile =
						$workingClassPaths[$wc] . '/' .
						implode('/', $classV->slice($cd + 1)->toArray()) . '.php';

					if (file_exists($workingClassFile)) {
						require $workingClassFile;
						return true;
					}
				}
			}
		}

		return false;
	}

	/**
	 * Include files.
	 *
	 * @return void
	 */
	public static function loadFiles()
	{
		foreach (require self::$root . '/composer/autoload_files.php' as $file) {
			require_once $file;
		}
	}
}
