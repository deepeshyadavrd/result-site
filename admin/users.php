<?php
require_once __DIR__ . '/../functions.php';
require_login();
if (!is_superadmin()) { header("Location: dashboard.php"); exit; }

$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $role = $_POST['role'] ?? 'manager';

    if ($username && $password) {
        $hash = password_hash($password, PASSWORD_BCRYPT);
        $stmt = $db->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
        $stmt->execute([$username, $hash, $role]);
        $msg = "User created.";
    }
}

$users = $db->query("SELECT id, username, role, created_at FROM users ORDER BY id DESC")->fetchAll();
?>
<!doctype html>
<html>
<head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Manage Users</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="/assets/style.css"></head>
<body>
  <div class="container">
    <div class="container-card">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <h3>Manage Users</h3>
        <a href="dashboard.php" class="btn btn-sm btn-link">Back</a>
      </div>
      <?php if($msg): ?><div class="alert alert-success"><?=htmlspecialchars($msg)?></div><?php endif; ?>

      <form method="post" class="row g-2 mb-3">
        <div class="col-md-4"><input name="username" class="form-control" placeholder="Username" required></div>
        <div class="col-md-4"><input name="password" type="password" class="form-control" placeholder="Password" required></div>
        <div class="col-md-2">
          <select name="role" class="form-select">
            <option value="manager">Manager</option>
            <option value="superadmin">Super Admin</option>
          </select>
        </div>
        <div class="col-auto"><button class="btn btn-primary">Create User</button></div>
      </form>

      <h5>Existing Users</h5>
      <table class="table">
        <tr><th>ID</th><th>Username</th><th>Role</th><th>Created</th></tr>
        <?php foreach($users as $u): ?>
        <tr>
          <td><?=$u['id']?></td>
          <td><?=htmlspecialchars($u['username'])?></td>
          <td><?=htmlspecialchars($u['role'])?></td>
          <td><?=$u['created_at']?></td>
        </tr>
        <?php endforeach; ?>
      </table>
    </div>
  </div>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
