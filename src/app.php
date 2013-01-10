<?php

use \Symfony\Component\HttpFoundation\Request;
use \Symfony\Component\HttpFoundation\Response;

$app = require __DIR__ . '/bootstrap.php';

$app->get('/', function(Request $request) use($app) {
    $page = $request->get('page', 1);

    $pager = $app['pomm']->getMapFor('\Model\Snippet')
        ->paginateFindWhere('1 = 1', array(), 'ORDER BY created DESC', 25, $page);

    return $app['twig']->render(
        'list.html.twig',
        array(
            'pager' => $pager,
        )
    );
});

$app->get('/show/{id}', function($id) use($app) {
    $snippet = $app['pomm']->getMapFor('\Model\Snippet')
        ->findByPk(array('id' => $id));
    if (is_null($snippet)) {
        return new Response("Snippet $id not found", 404);
    }

    return $app['twig']->render(
        'show.html.twig',
        array(
            'snippet' => $snippet->extract(),
        )
    );
});

$app->get('/add', function() use($app) {
    $snippet = $app['pomm']->getMapFor('\Model\Snippet')
        ->createObject(array(
            'title' => '',
            'code' => '',
            'keywords' => array(),
            'language' => 'text',
        ));

    return $app['twig']->render(
        'edit.html.twig',
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
        ->findByPk(array('id' => $id));
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

    $snippet = $map->findByPk(array('id' => $id));
    if (is_null($snippet)) {
        return new Response("Snippet $id not found", 404);
    }

    $snippet->hydrate($request->request->get('snippet'));
    $map->saveOne($snippet);

    return $app->redirect("/show/{$snippet->id}");
});

$app->get('/delete/{id}', function($id) use($app) {
    $snippet = $app['pomm']->getMapFor('\Model\Snippet')
        ->findByPk(array('id' => $id));
    if (is_null($snippet)) {
        return new Response("Snippet $id not found", 404);
    }

    return $app['twig']->render(
        'delete.html.twig',
        array(
            'snippet' => $snippet->extract(),
        )
    );
});

$app->delete('/delete/{id}', function($id) use($app) {
    $map = $app['pomm']->getMapFor('\Model\Snippet');

    $snippet = $map->findByPk(array('id' => $id));
    if (is_null($snippet)) {
        return new Response("Snippet $id not found", 404);
    }

    $map->deleteOne($snippet);

    return $app->redirect('/');
});

return $app;
