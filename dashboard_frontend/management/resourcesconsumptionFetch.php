<?php

/*TLDR: get route from post, get data from API endpoint, make tables for data if data and charts if at least 2 dates in data
expects: $_SESSION['accessToken'], $_POST['route'], connection to userstats API at $resourcesconsumptionLocation
extra: $_SESSION['loggedOrganization'] (it's not used by the api by default.... it uses directly the token with ou in it, but if you have a realm where ou is not defined/passed
  in the access token then it can use this as fallback. )
would be nice to change: cdn.jsdelivr to static files (bootstrap.min.css, chart.js)
*/

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
/*
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
*/

$protocol = parse_url($appUrl, PHP_URL_SCHEME);
$api_base = $protocol . "://" . $appHost . $resourcesconsumptionLocation;
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
  <a class="btn btn-secondary mb-4" id="btn_activity_table">Hide Activity table</a>
  <a class="btn btn-secondary mb-4" id="btn_charts">Hide Charts</a>
  <a class="btn btn-secondary mb-4" id="btn_dashboards">Hide Dashboard Tables</a>
  <a class="btn btn-secondary mb-4" id="btn_api_table">Hide API Usage Tables</a>

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
    <div class="main-table mb-4" id="activity_table">
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
      <div id="charts">
        <?php foreach (array_chunk($metrics, 2) as $pair): ?>
          <div class="row">
            <?php foreach ($pair as $m): ?>
              <div class="col-md-6 chart-container">
                <canvas id="chart_<?= htmlspecialchars($m) ?>"></canvas>
              </div>
            <?php endforeach; ?>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
    <!-- Dashboard Activity Summary -->
    <?php
  $dashboards_used_by_date  = [];
  $dashboards_owned_by_date = [];

  foreach ($dates as $date) {
    $dashboards_used_by_date[$date]  = $data[$date]['dashboard_used_by_org']
                                      ?? $data[$date]['dashboard_activity_summary']
                                      ?? [];
    $dashboards_owned_by_date[$date] = $data[$date]['owned_dashboard_activity']
                                      ?? $data[$date]['owned_dashboard_usage']
                                      ?? [];
  }
?>
<div id="dashboards">
  <?php if (!empty($dashboards_used_by_date)): ?>
    <h5 class="mt-4">📊 Dashboards Used by users</h5>
    <h6 class="mt-4">These are dashboards that users have accessed.</h6>
    <table class="table table-sm table-bordered mb-5">
      <thead>
        <tr>
          <th>Date</th>
          <th>Dashboards (ID: Accesses, Minutes)</th>
          <th class="text-center">Total Accesses</th>
          <th class="text-center">Total Minutes</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($dashboards_used_by_date as $date => $entries): ?>
          <?php 
            $totalAccesses = 0;
            $totalMinutes  = 0;
          ?>
          <tr>
            <td><?= htmlspecialchars($date) ?></td>
            <td>
              <ul class="mb-0 ps-3">
                <?php foreach ($entries as $e): 
                  $a = (int)$e['nAccessPerDay'];
                  $m = (int)$e['nMinutesPerDay'];
                  $totalAccesses += $a;
                  $totalMinutes  += $m;
                ?>
                  <li>
                    ID <strong><?= htmlspecialchars($e['idDashboard']) ?></strong>:
                    <strong><?= $a ?></strong> access<?= $a!==1?'es':'' ?>,
                    <strong><?= $m ?></strong> minute<?= $m!==1?'s':'' ?>
                  </li>
                <?php endforeach; ?>
              </ul>
            </td>
            <td class="text-center"><strong><?= $totalAccesses ?></strong></td>
            <td class="text-center"><strong><?= $totalMinutes ?></strong></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  <?php endif; ?>

  <?php if (!empty($dashboards_owned_by_date)): ?>
    <h5 class="mt-4">📂 Dashboards Owned</h5>
    <h6 class="mt-4">These are accesses to dashboards that are owned by the users.</h6>
    <table class="table table-sm table-bordered mb-5">
      <thead>
        <tr>
          <th>Date</th>
          <th>Dashboards (ID: Accesses, Minutes)</th>
          <th class="text-center">Total Accesses</th>
          <th class="text-center">Total Minutes</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($dashboards_owned_by_date as $date => $entries): ?>
          <?php 
            $totalAccesses = 0;
            $totalMinutes  = 0;
          ?>
          <tr>
            <td><?= htmlspecialchars($date) ?></td>
            <td>
              <ul class="mb-0 ps-3">
                <?php foreach ($entries as $dashId => $e):
                  // for owned, $e has keys ['total_accesses','total_minutes']
                  $a = (int)$e['total_accesses'];
                  $m = (int)$e['total_minutes'];
                  $totalAccesses += $a;
                  $totalMinutes  += $m;
                ?>
                  <li>
                    ID <strong><?= htmlspecialchars($dashId) ?></strong>:
                    <strong><?= $a ?></strong> access<?= $a!==1?'es':'' ?>,
                    <strong><?= $m ?></strong> minute<?= $m!==1?'s':'' ?>
                  </li>
                <?php endforeach; ?>
              </ul>
            </td>
            <td class="text-center"><strong><?= $totalAccesses ?></strong></td>
            <td class="text-center"><strong><?= $totalMinutes ?></strong></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  <?php endif; ?>
