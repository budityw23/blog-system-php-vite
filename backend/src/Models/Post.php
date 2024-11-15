<?php

namespace App\Models;

class Post
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function getAllPosts()
    {
        $stmt = $this->db->query("
            SELECT p.*, u.name as author_name 
            FROM posts p 
            JOIN users u ON p.user_id = u.id 
            ORDER BY p.created_at DESC
        ");
        return $stmt->fetchAll(\PDO::FETCH_OBJ);
    }

    public function create(array $data)
    {
        $stmt = $this->db->prepare("
            INSERT INTO posts (title, content, user_id, status) 
            VALUES (:title, :content, :user_id, :status)
        ");

        $stmt->execute([
            'title' => $data['title'],
            'content' => $data['content'],
            'user_id' => $data['user_id'],
            'status' => $data['status']
        ]);

        $id = $this->db->lastInsertId();
        return $this->getById($id);
    }

    public function update($id, array $data, $userId)
    {
        // Check if user is authorized to update this post
        $post = $this->getById($id);
        if (!$post || ($post->user_id !== $userId)) {
            return false;
        }

        $stmt = $this->db->prepare("
            UPDATE posts 
            SET title = :title, content = :content, status = :status 
            WHERE id = :id
        ");

        $stmt->execute([
            'id' => $id,
            'title' => $data['title'],
            'content' => $data['content'],
            'status' => $data['status']
        ]);

        return $this->getById($id);
    }

    public function delete($id, $userId)
    {
        $post = $this->getById($id);
        if (!$post) {
            return false;
        }

        $stmt = $this->db->prepare("DELETE FROM posts WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function getById($id)
    {
        $stmt = $this->db->prepare("
            SELECT p.*, u.name as author_name 
            FROM posts p 
            JOIN users u ON p.user_id = u.id 
            WHERE p.id = ?
        ");
        $stmt->execute([$id]);
        return $stmt->fetch(\PDO::FETCH_OBJ);
    }
}
