<?php
    /* Dashboard Builder.
   Copyright (C) 2017 DISIT Lab http://www.disit.org - University of Florence

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

   include('process-form.php'); 
?>

<!DOCTYPE html>
<html lang="en">
<head>    
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Dashboard Management System</title>
    
    <!-- jQuery -->
    <!--<script src="http://code.jquery.com/jquery-1.10.1.min.js"></script>-->
    <script src="../js/jquery-1.10.1.min.js"></script>
    
    <!-- JQUERY UI -->
    <!--<script src="//cdnjs.cloudflare.com/ajax/libs/jqueryui/1.11.2/jquery-ui.js"></script>-->
    <script src="../js/jqueryUi/jquery-ui.js"></script>

    <!-- Bootstrap Core JavaScript -->
    <script src="../js/bootstrap.min.js"></script>

    <!-- Custom Core JavaScript -->
    <script src="../js/bootstrap-colorpicker.min.js"></script>

    <!-- Bootstrap Core CSS -->
    <link href="../css/bootstrap.css" rel="stylesheet">
    
    <!-- Filestyle -->
    <script type="text/javascript" src="../js/filestyle/src/bootstrap-filestyle.min.js"></script>

    <!-- Custom CSS -->
    <link href="../css/dashboard.css" rel="stylesheet">
    <link href="../css/bootstrap-colorpicker.min.css" rel="stylesheet">   
    
    <!-- Custom scripts -->
    <script type="text/javascript" src="../js/dashboard_mng.js"></script>

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->

</head>

<body>
    <div id="wrapper">
        <!-- Navigation -->
        <nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">
            <!-- Brand and toggle get grouped for better mobile display -->
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-ex1-collapse">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="index.html">Dashboard Builder</a>
            </div>
            <!-- Top Menu Items -->
            <ul class="nav navbar-right top-nav">
                <li><a href="#"><span class="glyphicon glyphicon-user" aria-hidden="true"></span><span id="usernameHeader"><?= $_SESSION['loggedUsername']; ?></span></a></li>
                <li><a id="logoutBtn" href="logout.php">Logout</a></li>
            </ul>
            <!-- Sidebar Menu Items - These collapse to the responsive navigation menu on small screens -->
            <div class="collapse navbar-collapse navbar-ex1-collapse">
                <ul class="nav navbar-nav side-nav">
                    <li class="active">
                        <a href="../management/dashboard_mng.php" ><i class="fa fa-fw fa-dashboard"></i> Dashboards management</a>
                    </li>
                    <?php
                        if(isset($_SESSION['loggedRole'])&&isset($_SESSION['loggedType']))
                        {     
                           if($_SESSION['loggedType'] == "local")
                           {
                              echo '<li><a href="../management/accountManagement.php" id="accountManagementLink">Account management</a></li>';
                           }
                           
                           if($_SESSION['loggedRole'] == "ToolAdmin")
                           {
                                echo '<li><a href="../management/metrics_mng.php" id="link_metric_mng">Metrics management</a></li>';
                                echo '<li><a href="../management/widgets_mng.php" id="link_widgets_mng">Widgets management</a></li>';
                                echo '<li><a href="../management/dataSources_mng.php" id="link_sources_mng">Data sources management</a></li>';
                                echo '<li><a href="../management/usersManagement.php" id="link_user_register">Users management</a></li>';
                           }
                           
                           if(($_SESSION['loggedRole'] == "ToolAdmin") || ($_SESSION['loggedRole'] == "AreaManager"))
                           {
                              echo '<li><a href="../management/poolsManagement.php?showManagementTab=false&selectedPoolId=-1" id="link_pools_management">Users pools management</a></li>';
                           }
                        }
                    ?>
                    <li>
                        <a href="<?php echo $notificatorLink?>" target="blank"> Notificator</a>
                    </li>
                </ul>
            </div>
        </nav>

        <div id="page-wrapper">
            <div class="container-fluid">
                <!-- Page Heading -->
                <div class="row">
                    <div class="col-lg-12">
                        <h1 class="page-header">
                            <br/>Dashboards overview
                        </h1>
                        <nav id="modify-bar-dashboard" class="navbar navbar-default">
                            <div class="container-fluid">
                                <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                                    <ul class="nav navbar-nav">
                                        <li class="active"><a id="link_add_dashboard" href="#" data-toggle="modal" data-target="#modal-create-dashboard"><span class="glyphicon glyphicon-plus-sign" aria-hidden="true"></span> Dashboard <span class="sr-only">(current)</span></a></li>                           
                                        <li><a id ="link_help" href="#"><span class="glyphicon glyphicon-question-sign" aria-hidden="true"></span></a></li>
                                    </ul>
                                </div>
                            </div>
                        </nav>
                    </div>
                </div>

                <div class="row">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h3 class="panel-title"><i class="fa fa-money fa-fw"></i>My dashboards</h3>
                        </div>
                        <div class="panel-body">
                            <div class="table-responsive">
                                <table id="list_dashboard" class="table table-bordered table-hover table-striped">
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Title</th>
                                            <th>Creation Date</th>
                                            <th>Status</th>
                                            <th>User</th>
                                            <th>Modify</th>
                                            <th>Preview</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="modal-create-dashboard" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">Create new dashboard</h4>
                </div>
                <div class="modal-body">
                    <form id="form-setting-dashboard" class="form-horizontal" name="form-setting-dashboard" role="form" method="post" action="" data-toggle="validator" enctype="multipart/form-data">
                        <div class="form-group">
                            <label for="inputNameDashboard" class="col-md-4 col-md-offset-1 control-label">Dashboard name</label>
                            <div class="col-md-4">
                                <input type="text" class="form-control" id="inputNameDashboard" name="inputNameDashboard" placeholder="Name" required>
                            </div>
                        </div>
                        <div class="well">
                            <legend class="legend-form-group">Header</legend>
                            <div class="form-group">
                                <label for="inputTitleDashboard" class="col-md-4 control-label">Title</label>
                                <div class="col-md-6">
                                    <input type="text" class="form-control" id="inputTitleDashboard" name="inputTitleDashboard" placeholder="Title" required>
                                </div>
                                <label for="inputSubTitleDashboard" class="col-md-4 control-label">Subtitle</label>
                                <div class="col-md-6">
                                    <input type="text" class="form-control" id="inputSubTitleDashboard" name="inputSubTitleDashboard" placeholder="Subtitle">
                                </div>
                                <label for="inputColorDashboard" class="col-md-4 control-label">Header color</label>
                                <div class="col-md-6">
                                    <div class="input-group color-choice">
                                        <input type="text" class="form-control demo demo-1 demo-auto" id="inputColorDashboard" name="inputColorDashboard" value="#5367ce" required>
                                        <span class="input-group-addon"><i></i></span>
                                    </div>
                                </div>
                                <label for="headerFontSize" class="col-md-4 control-label">Header font size (pt)</label>
                                <div class="col-md-6">
                                    <input type="text" class="form-control" id="headerFontSize" name="headerFontSize" value="60">
                                </div>
                                <label for="headerFontColor" class="col-md-4 control-label">Header font color</label>
                                <div class="col-md-6">
                                    <div class="input-group color-choice">
                                        <input type="text" class="form-control demo demo-1 demo-auto" id="headerFontColor" name="headerFontColor" value="#ffffff">
                                        <span class="input-group-addon"><i id="color_hf"></i></span>
                                    </div>
                                </div>
                                <label for="inputColorBackgroundDashboard" class="col-md-4 control-label">Background color</label>
                                <div class="col-md-6">
                                    <div class="input-group color-choice">
                                        <input type="text" class="form-control demo demo-1 demo-auto" id="inputColorBackgroundDashboard" name="inputColorBackgroundDashboard" value="#eeeeee" required>
                                        <span class="input-group-addon"><i></i></span>
                                    </div>
                                </div>
                                <label for="inputExternalColorDashboard" class="col-md-4 control-label">External frame color</label>
                                <div class="col-md-6">
                                    <div class="input-group color-choice">
                                        <input type="text" class="form-control" id="inputExternalColorDashboard" name="inputExternalColorDashboard" value="#ffffff" required>
                                        <span class="input-group-addon"><i></i></span>
                                    </div>
                                </div>
                                <label for="widgetsBorders" class="col-md-4 control-label">Widgets borders</label>
                                <div class="col-md-6">
                                    <select name="widgetsBorders" class="form-control" id="widgetsBorders" required>
                                        <option value="yes">Yes</option>
                                        <option value="no">No</option>
                                    </select>
                                </div>
                                <label for="inputWidgetsBordersColor" class="col-md-4 control-label">Widgets border color</label>
                                <div class="col-md-6">
                                    <div class="input-group color-choice">
                                        <input type="text" class="form-control" id="inputWidgetsBordersColor" name="inputWidgetsBordersColor" value="#dddddd" required>
                                        <span class="input-group-addon"><i></i></span>
                                    </div>
                                </div>
                                <label for="dashboardLogoInput" class="col-md-4 control-label">Dashboard logo</label>
                                <div class="col-md-6">
                                    <input id="dashboardLogoInput" name="dashboardLogoInput" type="file" class="filestyle form-control" data-badge="false" data-input ="true" data-size="sm" data-buttonName="btn-primary" data-buttonText="Choose file">
                                </div>
                                <label for="dashboardLogoLinkInput" class="col-md-4 control-label">Dashboard logo link</label>
                                <div class="col-md-6">
                                    <input type="text" class="form-control" id="dashboardLogoLinkInput" name="dashboardLogoLinkInput" disabled>
                                </div>
                                <input type="hidden" name="MAX_FILE_SIZE" value="2000000">
                            </div>
                        </div>
                        <div class="well">
                            <legend class="legend-form-group">Measures</legend>
                            <div class="form-group">
                                <label for="inputWidthDashboard" class="col-md-4 control-label">Width (columns)</label>
                                <div class="col-md-6">
                                    <input type="text" class="form-control" id="inputWidthDashboard" name="inputWidthDashboard" placeholder="width" required>
                                </div>
                                <label for="pixelWidth" class="col-md-4 control-label">Resulting width (px)</label>
                                <div class="col-md-6">
                                    <input type="text" class="form-control" id="pixelWidth" name="pixelWidth" disabled>
                                </div>
                                <label for="percentWidth" class="col-md-4 control-label">Percent width occupied on your monitor (fullscreen)</label>
                                <div class="col-md-6">
                                    <input type="text" class="form-control" id="percentWidth" name="percentWidth" disabled>
                                </div>
                            </div>
                        </div>
                       <div class="well">
                            <legend class="legend-form-group">Dashboard visibility</legend>
                            <div class="form-group">
                                <div class="row">
                                    <label for="inputDashboardVisibility" class="col-md-4 control-label">People who can see this dashboard</label>
                                    <div class="col-md-6">
                                        <select name="inputDashboardVisibility" class="form-control" id="inputDashboardVisibility" required>
                                            <option value="author">Dashboard author only</option>
                                            <option value="restrict">Author and a set of selected users</option>
                                            <option value="public">Everybody (public)</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="row">
                                    <label for="inputDashboardVisibilityUsersTable" class="col-md-4 control-label">Single users who can see this dashboard</label>
                                    <div class="col-md-6" id="inputDashboardVisibilityUsersTableContainer">
                                        <table id="inputDashboardVisibilityUsersTable">
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div> 
                </div>
                <div class="modal-footer">
                    <button id="button_close_popup" type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button id="button_creation_dashboard" name="creation_dashboard" class="btn btn-primary" type="submit">Create</button>
                </div>
                </form>

            </div>
        </div>
    </div>
    <div class="modal fade" id="modal-disable-dashboard" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">Change status dashboard</h4>
                </div>
                <div class="modal-body">
                    <form id="form-disable-dashboard" class="form-horizontal" name="form-disable-dashboard" role="form" method="post" action="" data-toggle="validator">
                        <div class="form-group">
                            <label for="select-dashboard-disable" class="col-md-4 col-md-offset-1 control-label">Name Dashboard</label>
                            <div class="col-md-4">
                                <select class="form-control" id="select-dashboard-disable" name="select-dashboard-disable" required>
                                </select>    
                            </div>
                        </div>
                        <div class="well">
                            <legend class="legend-form-group">Information</legend>
                            <div class="form-group">
                                <label for="inputTitleDashboard-disable" class="col-md-4 control-label">Title</label>
                                <div class="col-md-6">
                                    <input type="text" class="form-control" id="inputTitleDashboard-disable" name="inputTitleDashboard-disable" readonly>
                                </div>
                                <label for="inputUserDashboard-disable" class="col-md-4 control-label">Holder</label>
                                <div class="col-md-6">
                                    <input type="text" class="form-control" id="inputUserDashboard-disable" name="inputUserDashboard-disable" readonly>
                                </div>
                                <!--<label for="textarea-widgets-disable" class="col-md-4 control-label">Widgets</label>
                                <div class="col-md-6">                                   
                                    <textarea id ="textarea-widgets-disable" class="form-control" name="textarea-widgets-disable" rows="6" readonly></textarea>
                                </div>-->
                            </div>
                        </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button id="button_disable_dashboard" name="disable_dashboard" class="btn btn-primary" type="submit">Change</button>
                </div>
                </form>

            </div>
        </div>
    </div>  
    <!--
     Modal Eliminazione Dashboard - NON CANCELLARE
    <div class="modal fade" id="modal-delete-dashboard" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">Elimina Dashboard</h4>
                </div>
                <div class="modal-body">
                    <form id="form-delete-dashboard" class="form-horizontal" name="form-delete-dashboard" role="form" method="post" action="" data-toggle="validator">
                        <div class="form-group">
                            <label for="select-dashboard" class="col-md-4 col-md-offset-1 control-label">Nome Dashboard</label>
                            <div class="col-md-4">
                                <select class="form-control" id="select-dashboard-delete" name="select-dashboard-delete" required>
                                </select>
                            </div>                      
                        </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button id="button_delete_dashboard" name="delete_dashboard" class="btn btn-primary" type="submit">Elimina</button>
                </div>
                </form>

            </div>
        </div>
    </div>
    -->
    <!-- Nuovo modal di modifica -->
    <div class="modal fade" id="modal-dashboard-m2" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document" id="dialog-dashboard-m2">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">Modify dashboard</h4>
                </div>
                <div class="modal-body">
                    <form id="form-modify-dashboard2" class="form-horizontal" name="form-modify-dashboard2" role="form" method="post" action="" data-toggle="validator">
                        <div id="status_message"> 
                                <p>Are you sure do you want to modify this dashboard?</p>
                        </div>
                        <input type="hidden" id="selectedDashboardName" name="selectedDashboardName">
                        <input type="hidden" id="selectedDashboardAuthorName" name="selectedDashboardAuthorName">
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                            <button id="button_modify_dashboard" name="modify_dashboard" class="btn btn-primary" type="submit">Modify</button>
                        </div>
                    </form>
                </div>            
            </div>            
        </div> 
    </div>
    <!-- Fine nuovo modal di modifica -->    
    <!-- Nuovo modal disabilita dashboard -->
    <div class="modal fade" id="modal-dashboard-disability" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document" id="dialog-dashboard-disability">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">Modify dashboard status</h4>
                </div>
                <form id="form-disability-dashboard2" class="form-horizontal" name="form-disability-dashboard2" role="form" method="post" action="" data-toggle="validator">
                    <div class="modal-body">
                        <div id="status_message">   
                            <p>
                            Are you sure do you want to change the status of this dashboard?
                            </p>
                        </div>
                        <input type="hidden" id="selectedDashboardNameForStatusChange" name="selectedDashboardNameForStatusChange">
                        <input type="hidden" id="selectedDashboardAuthorNameForStatusChange" name="selectedDashboardAuthorNameForStatusChange">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button id="button_status_dashboard" name="modify_status_dashboard" class="btn btn-primary" type="submit">Confirm</button>
                    </div>
                </form>
            </div>
        </div>            
    </div> 

    <script type='text/javascript'>
        $(document).ready(function () 
        {
            var loggedRole = "<?= $_SESSION['loggedRole'] ?>";
            var loggedType = "<?= $_SESSION['loggedType'] ?>";
            var usr = "<?= $_SESSION['loggedUsername'] ?>";
            var userVisibilitySet = null;
            var array_dahsboards = new Array();
            $('#color_hf').css("background-color", '#ffffff');
            
            setGlobals(loggedRole, usr, loggedType, userVisibilitySet);
            
            $("#logoutBtn").click(function(event)
            {
               event.preventDefault();
               
               $.ajax({
                  url: "http://localhost/Notificator/restInterface.php",
                  data: {
                     apiUsr: "alarmManager",
                     apiPwd: "d0c26091b8c8d4c42c02085ff33545c1", //MD5
                     operation: "remoteLogout",
                     app: "Dashboard",
                     appUsr: usr
                  },
                  type: "POST",
                  async: true,
                  dataType: 'json',
                  success: function (data) 
                  {
                     console.log("Correct");
                     console.log(data);
                     location.href = "logout.php";
                  },
                  error: function (data)
                  {
                     console.log("Error");
                     console.log(data);
                     location.href = "logout.php";
                  }
               });
            });
            
            $('#inputDashboardVisibility').change(function(){
               if($(this).val() === 'restrict') 
               {
                   $('label[for="inputDashboardVisibilityUsersTable"]').show();
                   $('#inputDashboardVisibilityUsersTableContainer').show();
               }
               else
               {
                   $('label[for="inputDashboardVisibilityUsersTable"]').hide();
                   $('#inputDashboardVisibilityUsersTableContainer').hide();
               }
            });
            
            $('#inputWidthDashboard').on('input',function(e)
            {
               var cols = parseInt($('#inputWidthDashboard').val());
               var isNotNumber = isNaN(cols);
               if(isNotNumber)
               {
                   $('#pixelWidth').val("");
                   $('#percentWidth').val("");
               }
               else
               {
                   var px = parseInt(cols*78 + 10);
                   var percent = parseInt(px/screen.width*100);
                   $('#pixelWidth').css("background-color", "white");
                   $('#percentWidth').css("background-color", "white");
                   $('#pixelWidth').val(px + " px");
                   $('#percentWidth').val(percent + " %");
               }
            });
            
            $.ajax({
                url: "get_data.php",
                data: {action: "get_dashboards"},
                type: "GET",
                async: true,
                dataType: 'json',
                success: function (data) 
                {
                    for (var i = 0; i < data.length; i++)
                    {
                        array_dahsboards[i] = {id: data[i].dashboard['id'],
                            name: data[i].dashboard['name'],
                            title: data[i].dashboard['title_header'],
                            date: data[i].dashboard['creation_date'],
                            status: data[i].dashboard['status'],
                            username: data[i].dashboard['username'],
                            reference: data[i].dashboard['reference']
                        };
                        
                        var trCode = null;
                        
                        if(array_dahsboards[i]['reference'] === '1')
                        {
                            trCode = '<tr style="font-style: italic">'; 
                        }
                        else
                        {
                            trCode = '<tr>';    
                        }

                        var status;//Ma è usata?
                        
                        //Lasciare i valori di controllo come stringa, visto che la query a monte lato PHP non è fatta con i prepared statements
                        if(data[i].dashboard['status'] === "1") 
                        {
                            status = "Attiva";//Ma è usata?
                            //Popolamento lista delle dashboard
                            $('#list_dashboard tbody').append(trCode + '<td class="name_dash">' + array_dahsboards[i]['name'] + '</td><td>' + array_dahsboards[i]['title'] + '</td><td>' + array_dahsboards[i]['date'] + '</td><td><a class="icon-status-dash" href="#" data-toggle="modal" data-target="#modal-dashboard-disability"><input type="checkbox" class="checkStato" name="stato" value="1" checked></a></td><td class="name_user">' + array_dahsboards[i]['username'] + '</td><td><div class="icons-modify-dash"><a class="icon-cfg-metric" href="#" data-toggle="modal" data-target="#modal-dashboard-m2" style="float:left;"><span class="glyphicon glyphicon-cog glyphicon-modify-metric" tabindex="-1" aria-hidden="true"></span></a></div></td><td><button type="button" class="btn btn-default button-preview"><span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span></button></td></tr>');
                        } 
                        else if(data[i].dashboard['status'] === "0")
                        {
                            status = "Non attiva";//Ma è usata?
                            //Popolamento lista delle dashboard
                            $('#list_dashboard tbody').append(trCode + '<td class="name_dash">' + array_dahsboards[i]['name'] + '</td><td>' + array_dahsboards[i]['title'] + '</td><td>' + array_dahsboards[i]['date'] + '</td><td><a class="icon-status-dash" href="#" data-toggle="modal" data-target="#modal-dashboard-disability"><input type="checkbox" class="checkStato" name="stato" value="0"></a></td><td class="name_user">' + array_dahsboards[i]['username'] + '</td><td><div class="icons-modify-dash"><a class="icon-cfg-metric" href="#" data-toggle="modal" data-target="#modal-dashboard-m2" style="float:left;"><span class="glyphicon glyphicon-cog glyphicon-modify-metric" tabindex="-1" aria-hidden="true"></span></a></div></td><td><button type="button" class="btn btn-default button-preview"><span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span></button></td></tr>');
                        }
                    }
                }
            });

            $('#dashboardLogoInput').change(function ()
            {
                $('#dashboardLogoLinkInput').removeAttr('disabled');
            });

            //Listener apertura view di una dashboard
            $(document).on('click', '.button-preview', function () 
            {
                var dashboardName = $(this).parent().parent().find('td:first').text();
                var dashboardAuthor = $(this).parent().parent().find('.name_user').text();
                window.open("../view/preview.php?dashboardName=" + dashboardName + "&dashboardAuthor=" + dashboardAuthor);
            });

            $(function () 
            {
                $('.color-choice').colorpicker();
            });

            //Listener per pulsante di modifica dashboard
            $(document).on('click', '.icons-modify-dash', function () 
            {
                $("#selectedDashboardName").val($(this).parent().parent().find('.name_dash').text());
                $("#selectedDashboardAuthorName").val($(this).parent().parent().find('.name_user').text());
            });
            
            //Listener per pulsante di modifica stato dashboard
            $(document).on('click','.icon-status-dash', function ()
            {
                $('#selectedDashboardNameForStatusChange').val($(this).parent().parent().find('.name_dash').text());
                $("#selectedDashboardAuthorNameForStatusChange").val($(this).parent().parent().find('.name_user').text());
            });
            
            //Caricamento dell'insieme di visibilità per l'utente collegato
            $.ajax({
               url: "getUserVisibilitySet.php",
               type: "POST",
               async: true,
               dataType: 'JSON',
               cache: false, 
               success: function (data) 
               {
                   userVisibilitySet = data;

                   $("#inputDashboardVisibilityUsersTable").append('<tr><th class="selectCell">Select</th><th class="usernameCell">Username</th></tr>');

                   for(var i = 0; i < userVisibilitySet.length; i++)
                   {
                      $("#inputDashboardVisibilityUsersTable").append('<tr><td><input type="checkbox" name="selectedVisibilityUsers[]" value="' + userVisibilitySet[i] + '"/></td><td>' + userVisibilitySet[i] + '</td></tr>'); 
                   }

                   //Metodo apposito per settare/desettare gli attributi checked sulle checkbox
                   $('#inputDashboardVisibilityUsersTable input[type="checkbox"').off('click');
                   $('#inputDashboardVisibilityUsersTable input[type="checkbox"').click(function(){
                       if($(this).attr("checked") === "checked")
                       {
                           $(this).removeAttr("checked");
                       }
                       else
                       {
                           $(this).attr("checked", "true");
                       }
                   });
               },
               error: function (data) 
               {
                   //TBD
                   console.log("Error: " + JSON.stringify(data));
               }
           });
        });
    </script>
</body>
</html>

