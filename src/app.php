<?php

use \Symfony\Component\HttpFoundation\Request;
use \Symfony\Component\HttpFoundation\Response;

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

$app->get('/edit/{id}', function($id) use($app) {
    $snippet = $app['pomm']->getMapFor('\Model\Snippet')
        ->findByPk(array('id' => $id));;
    if (is_null($snippet)) {
        return new Response("Snippet $id not found", 404);
    }

    return $app['twig']->render(
        'edit.html.twig',
        array(
            'snippet' => $snippet->extract(),
            'languages' => array('php', 'text'),
        )
    );
});

$app->put('/edit/{id}', function(Request $request, $id) use($app) {
    $map = $app['pomm']->getMapFor('\Model\Snippet');

    $snippet = $map->findByPk(array('id' => $id));;
    if (is_null($snippet)) {
        return new Response("Snippet $id not found", 404);
    }

    $snippet->hydrate($request->request->get('snippet'));
    $map->saveOne($snippet);

    return $app->redirect("/show/{$snippet->id}");
});

return $app;
