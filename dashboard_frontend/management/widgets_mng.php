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
        else if($_SESSION['loggedRole'] != "ToolAdmin")
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
                <a class="navbar-brand" href="index.html">Widgets Management</a>
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
                        <a href="../management/dashboard_mng.php"><i class="fa fa-fw fa-dashboard"></i> Dashboards management</a>
                    </li>
                    <?php
                        if(isset($_SESSION['loggedRole'])&&isset($_SESSION['loggedType']))
                        {     
                           if($_SESSION['loggedType'] == "local")
                           {
                              echo '<li><a class="internalLink" href="../management/accountManagement.php" id="accountManagementLink">Account management</a></li>';
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

                <!-- Page Heading -->
                <div class="row">
                    <div class="col-lg-12">
                        <h1 class="page-header">
                            <br/>Widgets Overview
                        </h1>
                        <nav id="modify-bar-dashboard" class="navbar navbar-default">
                            <div class="container-fluid">
                                <!-- Brand and toggle get grouped for better mobile display -->


                                <!-- Collect the nav links, forms, and other content for toggling -->
                                <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                                    <ul class="nav navbar-nav">
                                        <li class="active"><a id="link_add_widget" href="#" data-toggle="modal" data-target="#modal-add_widget"><span class="glyphicon glyphicon-plus-sign" aria-hidden="true"></span> Widgets <span class="sr-only">(current)</span></a></li>                           
                                        <li><a id ="link_help" href="#"><span class="glyphicon glyphicon-question-sign" aria-hidden="true"></span></a></li>
                                    </ul>
                                </div><!-- /.navbar-collapse -->
                            </div><!-- /.container-fluid -->
                        </nav>
                    </div>
                </div>


                <div class="row">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h3 class="panel-title"><i class="fa fa-money fa-fw"></i> Widgets</h3>
                        </div>
                        <div class="panel-body">
                            <div class="table-responsive">
                                <table id="list_widgets" class="table table-bordered table-hover table-striped">
                                    <thead>
                                        <tr>
                                            <th>Id widget</th>
                                            <th>file php</th>    
                                            <th>min columns</th>
                                            <th>max columns</th>
                                            <th>min rows</th>
                                            <th>max rows</th>
                                            <th>Type</th>
                                            <th>Unique_metric</th>
                                            <th>num. metrics</th>
                                            <th>Numeric Range</th>
                                            <th hidden>color</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- modifica crea widget type -->
                <div class="modal fade" id="modal-add_widget" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                <h4 class="modal-title">Add new Widget</h4>
                            </div>
                            <div class="modal-body">
                                <form id="form-add-widget" class="form-horizontal" role="form" method="post" action="" data-toggle="validator">
                                    <div class="tab-content">
                                        <div class="row" hidden>
                                            <label for="#" class="col-md-4 control-label">Id Widget</label> 
                                            <div class="col-md-6">
                                                <input type="text" class="form-control" id="id_w" name="id_w">
                                            </div>
                                        </div>
                                        <div class="row">
                                            <label for="#" class="col-md-4 control-label">Php File</label> 
                                            <div class="col-md-6">
                                                <input type="text" class="form-control" id="php_w" name="php_w">
                                            </div>
                                        </div>
                                        <div class="row" hidden>
                                            <label for="#" class="col-md-4 control-label">Type</label> 
                                            <div class="col-md-6">
                                                <!--<input type="text" class="form-control" id="type_w" name="type_w">-->
                                                <select class="form-control" name="type_w" id="type_w">      
                                                    <option>Intero</option>
                                                    <option>Testuale</option>
                                                    <option>Percentuale</option>
                                                    <option>Float</option>
                                                    <option>Unico</option>
                                                    <option>SCE</option>
                                                </select>
                                                </div>
                                            </div>
                                        <!--
                                        <div class="row" id="unique_row" hidden>
                                            <label for="#" class="col-md-4 control-label">Unique Metric</label> 
                                            <div class="col-md-6">
                                                <select class="form-control" id="metric_w" name="metric_w">
                                                    <option></option>  
                                                </select>
                                            </div>
                                        </div>
                                        -->
                                        <!-- inserimento checkbox dei tipi-->
                                        <div class="well">
                                            <legend class="legend-form-group">Types</legend>
                                         <div class="row">
                                            <label for="#" class="col-md-3 control-label">Integer</label>
                                            <div class="col-md-3">
                                                <input type="checkbox" class="tipo_c" name="integer_w" id="integer_w" value="Intero"/>
                                            </div>
                                            <label for="#" class="col-md-3 control-label">Percentage</label>
                                            <div class="col-md-3">
                                                <input type="checkbox" class="tipo_c" name="percentage_w" id="percentage_w" value="Percentuale"/>
                                            </div>
                                        </div>
                                            <div class="row">
                                            <label for="#" class="col-md-3 control-label">Float</label>
                                            <div class="col-md-3">
                                                <input type="checkbox" class="tipo_c" name="float_w" id="float_w" value="Float"/>
                                            </div>
                                            <label for="#" class="col-md-3 control-label">Textual</label>
                                            <div class="col-md-3">
                                                <input type="checkbox" class="tipo_c" name="textual_w" id="textual_w" value="Testuale"/>
                                            </div>
                                        </div>
                                            <div class="row">
                                            <label for="#" class="col-md-3 control-label">SCE</label>
                                            <div class="col-md-3">
                                                <input type="checkbox" class="tipo_c" name="sce_w" id="sce_w" value="SCE"/>
                                            </div>
                                            <label for="#" class="col-md-3 control-label">Map</label>
                                            <div class="col-md-3">
                                                <input type="checkbox" class="tipo_c" name="map_w" id="map_w" value="Map"/>
                                            </div>                                            
                                        </div>
                                            <div class="row">
                                                <label for="#" class="col-md-3 control-label">Button</label>
                                            <div class="col-md-3">
                                                <input type="checkbox" class="tipo_c" name="button_w" id="button_w" value="Button"/>
                                            </div>
                                              <label for="#" class="col-md-3 control-label">Unique</label>
                                            <div class="col-md-3">
                                                <input type="checkbox" class="checkstato" name="unique_w" id="unique_w" value="Unico"/>
                                            </div>
                                            </div>
                                            <div class="row" id="unique_row" hidden>
                                            <label for="#" class="col-md-4 control-label">Unique Metric</label> 
                                            <div class="col-md-6">
                                                <select class="form-control" id="metric_w" name="metric_w">
                                                    <option></option>  
                                                </select>
                                            </div>
                                        </div>
                                        </div>
                                        <!-- fine checkbox dei tipi -->                                        
                                        <div class="well">
                                            <legend class="legend-form-group">Parameters</legend>
                                            <div class="row">
                                                <label for="#" class="col-md-3 control-label"><!--min. Columns number-->Min. Rows number</label> 
                                                <div class="col-md-3">
                                                    <input type="text" class="form-control" id="mnC_w" name="mnC_w">
                                                </div>                          
                                                <label for="#" class="col-md-3 control-label"><!--max. Columns number-->Max. Rows number</label> 
                                                <div class="col-md-3">
                                                    <input type="text" class="form-control" id="mxC_w" name="mxC_w">
                                                </div>
                                            </div>
                                            <div class="row">
                                                <label for="#" class="col-md-3 control-label"><!--min. Rows number-->Min Column number</label> 
                                                <div class="col-md-3">
                                                    <input type="text" class="form-control" id="mnR_w" name="mnR_w">
                                                </div>
                                                <label for="#" class="col-md-3 control-label"><!--max. Rows number-->Max Column number</label> 
                                                <div class="col-md-3"> 
                                                    <input type="text" class="form-control" id="mxR_w" name="mxR_w">
                                                </div>
                                            </div>
                                            <div class="row">
                                                <label for="#" class="col-md-3 control-label">n. metric</label> 
                                                <div class="col-md-3">  
                                                    <input type="text" class="form-control" id="met_w" name="met_w">
                                                </div>
                                                <label for="#" class="col-md-3 control-label">Color</label> 
                                                <div class="col-md-3"> 
                                                    <input type="text" class="form-control" id="col_w" name="col_w" value="0">
                                                </div>
                                            </div>
                                            <div class="row">
                                            <label for="#" class="col-md-4 control-label">Numeric Range</label>
                                            <div class="col-md-6">
                                                <input type="checkbox" class="checkStato" name="numeric_range_w" id="numeric_range_w"/>
                                            </div>
                                        </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                        <button type="submit" id="button_widgets" name="add_widget_type" class="btn btn-primary internalLink">Add</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- modifca widget -->
                <div class="modal fade" id="modal-modify_widget" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                <h4 class="modal-title">Modify Widget</h4>
                            </div>
                            <div class="modal-body">
                                <form id="form-datasources" class="form-horizontal" role="form" method="post" action="" data-toggle="validator">
                                    <div class="tab-content">                                        
                                        <div class="row">
                                            <label for="#" class="col-md-4 control-label">Id Widget</label> 
                                            <div class="col-md-6">
                                                <input type="text" class="form-control" id="id_m" name="id_m" readonly>
                                            </div>
                                        </div>
                                        <div class="row" hidden>
                                            <label for="#" class="col-md-4 control-label">Php File</label> 
                                            <div class="col-md-6">
                                                <input type="text" class="form-control" id="php_m" name="php_m">
                                            </div>
                                        </div>
                                        <!--Elenco checkbox tipi modifica -->
                                        <input type="text" id="type_m" hidden></input>
                                        <div class="well">
                                            <legend class="legend-form-group">Types</legend>
                                         <div class="row">
                                            <label for="#" class="col-md-3 control-label">Integer</label>
                                            <div class="col-md-3">
                                                <input type="checkbox" class="tipo_m" name="integer_m" id="integer_m" value="Intero"/>
                                            </div>
                                            <label for="#" class="col-md-3 control-label">Percentage</label>
                                            <div class="col-md-3">
                                                <input type="checkbox" class="tipo_m" name="percentage_m" id="percentage_m" value="Percentuale"/>
                                            </div>
                                        </div>
                                            <div class="row">
                                            <label for="#" class="col-md-3 control-label">Float</label>
                                            <div class="col-md-3">
                                                <input type="checkbox" class="tipo_m" name="float_m" id="float_m" value="Float"/>
                                            </div>
                                            <label for="#" class="col-md-3 control-label">Textual</label>
                                            <div class="col-md-3">
                                                <input type="checkbox" class="tipo_m" name="textual_m" id="textual_m" value="Testuale"/>
                                            </div>
                                        </div>
                                            <div class="row">
                                            <label for="#" class="col-md-3 control-label">SCE</label>
                                            <div class="col-md-3">
                                                <input type="checkbox" class="tipo_m" name="sce_m" id="sce_m" value="SCE"/>
                                            </div>
                                            <label for="#" class="col-md-3 control-label">Map</label>
                                            <div class="col-md-3">
                                                <input type="checkbox" class="tipo_m" name="map_m" id="map_m" value="Map"/>
                                            </div>                                            
                                        </div>
                                            <div class="row">
                                                <label for="#" class="col-md-3 control-label">Button</label>
                                            <div class="col-md-3">
                                                <input type="checkbox" class="tipo_m" name="button_m" id="button_m" value="Button"/>
                                            </div>
                                              <label for="#" class="col-md-3 control-label">Unique</label>
                                            <div class="col-md-3">
                                                <input type="checkbox" class="checkstato" name="unique_m" id="unique_m" value="Unico"/>
                                            </div>
                                            </div>
                                            <div class="row" id="unique_m_row" hidden>
                                            <label for="#" class="col-md-4 control-label">Unique Metric</label> 
                                            <div class="col-md-6">
                                                <select class="form-control" id="metric_m" name="metric_m">
                                                    <option></option>  
                                                </select>
                                            </div>
                                        </div>
                                        </div>
                                        <!-- fine elenco-->
                                        <div class="well">
                                            <legend class="legend-form-group">Parameters</legend>
                                            <div class="row">
                                                <label for="#" class="col-md-3 control-label"><!--Min. Columns number-->Min. Rows number</label> 
                                                <div class="col-md-3">
                                                    <input type="text" class="form-control" id ="mnC_m" name="mnC_m">
                                                </div>                          
                                                <label for="#" class="col-md-3 control-label"><!--Max. Columns number-->Max. Rows number</label> 
                                                <div class="col-md-3">
                                                    <input type="text" class="form-control" id="mxC_m" name="mxC_m">
                                                </div>
                                            </div>
                                            <div class="row">
                                                <label for="#" class="col-md-3 control-label"><!--Min. Rows number-->Min. Columns number</label> 
                                                <div class="col-md-3">
                                                    <input type="text" class="form-control" id="mnR_m" name="mnR_m">
                                                </div>
                                                <label for="#" class="col-md-3 control-label"><!--Max. Rows number-->Max. Columns number</label> 
                                                <div class="col-md-3"> 
                                                    <input type="text" class="form-control" id="mxR_m" name="mxR_m">
                                                </div>
                                            </div>
                                            <div class="row">
                                                <label for="#" class="col-md-3 control-label">n. metrics</label> 
                                                <div class="col-md-3">  
                                                    <input type="text" class="form-control" id="met_m" name="met_m">
                                                </div>
                                                <label for="#" class="col-md-3 control-label">Color</label> 
                                                <div class="col-md-3"> 
                                                    <input type="text" class="form-control" id="col_m" name="col_m" value="0">
                                                </div>
                                            </div>
                                            <div class="row">
                                            <label for="#" class="col-md-4 control-label">Numeric Range</label>
                                            <div class="col-md-6">
                                                <input type="checkbox" class="checkStato" name="numeric_range_m" id="numeric_range_m"/>
                                            </div>
                                        </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                        <button type="submit" id="button_widgets" name="modify_widget_type" class="btn btn-primary">Modify</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>               

<script type='text/javascript'>
    var array_widget = new Array();
    var array_metrics = new Array();
    var array_types = new Array();
    var tipi_compatibili;
    
    $(document).ready(function () 
    {
        var internalDest = false;
        $.ajax({
        url: "get_data.php",
        data: {action: "get_widget_types"},
        type: "GET",
        async: true,
        datatype: 'json',
        success: function (data) {
            for (var i = 0; i < data.length; i++) {
                array_widget[i] = {
                    id: data[i]['type_widget'],
                    source: data[i]['source_widget'],
                    min_r: data[i]['min_row'],
                    max_r: data[i]['max_row'],
                    min_c: data[i]['min_col'],
                    max_c: data[i]['max_col'],
                    met: data[i]['n_met'],
                    color: data[i]['color'],
                    type: data[i]['type'],
                    unique: data[i]['unique'],
                    range: data[i]['range']
                };
                var tipo_elenco;
                array_types[i]=data[i]['type'];
                var list_tipi=data[i]['type'];
                var tipi = list_tipi.split("|");
                var num_tipi = tipi.length;
                if (num_tipi == 1){
                    tipo_elenco = tipi[0];
                } else {
                    tipo_elenco = 'Multipli';
                }

                $('#list_widgets tbody').append('<tr><td class="id_wid">' + array_widget[i]['id'] + '</td><td class="php_wid">' + array_widget[i]['source'] + '</td><td class="minr_wid">' + array_widget[i]['min_r'] + '</td><td class="maxr_wid">' + array_widget[i]['max_r'] + '</td><td class="minc_wid">' + array_widget[i]['min_c'] + '</td><td class="maxc_wid">' + array_widget[i]['max_c'] + '</td><td class="type_wid">' + tipo_elenco + '</td><td class="unic_wid">' + array_widget[i]['unique'] + '</td><td class="invisible_type" hidden>'+array_widget[i]['type']+'</td><td class="unique_wid" hidden>'+array_widget[i]['unique']+'</td><td class="met_wid">' + array_widget[i]['met'] + '</td><td class="range_wid">' + array_widget[i]['range'] + '</td><td class="col_wid" hidden>' + array_widget[i]['color'] + '</td><td><div class="icons-modify-ds"><a class="icon-cfg-widget" href="#" data-toggle="modal" data-target="#modal-modify_widget" style="float:left;"><span class="glyphicon glyphicon-cog glyphicon-modify-wid" tabindex="-1" aria-hidden="true"></span></a></div></td></tr>');
            }

            //elenco metriche
            $.ajax({
                url: "get_data.php",
                data: {action: "get_metrics"},
                type: "GET",
                async: true,
                datatype: 'json',
                success: function (data) {
                    for (var j = 0; j < data.length; j++) 
                    {
                        array_metrics[j] = {
                            id: data[j]['idMetric']
                        };
                        $('#metric_w').append('<option>' + array_metrics[j]['id'] + '</option>');
                        $('#metric_m').append('<option>' + array_metrics[j]['id'] + '</option>');
                    }
                }
            });


            $('#unique_w').on('click',function(){
                if ($('#unique_w').prop('checked', true))
                {
                    $('#unique_row').show();
                }
                else if ($('#unique_w').prop('checked', false)){
                    $('#unique_row').hide();
                    $('#metric_w').val("");
                } 
            });

            $('#unique_m').on('click',function(){
                if ($('#unique_m').prop('checked', true))
                {
                    $('#unique_m_row').show();
                }
                else if ($('#unique_m').prop('checked', false))
                {
                    $('#unique_m_row').hide();
                    $('#metric_m').val("");
                } 
            });

             //mostra il campo della metrica unica
             $('#type_w').change(function () {
                if ($('#type_w').val() === "Unico") 
                {
                   $('#unique_row').show();
                } 
                else 
                {
                   $('#unique_row').hide();
                   $('#metric_w').val("");
                }
             });
            //fine
            $('#type_m').change(function () {
                if ($('#type_m').val() === "Unico") 
                {
                    $('#unique_m_row').show();
                } 
                else 
                {
                    $('#unique_m_row').hide();
                    $('#metric_m').val("");
                }
            });



            //carica dati nel modifica dei widgets
            $('.icon-cfg-widget').on('click', function () {
                var idW = $(this).parent().parent().parent().find('.id_wid').text();
                var phpW = $(this).parent().parent().parent().find('.php_wid').text();
                var mnRW = $(this).parent().parent().parent().find('.minr_wid').text();
                var mxRW = $(this).parent().parent().parent().find('.maxr_wid').text();
                var mnCW = $(this).parent().parent().parent().find('.minc_wid').text();
                var mxCW = $(this).parent().parent().parent().find('.maxc_wid').text();
                var metW = $(this).parent().parent().parent().find('.met_wid').text();
                var colW = $(this).parent().parent().parent().find('.col_wid').text();    
                var typeW = $(this).parent().parent().parent().find('.invisible_type').text();
                var unicW = $(this).parent().parent().parent().find('.unic_wid').text();
                var rangeW = $(this).parent().parent().parent().find('.range_wid').text();

                if (unicW != ''){
                  $('#unique_m_row').show();
                  $('#unique_m').prop('checked', true);
                }else {
                    $('#unique_m_row').hide();
                    $('#metric_m').val('');
                    $('#unique_m').prop('checked', false);
                }
                //su typeW si fa tutta una serie di if e pregmatch per attivare i checkbox
                var int_match = typeW.match('Intero');
                if (int_match){
                    $('#integer_m').prop('checked', true);
                } else {
                    $('#integer_m').prop('checked', false);
                }

                var map_match = typeW.match('Map');
                if (map_match){
                    $('#map_m').prop('checked', true);
                } else {
                    $('#map_m').prop('checked', false);
                }

                var perc_match = typeW.match('Percentuale');
                if (perc_match){
                    $('#percentage_m').prop('checked', true);
                } else {
                    $('#percentage_m').prop('checked', false);
                }

                var text_match = typeW.match('Testuale');
                if (text_match){
                    $('#textual_m').prop('checked', true);
                } else {
                    $('#textual_m').prop('checked', false);
                }

                var float_match = typeW.match('Float');
                if (float_match){
                    $('#float_m').prop('checked', true);
                } else {
                    $('#float_m').prop('checked', false);
                }

                var button_match = typeW.match('Button');
                if (button_match){
                    $('#button_m').prop('checked', true);
                } else {
                    $('#button_m').prop('checked', false);
                }

                var sce_match = typeW.match('SCE');
                if (sce_match){
                    $('#sce_m').prop('checked', true);
                } else {
                    $('#sce_m').prop('checked', false);
                }
                //fine attivazione

                $("#id_m").val(idW);
                $("#php_m").val(phpW);
                $("#mnR_m").val(mnRW);
                $("#mxR_m").val(mxRW);
                $("#mnC_m").val(mnCW);
                $("#mxC_m").val(mxCW);
                $("#met_m").val(metW);
                $("#type_m").val(typeW);
                $("#col_m").val(colW);
                $("#metric_m").val(unicW);

                if(rangeW === 0)
                {
                    $("#numeric_range_m").prop('checked', false);
                    $("#numeric_range_m").prop('value', 0);
                }
                else
                {
                    $("#numeric_range_m").prop('checked', true);
                    $("#numeric_range_m").prop('value', 1);
                }
            });
            //fine caricamento dei dati
        }
    });
});
</script>
</body>
</html>

