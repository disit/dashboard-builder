<?php
$ip = $_REQUEST['IP'];


?>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>IP Cam</title>
</head>
<body>
<img id="IPcamLink" src="getIPjpeg.php" width="100%">
<script>
    function increment() {
        console.log('interval');
      //  document.getElementById('IPcamLink').src = "getIPjpeg.php?t=" + new Date().getTime();
        document.getElementById('IPcamLink').src = "getIPjpeg.php?t=" + new Date().getTime() + "&ip=" + '<?php echo $_REQUEST['IP'] ;?>';     // GP MOD
    }
    setInterval(increment, 1000);   // Refresh di cattura immagine
</script>
</body>
</html>
