<?php

/*Dashboard Builder.
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
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
try {
   
   //Altrimenti restituisce in output le warning
   error_reporting(E_ERROR | E_NOTICE);
   
   session_start(); 
   checkSession('AreaManager');
   
   $link = mysqli_connect($host, $username, $password) or die("Failed to connect to server");
   mysqli_select_db($link, $dbname);
   
   $queryFail = null;
   
   if(!$link->set_charset("utf8")) 
   {
       die();
   }

   if(isset($_SESSION['loggedRole']) && $_SESSION['loggedRole'] != "Public")
   {
      if(($_SESSION['loggedRole'] == "RootAdmin")||($_SESSION['loggedRole'] == "ToolAdmin")||($_SESSION['loggedRole'] == "AreaManager"))
      {
         $newPoolJson = json_decode($_POST['newPoolJson']);

         $newPoolName = mysqli_real_escape_string($link, $newPoolJson->poolName);
         $usersAddedToNewPool = $newPoolJson->usersAddedToNewPool;

         //Controllo presenza nome pool
         $query = "SELECT poolName FROM Dashboard.UsersPools WHERE poolName = '$newPoolName'";
         $result = mysqli_query($link, $query);

         if($result)
         {
              $existingPoolCount = mysqli_num_rows($result);
              mysqli_free_result($result);

              if($existingPoolCount > 0)
             {
                 echo 2;
                 mysqli_close($link);
                 die();
             }
             else 
             {
                  //$file = fopen("C:\Users\marazzini\Desktop\dashboardLog.txt", "w");
                  $beginTransactionResult = mysqli_begin_transaction($link);
                  if (!$beginTransactionResult) throw new \mysqli_sql_exception('Transaction start failed');

                  //Inserimento nuovo pool
                  $query1 = "INSERT INTO Dashboard.UsersPools(poolName) VALUES ('$newPoolName')";
                  $result1 = mysqli_query($link, $query1);

                  if($result1) 
                  {
                      $newPoolId = null;
                      $selMaxIdQuery = "SELECT MAX(poolId) AS newPoolId FROM Dashboard.UsersPools";
                      $selMaxIdResult = mysqli_query($link, $selMaxIdQuery);

                      if($selMaxIdResult)
                      {
                          while($row = mysqli_fetch_assoc($selMaxIdResult)) 
                          { 
                              $newPoolId = $row["newPoolId"];
                          }
                      }
                      else
                      {
                          $rollbackResult = mysqli_rollback($link);
                          echo 0;
                          die();
                      }

                      //Inserimento dei nuovi utenti del nuovo pool
                      $queryFail = false;

                      for($i = 0; $i < count($newPoolJson->usersAddedToNewPool); $i++)
                      {
                          $username = $newPoolJson->usersAddedToNewPool[$i]->username;
                          $isAdmin = $newPoolJson->usersAddedToNewPool[$i]->isAdmin;

                          $query2 = "INSERT INTO Dashboard.UsersPoolsRelations(username, poolId, isAdmin) VALUES ('$username', $newPoolId, $isAdmin)";
                          $result2 = mysqli_query($link, $query2);

                          if(!$result2)
                          {
                              $rollbackResult = mysqli_rollback($link);
                              mysqli_close($link);
                              $queryFail = true;
                              echo 0;
                              die();
                          }
                      }

                      if(!$queryFail)
                      {
                           $commit = mysqli_commit($link);
                           if (!$commit) throw new \mysqli_sql_exception('Transaction commit failed');
                           mysqli_close($link);
                           echo 1;
                           die();
                      }
                  }
                  else
                  {
                      $rollbackResult = mysqli_rollback($link);
                      mysqli_close($link);
                      echo 0;
                      die();
                  }
             }
         }
         else 
         {
             echo 0;
             mysqli_close($link);
             die();
         }
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
