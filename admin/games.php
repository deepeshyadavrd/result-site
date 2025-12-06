<?php
require_once __DIR__ . '/../functions.php';
require_login();
if (!is_superadmin()) { header("Location: dashboard.php"); exit; }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['game_name'] ?? '');
    if ($name) {
        $stmt = $db->prepare("INSERT INTO games (game_name) VALUES (?)");
        $stmt->execute([$name]);
        $msg = "Game added.";
    }
}

$games = $db->query("SELECT * FROM games ORDER BY id DESC")->fetchAll();
?>
<!doctype html>
<html>
<head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Manage Games</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="/assets/style.css"></head>
<body>
  <div class="container">
    <div class="container-card">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <h3>Manage Games</h3>
        <a href="dashboard.php" class="btn btn-sm btn-link">Back</a>
      </div>

      <?php if(!empty($msg)) echo "<div class='alert alert-success'>$msg</div>"; ?>

      <form method="post" class="row g-2 mb-3">
        <div class="col-md-6">
          <input name="game_name" class="form-control" placeholder="New Game Name" required>
        </div>
        <div class="col-auto">
          <button class="btn btn-primary">Add Game</button>
        </div>
      </form>

      <h5>All Games</h5>
      <table class="table">
        <tr><th>ID</th><th>Name</th></tr>
        <?php foreach($games as $g): ?>
          <tr><td><?=$g['id']?></td><td><?=htmlspecialchars($g['game_name'])?></td></tr>
        <?php endforeach; ?>
      </table>
    </div>
  </div>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
