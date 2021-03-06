<?php

declare(strict_types=1);

namespace Api\App;

use Slim\App;
use Slim\Factory\AppFactory;
use Slim\Routing\RouteCollectorProxy;

use Api\Controllers\CategoryController;
use Api\Controllers\CompanyController;
use Api\Controllers\InfoController;
use Api\Controllers\ProductController;

use Api\Middlewares\LogMiddleware;
use Api\Middlewares\CacheMiddleware; 

class Routes
{
    private static $app;

    public static function create(): App
    {
        self::$app = AppFactory::create();

        self::$app->options('/{routes:.+}', function ($request, $response, $args) {
            return $response;
        });

        self::$app->add(function ($request, $handler) {
            $response = $handler->handle($request);
            return $response
                ->withHeader('Access-Control-Allow-Origin', '*')
                ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
                ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, PATCH, OPTIONS');
        });

        self::$app->group('/', function (RouteCollectorProxy $group): void {
            $group->get('', InfoController::class . ':home');
            $group->get('server', InfoController::class . ':server');
        })->add(new CacheMiddleware())
          ->add(new LogMiddleware());

        self::$app->group('/category', function (RouteCollectorProxy $group): void {
            $group->get('', CategoryController::class . ':index')->add(new CacheMiddleware());
            $group->post('', CategoryController::class . ':create');
            $group->get('/{id}', CategoryController::class . ':show');
            $group->put('/{id}', CategoryController::class . ':update');
            $group->delete('/{id}', CategoryController::class . ':delete');
        })->add(new LogMiddleware());

        self::$app->group('/company', function (RouteCollectorProxy $group): void {
            $group->get('', CompanyController::class . ':index')->add(new CacheMiddleware());
            $group->post('', CompanyController::class . ':create');
            $group->get('/{id}', CompanyController::class . ':show');
            $group->put('/{id}', CompanyController::class . ':update');
            $group->delete('/{id}', CompanyController::class . ':delete');
        })->add(new LogMiddleware());

        self::$app->group('/product', function (RouteCollectorProxy $group): void {
            $group->get('', ProductController::class . ':index')->add(new CacheMiddleware());
            $group->post('', ProductController::class . ':create');
            $group->get('/{id}', ProductController::class . ':show');
            $group->get('/image/{id}', ProductController::class . ':showImage');
            $group->get('/category/{id}', ProductController::class . ':showProductInCategory');
            $group->get('/company/{id}', ProductController::class . ':showProductInCompany');
            $group->put('/{id}', ProductController::class . ':update');
            $group->delete('/{id}', ProductController::class . ':delete');
        })->add(new LogMiddleware());

        return self::$app;
    }
}
