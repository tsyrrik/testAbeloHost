<?php
declare(strict_types=1);

use App\Core\Container;
use App\Core\NotFoundException;
use App\Core\Request;
use App\Core\Router;

require dirname(__DIR__) . '/vendor/autoload.php';

$container = new Container(dirname(__DIR__));
$router = new Router();

$router->get('/', static function () use ($container): void {
    $container->view()->display('home', [
        'title' => 'Blog',
        'message' => 'Skeleton is up. Pages will appear in the next steps.',
    ]);
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
