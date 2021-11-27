<?php

declare(strict_types=1);

namespace Api\Controllers;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Fig\Http\Message\StatusCodeInterface;
use Api\App\JsonResponse;

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
        $products = $this->query->findAll(Product::class);

        return JsonResponse::create(
            $response,
            $products,
            StatusCodeInterface::STATUS_OK
        );
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

        return JsonResponse::create(
            $response,
            $newProduct,
            StatusCodeInterface::STATUS_CREATED
        );
    }

    public function show(Request $request, Response $response, $args): Response
    {
        $id = $args['id'];
        $product = $this->query->find($id, Product::class);
        if (is_null($product)) {
            return JsonResponse::create(
                $response,
                ['message' => 'Product does not exist'],
                StatusCodeInterface::STATUS_NOT_FOUND
            );
        }

        return JsonResponse::create(
            $response,
            $product,
            StatusCodeInterface::STATUS_OK
        );
    }

    public function update(Request $request, Response $response, $args): Response
    {
        $id = $args['id'];

        $product = $this->query->find($id, Product::class);
        if (is_null($product)) {
            return $response->withStatus(404);
        }

        $productRequest = json_decode($request->getBody()->getContents());

        $product->name = $productRequest->name;
        $product->photo = $productRequest->photo;
        $product->description = $productRequest->description;
        $product->price = $productRequest->price;
        $product->category_id = $productRequest->category_id;
        $product->company_id = $productRequest->company_id;

        $this->query->update($product);

        $newProduct = $this->query->find($id, Product::class);

        return JsonResponse::create(
            $response,
            $newProduct,
            StatusCodeInterface::STATUS_OK
        );
    }

    public function delete(Request $request, Response $response, $args): Response
    {
        $product = $this->query->find($args['id'], Product::class);
        if (is_null($product)) {
            return JsonResponse::create(
                $response,
                ['message' => 'Product does not exist'],
                StatusCodeInterface::STATUS_NOT_FOUND
            );
        }
        $this->query->delete($product);
        return JsonResponse::create(
            $response,
            ['success' => true],
            StatusCodeInterface::STATUS_NO_CONTENT
        );
    }
}
