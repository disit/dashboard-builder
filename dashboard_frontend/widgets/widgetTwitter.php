<?php
/* Dashboard Builder.
   Copyright (C) 2016 DISIT Lab https://www.disit.org - University of Florence

   This program is free software; you can redistribute it and/or
   modify it under the terms of the GNU General Public License
   as published by the Free Software Foundation; either version 2
   of the License, or (at your option) any later version.
   This program is distributed in the hope that it will be useful,
   but WITHOUT ANY WARRANTY; without even the implied warranty of
   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
   GNU General Public License for more details.
   You should have received a copy of the GNU General Public License
   along with this program; if not, write to the Free Software
   Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA. */
?>

<script type='text/javascript'>
  
    $(document).ready(function twitterRet() {
        $.ajax({
            url: "../widgets/getDataMetrics.php",
            data: {"IdMisura": ["TweetsRet_Florence_Day"]},
            type: "GET",
            async: true,
            dataType: 'json',
            success: function (msg) {
                if (msg.data.lenght>0){
                var value_tweet = msg.data[0].commit.author.value_num;
                var value_retweet = msg.data[0].commit.author.quant_perc1;
                $("#twitter_t").html(value_tweet + " per giorno");
                $("#twitter_ret").html(value_retweet + " per giorno");
                var counter = 3600;
                var countdown = setInterval(function () {
                   
                    counter--;
                    if (counter === 0) {
                      
                        clearInterval(countdown);
                        setTimeout(twitterRet, 1000);

                    }
                }, 1000);
            }
            }
        });
    });



</script>