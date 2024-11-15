<?php

namespace App\Models;

class User {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function create(array $data)
    {
        $sql = "INSERT INTO users (name, email, password, role, created_at, updated_at) 
                VALUES (:name, :email, :password, :role, NOW(), NOW())";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['password'],
            'role' => $data['role']
        ]);

        $id = $this->db->lastInsertId();
        return $this->findById($id);
    }

    public function findById($id) {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(\PDO::FETCH_OBJ);
    }

    public function findByEmail($email) {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetch(\PDO::FETCH_OBJ);
    }

    public function getAllUsers() {
        $stmt = $this->db->query("SELECT id, email, name, role FROM users");
        return $stmt->fetchAll(\PDO::FETCH_OBJ);
    }

    public function updateRole($userId, $role) {
        $stmt = $this->db->prepare("UPDATE users SET role = ? WHERE id = ?");
        return $stmt->execute([$role, $userId]);
    }

    public function delete($id)
    {
        $stmt = $this->db->prepare("DELETE FROM users WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }
}
