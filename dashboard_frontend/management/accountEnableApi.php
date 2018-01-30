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
   require '../phpmailer/PHPMailerAutoload.php';
   
   //Altrimenti restituisce in output le warning
   error_reporting(E_ERROR | E_NOTICE);
   
   //Definizioni di funzione
   function sendResumeEmail($user, $password, $email, $host, $auth, $fromAdr, $fromName, $appUrl, $userRole)
   {
      $mailer = new PHPMailer;
      $mailer->isSMTP(); 
      $mailer->Host = $host;
      
      if($auth == "true")
      {
         $mailer->SMTPAuth = true;
      }
      else
      {
         $mailer->SMTPAuth = false;                        
      }
      $mailer->From = $fromAdr;
      $mailer->FromName = $fromName;
      $mailer->isHTML(true);
      $mailer->Subject = 'Account resume';
      
      if($userRole == "Observer")
      {
         $mailer->Body = '
         Dear ' . $user . ',
         your account has been activated, here follow your access data:
         <ul>
            <li>Username: ' . $user . '</li>
            <li>Password: ' . $password . '</li>
         </ul>
         You can use these credentials to open the dashboards for which you have view permission.
         ';
      }
      else
      {
         $mailer->Body = '
         Dear ' . $user . ',
         your account has been activated, here follow your access data:
         <ul>
            <li>Username: ' . $user . '</li>
            <li>Password: ' . $password . '</li>
         </ul>
         You can login at ' . $appUrl . '/management/index.php
         ';
      }
      
      $mailer->addAddress($email);
      $mailer->send();
   }
   
   //Corpo dell'API
   $link = mysqli_connect($host, $username, $password) or die("Failed to connect to server");
   mysqli_select_db($link, $dbname);
   
   $queryFail = null;
   
   if(!$link->set_charset("utf8")) 
   {
       die();
   }
   
   if(isset($_SESSION['loggedRole'])&&isset($_SESSION['loggedType']))
   {
       if($_SESSION['loggedRole'] == "ToolAdmin")
       {
            if(isset($_REQUEST['username'])&&isset($_REQUEST['email'])&&isset($_REQUEST['password'])&&isset($_REQUEST['hash']))
            {
               $username = mysqli_real_escape_string($link, $_REQUEST['username']);
               $email = mysqli_real_escape_string($link, $_REQUEST['email']);
               $password = mysqli_real_escape_string($link, $_REQUEST['password']);
               $hash = mysqli_real_escape_string($link, $_REQUEST['hash']);

               $query = "SELECT * FROM Dashboard.Users WHERE username = '$username' AND email = '$email' AND activationHash = '$hash'";
               $result = mysqli_query($link, $query) or die(mysqli_error($link));

               if($result)
               {
                  if($result->num_rows > 0) 
                  {
                     $row = mysqli_fetch_assoc($result);
                     $userRole = $row['admin'];
                     $md5Pwd = md5($password);
                     $query2 = "UPDATE Dashboard.Users SET password = '$md5Pwd', status = 1, activationHash = NULL WHERE username = '$username' AND email = '$email' AND activationHash = '$hash'";
                     $result2 = mysqli_query($link, $query2) or die(mysqli_error($link));

                     if($result2)
                     {
                        sendResumeEmail($username, $password, $email, $smtpHost, $smtpAuth, $emailFromAddress, $emailFromName, $appUrl, $userRole);
                        echo 1;
                        mysqli_close($link);
                     }
                     else
                     {
                        echo 0;
                        mysqli_close($link);
                     }
                  }
                  else
                  {
                     echo 0;
                     mysqli_close($link);
                  }
               }
               else
               {
                  echo 0;
                  mysqli_close($link);
               }
            }
            else
            {
               echo 0;
               mysqli_close($link);
            }
       }
   }
