<?php
include('../config.php');
if (!isset($_SESSION)) {
  session_start();
}
if (empty($_SESSION['accessToken']) || empty($_POST['route'])) {
    header("Location: ..");
    exit;
}

$token     = $_SESSION['accessToken'];
$route     = $_POST['route'];
$loggedorg = $_SESSION['loggedOrganization'];

$parsed = parse_url($route);
if (! empty($parsed['query'])) {
    parse_str($parsed['query'], $qp);

    if (isset($qp['username'])) {
        $usr = $qp['username'];
        if (! preg_match('/^[A-Za-z0-9_-]+$/', $qp['username'])) {
            http_response_code(400);
            die("Invalid username: $usr");
        }
    }
    if (isset($qp['org'])) {
        $org = $qp['org'];
        if (! preg_match('/^[A-Za-z0-9_-]+$/', $qp['org'])) {
            http_response_code(400);
            die("Invalid organization: $org");
        }
    }
}


$protocol = parse_url($appUrl, PHP_URL_SCHEME);
$api_base = $protocol . "://" . $appHost . "/userstats/";
$full_url = $api_base . ltrim($route, '/');

// call the API
$ch = curl_init($full_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Authorization: Bearer $token",
    "Accept: application/json",
    "loggedOrganization: $loggedorg"
]);
$response  = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

$data = json_decode($response, true);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Usage Report</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"
        rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <style>
    .chart-container { margin-bottom: 2rem; }
    .datapoint { text-align: center; }
    .main-table {
      max-height: 60vh;       
      overflow-y: auto;
      max-width: 95%;
      overflow-x: auto;
    }
  </style>

</head>
<body class="bg-light">
<div class="container py-5" style='margin-right: 0px;margin-left: 5%;'>
  <h4 class="mb-3">🔎 Endpoint: <code><?= htmlspecialchars($route) ?></code></h4>
  <a href="resourcesconsumption.php" class="btn btn-secondary mb-4">⬅️ Back</a>

  <?php if ($http_code === 200 && is_array($data) && count($data) > 0): ?>
    <?php
      // discover scalar metrics & dates
      $first   = reset($data);
      $metrics = array_values(array_filter(
        array_keys($first),
        function($k) use($first){ return !is_array($first[$k]); }
      ));
      $dates = array_keys($data);
      sort($dates);
      $multiDay = count($dates) > 1;
    ?>

    <!-- Data Table -->
    <div class="main-table mb-4">
     <table class="table table-striped">
       <thead>
         <tr><th>Date</th>
         <?php foreach ($metrics as $m): ?>
           <th><?= htmlspecialchars($m) ?></th>
         <?php endforeach; ?>
         </tr>
       </thead>
       <tbody>
         <?php foreach ($dates as $date): ?>
           <tr>
             <td><?= htmlspecialchars($date) ?></td>
             <?php foreach ($metrics as $m): ?>
               <td class="datapoint text-center"><?= htmlspecialchars($data[$date][$m]) ?></td>
             <?php endforeach; ?>
           </tr>
         <?php endforeach; ?>
       </tbody>
     </table>
    </div>
    <!-- Only show charts if more than one date -->
    <?php if ($multiDay): ?>
      <?php foreach (array_chunk($metrics, 2) as $pair): ?>
        <div class="row">
          <?php foreach ($pair as $m): ?>
            <div class="col-md-6 chart-container">
              <canvas id="chart_<?= htmlspecialchars($m) ?>"></canvas>
            </div>
          <?php endforeach; ?>
        </div>
      <?php endforeach; ?>
    <?php endif; ?>
    <!-- Dashboard Activity Summary -->
  <?php foreach ($dates as $date):
      // 1) Dashboards *used* by this org or user
      $dashUsed = $data[$date]['dashboard_used_by_org']
                   ?? $data[$date]['dashboard_activity_summary']
                   ?? [];
      // 2) Dashboards *owned* by this org or user
      $dashOwned = $data[$date]['owned_dashboard_activity']
                    ?? $data[$date]['owned_dashboard_usage']
                    ?? [];
  ?>

    <?php if (is_array($dashUsed) && count($dashUsed) > 0): ?>
      <h5 class="mt-4">📊 Dashboards Used on <?= htmlspecialchars($date) ?></h5>
      <table class="table table-sm table-bordered mb-4">
        <thead>
          <tr>
            <th>Dashboard ID</th>
            <th>Accesses</th>
            <th>Minutes</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($dashUsed as $entry): ?>
            <tr>
              <td><?= htmlspecialchars($entry['idDashboard']) ?></td>
              <td><?= htmlspecialchars($entry['nAccessPerDay']) ?></td>
              <td><?= htmlspecialchars($entry['nMinutesPerDay']) ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    <?php endif; ?>

    <?php if (is_array($dashOwned) && count($dashOwned) > 0): ?>
      <h5 class="mt-4">📂 Owned Dashboard Activity on <?= htmlspecialchars($date) ?></h5>
      <table class="table table-sm table-bordered mb-5">
        <thead>
          <tr>
            <th>Dashboard ID</th>
            <th>Total Accesses</th>
            <th>Total Minutes</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($dashOwned as $dashId => $entry): ?>
            <tr>
              <td><?= htmlspecialchars($dashId) ?></td>
              <td><?= htmlspecialchars($entry['total_accesses']) ?></td>
              <td><?= htmlspecialchars($entry['total_minutes']) ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    <?php endif; ?>

  <?php endforeach; ?>

  <?php else: ?>
    <div class="alert alert-danger">
      <strong>
        <?= $http_code !== 200
             ? "Error (HTTP $http_code):"
             : "No data returned for that period." ?>
      </strong><br>
      <?= htmlspecialchars($response) ?>
    </div>
  <?php endif; ?>
</div>

<?php if ($http_code === 200 && !empty($metrics) && $multiDay ): ?>
<script>
document.addEventListener('DOMContentLoaded', function(){
  const phpData = <?= json_encode($data, JSON_UNESCAPED_SLASHES) ?>;
  const dates   = <?= json_encode($dates) ?>;
  const metrics = <?= json_encode($metrics) ?>;

  metrics.forEach(metric => {
    const canvas = document.getElementById('chart_' + metric);
    if (!canvas) return;
    const ctx = canvas.getContext('2d');
    const vals = dates.map(d => {
      const v = phpData[d][metric];
      return (typeof v === 'number') ? v : parseInt(v)||0;
    });

    new Chart(ctx, {
      type: 'line',
      data: {
        labels: dates,
        datasets: [{ label: metric.replace(/_/g,' '), data: vals, fill: false }]
      },
      options: {
        responsive: true,
        plugins: { title: { display: true, text: metric.replace(/_/g,' ') } },
        scales: { y: { beginAtZero: true } }
      }
    });
  });
});
</script>
<?php endif; ?>
</body>
</html>
