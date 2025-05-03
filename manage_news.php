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
                $stmt = $pdo->prepare("INSERT INTO news (title, image_url, content) VALUES (?, ?, ?)");
                $stmt->execute([$_POST['title'], $_POST['image_url'], $_POST['content']]);
                break;
            case 'edit':
                $stmt = $pdo->prepare("UPDATE news SET title = ?, image_url = ?, content = ? WHERE id = ?");
                $stmt->execute([$_POST['title'], $_POST['image_url'], $_POST['content'], $_POST['id']]);
                break;
            case 'delete':
                $stmt = $pdo->prepare("DELETE FROM news WHERE id = ?");
                $stmt->execute([$_POST['id']]);
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
                                <td><img src="<?php echo htmlspecialchars($item['image_url']); ?>" alt="" style="width: 100px;"></td>
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
            document.getElementById('edit_image_url').value = news.image_url;
            document.getElementById('edit_content').value = news.content;
            new bootstrap.Modal(document.getElementById('editModal')).show();
        }
    </script>
</body>
</html> 