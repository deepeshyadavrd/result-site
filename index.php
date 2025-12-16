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
$sql = "SELECT created_at FROM results ORDER BY created_at DESC LIMIT 1";
$result = $db->query($sql);

$recentTime = "No results declared yet";

if ($result && $row = $result->fetchAll()) {
    $recentTime = date("F j, Y - g:i A", strtotime($row[0]['created_at']));
}
//next pver
$month = isset($_GET['month']) ? (int)$_GET['month'] : (int)date('m');
$year  = isset($_GET['year'])  ? (int)$_GET['year']  : (int)date('Y');

$current = DateTime::createFromFormat('Y-m', "$year-$month");

$prev = clone $current;
$prev->modify('-1 month');

$next = clone $current;
$next->modify('+1 month');
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Results</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="assets/style1.css">
<style>
    .header {
        text-align: center;
        padding: 30px 20px;
        font-family: Arial, sans-serif;
    }

    .logo {
        font-size: 32px;
        font-weight: bold;
        margin-bottom: 10px;
    }

    .header p {
        margin: 6px 0;
        font-size: 16px;
        color: #444;
    }

    /* Second paragraph pinkish background */
    .pink-bg {
        background: #f73838;
        /* display: inline-block; */
        padding: 6px 12px;
        border-radius: 6px;
        color:#fff
    }

    .recent-time {
        margin-top: 12px;
        font-weight: bold;
        color: #2c3e50;
        font-size: 15px;
    }

    /* Table styling */
    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
    }
    th, td {
        padding: 10px;
        border: 1px solid #ccc;
        text-align: center;
    }

    /* First header row teal */
    .header1 {
        background: #008080;  /* Teal */
        color: white;
    }

    /* Second header row black */
    .header2 {
        background: #000;     /* Black */
        color: white;
    }

</style>
</head>

<body class="container mt-5">
    <div class="container">
        <header class="header">
            <div class="logo">Japan King</div>
            <div>Daily Superfast Satta King Result of 13th December 2025 And Leak Numbers for Gali, Desawar, Ghaziabad and Faridabad With Complete Old Satta King Chart of 2015, 2016, 2017, 2018, 2019, 2020, 2021, 2023, 2024, 2025 From Satta King Fast, Satta King Ghaziabad, Satta King Desawar, Satta King Gali, Satta King Faridabad.</div>
            <div class="pink-bg">DISCLAIMER: This website is an independent media portal for informational and journalistic purposes only. As a non-transactional service, we are not affiliated with any entity mentioned. Users are solely responsible for complying with all applicable laws in their jurisdiction.</div>
            <div class="recent-time">Recent Result Time: <?php echo $recentTime; ?></div>
        </header>
        <section>
            <h2 class="mb-3">Latest Result</h2>

            <table class="table table-bordered">
                <thead>
                    <tr class="header1">
                        <th colspan=4  style="text-align:center;">Results of <?php 
                        $today1 = date("F j, Y", strtotime($today));
                        $yesterday1 = date("F j, Y", strtotime($yesterday));
                        echo $yesterday1;
                        echo " & ";
                        echo $today1; ?>
                        </th>
                    </tr>
                    <tr class="header2">
                        <th>Game</th>
                        <th><?php 
                        $dt = new DateTime($yesterday1);
                        $yesterday2 = $dt->format("D. jS");
                        echo $yesterday2; ?></th>
                        <th><?php 
                        $dt = new DateTime($today1);
                        $today2 = $dt->format("D. jS");
                        echo $today2; ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    foreach ($games as $g): ?>
                        <tr>
                        <td><h3><?php echo htmlspecialchars($g['game_name']); ?></h3><h3><a class="btn btn-sm btn-link" href="single_game.php?game_id=<?php echo $g['id']; ?>">View Record</a></h3></td>
                        <td>
                            <?php echo $last2Formatted[$g['id']][$yesterday] ?? "-"; ?>
                        </td>
                        <td>
                            <?php echo $last2Formatted[$g['id']][$today] ?? "-"; ?>
                        </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </section>
        <hr class="my-5">
        <scetion>
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
                        $date = date("d", strtotime("$start + $i day"));
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
            <div style="display:flex;justify-content: space-between; margin:20px 0;">
                <a href="all_games.php?month=<?php echo $prev->format('m'); ?>&year=<?php echo $prev->format('Y'); ?>" class="btn btn-primary">
                <?php echo $prev->format('F Y'); ?>
                </a>
                
                &nbsp;&nbsp;&nbsp;
                
                <a href="all_games.php?month=<?php echo $next->format('m'); ?>&year=<?php echo $next->format('Y'); ?>" class="btn btn-primary">
                <?php echo $next->format('F Y'); ?>
                </a>
            </div>
        </scetion>    
            <hr class="my-5">
            <section>
    <h3>Select Month & Year</h3>

    <form method="GET" action="all_games.php" class="row g-2 align-items-center">

        <!-- Month -->
        <div class="col-6 col-md-4">
            <select name="month" class="form-control">
                <?php for ($m = 1; $m <= 12; $m++): ?>
                    <option value="<?= $m ?>">
                        <?= date("F", mktime(0,0,0,$m,1)) ?>
                    </option>
                <?php endfor; ?>
            </select>
        </div>

        <!-- Year -->
        <div class="col-6 col-md-4">
            <select name="year" class="form-control">
                <?php for ($y = date("Y")-5; $y <= date("Y"); $y++): ?>
                    <option value="<?= $y ?>"><?= $y ?></option>
                <?php endfor; ?>
            </select>
        </div>

        <!-- Button -->
        <div class="col-12 col-md-4">
            <button class="btn btn-success w-100">
                Get Results
            </button>
        </div>

    </form>
</section>   
    </div>
</body>
</html>
