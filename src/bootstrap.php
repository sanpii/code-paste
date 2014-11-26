<?php

use \Silex\Provider;
use \PommProject\Silex\ {
    ServiceProvider\PommServiceProvider,
    ProfilerServiceProvider\PommProfilerServiceProvider
};

require_once __DIR__ . '/../vendor/autoload.php';

if (!is_file(__DIR__ . '/config/current.php')) {
    throw new \RunTimeException('No current configuration file found in config.');
}

$app = new \Silex\Application();

$app['config'] = function () use($app) {
    $config = require __DIR__ . '/config/current.php';
    $config['pomm.configuration']['code']['class:session_builder'] = '\Model\SessionBuilder';

    return $config;
};

$app['debug'] = $app['config']['debug'];

$app->register(new Provider\TwigServiceProvider, [
    'twig.path' => __DIR__ . '/views',
]);

$app->register(new PommServiceProvider, $app['config']);

$app['db'] = function () use($app) {
    return $app['pomm']['code'];
};

$app['geshi'] = function() use ($app) {
    return new GeSHi();
};

if (class_exists('\Silex\Provider\WebProfilerServiceProvider')) {
    $app->register(new Provider\ServiceControllerServiceProvider);
    $app->register(new Provider\HttpFragmentServiceProvider);

    $app->register(new Provider\WebProfilerServiceProvider, [
        'profiler.cache_dir' => __DIR__ . '/../cache/profiler',
        'profiler.mount_prefix' => '/_profiler',
    ]);

    $app->register(new PommProfilerServiceProvider);
}

return $app;
