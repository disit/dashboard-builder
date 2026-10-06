<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

// Language detection
    $curr = '';

    // 1. Give precedence to lang_dash from session
    if (session_status() === PHP_SESSION_ACTIVE && isset($_SESSION['lang_dash']) && $_SESSION['lang_dash'] !== '') {
        $curr = $_SESSION['lang_dash'];
    }
    // 2. Else, give precedence to lang_dash from cookie
    elseif (isset($_COOKIE['lang_dash']) && $_COOKIE['lang_dash'] !== '') {
        $curr = $_COOKIE['lang_dash'];
        // If found in cookie, ensure it's also in session for consistency during the current request
        if (session_status() === PHP_SESSION_ACTIVE) {
            $_SESSION['lang_dash'] = $curr;
        }
    }
    // 3. If lang_dash is still not set, use lang from session
    elseif (session_status() === PHP_SESSION_ACTIVE && isset($_SESSION['lang']) && $_SESSION['lang'] !== '') {
        $curr = $_SESSION['lang'];
        // Since lang_dash was not set, we use lang and promote it to lang_dash
        $_SESSION['lang_dash'] = $curr;
    }
    // 4. Else, use lang from cookie
    elseif (isset($_COOKIE['lang']) && $_COOKIE['lang'] !== '') {
        $curr = $_COOKIE['lang'];
        // Since lang_dash was not set, we use lang and promote it to lang_dash
        if (session_status() === PHP_SESSION_ACTIVE) {
            $_SESSION['lang_dash'] = $curr;
        }
    }
    // 5. Final fallback: use $curr_lang if available
    elseif (isset($curr_lang) && $curr_lang !== '') {
        $curr = $curr_lang;
        // Since lang_dash was not set, we use $curr_lang and promote it to lang_dash
        if (session_status() === PHP_SESSION_ACTIVE) {
            $_SESSION['lang_dash'] = $curr;
        }
    }
echo "<script>const currentLang = " . json_encode($curr) . ";</script>";
?>
<!--<span id="tick2"></span>-->
<script>
    var lang = <?php echo json_encode($curr); ?>; // "it_IT" or "en_US" etc.

    function updateTime() 
    {
        var now = new Date();
        var days = [];
        var months = [];

        // --- language conditional ---
        if (lang === "it_IT") {
            days = ["Dom", "Lun", "Mar", "Mer", "Gio", "Ven", "Sab"];
            months = ["Gen", "Feb", "Mar", "Apr", "Mag", "Giu", "Lug", "Ago", "Set", "Ott", "Nov", "Dic"];
        } else { // default English
            days = ["Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat"];
            months = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
        }

        if(!document.all && !document.getElementById) return;

        var timeContainer = document.getElementById ? document.getElementById("tick2") : document.all.tick2;

        var day = days[now.getDay()];
        var month = months[now.getMonth()];
        var hours = now.getHours();
        var minutes = now.getMinutes();
        var seconds = now.getSeconds();

        if(hours <= 9) hours = "0" + hours;
        if(minutes <= 9) minutes = "0" + minutes;
        if(seconds <= 9) seconds = "0" + seconds;

        var ctime = day + " " + now.getDate() + " " + month + " " + hours + ":" + minutes + ":" + seconds;
        timeContainer.innerHTML = ctime;
        setTimeout(updateTime, 1000);
    }

    updateTime();
</script>