<?php
    include '../config.php';
    
    $link = mysqli_connect($host, $username, $password);
    mysqli_select_db($link, $dbname);
    error_reporting(E_ERROR | E_NOTICE);
    $response = [];
    date_default_timezone_set('Europe/Rome');
    
    if(!$link->set_charset("utf8")) 
    {
        echo '<script type="text/javascript">';
        echo 'alert("Error loading character set utf8: %s\n");';
        echo '</script>';
        exit();
    }
    
    if(isset($_REQUEST['openDashboardToEdit']))
    {
        session_start();
        
        if(isset($_SESSION['loggedRole'])&&(isset($_SESSION['loggedUsername'])))
        {
            //IN CORSO - Caso entrata dal tool principale dopo login
            $isAdmin = $_SESSION['loggedRole'];
            $dashboardId = mysqli_real_escape_string($link, $_REQUEST['dashboardId']);

            //Reperimento da DB del dashboardId e dell'id dell'autore della dashboard
            $query = "SELECT * FROM Dashboard.Config_dashboard WHERE Id = $dashboardId";
            $result = mysqli_query($link, $query);

            if($result) 
            {
                if($result->num_rows > 0) 
                {
                    while($row = mysqli_fetch_array($result)) 
                    {
                        $dashboardTitle = $row['name_dashboard'];
                        $dashboardAuthorName = $row['user'];
                    }
                }
            } 
            mysqli_close($link);

            if(($_SESSION['loggedRole'] == "Manager")||($_SESSION['loggedRole'] == "AreaManager")||($_SESSION['loggedRole'] == "ToolAdmin"))
            {
                //Utente non amministratore, edita una dashboard solo se ne é l'autore
                if((isset($_SESSION['loggedUsername']))&&($_SESSION['loggedUsername'] == $dashboardAuthorName))
                {
                    //header("location: dashboard_configdash.php?dashboardId=" . $dashboardId);
                    $response['detail'] = 'Ok';
                    $response['dashboardAuthorName'] = $dashboardAuthorName;
                    $response['dashboardTitle'] = $dashboardTitle;
                }
                else
                {
                    $response['detail'] = 'unauthorized';
                }
            }
            else if($_SESSION['loggedRole'] == "RootAdmin")
            {
                //Utente amministratore, edita qualsiasi dashboard
                $response['detail'] = 'Ok';
                $response['dashboardAuthorName'] = $dashboardAuthorName;
                $response['dashboardTitle'] = $dashboardTitle;
            }
        }
        else
        {
            //TBD - Caso entrata da NodeRED link
            
        }
    } 
    else
    {
        $response['detail'] = 'missingParam';
    }
    
    echo json_encode($response);
    