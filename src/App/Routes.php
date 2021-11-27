<?php

declare(strict_types=1);

namespace Api\App;

use Slim\App;
use Slim\Factory\AppFactory;
use Slim\Routing\RouteCollectorProxy;

use Api\Controllers\CategoryController;
use Api\Controllers\CompanyController;
use Api\Controllers\ProductController;


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

        self::$app->group('/category', function (RouteCollectorProxy $group): void {
            $group->get('', CategoryController::class . ':index');
            $group->post('', CategoryController::class . ':create');
            $group->get('/{id}', CategoryController::class . ':show');
            $group->put('/{id}', CategoryController::class . ':update');
            $group->delete('/{id}', CategoryController::class . ':delete');
        });

        self::$app->group('/company', function (RouteCollectorProxy $group): void {
            $group->get('', CompanyController::class . ':index');
            $group->post('', CompanyController::class . ':create');
            $group->get('/{id}', CompanyController::class . ':show');
            $group->put('/{id}', CompanyController::class . ':update');
            $group->delete('/{id}', CompanyController::class . ':delete');
        });

        self::$app->group('/product', function (RouteCollectorProxy $group): void {
            $group->get('', ProductController::class . ':index');
            $group->post('', ProductController::class . ':create');
            $group->get('/{id}', ProductController::class . ':show');
            $group->get('/category/{id}', ProductController::class . ':showProductInCategory');
            $group->get('/company/{id}', ProductController::class . ':showProductInCompany');
            $group->put('/{id}', ProductController::class . ':update');
            $group->delete('/{id}', ProductController::class . ':delete');
        });

        return self::$app;
    }
}
