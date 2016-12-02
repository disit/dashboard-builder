<?
date_default_timezone_set('CET');
//echo date('D j M @ H:i:s'); 
?>

<span id=tick2>
</span>

<script>
<!--

    /*By JavaScript Kit
     http://javascriptkit.com
     Credit MUST stay intact for use
     */

    function getDate(offset) {
        var now = new Date();
        var hour = 60 * 60 * 1000;
        var min = 60 * 1000;
        return new Date(now.getTime() + (now.getTimezoneOffset() * min) + (offset * hour));
    }

    function show2() {
        if (!document.all && !document.getElementById)
            return
        thelement = document.getElementById ? document.getElementById("tick2") : document.all.tick2
        var Digital = getDate(1);
        var days = new Array();
        days[0] = "Sun";
        days[1] = "Mon";
        days[2] = "Tue";
        days[3] = "Wed";
        days[4] = "Thu";
        days[5] = "Fri";
        days[6] = "Sat";
        var day = days[Digital.getDay()];
        var month = new Array();
        month[0] = "Jan";
        month[1] = "Feb";
        month[2] = "Mar";
        month[3] = "Apr";
        month[4] = "May";
        month[5] = "Jun";
        month[6] = "Jul";
        month[7] = "Aug";
        month[8] = "Sep";
        month[9] = "Oct";
        month[10] = "Nov";
        month[11] = "Dec";
        var month_name = month[Digital.getMonth()];
        var hours = Digital.getHours()
        var minutes = Digital.getMinutes()
        var seconds = Digital.getSeconds()
        if (hours <= 9)
            hours = "0" + hours
        if (minutes <= 9)
            minutes = "0" + minutes
        if (seconds <= 9)
            seconds = "0" + seconds
        var ctime = day + " " + Digital.getDate() + " " + month_name + " @ " + hours + ":" + minutes + ":" + seconds
        thelement.innerHTML = ctime
        setTimeout("show2()", 1000)
    }
    $(function () {
        show2();
    });//-->
</script>