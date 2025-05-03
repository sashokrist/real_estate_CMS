<?php
require_once 'db_connection.php';
session_start();

// Check if user is logged in
if (!isset($_SESSION['admin'])) {
    header('Location: login.php');
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'add':
                $stmt = $pdo->prepare("INSERT INTO renovating_services (title, image_url, description, service_type) VALUES (?, ?, ?, ?)");
                $stmt->execute([
                    $_POST['title'],
                    $_POST['image_url'],
                    $_POST['description'],
                    $_POST['service_type']
                ]);
                break;
            case 'edit':
                $stmt = $pdo->prepare("UPDATE renovating_services SET title = ?, image_url = ?, description = ?, service_type = ? WHERE id = ?");
                $stmt->execute([
                    $_POST['title'],
                    $_POST['image_url'],
                    $_POST['description'],
                    $_POST['service_type'],
                    $_POST['id']
                ]);
                break;
            case 'delete':
                $stmt = $pdo->prepare("DELETE FROM renovating_services WHERE id = ?");
                $stmt->execute([$_POST['id']]);
                break;
        }
        header('Location: manage_renovating.php');
        exit();
    }
}

// Fetch all renovating services
$stmt = $pdo->query("SELECT * FROM renovating_services ORDER BY created_at DESC");
$services = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Manage Renovating Services - DreamSpace Realty</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1 class="text-center mb-5">Manage Renovating Services</h1>
        
        <!-- Add Service Form -->
        <div class="card mb-5">
            <div class="card-body">
                <h5 class="card-title">Add New Service</h5>
                <form method="POST">
                    <input type="hidden" name="action" value="add">
                    <div class="mb-3">
                        <label for="title" class="form-label">Title</label>
                        <input type="text" class="form-control" id="title" name="title" required>
                    </div>
                    <div class="mb-3">
                        <label for="image_url" class="form-label">Image URL</label>
                        <input type="url" class="form-control" id="image_url" name="image_url" required>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="3" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="service_type" class="form-label">Service Type</label>
                        <select class="form-select" id="service_type" name="service_type" required>
                            <option value="kitchen">Kitchen</option>
                            <option value="bathroom">Bathroom</option>
                            <option value="full_home">Full Home</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Add Service</button>
                </form>
            </div>
        </div>

        <!-- Services List -->
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Existing Services</h5>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>Image</th>
                                <th>Service Type</th>
                                <th>Description</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($services as $service): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($service['title']); ?></td>
                                <td><img src="<?php echo htmlspecialchars($service['image_url']); ?>" alt="" style="width: 100px;"></td>
                                <td><?php echo ucfirst(str_replace('_', ' ', $service['service_type'])); ?></td>
                                <td><?php echo htmlspecialchars($service['description']); ?></td>
                                <td>
                                    <button class="btn btn-sm btn-warning" onclick="editService(<?php echo htmlspecialchars(json_encode($service)); ?>)">Edit</button>
                                    <form method="POST" style="display: inline;">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="id" value="<?php echo $service['id']; ?>">
                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</button>
                                    </form>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Modal -->
    <div class="modal fade" id="editModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Service</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form method="POST" id="editForm">
                        <input type="hidden" name="action" value="edit">
                        <input type="hidden" name="id" id="edit_id">
                        <div class="mb-3">
                            <label for="edit_title" class="form-label">Title</label>
                            <input type="text" class="form-control" id="edit_title" name="title" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_image_url" class="form-label">Image URL</label>
                            <input type="url" class="form-control" id="edit_image_url" name="image_url" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_description" class="form-label">Description</label>
                            <textarea class="form-control" id="edit_description" name="description" rows="3" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="edit_service_type" class="form-label">Service Type</label>
                            <select class="form-select" id="edit_service_type" name="service_type" required>
                                <option value="kitchen">Kitchen</option>
                                <option value="bathroom">Bathroom</option>
                                <option value="full_home">Full Home</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function editService(service) {
            document.getElementById('edit_id').value = service.id;
            document.getElementById('edit_title').value = service.title;
            document.getElementById('edit_image_url').value = service.image_url;
            document.getElementById('edit_description').value = service.description;
            document.getElementById('edit_service_type').value = service.service_type;
            new bootstrap.Modal(document.getElementById('editModal')).show();
        }
    </script>
</body>
</html> 