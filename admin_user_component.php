<?php



if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action']) && $_POST['action'] === 'delete') {
        $id = (int)$_POST['id'];
        $db->delete("DELETE FROM users WHERE id = ?", [$id]);
        header("Location: admin_dashboard.php?view=users&status=deleted");
        exit();
    }
    if (isset($_POST['action']) && $_POST['action'] === 'edit') {
        $id = (int)$_POST['id'];
        $username = trim($_POST['username']);
        $email = trim($_POST['email']);
        $db->update("UPDATE users SET username = ?, email = ? WHERE id = ?", [$username, $email, $id]);
        header("Location: admin_dashboard.php?view=users&status=edited");
        exit();
    }
}

try {
    $sql = "SELECT users.id, users.username, users.email, roles.role_name 
            FROM users
            LEFT JOIN roles ON users.role_id = roles.id
            ORDER BY users.id";
    $items = $db->fetch($sql);
} catch (Exception $e) {
    die("Database error: " . $e->getMessage());
}
?>

<link rel="stylesheet" href="https://cdn.datatables.net/2.1.4/css/dataTables.dataTables.min.css">
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/2.1.4/js/dataTables.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>


<div class="container-fluid">
    <?php if(isset($_GET['status']) && $_GET['status'] === 'edited'): ?>
        <div class="alert alert-success auto-dismiss-alert">User updated successfully!</div>
    <?php endif; ?>
    <?php if(isset($_GET['status']) && $_GET['status'] === 'deleted'): ?>
        <div class="alert alert-success auto-dismiss-alert">User deleted successfully!</div>
    <?php endif; ?>

    <table id="myTable" class="display table table-striped" style="width:100%">
        <thead>
            <tr>
                <th>ID</th>
                <th>Full Name</th>
                <th>Email</th>
                <th>Role</th> <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($items as $item){?>
                <tr>
                    <td><?= htmlspecialchars($item['id']); ?></td>
                    <td><?= htmlspecialchars($item['username']); ?></td>
                    <td><?= htmlspecialchars($item['email']); ?></td>
                    <td><?= htmlspecialchars($item['role_name'] ?? 'user'); ?></td>
                    <td>
                        <button class="btn btn-sm btn-primary edit-btn" 
                                data-id="<?= $item['id'] ?>"
                                data-username="<?= htmlspecialchars($item['username']) ?>"
                                data-email="<?= htmlspecialchars($item['email']) ?>">
                            Edit
                        </button>
                        <form method="POST" action="admin_dashboard.php?view=users" style="display:inline;" onsubmit="return confirm('Are you sure?');">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="id" value="<?= $item['id'] ?>">
                            <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</div>

<div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form id="editForm" method="POST" action="admin_dashboard.php?view=users">
        <div class="modal-header">
          <h5 class="modal-title">Edit User</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="action" value="edit">
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

if (!$.fn.DataTable.isDataTable('#myTable')) {
    $(document).ready(function () {
        $('#myTable').DataTable();
    });
}

$('#myTable').on('click', '.edit-btn', function () {
    const id = $(this).data('id');
    const username = $(this).data('username');
    const email = $(this).data('email');
    $('#edit-id').val(id);
    $('#edit-username').val(username);
    $('#edit-email').val(email);
    new bootstrap.Modal(document.getElementById('editModal')).show();
});

setTimeout(function() {
    $('.auto-dismiss-alert').slideUp('slow');
}, 3000);
</script>