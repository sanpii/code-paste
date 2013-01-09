<?php

use \Symfony\Component\HttpFoundation\Request;

$app = require __DIR__ . '/bootstrap.php';

$app->get('/add', function() use($app) {
    $snippet = $app['pomm']->getMapFor('\Model\Snippet')
        ->createObject(array(
            'title' => '',
            'code' => '',
            'keywords' => array(),
            'language' => 'text',
        ));

    return $app['twig']->render(
        'add.html.twig',
        array(
            'snippet' => $snippet->extract(),
            'languages' => array('php', 'text'),
        )
    );
});

$app->post('/add', function(Request $request) use($app) {
    $snippet = $app['pomm']->getMapFor('\Model\Snippet')
        ->createAndSaveObject($request->request->get('snippet'));
    return $app->redirect("/show/{$snippet->id}");
});

return $app;
