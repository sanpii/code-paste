<?php

use \Symfony\Component\HttpFoundation\Request;
use \Symfony\Component\HttpFoundation\Response;
use \Symfony\Component\HttpKernel\HttpKernelInterface;

$app = require __DIR__ . '/bootstrap.php';

$app->get('/', function(Request $request) use($app) {
    return $app->handle(
        Request::create('/search', 'GET', $request->request->all()),
        HttpKernelInterface::SUB_REQUEST
    );
});

$app->get('/search', function(Request $request) use($app) {
    $page = $request->get('page', 1);
    $query = $request->get('q', '');

    $pager = $app['pomm']->getMapFor('\Model\Snippet')
        ->search($query, 25, $page);

    return $app['twig']->render(
        'search.html.twig',
        array(
            'pager' => $pager,
            'q' => $query,
        )
    );
});

$app->get('/show/{id}', function($id) use($app) {
    $snippet = $app['pomm']->getMapFor('\Model\Snippet')
        ->findByPk(array('id' => $id));
    if (is_null($snippet)) {
        $app->abort(404, "Snippet $id not found");
    }

    $geshi = $app['geshi'];
    $geshi->set_language($snippet->language);
    $geshi->set_source($snippet->code);
    $snippet->source = $geshi->parse_code();

    return $app['twig']->render(
        'show.html.twig',
        array(
            'snippet' => $snippet->extract(),
        )
    );
});

$app->get('/add', function() use($app) {
    return $app->handle(
        Request::create('/edit/-1', 'GET'),
        HttpKernelInterface::SUB_REQUEST
    );
});

$app->post('/add', function(Request $request) use($app) {
    return $app->handle(
        Request::create('/edit/-1', 'PUT', $request->request->all()),
        HttpKernelInterface::SUB_REQUEST
    );
});

$app->get('/edit/{id}', function($id) use($app) {
    $map = $app['pomm']->getMapFor('\Model\Snippet');

    if ($id > 0) {
        $snippet = $map->findByPk(array('id' => $id));
        if (is_null($snippet)) {
            $app->abort(404, "Snippet $id not found");
        }
    }
    else {
        $snippet = $map->createObject(array(
            'title' => '',
            'code' => '',
            'keywords' => array(),
            'language' => 'text',
        ));
    }

    $data = $snippet->extract();
    $data['keywords'][] = '';

    return $app['twig']->render(
        'edit.html.twig',
        array(
            'snippet' => $data,
            'languages' => $app['geshi']->get_supported_languages(true),
        )
    );
});

$app->put('/edit/{id}', function(Request $request, $id) use($app) {
    $map = $app['pomm']->getMapFor('\Model\Snippet');

    if ($id > 0) {
        $snippet = $map->findByPk(array('id' => $id));
        if (is_null($snippet)) {
            $app->abort(404, "Snippet $id not found");
        }
    }
    else {
        $snippet = $map->createObject();
    }

    $snippet->hydrate($request->request->get('snippet'));
    $map->saveOne($snippet);

    return $app->redirect("/show/{$snippet->id}");
});

$app->get('/delete/{id}', function($id) use($app) {
    $snippet = $app['pomm']->getMapFor('\Model\Snippet')
        ->findByPk(array('id' => $id));
    if (is_null($snippet)) {
        $app->abort(404, "Snippet $id not found");
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
        $app->abort(404, "Snippet $id not found");
    }

    $map->deleteOne($snippet);

    return $app->redirect('/');
});

return $app;
