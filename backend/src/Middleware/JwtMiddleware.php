<?php

namespace App\Middleware;

use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Slim\Psr7\Response as SlimResponse;

class JwtMiddleware implements MiddlewareInterface
{
    private $allowedRoles;

    public function __construct(array $allowedRoles = [])
    {
        $this->allowedRoles = $allowedRoles;
    }

    public function process(Request $request, RequestHandlerInterface $handler): Response
    {
        $headers = $request->getHeaders();
        
        // Get path and extract ID
        $path = $request->getUri()->getPath();
        
        // Extract ID from path
        $pathParts = explode('/', trim($path, '/'));
        $id = end($pathParts);  // Get the last segment
        

        // Store ID as request attribute
        $request = $request->withAttribute('id', $id);

        if (!isset($headers['Authorization'][0])) {
            return $this->createJsonResponse(['error' => 'No token provided'], 401);
        }

        try {
            $authHeader = $headers['Authorization'][0];
            $token = str_replace('Bearer ', '', $authHeader);
            
            if (empty($token)) {
                return $this->createJsonResponse(['error' => 'Empty token'], 401);
            }

            $decoded = JWT::decode($token, new Key($_ENV['JWT_SECRET'], 'HS256'));
            
            if (!empty($this->allowedRoles) && !in_array($decoded->role, $this->allowedRoles)) {
                return $this->createJsonResponse([
                    'error' => 'Unauthorized',
                    'userRole' => $decoded->role,
                    'requiredRoles' => $this->allowedRoles
                ], 403);
            }
            
            // Add JWT to request
            $request = $request->withAttribute('jwt', $decoded);
            
            return $handler->handle($request);
        } catch (\Exception $e) {
            return $this->createJsonResponse([
                'error' => 'Invalid token',
                'details' => $e->getMessage()
            ], 401);
        }
    }
    

    private function createJsonResponse(array $data, int $statusCode): Response
    {
        $response = new SlimResponse($statusCode);
        $response = $response->withHeader('Content-Type', 'application/json');
        $response->getBody()->write(json_encode($data));
        
        return $response;
    }
}
