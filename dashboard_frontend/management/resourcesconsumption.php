<?php
include('../config.php');
if (!isset($_SESSION)) {
    session_start();
}

checkSession('Manager');

$role = $_SESSION['loggedRole'];
$canSeeAdmin = ($role === 'RootAdmin');

// fetch delegated orgs if AreaManager
$delegatedOrgs = [];
if ($role === 'AreaManager') {
    $link = mysqli_connect(
        $resourcesconsumptionHost,
        $resourcesconsumptionUser,
        $resourcesconsumptionPassword,
        $resourcesconsumptionDb,
        $resourcesconsumptionPort
    );
    if ($link) {
        $encMe = encryptOSSL(
            $_SESSION['loggedUsername'],
            $encryptionInitKey,
            $encryptionIvKey,
            $encryptionMethod
        );
        if ($encMe) {
            $sql = "SELECT delegated_orgs FROM users WHERE owner = ?";
            $stmt = mysqli_prepare($link, $sql);
            mysqli_stmt_bind_param($stmt, "s", $encMe);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_bind_result($stmt, $csv);
            if (mysqli_stmt_fetch($stmt) && $csv) {
                $delegatedOrgs = array_filter(array_map('trim', explode(',', $csv)));
                if (count($delegatedOrgs) > 0) {
                    $canSeeAdmin = true;
                }
            }
            mysqli_stmt_close($stmt);
        }
        mysqli_close($link);
    }
}

