<?php

namespace App\Controllers;

use App\Database\DB;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class CustomerController
{
    private string $collection = 'customers';

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
            $customers = DB::find($this->collection);
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
            $customer = DB::findOne($this->collection, ['_id' => $id]);
            
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

            $data = [
                'name' => $body['name'],
                'phone' => $body['phone'] ?? '',
                'address' => $body['address'] ?? '',
                'created_at' => date('c'),
                'updated_at' => date('c')
            ];

            $id = DB::insert($this->collection, $data);
            $data['id'] = $id;

            $response->getBody()->write(json_encode($data));
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

            $existing = DB::findOne($this->collection, ['_id' => $id]);
            if (!$existing) {
                $response->getBody()->write(json_encode(['error' => 'Customer not found']));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
            }

            $updateData = [
                'name' => $body['name'] ?? $existing['name'],
                'phone' => $body['phone'] ?? $existing['phone'],
                'address' => $body['address'] ?? $existing['address'],
                'updated_at' => date('c')
            ];

            DB::update($this->collection, ['_id' => $id], ['$set' => $updateData]);
            
            $updated = DB::findOne($this->collection, ['_id' => $id]);
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
            $existing = DB::findOne($this->collection, ['_id' => $id]);
            
            if (!$existing) {
                $response->getBody()->write(json_encode(['error' => 'Customer not found']));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
            }

            DB::delete($this->collection, ['_id' => $id], true);
            
            $response->getBody()->write(json_encode(['message' => 'Customer deleted successfully']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode(['error' => $e->getMessage()]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }
}
