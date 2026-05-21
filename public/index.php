<?php
declare(strict_types=1);

use App\Controller\ArticleController;
use App\Controller\CategoryController;
use App\Controller\HomeController;
use App\Core\Container;
use App\Core\NotFoundException;
use App\Core\Request;
use App\Core\Router;

require dirname(__DIR__) . '/vendor/autoload.php';

$container = new Container(dirname(__DIR__));
$router = new Router();

$router->get('/', static function () use ($container): void {
    (new HomeController(
        $container->view(),
        $container->categoryRepository(),
        $container->articleRepository(),
    ))->index();
});

$router->get('/category/{slug}', static function (array $params) use ($container): void {
    (new CategoryController(
        $container->view(),
        $container->categoryRepository(),
        $container->articleRepository(),
    ))->show($params);
});

$router->get('/article/{slug}', static function (array $params) use ($container): void {
    (new ArticleController(
        $container->view(),
        $container->articleRepository(),
    ))->show($params);
});

try {
    $router->dispatch(Request::method(), Request::path());
} catch (NotFoundException) {
    http_response_code(404);
    $container->view()->display('404', ['title' => 'Page not found']);
} catch (Throwable $e) {
    http_response_code(500);
    error_log($e->getMessage() . "\n" . $e->getTraceAsString());
    echo 'Internal server error';
}
