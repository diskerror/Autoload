<?php

namespace Diskerror;

use Ds\Map;
use function array_slice;
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
	private static $classmap;

	/**
	 * @var Map
	 */
	private static $namespaces;

	/**
	 * @var Map
	 */
	private static $psr4;

	/**
	 * @var array
	 */
	private static $files;

	/**
	 * Initialize.
	 * Gather Composer generated files.
	 *
	 * @return void
	 */
	public static function init()
	{
		$classmapFile   = __DIR__ . '/../../composer/autoload_classmap.php';
		$namespacesFile = __DIR__ . '/../../composer/autoload_namespaces.php';
		$psr4File       = __DIR__ . '/../../composer/autoload_psr4.php';
		$filesFile      = __DIR__ . '/../../composer/autoload_files.php';

		self::$classmap   = file_exists($classmapFile) ? new Map(require $classmapFile) : new Map();
		self::$namespaces = file_exists($namespacesFile) ? new Map(require $namespacesFile) : new Map();
		self::$psr4       = file_exists($psr4File) ? new Map(require $psr4File) : new Map();
		self::$files      = file_exists($filesFile) ? require $filesFile : [];
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

		$classA     = explode('\\', $class);
		$classDepth = count($classA);

		//	TODO: Need sample data to test.
		$requestedClass = '';
		for ($cd = 0; $cd < $classDepth; ++$cd) {
			$requestedClass .= $classA[$cd] . '\\';
			if (self::$namespaces->hasKey($requestedClass)) {
				$workingClassFile =
					self::$namespaces->get($requestedClass) . '/' .
					implode('/', array_slice($classA, $cd + 1)) . '.php';

				if (file_exists($workingClassFile)) {
					require $workingClassFile;
					return true;
				}
			}
		}

		$requestedClass = '';
		for ($cd = 0; $cd < $classDepth; ++$cd) {
			$requestedClass .= $classA[$cd] . '\\';
			if (self::$psr4->hasKey($requestedClass)) {
				$workingClassPaths = self::$psr4->get($requestedClass);
				$pathCount         = count($workingClassPaths);
				for ($wc = 0; $wc < $pathCount; ++$wc) {
					$workingClassFile =
						$workingClassPaths[$wc] . '/' .
						implode('/', array_slice($classA, $cd + 1)) . '.php';

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
		foreach (self::$files as $file) {
			require_once $file;
		}
	}
}
