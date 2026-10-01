<?php

namespace App\Controllers;

use App\Models\Customer;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class CustomerController
{
    private function parseBody(Request $request): array
    {
        $body = $request->getParsedBody();
        if (is_array($body) && !empty($body)) {
            return $body;
        }
        $contents = $request->getBody()->getContents();
        $data = json_decode($contents, true);
        return is_array($data) ? $data : [];
    }

    public function getAll(Request $request, Response $response): Response
    {
        try {
            $customers = Customer::all();
            $response->getBody()->write(json_encode($customers));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode(['error' => $e->getMessage()]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }

    public function getById(Request $request, Response $response, array $args): Response
    {
        try {
            $id = $args['id'] ?? '';
            $customer = Customer::find($id);
            
            if (!$customer) {
                $response->getBody()->write(json_encode(['error' => 'Customer not found']));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
            }

            $response->getBody()->write(json_encode($customer));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode(['error' => $e->getMessage()]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }

    public function create(Request $request, Response $response): Response
    {
        try {
            $body = $this->parseBody($request);
            
            if (empty($body['name'])) {
                $response->getBody()->write(json_encode(['error' => 'Name is required']));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
            }

            $customer = Customer::create($body);

            $response->getBody()->write(json_encode($customer));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode(['error' => $e->getMessage()]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }

    public function update(Request $request, Response $response, array $args): Response
    {
        try {
            $id = $args['id'] ?? '';
            $body = $this->parseBody($request);

            $updated = Customer::update($id, $body);
            if (!$updated) {
                $response->getBody()->write(json_encode(['error' => 'Customer not found']));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
            }

            $response->getBody()->write(json_encode($updated));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode(['error' => $e->getMessage()]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }

    public function delete(Request $request, Response $response, array $args): Response
    {
        try {
            $id = $args['id'] ?? '';
            $success = Customer::delete($id);
            
            if (!$success) {
                $response->getBody()->write(json_encode(['error' => 'Customer not found']));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
            }

            $response->getBody()->write(json_encode(['message' => 'Customer deleted successfully']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode(['error' => $e->getMessage()]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }
}
