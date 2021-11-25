<?php

declare(strict_types=1);

namespace Api\Controllers;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

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
        $response->getBody()->write(json_encode($this->query->findAll(Category::class)));
        return $response->withHeader('Content-Type', 'application/json')
            ->withStatus(200);
    }

    public function create(Request $request, Response $response, $args): Response
    {
        $categoryRequest = json_decode($request->getBody()->getContents());

        $category = new Category;
        $category->name = $categoryRequest->name;

        $id = $this->query->insert($category);

        $newCategory = $this->query->find($id, Category::class);

        $response->getBody()->write(json_encode($newCategory));
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(201);
    }

    public function show(Request $request, Response $response, $args): Response
    {
        $id = $args['id'];
        $category = $this->query->find($id, Category::class);
        if (is_null($category)) {
            return $response->withStatus(404);
        }

        $response->getBody()->write(json_encode($category));
        return $response
            ->withHeader('Content-Type', 'application/json');
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

        $response->getBody()->write(json_encode($this->query->find($id, Category::class)));
        return $response->withHeader('Content-Type', 'application/json')
            ->withStatus(200);
    }

    public function delete(Request $request, Response $response, $args): Response
    {
        $category = $this->query->find($args['id'], Category::class);
        if (is_null($category)) {
            return $response->withStatus(404);
        }
        $this->query->delete($category);
        return $response->withStatus(204);
    }
}
