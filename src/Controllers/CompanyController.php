<?php

declare(strict_types=1);

namespace Api\Controllers;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

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
        $response->getBody()->write(json_encode($this->query->findAll(Company::class)));
        return $response->withHeader('Content-Type', 'application/json')
            ->withStatus(200);
    }

    public function create(Request $request, Response $response, $args): Response
    {
        $companyRequest = json_decode($request->getBody()->getContents());

        $company = new Company;
        $company->name = $companyRequest->name;
        $company->whatsapp = $companyRequest->whatsapp;

        $id = $this->query->insert($company);

        $newCompany = $this->query->find($id, Company::class);

        $response->getBody()->write(json_encode($newCompany));
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(201);
    }

    public function show(Request $request, Response $response, $args): Response
    {
        $id = $args['id'];
        $company = $this->query->find($id, Company::class);
        if (is_null($company)) {
            return $response->withStatus(404);
        }

        $response->getBody()->write(json_encode($company));
        return $response
            ->withHeader('Content-Type', 'application/json');
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

        $response->getBody()->write(json_encode($this->query->find($id, Company::class)));
        return $response->withHeader('Content-Type', 'application/json')
            ->withStatus(200);
    }

    public function delete(Request $request, Response $response, $args): Response
    {
        $company = $this->query->find($args['id'], Company::class);
        if (is_null($company)) {
            return $response->withStatus(404);
        }
        $this->query->delete($company);
        return $response->withStatus(204);
    }
}
