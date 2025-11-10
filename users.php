<?php
session_start();
require_once __DIR__ . '/ClassAutoLoad.php';
require_once __DIR__ . '/DBConnection.php';



if (!isset($_SESSION['user_id'])) {
    header("Location: /iap-group-project/Forms/login.php");
    exit();
}

$db = new database($conf);
$conn = $db->connect();

try {
    $stmt = $conn->prepare("SELECT id, username, email FROM users");
    $stmt->execute();
    $items = $db->fetch("SELECT id, username, email FROM users");
 
} catch (Exception $e) {
    die("Database error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Dashboard</title>
    <link rel="stylesheet" href="users.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/2.1.4/css/dataTables.dataTables.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/2.1.4/js/dataTables.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body class="p-4">
    <div class="container">
        <h2>Welcome, <?php echo htmlspecialchars($_SESSION['username'] ?? 'User'); ?></h2>
        <p>Here are all registered users:</p>

        <table id="myTable" class="display table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Full Name</th>
                    <th>Email</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($items as $item){?>
                    <tr data-id="<?= $item['id'] ?>">
                        <td><?= htmlspecialchars($item['id']); ?></td>
                        <td><?= htmlspecialchars($item['username']); ?></td>
                        <td><?= htmlspecialchars($item['email']); ?></td>
                        <td>
                            <button class="btn btn-sm btn-primary edit-btn">Edit</button>
                            <button class="btn btn-sm btn-danger delete-btn">Delete</button>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>

    <!-- Edit Modal -->
    <div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <form id="editForm">
            <div class="modal-header">
              <h5 class="modal-title">Edit User</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
              <input type="hidden" name="id" id="edit-id">
              <div class="mb-3">
                <label>Full Name</label>
                <input type="text" name="username" id="edit-username" class="form-control" required>
              </div>
              <div class="mb-3">
                <label>Email</label>
                <input type="email" name="email" id="edit-email" class="form-control" required>
              </div>
            </div>
            <div class="modal-footer">
              <button type="submit" class="btn btn-success">Save Changes</button>
            </div>
          </form>
        </div>
      </div>
    </div>

<script>
$(document).ready(function () {
    const table = $('#myTable').DataTable();

    // Handle Edit button click
    $('#myTable').on('click', '.edit-btn', function () {
        const row = $(this).closest('tr');
        const id = row.data('id');
        const username = row.find('td:eq(1)').text();
        const email = row.find('td:eq(2)').text();

        $('#edit-id').val(id);
        $('#edit-username').val(username);
        $('#edit-email').val(email);

        new bootstrap.Modal(document.getElementById('editModal')).show();
    });

    // Handle form submit for edit
    $('#editForm').on('submit', function (e) {
        e.preventDefault();

        $.ajax({
            url: 'edit_user.php',
            method: 'POST',
            data: $(this).serialize(),
            success: function (response) {
                const data = JSON.parse(response);
                if (data.success) {
                    const row = $('#myTable tr[data-id="' + data.id + '"]');
                    row.find('td:eq(1)').text(data.username);
                    row.find('td:eq(2)').text(data.email);
                    bootstrap.Modal.getInstance(document.getElementById('editModal')).hide();
                } else {
                    alert('Update failed: ' + data.error);
                }
            }
        });
    });

    
    $('#myTable').on('click', '.delete-btn', function () {
        const row = $(this).closest('tr');
        const id = row.data('id');

        if (confirm('Are you sure you want to delete this user?')) {
            $.ajax({
                url: 'delete_user.php',
                method: 'POST',
                data: { id },
                success: function (response) {
                    const data = JSON.parse(response);
                    if (data.success) {
                        table.row(row).remove().draw();
                    } else {
                        alert('Delete failed: ' + data.error);
                    }
                }
            });
        }
    });
});
</script>

</body>
</html>
