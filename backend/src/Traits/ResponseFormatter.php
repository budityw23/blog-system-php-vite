<?php
namespace App\Traits;

use Psr\Http\Message\ResponseInterface as Response;

trait ResponseFormatter
{
    protected function jsonResponse(Response $response, $data, int $status = 200): Response
    {
        $payload = json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        
        $response->getBody()->write($payload);
        
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus($status);
    }

    protected function successResponse(Response $response, $data = null): Response
    {
        return $this->jsonResponse($response, [
            'success' => true,
            'data' => $data
        ]);
    }

    protected function errorResponse(Response $response, string $message, int $status = 400): Response
    {
        return $this->jsonResponse($response, [
            'success' => false,
            'error' => $message
        ], $status);
    }
}
