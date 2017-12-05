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
   function sendActivationEmail($user, $email, $hash, $host, $auth, $fromAdr, $fromName, $appUrl)
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
      $mailer->Subject = 'Account activation';
      $mailer->Body = '

      Dear ' . $user . ',
      your account has been created, please click the following URL to choose your password and activate your account.'
      
      . $appUrl . '/management/accountEnable.php?user='.$user.'&email='.$email.'&hash='.$hash.'

      ';
      
      $mailer->addAddress($email);
      $mailer->send();
   }

   //Corpo dell'API
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
       if($_SESSION['loggedRole'] == "ToolAdmin")
       {
           $newUserJson = json_decode($_POST['newUserJson']);
           $username = mysqli_real_escape_string($link, $newUserJson->username); 
           $firstName = mysqli_real_escape_string($link, $newUserJson->firstName); 
           $lastName = mysqli_real_escape_string($link, $newUserJson->lastName); 
           $organization = mysqli_real_escape_string($link, $newUserJson->organization); 
           $email = mysqli_real_escape_string($link, $newUserJson->email); 
           $userType = mysqli_real_escape_string($link, $newUserJson->userType); 
           $pools = $newUserJson->pools;
           
           //Controllo presenza username
           $query = "SELECT username FROM Dashboard.Users WHERE username = '$username'";
           $result = mysqli_query($link, $query) or die(mysqli_error($link));
           
           if($result)
           {
              mysqli_free_result($result);
               
               if(mysqli_num_rows($result) > 0) 
               {
                   echo 3;
                   mysqli_close($link);
                   die();
               }
               else 
               {
                  //Controllo di presenza di almeno una fra la coppia nome-cognome e il nome dell'organizzazione
                  if(((strlen($firstName) == 0)&&(strlen($lastName) == 0)&&(strlen($organization) == 0))||((strlen($firstName) > 0)&&(strlen($lastName) == 0)&&(strlen($organization) == 0))||((strlen($firstName) == 0)&&(strlen($lastName) > 0)&&(strlen($organization) == 0)))
                  {
                      echo 6;
                      die();
                  }
                  else
                  {
                      //Controllo pattern e-mail
                      if((preg_match("/[a-z0-9!#$%&'*+\/=?^_`{|}~-]+(?:\.[a-z0-9!#$%&'*+\/=?^_`{|}~-]+)*@(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\.)+[a-z0-9](?:[a-z0-9-]*[a-z0-9])?/", $email) === 0)||(preg_match("/[a-z0-9!#$%&'*+\/=?^_`{|}~-]+(?:\.[a-z0-9!#$%&'*+\/=?^_`{|}~-]+)*@(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\.)+[a-z0-9](?:[a-z0-9-]*[a-z0-9])?/", $email) === false))
                      {
                          echo 7;
                          die();
                      }
                      else
                      {
                         $activationHash = md5(rand(0,1000));
                         $beginTransactionResult = mysqli_begin_transaction($link, MYSQLI_TRANS_START_READ_WRITE);

                          //Inserimento utente
                          $query1 = "INSERT INTO Dashboard.Users(username, name, surname, organization, email, reg_data, status, admin, activationHash) VALUES ('$username', '$firstName', '$lastName', '$organization', '$email', now(), 0, '$userType', '$activationHash')";
                          $result1 = mysqli_query($link, $query1);

                          if($result1) 
                          {
                              switch($userType)
                              {
                                 //Inserimento appartenenze a pools per observers
                                 case "Observer":
                                    $queryFail = false;
                                    foreach ($pools as $pool)
                                    {
                                       $poolNumber = $pool->poolId;
                                       $query2 = "INSERT INTO Dashboard.UsersPoolsRelations(username, poolId, isAdmin) VALUES ('$username', $poolNumber, 0)";
                                       //fwrite($file, "Query: " . $query2 . "\n");
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
                                       sendActivationEmail($username, $email, $activationHash, $smtpHost, $smtpAuth, $emailFromAddress, $emailFromName, $appUrl);
                                       echo 1;
                                       die();
                                    }
                                    break;
                                 
                                 //Inserimento appartenenze a pools per managers
                                 case "Manager":
                                    $queryFail = false;
                                    foreach ($pools as $pool)
                                    {
                                       $poolNumber = $pool->poolId;
                                       $query2 = "INSERT INTO Dashboard.UsersPoolsRelations(username, poolId, isAdmin) VALUES ('$username', $poolNumber, 0)";
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
                                       sendActivationEmail($username, $email, $activationHash, $smtpHost, $smtpAuth, $emailFromAddress, $emailFromName, $appUrl);
                                       echo 1;
                                       die();
                                    }
                                    break;

                                 case "AreaManager":
                                    $queryFail = false;
                                    
                                    foreach ($pools as $pool)
                                    {
                                       $poolNumber = $pool->poolId;
                                       $makeAdmin = $pool->makeAdmin;

                                       if($makeAdmin == false)
                                       {
                                          $query2 = "INSERT INTO Dashboard.UsersPoolsRelations(username, poolId, isAdmin) VALUES ('$username', $poolNumber, 0)";
                                       }
                                       else
                                       {
                                          $query2 = "INSERT INTO Dashboard.UsersPoolsRelations(username, poolId, isAdmin) VALUES ('$username', $poolNumber, 1)";
                                       }

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
                                         sendActivationEmail($username, $email, $activationHash, $smtpHost, $smtpAuth, $emailFromAddress, $emailFromName, $appUrl);
                                         echo 1;
                                         die();
                                     }
                                    break;

                                 default:
                                    $commit = mysqli_commit($link);
                                    sendActivationEmail($username, $email, $activationHash, $smtpHost, $smtpAuth, $emailFromAddress, $emailFromName, $appUrl);
                                    echo 1;
                                    die();
                                    break;
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
               }
           }
           else 
           {
               echo 2;
               die();
           }
       }
   }

