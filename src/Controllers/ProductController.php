<?php

declare(strict_types=1);

namespace Api\Controllers;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

use Api\Models\Product;
use Api\Factorys\QueryFactory;


class ProductController
{
    private $query;

    public function __construct()
    {
        $this->query = QueryFactory::getIstance();
    }

    public function index(Request $request, Response $response, $args): Response
    {
        $response->getBody()->write(json_encode($this->query->findAll(Product::class)));
        return $response->withHeader('Content-Type', 'application/json')
            ->withStatus(200);
    }

    public function create(Request $request, Response $response, $args): Response
    {
        $productRequest = json_decode($request->getBody()->getContents());

        $product = new Product;
        $product->name = $productRequest->name;
        $product->photo = $productRequest->photo;
        $product->description = $productRequest->description;
        $product->price = $productRequest->price;
        $product->category_id = $productRequest->category_id;
        $product->company_id = $productRequest->company_id;

        $id = $this->query->insert($product);

        $newProduct = $this->query->find($id, Product::class);

        $response->getBody()->write(json_encode($newProduct));
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(201);
    }

    public function show(Request $request, Response $response, $args): Response
    {
        $id = $args['id'];
        $product = $this->query->find($id, Product::class);
        if (is_null($product)) {
            return $response->withStatus(404);
        }

        $response->getBody()->write(json_encode($product));
        return $response
            ->withHeader('Content-Type', 'application/json');
    }

    public function update(Request $request, Response $response, $args): Response
    {
        $id = $args['id'];

        $product = $this->query->find($id, Product::class);
        if (is_null($product)) {
            return $response->withStatus(404);
        }

        $productRequest = json_decode($request->getBody()->getContents());

        $product = new Product;
        $product->name = $productRequest->name;
        $product->photo = $productRequest->photo;
        $product->description = $productRequest->description;
        $product->price = $productRequest->price;
        $product->category_id = $productRequest->category_id;
        $product->company_id = $productRequest->company_id;


        $this->query->update($product);

        $response->getBody()->write(json_encode($this->query->find($id, Product::class)));
        return $response->withHeader('Content-Type', 'application/json')
            ->withStatus(200);
    }

    public function delete(Request $request, Response $response, $args): Response
    {
        $product = $this->query->find($args['id'], Product::class);
        if (is_null($product)) {
            return $response->withStatus(404);
        }
        $this->query->delete($product);
        return $response->withStatus(204);
    }
}
