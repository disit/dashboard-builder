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
   $link = mysqli_connect($host, $username, $password);
   mysqli_select_db($link, $dbname);
   
   //Definizioni di funzione
   function checkLdapRole($connection, $userDn, $role) 
   {
      $result = ldap_search($connection, 'dc=ldap,dc=disit,dc=org', '(&(objectClass=organizationalRole)(cn=' . $role . ')(roleOccupant=' . $userDn . '))');
      $entries = ldap_get_entries($connection, $result);
      foreach ($entries as $key => $value) 
      {
         if(is_numeric($key)) 
         {
            if($value["cn"]["0"] == $role) 
            {
               return true;
            }
         }
      }
      return false;
  }
   
   if(!$link->set_charset("utf8")) 
   {
       exit();
   }

   if(isset($_SESSION['loggedRole']))
   {
      if(($_SESSION['loggedRole'] == "ToolAdmin")||($_SESSION['loggedRole'] == "AreaManager"))
      {
         //Elenco complessivo utenti LDAP non tool admin
         $temp = [];
         $ldapUsers = [];

         $ds = ldap_connect($ldapServer, $ldapPort);
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
            if(!checkLdapRole($ds, $temp[$i], "ToolAdmin"))
            {
               $name = str_replace("cn=", "", $temp[$i]);
               $name = str_replace(",dc=ldap,dc=disit,dc=org", "", $name);
               if(checkLdapRole($ds, $temp[$i], "Observer"))
               {
                  $role = "Observer";
               }
               else
               {
                  if(checkLdapRole($ds, $temp[$i], "Manager"))
                  {
                     $role = "Manager";
                  }
                  else
                  {
                     if(checkLdapRole($ds, $temp[$i], "AreaManager"))
                     {
                        $role = "AreaManager";
                     }
                  }
               }
               $ldapUsers[$name] = $role;
            }
         }

         $query = "SELECT * FROM Dashboard.UsersPools";
         $result = mysqli_query($link, $query);

         $pools = [];

         if($result)
         {
             while($row = mysqli_fetch_assoc($result)) 
             {
                 $pool = [];
                 $poolId = $row["poolId"];
                 $poolName = $row["poolName"];

                 $innerMembers = [];
                 $innerMembersUsernames = [];
                 $outerMembers = [];
                 $addedMembers = [];
                 $removedMembers = [];
                 $adminChangedMembers = [];
                 $complementaryArray = [];

                 //Tutti i membri del gruppo, ldap e locali
                 $query2 = "SELECT Dashboard.UsersPoolsRelations.*, Dashboard.Users.admin AS userRole FROM Dashboard.UsersPoolsRelations LEFT JOIN Dashboard.Users ON Dashboard.UsersPoolsRelations.username = Dashboard.Users.username WHERE Dashboard.UsersPoolsRelations.poolId = '$poolId'";
                 $result2 = mysqli_query($link, $query2);

                 if($result2)
                 {
                     $complementary = "(";
                     $i = 0;
                     while($row2 = mysqli_fetch_assoc($result2)) 
                     {
                        $user = [];
                        $user['username'] = $row2["username"];
                        $user['isAdmin'] = $row2["isAdmin"];
                        $user['status'] = "kept";//Campo di comodità per successivi salvataggi post modifiche, così ce l'hanno tutti gli utenti

                        if(($row2['userRole'] == 'NULL')||($row2['userRole'] == NULL))
                        {
                           $user['userRole'] = $ldapUsers[$row2["username"]];
                        }
                        else
                        {
                           $user['userRole'] = $row2["userRole"];
                        }

                        if($i < (mysqli_num_rows($result2) - 1))
                        {
                           $complementary .= "'" . $row2["username"] . "', ";
                        }
                        else
                        {
                           $complementary .= "'" . $row2["username"] . "')";
                        }

                        array_push($innerMembersUsernames, $user['username']);
                        array_push($innerMembers, $user);
                        $i++;
                     }

                     $query3 = "SELECT * FROM Dashboard.Users WHERE Dashboard.Users.admin <> 'ToolAdmin' AND Dashboard.Users.username NOT IN " . $complementary;
                     $result3 = mysqli_query($link, $query3);

                     if($result3)
                     {
                         while($row3 = mysqli_fetch_assoc($result3)) 
                         {
                            $user = [];
                            $user['username'] = $row3["username"];
                            $user['status'] = "kept";//Campo di comodità per successivi salvataggi post modifiche, così ce l'hanno tutti gli utenti
                            $user['userRole'] = $row3["admin"];
                            array_push($outerMembers, $user);
                         }

                        //Aggiunta all'insieme outer degli utenti LDAP non già presenti nel gruppo
                        foreach ($ldapUsers as $key => $value)
                        {
                           if(!in_array($key, $innerMembersUsernames))
                           {
                              $user = [];
                              $user['username'] = $key;
                              $user['status'] = "kept";//Campo di comodità per successivi salvataggi post modifiche, così ce l'hanno tutti gli utenti
                              $user['userRole'] = $value;
                              array_push($outerMembers, $user);
                           }
                        }

                         $pool["poolId"] = $poolId;
                         $pool["poolName"] = $poolName;
                         $pool["edited"] = false;//Campo di comodità per successivi salvataggi post modifiche, così ce l'hanno tutti i pools
                         $pool["innerMembers"] = $innerMembers;
                         $pool["outerMembers"] = $outerMembers;
                         $pool["addedMembers"] = $addedMembers;//Campo di comodità per successivi salvataggi post modifiche, così ce l'hanno tutti i pools
                         $pool["removedMembers"] = $removedMembers;//Campo di comodità per successivi salvataggi post modifiche, così ce l'hanno tutti i pools
                         $pool["adminChangedMembers"] = $adminChangedMembers;//Campo di comodità per successivi salvataggi post modifiche, così ce l'hanno tutti i pools

                         array_push($pools, $pool);
                     }
                 }
             }
         }

         echo json_encode($pools);
         mysqli_close($link);
      }
   }