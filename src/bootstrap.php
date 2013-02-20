<?php

use \Silex\Provider\TwigServiceProvider;
use \Silex\Provider\WebProfilerServiceProvider;
use \Silex\Provider\UrlGeneratorServiceProvider;
use \Silex\Provider\ServiceControllerServiceProvider;

require_once __DIR__ . '/../vendor/autoload.php';

if (!is_file(__DIR__ . '/config/current.php')) {
    throw new \RunTimeException('No current configuration file found in config.');
}

$app = new Silex\Application();

$app['config'] = require __DIR__ . '/config/current.php';

$app['debug'] = $app['config']['debug'];

$app->register(new TwigServiceProvider(), array(
    'twig.path' => __DIR__ . '/views',
));

$app['pomm.service'] = $app->share(function() use ($app) {
    return new Pomm\Service($app['config']['pomm']);
});

$app['pomm'] = function() use ($app) {
    return $app['pomm.service']->getDatabase()
        ->registerConverter('source', new Model\Converter\Source(), array('public.source', 'source'))
        ->createConnection();
};

$app['geshi'] = function() use ($app) {
    return new GeSHi();
};

if (class_exists('\Silex\Provider\WebProfilerServiceProvider')) {
    $app->register(new UrlGeneratorServiceProvider());
    $app->register(new ServiceControllerServiceProvider());

    $profiler = new WebProfilerServiceProvider();
    $app->register($profiler, array(
        'profiler.cache_dir' => __DIR__ . '/../cache/profiler',
    ));
    $app->mount('/_profiler', $profiler);
}

return $app;
