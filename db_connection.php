<?php
try {
    $pdo = new PDO(
        'mysql:host=localhost;dbname=real_estate;charset=utf8',
        'root',
        '',
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]
    );
    echo "Database setup completed successfully!";
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?> 