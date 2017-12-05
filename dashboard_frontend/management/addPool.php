<?php

/*Dashboard Builder.
   Copyright (C) 2017 DISIT Lab https://www.disit.org - University of Florence

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

include '../config.php';
   
   //Altrimenti restituisce in output le warning
   error_reporting(E_ERROR | E_NOTICE);
   
   session_start(); 
   $link = mysqli_connect($host, $username, $password) or die("Failed to connect to server");
   mysqli_select_db($link, $dbname);
   
   $queryFail = null;
   
   if(!$link->set_charset("utf8")) 
   {
       die();
   }

   if(isset($_SESSION['loggedRole']))
   {
      if(($_SESSION['loggedRole'] == "ToolAdmin")||($_SESSION['loggedRole'] == "AreaManager"))
      {
         $newPoolJson = json_decode($_POST['newPoolJson']);

         $newPoolName = mysqli_real_escape_string($link, $newPoolJson->poolName);
         $usersAddedToNewPool = $newPoolJson->usersAddedToNewPool;

         //Controllo presenza nome pool
         $query = "SELECT poolName FROM Dashboard.UsersPools WHERE poolName = '$newPoolName'";
         $result = mysqli_query($link, $query) or die(mysqli_error($link));

         if($result)
         {
             mysqli_free_result($result);

             if(mysqli_num_rows($result) > 0) 
             {
                 echo 2;
                 mysqli_close($link);
                 die();
             }
             else 
             {
                  //$file = fopen("C:\Users\marazzini\Desktop\dashboardLog.txt", "w");
                  $beginTransactionResult = mysqli_begin_transaction($link, MYSQLI_TRANS_START_READ_WRITE);

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