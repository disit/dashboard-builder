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
    
   $link = mysqli_connect($host, $username, $password);
   mysqli_select_db($link, $dbname);
   error_reporting(E_ERROR | E_NOTICE);
    
   if(!$link->set_charset("utf8")) 
   {
      exit();
   } 
   
   $response = [];
   
   if(isset($_REQUEST['apiPwd'])&&isset($_REQUEST['usr'])&&isset($_REQUEST['pwd']))
   {
      if($_REQUEST['apiPwd'] == $notificatorApiPwd)
      {
         $username = mysqli_real_escape_string($link, $_REQUEST['usr']);
         $password = mysqli_real_escape_string($link, $_REQUEST['pwd']);
         
         $query = "SELECT * FROM Dashboard.Users WHERE username = '$username' AND password = '$password' AND status = 1";
         $result = mysqli_query($link, $query);

         if($result == false) 
         {
            $response['detail'] = "Ko";
         }
         else
         {
            if(mysqli_num_rows($result) > 0) 
            {
               $row = $result->fetch_assoc();
               $response['detail'] = "Ok";
               $response['usrRole'] = $row["admin"];
            }
            else
            {
               $response['detail'] = "Ko";
            }
         }
      }
      else
      {
         $response['detail'] = "Ko";
      }
   }
   else
   {
      $response['detail'] = "missingParams";
   }
   
   echo json_encode($response);
   mysqli_close($link);