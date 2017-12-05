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
   
   //Altrimenti restituisce in output le warning
   error_reporting(E_ERROR | E_NOTICE);
   
   session_start(); 
   $link = mysqli_connect($host, $username, $password) or die("Failed to connect to server");
   mysqli_select_db($link, $dbname);
   
   $queryFail = false;
   
   if(!$link->set_charset("utf8")) 
   {
       echo '<script type="text/javascript">';
       echo 'alert("KO");';
       echo '</script>';
       printf("Error loading character set utf8: %s\n", $link->error);
       exit();
   }
   
   if(isset($_SESSION['loggedRole']))
   {   
        if(isset($_GET['dashboardId']) && !empty($_GET['dashboardId']))
        {
            $dashboardId = mysqli_real_escape_string($link, $_GET['dashboardId']);
        }
        else 
        {
            $dashboardId = $_SESSION['dashboardId'];
        }
        
        $visibilitySet = [];
        $users = [];
        
        $query = "SELECT Dashboard.Config_dashboard.user AS author, Dashboard.Config_dashboard.visibility AS visibility, Dashboard.Users.admin AS authorRole " .
                 "FROM Dashboard.Config_dashboard " .
                 "LEFT JOIN Dashboard.Users " .
                 "ON Dashboard.Config_dashboard.user = Dashboard.Users.username " .
                 "WHERE Dashboard.Config_dashboard.Id = $dashboardId";
        $result = mysqli_query($link, $query);
        
        if($result)
        {
            $row = mysqli_fetch_array($result);
            $visibilitySet['visibility'] = $row['visibility'];
            $author = $row['author'];
            $authorRole = $row['authorRole'];
        }
        
       switch($_SESSION['loggedRole'])
       {
            //OK - MANAGER
            //EDITA SICURAMENTE UNA DASHBOARD DI CUI E' AUTORE
            //Manager: si restituiscono solo manager ed area manager (ma che, nei pool dell'utente, sono manager normali) (LOCALI ED LDAP) dei pools di cui fa parte l'utente stesso
            //Gli admin dei pool cui appartiene l'autore verranno "pescati" nel controllo in apertura della view in consultazione
            case "Manager":
               $query = "SELECT * FROM " . 
                        "(SELECT rels.username AS ConsultationSetUser FROM Dashboard.UsersPoolsRelations AS rels " .
                        "WHERE rels.poolId IN (SELECT poolId FROM Dashboard.UsersPoolsRelations WHERE username = '$author') " .
                        "AND rels.username <> '$author' " . 
                        "AND rels.username NOT IN (SELECT Users.username FROM Dashboard.Users WHERE admin = 'ToolAdmin') " .
                        "AND rels.username NOT IN (SELECT Dashboard.UsersPoolsRelations.username FROM Dashboard.UsersPoolsRelations WHERE Dashboard.UsersPoolsRelations.poolId IN (SELECT poolId FROM Dashboard.UsersPoolsRelations WHERE username = '$author') AND Dashboard.UsersPoolsRelations.isAdmin = 1) " .
                        "GROUP BY rels.username) AS A ". 
                        "LEFT JOIN " .
                        "(SELECT username AS EnabledViewUser FROM Dashboard.DashboardsViewPermissions WHERE Dashboard.DashboardsViewPermissions.IdDashboard = $dashboardId) " .
                        "AS B " .
                        "ON A.ConsultationSetUser = B.EnabledViewUser";
               
               $result = mysqli_query($link, $query);
               
               if($result)
               { 
                   while($row = mysqli_fetch_array($result)) 
                    {
                        $user = [];
                        $user['username'] = $row["ConsultationSetUser"];
                        $user['consultationSetUser'] = $row["ConsultationSetUser"];
                        $user['enabledViewUser'] = $row["EnabledViewUser"];
                        array_push($users, $user);
                    }
               }
               $visibilitySet['users'] = $users;
               echo json_encode($visibilitySet);
               mysqli_close($link);
               break;
           
               
            //OK - Area manager
            //EDITA UNA DASHBOARD DI CUI E' AUTORE OPPURE UNA DI UN Manager APPARTENENTE AD UN POOL DI CUI LUI E' Area Manager
            case "AreaManager":
                if($author == $_SESSION['loggedUsername'])
                {
                    //Area manager - Se area manager edita una dashboard di cui è autore si restituiscono solo gli end users dei pools di cui fa parte l'area manager stesso (come admin o come end-user) (quindi esclusi i superadmin, gli altri admins e l'autore della dashboard) - Gli altri area manager non vengono abilitati in automatico alla consultazione della dashboard.
                     $query = "SELECT * FROM " . 
                              "(SELECT rels.username AS ConsultationSetUser FROM Dashboard.UsersPoolsRelations AS rels " .
                              "WHERE rels.poolId IN (SELECT poolId FROM Dashboard.UsersPoolsRelations WHERE username = '$author') " .
                              "AND rels.username <> '$author' " .
                              "AND rels.username NOT IN (SELECT Users.username FROM Dashboard.Users WHERE admin = 'ToolAdmin') " .
                              "AND rels.isAdmin = 0 " .
                              "AND rels.username NOT IN (SELECT Dashboard.UsersPoolsRelations.username FROM Dashboard.UsersPoolsRelations WHERE Dashboard.UsersPoolsRelations.isAdmin = 1 AND Dashboard.UsersPoolsRelations.username NOT IN (SELECT username FROM Dashboard.UsersPoolsRelations WHERE isAdmin = 0)) " .
                              "GROUP BY rels.username) AS A " .
                              "LEFT JOIN " .
                              "(SELECT username AS EnabledViewUser FROM Dashboard.DashboardsViewPermissions WHERE Dashboard.DashboardsViewPermissions.IdDashboard = $dashboardId) AS B " .
                              "ON A.ConsultationSetUser = B.EnabledViewUser";
                }
                else 
                {
                    //Area manager - SE Area manager EDITA UNA DASHBOARD DI UN END USER DI CUI LUI E' ADMIN SI RESTITUISCONO GLI END USERS DEI POOLS DI CUI FA PARTE L'AUTORE PIU' QUELLI DEI POOLS DI CUI FA PARTE L'ADMIN (come admin o come end-user) (quindi esclusi i superadmin, gli altri admins e l'autore della dashboard)  
                    //gli altri admin (SOLO QUELLI DEI POOL DI CUI AUTORE DASHBOARD E QUESTO ADMIN FANNO PARTE, NON TUTTI) verranno "pescati" nel controllo in apertura della view in consultazione    
                      $loggedUsername = $_SESSION['loggedUsername'];
                      $query = "SELECT * FROM " . 
                              "(SELECT rels.username AS ConsultationSetUser FROM Dashboard.UsersPoolsRelations AS rels " .
                              "WHERE rels.poolId IN (SELECT poolId FROM Dashboard.UsersPoolsRelations WHERE username IN ('$author', '$loggedUsername')) " .
                              "AND rels.username NOT IN ('$author', '$loggedUsername') " .
                              "AND rels.username NOT IN (SELECT Users.username FROM Dashboard.Users WHERE admin = 'ToolAdmin') " .
                              "AND rels.isAdmin = 0 " .
                              "AND rels.username NOT IN (SELECT Dashboard.UsersPoolsRelations.username FROM Dashboard.UsersPoolsRelations WHERE Dashboard.UsersPoolsRelations.poolId IN (SELECT poolId FROM Dashboard.UsersPoolsRelations WHERE username IN ('$author', '$loggedUsername')) AND Dashboard.UsersPoolsRelations.isAdmin = 1) " .
                              "GROUP BY rels.username) AS A " .
                              "LEFT JOIN " .
                              "(SELECT username AS EnabledViewUser FROM Dashboard.DashboardsViewPermissions WHERE Dashboard.DashboardsViewPermissions.IdDashboard = $dashboardId) AS B " .
                              "ON A.ConsultationSetUser = B.EnabledViewUser";      
                }     
               
               $result = mysqli_query($link, $query);
               
               if($result)
               {
                  while($row = mysqli_fetch_array($result)) 
                  {
                      $user = [];
                      $user['username'] = $row["ConsultationSetUser"];
                      $user['consultationSetUser'] = $row["ConsultationSetUser"];
                      $user['enabledViewUser'] = $row["EnabledViewUser"];
                      array_push($users, $user);
                  }
               }
               
               $visibilitySet['users'] = $users;
               echo json_encode($visibilitySet);
               mysqli_close($link);
               break;
           
            //OK - Tool admin
            //EDITA UNA DASHBOARD DI CUI E' AUTORE OPPURE UNA DI UN ALTRO Tool Admin OPPURE UNA DI UN Area Manager OPPURE UNA DI UN Manager  
           case "ToolAdmin":
               if($author == $_SESSION['loggedUsername'])
               {
                   //OK - EDITA UNA DASHBOARD DI CUI E' AUTORE: si restituiscono tutti gli area manager, tutti i manager e tutti gli observer, anche i non facenti parte di pools
                     $temp = [];
                     $usersList = [];

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
                           array_push($usersList, $name);
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
                               array_push($usersList, $row["username"]);
                            }
                         }
                      } 
                      
                      foreach ($usersList as $item)
                      {
                        $user = [];
                        $user['username'] = $item;
                        $user['consultationSetUser'] = $item; 
                         
                        $query3 = "SELECT * FROM Dashboard.DashboardsViewPermissions WHERE IdDashboard = $dashboardId AND username = '$item'";
                        $result3 = mysqli_query($link, $query3) or die(mysqli_error($link));
                         
                        if($result3)
                        {
                           if($result3->num_rows > 0) 
                           {
                              $user['enabledViewUser'] = $item;
                           }
                           else
                           {
                              $user['enabledViewUser'] = NULL;
                           }
                        }
                        
                        array_push($users, $user);
                      }
               }
               else
               {
                  if(/*($authorRole == NULL)||($authorRole == 'NULL')*/false)
                  {
                     $ldapAuthor = "cn=". $author . ",dc=ldap,dc=disit,dc=org";
                     $ds = ldap_connect($ldapServer, $ldapPort);
                     ldap_set_option($ds, LDAP_OPT_PROTOCOL_VERSION, 3);
                     $bind = ldap_bind($ds);
                     
                     if(ldapCheckRole($ds, $ldapAuthor, "Manager"))
                     {
                        $authorRole = "Manager";
                     }
                     else
                     {
                        if(ldapCheckRole($ds, $ldapAuthor, "AreaManager"))
                        {
                           $authorRole = "AreaManager";
                        }
                        else
                        {
                           if(ldapCheckRole($ds, $ldapAuthor, "ToolAdmin"))
                           {
                              $authorRole = "ToolAdmin";
                           }
                        }
                     }
                  }
                  
                  switch($authorRole)
                  {
                     //OK - Edita la dashboard creata da un altro tool admin
                     case "ToolAdmin":
                        $temp = [];
                        $usersList = [];
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
                              array_push($usersList, $name);
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
                                  array_push($usersList, $row["username"]);
                               }
                            }
                         } 

                         foreach ($usersList as $item)
                         {
                           $user = [];
                           $user['username'] = $item;
                           $user['consultationSetUser'] = $item; 

                           $query3 = "SELECT * FROM Dashboard.DashboardsViewPermissions WHERE IdDashboard = $dashboardId AND username = '$item'";
                           $result3 = mysqli_query($link, $query3) or die(mysqli_error($link));

                           if($result3)
                           {
                              if($result3->num_rows > 0) 
                              {
                                 $user['enabledViewUser'] = $item;
                              }
                              else
                              {
                                 $user['enabledViewUser'] = NULL;
                              }
                           }

                           array_push($users, $user);
                         }
                        break;
                     
                     //OK - Edita la dashboard di un area manager: si tolgono i tool admin e l'autore della dashboard, entrambi abilitati sempre.
                     case "AreaManager":
                        $temp = [];
                        $usersList = [];

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
                              array_push($usersList, $name);
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
                                  array_push($usersList, $row["username"]);
                               }
                            }
                         } 

                         foreach($usersList as $item)
                         {
                           if($item != $author)
                           {
                              $user = [];
                              $user['username'] = $item;
                              $user['consultationSetUser'] = $item; 

                              $query3 = "SELECT * FROM Dashboard.DashboardsViewPermissions WHERE IdDashboard = $dashboardId AND username = '$item'";
                              $result3 = mysqli_query($link, $query3) or die(mysqli_error($link));

                              if($result3)
                              {
                                 if($result3->num_rows > 0) 
                                 {
                                    $user['enabledViewUser'] = $item;
                                 }
                                 else
                                 {
                                    $user['enabledViewUser'] = NULL;
                                 }
                              }

                              array_push($users, $user);
                           }
                         }
                        break;
                        
                     //OK - Edita la dashboard di un manager: si tolgono i tool admin, l'autore della dashboard e gli admin dei pool di cui l'autore fa parte, perché sono utenze abilitate sempre.
                     case "Manager":
                        $temp = [];
                        $usersList = [];

                        //Reperimento elenco utenti LDAP (con rimozione dei tool admin)   
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
                              array_push($usersList, $name);
                           }
                        }*/

                         //Reperimento elenco utenti locali (Con rimozione degli area manager dei pool di cui fa parte l'autore della dashboard, inclusi tali area manager di orgine LDAP)
                         $query2 = "SELECT username FROM Dashboard.Users " .
                                   "WHERE admin <> 'ToolAdmin' " .
                                   "AND username NOT IN(SELECT Dashboard.UsersPoolsRelations.username FROM Dashboard.UsersPoolsRelations WHERE Dashboard.UsersPoolsRelations.poolId IN (SELECT poolId FROM Dashboard.UsersPoolsRelations WHERE username = '$author') AND Dashboard.UsersPoolsRelations.isAdmin = 1 GROUP BY Dashboard.UsersPoolsRelations.username)";
                         
                         $result2 = mysqli_query($link, $query2) or die(mysqli_error($link));

                         if($result2)
                         {
                            if($result2->num_rows > 0) 
                            {
                               while ($row = $result2->fetch_assoc()) 
                               {
                                  array_push($usersList, $row["username"]);
                               }
                            }
                         } 

                         foreach($usersList as $item)
                         {
                           if($item != $author)//Viene tolto l'autore della dashboard
                           {
                              $user = [];
                              $user['username'] = $item;
                              $user['consultationSetUser'] = $item; 
                              
                              $query3 = "SELECT * FROM Dashboard.DashboardsViewPermissions " .
                                        "WHERE IdDashboard = $dashboardId " .
                                        "AND username = '$item'";
                              
                              $result3 = mysqli_query($link, $query3) or die(mysqli_error($link));

                              if($result3)
                              {
                                 if($result3->num_rows > 0) 
                                 {
                                    $user['enabledViewUser'] = $item;
                                 }
                                 else
                                 {
                                    $user['enabledViewUser'] = NULL;
                                 }
                              }

                              array_push($users, $user);
                           }
                         }
                        break;
                  }
               }
               
               $visibilitySet['users'] = $users;
               echo json_encode($visibilitySet);
               mysqli_close($link);
               break;
       }
   }