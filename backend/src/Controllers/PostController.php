<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Models\Post;
use App\Traits\ResponseFormatter;

class PostController
{
    use ResponseFormatter;

    private $post;

    public function __construct(Post $post)
    {
        $this->post = $post;
    }

    public function getAllPosts(Request $request, Response $response): Response
    {
        $posts = $this->post->getAllPosts();
        return $this->successResponse($response, $posts);
    }

    public function createPost(Request $request, Response $response): Response
    {
        $data = $request->getParsedBody();
        $userId = $request->getAttribute('jwt')->id;

        $validation = $this->validatePost($data);
        if (!$validation['valid']) {
            return $this->errorResponse($response, $validation['error'], 400);
        }

        $post = $this->post->create([
            'title' => $data['title'],
            'content' => $data['content'],
            'user_id' => $userId,
            'status' => $data['status'] ?? 'draft'
        ]);

        return $this->successResponse($response, $post, 201);
    }

    public function updatePost(Request $request, Response $response): Response
    {        
        // Get IDs from request attributes
        $userId = $request->getAttribute('jwt')->id;
        $postId = $request->getAttribute('id');

        // Get and parse request body
        $data = $request->getParsedBody();

        $validation = $this->validatePost($data);
        if (!$validation['valid']) {
            return $this->errorResponse($response, $validation['error'], 400);
        }

        $post = $this->post->update($postId, [
            'title' => $data['title'],
            'content' => $data['content'],
            'status' => $data['status'] ?? 'draft'
        ], $userId);

        if (!$post) {
            return $this->errorResponse($response, 'Post not found or unauthorized', 404);
        }

        return $this->successResponse($response, $post);
    }

    public function deletePost(Request $request, Response $response): Response
    {
        try {   
            // Get IDs from request attributes
            $userId = $request->getAttribute('jwt')->id;
            $postId = $request->getAttribute('id'); // Get ID from request attribute
    
            if (!$postId) {
                return $this->errorResponse($response, 'Invalid post ID', 400);
            }
    
            $result = $this->post->delete($postId, $userId);
            
            if (!$result) {
                return $this->errorResponse($response, 'Post not found or unauthorized', 404);
            }
            return $response->withStatus(204);
    
        } catch (Exception $e) {
            return $this->errorResponse($response, 'Server error', 500);
        }
    }
    

    private function validatePost($data): array
    {
        if (empty($data['title'])) {
            return ['valid' => false, 'error' => 'Title is required'];
        }

        if (empty($data['content'])) {
            return ['valid' => false, 'error' => 'Content is required'];
        }

        if (isset($data['status']) && !in_array($data['status'], ['draft', 'published'])) {
            return ['valid' => false, 'error' => 'Invalid status'];
        }

        return ['valid' => true];
    }
}
