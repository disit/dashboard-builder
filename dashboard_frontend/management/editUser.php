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
       echo '<script type="text/javascript">';
       echo 'alert("KO");';
       echo '</script>';
       printf("Error loading character set utf8: %s\n", $link->error);
       die();
   }

    //$file = fopen("C:\Users\marazzini\Desktop\dashboardLog.txt", "w");
   
   if(isset($_SESSION['loggedRole']))
   {
       if($_SESSION['loggedRole'] == "ToolAdmin")
       {
          switch($_REQUEST['operation'])
          {
             case "getUserPoolMemberships":
                $username = $_REQUEST['username'];
                $query = "SELECT pools.poolId, pools.poolName, rels.username, rels.isAdmin FROM Dashboard.UsersPools AS pools " .
                         "LEFT JOIN (SELECT * FROM Dashboard.UsersPoolsRelations WHERE username = '$username') AS rels " .
                         "ON pools.poolId = rels.poolId " .
                         "ORDER BY pools.poolId";
                
                $result = mysqli_query($link, $query) or die(mysqli_error($link));

                if($result)
                {
                  $memberships = [];
                  while($row = mysqli_fetch_assoc($result)) 
                  {
                     array_push($memberships, $row);
                  }
                  
                  echo json_encode($memberships);
                }
                else 
                {
                   echo 0;
                   die();
                }
                break;
                
             case "updateAccount":
               $accountJson = json_decode($_REQUEST['accountJson']);
               $username = mysqli_real_escape_string($link, $accountJson->username); 
               $firstName = mysqli_real_escape_string($link, $accountJson->firstName); 
               $lastName = mysqli_real_escape_string($link, $accountJson->lastName); 
               $organization = mysqli_real_escape_string($link, $accountJson->organization); 
               $email = mysqli_real_escape_string($link, $accountJson->email); 
               $userType = mysqli_real_escape_string($link, $accountJson->userType); 
               $userStatus = mysqli_real_escape_string($link, $accountJson->userStatus);
               $pools = $accountJson->pools;

               //Controllo caratteristiche password
               /*if((strlen($password) < 8)||(preg_match ('/\d/', $password) === 0)||(preg_match ('/\d/', $password) === false)||(preg_match ('/\D/', $password) === 0)||(preg_match ('/\D/', $password) === false))
               {
                   echo 4;
                   die();
               }
               else
               {*/
                   //Controllo coerenza fra password e conferma password
                   /*if($password != $passwordConfirm)
                   {
                       echo 5;
                       die();
                   }
                   else
                   {*/
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
                               $beginTransactionResult = mysqli_begin_transaction($link, MYSQLI_TRANS_START_READ_WRITE);

                               //Aggiornamento record utente
                               $query1 = "UPDATE Dashboard.Users SET name='$firstName', surname='$lastName', organization='$organization', email='$email', status='$userStatus', admin='$userType' WHERE username='$username'";
                               $result1 = mysqli_query($link, $query1);

                               if($result1) 
                               {
                                  //Cancellazione precedenti appartenenze a pool
                                  $query2 = "DELETE FROM Dashboard.UsersPoolsRelations WHERE username='$username'";
                                  $result2 = mysqli_query($link, $query2);
                                 
                                  if($result2) 
                                  {
                                    switch($userType)
                                    {
                                       //Inserimento appartenenze a pools per manager ed observer
                                       case "Observer": case "Manager":
                                          $queryFail = false;
                                          foreach ($pools as $pool)
                                          {
                                             $poolNumber = $pool->poolId;
                                             $query3 = "INSERT INTO Dashboard.UsersPoolsRelations(username, poolId, isAdmin) VALUES ('$username', $poolNumber, 0)";
                                             $result3 = mysqli_query($link, $query3);
                                             if(!$result3)
                                             {
                                                   $rollbackResult = mysqli_rollback($link);
                                                   //fwrite($file, "Query2 KO: " . $query2 . "\n");
                                                   mysqli_close($link);
                                                   $queryFail = true;
                                                   echo 0;
                                                   die();
                                             }
                                          }

                                          if(!$queryFail)
                                          {
                                             $commit = mysqli_commit($link);
                                             //fwrite($file, "Query2 OK: " . $query2 . "\n");
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
                                                $query3 = "INSERT INTO Dashboard.UsersPoolsRelations(username, poolId, isAdmin) VALUES ('$username', $poolNumber, 0)";
                                             }
                                             else
                                             {
                                                $query3 = "INSERT INTO Dashboard.UsersPoolsRelations(username, poolId, isAdmin) VALUES ('$username', $poolNumber, 1)";
                                             }

                                              $result3 = mysqli_query($link, $query3);
                                              if(!$result3)
                                              {
                                                   $rollbackResult = mysqli_rollback($link);
                                                   //fwrite($file, "Query2 KO: " . $query2 . "\n");
                                                   mysqli_close($link);
                                                   $queryFail = true;
                                                   echo 0;
                                                   die();
                                              }
                                           }

                                           if(!$queryFail)
                                           {
                                               $commit = mysqli_commit($link);
                                               //fwrite($file, "Query2 OK: " . $query2 . "\n");
                                               echo 1;
                                               die();
                                           }
                                          break;

                                       default:
                                          $commit = mysqli_commit($link);
                                          //fwrite($file, "Query2 OK: " . $query2 . "\n");
                                          echo 1;
                                          die();
                                          break;
                                    }
                                  }
                                  else
                                  {
                                     $rollbackResult = mysqli_rollback($link);
                                     //fwrite($file, "Query2 KO: " . $query2 . "\n");
                                     mysqli_close($link);
                                     $queryFail = true;
                                     echo 0;
                                     die();
                                  }
                               } 
                               else
                               {
                                   $rollbackResult = mysqli_rollback($link);
                                   //fwrite($file, "Query1 KO: " . $query1 . "\n");
                                   mysqli_close($link);
                                   echo 0;
                                   die();
                               }
                           }
                       }
                   //}
               //}
               break;
               
               default:
                  break;
          }
       }
       
      if((($_SESSION['loggedRole'] == "Manager")||($_SESSION['loggedRole'] == "AreaManager")||($_SESSION['loggedRole'] == "ToolAdmin"))&&($_REQUEST['operation'] == "updateAccountFromAccountPage"))
      {
         $accountJson = json_decode($_REQUEST['accountJson']);
         $username = mysqli_real_escape_string($link, $accountJson->username); 
         $password = mysqli_real_escape_string($link, $accountJson->password);
         $firstName = mysqli_real_escape_string($link, $accountJson->firstName); 
         $lastName = mysqli_real_escape_string($link, $accountJson->lastName); 
         $organization = mysqli_real_escape_string($link, $accountJson->organization); 
         $email = mysqli_real_escape_string($link, $accountJson->email); 

         $md5Pass = md5($password);   
         $query = "UPDATE Dashboard.Users SET password='$md5Pass', name='$firstName', surname='$lastName', organization='$organization', email='$email' WHERE username='$username'";
         $result = mysqli_query($link, $query);

         if($result) 
         {
            mysqli_close($link);
            echo 1;
         }
         else
         {
            mysqli_close($link);
            echo 0;
         }
      }
      
   }
