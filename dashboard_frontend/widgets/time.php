<span id="tick2"></span>

<script>
    function updateTime() 
    {
        var now = new Date();
        var days = new Array();
        var months = new Array();
        
        days[0] = "Sun";
        days[1] = "Mon";
        days[2] = "Tue";
        days[3] = "Wed";
        days[4] = "Thu";
        days[5] = "Fri";
        days[6] = "Sat";
        
        months[0] = "Jan";
        months[1] = "Feb";
        months[2] = "Mar";
        months[3] = "Apr";
        months[4] = "May";
        months[5] = "Jun";
        months[6] = "Jul";
        months[7] = "Aug";
        months[8] = "Sep";
        months[9] = "Oct";
        months[10] = "Nov";
        months[11] = "Dec";
      
        if(!document.all && !document.getElementById)
        {
           return;
        }
            
        var timeContainer = document.getElementById ? document.getElementById("tick2") : document.all.tick2;
        
        var day = days[now.getDay()];
        var month = months[now.getMonth()];
        var hours = now.getHours();
        var minutes = now.getMinutes();
        var seconds = now.getSeconds();
        
        if(hours <= 9)
        {
           hours = "0" + hours;
        }
            
        if(minutes <= 9)
        {
           minutes = "0" + minutes;
        }
            
        if(seconds <= 9)
        {
           seconds = "0" + seconds;
        }
            
        var ctime = day + " " + now.getDate() + " " + month + " " + hours + ":" + minutes + ":" + seconds;
        timeContainer.innerHTML = ctime;
        setTimeout("updateTime()", 1000);
    }
    
    
    updateTime();
</script>