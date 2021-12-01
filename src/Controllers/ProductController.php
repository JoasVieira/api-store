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

        $namePhoto = bin2hex(random_bytes(10)) .'.png';
        $path = '../public/images/' . $namePhoto;
        file_put_contents($path, base64_decode($productRequest->photo));

        $product = new Product;
        $product->name = $productRequest->name;
        $product->photo = $namePhoto;
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

    public function showImage(Request $request, Response $response, $args): Response
    {
        $id = $args['id'];

        $files = file_get_contents('../public/images/' . $id);

        $response->getBody()->write($files);
        return $response->withHeader('Content-type', 'image/png')
                        ->withStatus(200);
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

    public function showProductInCategory(Request $request, Response $response, $args): Response
    {
        $condition = $args['id'];

        $sql = "select * from product where category_id = $condition";

        $products = $this->query->executeSql($sql, Product::class);

        if (is_null($products)) {
            return JsonResponse::create(
                $response,
                ['message' => 'There are no products in this category'],
                StatusCodeInterface::STATUS_NOT_FOUND
            );
        }

        return JsonResponse::create(
            $response,
            $products,
            StatusCodeInterface::STATUS_OK
        );
    }

    public function showProductInCompany(Request $request, Response $response, $args): Response
    {
        $condition = $args['id'];

        $sql = "select * from product where company_id = $condition";

        $products = $this->query->executeSql($sql, Product::class);

        if (is_null($products)) {
            return JsonResponse::create(
                $response,
                ['message' => 'There are no products in this company'],
                StatusCodeInterface::STATUS_NOT_FOUND
            );
        }

        return JsonResponse::create(
            $response,
            $products,
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
