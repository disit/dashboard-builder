<?php
/* Dashboard Builder.
   Copyright (C) 2018 DISIT Lab https://www.disit.org - University of Florence

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
    $link = mysqli_connect($host, $username, $password);
    mysqli_select_db($link, $dbname);
    session_start(); 
    
    //Definizioni di funzione
    function isToolAdmin($localLink, $checkedUsr, $checkedPwd, $ldapServer, $ldapPort, $localLdapFlag)
    {
        $toolAdmins = [];
        $ldapUsername = "cn=". $checkedUsr . ",dc=ldap,dc=disit,dc=org";
        if($localLdapFlag == "yes")
        {
            $ds1 = ldap_connect($ldapServer, $ldapPort);
            ldap_set_option($ds1, LDAP_OPT_PROTOCOL_VERSION, 3);
            $bind = ldap_bind($ds1, $ldapUsername, $checkedPwd);

            if($bind)
            {
                $toolAdminsResult = ldap_search(
                    $ds1, 'dc=ldap,dc=disit,dc=org', 
                    '(cn=Dashboard)'
                 );

                 $toolAdminsEntries = ldap_get_entries($ds1, $toolAdminsResult);

                 foreach($toolAdminsEntries as $key => $value) 
                 {
                    for($index = 0; $index < (count($value["memberuid"]) - 1); $index++)
                    { 
                       $ldapusr = $value["memberuid"][$index];
                       if(ldapCheckRole(/*$ds2*/$ds1, $ldapusr, "ToolAdmin"))
                       {
                          $usr = str_replace("cn=", "", $ldapusr);
                          $usr = str_replace(",dc=ldap,dc=disit,dc=org", "", $usr);

                          array_push($toolAdmins, $usr); 
                       }
                    }
                 }

                 //ldap_unbind($ds2);
                 ldap_unbind($ds1);
            }
        }
        
        $permissionQuery4 = "SELECT username FROM Dashboard.Users WHERE admin = 'ToolAdmin'";
        $permissionResult4 = mysqli_query($localLink, $permissionQuery4);
        if($permissionResult4)
        {
           if(mysqli_num_rows($permissionResult4) > 0) 
           {
              while($row = $permissionResult4->fetch_assoc()) 
              {
                 array_push($toolAdmins, $row["username"]);
              }
           }
        }
        

        if(in_array($checkedUsr, $toolAdmins))
        {
           return true;
        }
        else
        {
           return false;
        }
    }
    
    
    function checkMembership($connection, $userDn, $tool) 
    {
         $result = ldap_search(
                 $connection, 'dc=ldap,dc=disit,dc=org', 
                 '(&(objectClass=posixGroup)(memberUid=' . $userDn . '))'
         );
         $entries = ldap_get_entries($connection, $result);
         //echo var_dump($entries);
         foreach ($entries as $key => $value) {
             
             if (is_numeric($key)) {
                 if ($value["cn"]["0"] == $tool) {
                     return true;
                 }
             }
         }
         return false;
     }
    
    function ldapCheckRole($connection, $userDn, $role) 
    {
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
    
    function getDashboardParams($link) 
    {
        if(isset($_GET['dashboardId']) && !empty($_GET['dashboardId']))
        {
            $dashboardId = mysqli_real_escape_string($link, $_GET['dashboardId']);
        }
        else 
        {
           return false;
        }
        
        $query = "SELECT * FROM Dashboard.Config_dashboard WHERE Id = '$dashboardId'";
        $result = mysqli_query($link, $query);
        
        return mysqli_fetch_array($result);
    }
    
    function getDashboardWidgets($link)
    {
        if(isset($_GET['dashboardId']) && !empty($_GET['dashboardId']))
        {
            $dashboardId = mysqli_real_escape_string($link, $_GET['dashboardId']);
        }
        else 
        {
           return false;
        }
        
        $query = "SELECT * FROM Config_widget_dashboard AS dashboardWidgets " .
                    "LEFT JOIN Widgets AS widgetTypes ON dashboardWidgets.type_w = widgetTypes.id_type_widget " .
                    "LEFT JOIN Descriptions AS metrics ON dashboardWidgets.id_metric = metrics.IdMetric " .   
                    "LEFT JOIN NodeRedMetrics AS nrMetrics ON dashboardWidgets.id_metric = nrMetrics.name " . 
                    "LEFT JOIN NodeRedInputs AS nrInputs ON dashboardWidgets.id_metric = nrInputs.name " . 
                    "WHERE dashboardWidgets.id_dashboard = $dashboardId " .
                    "AND dashboardWidgets.canceller IS NULL " .
                    "AND dashboardWidgets.cancelDate IS NULL " . 
                    "ORDER BY dashboardWidgets.n_row, dashboardWidgets.n_column ASC";
        
        $result = mysqli_query($link, $query);
        $dashboardWidgets = array();

        if(mysqli_num_rows($result) > 0) 
        {
            while($row = mysqli_fetch_assoc($result)) 
            {
                array_push($dashboardWidgets, $row);
            }
        }
        mysqli_close($link);
        return $dashboardWidgets;
    }
    
    //Corpo dell'API        
    if(!$link->set_charset("utf8")) 
    {
       echo '<script type="text/javascript">';
       echo 'alert("KO");';
       echo '</script>';
       printf("Error loading character set utf8: %s\n", $link->error);
       exit();
    }
    
        $dashboardId = $_REQUEST['dashboardId'];
        $query = "SELECT Dashboard.Config_dashboard.visibility AS visibility, Dashboard.Users.admin AS role, Dashboard.Config_dashboard.user AS username FROM Dashboard.Config_dashboard " .
                 "LEFT JOIN Dashboard.Users " .
                 "ON Dashboard.Config_dashboard.user = Dashboard.Users.username " .   
                 "WHERE Dashboard.Config_dashboard.Id = '$dashboardId'";
        
        $result = mysqli_query($link, $query);
        $response = [];
        $ds = null;

        if($result)
        {
            $row = mysqli_fetch_array($result);
            $visibility = $row["visibility"];
            $authorUsername = $row["username"];
            $authorLdapUsername = "cn=". $row["username"] . ",dc=ldap,dc=disit,dc=org";
            $response["visibility"] = $visibility;

            if(($row["role"] == NULL) || ($row["role"] == "NULL"))
            {
                //Autore LDAP
                if($ldapActive == "yes")
                {
                    $ds = ldap_connect($ldapServer, $ldapPort);
                    ldap_set_option($ds, LDAP_OPT_PROTOCOL_VERSION, 3);
                    //Lasciamo questo bind anonimo, è solo un controllo sul ruolo dell'autore, a questo punto di codice non abbiamo credenziali utente
                    $bind = ldap_bind($ds);

                    if(ldapCheckRole($ds, $authorLdapUsername, "Manager"))
                    {
                       $authorRole = "Manager";
                    }
                    else
                    {
                      if(ldapCheckRole($ds, $authorLdapUsername, "AreaManager"))
                      {
                          $authorRole = "AreaManager";
                      }
                      else
                      {
                         if(ldapCheckRole($ds, $authorLdapUsername, "ToolAdmin"))
                         {
                            $authorRole = "ToolAdmin";
                         }
                         else
                         {
                             //Caso in cui l'utente proviene da NodeRED e non è né censito su LDAP né su DB locale: come patch lo marchiamo come "Manager"
                             $authorRole = "Manager";
                         }
                      }
                    }

                    ldap_unbind($ds);
                }
            }
            else
            {
               $authorRole = $row["role"];
            }

            switch($visibility)
            {
                //Ok
                case "public":
                    $response["dashboardParams"] = getDashboardParams($link);
                    $response["dashboardWidgets"] = getDashboardWidgets($link);
                    break;

                //Ok 
                case "author":
                   //Utente collegato all'applicazione
                   if((isset($_SESSION['loggedUsername']))&&($_REQUEST['loggedUserFirstAttempt'] == "true"))
                   {
                      $applicantUsername = $_SESSION['loggedUsername'];
                      $applicantPassword = $_SESSION['loggedPassword'];

                      //Controllo di presenza del richiedente fra l'elenco degli utenti autorizzati a vedere questa dashboard
                      switch($authorRole)
                      {
                          //Autore è un Manager: possono entrare l'autore, gli utenti abilitati in Dashboard.DashboardsViewPermissions, gli area managers dei gruppi di cui l'autore fa parte e i tool admin
                              case "Manager":
                                 //Controlliamo se è l'autore
                                 $permissionQuery1 = "SELECT count(*) AS isAuthor FROM Dashboard.Config_dashboard WHERE Id = $dashboardId AND user = '$applicantUsername'";
                                 $permissionResult1 = mysqli_query($link, $permissionQuery1);
                                 
                                 if($permissionResult1)
                                 {
                                     $row = mysqli_fetch_array($permissionResult1);
                                     if($row["isAuthor"] > 0)
                                     {
                                         $response["detail"] = "Ok";
                                         $response["context"] = "View";
                                         $response["dashboardParams"] = getDashboardParams($link);
                                         $response["dashboardWidgets"] = getDashboardWidgets($link);
                                     }
                                     else
                                     {
                                        //Controlliamo se è un area manager dei gruppi di cui l'autore fa parte
                                        $permissionQuery3 = "SELECT count(*) AS isAreaManager FROM Dashboard.UsersPoolsRelations WHERE username = '$applicantUsername' AND isAdmin = 1 AND poolId IN (SELECT poolId FROM Dashboard.UsersPoolsRelations WHERE username = '$authorUsername')";
                                        $permissionResult3 = mysqli_query($link, $permissionQuery3);

                                        if($permissionResult3)
                                        {
                                          $row = mysqli_fetch_array($permissionResult3);
                                          if($row["isAreaManager"] > 0)
                                          {
                                            $response["detail"] = "Ok";
                                            $response["context"] = "View";
                                            $response["dashboardParams"] = getDashboardParams($link);
                                            $response["dashboardWidgets"] = getDashboardWidgets($link);
                                          }
                                          else
                                          {
                                             //Controlliamo se è un tool admin
                                             if(isToolAdmin($link, $applicantUsername, $applicantPassword, $ldapServer, $ldapPort, $ldapActive))
                                             {
                                                $response["detail"] = "Ok";
                                                $response["context"] = "View";
                                                $response["dashboardParams"] = getDashboardParams($link);
                                                $response["dashboardWidgets"] = getDashboardWidgets($link);
                                             }
                                             else
                                             {
                                                $response["detail"] = "loggedUserKo";
                                             }
                                          }
                                        }
                                     }
                                 }
                                 else
                                 {
                                     $response["detail"] = "checkLoggedUserQueryKo";
                                 }
                                 break;

                              case "AreaManager":
                                 //RESTITUIAMO L'AUTORE E I TOOL ADMIN
                                  //Controlliamo se è l'autore
                                  $permissionQuery1 = "SELECT count(*) AS isAuthor FROM Dashboard.Config_dashboard WHERE Id = $dashboardId AND user = '$applicantUsername'";
                                  $permissionResult1 = mysqli_query($link, $permissionQuery1);
                                  if($permissionResult1)
                                  {
                                      $row = mysqli_fetch_array($permissionResult1);
                                      if($row["isAuthor"] > 0)
                                      {
                                        $response["detail"] = "Ok";
                                        $response["context"] = "View";
                                        $response["dashboardParams"] = getDashboardParams($link);
                                        $response["dashboardWidgets"] = getDashboardWidgets($link);
                                      }
                                      else
                                      {//Controlliamo se è tool admin
                                        if(isToolAdmin($link, $applicantUsername, $applicantPassword, $ldapServer, $ldapPort, $ldapActive))
                                        {
                                           $response["detail"] = "Ok";
                                           $response["context"] = "View";
                                           $response["dashboardParams"] = getDashboardParams($link);
                                           $response["dashboardWidgets"] = getDashboardWidgets($link);
                                        }
                                        else
                                        {
                                           $response["detail"] = "loggedUserKo";
                                        }
                                      }
                                  }
                                  else
                                  {
                                     $response["detail"] = "checkLoggedUserQueryKo";
                                  }
                                 break;

                              case "ToolAdmin":
                                 //Controlliamo se è un tool admin
                                  if(isToolAdmin($link, $applicantUsername, $applicantPassword, $ldapServer, $ldapPort, $ldapActive))
                                  {
                                     $response["detail"] = "Ok";
                                     $response["context"] = "View";
                                     $response["dashboardParams"] = getDashboardParams($link);
                                     $response["dashboardWidgets"] = getDashboardWidgets($link);
                                  }
                                  else
                                  {
                                     $response["detail"] = "loggedUserKo";
                                  }
                                 break;
                      }

                   }
                   //Utente NON collegato all'applicazione
                   else
                   {  
                      $proceed = null;
                      //Caso credenziali non fornite
                      if(($_REQUEST['username'] == "") || ($_REQUEST['password'] == ""))
                      {
                         $response["detail"] = "credentialsMissing";
                         $proceed = false; 
                     }
                     else//Credenziali fornite
                     {
                         //Controllo presenza credenziali fornite su elenco utenti LDAP e locale
                         $applicantUsername = $_REQUEST['username'];
                         $ldapusr = "cn=" . $applicantUsername . ",dc=ldap,dc=disit,dc=org";
                         $applicantPassword = $_REQUEST['password'];
                         $applicantPasswordMd5 = md5($applicantPassword);

                         $query = "SELECT count(*) AS isRegistered FROM Dashboard.Users WHERE username = '$applicantUsername' AND password = '$applicantPasswordMd5'";
                         $result = mysqli_query($link, $query);
                         if($result)
                         {
                            $row = mysqli_fetch_array($result);
                            $tool = "Dashboard";
                            
                            if($ldapActive == "yes")
                            {
                                $ds2 = ldap_connect($ldapServer, $ldapPort);
                                ldap_set_option($ds2, LDAP_OPT_PROTOCOL_VERSION, 3);
                                $bind = ldap_bind($ds2, $ldapusr, $applicantPassword);//Mancano username e password

                                $ldapMember = checkMembership($ds2, $ldapusr, $tool);
                                ldap_unbind($ds2);

                                 if(($row["isRegistered"] <= 0)&&($ldapMember == false))
                                 {
                                   $response["detail"] = "userNotRegistered";
                                   $proceed = false;
                                 }
                                 else
                                 {
                                     //Controllo di presenza del richiedente fra l'elenco degli utenti autorizzati a vedere questa dashboard
                                     $proceed = true;
                                 }
                            }
                         }
                         else 
                         {
                            $response["detail"] = "checkUserQueryKo";
                            $proceed = false;
                         }
                     }

                     //Codice a comune tra i due casi if-else precedenti se vanno a buon fine
                     if($proceed)
                     {
                         switch($authorRole)
                         {
                            //Autore è un Manager: possono entrare l'autore, i utenti abilitati in Dashboard.DashboardsViewPermissions, gli area managers dei gruppi di cui l'autore fa parte e i tool admin
                             case "Manager":
                                //Controlliamo se è l'autore
                                $permissionQuery1 = "SELECT count(*) AS isAuthor FROM Dashboard.Config_dashboard WHERE Id = $dashboardId AND user = '$applicantUsername'";
                                $permissionResult1 = mysqli_query($link, $permissionQuery1);
                                if($permissionResult1)
                                {
                                    $row = mysqli_fetch_array($permissionResult1);
                                    if($row["isAuthor"] > 0)
                                    {
                                        $response["detail"] = "Ok";
                                        $response["context"] = "View";
                                        $response["dashboardParams"] = getDashboardParams($link);
                                        $response["dashboardWidgets"] = getDashboardWidgets($link);
                                    }
                                    else
                                    {
                                        //Controlliamo se è un area manager dei gruppi di cui l'autore fa parte
                                        $permissionQuery3 = "SELECT count(*) AS isAreaManager FROM Dashboard.UsersPoolsRelations WHERE username = '$applicantUsername' AND isAdmin = 1 AND poolId IN (SELECT poolId FROM Dashboard.UsersPoolsRelations WHERE username = '$authorUsername')";
                                        $permissionResult3 = mysqli_query($link, $permissionQuery3);

                                        if($permissionResult3)
                                        {
                                          $row = mysqli_fetch_array($permissionResult3);
                                          if($row["isAreaManager"] > 0)
                                          {
                                            $response["detail"] = "Ok";
                                            $response["context"] = "View";
                                            $response["dashboardParams"] = getDashboardParams($link);
                                            $response["dashboardWidgets"] = getDashboardWidgets($link);
                                          }
                                          else
                                          {
                                             //Controlliamo se è un tool admin
                                             if(isToolAdmin($link, $applicantUsername, $applicantPassword, $ldapServer, $ldapPort, $ldapActive))
                                             {
                                                $response["detail"] = "Ok";
                                                $response["context"] = "View";
                                                $response["dashboardParams"] = getDashboardParams($link);
                                                $response["dashboardWidgets"] = getDashboardWidgets($link);
                                             }
                                             else
                                             {
                                                $response["detail"] = "Ko";
                                             }
                                          }
                                        }
                                    }
                                }
                                else
                                {
                                    $response["detail"] = "checkUserQueryKo";
                                }
                                break;

                             case "AreaManager":
                                //RESTITUIAMO L'AUTORE E I TOOL ADMIN
                                //Controlliamo se è l'autore
                                $permissionQuery1 = "SELECT count(*) AS isAuthor FROM Dashboard.Config_dashboard WHERE Id = $dashboardId AND user = '$applicantUsername'";
                                $permissionResult1 = mysqli_query($link, $permissionQuery1);
                                if($permissionResult1)
                                {
                                    $row = mysqli_fetch_array($permissionResult1);
                                    if($row["isAuthor"] > 0)
                                    {
                                        $response["detail"] = "Ok";
                                        $response["context"] = "View";
                                        $response["dashboardParams"] = getDashboardParams($link);
                                        $response["dashboardWidgets"] = getDashboardWidgets($link);
                                    }
                                    else
                                    {
                                      //Se non è l'autore, controlliamo se è un tool admin
                                      if(isToolAdmin($link, $applicantUsername, $applicantPassword, $ldapServer, $ldapPort, $ldapActive))
                                      {
                                         $response["detail"] = "Ok";
                                         $response["context"] = "View";
                                         $response["dashboardParams"] = getDashboardParams($link);
                                         $response["dashboardWidgets"] = getDashboardWidgets($link);
                                      }
                                      else
                                      {
                                         $response["detail"] = "Ko";
                                      }
                                    }
                                }
                                else
                                {
                                   $response["detail"] = "checkUserQueryKo";
                                }
                                break;

                             case "ToolAdmin":
                                //Controlliamo se è un tool admin
                                if(isToolAdmin($link, $applicantUsername, $applicantPassword, $ldapServer, $ldapPort, $ldapActive))
                                {
                                   $response["detail"] = "Ok";
                                   $response["context"] = "View";
                                   $response["dashboardParams"] = getDashboardParams($link);
                                   $response["dashboardWidgets"] = getDashboardWidgets($link);
                                }
                                else
                                {
                                   $response["detail"] = "Ko";
                                }
                                break;
                         }
                     }
                   }
                   break;

                case "restrict":
                   //Utente collegato all'applicazione
                   if((isset($_SESSION['loggedUsername']))&&($_REQUEST['loggedUserFirstAttempt'] == "true"))
                   {
                      $applicantUsername = $_SESSION['loggedUsername'];
                      $applicantPassword = $_SESSION['loggedPassword'];
                      //Controllo di presenza del richiedente fra l'elenco degli utenti autorizzati a vedere questa dashboard
                      switch($authorRole)
                      {
                          //Autore è un Manager: possono entrare l'autore, gli utenti abilitati in Dashboard.DashboardsViewPermissions, gli area managers dei gruppi di cui l'autore fa parte e i tool admin
                            case "Manager":
                               //Controlliamo se è l'autore
                               $permissionQuery1 = "SELECT count(*) AS isAuthor FROM Dashboard.Config_dashboard WHERE Id = $dashboardId AND user = '$applicantUsername'";
                               $permissionResult1 = mysqli_query($link, $permissionQuery1);
                               if($permissionResult1)
                               {
                                   $row = mysqli_fetch_array($permissionResult1);
                                   if($row["isAuthor"] > 0)
                                   {
                                       $response["detail"] = "Ok";
                                       $response["context"] = "View";
                                       $response["dashboardParams"] = getDashboardParams($link);
                                       $response["dashboardWidgets"] = getDashboardWidgets($link);
                                   }
                                   else
                                   {
                                      //Controlliamo se è un utente autorizzato in DashboardsViewPermissions
                                      $permissionQuery2 = "SELECT count(*) AS isAuthorized FROM Dashboard.DashboardsViewPermissions WHERE IdDashboard = $dashboardId AND username = '$applicantUsername'";
                                      $permissionResult2 = mysqli_query($link, $permissionQuery2);

                                      if($permissionResult2)
                                      {
                                         $row = mysqli_fetch_array($permissionResult2);

                                         if($row["isAuthorized"] > 0)
                                         {
                                           $response["detail"] = "Ok";
                                           $response["context"] = "View";
                                           $response["dashboardParams"] = getDashboardParams($link);
                                           $response["dashboardWidgets"] = getDashboardWidgets($link);
                                         }
                                         else
                                         {
                                            //Controlliamo se è un area manager dei gruppi di cui l'autore fa parte
                                            $permissionQuery3 = "SELECT count(*) AS isAreaManager FROM Dashboard.UsersPoolsRelations WHERE username = '$applicantUsername' AND isAdmin = 1 AND poolId IN (SELECT poolId FROM Dashboard.UsersPoolsRelations WHERE username = '$authorUsername')";
                                            $permissionResult3 = mysqli_query($link, $permissionQuery3);

                                            if($permissionResult3)
                                            {
                                              $row = mysqli_fetch_array($permissionResult3);
                                              if($row["isAreaManager"] > 0)
                                              {
                                                $response["detail"] = "Ok";
                                                $response["context"] = "View";
                                                $response["dashboardParams"] = getDashboardParams($link);
                                                $response["dashboardWidgets"] = getDashboardWidgets($link);
                                              }
                                              else
                                              {
                                                 //Controlliamo se è un tool admin
                                                 if(isToolAdmin($link, $applicantUsername, $applicantPassword, $ldapServer, $ldapPort, $ldapActive)) 
                                                 {
                                                    $response["detail"] = "Ok";
                                                    $response["context"] = "View";
                                                    $response["dashboardParams"] = getDashboardParams($link);
                                                    $response["dashboardWidgets"] = getDashboardWidgets($link);
                                                 }
                                                 else
                                                 {
                                                    $response["detail"] = "loggedUserKo";
                                                 }
                                              }
                                            }
                                         }
                                      }
                                      else
                                      {
                                         $response["detail"] = "checkLoggedViewUserQueryKo";
                                      }
                                   }
                               }
                               else
                               {
                                   $response["detail"] = "checkLoggedViewUserQueryKo";
                               }
                               break;

                            case "AreaManager":
                               //RESTITUIAMO L'AUTORE E I TOOL ADMIN
                                //Controlliamo se è l'autore
                                $permissionQuery1 = "SELECT count(*) AS isAuthor FROM Dashboard.Config_dashboard WHERE Id = $dashboardId AND user = '$applicantUsername'";   
                                $permissionResult1 = mysqli_query($link, $permissionQuery1);
                                if($permissionResult1)
                                {
                                    $row = mysqli_fetch_array($permissionResult1);
                                    if($row["isAuthor"] > 0)
                                    {
                                        $response["detail"] = "Ok";
                                        $response["context"] = "View";
                                        $response["dashboardParams"] = getDashboardParams($link);
                                        $response["dashboardWidgets"] = getDashboardWidgets($link);
                                    }
                                    else
                                    {
                                       //Controlliamo se è un utente autorizzato in DashboardsViewPermissions
                                      $permissionQuery2 = "SELECT count(*) AS isAuthorized FROM Dashboard.DashboardsViewPermissions WHERE IdDashboard = $dashboardId AND username = '$applicantUsername'";  
                                      $permissionResult2 = mysqli_query($link, $permissionQuery2);

                                      if($permissionResult2)
                                      {
                                         $row = mysqli_fetch_array($permissionResult2);

                                         if($row["isAuthorized"] > 0)
                                         {
                                           $response["detail"] = "Ok";
                                           $response["context"] = "View";
                                           $response["dashboardParams"] = getDashboardParams($link);
                                           $response["dashboardWidgets"] = getDashboardWidgets($link);
                                         }
                                         else
                                         {
                                            //Controlliamo se è un area manager dei gruppi di cui l'autore fa parte
                                            $permissionQuery3 = "SELECT count(*) AS isAreaManager FROM Dashboard.UsersPoolsRelations WHERE username = '$applicantUsername' AND isAdmin = 1 AND poolId IN (SELECT poolId FROM Dashboard.UsersPoolsRelations WHERE username = '$authorUsername')";                 
                                            $permissionResult3 = mysqli_query($link, $permissionQuery3);

                                            if($permissionResult3)
                                            {
                                              $row = mysqli_fetch_array($permissionResult3);
                                              if($row["isAreaManager"] > 0)
                                              {
                                                $response["detail"] = "Ok";
                                                $response["context"] = "View";
                                                $response["dashboardParams"] = getDashboardParams($link);
                                                $response["dashboardWidgets"] = getDashboardWidgets($link);
                                              }
                                              else
                                              {
                                                 //Controlliamo se è un tool admin
                                                 if(isToolAdmin($link, $applicantUsername, $applicantPassword, $ldapServer, $ldapPort, $ldapActive)) 
                                                 {
                                                    $response["detail"] = "Ok";
                                                    $response["context"] = "View";
                                                    $response["dashboardParams"] = getDashboardParams($link);
                                                    $response["dashboardWidgets"] = getDashboardWidgets($link);
                                                 }
                                                 else
                                                 {
                                                    $response["detail"] = "loggedUserKo";
                                                 }
                                              }
                                            }
                                         }
                                      }
                                      else
                                      {
                                         $response["detail"] = "checkLoggedUserQueryKo";
                                      }
                                    }
                                }
                                else
                                {
                                   $response["detail"] = "checkLoggedUserQueryKo";
                                }
                               break;

                            case "ToolAdmin":
                               //Controlliamo se è un tool admin
                                if(isToolAdmin($link, $applicantUsername, $applicantPassword, $ldapServer, $ldapPort, $ldapActive))
                                {
                                   $response["detail"] = "Ok";
                                   $response["context"] = "View";
                                   $response["dashboardParams"] = getDashboardParams($link);
                                   $response["dashboardWidgets"] = getDashboardWidgets($link);
                                }
                                else
                                {
                                  //Controlliamo se è un utente autorizzato in DashboardsViewPermissions
                                  $permissionQuery2 = "SELECT count(*) AS isAuthorized FROM Dashboard.DashboardsViewPermissions WHERE IdDashboard = $dashboardId AND username = '$applicantUsername'";
                                  $permissionResult2 = mysqli_query($link, $permissionQuery2);

                                  if($permissionResult2)
                                  {
                                     $row = mysqli_fetch_array($permissionResult2);

                                     if($row["isAuthorized"] > 0)
                                     {
                                       $response["detail"] = "Ok";
                                       $response["context"] = "View";
                                       $response["dashboardParams"] = getDashboardParams($link);
                                       $response["dashboardWidgets"] = getDashboardWidgets($link);
                                     }
                                     else
                                     {
                                       $response["detail"] = "loggedUserKo";
                                     }
                                  }
                                  else
                                  {
                                     $response["detail"] = "checkLoggedUserQueryKo";
                                  }
                                }
                               break;
                      }
                   }
                   //Utente NON collegato all'applicazione
                   else
                   {  
                      $proceed = null;

                      //Caso credenziali non fornite
                      if(($_REQUEST['username'] == "") || ($_REQUEST['password'] == ""))
                      {
                         $response["detail"] = "credentialsMissing";
                         $proceed = false; 
                     }
                     else//Credenziali fornite
                     {
                         //Controllo presenza credenziali fornite su elenco utenti LDAP e locale
                         $applicantUsername = $_REQUEST['username'];
                         $ldapusr = "cn=" . $applicantUsername . ",dc=ldap,dc=disit,dc=org";
                         $applicantPassword = $_REQUEST['password'];
                         $applicantPasswordMd5 = md5($applicantPassword);

                         $query = "SELECT count(*) AS isRegistered FROM Dashboard.Users WHERE username = '$applicantUsername' AND password = '$applicantPasswordMd5'";
                         $result = mysqli_query($link, $query);
                         if($result)
                         {
                            $row = mysqli_fetch_array($result);
                            $tool = "Dashboard";
                            
                            if($ldapActive == "yes")
                            {
                                $ds2 = ldap_connect($ldapServer, $ldapPort);
                                ldap_set_option($ds2, LDAP_OPT_PROTOCOL_VERSION, 3);
                                $bind = ldap_bind($ds2, $ldapusr, $applicantPassword);//Mancano username e password

                                $ldapMember = checkMembership($ds2, $ldapusr, $tool);
                                ldap_unbind($ds2);

                                 if(($row["isRegistered"] <= 0)&&($ldapMember == false))
                                 {
                                   $response["detail"] = "userNotRegistered";
                                   $proceed = false;
                                 }
                                 else
                                 {
                                     //Controllo di presenza del richiedente fra l'elenco degli utenti autorizzati a vedere questa dashboard
                                     $proceed = true;
                                 }
                            }
                         }
                         else 
                         {
                            $response["detail"] = "checkLoggedViewUserQueryKo";
                            $proceed = false;
                         }
                     }

                     //Codice a comune tra i due casi if-else precedenti se vanno a buon fine
                     if($proceed)
                     {
                         switch($authorRole)
                         {
                            //Autore è un Manager: possono entrare l'autore, gli utenti abilitati in Dashboard.DashboardsViewPermissions, gli area managers dei gruppi di cui l'autore fa parte e i tool admin
                             case "Manager":
                                //Controlliamo se è l'autore
                                $permissionQuery1 = "SELECT count(*) AS isAuthor FROM Dashboard.Config_dashboard WHERE Id = $dashboardId AND user = '$applicantUsername'";
                                $permissionResult1 = mysqli_query($link, $permissionQuery1);
                                if($permissionResult1)
                                {
                                    $row = mysqli_fetch_array($permissionResult1);
                                    if($row["isAuthor"] > 0)
                                    {
                                        $response["detail"] = "Ok";
                                        $response["context"] = "View";
                                        $response["dashboardParams"] = getDashboardParams($link);
                                        $response["dashboardWidgets"] = getDashboardWidgets($link);
                                    }
                                    else
                                    {
                                       //Controlliamo se è un utente autorizzato in DashboardsViewPermissions
                                       $permissionQuery2 = "SELECT count(*) AS isAuthorized FROM Dashboard.DashboardsViewPermissions WHERE IdDashboard = $dashboardId AND username = '$applicantUsername'";
                                       $permissionResult2 = mysqli_query($link, $permissionQuery2);

                                       if($permissionResult2)
                                       {
                                          $row = mysqli_fetch_array($permissionResult2);
                                          if($row["isAuthorized"] > 0)
                                          {
                                            $response["detail"] = "Ok";
                                            $response["context"] = "View";
                                            $response["dashboardParams"] = getDashboardParams($link);
                                            $response["dashboardWidgets"] = getDashboardWidgets($link);
                                          }
                                          else
                                          {
                                             //Controlliamo se è un area manager dei gruppi di cui l'autore fa parte
                                             $permissionQuery3 = "SELECT count(*) AS isAreaManager FROM Dashboard.UsersPoolsRelations WHERE username = '$applicantUsername' AND isAdmin = 1 AND poolId IN (SELECT poolId FROM Dashboard.UsersPoolsRelations WHERE username = '$authorUsername')";
                                             $permissionResult3 = mysqli_query($link, $permissionQuery3);

                                             if($permissionResult3)
                                             {
                                               $row = mysqli_fetch_array($permissionResult3);
                                               if($row["isAreaManager"] > 0)
                                               {
                                                 $response["detail"] = "Ok";
                                                 $response["context"] = "View";
                                                 $response["dashboardParams"] = getDashboardParams($link);
                                                 $response["dashboardWidgets"] = getDashboardWidgets($link);
                                               }
                                               else
                                               {
                                                  //Controlliamo se è un tool admin
                                                  if(isToolAdmin($link, $applicantUsername, $applicantPassword, $ldapServer, $ldapPort, $ldapActive)) 
                                                  {
                                                     $response["detail"] = "Ok";
                                                     $response["context"] = "View";
                                                     $response["dashboardParams"] = getDashboardParams($link);
                                                     $response["dashboardWidgets"] = getDashboardWidgets($link);
                                                  }
                                                  else
                                                  {
                                                     $response["detail"] = "Ko";
                                                  }
                                               }
                                             }
                                          }
                                       }
                                       else
                                       {
                                          $response["detail"] = "checkLoggedViewUserQueryKo";
                                       }
                                    }
                                }
                                else
                                {
                                    $response["detail"] = "checkLoggedViewUserQueryKo";
                                }
                                break;

                             case "AreaManager":
                                //RESTITUIAMO L'AUTORE E I TOOL ADMIN
                                //Controlliamo se è l'autore
                                $permissionQuery1 = "SELECT count(*) AS isAuthor FROM Dashboard.Config_dashboard WHERE Id = $dashboardId AND user = '$applicantUsername'";
                                $permissionResult1 = mysqli_query($link, $permissionQuery1);
                                if($permissionResult1)
                                {
                                    $row = mysqli_fetch_array($permissionResult1);
                                    if($row["isAuthor"] > 0)
                                    {
                                        $response["detail"] = "Ok";
                                        $response["context"] = "View";
                                        $response["dashboardParams"] = getDashboardParams($link);
                                        $response["dashboardWidgets"] = getDashboardWidgets($link);
                                    }
                                    else
                                    {
                                      //Controlliamo se è un utente autorizzato in DashboardsViewPermissions
                                      $permissionQuery2 = "SELECT count(*) AS isAuthorized FROM Dashboard.DashboardsViewPermissions WHERE IdDashboard = $dashboardId AND username = '$applicantUsername'";
                                      $permissionResult2 = mysqli_query($link, $permissionQuery2);

                                      if($permissionResult2)
                                      {
                                         $row = mysqli_fetch_array($permissionResult2);

                                         if($row["isAuthorized"] > 0)
                                         {
                                           $response["detail"] = "Ok";
                                           $response["context"] = "View";
                                           $response["dashboardParams"] = getDashboardParams($link);
                                           $response["dashboardWidgets"] = getDashboardWidgets($link);
                                         }
                                         else
                                         {
                                           //Controlliamo se è un area manager dei gruppi di cui l'autore fa parte
                                            $permissionQuery3 = "SELECT count(*) AS isAreaManager FROM Dashboard.UsersPoolsRelations WHERE username = '$applicantUsername' AND isAdmin = 1 AND poolId IN (SELECT poolId FROM Dashboard.UsersPoolsRelations WHERE username = '$authorUsername')";                 
                                            $permissionResult3 = mysqli_query($link, $permissionQuery3);

                                            if($permissionResult3)
                                            {
                                              $row = mysqli_fetch_array($permissionResult3);
                                              if($row["isAreaManager"] > 0)
                                              {
                                                $response["detail"] = "Ok";
                                                $response["context"] = "View";
                                                $response["dashboardParams"] = getDashboardParams($link);
                                                $response["dashboardWidgets"] = getDashboardWidgets($link);
                                              }
                                              else
                                              {
                                                  //Controlliamo se è un tool admin
                                                 if(isToolAdmin($link, $applicantUsername, $applicantPassword, $ldapServer, $ldapPort, $ldapActive)) 
                                                 {
                                                    $response["detail"] = "Ok";
                                                    $response["context"] = "View";
                                                    $response["dashboardParams"] = getDashboardParams($link);
                                                    $response["dashboardWidgets"] = getDashboardWidgets($link);
                                                 }
                                                 else
                                                 {
                                                    $response["detail"] = "Ko";
                                                 }
                                              } 
                                            }
                                         }
                                      }
                                      else
                                      {
                                         $response["detail"] = "checkLoggedViewUserQueryKo";
                                      }
                                    }
                                }
                                else
                                {
                                   $response["detail"] = "checkLoggedViewUserQueryKo";
                                }
                                break;

                             case "ToolAdmin":
                                //Controlliamo se è un tool admin
                                if(isToolAdmin($link, $applicantUsername, $applicantPassword, $ldapServer, $ldapPort, $ldapActive)) 
                                {
                                   $response["detail"] = "Ok";
                                   $response["context"] = "View";
                                   $response["dashboardParams"] = getDashboardParams($link);
                                   $response["dashboardWidgets"] = getDashboardWidgets($link);
                                }
                                else
                                {
                                  //Controlliamo se è un utente autorizzato in DashboardsViewPermissions
                                  $permissionQuery2 = "SELECT count(*) AS isAuthorized FROM Dashboard.DashboardsViewPermissions WHERE IdDashboard = $dashboardId AND username = '$applicantUsername'";
                                  $permissionResult2 = mysqli_query($link, $permissionQuery2);

                                  if($permissionResult2)
                                  {
                                     $row = mysqli_fetch_array($permissionResult2);

                                     if($row["isAuthorized"] > 0)
                                     {
                                       $response["detail"] = "Ok";
                                       $response["context"] = "View";
                                       $response["dashboardParams"] = getDashboardParams($link);
                                       $response["dashboardWidgets"] = getDashboardWidgets($link);
                                     }
                                     else
                                     {
                                       $response["detail"] = "Ko";
                                     }
                                  }
                                  else
                                  {
                                     $response["detail"] = "checkLoggedViewUserQueryKo";
                                  }
                                }
                                break;
                         }
                     }
                   }
                   break;
            }
        }
        else
        {
            $response["visibility"] = "Ko";
        }

        echo json_encode($response);
        mysqli_close($link);
    
   
    

