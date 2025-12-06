<?php
require_once __DIR__ . '/db.php';
$game_id = isset($_GET['game_id']) ? intval($_GET['game_id']) : 0;
$stmt = $db->prepare("SELECT * FROM games WHERE id = ?");
$stmt->execute([$game_id]);
$game = $stmt->fetch();
if (!$game) { die("Invalid game."); }

$month = $_GET['month'] ?? date('Y-m');
$start = $month . '-01';
$end = date('Y-m-t', strtotime($start));
$stmt = $db->prepare("SELECT * FROM results WHERE game_id = ? AND result_date BETWEEN ? AND ? ORDER BY result_date DESC");
$stmt->execute([$game_id, $start, $end]);
$rows = $stmt->fetchAll();
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?=htmlspecialchars($game['game_name'])?> — Chart</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="/assets/style.css">
</head>
<body>
  <div class="container">
    <div class="container-card">
      <header class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="h4"><?=htmlspecialchars($game['game_name'])?></h2>
        <a href="/" class="btn btn-sm btn-outline-secondary">Back</a>
      </header>

      <?php include __DIR__ . '/components/today-result.php'; ?>

      <section>
        <form method="get" class="row gy-2 gx-2 align-items-center mb-3">
          <input type="hidden" name="game_id" value="<?=htmlspecialchars($game_id)?>">
          <div class="col-auto">
            <label class="form-label small">Select month</label>
            <input class="form-control" type="month" name="month" value="<?=htmlspecialchars($month)?>" onchange="this.form.submit()">
          </div>
          <div class="col-auto d-none d-sm-block">
            <label class="form-label small">&nbsp;</label>
            <noscript><button class="btn btn-primary">Load</button></noscript>
          </div>
        </form>

        <h3>Results for <?=date('F Y', strtotime($month.'-01'))?></h3>

        <?php if (empty($rows)) : ?>
          <div class="alert alert-warning">No results for this month.</div>
        <?php else: ?>
          <table class="table table-striped">
            <thead><tr><th>Date</th><th>Result</th></tr></thead>
            <tbody>
              <?php foreach($rows as $r): ?>
                <tr>
                  <td><?=date('d M Y', strtotime($r['result_date']))?></td>
                  <td><?=htmlspecialchars($r['result_value'])?></td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        <?php endif; ?>
      </section>
    </div>
  </div>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
