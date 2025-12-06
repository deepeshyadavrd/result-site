<?php
$today = date('Y-m-d');
$todayData = $db->prepare("
    SELECT r.*, g.game_name
    FROM results r
    JOIN games g ON g.id = r.game_id
    WHERE r.result_date = ?
    ORDER BY g.game_name ASC
");
$todayData->execute([$today]);
$todayRows = $todayData->fetchAll();
?>
<div class="today-box mb-3">
  <h5>Today's Results (<?=date('d M Y')?>)</h5>
  <?php if(!$todayRows): ?>
    <p class="mb-0">No results published for today.</p>
  <?php else: ?>
    <ul class="list-unstyled mb-0">
      <?php foreach($todayRows as $tr): ?>
        <li><strong><?=htmlspecialchars($tr['game_name'])?>:</strong> <?=htmlspecialchars($tr['result_value'])?></li>
      <?php endforeach; ?>
    </ul>
  <?php endif; ?>
</div>
