<?php
require_once __DIR__ . '/../functions.php';
require_login();
$user = current_user();
$role = $user['role'];

if ($role === 'superadmin') {
    $stmt = $db->query("SELECT * FROM games ORDER BY id DESC");
    $games = $stmt->fetchAll();
} else {
    $stmt = $db->prepare("
        SELECT g.* FROM games g
        JOIN user_game_map m ON m.game_id = g.id
        WHERE m.user_id = ?
        ORDER BY g.id DESC
    ");
    $stmt->execute([$user['id']]);
    $games = $stmt->fetchAll();
}
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Admin Dashboard</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="../assets/style.css">
</head>
<body>
  <div class="container">
    <div class="container-card">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <h3>Dashboard — <?=htmlspecialchars($user['username'])?> (<?=htmlspecialchars($user['role'])?>)</h3>
        <div><a href="logout.php" class="btn btn-sm btn-outline-secondary">Logout</a></div>
      </div>

      <nav class="mb-3">
        <?php if (is_superadmin()): ?>
          <a class="btn btn-sm btn-outline-primary me-2" href="games.php">Manage Games</a>
          <a class="btn btn-sm btn-outline-primary me-2" href="users.php">Manage Users</a>
          <a class="btn btn-sm btn-outline-primary me-2" href="assign.php">Assign Users</a>
        <?php endif; ?>
        <a class="btn btn-sm btn-primary" href="add_result.php">Add / Edit Result</a>
        <a class="btn btn-sm btn-link" href="/index.php">View Site</a>
      </nav>

      <section>
        <h5>Your Games</h5>
        <?php if (empty($games)): ?>
          <p>No games assigned / created yet.</p>
        <?php else: ?>
          <ul class="list-group">
            <?php foreach($games as $g): ?>
              <li class="list-group-item d-flex justify-content-between align-items-center">
                <?=htmlspecialchars($g['game_name'])?>
                <a href="add_result.php?game_id=<?=$g['id']?>" class="btn btn-sm btn-outline-secondary">Add Result</a>
              </li>
            <?php endforeach; ?>
          </ul>
        <?php endif; ?>
      </section>
    </div>
  </div>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
