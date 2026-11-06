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
   
   //Altrimenti restituisce in output le warning
   error_reporting(E_ERROR | E_NOTICE);
   $link = null;
   try {
   
   session_start(); 
   $link = mysqli_connect($host, $username, $password) or die("Failed to connect to server");
   mysqli_select_db($link, $dbname);
   
   if(!$link->set_charset("utf8")) 
   {
      exit();
   }

   if(isset($_SESSION['loggedRole']))
   {
      if(($_SESSION['loggedRole'] == "RootAdmin")||($_SESSION['loggedRole'] == "ToolAdmin")||($_SESSION['loggedRole'] == "AreaManager"))
      {
         if (!mysqli_begin_transaction($link)) {
             mysqli_close($link);
             echo 0;
             return;
         }
         $pools = json_decode($_POST["poolsJson"]);
         $queryFail = false;

         for($i = 0; $i < count($pools); $i++)
         {
            $poolId = $pools[$i]->poolId;

            if($pools[$i]->edited == true)
            {
                //Cancellazione utenti rimossi
                for($j = 0; $j < count($pools[$i]->removedMembers); $j++)
                {
                    $username = $pools[$i]->removedMembers[$j]->username;
                    $query = "DELETE FROM Dashboard.UsersPoolsRelations WHERE poolId = '$poolId' AND username = '$username'";
                    $result = mysqli_query($link, $query);

                     if(!$result)
                     {
                         $rollbackResult = mysqli_rollback($link);
                         mysqli_close($link);
                         $queryFail = true;
                         echo 0;
                         die();
                     }
                }

                //Inserimento utenti aggiunti
                for($j = 0; $j < count($pools[$i]->addedMembers); $j++)
                {
                     $username = $pools[$i]->addedMembers[$j]->username;
                     $isAdmin = $pools[$i]->addedMembers[$j]->isAdmin;

                     $query = "INSERT INTO Dashboard.UsersPoolsRelations(username, poolId, isAdmin) VALUES('$username', '$poolId', '$isAdmin')";
                     $result = mysqli_query($link, $query);

                     if(!$result)
                     {
                         $rollbackResult = mysqli_rollback($link);
                         mysqli_close($link);
                         $queryFail = true;
                         echo 0;
                         die();
                     }
                }

                //Aggiornamento ruolo utenti promossi/declassati admin/end user
                for($k = 0; $k < count($pools[$i]->adminChangedMembers); $k++)
                {
                     $username = $pools[$i]->adminChangedMembers[$k]->username;
                     $isAdmin = $pools[$i]->adminChangedMembers[$k]->isAdmin;

                     $query = "UPDATE Dashboard.UsersPoolsRelations SET isAdmin = '$isAdmin' WHERE username = '$username' AND poolId = '$poolId'";
                     $result = mysqli_query($link, $query);

                     if(!$result)
                     {
                         $rollbackResult = mysqli_rollback($link);
                         mysqli_close($link);
                         $queryFail = true;
                         echo 0;
                         die();
                     }
                }
            }
         }

         if(!$queryFail)
         {
             if (!mysqli_commit($link)) {
                 mysqli_rollback($link);
                 mysqli_close($link);
                 echo 0;
                 return;
             }
             mysqli_close($link);
             echo 1;
         }
      }
   }
   } catch (\mysqli_sql_exception $exception) {
       if ($link !== null && $link !== false) {
           try { mysqli_rollback($link); } catch (\Throwable $rollbackError) {}
           try { mysqli_close($link); } catch (\Throwable $closeError) {}
       }
       http_response_code(500);
       echo 0;
   }
