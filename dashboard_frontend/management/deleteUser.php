<?php

/* Dashboard Builder.
   Copyright (C) 2017 DISIT Lab https://www.disit.org - University of Florence

   This program is free software: you can redistribute it and/or modify
   it under the terms of the GNU Affero General Public License as
   published by the Free Software Foundation, either version 3 of the
   License, or (at your option) any later version.
   This program is distributed in the hope that it will be useful,
   but WITHOUT ANY WARRANTY; without even the implied warranty of
   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
   GNU Affero General Public License for more details.
   You should have received a copy of the GNU Affero General Public License
   along with this program.  If not, see <http://www.gnu.org/licenses/>. */

   include '../config.php';
$dashboardDbBufferLevel = ob_get_level();
ob_start();
try {
   
   //Altrimenti restituisce in output le warning
   error_reporting(E_ERROR | E_NOTICE);
   
   session_start(); 
   checkSession('RootAdmin');
   
   $link = mysqli_connect($host, $username, $password);
   mysqli_select_db($link, $dbname);
   
   if(!$link->set_charset("utf8")) 
   {
       die();
   }

   if(isset($_SESSION['loggedRole']))
   {
      $username = $_POST['username'];
      
      $beginTransactionResult = mysqli_begin_transaction($link);
      
      if (!$beginTransactionResult) throw new \mysqli_sql_exception('Transaction start failed');

      $query = "DELETE FROM Dashboard.UsersPoolsRelations WHERE username = '$username'";
      $result = mysqli_query($link, $query);

      if($result)
      {
          $query2 = "DELETE FROM Dashboard.Users WHERE username = '$username'";
          $result2 = mysqli_query($link, $query2);

          if($result2)
          {
             $commit = mysqli_commit($link);
             if (!$commit) throw new \mysqli_sql_exception('Transaction commit failed');
             mysqli_close($link);
             echo 1;
          }
          else
          {
             $rollbackResult = mysqli_rollback($link);
             mysqli_close($link);
             echo 0;
          }
      }
      else
      {
         $rollbackResult = mysqli_rollback($link);
         mysqli_close($link);
         echo 0;
      }
   }
} catch (\mysqli_sql_exception $exception) {
    if (isset($link)) {
        try { mysqli_rollback($link); } catch (\Throwable $rollbackError) { /* Preserve the numeric failure if cleanup also fails. */ }
        try { mysqli_close($link); } catch (\Throwable $closeError) { /* The connection may already be unavailable. */ }
    }
    while (ob_get_level()>$dashboardDbBufferLevel) ob_end_clean();
    error_log('Legacy user/pool database failure ['.(int)$exception->getCode().'] at '.basename(__FILE__).':'.$exception->getLine());
    http_response_code(500);
    echo 0;
}
if (ob_get_level()>$dashboardDbBufferLevel) ob_end_flush();