// if admin, fetch & decrypt users
$allUsers = [];
if ($canSeeAdmin) {
    $link2 = mysqli_connect(
        $resourcesconsumptionHost,
        $resourcesconsumptionUser,
        $resourcesconsumptionPassword,
        $resourcesconsumptionDb,
        $resourcesconsumptionPort
    );
    if ($link2) {
        $sqlU   = "SELECT owner FROM users";
        $params = [];
        $types  = "";

        if ($role === 'AreaManager' && !empty($delegatedOrgs)) {
            $ph     = implode(',', array_fill(0, count($delegatedOrgs), '?'));
            $sqlU  .= " WHERE organization IN ($ph)";
            $types  = str_repeat('s', count($delegatedOrgs));
            $params = $delegatedOrgs;
        }

        $stmtU = mysqli_prepare($link2, $sqlU);
        if ($stmtU && !empty($params)) {
            mysqli_stmt_bind_param($stmtU, $types, ...$params);
        }

        mysqli_stmt_execute($stmtU);
        mysqli_stmt_bind_result($stmtU, $encOwner);
        while (mysqli_stmt_fetch($stmtU)) {
            $dec = decryptOSSL(
                $encOwner,
                $encryptionInitKey,
                $encryptionIvKey,
                $encryptionMethod
            );
            if ($dec) {
                $allUsers[] = $dec;
            }
        }
        mysqli_stmt_close($stmtU);
        mysqli_close($link2);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Resources Consumption Dashboard</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"
        rel="stylesheet">
</head>
<body class="bg-light">
  <div class="container py-5">
    <h2 class="mb-4">📊 Resources Consumption Dashboard</h2>
    <h4>Organization: <?= htmlspecialchars($_SESSION['loggedOrganization'], ENT_QUOTES) ?></h4>

    <?php if (!$canSeeAdmin): ?>
  <!-- Non-admin: choose day / month / range for SELF -->
  <form id="userForm" method="post" action="resourcesconsumptionFetch.php"
        class="card p-4 shadow-sm">
    <input type="hidden" name="route" id="routeInputUser" value="">

    <!-- Period: Day vs Month vs Range -->
    <div class="mb-3">
      <label class="form-label">Period</label>
      <div>
        <label class="form-check form-check-inline">
          <input class="form-check-input" type="radio"
                 name="periodType" id="u_byDay" value="day" checked>
          <span class="form-check-label">Day</span>
        </label>
        <label class="form-check form-check-inline">
          <input class="form-check-input" type="radio"
                 name="periodType" id="u_byMonth" value="month">
          <span class="form-check-label">Month</span>
        </label>
        <label class="form-check form-check-inline">
          <input class="form-check-input" type="radio"
                 name="periodType" id="u_byRange" value="range">
          <span class="form-check-label">Range</span>
        </label>
      </div>
    </div>

    <!-- Day input -->
    <div class="mb-3" id="u_dayContainer">
      <label class="form-label" for="u_dateDay">Date (YYYY-MM-DD)</label>
      <input type="date" id="u_dateDay" class="form-control">
    </div>

    <!-- Month input -->
    <div class="mb-3" id="u_monthContainer" style="display:none">
      <label class="form-label" for="u_dateMonth">Month (YYYY-MM)</label>
      <input type="month" id="u_dateMonth" class="form-control">
    </div>

    <!-- Range inputs -->
    <div class="mb-3" id="u_rangeContainer" style="display:none">
      <label class="form-label">From / To</label>
      <div class="d-flex gap-2">
        <input type="date" id="u_startDate" class="form-control" placeholder="Start date">
        <input type="date" id="u_endDate"   class="form-control" placeholder="End date">
      </div>
    </div>

    <button type="submit" class="btn btn-primary">View My Usage</button>
  </form>

  <script>
  (function(){
    const byDay   = document.getElementById('u_byDay');
    const byMon   = document.getElementById('u_byMonth');
    const byRange = document.getElementById('u_byRange');

    const dayCont   = document.getElementById('u_dayContainer');
    const monCont   = document.getElementById('u_monthContainer');
    const rangeCont = document.getElementById('u_rangeContainer');

    const dateDay   = document.getElementById('u_dateDay');
    const dateMon   = document.getElementById('u_dateMonth');
    const startDate = document.getElementById('u_startDate');
    const endDate   = document.getElementById('u_endDate');

    const form      = document.getElementById('userForm');
    const routeIn   = document.getElementById('routeInputUser');

    function toggleFields(){
      dayCont.style.display   = byDay.checked   ? 'block' : 'none';
      monCont.style.display   = byMon.checked   ? 'block' : 'none';
      rangeCont.style.display = byRange.checked ? 'block' : 'none';

      dateDay.required   = byDay.checked;
      dateMon.required   = byMon.checked;
      startDate.required = byRange.checked;
      endDate.required   = byRange.checked;
    }

    [byDay, byMon, byRange].forEach(el => 
      el.addEventListener('change', toggleFields)
    );

    form.addEventListener('submit', () => {
      // base route always self=true
      let route = 'user/selected_usage?self=true';

      if (byDay.checked) {
        route += `&date=${dateDay.value}`;
      } else if (byMon.checked) {
        route += `&date=${dateMon.value}`;
      } else {
        route += `&start_date=${startDate.value}&end_date=${endDate.value}`;
      }
      routeIn.value = route;
    });

    // initialize
    toggleFields();
  })();
  </script>

    <?php else: ?>
      <!-- Admin form -->
      <form id="adminForm" method="post"
            action="resourcesconsumptionFetch.php"
            class="card p-4 shadow-sm">
        <input type="hidden" name="route" id="routeInput" value="">

        <!-- User vs Org -->
        <div class="mb-3">
          <label class="form-label">Query for</label>
          <div>
            <label class="form-check form-check-inline">
              <input class="form-check-input" type="radio"
                     name="queryType" id="byUser" value="user" checked>
              <span class="form-check-label">User</span>
            </label>
            <label class="form-check form-check-inline">
              <input class="form-check-input" type="radio"
                     name="queryType" id="byOrg" value="org">
              <span class="form-check-label">Organization</span>
            </label>
          </div>
        </div>

        <!-- Username -->
        <div class="mb-3" id="userSelection">
          <label for="usernameInput" class="form-label">Username</label>
          <input list="users" id="usernameInput" class="form-control"
                 placeholder="Start typing or enter username">
          <datalist id="users">
            <?php foreach ($allUsers as $u): ?>
              <option value="<?= htmlspecialchars($u, ENT_QUOTES) ?>">
            <?php endforeach; ?>
          </datalist>
        </div>

        <!-- Organization -->
        <div class="mb-3" id="orgSelection" style="display:none">
          <label for="orgInput" class="form-label">Organization</label>
          <input list="orgs" id="orgInput" class="form-control"
                 placeholder="Select or enter org">
          <datalist id="orgs">
            <?php if ($role === 'RootAdmin'): ?>
              <!-- TODO: trovare lista di org -->
              <option value="Organization">
              <option value="OrgB">
            <?php else: ?>
              <?php foreach ($delegatedOrgs as $o): ?>
                <option value="<?= htmlspecialchars($o, ENT_QUOTES) ?>">
              <?php endforeach; ?>
            <?php endif; ?>
          </datalist>
        </div>

        <!-- Period: Day vs Month vs Range -->
        <div class="mb-3">
          <label class="form-label">Period</label>
          <div>
            <label class="form-check form-check-inline">
              <input class="form-check-input" type="radio"
                     name="periodType" id="byDay" value="day" checked>
              <span class="form-check-label">Day</span>
            </label>
            <label class="form-check form-check-inline">
              <input class="form-check-input" type="radio"
                     name="periodType" id="byMonth" value="month">
              <span class="form-check-label">Month</span>
            </label>
            <label class="form-check form-check-inline">
              <input class="form-check-input" type="radio"
                     name="periodType" id="byRange" value="range">
              <span class="form-check-label">Range</span>
            </label>
          </div>
        </div>

        <!-- Day input -->
        <div class="mb-3" id="dayInputContainer">
          <label class="form-label" for="dateInputDay">Date (YYYY-MM-DD)</label>
          <input type="date" id="dateInputDay" class="form-control">
        </div>

        <!-- Month input -->
        <div class="mb-3" id="monthInputContainer" style="display:none">
          <label class="form-label" for="dateInputMonth">Month (YYYY-MM)</label>
          <input type="month" id="dateInputMonth" class="form-control">
        </div>

        <!-- Range inputs -->
        <div class="mb-3" id="rangeInputContainer" style="display:none">
          <label class="form-label">From / To</label>
          <div class="d-flex gap-2">
            <input type="date" id="startDateInput" class="form-control" placeholder="Start date">
            <input type="date" id="endDateInput"   class="form-control" placeholder="End date">
          </div>
        </div>

        <button type="submit" class="btn btn-primary">Fetch Data</button>
      </form>

      <script>
      (function(){
        const byUser    = document.getElementById('byUser');
        const byOrg     = document.getElementById('byOrg');
        const byDay     = document.getElementById('byDay');
        const byMonth   = document.getElementById('byMonth');
        const byRange   = document.getElementById('byRange');

        const userInput     = document.getElementById('usernameInput');
        const orgInput      = document.getElementById('orgInput');
        const dateDay       = document.getElementById('dateInputDay');
        const dateMon       = document.getElementById('dateInputMonth');
        const startDate     = document.getElementById('startDateInput');
        const endDate       = document.getElementById('endDateInput');

        const userSel       = document.getElementById('userSelection');
        const orgSel        = document.getElementById('orgSelection');
        const dayCont       = document.getElementById('dayInputContainer');
        const monCont       = document.getElementById('monthInputContainer');
        const rangeCont     = document.getElementById('rangeInputContainer');

        const routeIn       = document.getElementById('routeInput');
        const form          = document.getElementById('adminForm');

        function toggleFields(){
          // user vs org
          userSel.style.display   = byUser.checked  ? 'block' : 'none';
          orgSel.style.display    = byOrg.checked   ? 'block' : 'none';
          // period type
          dayCont.style.display   = byDay.checked   ? 'block' : 'none';
          monCont.style.display   = byMonth.checked ? 'block' : 'none';
          rangeCont.style.display = byRange.checked ? 'block' : 'none';

          // required / disabled toggles
          userInput.required  = byUser.checked;
          userInput.disabled  = !byUser.checked;
          orgInput.required   = byOrg.checked;
          orgInput.disabled   = !byOrg.checked;

          dateDay.required    = byDay.checked;
          dateDay.disabled    = !byDay.checked;
          dateMon.required    = byMonth.checked;
          dateMon.disabled    = !byMonth.checked;
          startDate.required  = byRange.checked;
          startDate.disabled  = !byRange.checked;
          endDate.required    = byRange.checked;
          endDate.disabled    = !byRange.checked;
        }

        [byUser,byOrg,byDay,byMonth,byRange].forEach(el=>
          el.addEventListener('change', toggleFields)
        );

        form.addEventListener('submit', ()=>{
          let route;
          if (byUser.checked) {
            const user = encodeURIComponent(userInput.value);
            if (byDay.checked) {
              route = `user/selected_usage?username=${user}&date=${dateDay.value}`;
            } else if (byMonth.checked) {
              route = `user/selected_usage?username=${user}&date=${dateMon.value}`;
            } else {
              route = `user/selected_usage?username=${user}`
                    + `&start_date=${startDate.value}`
                    + `&end_date=${endDate.value}`;
            }
          } else {
            const org = encodeURIComponent(orgInput.value);
            if (byDay.checked) {
              route = `org/usage?org=${org}&date=${dateDay.value}`;
            } else if (byMonth.checked) {
              route = `org/usage?org=${org}&date=${dateMon.value}`;
            } else {
              route = `org/usage?org=${org}`
                    + `&start_date=${startDate.value}`
                    + `&end_date=${endDate.value}`;
            }
          }
          routeIn.value = route;
        });

        // initial toggle
        toggleFields();
      })();
      </script>
    <?php endif; ?>

  </div>
</body>
</html>
