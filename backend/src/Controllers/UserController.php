<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Models\User;
use App\Traits\ResponseFormatter;

class UserController
{
    use ResponseFormatter;

    private $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function createUser(Request $request, Response $response): Response
    {
        try {
            // Get and parse request body
            $body = (string) $request->getBody();
            
            $data = $request->getParsedBody();

            // Validate input
            $validation = $this->validateUserData($data);
            
            if (!$validation['valid']) {
                return $this->errorResponse($response, $validation['error'], 400);
            }

            // Check if email exists
            $existingUser = $this->user->findByEmail($data['email']);
            if ($existingUser) {
                return $this->errorResponse($response, 'Email already exists', 400);
            }

            // Prepare user data
            $userData = [
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => password_hash($data['password'], PASSWORD_DEFAULT),
                'role' => $data['role'] ?? 'viewer'
            ];

            // Create user
            $newUser = $this->user->create($userData);
            
            if (!$newUser) {
                return $this->errorResponse($response, 'Failed to create user', 500);
            }
            

            // Remove password from response
            unset($newUser->password);
            return $this->successResponse($response, $newUser, 201);

        } catch (Exception $e) {
            return $this->errorResponse(
                $response, 
                'Server error while creating user: ' . $e->getMessage(), 
                500
            );
        }
    }

    // Add this helper method to your controller if you don't have it already
    private function validateUserData($data): array
    {   
        $required = ['name', 'email', 'password'];
        foreach ($required as $field) {
            if (!isset($data[$field]) || empty(trim($data[$field]))) {
                return [
                    'valid' => false,
                    'error' => "Missing required field: {$field}"
                ];
            }
        }

        // Validate email format
        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            return [
                'valid' => false,
                'error' => 'Invalid email format'
            ];
        }

        // Validate password length
        if (strlen($data['password']) < 8) {
            return [
                'valid' => false,
                'error' => 'Password must be at least 8 characters long'
            ];
        }

        // Validate role if provided
        if (isset($data['role'])) {
            $allowedRoles = ['admin', 'editor', 'viewer'];
            if (!in_array($data['role'], $allowedRoles)) {
                return [
                    'valid' => false,
                    'error' => 'Invalid role. Must be one of: ' . implode(', ', $allowedRoles)
                ];
            }
        }

        return ['valid' => true, 'error' => null];
    }

    public function getAllUsers(Request $request, Response $response): Response
    {
        $users = $this->user->getAllUsers();
        return $this->successResponse($response, $users);
    }

    public function deleteUser(Request $request, Response $response): Response
    {
        try {
            // Get ID from request attribute
            $userId = $request->getAttribute('id');

            if (!$userId) {
                return $this->errorResponse($response, 'Invalid user ID', 400);
            }

            // Check if trying to delete self
            $currentUserId = $request->getAttribute('jwt')->id;
            if ($currentUserId == $userId) {
                return $this->errorResponse($response, 'Cannot delete your own account', 403);
            }

            $result = $this->user->delete($userId);
            
            if (!$result) {
                return $this->errorResponse($response, 'User not found', 404);
            }

            return $response->withStatus(204);

        } catch (Exception $e) {
            return $this->errorResponse($response, 'Server error', 500);
        }
    }

    public function updateRole(Request $request, Response $response): Response
    {
        try {           
            // Get user ID from request attribute
            $userId = $request->getAttribute('id');
            
            if (!$userId) {
                return $this->errorResponse($response, 'Invalid user ID', 400);
            }

            // Get and validate role from request body
            $data = json_decode($request->getBody()->getContents(), true);

            if (!isset($data['role'])) {
                return $this->errorResponse($response, 'Role is required', 400);
            }

            $allowedRoles = ['admin', 'editor', 'viewer'];
            if (!in_array($data['role'], $allowedRoles)) {
                return $this->errorResponse(
                    $response, 
                    'Invalid role. Must be one of: ' . implode(', ', $allowedRoles), 
                    400
                );
            }

            // Update the role
            $result = $this->user->updateRole($userId, $data['role']);
            
            if (!$result) {
                return $this->errorResponse($response, 'User not found', 404);
            }

            $responseData = [
                'message' => 'Role updated successfully',
                'userId' => $userId,
                'newRole' => $data['role']
            ];

            $response->getBody()->write(json_encode($responseData));
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(200);

        } catch (Exception $e) {
            return $this->errorResponse($response, 'Server error while updating role', 500);
        }
    }

}
