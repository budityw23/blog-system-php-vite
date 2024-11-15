<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Firebase\JWT\JWT;
use App\Models\User;
use App\Traits\ResponseFormatter;

class AuthController {
    use ResponseFormatter;
    private $user;

    public function __construct(User $user) {
        $this->user = $user;
    }

    public function login(Request $request, Response $response): Response 
    {      
        $data = $request->getParsedBody();
        $email = $data['email'] ?? '';
        $password = $data['password'] ?? '';
    
        $user = $this->user->findByEmail($email);

        if ($user) {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $isPasswordValid = password_verify($password, $user->password);
        }
    
        if (!$user || !password_verify($password, $user->password)) {
            return $this->jsonResponse($response, [
                'error' => 'Invalid credentials'
            ], 401);
        }
    
        $token = JWT::encode([
            'id' => $user->id,
            'email' => $user->email,
            'role' => $user->role,
            'exp' => time() + (60 * 60)
        ], getenv('JWT_SECRET'), 'HS256');
    
        return $this->jsonResponse($response, [
            'token' => $token
        ]);
    }
}
