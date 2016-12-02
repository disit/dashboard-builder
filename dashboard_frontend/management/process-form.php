<?php
/* Dashboard Builder.
   Copyright (C) 2016 DISIT Lab http://www.disit.org - University of Florence

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

session_start(); // Starting Session
$link = mysqli_connect($host, $username, $password) or die("failed to connect to server !!");
mysqli_select_db($link, $dbname);


if (isset($_REQUEST['register_confirm'])) {

    $username = $_POST['inputUsername'];
    $password = $_POST['inputPassword'];
    $firstname = $_POST['inputNameUser'];
    $lastname = $_POST['inputSurnameUser'];
    $email = $_POST['inputEmail'];
    if (isset($_POST['adminCheck'])) {
        //$valueAdmin = $_POST['checkAdmin'];
        $valueAdmin = 1;
    } else {
        $valueAdmin = 0;
    }
    $selqDbtbCheck = "SELECT * FROM `Dashboard`.`Users` WHERE username='$username'";
    $resultCheck = mysqli_query($link, $selqDbtbCheck) or die(mysqli_error($link));

    if (mysqli_num_rows($resultCheck) > 0) { //check if there is already an entry for that username
        mysqli_close($link);
        echo '<script type="text/javascript">';
        echo 'alert("Username già in uso da altro utente: Ripetere registrazione");';
        echo 'window.location.href = "dashboard_register.php";';
        echo '</script>';
    } else {
        //Inserting record in table using INSERT query
        //INSERIRE Nuovo utente con campo Admin

        $insqDbtb = "INSERT INTO `Dashboard`.`Users`
          (`IdUser`, `username`, `password`, `name`, `surname`,
          `email`, `reg_data`, `status`, `ret_code`,`admin`) VALUES (NULL, '$username',
          '$password', '$firstname', '$lastname', '$email', now(), 1, 1, '$valueAdmin')";

        $result = mysqli_query($link, $insqDbtb) or die(mysqli_error($link));
        if ($result) {
            mysqli_close($link);
            echo '<script type="text/javascript">';
            echo 'alert("Registrazione avvenuta con successo");';
            //echo 'window.location.href = "index.php";';
            echo 'window.location.href = "dashboard_mng.php";';
            echo '</script>';
        } else {
            mysqli_close($link);
            echo '<script type="text/javascript">';
            echo 'alert("Error: Ripetere registrazione");';
            echo 'window.location.href = "dashboard_register.php";';
            echo '</script>';
        }
    }
} else if (isset($_REQUEST['login'])) {

    $username = $_POST['loginUsername'];
    $password = $_POST['loginPassword'];


    $selqDbtb = "SELECT * FROM `Dashboard`.`Users` WHERE username='$username' and password='$password'";
    $result2 = mysqli_query($link, $selqDbtb);
    if ($result2 == false) {
        die(mysqli_error($link));
    }

    if (mysqli_num_rows($result2) > 0) {
        $_SESSION['login_user'] = $username;

        while ($row = $result2->fetch_assoc()) {
            $_SESSION['login_user_id'] = $row["IdUser"];
            //valore dell'utente amministratore
            $_SESSION['admin'] = $row["admin"];
            //
        }
        mysqli_close($link);
        /* echo '<script type="text/javascript">';
          echo 'alert("'.$id.'")';
          echo 'alert("Login avvenuto con successo");';
          echo 'window.location.href = "dashboard_mng.php";';
          echo '</script>'; */
        header("location: dashboard_mng.php");
    } else {
        mysqli_close($link);
        echo '<script type="text/javascript">';
        echo 'alert("Username e/o password errata/i: ripetere login");';
        echo 'window.location.href = "index.php";';
        echo '</script>';
    }
} 
else if(isset($_REQUEST['creation_dashboard'])) 
{
    $name_dashboard = $_POST['inputNameDashboard'];
    $title = $_POST['inputTitleDashboard'];
    $subtitle = $_POST['inputSubTitleDashboard'];
    $color = $_POST['inputColorDashboard'];
    $background = $_POST['inputColorBackgroundDashboard'];
    $externalColor = $_POST['inputExternalColorDashboard'];
    $nCols = $_POST['inputWidthDashboard'];
    $headerFontColor = $_POST['headerFontColor'];
    $headerFontSize = $_POST['headerFontSize'];
    $logoLink = null;
    
    if($headerFontSize > 45)
    {
        $headerFontSize = 45;
    }

    $user_id = $_SESSION['login_user_id'];

    /*Logo della dashboard*/
    $uploadFolder ="../img/dashLogos/".$name_dashboard."/";
    
    if(isset($_POST['creation_dashboard']) && $_FILES['dashboardLogoInput']['size'] > 0)
    {
        mkdir("../img/dashLogos/");
        mkdir($uploadFolder);
       
        if(!is_dir($uploadFolder))  
        {  
            echo '<script type="text/javascript">';
            echo 'alert("Directory dashLogos/"' . $name_dashboard . '"/ does not exist");';
            echo 'window.location.href = "dashboard_mng.php";';
            echo '</script>';  
        }   
        else   
        {  
            if(!is_writable($uploadFolder))
            {
                echo '<script type="text/javascript">';
                echo 'alert("Directory dashLogos is not writable");';
                echo 'window.location.href = "dashboard_mng.php";';
                echo '</script>';
            }
            else
            {
                $pointIndex = strrpos($_FILES['dashboardLogoInput']['name'], ".");
                $extension = substr($_FILES['dashboardLogoInput']['name'], $pointIndex);
                $filename = 'logo'.$extension;
                
                if(!move_uploaded_file($_FILES['dashboardLogoInput']['tmp_name'], $uploadFolder.$filename))  
                {  
                    echo '<script type="text/javascript">';
                    echo 'alert("Something has gone wrong during logo upload.");';
                    echo 'window.location.href = "dashboard_mng.php";';
                    echo '</script>'; 
                }  
                else  
                {  
                    $selqDbtbCheck2 = "SELECT * FROM Dashboard.Config_dashboard WHERE name_dashboard='$name_dashboard' AND user='$user_id'";
                    $resultCheck2 = mysqli_query($link, $selqDbtbCheck2) or die(mysqli_error($link));

                    if (mysqli_num_rows($resultCheck2) > 0) 
                    { 
                        mysqli_close($link);
                        echo '<script type="text/javascript">';
                        echo 'alert("Chosen dashboard name is already in use: please choose another one.");';
                        echo 'window.location.href = "dashboard_mng.php";';
                        echo '</script>';
                    }
                    else 
                    {
                        //New version: lasciamo gli addendi espliciti per agevolare la lettura
                        $width = ($nCols * 78) + 10;
                        
                        if($_POST['dashboardLogoLinkInput'] != '')
                        {
                            if(strpos($_POST['dashboardLogoLinkInput'], 'http://') === false) 
                            {
                                $logoLink = 'http://' . $_POST['dashboardLogoLinkInput'];
                            }
                            else 
                            {
                                $logoLink = $_POST['dashboardLogoLinkInput'];
                            }
                            
                            $insqDbtb2 = "INSERT INTO `Dashboard`.`Config_dashboard`
                            (`Id`, `name_dashboard`, `title_header`, `subtitle_header`, `color_header`,
                            `width`, `height`, `num_rows`, `num_columns`, `user`, `status_dashboard`, `creation_date`, `remains_width`, `remains_height`,`color_background`,`external_frame_color`, `headerFontColor`, `headerFontSize`, `logoFilename`, `logoLink`) 
                            VALUES (NULL, '$name_dashboard', '$title', '$subtitle', '$color', $width, 0, 0, $nCols, '$user_id', 1, now(), NULL, NULL,'$background', '$externalColor', '$headerFontColor', $headerFontSize, '$filename', '$logoLink')";
                        }
                        else
                        {
                            $insqDbtb2 = "INSERT INTO `Dashboard`.`Config_dashboard`
                            (`Id`, `name_dashboard`, `title_header`, `subtitle_header`, `color_header`,
                            `width`, `height`, `num_rows`, `num_columns`, `user`, `status_dashboard`, `creation_date`, `remains_width`, `remains_height`,`color_background`,`external_frame_color`, `headerFontColor`, `headerFontSize`, `logoFilename`) 
                            VALUES (NULL, '$name_dashboard', '$title', '$subtitle', '$color', $width, 0, 0, $nCols, '$user_id', 1, now(), NULL, NULL,'$background', '$externalColor', '$headerFontColor', $headerFontSize, '$filename')";
                        }
                        
                        $result3 = mysqli_query($link, $insqDbtb2) or die(mysqli_error($link));
                        if ($result3) {
                            $_SESSION['id_dashboard'] = mysqli_insert_id($link);
                            mysqli_close($link);
                            header("location: dashboard_configdash.php");
                        } else {
                            mysqli_close($link);
                            echo '<script type="text/javascript">';
                            echo 'alert("Error during dashboard creation: please repeat the procedure.");';
                            echo 'window.location.href = "dashboard_mng.php";';
                            echo '</script>';
                        }
                    }
                }
            }
        } 
    }
    else
    {
        //Nessun file caricato
        $selqDbtbCheck2 = "SELECT * FROM Dashboard.Config_dashboard WHERE name_dashboard='$name_dashboard' AND user='$user_id'";
        $resultCheck2 = mysqli_query($link, $selqDbtbCheck2) or die(mysqli_error($link));

        if (mysqli_num_rows($resultCheck2) > 0) 
        { 
            mysqli_close($link);
            echo '<script type="text/javascript">';
            echo 'alert("Il nome dato alla nuova dashboard è già in uso: ripetere creazione dashboard");';
            echo 'window.location.href = "dashboard_mng.php";';
            echo '</script>';
        }
        else 
        {
            //New version: lasciamo gli addendi espliciti per agevolare la lettura
            $width = ($nCols * 78) + 10;

            $insqDbtb2 = "INSERT INTO `Dashboard`.`Config_dashboard`
            (`Id`, `name_dashboard`, `title_header`, `subtitle_header`, `color_header`,
            `width`, `height`, `num_rows`, `num_columns`, `user`, `status_dashboard`, `creation_date`, `remains_width`, `remains_height`,`color_background`,`external_frame_color`, `headerFontColor`, `headerFontSize`) 
            VALUES (NULL, '$name_dashboard', '$title', '$subtitle', '$color', $width, 0, 0, $nCols, '$user_id', 1, now(), NULL, 0,'$background', '$externalColor', '$headerFontColor', $headerFontSize)";
            $result3 = mysqli_query($link, $insqDbtb2) or die(mysqli_error($link));
            if ($result3) {
                $_SESSION['id_dashboard'] = mysqli_insert_id($link);
                mysqli_close($link);
                header("location: dashboard_configdash.php");
            } else {
                mysqli_close($link);
                echo '<script type="text/javascript">';
                echo 'alert("Error: Ripetere creazione dashboard");';
                echo 'window.location.href = "dashboard_mng.php";';
                echo '</script>';
            }
        }
    }
} 
else if (isset($_REQUEST['add_widget'])) {
    if (isset($_POST['textarea-selected-metrics']) && $_POST['textarea-selected-metrics'] != "") {
        $id_dashboard = $_SESSION['id_dashboard'];
        $selqDbtbMaxSel1 = "SELECT MAX(n_row) AS MaxNrow, MAX(size_columns) AS MaxSize FROM Dashboard.Config_widget_dashboard WHERE id_dashboard='$id_dashboard'";
        $resultMaxSel1 = mysqli_query($link, $selqDbtbMaxSel1) or die(mysqli_error($link));

        if($resultMaxSel1) 
        {
            $nextId = 1;
            $nextRow = 1;
            if($resultMaxSel1->num_rows > 0) 
            {
                while ($rowMaxSel1 = mysqli_fetch_array($resultMaxSel1)) 
                {
                    if ((!is_null($rowMaxSel1['MaxNrow'])) && (!empty($rowMaxSel1['MaxNrow']))) 
                    {
                        $nextRow = $rowMaxSel1['MaxNrow'] + $rowMaxSel1['MaxSize'];
                    }
                }
                $selqDbtbMaxSel2 = "SELECT MAX(Id) AS MaxId FROM Dashboard.Config_widget_dashboard";
                $resultMaxSel2 = mysqli_query($link, $selqDbtbMaxSel2) or die(mysqli_error($link));
                if($resultMaxSel2) 
                {
                    while($rowMaxSel2 = mysqli_fetch_array($resultMaxSel2)) 
                    {
                        if ((!is_null($rowMaxSel2['MaxId'])) && (!empty($rowMaxSel2['MaxId']))) 
                        {
                            $nextId = $rowMaxSel2['MaxId'] + 1;
                        }
                    }
                    
                    $id_metric = $_POST['textarea-selected-metrics'];
                    $type_widget = $_POST['select-widget'];
                    $title_widget = NULL;
                    $color_widget = $_POST['inputColorWidget'];
                    $freq_widget = NULL;
                    $sizeRowsWidget = $_POST['inputSizeRowsWidget'];
                    $sizeColumnsWidget = $_POST['inputSizeColumnsWidget'];

                    //Aggiunta del campo della tabella "config_widget_dashboard" per i messaggi informativi
                    $message_widget = $_POST['textarea-information-metrics'];
                    //colore della finestra
                    $frame_color = NULL;

                    if(isset($_POST['inputTitleWidget'])&&($_POST['inputTitleWidget']!=""))
                    {
                        $title_widget = $_POST['inputTitleWidget'];
                    }
                    
                    if(isset($_POST['inputFreqWidget'])&&($_POST['inputFreqWidget']!=""))
                    {
                        $freq_widget = $_POST['inputFreqWidget'];
                    }
                    
                    if(isset($_POST['inputFrameColorWidget'])&&($_POST['inputFrameColorWidget']!=""))
                    {
                        $frame_color = $_POST['inputFrameColorWidget'];
                    }
                    
                    //Parametri
                    $parameters = NULL;
                    if (isset($_POST['textarea-range-value'])&&($_POST['textarea-range-value']!=""))
                    {
                        $parameters= $_POST['textarea-range-value'];
                    }
                    /*else if($_POST['textarea-range-value'] == "")
                    {
                        $parameters = 'NULL';
                    }*/

                    //Gestione parametri per widget di stato del singolo processo
                    if($id_metric == 'Process')
                    {
                        $host = $_POST['host'];
                        $user = $_POST['user'];
                        $pass = $_POST['pass'];
                        $schedulerName = $_POST['schedulerName'];
                        $jobArea = $_POST['jobArea'];
                        $jobGroup = $_POST['jobGroup'];
                        $jobName = $_POST['jobName'];
                        $parametersArray = array('host' => $host, 'user' => $user, 'pass' => $pass, 'schedulerName' => $schedulerName, 'jobArea' => $jobArea, 'jobGroup' => $jobGroup, 'jobName' => $jobName);
                        $parameters = json_encode($parametersArray);
                    }

                    if(isset($_POST['inputUrlWidget'])&& ($_POST['inputUrlWidget'] != ""))
                    {
                        if (strpos($_POST['inputUrlWidget'], 'http://') === false) 
                        {
                            $url_widget = 'http://' . $_POST['inputUrlWidget'];
                        }
                        else 
                        {
                            $url_widget = $_POST['inputUrlWidget'];
                        }
                    }
                    else
                    {
                        $url_widget = NULL;
                    }

                    $comune_widget = NULL;
                    if (isset($_POST['inputComuneWidget']) && $_POST['inputComuneWidget'] != "") 
                    {
                        $comune_widget = strtoupper($_POST['inputComuneWidget']);
                        $name_widget = preg_replace('/\+/', '', $id_metric) . "_" . $comune_widget . "_" . $id_dashboard . "_" . $type_widget . $nextId;
                    } 
                    else  
                    {
                        $name_widget = preg_replace('/\+/', '', $id_metric) . "_" . $id_dashboard . "_" . $type_widget . $nextId;
                    }

                    $int_temp_widget = NULL;
                    if (isset($_POST['select-IntTemp-Widget']) && $_POST['select-IntTemp-Widget'] != "") {
                        $int_temp_widget = $_POST['select-IntTemp-Widget'];

                        if ($_POST['select-IntTemp-Widget'] != "Nessuno") {
                            $name_widget = $name_widget . "_" . preg_replace('/ /', '', $_POST['select-IntTemp-Widget']);
                        }
                    }

                    $inputUdmWidget = NULL;
                    if(isset($_POST['inputUdmWidget']) && ($_POST['inputUdmWidget'] != "")) 
                    {
                        $inputUdmWidget = $_POST['inputUdmWidget'];
                    }
                    
                    $fontSize = NULL;
                    if(isset($_POST['inputFontSize']) && ($_POST['inputFontSize'] != '') && (!empty($_POST['inputFontSize']))) 
                    {
                        $fontSize = $_POST['inputFontSize'];
                    }
                    
                    $fontColor = NULL;
                    if(isset($_POST['inputFontColor']) && ($_POST['inputFontColor'] != '') && (!empty($_POST['inputFontColor']))) 
                    {
                        $fontColor = $_POST['inputFontColor'];
                    }

                    //Inserting record in table using INSERT query
                    /*$insqDbtb3 = "INSERT INTO Dashboard.Config_widget_dashboard (Id, name_w, id_dashboard, id_metric, type_w, n_row, n_column, size_rows, size_columns, title_w, color_w, frequency_w, temporal_range_w, municipality_w, infoMessage_w, link_w, parameters, frame_color_w, udm, fontSize, fontColor)"
                               . "VALUES ('$nextId', '$name_widget', '$id_dashboard', '$id_metric', '$type_widget', $nextRow, 1, '$sizeRowsWidget', '$sizeColumnsWidget', '$title_widget','$color_widget', '$freq_widget', '$int_temp_widget', '$comune_widget','$message_widget','$url_widget','$parameters','$frame_color', '$inputUdmWidget', $fontSize, '$fontColor')";
                    $result4 = mysqli_query($link, $insqDbtb3) or die(mysqli_error($link));*/
                    
                    $nCol = 1;
                    $insqDbtb3 = $link->prepare("INSERT INTO Dashboard.Config_widget_dashboard (Id, name_w, id_dashboard, id_metric, type_w, n_row, n_column, size_rows, size_columns, title_w, color_w, frequency_w, temporal_range_w, municipality_w, infoMessage_w, link_w, parameters, frame_color_w, udm, fontSize, fontColor) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                    $insqDbtb3->bind_param('isissiiiissssssssssis', $nextId, $name_widget, $id_dashboard, $id_metric, $type_widget, $nextRow, $nCol, $sizeRowsWidget, $sizeColumnsWidget, $title_widget, $color_widget, $freq_widget, $int_temp_widget, $comune_widget, $message_widget, $url_widget, $parameters, $frame_color, $inputUdmWidget, $fontSize, $fontColor);
                    $result4 = $insqDbtb3->execute();
                    
                    if ($result4) 
                    {
                        mysqli_close($link);
                        header("location: dashboard_configdash.php");
                    } 
                    else 
                    {
                        mysqli_close($link);
                        echo '<script type="text/javascript">';
                        echo 'alert("Error: Ripetere inserimento widget");';
                        echo 'window.location.href = "dashboard_configdash.php";';
                        echo '</script>';
                    }
                }
                else 
                {
                    mysqli_close($link);
                    echo '<script type="text/javascript">';
                    echo 'alert("Error: Ripetere inserimento widget");';
                    echo 'window.location.href = "dashboard_configdash.php";';
                    echo '</script>';
                }
            }
        } 
        else 
        {
            mysqli_close($link);
            echo '<script type="text/javascript">';
            echo 'alert("Error: Ripetere inserimento widget");';
            echo 'window.location.href = "dashboard_configdash.php";';
            echo '</script>';
        }
    } else {
        echo '<script type="text/javascript">';
        echo 'alert("Error: Nessuna metrica selezionata - ripetere inserimento widget");';
        echo 'window.location.href = "dashboard_configdash.php";';
        echo '</script>';
    }
} 
else if (isset($_REQUEST['modify_dashboard'])) 
{
    $user_id2 = $_POST['select-user'];
    $name_dashboard_select2 = $_POST['select-dashboard2'];
    //ricerca dell'utente
    $cercaUtente = "SELECT * FROM Dashboard.Users WHERE username='$user_id2'";
    $resultUtente = mysqli_query($link, $cercaUtente) or die(mysqli_error($link));
    if ($resultUtente) 
    {
        if ($resultUtente->num_rows > 0) 
        {
            while ($rowUtente = mysqli_fetch_array($resultUtente))
            {
                $creatore = $rowUtente['IdUser'];
            }
        }
    }
    $selqDbtb2 = "SELECT * FROM Dashboard.Config_dashboard WHERE name_dashboard='$name_dashboard_select2' and user='$creatore'";
    $result5 = mysqli_query($link, $selqDbtb2) or die(mysqli_error($link));

    if ($result5) 
    {
        if ($result5->num_rows > 0) 
        {
            while ($row2 = mysqli_fetch_array($result5)) 
            {
                $_SESSION['id_dashboard'] = $row2['Id'];
            }
        }

        mysqli_close($link);
        header("location: dashboard_configdash.php");
    } 
    else 
    {
        mysqli_close($link);
        echo '<script type="text/javascript">';
        echo 'alert("Error: Ripetere inserimento widget");';
        echo 'window.location.href = "dashboard_mng.php";';
        echo '</script>';
    }
} 
else if (isset($_REQUEST['disable_dashboard'])) {
    $user_id = $_SESSION['login_user_id'];
    $name_dashboard_select = $_POST['select-dashboard-disable'];
    $new_status_dashboard = 0;

    //$updqDbtb2 = "UPDATE Dashboard.Config_dashboard SET status_dashboard = '$new_status_dashboard' WHERE name_dashboard='$name_dashboard_select' ";
    $updqDbtb2 = "UPDATE Dashboard.Config_dashboard SET status_dashboard = '$new_status_dashboard' WHERE name_dashboard='$name_dashboard_select' and user='$user_id'";
    $result6 = mysqli_query($link, $updqDbtb2) or die(mysqli_error($link));

    if ($result6) {
        mysqli_close($link);
        echo '<script type="text/javascript">';
        echo 'alert("Disabilitazione dashboard avvenuta con successo");';
        echo 'window.location.href = "dashboard_mng.php";';
        echo '</script>';
    } else {
        mysqli_close($link);
        echo '<script type="text/javascript">';
        echo 'alert("Error: Ripetere disabilitazione dashboard");';
        echo 'window.location.href = "dashboard_mng.php";';
        echo '</script>';
    }
} else if (isset($_REQUEST['modify_widget'])) {

    $type_widget_m = $_POST['select-widget-m'];
    $title_widget_m = NULL;
    $color_widget_m = $_POST['inputColorWidgetM'];
    $freq_widget_m = NULL;
    $name_widget_m = $_POST['inputNameWidgetM'];
    $info_m = $_POST['textareaInfoWidgetM'];
    $url_m = $_POST['urlWidgetM'];
    $col_m = $_POST['inputColumn-m'];
    $row_m = $_POST['inputRows-m'];
    $color_frame_m = NULL;
    
    if(isset($_POST['inputTitleWidgetM']) && ($_POST['inputTitleWidgetM']!=""))
    {
        $title_widget_m = $_POST['inputTitleWidgetM'];
    }
    
    if(isset($_POST['inputFreqWidgetM']) && ($_POST['inputFreqWidgetM']!=""))
    {
        $freq_widget_m = $_POST['inputFreqWidgetM'];
    }
    
    if(isset($_POST['select-frameColor-Widget-m']) && ($_POST['select-frameColor-Widget-m']!=""))
    {
        $color_frame_m = $_POST['select-frameColor-Widget-m'];
    }
    
    if (isset($_POST['textarea-range-value_m']) && $_POST['textarea-range-value_m']!="")
    {
        $parametersM = $_POST['textarea-range-value_m'];
    }
    else
    {
        $parametersM = NULL;  
    }
    
    if(isset($_POST['inputFontSizeM']) && ($_POST['inputFontSizeM']!=""))
    {
        $fontSizeM = $_POST['inputFontSizeM'];
    }
    else
    {
        $fontSizeM = NULL;  
    }
    
    if(isset($_POST['inputFontColorM']) && ($_POST['inputFontColorM']!=""))
    {
        $fontColorM = $_POST['inputFontColorM'];
    }
    else
    {
        $fontColorM = NULL;  
    }
    
    //Gestione parametri per widget di stato del singolo processo
    if($type_widget_m == 'widgetProcess')
    {
	$hostM = $_POST['hostM'];
	$userM = $_POST['userM'];
	$passM = $_POST['passM'];
	$schedulerNameM = $_POST['schedulerNameM'];
	$jobAreaM = $_POST['jobAreaM'];
	$jobGroupM = $_POST['jobGroupM'];
	$jobNameM = $_POST['jobNameM'];
	$parametersArrayM = array('host' => $hostM, 'user' => $userM, 'pass' => $passM, 'schedulerName' => $schedulerNameM, 'jobArea' => $jobAreaM, 'jobGroup' => $jobGroupM, 'jobName' => $jobNameM);
	$parametersM = json_encode($parametersArrayM);
    }
    
    $id_dashboard2 = $_SESSION['id_dashboard'];

    if(isset($_POST['select-IntTemp-Widget-m']) && ($_POST['select-IntTemp-Widget-m'] != "") &&($type_widget_m != 'widgetProtezioneCivile')) 
    {
        $int_temp_widget_m = $_POST['select-IntTemp-Widget-m'];
    }
    else
    {
        $int_temp_widget_m = NULL;
    }

    if (isset($_POST['inputComuneWidgetM']) && ($_POST['inputComuneWidgetM'] != "") &&($type_widget_m != 'widgetProtezioneCivile')) 
    {
        $comune_widget_m = $_POST['inputComuneWidgetM'];
    }
    else 
    {
        $comune_widget_m = NULL;
    }
    
    if(isset($_POST['urlWidgetM'])&& ($_POST['urlWidgetM'] != ""))
    {
        if (strpos($_POST['urlWidgetM'], 'http://') === false) 
        {
            $url_m = 'http://' . $_POST['urlWidgetM'];
        }
        else 
        {
            $url_m = $_POST['urlWidgetM'];
        }
    }
    else
    {
        $url_m = NULL;
    }
    
    $inputUdmWidget = NULL;
    if(isset($_POST['inputUdmM']) && $_POST['inputUdmM'] != "") 
    {
        $inputUdmWidget = $_POST['inputUdmM'];
    }

    //Inserting record in table using INSERT query
    /*$upsqDbtb = "UPDATE Dashboard.Config_widget_dashboard SET type_w = '$type_widget_m',size_columns='$col_m',size_rows='$row_m', title_w = '$title_widget_m', color_w = '$color_widget_m', frequency_w = '$freq_widget_m', temporal_range_w = '$int_temp_widget_m', municipality_w = '$comune_widget_m', infoMessage_w='$info_m', link_w='$url_m', parameters='$parametersM', frame_color_w='$color_frame_m', udm='$inputUdmWidget', fontSize=$fontSizeM, fontColor='$fontColorM' WHERE name_w='$name_widget_m' AND id_dashboard='$id_dashboard2'";
    $result7 = mysqli_query($link, $upsqDbtb) or die(mysqli_error($link));*/
    
    $upsqDbtb = $link->prepare("UPDATE Dashboard.Config_widget_dashboard SET type_w = ?, size_columns = ?, size_rows = ?, title_w = ?, color_w = ?, frequency_w = ?, temporal_range_w = ?, municipality_w = ?, infoMessage_w = ?, link_w = ?, parameters = ?, frame_color_w = ?, udm = ?, fontSize = ?, fontColor=? WHERE name_w = ? AND id_dashboard = ?");
    $upsqDbtb->bind_param('siissssssssssissi', $type_widget_m, $col_m, $row_m, $title_widget_m, $color_widget_m, $freq_widget_m, $int_temp_widget_m, $comune_widget_m, $info_m, $url_m, $parametersM, $color_frame_m, $inputUdmWidget, $fontSizeM, $fontColorM, $name_widget_m, $id_dashboard2);
    $result7 = $upsqDbtb->execute();
    
    if ($result7) 
    {
        mysqli_close($link);
        header("location: dashboard_configdash.php");
    } 
    else 
    {
        mysqli_close($link);
        echo '<script type="text/javascript">';
        echo 'alert("Error: Ripetere modifica widget");';
        echo 'window.location.href = "dashboard_configdash.php";';
        echo '</script>';
    }
} else if (isset($_REQUEST['add_new_metric'])) {
    //creazione di una nuova metrica 
    //Valori di Default
    $valueThreshold = 'null';
    $valueThresholdEvalCount = 'null';
    $valueTresholdTime = 'null';
    //Inserisci una nuova metrica nel database

    echo 'passaggio dei parametri avvenuto!';
    $valueIdMetric = $_POST['nameMetric'];
    //$valueDescription = utf8_encode($_POST['descriptionMetric']);
    $valueDescription = $_POST['descriptionMetric'];
    //manipolazione descrizione
    //inserimento Query inizio
    if (isset($_POST['queryMetric'])) {
        $query_composta = $_POST['queryMetric'];
        $valueQuery = $_POST['queryMetric'];
    }
    //$valueQuery = utf8_encode($_POST['queryMetric']);
    if (isset($_POST['queryMetric2']) && $_POST['queryMetric2'] != NULL) {
        $valueQuery2 = $_POST['queryMetric2'];
        $query_composta = $valueQuery . "|" . $valueQuery2;
    }

    //inserimento query fine
    $valueQueryType = $_POST['queryTypeMetric'];
    $valueMetricType = $_POST['typeMetric'];
    $valueFrequency = $_POST['frequencyMetric'];
    $valueProcessType = $_POST['processTypeMetric'];
    $valueArea = $_POST['areaMetric'];
    $valueSource = $_POST['sourceMetric'];
    $valueDescriptionShort = $_POST['descriptionShortMetric'];
    if (isset($_POST['dataSourceMetric'])) {
        $dataSourceComposto = $_POST['dataSourceMetric'];
        $valueDataSource = $_POST['dataSourceMetric'];
    }
    if (isset($_POST['dataSourceMetric2']) && $_POST['dataSourceMetric2'] != NULL) {
        $valueDataSource2 = $_POST['dataSourceMetric2'];
        $dataSourceComposto = $valueDataSource . "|" . $valueDataSource2;
    }
    //doppio datasources
    $valueDataSource = $_POST['dataSourceMetric'];
    //fine doppia datasources
    if ($_POST['thresholdMetric'] != '') {
        $valueThreshold = $_POST['thresholdMetric'];
    } else {
        $valueThreshold = 'null';
    }
    $valueThresholdEval = $_POST['thresholdEvalMetric'];
    //$valueThresholdEvalCount = utf8_encode($_POST['thresholdEvalCountMetric']);
    if ($_POST['thresholdEvalCountMetric'] != '') {
        $valueThresholdEvalCount = $_POST['thresholdEvalCountMetric'];
    } else {
        $valueThresholdEvalCount = 'null';
    }
    if ($_POST['thresholdTimeMetric'] != '') {
        $valueTresholdTime = $_POST['thresholdTimeMetric'];
    } else {
        $valueTresholdTime = 'null';
    }
    //$valueTresholdTime = utf8_encode($_POST['thresholdTimeMetric']);
    if (isset($_POST['storingDataMetric'])) {
        $valueStoringData = 1;
    } else {
        $valueStoringData = 0;
    }
    if (isset($_POST['contextMetric'])) {
        $valueMunicipalityOption = 1;
    } else {
        $valueMunicipalityOption = 0;
    }
    if (isset($_POST['timeRangeMetric'])) {
        $valueTimeRangeOption = 1;
    } else {
        $valueTimeRangeOption = 0;
    }

    $insqDbtb6 = "INSERT INTO Dashboard.Descriptions(IdMetric, description, status, query, query2, queryType, metricType, frequency, processType, area, source, description_short , dataSource, threshold, thresholdEval, thresholdEvalCount, thresholdTime, storingData, municipalityOption, timeRangeOption) 
    VALUES ('$valueIdMetric',\"" . $valueDescription . "\",'Attivo',\"" . $query_composta . "\",'','$valueQueryType','$valueMetricType','$valueFrequency','$valueProcessType','$valueArea','$valueSource','$valueDescriptionShort','$dataSourceComposto', $valueThreshold , '$valueThresholdEval', $valueThresholdEvalCount,$valueTresholdTime,'$valueStoringData','$valueMunicipalityOption','$valueTimeRangeOption')";
    echo $insqDbtb6;
    $result8 = mysqli_query($link, $insqDbtb6) or die(mysqli_error($link));


    $file = "querylog.txt";
    file_put_contents($file, $result8);
    if ($result8) {
        mysqli_close($link);
        header("location: metrics_mng.php");
    } else {
        mysqli_close($link);
        echo '<script type="text/javascript">';
        echo 'alert("Error during metric creation");';
        echo 'window.location.href = "metrics_mng.php";';
        echo '</script>';
    }
    //fine aggiunta metrica
} else if (isset($_REQUEST['modify_metric'])) {
    echo 'modifica della metrica avvenuta!';

    //$valueIdMetric_M = 'a2';
    //post dei dati da metric_mng;
    $valueIdMetric_M = $_POST['modify-nameMetric'];
    $valueDescription_M = htmlspecialchars($_POST['modify-descriptionMetric'], ENT_QUOTES);
    //querycomposte
    if (isset($_POST['modify-queryMetric'])) {
        $queryComposta_M = mysql_real_escape_string($_POST['modify-queryMetric']);
        $valueQuery_M = mysql_real_escape_string($_POST['modify-queryMetric']);
    }
    if (isset($_POST['modify-queryMetric2']) && $_POST['modify-queryMetric2'] != NULL) {
        $valueQuery2_M = mysql_real_escape_string($_POST['modify-queryMetric2']);
        $queryComposta_M = mysql_real_escape_string($valueQuery_M . "|" . $valueQuery2_M);
    }
    //$valueQuery2_M = utf8_encode($_POST['modify-queryMetric2']);
    $valueQueryType_M = $_POST['modify-queryTypeMetric'];
    echo $valueQuery_M;
    //$valueMetricType_M =utf8_encode($_POST['modify-metricTypeMetric']);
    $valueFrequency_M = $_POST['modify-frequencyMetric'];
    $valueProcessType_M = $_POST['modify-processTypeMetric'];
    $valueArea_M = $_POST['modify-areaMetric'];
    $valueSource_M = $_POST['modify-sourceMetric'];
    $valueDescriptionShort_M = $_POST['modify-descriptionShortMetric'];
    if (isset($_POST['modify-dataSourceMetric'])) {
        $valueDataSource_M = $_POST['modify-dataSourceMetric'];
        $datasourceComposto_M = $_POST['modify-dataSourceMetric'];
    }
    if (isset($_POST['modify-datasourceMetric2']) && $_POST['modify-datasourceMetric2'] != NULL) {
        $valueDataSource2_M = $_POST['modify-datasourceMetric2'];
        $datasourceComposto_M = $valueDataSource_M . "|" . $valueDataSource2_M;
    }
    if ($_POST['modify-thresholdMetric'] != '') {
        $valueThreshold_M = $_POST['modify-thresholdMetric'];
    } else {
        $valueThreshold_M = 'null';
    }
    $valueThresholdEval_M = $_POST['modify-thresholdEvalMetric'];
    if ($_POST['modify-thresholdEvalCountMetric'] != '') {
        $valueThresholdEvalCount_M = $_POST['modify-thresholdEvalCountMetric'];
    } else {
        $valueThresholdEvalCount_M = 'null';
    }


    if ($_POST['modify-thresholdTime'] != '') {
        $valueTresholdTime_M = $_POST['modify-thresholdTime'];
    } else {
        $valueTresholdTime_M = 'null';
    }
    //checkbox
    if (isset($_POST['modify-storingDataMetric'])) {
        $valueStoringData_M = 1;
    } else {
        $valueStoringData_M = 0;
    }
    if (isset($_POST['modify-contextMetric'])) {
        $valueMunicipalityOption_M = 1;
    } else {
        $valueMunicipalityOption_M = 0;
    }
    if (isset($_POST['modify-timeRangeMetric'])) {
        $valueTimeRangeOption_M = 1;
    } else {
        $valueTimeRangeOption_M = 0;
    }
    //$valueStoringData_M =utf8_encode($_POST['modify-storingDataMetric']);
    //$valueMunicipalityOption_M =utf8_encode($_POST['modify-contextMetric']);
    //$valueTimeRangeOption_M =utf8_encode($_POST['modify-timeRangeMetric']);
    // metricType='$valueMetricType_M',
    $updqDbtbY = "UPDATE Dashboard.Descriptions SET description = \"" . $valueDescription_M . "\", query=\"" . $queryComposta_M . "\", query2='', queryType='$valueQueryType_M', frequency='$valueFrequency_M', processType='$valueProcessType_M', area='$valueArea_M', source='$valueSource_M', description_short='$valueDescriptionShort_M', dataSource='$datasourceComposto_M', threshold=$valueThreshold_M, thresholdEval='$valueThresholdEval_M', thresholdEvalCount=$valueThresholdEvalCount_M, thresholdTime=$valueTresholdTime_M, storingData=$valueStoringData_M, municipalityOption=$valueMunicipalityOption_M, timeRangeOption=$valueTimeRangeOption_M WHERE IdMetric='$valueIdMetric_M' ";
    //echo ($updqDbtbY);
    $resultY = mysqli_query($link, $updqDbtbY) or die(mysqli_error($link));
    if ($resultY) {
        mysqli_close($link);
        header("location: metrics_mng.php");
    } else {
        mysqli_close($link);
        echo '<script type="text/javascript">';
        echo 'alert("Errore during metric modify");';
        echo 'window.location.href = "metrics_mng.php";';
        echo '</script>';
    }
} else if (isset($_REQUEST['delete_metric'])) {
    echo 'eliminazione della metrica avvenuta';
    //$name_metric_select = $_POST[''];
    $name_metric_select = $_POST['delete_metric'];
    $delqDbtbZ = "DELETE FROM Dashboard.Descriptions WHERE IdMetric='$name_metric_select'";
    $resultZ = mysqli_query($link, $delqDbtbZ) or die(mysqli_error($link));
    if ($resultZ) {
        mysqli_close($link);
        header("location: metrics_mng.php");
    } else {
        mysqli_close($link);
        echo '<script type="text/javascript">';
        echo 'alert("Error during metric delete");';
        echo 'window.location.href = "metrics_mng.php";';
        echo '</script>';
    }
} else if (isset($_REQUEST['modify-status'])) {
    echo 'modifica status della metrica';
    //$valore_metrica = $_POST['query_selezionata'];
    $name_metric_selected = $_POST['modify-status'];
    $query_select_status = "SELECT Descriptions.status FROM Dashboard.Descriptions WHERE Descriptions.IdMetric='$name_metric_selected'";
    $valore = mysqli_query($link, $query_select_status) or die(mysqli_error($link));
    //$valore1= $valore[0];

    if ($valore->num_rows > 0) {
        //$valore1= $valore[0];
        while ($rowStatus = mysqli_fetch_array($valore)) {
            echo ('Il valore è: ' + $valore);
            if ($rowStatus[0] == 'Non Attivo') {
                $updqDbtbStatus = "UPDATE Dashboard.Descriptions SET Descriptions.status='Attivo' WHERE Descriptions.IdMetric='$name_metric_selected'";
            } else if ($rowStatus[0] == 'Attivo') {
                $updqDbtbStatus = "UPDATE Dashboard.Descriptions SET Descriptions.status='Non Attivo' WHERE Descriptions.IdMetric='$name_metric_selected'";
            }
            $resultStatus = mysqli_query($link, $updqDbtbStatus) or die(mysqli_error($link));
            if ($resultStatus) {
                mysqli_close($link);
                header("location: metrics_mng.php");
            } else {
                mysqli_close($link);
                echo '<script type="text/javascript">';
                echo 'alert("Error during status metric modify");';
                echo 'window.location.href = "metrics_mng.php";';
                echo '</script>';
            }
            //fine modifca dello status
        }
    }
} else if (isset($_REQUEST['modify_status_dashboard'])) {
    echo 'modifica status delle dashboard';
    //$valore_metrica = $_POST['query_selezionata'];
    $name_dashboard_selected = $_POST['select-dashboard-status-name'];
    $user_dashboard_selected = $_POST['select-dashboard-status-user'];

    $cercaUtente2 = "SELECT * FROM Dashboard.Users WHERE username='$user_dashboard_selected'";
    $resultUtente2 = mysqli_query($link, $cercaUtente2) or die(mysqli_error($link));
    if ($resultUtente2) {
        if ($resultUtente2->num_rows > 0) {
            while ($rowUtente2 = mysqli_fetch_array($resultUtente2)) {
                $creatore2 = $rowUtente2['IdUser'];
            }
        }
    }


    $query_select_status_dashboard = "SELECT Config_dashboard.status_dashboard FROM Dashboard.Config_dashboard WHERE Config_dashboard.name_dashboard='$name_dashboard_selected' AND Config_dashboard.user='$creatore2'";
    $valoreStatusDash = mysqli_query($link, $query_select_status_dashboard) or die(mysqli_error($link));
    //$valore1= $valore[0];

    if ($valoreStatusDash->num_rows > 0) {
        //$valore1= $valore[0];
        while ($rowStatusDash = mysqli_fetch_array($valoreStatusDash)) {
            echo ('Il valore è: ' + $valoreStatusDash);
            if ($rowStatusDash[0] == '0') {
                $updqDbtbStatusDash = "UPDATE Dashboard.Config_dashboard SET Config_dashboard.status_dashboard='1' WHERE Config_dashboard.name_dashboard='$name_dashboard_selected' AND Config_dashboard.user='$creatore2'";
            } else if ($rowStatusDash[0] == '1') {
                $updqDbtbStatusDash = "UPDATE Dashboard.Config_dashboard SET Config_dashboard.status_dashboard='0' WHERE Config_dashboard.name_dashboard='$name_dashboard_selected' AND Config_dashboard.user='$creatore2'";
            }
            $resultStatusDash = mysqli_query($link, $updqDbtbStatusDash) or die(mysqli_error($link));
            if ($resultStatusDash) {
                mysqli_close($link);
                header("location: dashboard_mng.php");
            } else {
                mysqli_close($link);
                echo '<script type="text/javascript">';
                echo 'alert("Error during status metric modify");';
                echo 'window.location.href = "dashboard_mng.php";';
                echo '</script>';
            }
            //fine modifca dello status
        }
    }
} else if (isset($_REQUEST['create_dataSources'])) {
    $id_ds = $_POST['name_Id_dataSource'];
    $url_ds = $_POST['url_dataSource'];
    $dataBase_ds = $_POST['database_dataSource'];
    $user_ds = $_POST['username_dataSource'];
    $pass_ds = $_POST['password_dataSource'];
    $dataType_ds = $_POST['databaseType_dataSource'];

    echo $id_ds . '<br>';
    echo $url_ds . '<br>';
    echo $dataBase_ds . '<br>';
    echo $user_ds . '<br>';
    echo $pass_ds . '<br>';
    echo $dataType_ds . '<br>';

    $insDbDatasource = "INSERT INTO `Dashboard`.`DataSource` (`Id`, `url`, `database`, `username`, `password`, `databaseType`)VALUES ('$id_ds','$url_ds','$dataBase_ds','$user_ds','$pass_ds','$dataType_ds')";
    $resultDataSource = mysqli_query($link, $insDbDatasource) or die(mysqli_error($link));
    if ($resultDataSource) {
        mysqli_close($link);
        header("location: dataSources_mng.php");
    } else {
        mysqli_close($link);
        echo '<script type="text/javascript">';
        echo 'alert("Error: Repeat Data Source creation");';
        echo 'window.location.href = "dataSources_mng.php";';
        echo '</script>';
    }
} else if (isset($_REQUEST['modify_dataSources'])) {
    $id_ds_M = $_POST['name_Id_dataSource_M'];
    $url_ds_M = $_POST['url_dataSource_M'];
    $dataBase_ds_M = $_POST['database_dataSource_M'];
    $user_ds_M = $_POST['username_dataSource_M'];
    $pass_ds_M = $_POST['password_dataSource_M'];
    $dataType_ds_M = $_POST['databaseType_dataSource_M'];

    $updateDataSource = "UPDATE `Dashboard`.`DataSource` SET `DataSource`.`url`='$url_ds_M', `DataSource`.`database`='$dataBase_ds_M', `DataSource`.`username`='$user_ds_M', `DataSource`.`password`='$pass_ds_M', `DataSource`.`databaseType`='$dataType_ds_M'  WHERE `DataSource`.`Id`='$id_ds_M'";
    $resultUpdateDataSource = mysqli_query($link, $updateDataSource) or die(mysqli_error($link));
    if ($updateDataSource) {
        mysqli_close($link);
        header("location: dataSources_mng.php");
    } else {
        mysqli_close($link);
        echo '<script type="text/javascript">';
        echo 'alert("Error: Ripetere modifica del Datasources");';
        echo 'window.location.href = "dataSources_mng.php";';
        echo '</script>';
    }
} else if (isset($_REQUEST['add_widget_type'])) {
   // $id_w = $_POST['id_w'];
      
    $php_w = $_POST['php_w'];
    $dividiPhp = preg_split("/.php/", $php_w);
    $id_w = $dividiPhp[0];
    
    //valori delle checkbox
    $intero = $_POST['integer_w'];
    $percentuale = $_POST['percentage_w'];
    $testuale = $_POST['textual_w'];
    $mappa = $_POST['map_w'];
    $sce = $_POST['sce_w'];
    $float= $_POST['float_w'];
    $bottone= $_POST['button_w'];
    
    $elenco_tipi= $intero.'|'.$percentuale.'|'.$testuale.'|'.$mappa.'|'.$sce.'|'.$float.'|'.$bottone;
    
    echo ($elenco_tipi);
    $color_w = 0;
    $type_w = NULL;
    $met_w = NULL;  
    
    if (isset($_POST['mnC_w'])|| $_POST['mnC_w']!==''){
    $minC = $_POST['mnC_w'];    
    }else{
      $minC = NULL;  
    }
    
    if (isset($_POST['mxC_w'])|| $_POST['mxC_w']!==''){
    $maxC = $_POST['mxC_w'];    
    }else{
      $maxC = NULL;  
    }
       
    if (isset($_POST['mnR_w'])|| $_POST['mnR_w']!==''){
    $minR = $_POST['mnR_w'];    
    }else{
      $minR = NULL;  
    }      
    
    if (isset($_POST['mxR_w'])|| $_POST['mxR_w']!==''){
     $maxR = $_POST['mxR_w'];   
    }else{
        $maxR = NULL;
    }
      
    if (isset($_POST['met_w'])){
     $met_w = $_POST['met_w'];   
    }
    
    if (isset($_POST['col_w'])|| $_POST['col_w']!=''){
      $color_w = $_POST['col_w'];  
    }


    If (isset($_POST['type_w'])) {
        $type_w = $_POST['type_w'];
    } 
    
    If (isset($_POST['metric_w'])){
        $metric_w = $_POST['metric_w'];
    }
    
    if (isset($_POST['numeric_range_w'])){
    $range_w = 1;
    }else {
    $range_w = 0;   
    }   
    
    $stringa = str_replace("|||", "|", $elenco_tipi);
    $stringa = str_replace("||", "|", $stringa); 
    $stringa = str_replace("||", "|", $stringa);
    if(substr($stringa, 0, 1) == '|'){
        $stringa = substr($stringa, 1);
    }
    //$stringa = str_replace("|","",$stringa);
    //$stringa = str_replace("|", "", $stringa);
    //$stringa = str_replace("s/|","",$stringa);
    $type_w = $stringa;

    $insWid = "INSERT INTO `Dashboard`.`Widgets` (`id_type_widget`,`source_php_widget`,`min_row`,`max_row`,`min_col`,`max_col`,`widgetType`,`unique_metric`,`numeric_rangeOption`,`number_metrics_widget`,`color_widgetOption`) VALUES ('$id_w','$php_w','$minR','$maxR','$minC','$maxC','$type_w','$metric_w','$range_w','$met_w','$color_w')";
    $resultWid = mysqli_query($link, $insWid) or die(mysqli_error($link));

    if ($insWid) {
        mysqli_close($link);
        header("location: widgets_mng.php");
    } else {
        mysqli_close($link);
        echo '<script type="text/javascript">';
        echo 'alert("Error: Inserimento Widgets");';
        echo 'window.location.href = "widgets_mng.php";';
        echo '</script>';
    }
}
//modifica widget
else if (isset($_REQUEST['modify_widget_type'])) {
    
    $intero_m = $_POST['integer_m'];
    $percentuale_m = $_POST['percentage_m'];
    $testuale_m = $_POST['textual_m'];
    $mappa_m = $_POST['map_m'];
    $sce_m = $_POST['sce_m'];
    $float_m= $_POST['float_m'];
    $bottone_m= $_POST['button_m'];
    
    $elenco_tipi_m= $intero_m.'|'.$percentuale_m.'|'.$testuale_m.'|'.$mappa_m.'|'.$sce_m.'|'.$float_m.'|'.$bottone_m; 
    $id_m = $_POST['id_m'];
    $php_m = $_POST['php_m'];
    $minC_m = $_POST['mnC_m'];
    $maxC_m = $_POST['mxC_m'];
    $minR_m = $_POST['mnR_m'];
    $maxR_m = $_POST['mxR_m'];
    $met_m = $_POST['met_m'];
    $color_m = $_POST['col_m'];
    //modifca sul tipo di widget
    $type_m = $_POST['type_m'];
    //$type_m = $elenco_tipi_m;
    
    $stringa_m = str_replace("|||", "|", $elenco_tipi_m);
    $stringa_m = str_replace("||", "|", $stringa_m); 
    $stringa_m = str_replace("||", "|", $stringa_m);
    if(substr($stringa_m, 0, 1) == '|'){
        $stringa_m = substr($stringa_m, 1);
    }
    $lunghezza_m = $stringa_m.legth;
    if(substr($stringa_m, $lunghezza_m-1, 1) == '|'){
       // $stringa_m = substr($stringa_m, $lunghezza_m-1);
    }
    //$stringa_m = str_replace("|","",$stringa_m[0]);
   // $stringa_m = str_replace("|", "", $stringa_m[0]);
    $type_m = $stringa_m;
    
    $metric_m = $_POST['metric_m'];
    
    if (isset($_POST['numeric_range_m'])){
    $range_m = 1;
    }else {
    $range_m = 0;   
    }   

    $modWid = "UPDATE `Dashboard`.`Widgets` SET `Widgets`.`source_php_widget`='$php_m', `Widgets`.`min_row`='$minR_m',`Widgets`.`max_row`='$maxR_m', `Widgets`.`min_col`='$minC_m', `Widgets`.`max_col`='$maxC_m', `Widgets`.`widgetType`='$type_m', `Widgets`.`unique_metric`='$metric_m', `widgets`.`numeric_rangeOption`='$range_m', `widgets`.`number_metrics_widget`='$met_m', `widgets`.`color_widgetOption`='$color_m' WHERE `Widgets`.`id_type_widget`='$id_m'";
    $resultModWid = mysqli_query($link, $modWid) or die(mysqli_error($link));

    if ($modWid) {
        mysqli_close($link);
        header("location: widgets_mng.php");
    } else {
        mysqli_close($link);
        echo '<script type="text/javascript">';
        echo 'alert("Error: Inserimento Widgets");';
        echo 'window.location.href = "widgets_mng.php";';
        echo '</script>';
    }
}
?>