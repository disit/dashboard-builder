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
    <script src="../js/jquery-1.10.1.min.js"></script>
    
    <!-- JQUERY UI -->
    <script src="../js/jqueryUi/jquery-ui.js"></script>

    <!-- Bootstrap Core JavaScript -->
    <script src="../js/bootstrap.min.js"></script>

    <!-- Custom Core JavaScript -->
    <script src="../js/bootstrap-colorpicker.min.js"></script>

    <!-- Bootstrap Core CSS -->
    <link href="../css/bootstrap.css" rel="stylesheet">
    
    <!-- Bootstrap editable tables -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/x-editable/1.5.0/bootstrap3-editable/css/bootstrap-editable.css" rel="stylesheet"/>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/x-editable/1.5.0/bootstrap3-editable/js/bootstrap-editable.min.js"></script>
    
    <!-- Filestyle -->
    <script type="text/javascript" src="../js/filestyle/src/bootstrap-filestyle.min.js"></script>
    
    <!-- Bootstrap table -->
   <link rel="stylesheet" href="../boostrapTable/dist/bootstrap-table.css">
   <script src="../boostrapTable/dist/bootstrap-table.js"></script>
   <!-- Questa inclusione viene sempre DOPO bootstrap-table.js -->
   <script src="../boostrapTable/dist/locale/bootstrap-table-en-US.js"></script>
   
   <!-- Font awesome icons -->
    <link rel="stylesheet" href="../js/fontAwesome/css/font-awesome.min.css">
    
     <!-- Bootstrap toggle button -->
   <link href="../bootstrapToggleButton/css/bootstrap-toggle.min.css" rel="stylesheet">
   <script src="../bootstrapToggleButton/js/bootstrap-toggle.min.js"></script>
 
    <!-- Custom CSS -->
    <link href="../css/dashboard.css" rel="stylesheet">
    <link href="../css/bootstrap-colorpicker.min.css" rel="stylesheet">   
    
    <!-- Custom scripts -->
    <script type="text/javascript" src="../js/dashboard_mng.js"></script>
    
    <link href="https://fonts.googleapis.com/css?family=Cabin:400,500,600,700|Catamaran|Varela+Round" rel="stylesheet">
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
                <a class="navbar-brand" href="index.html">Dashboard Management System</a>
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
                        <a href="../management/dashboard_mng.php" class="internalLink"> Dashboards management</a>
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
                        <a href="<?php echo $notificatorLink?>" target="blank" class="internalLink"> Notificator</a>
                    </li>
                </ul>
            </div>
        </nav>

        <div id="page-wrapper">
            <div class="container-fluid">
                <div class="row" style="margin-top: 50px">
                    <div class="col-xs-12 centerWithFlex mainPageTitleContainer">
                        Dashboards
                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-12">
                        <table id="list_dashboard" class="table"></table>
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
                                <label for="headerVisible" class="col-md-4 control-label">Show header</label>
                                <div class="col-md-6">
                                    <select name="headerVisible" id="headerVisible" class="form-control" required>
                                        <option value="1">Yes</option>
                                        <option value="0">No</option>
                                    </select>
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
                            <legend class="legend-form-group">Iframe embeddability</legend>
                            <div class="form-group">
                                <div class="row">
                                    <label for="embeddable" class="col-md-4 control-label">Allow embed</label>
                                    <div class="col-md-6">
                                        <select id="embeddable" name="embeddable" class="form-control">
                                            <option value="no">No</option>
                                            <option value="yes">Yes</option>
                                        </select>
                                    </div>
                                    <label for="authorizedPages" class="col-md-4 control-label">Authorized pages</label>
                                    <div class="col-md-6">
                                        <table id="authorizedPagesTable">
                                            <thead>
                                                <th>Page</th>
                                                <th><i id="addAuthorizedPageBtn" class="fa fa-plus"></i></th>
                                            </thead>
                                            <tbody></tbody>
                                        </table> 
                                    </div>
                                    <input type="hidden" id="authorizedPagesJson" name="authorizedPagesJson" />
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
                    <button id="button_creation_dashboard" name="creation_dashboard" class="btn btn-primary internalLink" type="submit">Create</button>
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
            var authorizedPages = null;
            var internalDest = false;
            var tableFirstLoad = true;
            
            $('.internalLink').on('mousedown', function(){
                internalDest = true;
            });

            /*$(window).on('beforeunload', function(){
                $.ajax({
                        url: "iframeProxy.php",
                        action: "notificatorRemoteLogout",
                        async: false,
                        success: function()
                        {
                            console.log("Remote logout from Notificator OK");
                        },
                        error: function(errorData)
                        {
                            console.log("Remote logout from Notificator failed");
                            console.log(JSON.stringify(errorData));
                        }
                    });
                if(internalDest === false)
                {
                    console.log("Logout notificatore");
                    $.ajax({
                        url: "iframeProxy.php",
                        action: "notificatorRemoteLogout",
                        async: false,
                        success: function()
                        {
                            console.log("Remote logout from Notificator OK");
                        },
                        error: function(errorData)
                        {
                            console.log("Remote logout from Notificator failed");
                            console.log(JSON.stringify(errorData));
                        }
                    });
                }
                else
                {
                    console.log("Navigazione interna");
                    return 'Are you sure you want to leave?';
                }
            });*/
            
            
            
            $('#authorizedPagesJson').val("");
            $('label[for=authorizedPages]').hide();
            $('#authorizedPagesTable').parent().hide();
            $('#color_hf').css("background-color", '#ffffff');
            
            $('#embeddable').change(function(){
                if($(this).val() === 'no') 
                {
                    $('label[for=authorizedPages]').hide();
                    $('#authorizedPagesTable').parent().hide();
                    $('#authorizedPagesTable tbody').empty();
                    
                    authorizedPages = [];
                    $('#authorizedPagesJson').val("");
                }
                else
                {
                    $('label[for=authorizedPages]').show();
                    $('#authorizedPagesTable').parent().show();
                }
            });
            
            $('#addAuthorizedPageBtn').click(function(){
                 var row = $('<tr><td><a href="#" class="toBeEdited" data-type="text" data-mode="popup"></a></td><td><i class="fa fa-minus"></i></td></tr>');
                 $('#authorizedPagesTable tbody').append(row);
                 
                 var rowIndex = row.index();
                 
                 row.find('a').editable({
                    emptytext: "Empty",
                    display: function(value, response){
                        if(value.length > 35)
                        {
                            $(this).html(value.substring(0, 32) + "...");
                        }
                        else
                        {
                           $(this).html(value); 
                        }
                    }
                });
                
                authorizedPages[rowIndex] = null;
                $('#authorizedPagesJson').val(JSON.stringify(authorizedPages));
                
                row.find('i.fa-minus').click(function(){
                    var rowIndex = $(this).parents('tr').index();
                    $('#authorizedPagesTable tbody tr').eq(rowIndex).remove();
                    authorizedPages.splice(rowIndex, 1);
                    $('#authorizedPagesJson').val(JSON.stringify(authorizedPages));
                });
                
                row.find('a.toBeEdited').on('save', function(e, params){
                    var rowIndex = $(this).parents('tr').index();
                    authorizedPages[rowIndex] = params.newValue;
                    $('#authorizedPagesJson').val(JSON.stringify(authorizedPages));
                });
            });
            
            setGlobals(loggedRole, usr, loggedType, userVisibilitySet);
            
            $("#logoutBtn").click(function(event)
            {
               event.preventDefault();
               location.href = "logout.php";
               /*$.ajax({
                    url: "iframeProxy.php",
                    action: "notificatorRemoteLogout",
                    async: true,
                    success: function()
                    {
                        
                    },
                    error: function(errorData)
                    {
                        console.log("Remote logout from Notificator failed");
                        console.log(JSON.stringify(errorData));
                    },
                    complete: function()
                    {
                        location.href = "logout.php";
                    }
                });*/
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
                data: {
                    action: "get_dashboards"
                },
                type: "GET",
                async: true,
                dataType: 'json',
                success: function(data) 
                {
                    $('#list_dashboard').bootstrapTable({
                        columns: [{
                            field: 'title_header',
                            title: 'Title',
                            sortable: true,
                            valign: "middle",
                            align: "center",
                            halign: "center",
                            formatter: function(value, row, index)
                            {
                                if(value !== null)
                                {
                                    if(value.length > 75)
                                    {
                                       return value.substr(0, 75) + " ...";
                                    }
                                    else
                                    {
                                       return value;
                                    } 
                                }
                            }
                        }, {
                            field: 'user',
                            title: 'Creator',
                            sortable: true,
                            valign: "middle",
                            align: "center",
                            halign: "center",
                            formatter: function(value, row, index)
                            {
                                if(value !== null)
                                {
                                    if(value.length > 50)
                                    {
                                       return value.substr(0, 50) + " ...";
                                    }
                                    else
                                    {
                                       return value;
                                    } 
                                }
                            }
                        },
                        {
                            field: 'creation_date',
                            title: 'Creation date',
                            sortable: true,
                            valign: "middle",
                            align: "center",
                            halign: "center",
                        },
                        {
                            field: 'visibility',
                            title: 'Visibility',
                            sortable: true,
                            valign: "middle",
                            align: "center",
                            halign: "center",
                            formatter: function(value, row, index)
                            {
                                return value;
                            }
                        },
                        {
                            field: 'embeddable',
                            title: 'Embeddable',
                            sortable: true,
                            valign: "middle",
                            align: "center",
                            halign: "center",
                            formatter: function(value, row, index)
                            {
                                return value;
                            }
                        },
                        {
                            field: 'status_dashboard',
                            title: "Status",
                            align: "center",
                            valign: "middle",
                            align: "center",
                            halign: "center",
                            formatter: function(value, row, index)
                            {
                                if((value === '0')||(value === 0))
                                {
                                    return '<input type="checkbox" data-toggle="toggle" class="changeDashboardStatus">';
                                }
                                else
                                {
                                    return '<input type="checkbox" checked data-toggle="toggle" class="changeDashboardStatus">';
                                }
                            }
                        },
                        {
                            title: "Edit",
                            align: "center",
                            valign: "middle",
                            align: "center",
                            halign: "center",
                            formatter: function(value, row, index)
                            {
                                return '<span class="glyphicon glyphicon-cog"></span>'; 
                            }
                        },
                        {
                            title: "View",
                            align: "center",
                            valign: "middle",
                            align: "center",
                            halign: "center",
                            formatter: function(value, row, index)
                            {
                                return '<i class="fa fa-eye"></i>'; 
                            }
                        }],
                        data: data,
                        search: true,
                        pagination: true,
                        pageSize: 10,
                        locale: 'en-US',
                        searchAlign: 'left',
                        uniqueId: "Id",
                        striped: true,
                        onPostBody: function()
                        {
                            if(tableFirstLoad)
                            {
                                //Caso di primo caricamento della tabella
                                tableFirstLoad = false;
                                var addDasboardDiv = $('<div class="pull-right"><i id="link_add_dashboard" data-toggle="modal" data-target="#modal-add-metric" class="fa fa-plus-square" style="font-size:36px; color: #ffcc00"></i></div>');
                                $('div.fixed-table-toolbar').append(addDasboardDiv);
                                addDasboardDiv.css("margin-top", "10px");
                                addDasboardDiv.find('i.fa-plus-square').off('hover');
                                addDasboardDiv.find('i.fa-plus-square').hover(function(){
                                    $(this).css('color', 'red');
                                    $(this).css('cursor', 'pointer');
                                }, 
                                function(){
                                    $(this).css('color', '#ffcc00');
                                    $(this).css('cursor', 'normal');
                                });
                                
                                $('#link_add_dashboard').off('click');
                                $('#link_add_dashboard').click(function(){
                                    authorizedPages = [];
                                    $('#modal-create-dashboard').modal('show');
                                });
                            }
                            else
                            {
                                //Casi di cambio pagina
                            }

                            //Istruzioni da eseguire comunque
                            $('#list_dashboard span.glyphicon-cog').css('color', '#337ab7');
                            $('#list_dashboard span.glyphicon-cog').css('font-size', '20px');

                            $('#list_dashboard span.glyphicon-cog').off('hover');
                            $('#list_dashboard span.glyphicon-cog').hover(function(){
                                $(this).css('color', '#ffcc00');
                                $(this).css('cursor', 'pointer');
                            }, 
                            function(){
                                $(this).css('color', '#337ab7');
                                $(this).css('cursor', 'normal');
                            });
                            
                            $('#list_dashboard i.fa-eye').css('color', '#337ab7');
                            $('#list_dashboard i.fa-eye').css('font-size', '24px');
                            
                            $('#list_dashboard i.fa-eye').off('hover');
                            $('#list_dashboard i.fa-eye').hover(function(){
                                $(this).css('color', '#ffcc00');
                                $(this).css('cursor', 'pointer');
                            }, 
                            function(){
                                $(this).css('color', '#337ab7');
                                $(this).css('cursor', 'normal');
                            });
                            
                            $('#list_dashboard i.fa-eye').off('click');
                            $('#list_dashboard i.fa-eye').click(function () 
                            {
                                var dashboardId = $(this).parents('tr').attr("data-uniqueid");
                                window.open("../view/index.php?iddasboard=" + btoa(dashboardId));
                            });
                            
                            $('#list_dashboard input.changeDashboardStatus').bootstrapToggle({
                                on: "On",
                                off: "Off",
                                onstyle: "primary",
                                offstyle: "default",
                                size: "small"
                            });

                            $('#list_dashboard tbody input.changeDashboardStatus').off('change');
                            $('#list_dashboard tbody input.changeDashboardStatus').change(function() {
                                if($(this).prop('checked') === false)
                                {
                                    var newStatus = 0;
                                }
                                else
                                {
                                    var newStatus = 1;
                                }

                                $.ajax({
                                    url: "process-form.php",
                                    data: {
                                        modify_status_dashboard: true,
                                        dashboardId: $(this).parents('tr').attr('data-uniqueid'),
                                        newStatus: newStatus
                                    },
                                    type: "POST",
                                    async: true,
                                    success: function(data)
                                    {
                                        console.log("Success");
                                        if(data !== "Ok")
                                        {
                                            console.log("Error updating dashboard status");
                                            console.log(data);
                                            alert("Error updating dashboard status");
                                            location.reload();
                                        }
                                    },
                                    error: function(errorData)
                                    {
                                        console.log("Error updating dashboard status");
                                        console.log(errorData);
                                        alert("Error updating dashboard status");
                                        location.reload();
                                    }
                                });
                            });

                            $('#list_dashboard tbody span.glyphicon-cog').off('click');
                            $('#list_dashboard tbody span.glyphicon-cog').click(function() 
                            {
                                var dashboardId = $(this).parents('tr').attr('data-uniqueid');
                                location.href = "process-form.php?openDashboardToEdit=true&dashboardId=" + dashboardId;
                            });
                        }
                    });
                },
                error: function(errorData)
                {
                    console.log("KO");
                    console.log(errorData);
                }
            });

            $('#dashboardLogoInput').change(function ()
            {
                $('#dashboardLogoLinkInput').removeAttr('disabled');
            });

            $(function() 
            {
                $('.color-choice').colorpicker({format: "rgba"});
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

