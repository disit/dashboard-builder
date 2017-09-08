<?php
   session_start();
   
   header('Content-Type: text/event-stream');
   header('Cache-Control: no-cache');
   //echo 'data: ' . strftime('%H:%M:%S',time()) . PHP_EOL;
   echo 'data: ' . $_SESSION['inducedLogout'] . PHP_EOL;
   echo PHP_EOL;
   flush();
