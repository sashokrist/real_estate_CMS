<?php
try {
    // Connect to MySQL without selecting a database
    $pdo = new PDO(
        'mysql:host=localhost',
        'root',
        '',
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]
    );

    // Read and execute the SQL file
    $sql = file_get_contents('database.sql');
    
    // Split the SQL file into individual statements
    $statements = array_filter(array_map('trim', explode(';', $sql)));
    
    // Execute each statement
    foreach ($statements as $statement) {
        if (!empty($statement)) {
            $pdo->exec($statement);
        }
    }
    
    echo "Database setup completed successfully!";
    
} catch (PDOException $e) {
    die("Setup failed: " . $e->getMessage());
} 