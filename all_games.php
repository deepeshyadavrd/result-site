<?php
require "db.php";

$month = isset($_GET['month']) ? intval($_GET['month']) : date("m");
$year  = isset($_GET['year']) ? intval($_GET['year']) : date("Y");

$today = date("Y-m-d");
$yesterday = date("Y-m-d", strtotime("-1 day"));

// Fetch games
$stmt = $db->query("SELECT id, game_name FROM games ORDER BY id");
$games = $stmt->fetchAll();

// Fetch today & yesterday results
$stmt2 = $db->prepare("
    SELECT game_id, result_date, result_value
    FROM results
    WHERE result_date IN (?, ?)
");
$stmt2->execute([$today, $yesterday]);
$tyResults = $stmt2->fetchAll();

$tyFormatted = [];
foreach ($tyResults as $r) {
    $tyFormatted[$r['game_id']][$r['result_date']] = $r['result_value'];
}

// Fetch selected month results
$stmt3 = $db->prepare("
    SELECT game_id, result_date, result_value
    FROM results
    WHERE YEAR(result_date)=:y
      AND MONTH(result_date)=:m
    ORDER BY result_date ASC
");
$stmt3->execute(['y'=>$year,'m'=>$month]);
$rows = $stmt3->fetchAll();

$monthFormatted = [];
foreach ($rows as $r) {
    $monthFormatted[$r['result_date']][$r['game_id']] = $r['result_value'];
}

$daysInMonth = date("t", strtotime("$year-$month-01"));

//next prev
// $month = isset($_GET['month']) ? (int)$_GET['month'] : (int)date('m');
// $year  = isset($_GET['year'])  ? (int)$_GET['year']  : (int)date('Y');

$current = DateTime::createFromFormat('Y-m', "$year-$month");

$prev = clone $current;
$prev->modify('-1 month');

$next = clone $current;
$next->modify('+1 month');
?>
<!DOCTYPE html>
<html>
<head>
<title>All Games - Result</title>

<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>

<body class="container mt-4">

<h2>All Games – Results</h2>
<a href="index.php" class="btn btn-secondary btn-sm mb-3">⬅ Back</a>

<!-- TODAY & YESTERDAY -->
<h4>Today & Yesterday</h4>

<table class="table table-bordered">
<thead>
<tr>
    <th>Game</th>
    <th><?= $yesterday ?></th>
    <th><?= $today ?></th>
</tr>
</thead>
<tbody>
<?php foreach ($games as $g): ?>
<tr>
<td><?= htmlspecialchars($g['game_name']) ?></td>
<td><?= $tyFormatted[$g['id']][$yesterday] ?? "-" ?></td>
<td><?= $tyFormatted[$g['id']][$today] ?? "-" ?></td>
</tr>
<?php endforeach; ?>
</tbody>
</table>

<!-- MONTH-YEAR SELECTOR -->
<form method="GET" class="row g-3 mb-4">

    <div class="col-md-4">
        <label>Month</label>
        <select name="month" class="form-control">
            <?php for ($m = 1; $m <= 12; $m++): ?>
                <option value="<?= $m ?>" <?= ($m == $month) ? "selected":"" ?>>
                    <?= date("F", mktime(0,0,0,$m,1)) ?>
                </option>
            <?php endfor; ?>
        </select>
    </div>

    <div class="col-md-4">
        <label>Year</label>
        <select name="year" class="form-control">
            <?php for ($y=date("Y")-5; $y<=date("Y"); $y++): ?>
                <option value="<?= $y ?>" <?= ($y==$year)?"selected":"" ?>><?= $y ?></option>
            <?php endfor; ?>
        </select>
    </div>

    <div class="col-md-4 align-self-end">
        <button class="btn btn-success">Show</button>
    </div>

</form>

<h4>Results for <?= date("F", mktime(0,0,0,$month,1)) ?> <?= $year ?></h4>

<table class="table table-bordered">
<thead>
<tr>
    <th>Date</th>
    <?php foreach ($games as $g): ?>
    <th><?= htmlspecialchars($g['game_name']) ?></th>
    <?php endforeach; ?>
</tr>
</thead>

<tbody>
<?php for ($d=1; $d<=$daysInMonth; $d++): 
    $date = sprintf( $d);
?>
<tr>
<td><?= $date ?></td>

<?php foreach ($games as $g): ?>
<td><?= $monthFormatted[$date][$g['id']] ?? "-" ?></td>
<?php endforeach; ?>

</tr>
<?php endfor; ?>
</tbody>
</table>
<div style="display:flex;justify-content: space-between; margin:20px 0;">
    <a href="all_games.php?month=<?php echo $prev->format('m'); ?>&year=<?php echo $prev->format('Y'); ?>" class="btn btn-primary">
    <?php echo $prev->format('F Y'); ?>
    </a>

    &nbsp;&nbsp;&nbsp;

    <a href="all_games.php?month=<?php echo $next->format('m'); ?>&year=<?php echo $next->format('Y'); ?>" class="btn btn-primary">
    <?php echo $next->format('F Y'); ?>
    </a>
</div>
</body>
</html>
