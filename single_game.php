<?php
require "db.php";

if (!isset($_GET['game_id'])) {
    die("Game ID missing.");
}

$game_id = intval($_GET['game_id']);

// Selected month & year
$month = isset($_GET['month']) ? intval($_GET['month']) : date("m");
$year  = isset($_GET['year'])  ? intval($_GET['year'])  : date("Y");

$today = date("Y-m-d");
$yesterday = date("Y-m-d", strtotime("-1 day"));

// Fetch game name
$stmt = $db->prepare("SELECT game_name FROM games WHERE id = ?");
$stmt->execute([$game_id]);
$game = $stmt->fetch();
if (!$game) die("Game not found.");

$game_name = $game['game_name'];

// Fetch today & yesterday
$stmt2 = $db->prepare("
    SELECT result_date, result_value FROM results
    WHERE game_id = ?
      AND result_date IN (?, ?)
");
$stmt2->execute([$game_id, $today, $yesterday]);
$ty = $stmt2->fetchAll();

$todayVal = "-";
$yesterdayVal = "-";

foreach ($ty as $r) {
    if ($r['result_date'] == $today) $todayVal = $r['result_value'];
    if ($r['result_date'] == $yesterday) $yesterdayVal = $r['result_value'];
}

// Fetch selected month results
$stmt3 = $db->prepare("
    SELECT result_date, result_value
    FROM results
    WHERE game_id = :gid 
      AND YEAR(result_date) = :y
      AND MONTH(result_date) = :m
    ORDER BY result_date ASC
");
$stmt3->execute(['gid' => $game_id, 'y' => $year, 'm' => $month]);
$monthData = $stmt3->fetchAll();

$formatted = [];
foreach ($monthData as $r) {
    $formatted[$r['result_date']] = $r['result_value'];
}

$daysInMonth = date("t", strtotime("$year-$month-01"));
?>
<!DOCTYPE html>
<html>
<head>
<title><?= htmlspecialchars($game_name) ?> - Results</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>

<body class="container mt-4">

<h2><?= htmlspecialchars($game_name) ?> – Results</h2>
<a href="index.php" class="btn btn-secondary btn-sm mb-3">⬅ Back</a>

<!-- TODAY & YESTERDAY -->
<h4>Today & Yesterday</h4>
<table class="table table-bordered w-50">
<tr><th>Date</th><th>Result</th></tr>
<tr><td><?= $yesterday ?></td><td><?= $yesterdayVal ?></td></tr>
<tr><td><?= $today ?></td><td><?= $todayVal ?></td></tr>
</table>

<!-- MONTH-YEAR SELECTOR -->
<form method="GET" class="row g-3 mb-4">
    <input type="hidden" name="game_id" value="<?= $game_id ?>">

    <div class="col-md-4">
        <label>Month</label>
        <select name="month" class="form-control">
            <?php for ($m=1;$m<=12;$m++): ?>
                <option value="<?= $m ?>" <?= ($m==$month)?"selected":"" ?>>
                    <?= date("F", mktime(0,0,0,$m,1)) ?>
                </option>
            <?php endfor; ?>
        </select>
    </div>

    <div class="col-md-4">
        <label>Year</label>
        <select name="year" class="form-control">
            <?php for ($y=date("Y")-5;$y<=date("Y");$y++): ?>
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
<thead><tr><th>Date</th><th>Result</th></tr></thead>
<tbody>
<?php for ($d=1;$d<=$daysInMonth;$d++): 
    $date = sprintf("%04d-%02d-%02d", $year, $month, $d);
?>
<tr>
<td><?= $date ?></td>
<td><?= $formatted[$date] ?? "-" ?></td>
</tr>
<?php endfor; ?>
</tbody>
</table>

</body>
</html>
