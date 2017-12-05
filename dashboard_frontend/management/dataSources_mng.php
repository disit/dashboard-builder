<?php
    /* Dashboard Builder.
   Copyright (C) 2016 DISIT Lab https://www.disit.org - University of Florence

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
    session_start();
?>

<html lang="en">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Dashboard Management System</title>

    <!-- Bootstrap Core CSS -->
    <link href="../css/bootstrap.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link href="../css/dashboard.css" rel="stylesheet">
    <link href="../css/bootstrap-colorpicker.min.css" rel="stylesheet">
    
    <!-- jQuery -->
    <script src="../js/jquery-1.10.1.min.js"></script>
    
    <!-- JQUERY UI -->
    <script src="../js/jqueryUi/jquery-ui.js"></script>

    <!-- Bootstrap Core JavaScript -->
    <script src="../js/bootstrap.min.js"></script>

    <!-- Custom Core JavaScript -->
    <script src="../js/bootstrap-colorpicker.min.js"></script>
</head>

<body>
    <?php
        if(!isset($_SESSION['loggedRole']))
        {
            echo '<script type="text/javascript">';
            echo 'window.location.href = "unauthorizedUser.php";';
            echo '</script>';
        }
        else if(($_SESSION['loggedRole'] != "ToolAdmin") && ($_SESSION['loggedRole'] != "AreaManager") && ($_SESSION['loggedRole'] != "Manager"))
        {
            echo '<script type="text/javascript">';
            echo 'window.location.href = "unauthorizedUser.php";';
            echo '</script>';
        }
    ?>
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
                <a class="navbar-brand" href="index.html">Gestione DataSources</a>
            </div>
            <!-- Top Menu Items -->
            <ul class="nav navbar-right top-nav">
                <?php
                    if(isset($_SESSION['loggedUsername']))
                    {
                        echo '<li><a href="#"><span class="glyphicon glyphicon-user" aria-hidden="true"></span>' . $_SESSION["loggedUsername"] . '</a></li>';
                        echo '<li><a href="logout.php">Logout</a></li>';
                    }
                ?>
            </ul>
            <!-- Sidebar Menu Items - These collapse to the responsive navigation menu on small screens -->
            <div class="collapse navbar-collapse navbar-ex1-collapse">
                <ul class="nav navbar-nav side-nav">
                    <li>
                        <a href="../management/dashboard_mng.php" class="internalLink"><i class="fa fa-fw fa-dashboard"></i> Dashboards management</a>
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
                                echo '<li><a class="internalLink" href="../management/metrics_mng.php" id="link_metric_mng">Metrics management</a></li>';
                                echo '<li><a class="internalLink" href="../management/widgets_mng.php" id="link_widgets_mng">Widgets management</a></li>';
                                echo '<li><a class="internalLink" href="../management/dataSources_mng.php" id="link_sources_mng">Data sources management</a></li>';
                                echo '<li><a class="internalLink" href="../management/usersManagement.php" id="link_user_register">Users management</a></li>';
                                
                           }
                           
                           if(($_SESSION['loggedRole'] == "ToolAdmin") || ($_SESSION['loggedRole'] == "AreaManager"))
                           {
                              echo '<li><a class="internalLink" href="../management/poolsManagement.php?showManagementTab=false&selectedPoolId=-1" id="link_pools_management">Users pools management</a></li>';
                           }
                        }
                    ?>
                    <li>
                        <a href="<?php echo $notificatorLink?>" target="blank"> Notificator</a>
                    </li>
                </ul>
            </div>
            <!-- /.navbar-collapse -->
        </nav>

        <div id="page-wrapper">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-lg-12">
                        <h1 class="page-header">
                            <br/>DataSources Overview
                        </h1>

                        <nav id="modify-bar-dashboard" class="navbar navbar-default">
                            <div class="container-fluid">
                                <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                                    <ul class="nav navbar-nav">
                                        <li class="active"><a id="link_add_dataSource" href="#" data-toggle="modal" data-target="#modal-datasources"><span class="glyphicon glyphicon-plus-sign" aria-hidden="true"></span> DataSource <span class="sr-only">(current)</span></a></li>                           
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
                            <h3 class="panel-title"><i class="fa fa-money fa-fw"></i> Data Sources</h3>
                        </div>
                        <div class="panel-body">
                            <div class="table-responsive">
                                <table id="list_dataSources" class="table table-bordered table-hover table-striped">
                                    <thead>
                                        <tr>
                                            <th>Id</th>
                                            <th>Url</th>
                                            <th>Database</th>
                                            <th>Utente</th>
                                            <th>Password</th>
                                            <th>Database Type</th>
                                            <th>Modify</th>
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
    <!-- Aggiungi nuovo data sources-->    
    <div class="modal fade" id="modal-datasources" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Crea Datasources</h4>
                </div>
                <div class="modal-body">
                    <form id="form-datasources" class="form-horizontal" role="form" method="post" action="" data-toggle="validator">
                        <div class="tab-content">
                            <div class="row">
                                <label for="#" class="col-md-4 control-label">Id DataSource</label> 
                                <div class="col-md-6">
                                    <input type="text" class="form-control" id="name_Id_dataSource" name="name_Id_dataSource" required>
                                </div>
                            </div>
                            <div class="row">
                                <label for="#" class="col-md-4 control-label">Url</label> 
                                <div class="col-md-6">
                                    <input type="text" class="form-control" id="url_dataSource" name="url_dataSource">
                                </div>
                            </div>
                            <div class="row">
                                <label for="#" class="col-md-4 control-label">database Type</label> 
                                <div class="col-md-6">
                                    <select class="form-control" name="databaseType_dataSource" id="databaseType_dataSource"> 
                                        <option>
                                            MySQL 
                                        </option>
                                        <option>
                                            RDFstore   
                                        </option>
                                    </select> 
                                </div>
                            </div>
                            <div class="row">
                                <label for="#" class="col-md-4 control-label">Database</label> 
                                <div class="col-md-6">
                                    <input type="text" class="form-control" id="database_dataSource" name="database_dataSource">
                                </div>
                            </div>
                            <div class="row">
                                <label for="#" class="col-md-4 control-label">Username</label> 
                                <div class="col-md-6">
                                    <input type="text" class="form-control" id="username_dataSource" name="username_dataSource">
                                </div>
                            </div>
                            <div class="row">
                                <label for="#" class="col-md-4 control-label">Password</label> 
                                <div class="col-md-6">
                                    <input type="password" class="form-control" id="password_dataSource" name="password_dataSource">
                                </div>
                            </div>                           
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Annulla</button>
                            <button type="submit" id="button_datasources" name="create_dataSources" class="btn btn-primary internalLink">Conferma</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- Fine menù dei datasources -->
    <!-- modifica datasources -->
    <div class="modal fade" id="modal-modfy_datasources" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Modifica Datasources</h4>
                </div>
                <div class="modal-body">
                    <form id="form-datasources" class="form-horizontal" role="form" method="post" action="" data-toggle="validator">
                        <div class="tab-content">
                            <div class="row">
                                <label for="#" class="col-md-4 control-label">Id DataSource</label> 
                                <div class="col-md-6">
                                    <input type="text" class="form-control" id="name_Id_dataSource_M" name="name_Id_dataSource_M" readonly>
                                </div>
                            </div>
                            <div class="row">
                                <label for="#" class="col-md-4 control-label">Url</label> 
                                <div class="col-md-6">
                                    <input type="text" class="form-control" id="url_dataSource_M" name="url_dataSource_M">
                                </div>
                            </div>
                            <div class="row">
                                <label for="#" class="col-md-4 control-label">database Type</label> 
                                <div class="col-md-6">
                                    <select class="form-control" name="databaseType_dataSource_M" id="databaseType_dataSource_M"> 
                                        <option>
                                            MySQL 
                                        </option>
                                        <option>
                                            RDFstore   
                                        </option>
                                    </select> 
                                </div>
                            </div>
                            <div class="row">
                                <label for="#" class="col-md-4 control-label">Database</label> 
                                <div class="col-md-6">
                                    <input type="text" class="form-control" id="database_dataSource_M" name="database_dataSource_M">
                                </div>
                            </div>
                            <div class="row">
                                <label for="#" class="col-md-4 control-label">Username</label> 
                                <div class="col-md-6">
                                    <input type="text" class="form-control" id="username_dataSource_M" name="username_dataSource_M">
                                </div>
                            </div>
                            <div class="row">
                                <label for="#" class="col-md-4 control-label">Password</label> 
                                <div class="col-md-6">
                                    <input type="text" class="form-control" id="password_dataSource_M" name="password_dataSource_M">
                                </div>
                            </div>                           
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Annulla</button>
                            <button type="submit" id="button_datasources" name="modify_dataSources" class="btn btn-primary internalLink">Modifica</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script type='text/javascript'>
        $(document).ready(function () {
            var array_dataSources = new Array();
            var internalDest = false;
            
            $.ajax({
            url: "get_data.php",
            data: {action: "get_dataSource"},
            type: "GET",
            async: true,
            datatype: 'json',
            success: function (data) {
                for (var i = 0; i < data.length; i++) 
                {
                    array_dataSources[i] = {
                        id: data[i]['idDataS'],
                        url: data[i]['urlDataS'],
                        database: data[i]['databaseDS'],
                        userName: data[i]['usernameDS'],
                        passWord: data[i]['passwordDS'],
                        databaseType: data[i]['databaseTypeDS']
                    };

                    $('#list_dataSources tbody').append('<tr><td class="name_ds">' + array_dataSources[i]['id'] + '</td><td class="url_ds">' + array_dataSources[i]['url'] + '</td><td class="db_ds">' + array_dataSources[i]['database'] + '</td><td class="user_ds">' + array_dataSources[i]['userName'] + '</td><td class="pass_ds">' + array_dataSources[i]['passWord'] + '</td><td class="type_ds">' + array_dataSources[i]['databaseType'] + '</td><td><div class="icons-modify-ds"><a class="icon-cfg-datasources" href="#" data-toggle="modal" data-target="#modal-modfy_datasources" style="float:left;"><span class="glyphicon glyphicon-cog glyphicon-modify-ds" tabindex="-1" aria-hidden="true"></span></a></div></td></tr>');
                }

                $('.icon-cfg-datasources').on('click', function () 
                {
                    var name = $(this).parent().parent().parent().find('.name_ds').text();
                    var url = $(this).parent().parent().parent().find('.url_ds').text();
                    var db = $(this).parent().parent().parent().find('.db_ds').text();
                    var user = $(this).parent().parent().parent().find('.user_ds').text();
                    var pass = $(this).parent().parent().parent().find('.pass_ds').text();
                    var type = $(this).parent().parent().parent().find('.type_ds').text();
                    $("#name_Id_dataSource_M").val(name);
                    $("#url_dataSource_M").val(url);
                    $("#database_dataSource_M").val(db);
                    $("#username_dataSource_M").val(user);
                    $("#password_dataSource_M").val(pass);
                    $("#databaseType_dataSource_M").val(type);
                });
            }
        });
    });
    </script>
</body>
</html>

