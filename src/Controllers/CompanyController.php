<?php

declare(strict_types=1);

namespace Api\Controllers;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Fig\Http\Message\StatusCodeInterface;
use Api\App\JsonResponse;

use Api\Models\Company;
use Api\Factorys\QueryFactory;


class CompanyController
{
    private $query;

    public function __construct()
    {
        $this->query = QueryFactory::getIstance();
    }

    public function index(Request $request, Response $response, $args): Response
    {
        $companys = $this->query->findAll(Company::class);

        return JsonResponse::create(
            $response,
            $companys,
            StatusCodeInterface::STATUS_OK
        );
    }

    public function create(Request $request, Response $response, $args): Response
    {
        $companyRequest = json_decode($request->getBody()->getContents());

        $company = new Company;
        $company->name = $companyRequest->name;
        $company->whatsapp = $companyRequest->whatsapp;

        $id = $this->query->insert($company);

        $newCompany = $this->query->find($id, Company::class);

        return JsonResponse::create(
            $response,
            $newCompany,
            StatusCodeInterface::STATUS_CREATED
        );
    }

    public function show(Request $request, Response $response, $args): Response
    {
        $id = $args['id'];
        $company = $this->query->find($id, Company::class);
        if (is_null($company)) {
            return JsonResponse::create(
                $response,
                ['message' => 'Company does not exist'],
                StatusCodeInterface::STATUS_NOT_FOUND
            );
        }

        return JsonResponse::create(
            $response,
            $company,
            StatusCodeInterface::STATUS_OK
        );
    }

    public function update(Request $request, Response $response, $args): Response
    {
        $id = $args['id'];

        $company = $this->query->find($id, Company::class);
        if (is_null($company)) {
            return $response->withStatus(404);
        }

        $companyRequest = json_decode($request->getBody()->getContents());

        $company->name = $companyRequest->name;
        $company->whatsapp = $companyRequest->whatsapp;

        $this->query->update($company);

        $newCompany = $this->query->find($id, Company::class);

        return JsonResponse::create(
            $response,
            $newCompany,
            StatusCodeInterface::STATUS_OK
        );
    }

    public function delete(Request $request, Response $response, $args): Response
    {
        $company = $this->query->find($args['id'], Company::class);
        if (is_null($company)) {
            return JsonResponse::create(
                $response,
                ['message' => 'Company does not exist'],
                StatusCodeInterface::STATUS_NOT_FOUND
            );
        }
        $this->query->delete($company);
        return JsonResponse::create(
            $response,
            ['success' => true],
            StatusCodeInterface::STATUS_NO_CONTENT
        );
    }
}
