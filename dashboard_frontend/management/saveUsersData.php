<?php

/* Dashboard Builder.
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
   mysqli_autocommit($link, false);
   
   $queryFail = false;
   
   if(!$link->set_charset("utf8")) 
   {
       exit();
   }

   if(isset($_SESSION['loggedRole']))
   {
      //28 Giugno 2017 - Questo caso (area manager) non dovrebbe più esserci, consentiamo l'aggiunta e la modifica di utenti solo al tool admin
       if($_SESSION['loggedRole'] == "AreaManager")
       {
            $usersJson = json_decode($_POST['usersJson']);
            
            mysqli_begin_transaction(MYSQLI_TRANS_START_READ_WRITE);
           
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
                }
            }
            
            if($queryFail)
            {
                mysqli_rollback($link);
                echo 0;
            }
            else
            {
                mysqli_commit($link);
                echo 1;
            }
            mysqli_close($link);
       }
       else if($_SESSION['loggedRole'] == "ToolAdmin")
       {
           $usersJson = json_decode($_POST['usersJson']);
            
            mysqli_begin_transaction(MYSQLI_TRANS_START_READ_WRITE);
           
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
                }
            }
            
            if($queryFail)
            {
                mysqli_rollback($link);
                echo 0;
            }
            else
            {
                mysqli_commit($link);
                echo 1;
            }
            mysqli_close($link);
       }
   }