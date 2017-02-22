<?php

require __DIR__ . '/../vendor/autoload.php';

$configurator = new Nette\Configurator;

//$configurator->setDebugMode('23.75.345.200'); // enable for your remote IP
$configurator->enableTracy(__DIR__ . '/../log');

$configurator->setTimeZone('Europe/Prague');
$configurator->setTempDirectory(__DIR__ . '/../temp');

$configurator->createRobotLoader()
	->addDirectory(__DIR__)
	->register();

$configurator->addConfig(__DIR__ . '/config/config.neon');

if (PHP_SAPI !== 'cli-server') {
    header('HTTP/1.1 403 Forbidden');
?>
    <h1>Please run the demo using the PHP built-in webserver.</h1>
    <p>The demo uses a SQLite3 database and you could run into all sorts of trouble running it under Apache.</p>
<?php
    exit;
}

if (!file_exists(__DIR__ . '/models/blog.s3db')) {
    copy(__DIR__ . '/models/blog.dist.s3db', __DIR__ . '/models/blog.s3db');
    copy(__DIR__ . '/models/images/1.jpg', __DIR__ . '/../public/images/posts/1.jpg');
    copy(__DIR__ . '/models/images/2.jpg', __DIR__ . '/../public/images/posts/2.jpg');
    copy(__DIR__ . '/models/images/3.jpg', __DIR__ . '/../public/images/posts/3.jpg');
}

$container = $configurator->createContainer();

return $container;
