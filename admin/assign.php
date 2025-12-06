<?php
require_once __DIR__ . '/../functions.php';
require_login();
if (!is_superadmin()) { header("Location: dashboard.php"); exit; }

$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = intval($_POST['user_id']);
    $game_id = intval($_POST['game_id']);
    if ($user_id && $game_id) {
        $stmt = $db->prepare("INSERT IGNORE INTO user_game_map (user_id, game_id) VALUES (?, ?)");
        $stmt->execute([$user_id, $game_id]);
        $msg = "Assigned.";
    }
}

$managers = $db->query("SELECT id, username FROM users WHERE role='manager' ORDER BY username")->fetchAll();
$games = $db->query("SELECT id, game_name FROM games ORDER BY game_name")->fetchAll();
$assigns = $db->query("
    SELECT m.id, u.username, g.game_name
    FROM user_game_map m
    JOIN users u ON u.id = m.user_id
    JOIN games g ON g.id = m.game_id
    ORDER BY m.id DESC
")->fetchAll();
?>
<!doctype html>
<html>
<head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Assign Users</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="/assets/style.css"></head>
<body>
  <div class="container">
    <div class="container-card">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <h3>Assign Managers to Games</h3>
        <a href="dashboard.php" class="btn btn-sm btn-link">Back</a>
      </div>
      <?php if($msg) echo "<div class='alert alert-success'>$msg</div>"; ?>

      <form method="post" class="row g-2 align-items-center mb-3">
        <div class="col-md-4">
          <select name="user_id" class="form-select" required>
            <?php foreach($managers as $m): ?>
              <option value="<?=$m['id']?>"><?=htmlspecialchars($m['username'])?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-md-4">
          <select name="game_id" class="form-select" required>
            <?php foreach($games as $g): ?>
              <option value="<?=$g['id']?>"><?=htmlspecialchars($g['game_name'])?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-auto">
          <button class="btn btn-primary">Assign</button>
        </div>
      </form>

      <h5>Current Assignments</h5>
      <table class="table">
        <tr><th>ID</th><th>Manager</th><th>Game</th></tr>
        <?php foreach($assigns as $a): ?>
          <tr><td><?=$a['id']?></td><td><?=htmlspecialchars($a['username'])?></td><td><?=htmlspecialchars($a['game_name'])?></td></tr>
        <?php endforeach; ?>
      </table>
    </div>
  </div>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
