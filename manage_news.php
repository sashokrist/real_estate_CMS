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
    $image_name = ''; // Initialize image name variable

    // --- Handle Image Upload ---
    // Check if a file was uploaded for add or edit
    if (isset($_FILES['image_file']) && $_FILES['image_file']['error'] === UPLOAD_ERR_OK) {
        $target_dir = "images/"; // Make sure this directory exists and is writable
        // Create directory if it doesn't exist
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }

        $file_extension = pathinfo($_FILES["image_file"]["name"], PATHINFO_EXTENSION);
        // Generate a unique name to prevent overwriting
        $image_name = uniqid('news_') . '.' . $file_extension;
        $target_file = $target_dir . $image_name;

        // Attempt to move the uploaded file
        if (!move_uploaded_file($_FILES["image_file"]["tmp_name"], $target_file)) {
            // Handle upload error (e.g., display message, log error)
            echo "Error uploading file."; // Simple error message
            $image_name = ''; // Reset image name if upload failed
        }
    }
    // --- End Handle Image Upload ---

    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'add':
                // Only proceed if title, content are set and image upload was successful (or not required)
                // Assuming image is required for adding news
                if (!empty($_POST['title']) && !empty($_POST['content']) && !empty($image_name)) {
                    $stmt = $pdo->prepare("INSERT INTO news (title, image_url, content) VALUES (?, ?, ?)");
                    $stmt->execute([$_POST['title'], $image_name, $_POST['content']]);
                } else if (empty($image_name) && isset($_FILES['image_file']) && $_FILES['image_file']['error'] !== UPLOAD_ERR_NO_FILE) {
                    // Image upload failed, but was attempted
                    echo "Failed to add news due to image upload issue.";
                } else {
                     echo "Title, Content, and Image are required to add news.";
                }
                break;

            case 'edit':
                // If a new image was uploaded, update the image_url field
                if ($image_name !== '') {
                    // Optional: Delete old image file before updating DB
                    $stmt_old = $pdo->prepare("SELECT image_url FROM news WHERE id = ?");
                    $stmt_old->execute([$_POST['id']]);
                    $old_image = $stmt_old->fetchColumn();
                    if ($old_image && file_exists("images/" . $old_image)) {
                        @unlink("images/" . $old_image); // Suppress error if file not found
                    }

                    $stmt = $pdo->prepare("UPDATE news SET title = ?, image_url = ?, content = ? WHERE id = ?");
                    $stmt->execute([$_POST['title'], $image_name, $_POST['content'], $_POST['id']]);
                } else {
                    // If no new image, update only title and content
                    $stmt = $pdo->prepare("UPDATE news SET title = ?, content = ? WHERE id = ?");
                    $stmt->execute([$_POST['title'], $_POST['content'], $_POST['id']]);
                }
                break;

            case 'delete':
                // First, get the image filename to delete the file
                $stmt_img = $pdo->prepare("SELECT image_url FROM news WHERE id = ?");
                $stmt_img->execute([$_POST['id']]);
                $image_to_delete = $stmt_img->fetchColumn();

                // Delete the database record
                $stmt = $pdo->prepare("INSERT INTO news (title, image_url, content) VALUES (?, ?, ?)");
                if ($stmt->execute([$_POST['id']])) {
                    // If DB deletion is successful, delete the image file
                    if ($image_to_delete && file_exists("images/" . $image_to_delete)) {
                        @unlink("images/" . $image_to_delete); // Use @ to suppress errors if file not found
                    }
                }
                break;
        }
        header('Location: manage_news.php');
        exit();
    }
}

// Fetch all news
$stmt = $pdo->query("SELECT * FROM news ORDER BY created_at DESC");
$news = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Manage News - DreamSpace Realty</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1 class="text-center mb-5">Manage News</h1>
        
        <!-- Add News Form -->
        <div class="card mb-5">
            <div class="card-body">
                <h5 class="card-title">Add New News</h5>
                <form method="POST" enctype="multipart/form-data"> {/* Added enctype */}
                    <input type="hidden" name="action" value="add">
                    <div class="mb-3">
                        <label for="title" class="form-label">Title</label>
                        <input type="text" class="form-control" id="title" name="title" required>
                    </div>
                    <div class="mb-3">
                        <label for="image_file" class="form-label">Image File</label>
                        <input type="file" class="form-control" id="image_file" name="image_file" accept="image/*" required> {/* Changed input type */}
                    </div>
                    <div class="mb-3">
                        <label for="content" class="form-label">Content</label>
                        <textarea class="form-control" id="content" name="content" rows="3" required></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Add News</button>
                </form>
            </div>
        </div>

        <!-- News List -->
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Existing News</h5>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>Image</th>
                                <th>Content</th>
                                <th>Created At</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($news as $item): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($item['title']); ?></td>
                                <td>
                                    <?php if (!empty($item['image_url']) && file_exists("images/" . $item['image_url'])): ?>
                                        <img src="images/<?php echo htmlspecialchars($item['image_url']); ?>" alt="<?php echo htmlspecialchars($item['title']); ?>" style="width: 100px; height: auto;">
                                    <?php else: ?>
                                        <small>No Image</small>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo htmlspecialchars($item['content']); ?></td>
                                <td><?php echo $item['created_at']; ?></td>
                                <td>
                                    <button class="btn btn-sm btn-warning" onclick="editNews(<?php echo htmlspecialchars(json_encode($item)); ?>)">Edit</button>
                                    <form method="POST" style="display: inline;">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="id" value="<?php echo $item['id']; ?>">
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
                    <h5 class="modal-title">Edit News</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form method="POST" id="editForm" enctype="multipart/form-data"> {/* Added enctype */}
                        <input type="hidden" name="action" value="edit">
                        <input type="hidden" name="id" id="edit_id">
                        <div class="mb-3">
                            <label for="edit_title" class="form-label">Title</label>
                            <input type="text" class="form-control" id="edit_title" name="title" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_image_file" class="form-label">Upload New Image (Optional)</label>
                            <input type="file" class="form-control" id="edit_image_file" name="image_file" accept="image/*"> {/* Changed input type */}
                            <small class="form-text text-muted">Leave empty to keep the current image.</small>
                        </div>
                        <div class="mb-3">
                            <label for="edit_content" class="form-label">Content</label>
                            <textarea class="form-control" id="edit_content" name="content" rows="3" required></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function editNews(news) {
            document.getElementById('edit_id').value = news.id;
            document.getElementById('edit_title').value = news.title;
            // Cannot set value for file input, user must select new file if they want to change it.
            document.getElementById('edit_content').value = news.content;
            new bootstrap.Modal(document.getElementById('editModal')).show();
        }
    </script>
</body>
</html> 