<?php

namespace Diskerror;

use Ds\Map;

/**
 * Scope wrapper for autoload functions.
 */
final class Autoloader
{
	/**
	 * @var string
	 */
	private static string $projectRoot;

	/**
	 * @var Map
	 */
	private static Map $maps;

	/**
	 * Initialize.
	 * Gather Composer generated files.
	 *
	 * @return void
	 */
	public static function init(string $projectRootIn): void {
		self::$projectRoot = $projectRootIn;

		$classmapFile   = self::$projectRoot . '/vendor/composer/autoload_classmap.php';
		$namespacesFile = self::$projectRoot . '/vendor/composer/autoload_namespaces.php';
		$psr4File       = self::$projectRoot . '/vendor/composer/autoload_psr4.php';
		$filesFile      = self::$projectRoot . '/vendor/composer/autoload_files.php';
		$autoloadCache  = self::$projectRoot . '/vendor/autoload.cache';

		if (file_exists($autoloadCache)) {
			// Try/catch in case the cache exists but is corrupted.
			try {
				self::$maps = unserialize(file_get_contents($autoloadCache));
				$mtime      = filemtime($autoloadCache);
			}
			catch (\Throwable $e) {
				self::$maps = new Map();
				$mtime      = 0;
			}
		} else {
			self::$maps = new Map();
			$mtime      = 0;
		}
		$changed = false;

		if (filemtime($classmapFile) > $mtime) {
			self::$maps['classes'] = file_exists($classmapFile) ? new Map(require $classmapFile) : new Map();
			$changed               = true;
		}

		if (filemtime($namespacesFile) > $mtime) {
			self::$maps['namespaces'] = file_exists($namespacesFile) ? new Map(require $namespacesFile) : new Map();
			$changed                  = true;
		}

		if (filemtime($psr4File) > $mtime) {
			self::$maps['psr4'] = file_exists($psr4File) ? new Map(require $psr4File) : new Map();
			$changed            = true;
		}


		if (file_exists($filesFile)) {
			if (filemtime($filesFile) > $mtime) {
				self::$maps['files'] = require $filesFile;
				$changed             = true;
			}
		} elseif (!isset(self::$maps['files']) || !empty(self::$maps['files'])) {
			self::$maps['files'] = [];
			$changed             = true;
		}

		if ($changed) {
			file_put_contents($autoloadCache, serialize(self::$maps));
		}
	}

	/**
	 * Loader.
	 * Method to be registered with autoloader.
	 *
	 * @param string $class
	 *
	 * @return bool
	 */
	public static function load(string $class) {
		//	Classes mapped directly to files.
		$classmap = self::$maps['classes'];
		if ($classmap->hasKey($class)) {
			$classFile = $classmap->get($class);
			if (file_exists($classFile)) {
				require $classFile;
				return true;
			}
		}

		$classArr   = explode('\\', $class);
		$classDepth = count($classArr);

		$reqClass   = '';
		$psr4       = self::$maps['psr4'];
		$namespaces = self::$maps['namespaces'];
		for ($cd = 0; $cd < $classDepth; ++$cd) {
			$reqClass .= $classArr[$cd] . '\\';

			//	PSR-4
			if ($psr4->hasKey($reqClass)) {
				$workingClassPaths = $psr4->get($reqClass);
				$pathCount         = count($workingClassPaths);
				for ($wc = 0; $wc < $pathCount; ++$wc) {
					$workingClassFile =
						$workingClassPaths[$wc] . '/' . implode('/', array_slice($classArr, $cd + 1)) . '.php';

					if (file_exists($workingClassFile)) {
						require $workingClassFile;
						return true;
					}
				}
			}

			if ($namespaces->hasKey($reqClass)) {
				$workingClassFile =
					$namespaces->get($reqClass) . '/' . implode('/', array_slice($classArr, $cd + 1)) . '.php';

				if (file_exists($workingClassFile)) {
					require $workingClassFile;
					return true;
				}
			}

			//	Classes mapped directly to files from the base directory.
			$fullPath = self::$projectRoot . '/' . strtr(substr($reqClass, 0, -1), '\\', '/');
			if (!is_dir($fullPath) && file_exists($fullPath . '.php')) {
				require_once $fullPath . '.php';
				return true;
			}
		}

		return false;
	}

	/**
	 * Include files.
	 *
	 * @return void
	 */
	public static function loadFiles() {
		foreach (self::$maps['files'] as $file) {
			require_once $file;
		}
	}
}
