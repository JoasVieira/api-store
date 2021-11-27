<?php

declare(strict_types=1);

namespace Api\Controllers;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Fig\Http\Message\StatusCodeInterface;
use Api\App\JsonResponse;

use Api\Models\Category;
use Api\Factorys\QueryFactory;

class CategoryController
{
    private $query;

    public function __construct()
    {
        $this->query = QueryFactory::getIstance();
    }

    public function index(Request $request, Response $response, $args): Response
    {
        $categorys = $this->query->findAll(Category::class);

        return JsonResponse::create(
            $response,
            $categorys,
            StatusCodeInterface::STATUS_OK
        );
    }

    public function create(Request $request, Response $response, $args): Response
    {
        $categoryRequest = json_decode($request->getBody()->getContents());

        $category = new Category;
        $category->name = $categoryRequest->name;

        $id = $this->query->insert($category);

        $newCategory = $this->query->find($id, Category::class);

        return JsonResponse::create(
            $response,
            $newCategory,
            StatusCodeInterface::STATUS_CREATED
        );
    }

    public function show(Request $request, Response $response, $args): Response
    {
        $id = $args['id'];
        $category = $this->query->find($id, Category::class);
        if (is_null($category)) {
            return JsonResponse::create(
                $response,
                ['message' => 'Category does not exist'],
                StatusCodeInterface::STATUS_NOT_FOUND
            );
        }

        return JsonResponse::create(
            $response,
            $category,
            StatusCodeInterface::STATUS_OK
        );
    }

    public function update(Request $request, Response $response, $args): Response
    {
        $id = $args['id'];

        $category = $this->query->find($id, Category::class);
        if (is_null($category)) {
            return $response->withStatus(404);
        }

        $categoryRequest = json_decode($request->getBody()->getContents());

        $category->name = $categoryRequest->name;

        $this->query->update($category);

        $newCategory = $this->query->find($id, Category::class);

        return JsonResponse::create(
            $response,
            $newCategory,
            StatusCodeInterface::STATUS_OK
        );
    }

    public function delete(Request $request, Response $response, $args): Response
    {
        $category = $this->query->find($args['id'], Category::class);
        if (is_null($category)) {
            return JsonResponse::create(
                $response,
                ['message' => 'Category does not exist'],
                StatusCodeInterface::STATUS_NOT_FOUND
            );
        }
        $this->query->delete($category);
        return JsonResponse::create(
            $response,
            ['success' => true],
            StatusCodeInterface::STATUS_NO_CONTENT
        );
    }
}
