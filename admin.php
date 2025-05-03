<?php
require_once 'db_connection.php';
session_start();

// Check if user is logged in (you should implement proper authentication)
if (!isset($_SESSION['admin'])) {
    header('Location: login.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Panel - DreamSpace Realty</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1 class="text-center mb-5">Admin Panel</h1>
        
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Manage Content</h5>
                        <div class="d-grid gap-3">
                            <a href="manage_news.php" class="btn btn-primary">Manage News</a>
                            <a href="manage_properties.php" class="btn btn-success">Manage Properties</a>
                            <a href="manage_renovating.php" class="btn btn-info">Manage Renovating Services</a>
                            <a href="index.php" class="btn btn-secondary">Back to Website</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 