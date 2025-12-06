<?php
require_once __DIR__ . '/../functions.php';
require_login();

$user = current_user();
$role = $user['role'];

if ($role === 'superadmin') {
    $games = $db->query("SELECT * FROM games ORDER BY game_name")->fetchAll();
} else {
    $stmt = $db->prepare("
        SELECT g.* FROM games g
        JOIN user_game_map m ON m.game_id = g.id
        WHERE m.user_id = ?
        ORDER BY g.game_name
    ");
    $stmt->execute([$user['id']]);
    $games = $stmt->fetchAll();
}

$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $game_id = intval($_POST['game_id']);
    $result_date = $_POST['result_date'];
    $result_value = trim($_POST['result_value']);

    if ($role !== 'superadmin') {
        $allowed = false;
        foreach ($games as $g) {
            if ($g['id'] == $game_id) { $allowed = true; break; }
        }
        if (!$allowed) { die('Not allowed'); }
    }

    if ($game_id && $result_date && $result_value !== '') {
        $stmt = $db->prepare("SELECT id FROM results WHERE game_id = ? AND result_date = ?");
        $stmt->execute([$game_id, $result_date]);
        $exists = $stmt->fetch();

        if ($exists) {
            $stmt = $db->prepare("UPDATE results SET result_value = ? WHERE id = ?");
            $stmt->execute([$result_value, $exists['id']]);
            $msg = "Updated";
        } else {
            $stmt = $db->prepare("INSERT INTO results (game_id, result_date, result_value) VALUES (?, ?, ?)");
            $stmt->execute([$game_id, $result_date, $result_value]);
            $msg = "Saved";
        }
    } else {
        $msg = "All fields required";
    }
}

$pre_game_id = isset($_GET['game_id']) ? intval($_GET['game_id']) : null;
?>
<!doctype html>
<html>
<head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Add Result</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="/assets/style.css"></head>
<body>
  <div class="container">
    <div class="container-card">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <h3>Add / Edit Result</h3>
        <a href="dashboard.php" class="btn btn-sm btn-link">Back</a>
      </div>
      <?php if($msg) echo "<div class='alert alert-info'>$msg</div>"; ?>

      <form method="post" class="row g-2 mb-3">
        <div class="col-md-4">
          <label class="form-label">Game</label>
          <select name="game_id" class="form-select" required>
            <?php foreach($games as $g): ?>
              <option value="<?=$g['id']?>" <?=($pre_game_id && $pre_game_id == $g['id'])?'selected':''?>><?=htmlspecialchars($g['game_name'])?></option>
            <?php endforeach;?>
          </select>
        </div>

        <div class="col-md-3">
          <label class="form-label">Date</label>
          <input type="date" name="result_date" class="form-control" value="<?=date('Y-m-d')?>" required>
        </div>

        <div class="col-md-3">
          <label class="form-label">Result</label>
          <input type="text" name="result_value" class="form-control" required>
        </div>

        <div class="col-auto align-self-end">
          <button class="btn btn-primary">Save Result</button>
        </div>
      </form>

      <h5>Recent Results (this user's games)</h5>
      <?php
      $gameIds = array_map(function($g){return $g['id'];}, $games);
      if (!empty($gameIds)) {
          $placeholders = implode(',', array_fill(0, count($gameIds), '?'));
          $stmt = $db->prepare("SELECT r.*, g.game_name FROM results r JOIN games g ON g.id = r.game_id WHERE r.game_id IN ($placeholders) ORDER BY r.result_date DESC LIMIT 50");
          $stmt->execute($gameIds);
          $rows = $stmt->fetchAll();
      } else {
          $rows = [];
      }
      ?>
      <table class="table">
        <thead><tr><th>Date</th><th>Game</th><th>Result</th></tr></thead>
        <tbody>
          <?php foreach($rows as $r): ?>
            <tr>
              <td><?=htmlspecialchars($r['result_date'])?></td>
              <td><?=htmlspecialchars($r['game_name'])?></td>
              <td><?=htmlspecialchars($r['result_value'])?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
