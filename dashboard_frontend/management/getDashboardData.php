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
    function isToolAdmin($localLink, $checkedUsr, $ldapServer, $ldapPort)
    {
      $ds1 = ldap_connect($ldapServer, $ldapPort);
      ldap_set_option($ds1, LDAP_OPT_PROTOCOL_VERSION, 3);
      $bind = ldap_bind($ds1);

      $toolAdminsResult = ldap_search(
         $ds1, 'dc=ldap,dc=disit,dc=org', 
         '(cn=Dashboard)'
      );

      $toolAdminsEntries = ldap_get_entries($ds1, $toolAdminsResult);
      $toolAdmins = [];

      ldap_unbind($ds1);

      $ds2 = ldap_connect($ldapServer, $ldapPort);
      ldap_set_option($ds2, LDAP_OPT_PROTOCOL_VERSION, 3);
      $bind = ldap_bind($ds2);

      foreach ($toolAdminsEntries as $key => $value) 
      {
         for($index = 0; $index < (count($value["memberuid"]) - 1); $index++)
         { 
            $ldapusr = $value["memberuid"][$index];
            if(ldapCheckRole($ds2, $ldapusr, "ToolAdmin"))
            {
               $usr = str_replace("cn=", "", $ldapusr);
               $usr = str_replace(",dc=ldap,dc=disit,dc=org", "", $usr);

               array_push($toolAdmins, $usr); 
            }
         }
      }

      ldap_unbind($ds2);

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
      //echo var_dump($entries);
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
           if(isset($_SESSION['dashboardId']))
           {
              $dashboardId = $_SESSION['dashboardId'];
           }
           else
           {
              //Caso in cui né si è loggati all'applicazione né si è inviato il dashboard id in GET alla view
              return false;
           }
        }
        
        $query = "SELECT * FROM Dashboard.Config_dashboard WHERE Id = '$dashboardId'";
        $result = mysqli_query($link, $query) or die(mysqli_error($link));
        $dashboardParams = array();

        if($result->num_rows > 0) 
        {
            while($row = mysqli_fetch_array($result)) 
            {
                $dashboardParams[] = $row;
            }
        }
        
        return $dashboardParams;
    }
    
    function getDashboardWidgets($link)
    {
        if (isset($_GET['dashboardId']) && !empty($_GET['dashboardId']))
        {
            $dashboardId = mysqli_real_escape_string($link, $_GET['dashboardId']);
        }
        else 
        {
           if(isset($_SESSION['dashboardId']))
           {
              $dashboardId = $_SESSION['dashboardId'];
           }
           else
           {
              //Caso in cui né si è loggati all'applicazione né si è inviato il dashboard id in GET alla view
              return false;
           }
        }
        
        $query = "SELECT * FROM Config_widget_dashboard INNER JOIN Widgets ON Config_widget_dashboard.type_w=Widgets.id_type_widget WHERE id_dashboard = '$dashboardId' ORDER BY n_row, n_column ASC";
        $result = mysqli_query($link, $query) or die(mysqli_error($link));
        $dashboardWidgets = array();

        if($result->num_rows > 0) 
        {
            while ($row = mysqli_fetch_array($result)) 
            {
                $widget = array(
                    "id_widget" => $row['Id'],
                    "name_widget" => $row['name_w'],
                    "id_metric_widget" => $row['id_metric'],
                    "type_widget" => $row['type_w'],
                    "n_row_widget" => $row['n_row'],
                    "n_column_widget" => $row['n_column'],
                    "size_rows_widget" => $row['size_rows'],
                    "size_columns_widget" => $row['size_columns'],
                    "title_widget" => preg_replace('/\s+/', '_', $row['title_w']),
                    "color_widget" => $row['color_w'],
                    "frequency_widget" => $row['frequency_w'],
                    "temporal_range_widget" => $row['temporal_range_w'],
                    "source_file_widget" => $row['source_php_widget'],
                    "municipality_widget" => $row['municipality_w'],
                    "message_widget" => $row['infoMessage_w'],
                    "link_w" => $row['link_w'],
                    "param_w" => $row['parameters'],
                    "frame_color" => $row['frame_color_w'],
                    "udm" => $row['udm'],
                    "fontSize" => $row['fontSize'],
                    "fontColor" => $row['fontColor'],
                    "controlsPosition" => $row['controlsPosition'],
                    "showTitle" => $row['showTitle'],
                    "controlsVisibility" => $row['controlsVisibility'],
                    "zoomFactor" => $row['zoomFactor'],
                    "zoomControlsColor" => $row['zoomControlsColor'],
                    "defaultTab" => $row['defaultTab'],
                    "scaleX" => $row['scaleX'],
                    "scaleY" => $row['scaleY'],
                    "headerFontColor" => $row['headerFontColor']
                );
                array_push($dashboardWidgets, $widget);
            }
        }
        for ($j = 0; $j < count($dashboardWidgets); ++$j) 
        {
            $id_metric_tmp = array();
            if(strpos($dashboardWidgets[$j]['id_metric_widget'], '+') !== false) 
            {
                $id_metric_tmp = explode('+', $dashboardWidgets[$j]['id_metric_widget']);
            } 
            else 
            {
                $id_metric_tmp[] = $dashboardWidgets[$j]['id_metric_widget'];
            }

            $metrics_tmp = array();
            for ($k = 0; $k < count($id_metric_tmp); ++$k) 
            {
                $query7 = "SELECT metricType, description_short, source, municipalityOption, timeRangeOption FROM Dashboard.Descriptions where IdMetric='$id_metric_tmp[$k]'";
                $result7 = mysqli_query($link, $query7) or die(mysqli_error($link));

                if ($result7->num_rows > 0) 
                {
                    while ($row7 = mysqli_fetch_array($result7)) 
                    {
                        $metric_tmp = array("id_metric" => $id_metric_tmp[$k],
                            "descripshort_metric" => $row7['description_short'],
                            "type_metric" => $row7['metricType'],
                            "range" => $row7['timeRangeOption'],
                            "source_metric" => utf8_encode(preg_replace('/\s+/', '_', $row7['source'])));

                        array_push($metrics_tmp, $metric_tmp);
                    }
                }
            }
            $dashboardWidgets[$j] = array_merge($dashboardWidgets[$j], array("metrics_prop" => $metrics_tmp));
            unset($metrics_tmp);
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

            if(/*($row["role"] == NULL) || ($row["role"] == "NULL")*/false)
            {
                //Autore LDAP
                $ds = ldap_connect($ldapServer, $ldapPort);
                ldap_set_option($ds, LDAP_OPT_PROTOCOL_VERSION, 3);
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
                  }
                }

                ldap_unbind($ds);
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

                      //Controllo di presenza del richiedente fra l'elenco degli utenti autorizzati a vedere questa dashboard
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
                                             if(/*isToolAdmin($link, $applicantUsername, $ldapServer, $ldapPort)*/true)
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
                                        if(isToolAdmin($link, $applicantUsername, $ldapServer, $ldapPort))
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
                                  if(isToolAdmin($link, $applicantUsername, $ldapServer, $ldapPort))
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
                         //Controllare credenziali in sessione per la view
                         if(isset($_SESSION["dashViewUsername" . $dashboardId]))
                         {
                             $applicantUsername = $_SESSION["dashViewUsername" . $dashboardId];
                             $proceed = true;
                         }
                         else 
                         {
                             $response["detail"] = "credentialsMissing";
                             $proceed = false;
                         }
                     }
                     else//Credenziali fornite
                     {
                         //Controllo presenza credenziali fornite su elenco utenti LDAP e locale
                         $applicantUsername = $_REQUEST['username'];
                         $applicantPassword = $_REQUEST['password'];
                         $applicantPasswordMd5 = md5($applicantPassword);

                         $query = "SELECT count(*) AS isRegistered FROM Dashboard.Users WHERE username = '$applicantUsername' AND password = '$applicantPasswordMd5'";
                         $result = mysqli_query($link, $query);
                         if($result)
                         {
                            $row = mysqli_fetch_array($result);
                            $tool = "Dashboard";

                            $ds2 = ldap_connect($ldapServer, $ldapPort);
                            ldap_set_option($ds2, LDAP_OPT_PROTOCOL_VERSION, 3);
                            $bind = ldap_bind($ds2);

                            $ldapusr = "cn=" . $applicantUsername . ",dc=ldap,dc=disit,dc=org";

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
                                        $_SESSION["dashViewUsername" . $dashboardId] = $applicantUsername;
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
                                            $_SESSION["dashViewUsername" . $dashboardId] = $applicantUsername;
                                          }
                                          else
                                          {
                                             //Controlliamo se è un tool admin
                                             if(isToolAdmin($link, $applicantUsername, $ldapServer, $ldapPort))
                                             {
                                                $response["detail"] = "Ok";
                                                $response["context"] = "View";
                                                $response["dashboardParams"] = getDashboardParams($link);
                                                $response["dashboardWidgets"] = getDashboardWidgets($link);
                                                $_SESSION["dashViewUsername" . $dashboardId] = $applicantUsername;
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
                                        $_SESSION["dashViewUsername" . $dashboardId] = $applicantUsername;
                                    }
                                    else
                                    {
                                      //Se non è l'autore, controlliamo se è un tool admin
                                      if(isToolAdmin($link, $applicantUsername, $ldapServer, $ldapPort)) 
                                      {
                                         $response["detail"] = "Ok";
                                         $response["context"] = "View";
                                         $response["dashboardParams"] = getDashboardParams($link);
                                         $response["dashboardWidgets"] = getDashboardWidgets($link);
                                         $_SESSION["dashViewUsername" . $dashboardId] = $applicantUsername;
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
                                if(isToolAdmin($link, $applicantUsername, $ldapServer, $ldapPort))
                                {
                                   $response["detail"] = "Ok";
                                   $response["context"] = "View";
                                   $response["dashboardParams"] = getDashboardParams($link);
                                   $response["dashboardWidgets"] = getDashboardWidgets($link);
                                   $_SESSION["dashViewUsername" . $dashboardId] = $applicantUsername;
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
                                                 if(/*isToolAdmin($link, $applicantUsername, $ldapServer, $ldapPort)*/true)
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
                                                 if(isToolAdmin($link, $applicantUsername, $ldapServer, $ldapPort))
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
                                if(isToolAdmin($link, $applicantUsername, $ldapServer, $ldapPort))
                                {
                                   $response["detail"] = "Ok";
                                   $response["context"] = "View";
                                   $response["dashboardParams"] = getDashboardParams($link);
                                   $response["dashboardWidgets"] = getDashboardWidgets($link);
                                   $_SESSION["dashViewUsername" . $dashboardId] = $applicantUsername;
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
                                       $_SESSION["dashViewUsername" . $dashboardId] = $applicantUsername;
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
                         //Controllare credenziali in sessione per la view
                         if(isset($_SESSION["dashViewUsername" . $dashboardId]))
                         {
                             $applicantUsername = $_SESSION["dashViewUsername" . $dashboardId];
                             $proceed = true;
                         }
                         else 
                         {
                             $response["detail"] = "credentialsMissing";
                             $proceed = false;
                         }
                     }
                     else//Credenziali fornite
                     {
                         //Controllo presenza credenziali fornite su elenco utenti LDAP e locale
                         $applicantUsername = $_REQUEST['username'];
                         $applicantPassword = $_REQUEST['password'];
                         $applicantPasswordMd5 = md5($applicantPassword);

                         $query = "SELECT count(*) AS isRegistered FROM Dashboard.Users WHERE username = '$applicantUsername' AND password = '$applicantPasswordMd5'";
                         $result = mysqli_query($link, $query);
                         if($result)
                         {
                            $row = mysqli_fetch_array($result);
                            $tool = "Dashboard";

                            $ds2 = ldap_connect($ldapServer, $ldapPort);
                            ldap_set_option($ds2, LDAP_OPT_PROTOCOL_VERSION, 3);
                            $bind = ldap_bind($ds2);

                            $ldapusr = "cn=" . $applicantUsername . ",dc=ldap,dc=disit,dc=org";

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
                                        $_SESSION["dashViewUsername" . $dashboardId] = $applicantUsername;
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
                                            $_SESSION["dashViewUsername" . $dashboardId] = $applicantUsername;
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
                                                 $_SESSION["dashViewUsername" . $dashboardId] = $applicantUsername;
                                               }
                                               else
                                               {
                                                  //Controlliamo se è un tool admin
                                                  if(isToolAdmin($link, $applicantUsername, $ldapServer, $ldapPort))
                                                  {
                                                     $response["detail"] = "Ok";
                                                     $response["context"] = "View";
                                                     $response["dashboardParams"] = getDashboardParams($link);
                                                     $response["dashboardWidgets"] = getDashboardWidgets($link);
                                                     $_SESSION["dashViewUsername" . $dashboardId] = $applicantUsername;
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
                                        $_SESSION["dashViewUsername" . $dashboardId] = $applicantUsername;
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
                                           $_SESSION["dashViewUsername" . $dashboardId] = $applicantUsername;
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
                                                 if(isToolAdmin($link, $applicantUsername, $ldapServer, $ldapPort))
                                                 {
                                                    $response["detail"] = "Ok";
                                                    $response["context"] = "View";
                                                    $response["dashboardParams"] = getDashboardParams($link);
                                                    $response["dashboardWidgets"] = getDashboardWidgets($link);
                                                    $_SESSION["dashViewUsername" . $dashboardId] = $applicantUsername;
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
                                if(isToolAdmin($link, $applicantUsername, $ldapServer, $ldapPort))
                                {
                                   $response["detail"] = "Ok";
                                   $response["context"] = "View";
                                   $response["dashboardParams"] = getDashboardParams($link);
                                   $response["dashboardWidgets"] = getDashboardWidgets($link);
                                   $_SESSION["dashViewUsername" . $dashboardId] = $applicantUsername;
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
                                       $_SESSION["dashViewUsername" . $dashboardId] = $applicantUsername;
                                     }
                                     else
                                     {
                                       //Controlliamo se è un tool admin
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

        /*if(($response['visibility'] != "public")&&($response["detail"] == "Ok"))
        {
            $_SESSION["dashViewSessionEndTime" . $dashboardId] = time() + $sessionDuration;
        }*/

        echo json_encode($response);
        mysqli_close($link);
    
   
    

