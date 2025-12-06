<?php
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/functions.php';
// TODAY & YESTERDAY
$today = date("Y-m-d");
$yesterday = date("Y-m-d", strtotime("-1 day"));

// Fetch all games
$stmt = $db->query("SELECT id, game_name FROM games ORDER BY id");
$games = $stmt->fetchAll();

// Fetch last 2-day results
$stmt2 = $db->prepare("
    SELECT game_id, result_date, result_value 
    FROM results
    WHERE result_date IN (:today, :yesterday)
");
$stmt2->execute([
    "today" => $today,
    "yesterday" => $yesterday
]);
$last2 = $stmt2->fetchAll();

// Format result[game_id][date] = value
$last2Formatted = [];
foreach ($last2 as $r) {
    $last2Formatted[$r['game_id']][$r['result_date']] = $r['result_value'];
}

// Fetch this month results
$stmt3 = $db->prepare("
    SELECT game_id, result_date, result_value
    FROM results
    WHERE YEAR(result_date)=YEAR(CURDATE()) 
    AND MONTH(result_date)=MONTH(CURDATE())
    ORDER BY result_date ASC
");
$stmt3->execute();
$monthResults = $stmt3->fetchAll();

// Format monthResults[date][game_id]
$monthFormatted = [];
foreach ($monthResults as $r) {
    $monthFormatted[$r['result_date']][$r['game_id']] = $r['result_value'];
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Results</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="assets/style1.css">
</head>

<body class="container mt-5">

<h2 class="mb-3">Latest Result</h2>

<table class="table table-bordered">
<thead>
<tr>
    <th>Game</th>
    <th><?php echo $yesterday; ?></th>
    <th><?php echo $today; ?></th>
    <th>Full Month</th>
</tr>
</thead>
<tbody>
<?php foreach ($games as $g): ?>
<tr>
    <td><?php echo htmlspecialchars($g['game_name']); ?></td>

    <td>
        <?php echo $last2Formatted[$g['id']][$yesterday] ?? "-"; ?>
    </td>

    <td>
        <?php echo $last2Formatted[$g['id']][$today] ?? "-"; ?>
    </td>

    <td>
        <a class="btn btn-sm btn-primary" href="single_game.php?game_id=<?php echo $g['id']; ?>">
            View
        </a>
    </td>
</tr>
<?php endforeach; ?>
</tbody>
</table>

<hr class="my-5">

<h2>This Month's Results (Till Today)</h2>

<table class="table table-bordered">
<thead>
<tr>
    <th>Date</th>
    <?php foreach ($games as $g): ?>
        <th><?php echo htmlspecialchars($g['game_name']); ?></th>
    <?php endforeach; ?>
</tr>
</thead>
<tbody>

<?php
$start = date("Y-m-01");
$todayDay = date("d");

for ($i = 0; $i < $todayDay; $i++):
    $date = date("Y-m-d", strtotime("$start + $i day"));
?>
<tr>
    <td><?php echo $date; ?></td>

    <?php foreach ($games as $g): ?>
        <td>
            <?php echo $monthFormatted[$date][$g['id']] ?? "-"; ?>
        </td>
    <?php endforeach; ?>

</tr>
<?php endfor; ?>
</tbody>
</table>

<hr class="my-5">

<h3>Select Month & Year</h3>

<form method="GET" action="all_games.php" class="row g-3">

<div class="col-md-3">
    <label>Month</label>
    <select name="month" class="form-control">
        <?php for ($m = 1; $m <= 12; $m++): ?>
            <option value="<?= $m ?>"><?= date("F", mktime(0,0,0,$m,1)) ?></option>
        <?php endfor; ?>
    </select>
</div>

<div class="col-md-3">
    <label>Year</label>
    <select name="year" class="form-control">
        <?php for ($y = date("Y")-5; $y <= date("Y"); $y++): ?>
            <option value="<?= $y ?>"><?= $y ?></option>
        <?php endfor; ?>
    </select>
</div>

<div class="col-md-3 align-self-end">
    <button class="btn btn-success">Get Results</button>
</div>

</form>

</body>
</html>
