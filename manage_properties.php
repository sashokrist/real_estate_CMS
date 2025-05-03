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
                try {
                    $pdo->beginTransaction();
                    
                    // Insert property
                    $stmt = $pdo->prepare("INSERT INTO properties (id, title, image_url, description, price, bedrooms, bathrooms, location, size, year_built) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                    $propertyId = uniqid('prop_');
                    $stmt->execute([
                        $propertyId,
                        $_POST['title'],
                        $_POST['image_url'],
                        $_POST['description'],
                        $_POST['price'],
                        $_POST['bedrooms'],
                        $_POST['bathrooms'],
                        $_POST['location'],
                        $_POST['size'],
                        $_POST['year_built']
                    ]);

                    // Insert features
                    if (!empty($_POST['features'])) {
                        $features = array_map('trim', explode(',', $_POST['features']));
                        $stmt = $pdo->prepare("INSERT INTO property_features (property_id, feature) VALUES (?, ?)");
                        foreach ($features as $feature) {
                            if (!empty($feature)) {
                                $stmt->execute([$propertyId, $feature]);
                            }
                        }
                    }

                    $pdo->commit();
                } catch (Exception $e) {
                    $pdo->rollBack();
                    die("Error: " . $e->getMessage());
                }
                break;

            case 'edit':
                try {
                    $pdo->beginTransaction();
                    
                    // Update property
                    $stmt = $pdo->prepare("UPDATE properties SET title = ?, image_url = ?, description = ?, price = ?, bedrooms = ?, bathrooms = ?, location = ?, size = ?, year_built = ? WHERE id = ?");
                    $stmt->execute([
                        $_POST['title'],
                        $_POST['image_url'],
                        $_POST['description'],
                        $_POST['price'],
                        $_POST['bedrooms'],
                        $_POST['bathrooms'],
                        $_POST['location'],
                        $_POST['size'],
                        $_POST['year_built'],
                        $_POST['id']
                    ]);

                    // Update features
                    if (!empty($_POST['features'])) {
                        // Delete existing features
                        $stmt = $pdo->prepare("DELETE FROM property_features WHERE property_id = ?");
                        $stmt->execute([$_POST['id']]);

                        // Insert new features
                        $features = array_map('trim', explode(',', $_POST['features']));
                        $stmt = $pdo->prepare("INSERT INTO property_features (property_id, feature) VALUES (?, ?)");
                        foreach ($features as $feature) {
                            if (!empty($feature)) {
                                $stmt->execute([$_POST['id'], $feature]);
                            }
                        }
                    }

                    $pdo->commit();
                } catch (Exception $e) {
                    $pdo->rollBack();
                    die("Error: " . $e->getMessage());
                }
                break;

            case 'delete':
                try {
                    $pdo->beginTransaction();
                    
                    // Delete features first (due to foreign key constraint)
                    $stmt = $pdo->prepare("DELETE FROM property_features WHERE property_id = ?");
                    $stmt->execute([$_POST['id']]);
                    
                    // Delete property
                    $stmt = $pdo->prepare("DELETE FROM properties WHERE id = ?");
                    $stmt->execute([$_POST['id']]);
                    
                    $pdo->commit();
                } catch (Exception $e) {
                    $pdo->rollBack();
                    die("Error: " . $e->getMessage());
                }
                break;
        }
        header('Location: manage_properties.php');
        exit();
    }
}

// Fetch all properties with their features
$stmt = $pdo->query("
    SELECT p.*, GROUP_CONCAT(pf.feature) as features 
    FROM properties p 
    LEFT JOIN property_features pf ON p.id = pf.property_id 
    GROUP BY p.id 
    ORDER BY p.created_at DESC
");
$properties = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Manage Properties - DreamSpace Realty</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1 class="text-center mb-5">Manage Properties</h1>
        
        <!-- Add Property Form -->
        <div class="card mb-5">
            <div class="card-body">
                <h5 class="card-title">Add New Property</h5>
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
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="price" class="form-label">Price</label>
                                <input type="number" class="form-control" id="price" name="price" step="0.01" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="bedrooms" class="form-label">Bedrooms</label>
                                <input type="number" class="form-control" id="bedrooms" name="bedrooms" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="bathrooms" class="form-label">Bathrooms</label>
                                <input type="number" class="form-control" id="bathrooms" name="bathrooms" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="location" class="form-label">Location</label>
                                <input type="text" class="form-control" id="location" name="location" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="size" class="form-label">Size</label>
                                <input type="text" class="form-control" id="size" name="size" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="year_built" class="form-label">Year Built</label>
                                <input type="text" class="form-control" id="year_built" name="year_built" required>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="features" class="form-label">Features (comma-separated)</label>
                        <textarea class="form-control" id="features" name="features" rows="2" placeholder="e.g., Swimming Pool, Garden, Garage"></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Add Property</button>
                </form>
            </div>
        </div>

        <!-- Properties List -->
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Existing Properties</h5>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>Image</th>
                                <th>Price</th>
                                <th>Bedrooms</th>
                                <th>Bathrooms</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($properties as $property): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($property['title']); ?></td>
                                <td><img src="<?php echo htmlspecialchars($property['image_url']); ?>" alt="" style="width: 100px;"></td>
                                <td>$<?php echo number_format($property['price'], 2); ?></td>
                                <td><?php echo $property['bedrooms']; ?></td>
                                <td><?php echo $property['bathrooms']; ?></td>
                                <td>
                                    <button class="btn btn-sm btn-warning" onclick="editProperty(<?php echo htmlspecialchars(json_encode($property)); ?>)">Edit</button>
                                    <form method="POST" style="display: inline;">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="id" value="<?php echo $property['id']; ?>">
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
                    <h5 class="modal-title">Edit Property</h5>
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
                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="edit_price" class="form-label">Price</label>
                                    <input type="number" class="form-control" id="edit_price" name="price" step="0.01" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="edit_bedrooms" class="form-label">Bedrooms</label>
                                    <input type="number" class="form-control" id="edit_bedrooms" name="bedrooms" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="edit_bathrooms" class="form-label">Bathrooms</label>
                                    <input type="number" class="form-control" id="edit_bathrooms" name="bathrooms" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="edit_location" class="form-label">Location</label>
                                    <input type="text" class="form-control" id="edit_location" name="location" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="edit_size" class="form-label">Size</label>
                                    <input type="text" class="form-control" id="edit_size" name="size" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="edit_year_built" class="form-label">Year Built</label>
                                    <input type="text" class="form-control" id="edit_year_built" name="year_built" required>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="edit_features" class="form-label">Features (comma-separated)</label>
                            <textarea class="form-control" id="edit_features" name="features" rows="2" placeholder="e.g., Swimming Pool, Garden, Garage"></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function editProperty(property) {
            document.getElementById('edit_id').value = property.id;
            document.getElementById('edit_title').value = property.title;
            document.getElementById('edit_image_url').value = property.image_url;
            document.getElementById('edit_description').value = property.description;
            document.getElementById('edit_price').value = property.price;
            document.getElementById('edit_bedrooms').value = property.bedrooms;
            document.getElementById('edit_bathrooms').value = property.bathrooms;
            document.getElementById('edit_location').value = property.location;
            document.getElementById('edit_size').value = property.size;
            document.getElementById('edit_year_built').value = property.year_built;
            document.getElementById('edit_features').value = property.features;
            new bootstrap.Modal(document.getElementById('editModal')).show();
        }
    </script>
</body>
</html> 