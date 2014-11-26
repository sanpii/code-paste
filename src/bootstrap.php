<?php

use \Silex\Provider\TwigServiceProvider;
use \Silex\Provider\WebProfilerServiceProvider;
use \Silex\Provider\UrlGeneratorServiceProvider;
use \Silex\Provider\ServiceControllerServiceProvider;
use \PommProject\Silex\ServiceProvider\PommServiceProvider;

require_once __DIR__ . '/../vendor/autoload.php';

if (!is_file(__DIR__ . '/config/current.php')) {
    throw new \RunTimeException('No current configuration file found in config.');
}

$app = new \Silex\Application();

$app['config'] = $app->share(function () use($app) {
    $config = require __DIR__ . '/config/current.php';
    $config['pomm.configuration']['code']['class:session_builder'] = '\Model\SessionBuilder';

    return $config;
});

$app['debug'] = $app['config']['debug'];

$app->register(new TwigServiceProvider(), [
    'twig.path' => __DIR__ . '/views',
]);

$databaseConfig = $app['config']['pomm'];
foreach ($databaseConfig as $name => $values) {
    $databaseConfig[$name]['class'] = '\Model\Database';
}
$app->register(new PommServiceProvider(), [
    'pomm.class_path' => __DIR__ . '/vendor/pomm',
    'pomm.databases' => $databaseConfig,
]);

$app['geshi'] = function() use ($app) {
    return new GeSHi();
};

if (class_exists('\Silex\Provider\WebProfilerServiceProvider')) {
    $app->register(new UrlGeneratorServiceProvider());
    $app->register(new ServiceControllerServiceProvider());

    $profiler = new WebProfilerServiceProvider();
    $app->register($profiler, [
        'profiler.cache_dir' => __DIR__ . '/../cache/profiler',
    ]);
    $app->mount('/_profiler', $profiler);
}

return $app;
