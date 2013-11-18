<?php

use \Symfony\Component\HttpFoundation\Request;
use \Symfony\Component\HttpFoundation\Response;
use \Symfony\Component\HttpKernel\HttpKernelInterface;

$app = require __DIR__ . '/bootstrap.php';

$must_be_logged = function() use($app) {
    $response = null;

    if (!isset($_SERVER['PHP_AUTH_USER'])) {
        header('WWW-Authenticate: Basic realm="Code paste"');
        $response =  $app->abort(401, 'Not Authorised');
    }
    else {
        $author = $app['pomm.connection']->getMapFor('\Model\Author')
            ->findWhere('name = ? AND password = ?', [
                $_SERVER['PHP_AUTH_USER'],
                hash('sha512', $_SERVER['PHP_AUTH_PW']),
            ])
            ->current();
        if($author !== false) {
            $app['user'] = $author;
        }
        else {
            $response = $app->abort(403, 'Forbidden');
        }
    }
    return $response;
};

$app->get('/', function(Request $request) use($app) {
    return $app->handle(
        Request::create('/search', 'GET', $request->request->all()),
        HttpKernelInterface::SUB_REQUEST
    );
});

$app->get('/search', function(Request $request) use($app) {
    $page = $request->get('page', 1);
    $query = $request->get('q', '');

    $pager = $app['pomm.connection']->getMapFor('\Model\Snippet')
        ->search($query, 25, $page);

    return $app['twig']->render(
        'search.html.twig',
        [
            'pager' => $pager,
            'q' => $query,
        ]
    );
});

$app->get('/show/{id}', function($id) use($app) {
    $snippet = $app['pomm.connection']->getMapFor('\Model\Snippet')
        ->findByPk(compact('id'));
    if (is_null($snippet)) {
        $app->abort(404, "Snippet $id not found");
    }

    $geshi = $app['geshi'];
    foreach ($snippet->codes as $code) {
        $geshi->set_language($code->language);
        $geshi->set_source($code->content);
        $code->source = $geshi->parse_code();
    }

    return $app['twig']->render(
        'show.html.twig',
        [
            'snippet' => $snippet->extract(),
        ]
    );
});

$app->get('/add', function() use($app) {
    return $app->handle(
        Request::create('/edit/-1', 'GET'),
        HttpKernelInterface::SUB_REQUEST
    );
})->before($must_be_logged);

$app->post('/add', function(Request $request) use($app) {
    return $app->handle(
        Request::create('/edit/-1', 'PUT', $request->request->all()),
        HttpKernelInterface::SUB_REQUEST
    );
})->before($must_be_logged);

$app->get('/edit/{id}', function($id) use($app) {
    $map = $app['pomm.connection']->getMapFor('\Model\Snippet');

    if ($id > 0) {
        $snippet = $map->findByPk(compact('id'));
        if (is_null($snippet)) {
            $app->abort(404, "Snippet $id not found");
        }
    }
    else {
        $snippet = $map->createObject([
            'title' => '',
            'codes' => [],
            'keywords' => [],
        ]);
    }

    $data = $snippet->extract();
    $data['keywords'][] = '';
    $data['codes'][] = [
        'name' => '',
        'content' => '',
        'language' => 'text',
    ];

    return $app['twig']->render(
        'edit.html.twig',
        [
            'snippet' => $data,
            'languages' => $app['geshi']->get_supported_languages(true),
        ]
    );
})->before($must_be_logged);

$app->put('/edit/{id}', function(Request $request, $id) use($app) {
    $map = $app['pomm.connection']->getMapFor('\Model\Snippet');

    if ($id > 0) {
        $snippet = $map->findByPk(compact('id'));
        if (is_null($snippet)) {
            $app->abort(404, "Snippet $id not found");
        }
    }
    else {
        $snippet = $map->createObject();
        $snippet->author_id = $app['user']->id;
    }

    $snippet->hydrate($request->request->get('snippet'));
    $map->saveOne($snippet);

    return $app->redirect("/show/{$snippet->id}");
})->before($must_be_logged);

$app->get('/delete/{id}', function($id) use($app) {
    $snippet = $app['pomm.connection']->getMapFor('\Model\Snippet')
        ->findByPk(compact('id'));
    if (is_null($snippet)) {
        $app->abort(404, "Snippet $id not found");
    }

    return $app['twig']->render(
        'delete.html.twig',
        [
            'snippet' => $snippet->extract(),
        ]
    );
})->before($must_be_logged);

$app->delete('/delete/{id}', function($id) use($app) {
    $map = $app['pomm.connection']->getMapFor('\Model\Snippet');

    $snippet = $map->findByPk(compact('id'));
    if (is_null($snippet)) {
        $app->abort(404, "Snippet $id not found");
    }

    $map->deleteOne($snippet);

    return $app->redirect('/');
})->before($must_be_logged);

$app->get('/opensearch.xml', function(Request $request) use($app) {
    $baseurl = $request->getUriForPath('');

    $contents = $app['twig']->render(
        'opensearch.xml.twig',
        [
            'baseurl' => $baseurl,
        ]
    );

    return new Response($contents, 200, [
        'Content-Type' => 'application/xml',
    ]);
});

return $app;
