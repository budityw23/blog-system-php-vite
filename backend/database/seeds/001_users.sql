-- Password for all users is 'password123' (hashed using password_hash())
INSERT INTO users (email, password, name, role) VALUES
('admin@example.com', '$2y$10$fxMScRsA6uuKIVvffOOR3OnSSjHefo1xeyQe4dLFBJ4Kj1OLhnRre', 'Admin User', 'admin'),
('editor@example.com', '$2y$10$fxMScRsA6uuKIVvffOOR3OnSSjHefo1xeyQe4dLFBJ4Kj1OLhnRre', 'Editor User', 'editor'),
('viewer@example.com', '$2y$10$fxMScRsA6uuKIVvffOOR3OnSSjHefo1xeyQe4dLFBJ4Kj1OLhnRre', 'Viewer User', 'viewer');