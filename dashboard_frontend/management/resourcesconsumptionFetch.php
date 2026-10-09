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
$loggedorg = $_SESSION['loggedOrganization'] ?? '';
$isUserRoute = (strpos($route, 'user/') === 0);

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
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
curl_setopt($ch, CURLOPT_TIMEOUT, 120);
$response  = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
if ($response === false) {
    $response = "API not reachable: " . curl_error($ch);
}
curl_close($ch);

$data = json_decode($response, true);
if ($http_code === 401) {
    $response = "Your session has expired, please log in again.";
}

// extract org and date parameters from $route if present (org for /org/usage, dates for top users)
$orgParam = null;
$topStart = null;
$topEnd   = null;
$parsedRoute = parse_url($route);
if (isset($parsedRoute['query'])) {
    parse_str($parsedRoute['query'], $qs);
    if (isset($qs['org'])) {
        $orgParam = $qs['org'];
    }
    if (!empty($qs['start_date']) && !empty($qs['end_date'])) {
        $topStart = $qs['start_date'];
        $topEnd   = $qs['end_date'];
    } elseif (!empty($qs['date'])) {
        // same date or month selected in the form (YYYY-MM-DD or YYYY-MM)
        if (preg_match('/^\d{4}-\d{2}$/', $qs['date'])) {
            $topStart = $qs['date'] . '-01';
            $topEnd   = date('Y-m-t', strtotime($topStart));
        } else {
            $topStart = $qs['date'];
            $topEnd   = $qs['date'];
        }
    }
}
$path        = ltrim(parse_url($route, PHP_URL_PATH), '/');
$isUserRoute = strpos($path, 'user/') === 0;
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
    .pagination-controls {
      display: flex;
      align-items: center;
      gap: 0.5rem;
      margin-top: 0.5rem;
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
  <?php if ( ! $isUserRoute ): ?>
  <a class="btn btn-secondary mb-4" id="btn_top_users">Show Top Users</a>
  <a class="btn btn-secondary mb-4" id="btn_top_dashboards">Show Top Dashboards</a>
  <?php endif; ?>
  
  <!-- Wrapper for metric selector + top users table -->
  <div id="top_users_wrapper" style="display:none; margin-top:1rem;">
    <div class="mb-3">
      <label for="metric_selector" class="form-label">Select Metric:</label>
      <select id="metric_selector" class="form-select w-auto">
        <option value="dashboard_accesses">Dashboard Accesses</option>
        <option value="n_active_dashboards">Active Dashboards</option>
        <option value="n_active_iotapps">Active IoT Apps</option>
        <option value="n_devices">Devices</option>
        <option value="n_da_processes">DA Processes</option>
        <option value="n_odm_instances">ODM Instances</option>
        <option value="n_heatmaps">Heatmaps</option>
        <option value="n_traffic_flow">Traffic Flow</option>
        <option value="n_ascapi_accesses">ASC API Accesses</option>
        <option value="n_api_accesses">API Accesses</option>
      </select>
    </div>
    <div id="top_users_container"></div>
  </div>

  <!-- Wrapper for top dashboards table -->
  <div id="top_dashboards_wrapper" style="display:none; margin-top:1rem;">
    <div id="top_dashboards_container"></div>
  </div>

  <?php if ($http_code === 200 && is_array($data) && count($data) > 0): ?>
    <?php
      // Detect if this is the "all/usage" response (has top-level org keys and "all_organizations")
      $isAllUsage = isset($data['all_organizations']);

      // For tables, we always use either $data itself (for user/org routes) or $data['all_organizations']
      $tableData = $isAllUsage ? $data['all_organizations'] : $data;

      // discover scalar metrics & dates from $tableData
      $first   = reset($tableData);
      $metrics = array_values(array_filter(
        array_keys($first),
        function($k) use($first){ return !is_array($first[$k]); }
      ));
      $dates = array_keys($tableData);
      sort($dates);
      $multiDay = count($dates) > 1;

      // For charts: if all/usage, we need list of organizations to plot
      if ($isAllUsage) {
          $orgs = array_keys($data);
          // remove the 'all_organizations' key
          $orgs = array_filter($orgs, function($o) { 
              return $o !== 'all_organizations'; 
          });
          sort($orgs);
      }
      $encryptionMap = [];
      foreach ($tableData as $date => $row) {
          // owned_dashboard_activity OR owned_dashboard_usage
          $owned = $row['owned_dashboard_activity']
                 ?? $row['owned_dashboard_usage']
                 ?? [];
          foreach (array_keys($owned) as $dashId) {
              if (!isset($encryptionMap[$dashId])) {
                $encryptionMap[$dashId] = base64_encode($dashId);
              }
          }
      }
    ?>

    <!-- Data Table -->
    <div class="main-table mb-4" id="activity_table">
      <table class="table table-striped">
        <thead>
          <tr>
            <th>Date</th>
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
                <td class="datapoint text-center">
                  <?= htmlspecialchars($tableData[$date][$m] ?? '') ?>
                </td>
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
        // raw data from API, which for “all/usage” is an array‐of‐arrays:
        $rawUsed = $tableData[$date]['dashboard_used_by_org']
                  ?? $tableData[$date]['dashboard_activity_summary']
                  ?? [];
        if ($isAllUsage) {
          // flatten the nested lists into one single list
          $flat = [];
          foreach ($rawUsed as $sublist) {
            if (is_array($sublist)) {
              foreach ($sublist as $item) {
                $flat[] = $item;
              }
            }
          }
          $dashboards_used_by_date[$date] = $flat;
        } else {
          // single‐org/user route
          $dashboards_used_by_date[$date] = $rawUsed;
        }
        $dashboards_owned_by_date[$date] = $tableData[$date]['owned_dashboard_activity']
                                          ?? $tableData[$date]['owned_dashboard_usage']
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
                      $a = (int) ($e['nAccessPerDay']  ?? 0);
                      $m = (int) ($e['nMinutesPerDay'] ?? 0);
                      $totalAccesses += $a;
                      $totalMinutes  += $m;
                    ?>
                      <li>
                        ID <strong><?= htmlspecialchars($e['idDashboard'] ?? '') ?></strong>:
                        <strong><?= $a ?></strong> access<?= $a!==1 ? 'es' : '' ?>,
                        <strong><?= $m ?></strong> minute<?= $m!==1 ? 's' : '' ?>
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
                      $a = (int) ($e['total_accesses'] ?? 0);
                      $m = (int) ($e['total_minutes'] ?? 0);
                      $totalAccesses += $a;
                      $totalMinutes  += $m;
                    ?>
                      <li>
                        ID <strong><?= htmlspecialchars($dashId) ?></strong>:
                        <strong><?= $a ?></strong> access<?= $a!==1 ? 'es' : '' ?>,
                        <strong><?= $m ?></strong> minute<?= $m!==1 ? 's' : '' ?>
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
        $api_manager_usage[$date] = $tableData[$date]['api_manager'] ?? [];
        $ascapi_usage[$date]      = $tableData[$date]['ascapi']      ?? [];
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

<?php
// JSON safe inside <script>: escapes < > & ' " so data can't close the script tag
$jsFlags = JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT;
?>
<?php if ($http_code === 200 && !empty($metrics)): ?>
<script>
const dates       = <?= json_encode($dates, $jsFlags) ?>;
const metrics     = <?= json_encode($metrics, $jsFlags) ?>;
const chartData   = <?= json_encode($data, $jsFlags) ?>;
const phpData     = <?= json_encode($tableData, $jsFlags) ?>;
const isAllUsage  = <?= $isAllUsage ? 'true' : 'false' ?>;
const multiDay    = <?= $multiDay ? 'true' : 'false' ?>;
const currentOrg  = <?= json_encode($orgParam, $jsFlags) ?>;
const topStart    = <?= json_encode($topStart, $jsFlags) ?>;
const topEnd      = <?= json_encode($topEnd, $jsFlags) ?>;
const token       = <?= json_encode($token, $jsFlags) ?>;
const apiBase     = <?= json_encode($api_base, $jsFlags) ?>;
const loggedOrg   = <?= json_encode($loggedorg, $jsFlags) ?>;
const dashboardUrlPrefix = <?= json_encode($protocol . '://' . $appHost . '/dashboardSmartCity/view/index.php?iddasboard=', $jsFlags) ?>;
const encryptionMap      = <?= json_encode($encryptionMap, $jsFlags) ?>;

document.addEventListener('DOMContentLoaded', function(){
  //charts only exist with more than one date
  if (multiDay) {
  <?php if ($isAllUsage): ?>
    // For all/usage: we have multiple orgs
    const orgs = <?= json_encode($orgs) ?>;
    metrics.forEach(metric => {
      const canvas = document.getElementById('chart_' + metric);
      if (!canvas) return;
      const ctx = canvas.getContext('2d');
      const datasets = orgs.map(org => {
        const vals = dates.map(d => {
          const v = (chartData[org][d] && chartData[org][d][metric]);
          return (typeof v === 'number') ? v : parseInt(v)||0;
        });
        return {
          label: org,
          data: vals,
          fill: false
        };
      });
      new Chart(ctx, {
        type: 'line',
        data: {
          labels: dates,
          datasets: datasets
        },
        options: {
          responsive: true,
          plugins: { title: { display: true, text: metric.replace(/_/g,' ') } },
          scales: { y: { beginAtZero: true } }
        }
      });
    });
  <?php else: ?>
    // For single-org/user routes: one line per metric
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
  <?php endif; ?>
  }

  const btns = [
    'btn_activity_table',
    'btn_charts',
    'btn_dashboards',
    'btn_api_table'
  ];
  btns.forEach(id => {
    const btn = document.getElementById(id);
    if (!btn) return;
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

  // Top Users Variables
  let allUsersData = [];
  let filteredUsers = [];
  let currentUserPage = 1;
  const usersPerPage = 10;

  // Top Dashboards Variables
  let allDashData = [];
  let filteredDash = [];
  let currentDashPage = 1;
  const dashPerPage = 10;

  // Fetch Top Users from API
  async function fetchTopUsers(metric) {
    //period selected in the form, falls back to the dates that have data
    const startDate = topStart || dates[0];
    const endDate = topEnd || dates[dates.length - 1];
    let endpoint = '';
    if (isAllUsage) {
      endpoint = `all/top_users?metric=${metric}&start_date=${startDate}&end_date=${endDate}`;
    } else {
      endpoint = `org/top_users?org=${encodeURIComponent(currentOrg)}&metric=${metric}&start_date=${startDate}&end_date=${endDate}`;
    }
    const resp = await fetch(apiBase + endpoint, {
      headers: {
        'Authorization': 'Bearer ' + token,
        'Accept': 'application/json',
        'loggedOrganization': loggedOrg
      }
    });
    if (resp.status === 401) throw new Error('Your session has expired, please log in again.');
    if (!resp.ok) throw new Error('Failed to load top users: ' + await resp.text());
    return resp.json();
  }

  function computeTopDashboards() {
  const totals = {};
  // decide where our “per-day” data lives
  const days     = isAllUsage
                 ? Object.keys(chartData.all_organizations || {})
                 : Object.keys(phpData);
  days.forEach(date => {
    const dayData = isAllUsage
                  ? chartData.all_organizations[date]
                  : phpData[date];
    const ownedMap = dayData.owned_dashboard_activity
                  || dayData.owned_dashboard_usage
                  || {};
    Object.entries(ownedMap).forEach(([dashId, stats]) => {
      if (!totals[dashId]) totals[dashId] = { accesses:0, minutes:0 };
      totals[dashId].accesses += (stats.total_accesses || 0);
      totals[dashId].minutes  += (stats.total_minutes  || 0);
    });
  });

  return Object.entries(totals)
    .sort(([,a],[,b]) => b.accesses - a.accesses)
    .map(([id,stats]) => ({ id, accesses: stats.accesses, minutes: stats.minutes }));
}


  // Render Top Users with pagination & search
  function renderTopUsers(metric) {
    const wrapper = document.getElementById('top_users_wrapper');
    const container = document.getElementById('top_users_container');
    container.innerHTML = '';

    // Build data arrays
    allUsersData = allUsersData.map(u => ({ username: u.username, value: u[metric] }));
    filteredUsers = allUsersData.slice();
    currentUserPage = 1;

    // Header
    const header = document.createElement('h5');
    header.textContent = `Top Users by sum of ${metric.replace(/_/g,' ')} in the selected dates`;
    container.appendChild(header);

    // Search input
    const searchDiv = document.createElement('div');
    searchDiv.className = 'mb-2';
    searchDiv.innerHTML = `<input type="text" id="user_search" placeholder="Search user" class="form-control w-auto">`;
    container.appendChild(searchDiv);

    // Table
    const table = document.createElement('table');
    table.className = 'table table-sm table-striped';
    const thead = document.createElement('thead');
    thead.innerHTML = `
      <tr>
        <th>User</th>
        <th class="text-center">sum of ${metric.replace(/_/g, ' ')}</th>
      </tr>`;
    table.appendChild(thead);
    const tbody = document.createElement('tbody');
    table.appendChild(tbody);
    container.appendChild(table);

    // Pagination controls
    const paginationDiv = document.createElement('div');
    paginationDiv.className = 'pagination-controls';
    paginationDiv.innerHTML = `
      <button id="user_prev" class="btn btn-secondary btn-sm" disabled>Previous</button>
      <span id="user_page_info"></span>
      <button id="user_next" class="btn btn-secondary btn-sm" disabled>Next</button>
    `;
    container.appendChild(paginationDiv);

    displayUserPage();

    // Event listeners
    document.getElementById('user_search').addEventListener('input', function(){
      const term = this.value.trim().toLowerCase();
      filteredUsers = allUsersData.filter(u => u.username.toLowerCase().includes(term));
      currentUserPage = 1;
      displayUserPage();
    });
    document.getElementById('user_prev').addEventListener('click', function(){
      if (currentUserPage > 1) {
        currentUserPage--;
        displayUserPage();
      }
    });
    document.getElementById('user_next').addEventListener('click', function(){
      const maxPage = Math.ceil(filteredUsers.length / usersPerPage);
      if (currentUserPage < maxPage) {
        currentUserPage++;
        displayUserPage();
      }
    });

    function displayUserPage() {
      const startIdx = (currentUserPage - 1) * usersPerPage;
      const pageItems = filteredUsers.slice(startIdx, startIdx + usersPerPage);

      tbody.innerHTML = '';
      pageItems.forEach(item => {
        //textContent: usernames are never parsed as html
        const tr = document.createElement('tr');
        const tdUser = document.createElement('td');
        tdUser.textContent = item.username;
        const tdValue = document.createElement('td');
        tdValue.className = 'text-center';
        tdValue.textContent = item.value;
        tr.append(tdUser, tdValue);
        tbody.appendChild(tr);
      });

      const maxPage = Math.max(1, Math.ceil(filteredUsers.length / usersPerPage));
      document.getElementById('user_page_info').textContent = `Page ${currentUserPage} of ${maxPage}`;
      document.getElementById('user_prev').disabled = (currentUserPage === 1);
      document.getElementById('user_next').disabled = (currentUserPage === maxPage);
    }
  }

  // Render Top Dashboards with pagination & search
  function renderTopDashboardsPaged() {
    const wrapper = document.getElementById('top_dashboards_wrapper');
    const container = document.getElementById('top_dashboards_container');
    container.innerHTML = '';

    // Build data arrays
    allDashData = computeTopDashboards();
    filteredDash = allDashData.slice();
    currentDashPage = 1;

    // Header
    const header = document.createElement('h5');
    header.textContent = `Top Dashboards by sum of accesses in the selected dates`;
    container.appendChild(header);

    // Search input
    const searchDiv = document.createElement('div');
    searchDiv.className = 'mb-2';
    searchDiv.innerHTML = `<input type="text" id="dash_search" placeholder="Search dashboard ID" class="form-control w-auto">`;
    container.appendChild(searchDiv);

    // Table
    const table = document.createElement('table');
    table.className = 'table table-sm table-striped';
    const thead = document.createElement('thead');
    thead.innerHTML = `
      <tr>
        <th>Dashboard ID</th>
        <th class="text-center">Total Accesses</th>
        <th class="text-center">Total Minutes</th>
      </tr>`;
    table.appendChild(thead);
    const tbody = document.createElement('tbody');
    table.appendChild(tbody);
    container.appendChild(table);

    // Pagination controls
    const paginationDiv = document.createElement('div');
    paginationDiv.className = 'pagination-controls';
    paginationDiv.innerHTML = `
      <button id="dash_prev" class="btn btn-secondary btn-sm" disabled>Previous</button>
      <span id="dash_page_info"></span>
      <button id="dash_next" class="btn btn-secondary btn-sm" disabled>Next</button>
    `;
    container.appendChild(paginationDiv);

    displayDashPage();

    // Event listeners
    document.getElementById('dash_search').addEventListener('input', function(){
      const term = this.value.trim().toLowerCase();
      filteredDash = allDashData.filter(d => d.id.toLowerCase().includes(term));
      currentDashPage = 1;
      displayDashPage();
    });
    document.getElementById('dash_prev').addEventListener('click', function(){
      if (currentDashPage > 1) {
        currentDashPage--;
        displayDashPage();
      }
    });
    document.getElementById('dash_next').addEventListener('click', function(){
      const maxPage = Math.ceil(filteredDash.length / dashPerPage);
      if (currentDashPage < maxPage) {
        currentDashPage++;
        displayDashPage();
      }
    });

    function displayDashPage() {
      const startIdx = (currentDashPage - 1) * dashPerPage;
      const pageItems = filteredDash.slice(startIdx, startIdx + dashPerPage);

      tbody.innerHTML = '';
      pageItems.forEach(item => {
        const enc  = encryptionMap[item.id] || '';
        const tr   = document.createElement('tr');
        const tdId = document.createElement('td');
        const link = document.createElement('a');
        link.href = dashboardUrlPrefix + encodeURIComponent(enc);
        link.target = '_blank';
        link.textContent = item.id;
        tdId.appendChild(link);
        const tdAcc = document.createElement('td');
        tdAcc.className = 'text-center';
        tdAcc.textContent = item.accesses;
        const tdMin = document.createElement('td');
        tdMin.className = 'text-center';
        tdMin.textContent = item.minutes;
        tr.append(tdId, tdAcc, tdMin);
        tbody.appendChild(tr);
      });

      const maxPage = Math.max(1, Math.ceil(filteredDash.length / dashPerPage));
      document.getElementById('dash_page_info').textContent = `Page ${currentDashPage} of ${maxPage}`;
      document.getElementById('dash_prev').disabled = (currentDashPage === 1);
      document.getElementById('dash_next').disabled = (currentDashPage === maxPage);
    }
  }

  // Initial load for Top Users and Top Dashboards (buttons don't exist on user routes)
  if (!document.getElementById('btn_top_users')) return;
  document.getElementById('btn_top_users').addEventListener('click', async function(){
    const wrapper = document.getElementById('top_users_wrapper');
    if (wrapper.style.display === 'none' || wrapper.innerHTML.trim() === '') {
      const selectedMetric = document.getElementById('metric_selector').value;
      try {
        const userList = await fetchTopUsers(selectedMetric);
        allUsersData = userList.slice(); // store raw data
        renderTopUsers(selectedMetric);
        wrapper.style.display = '';
        this.textContent = 'Hide Top Users';
      } catch(e) {
        console.error(e);
        alert(e.message || 'Failed to load top users.');
      }
    } else {
      wrapper.style.display = 'none';
      this.textContent = 'Show Top Users';
    }
  });

  document.getElementById('metric_selector').addEventListener('change', async function(){
    const wrapper = document.getElementById('top_users_wrapper');
    if (wrapper.style.display !== 'none' && wrapper.innerHTML.trim() !== '') {
      const selectedMetric = this.value;
      try {
        const userList = await fetchTopUsers(selectedMetric);
        allUsersData = userList.slice();
        renderTopUsers(selectedMetric);
      } catch(e) {
        console.error(e);
        alert(e.message || 'Failed to update top users.');
      }
    }
  });

  document.getElementById('btn_top_dashboards').addEventListener('click', function(){
    const wrapper = document.getElementById('top_dashboards_wrapper');
    if (wrapper.style.display === 'none' || wrapper.innerHTML.trim()==='') {
      renderTopDashboardsPaged();
      wrapper.style.display = '';
      this.textContent = 'Hide Top Dashboards';
    } else {
      wrapper.style.display = 'none';
      this.textContent = 'Show Top Dashboards';
    }
  });
});
</script>
<?php endif; ?>
</body>
</html>
