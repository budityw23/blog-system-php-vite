<?php

require_once __DIR__ . '/vendor/autoload.php';

$config = require __DIR__ . '/config/database.php';


try {
    // Connect to MySQL
    $pdo = new PDO(
        "mysql:host={$config['host']};charset=utf8mb4",
        $config['username'],
        $config['password']
    );
    
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Create database if not exists
    $pdo->exec("CREATE DATABASE IF NOT EXISTS {$config['dbname']}");
    $pdo->exec("USE {$config['dbname']}");

    // Run migrations
    $migrations = glob(__DIR__ . '/database/migrations/*.sql');

    sort($migrations); // Ensure migrations run in order

    foreach ($migrations as $migration) {
        $sql = file_get_contents($migration);
        $pdo->exec($sql);
        echo "Executed migration: " . basename($migration) . PHP_EOL;
    }

    // Run seeds
    $seeds = glob(__DIR__ . '/database/seeds/*.sql');
    sort($seeds);

    foreach ($seeds as $seed) {
        $sql = file_get_contents($seed);
        $pdo->exec($sql);
        echo "Executed seed: " . basename($seed) . PHP_EOL;
    }

    echo "Database initialization completed successfully!\n";
} catch (PDOException $e) {
    die("Database initialization failed: " . $e->getMessage() . "\n");
}
