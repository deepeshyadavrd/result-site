<?php
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/functions.php';
$games = $db->query("SELECT * FROM games ORDER BY game_name")->fetchAll();
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Results Site</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="assets/style.css">
</head>
<body>
  <div class="container">
    <div class="container-card">
      <header class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h3">Daily Results</h1>
        <nav><a href="" class="me-2">Home</a> <a href="admin/login.php">Admin</a></nav>
      </header>

      <?php include __DIR__ . '/components/today-result.php'; ?>

      <section>
        <h3>View by Game</h3>
        <div class="list-group">
          <?php foreach($games as $g): ?>
            <a class="list-group-item list-group-item-action" href="chart.php?game_id=<?=$g['id']?>"><?=htmlspecialchars($g['game_name'])?></a>
          <?php endforeach; ?>
        </div>
      </section>

      <footer class="mt-4">
        <p class="text-muted small">Informational results site.</p>
      </footer>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
