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
   
   //Definizioni di funzione
   function ldapCheckRole($connection, $userDn, $role) {
      $result = ldap_search(
              $connection, 'dc=ldap,dc=disit,dc=org', 
              '(&(objectClass=organizationalRole)(cn=' . $role . ')(roleOccupant=' . $userDn . '))'
      );
      $entries = ldap_get_entries($connection, $result);
      
      foreach ($entries as $key => $value) {
          if (is_numeric($key)) {
              if ($value["cn"]["0"] == $role) 
              {
                  return true;
              }
          }
      }
      return false;
  }
   
  //Corpo dell'API
   session_start(); 
   $link = mysqli_connect($host, $username, $password) or die("Failed to connect to server");
   mysqli_select_db($link, $dbname);
   
   $queryFail = false;
   
   if(!$link->set_charset("utf8")) 
   {
       exit();
   }

   if(isset($_SESSION['loggedRole']))
   {
       $username = $_SESSION['loggedUsername'];
       $users = [];
       
       switch($_SESSION['loggedRole'])
       {
           case "Manager":
               //Manager: si restituiscono solo manager ed area manager (LOCALI ED LDAP) dei pools di cui fa parte l'utente stesso
               $query = "SELECT rels.username AS username FROM Dashboard.UsersPoolsRelations AS rels " .
                        "WHERE rels.poolId IN (SELECT poolId FROM Dashboard.UsersPoolsRelations WHERE username = '$username') " .
                        "AND rels.username <> '$username' " . 
                        "AND rels.username NOT IN (SELECT Users.username FROM Dashboard.Users WHERE admin = 'ToolAdmin') " .
                        "AND rels.username NOT IN (SELECT Dashboard.UsersPoolsRelations.username FROM Dashboard.UsersPoolsRelations WHERE Dashboard.UsersPoolsRelations.poolId IN (SELECT poolId FROM Dashboard.UsersPoolsRelations WHERE username = '$username') AND Dashboard.UsersPoolsRelations.isAdmin = 1) " .
                        "GROUP BY rels.username";      
               
               $result = mysqli_query($link, $query);
               
               if($result)
               {
                    while($row = mysqli_fetch_array($result)) 
                    {
                        array_push($users, $row["username"]);
                    }
               }
               
               echo json_encode($users);
               mysqli_close($link);
               break;
           
           case "AreaManager":
               //Area manager - Se area manager crea una dashboard si restituiscono solo gli end users dei pools di cui fa parte l'area manager stesso (come admin o come end-user) (quindi esclusi i superadmin, gli altri admins e l'autore della dashboard) - Gli altri area manager non vengono abilitati in automatico alla consultazione della dashboard.
               $query = "SELECT rels.username AS username FROM Dashboard.UsersPoolsRelations AS rels " .
               //Si restringe ai pool cui appartiene l'utente
               "WHERE rels.poolId IN (SELECT poolId FROM Dashboard.UsersPoolsRelations WHERE username = '$username') " .
               // Si toglie l'utente stesso
               "AND rels.username <> '$username' " .
               //Si tolgono i tool admin
               "AND rels.username NOT IN (SELECT Users.username FROM Dashboard.Users WHERE admin = 'ToolAdmin') " .
               //Si tolgono gli admin dei gruppi dell'utente, che sono abilitati di default
               "AND rels.isAdmin = 0 " .
               //Si tolgono gli admin che non sono admin dei pool dell'utente e non sono neanche end user di tali pool
               "AND rels.username NOT IN (SELECT Dashboard.UsersPoolsRelations.username FROM Dashboard.UsersPoolsRelations WHERE Dashboard.UsersPoolsRelations.isAdmin = 1 AND Dashboard.UsersPoolsRelations.username NOT IN (SELECT username FROM Dashboard.UsersPoolsRelations WHERE isAdmin = 0)) " .
               "GROUP BY rels.username";
                        
               $result = mysqli_query($link, $query);
               
               if($result)
               {
                    while($row = mysqli_fetch_array($result)) 
                    {
                        array_push($users, $row["username"]);
                    }
               }
               
               echo json_encode($users);
               mysqli_close($link);
               break;
           
           case "ToolAdmin":
               //Super admin: si restituiscono tutti i manager e tutti gli area manager e tutti gli observer
               //Reperimento elenco utenti LDAP
               $temp = [];
               $users = [];
               
               /*$ds = ldap_connect($ldapServer, $ldapPort);
               ldap_set_option($ds, LDAP_OPT_PROTOCOL_VERSION, 3);
               $bind = ldap_bind($ds);

               $result = ldap_search(
                       $ds, 'dc=ldap,dc=disit,dc=org', 
                       '(cn=Dashboard)'
               );
               $entries = ldap_get_entries($ds, $result);
               foreach ($entries as $key => $value) 
               {
                  for($index = 0; $index < (count($value["memberuid"]) - 1); $index++)
                  { 
                     $usr = $value["memberuid"][$index];
                     array_push($temp, $usr);
                  }
               }

               ldap_close();

               $ds = ldap_connect($ldapServer, $ldapPort);
               ldap_set_option($ds, LDAP_OPT_PROTOCOL_VERSION, 3);
               $bind = ldap_bind($ds);

               for($i = 0; $i < count($temp); $i++)
               {
                  if(!ldapCheckRole($ds, $temp[$i], "ToolAdmin"))
                  {
                     $name = str_replace("cn=", "", $temp[$i]);
                     $name = str_replace(",dc=ldap,dc=disit,dc=org", "", $name);
                     array_push($users, $name);
                  }
               }*/

                //Reperimento elenco utenti locali
                $query2 = "SELECT username FROM Dashboard.Users WHERE admin <> 'ToolAdmin'";
                $result2 = mysqli_query($link, $query2) or die(mysqli_error($link));

                if($result2)
                {
                   if($result2->num_rows > 0) 
                   {
                      while ($row = $result2->fetch_assoc()) 
                      {
                         array_push($users, $row["username"]);
                      }
                   }
                }
               
               echo json_encode($users);
               mysqli_close($link);
               break;
       }
   }