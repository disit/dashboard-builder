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
   mysqli_autocommit($link, false);
   
   $queryFail = false;
   
   if(!$link->set_charset("utf8")) 
   {
       exit();
   }

   if(isset($_SESSION['loggedRole']))
   {
      //28 Giugno 2017 - Questo caso (area manager) non dovrebbe più esserci, consentiamo l'aggiunta e la modifica di utenti solo al tool admin
       if(($_SESSION['loggedRole'] == "AreaManager")||($_SESSION['loggedRole'] == "ToolAdmin"))
       {
            $usersJson = json_decode($_POST['usersJson']);
            
            if (!mysqli_begin_transaction($link)) {
                mysqli_close($link);
                echo 0;
                return;
            }
           
            for($i = 0; $i < count($usersJson); $i++) 
            {
                $username = $usersJson[$i]->username;
                $name = $usersJson[$i]->name;
                if($name == 'Empty')
                {
                    $name = '';
                }
                $surname = $usersJson[$i]->surname;
                if($surname == 'Empty')
                {
                    $surname = '';
                }
                $organization = $usersJson[$i]->organization;
                if($organization == 'Empty')
                {
                    $organization = '';
                }
                $email = $usersJson[$i]->email;
                if($email == 'Empty')
                {
                    $email = '';
                }
                
                $status = $usersJson[$i]->status;

                $singleQuery = "UPDATE Dashboard.Users SET name = '$name', surname = '$surname', organization = '$organization', email = '$email', status = '$status' WHERE username = '$username'";
                $result = mysqli_query($link, $singleQuery);
                
                if(!$result)
                {
                    $queryFail = true;
                    break;
                }
            }
            
            if (!$queryFail && !mysqli_commit($link)) $queryFail = true;
             if ($queryFail) mysqli_rollback($link);
             mysqli_close($link);
             echo $queryFail ? 0 : 1;
             return;
       }
       else if(($_SESSION['loggedRole'] == "RootAdmin"))
       {
           $usersJson = json_decode($_POST['usersJson']);
            
            if (!mysqli_begin_transaction($link)) {
                mysqli_close($link);
                echo 0;
                return;
            }
           
            for($i = 0; $i < count($usersJson); $i++) 
            {
                $username = $usersJson[$i]->username;
                $password = $usersJson[$i]->password;
                $name = $usersJson[$i]->name;
                if($name == 'Empty')
                {
                    $name = '';
                }
                
                $surname = $usersJson[$i]->surname;
                if($surname == 'Empty')
                {
                    $surname = '';
                }
                
                $organization = $usersJson[$i]->organization;
                if($organization == 'Empty')
                {
                    $organization = '';
                }
                
                $admin = $usersJson[$i]->usertype;
                
                $email = $usersJson[$i]->email;
                if($email == 'Empty')
                {
                    $email = '';
                }
                
                $status = $usersJson[$i]->status;

                $singleQuery = "UPDATE Dashboard.Users SET password = '$password', name = '$name', surname = '$surname', organization = '$organization', admin = '$admin', email = '$email', status = '$status' WHERE username = '$username'";
                $result = mysqli_query($link, $singleQuery);
                
                if(!$result)
                {
                    $queryFail = true;
                    break;
                }
            }
            
            if (!$queryFail && !mysqli_commit($link)) $queryFail = true;
             if ($queryFail) mysqli_rollback($link);
             mysqli_close($link);
             echo $queryFail ? 0 : 1;
             return;
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