</div>
  <?php
  $api_manager_usage = [];
  $ascapi_usage      = [];
  
  foreach ($dates as $date):
  // API usage 
      $api_manager_usage[$date] = $data[$date]['api_manager'];
      $ascapi_usage[$date] = $data[$date]['ascapi'];
  endforeach;
  ?>
  <div id="api_table">
    <?php if (! empty($api_manager_usage)): ?>
      <div class="tablediv" id="apimanager_table">
        <h5 class="mt-4">📊 API Manager Usage</h5>
        <h6 class="mt-4">These accesses from users to APIs contained in SUMO.</h6>
        <table class="table table-sm table-bordered mb-5">
          <thead>
            <tr>
              <th>Date</th>
              <th>APIs and Accesses</th>
              <th>Total</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($api_manager_usage as $date => $apis): ?>
              <tr>
                <td><?= htmlspecialchars($date) ?></td>
                <td>
                  <ul class="mb-0 ps-3">
                    <?php foreach ($apis as $apiName => $count): ?>
                      <li>
                        <?= htmlspecialchars($apiName) ?>:
                        <strong><?= htmlspecialchars($count) ?></strong>
                      </li>
                    <?php endforeach; ?>
                  </ul>
                </td>
                <td class="text-center"><strong><?= htmlspecialchars(array_sum($apis)) ?></strong></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php endif; ?>
    <?php if (! empty($ascapi_usage)): ?>
      <h5 class="mt-4">📊 Ascapi Usage</h5>
      <h6 class="mt-4">These accesses from users to APIs ASCAPI.</h6>
      <table class="table table-sm table-bordered mb-5">
        <thead>
          <tr>
            <th>Date</th>
            <th>APIs and Accesses</th>
            <th>Total</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($ascapi_usage as $date => $apis): ?>
            <tr>
              <td><?= htmlspecialchars($date) ?></td>
              <td>
                <ul class="mb-0 ps-3">
                  <?php foreach ($apis as $apiName => $count): ?>
                    <li>
                      <?= htmlspecialchars($apiName) ?>:
                      <strong><?= htmlspecialchars($count) ?></strong>
                    </li>
                  <?php endforeach; ?>
                </ul>
              </td>
              <td class="text-center"><strong><?= htmlspecialchars(array_sum($apis)) ?></strong></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    <?php endif; ?>
  </div>

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
const btns = [
    'btn_activity_table',
    'btn_charts',
    'btn_dashboards',
    'btn_api_table'
  ];

  btns.forEach(id => {
    const btn = document.getElementById(id);
    btn.addEventListener('click', function() {
      toggle_visibility(this);
    });
  });

  function toggle_visibility(button) {
    const divId = button.id.replace(/^btn_/, '');
    const div = document.getElementById(divId);
    if (!div) {
      console.warn(`No element found with id="${divId}"`);
      return;
    }
    
    const isHidden = div.style.display === 'none';
    div.style.display = isHidden ? '' : 'none';
    button.textContent = button.textContent.replace(
      /^(Hide|Show)/,
      matched => matched === 'Hide' ? 'Show' : 'Hide'
    );
  }
</script>
<?php endif; ?>
</body>
</html>
