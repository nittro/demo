<?php

declare(strict_types=1);

namespace App;

use Nette\Bootstrap\Configurator;


class Bootstrap
{
	public static function boot(): Configurator
	{
		$configurator = new Configurator;
		$appDir = dirname(__DIR__);

		self::checkRequirements($appDir);
		self::initResources($appDir);

		$configurator->setDebugMode(true);
		$configurator->enableTracy($appDir . '/var/log');

		$configurator->setTimeZone('Europe/Prague');
		$configurator->setTempDirectory($appDir . '/var');

		$configurator->createRobotLoader()
			->addDirectory(__DIR__)
			->register();

		$configurator->addConfig($appDir . '/etc/config.neon');

		if (file_exists($appDir . '/etc/local.neon')) {
      $configurator->addConfig($appDir . '/etc/local.neon');
    }

		return $configurator;
	}

	private static function checkRequirements(string $appDir): void
  {
    if (PHP_SAPI !== 'cli-server' && PHP_SAPI !== 'cli') {
      header('HTTP/1.1 403 Forbidden');
      echo '<h1>Please run the demo using the PHP built-in webserver.</h1>';
      echo '<p>The demo uses a SQLite3 database and you could run into all sorts of trouble running it under Apache.</p>';
      exit;
    }

    if (!is_dir($appDir . '/node_modules/bootstrap/dist/fonts')) {
      header('HTTP/1.1 503 Service Temporarily Unavailable');
      echo '<h1>Static assets not found</h1>';
      echo '<p>Please run <code>npm install</code> in the root directory of the project.</p>';
      exit;
    }
  }

  private static function initResources(string $appDir): void
  {
    @mkdir($appDir . '/var/log', 0755, true);

    if (!file_exists($appDir . '/var/db/blog.s3db')) {
      @mkdir($appDir . '/var/db', 0755, true);
      copy(__DIR__ . '/Model/Resources/db/blog.dist.s3db', $appDir . '/var/db/blog.s3db');
    }

    if (!file_exists($appDir . '/public/images/posts/1.jpg')) {
      @mkdir($appDir . '/public/images/posts', 0755, true);
      copy(__DIR__ . '/Model/Resources/images/1.jpg', $appDir . '/public/images/posts/1.jpg');
      copy(__DIR__ . '/Model/Resources/images/2.jpg', $appDir . '/public/images/posts/2.jpg');
      copy(__DIR__ . '/Model/Resources/images/3.jpg', $appDir . '/public/images/posts/3.jpg');
    }

    if (!file_exists($appDir . '/public/css/bootstrap')) {
      symlink($appDir . '/node_modules/bootstrap/dist', $appDir . '/public/css/bootstrap');
    }
  }
}
