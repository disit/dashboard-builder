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
    
    <!-- Bootstrap table -->
    <link rel="stylesheet" href="../boostrapTable/dist/bootstrap-table.css">
    <script src="../boostrapTable/dist/bootstrap-table.js"></script>
    <!-- Questa inclusione viene sempre DOPO bootstrap-table.js -->
    <script src="../boostrapTable/dist/locale/bootstrap-table-en-US.js"></script>

    <!-- Bootstrap slider -->
    <script src="../bootstrapSlider/bootstrap-slider.js"></script>
    <link href="../bootstrapSlider/css/bootstrap-slider.css" rel="stylesheet"/>

    <!-- Font awesome icons -->
    <link rel="stylesheet" href="../js/fontAwesome/css/font-awesome.min.css">
    
    <link href="https://fonts.googleapis.com/css?family=Cabin:400,500,600,700|Catamaran|Varela+Round" rel="stylesheet">
    
    <script type='text/javascript'>
        $(document).ready(function () {
            
            var internalDest = false;
            var tableFirstLoad = true;
            buildMainTable(false);
            
            $('#addDsConfirmBtn').click(function(){
                $('div.addDsDataRow').hide();
                $('#addDsModalFooter').hide();
                $('#addDsLoadingMsg').show();
                $('#addDsLoadingIcon').show();
                
                $.ajax({
                    url: "process-form.php",
                    data: {
                        addDs: true,
                        name: $('#dsName').val(),
                        url: $('#dsUrl').val(),
                        dbType: $('#dsDbType').val(),
                        dbName: $('#dsDbName').val(),
                        dbUsr: $('#dsDbUsr').val(),
                        dbPwd: $('#dsDbUsr').val()
                    },
                    type: "POST",
                    async: true,
                    success: function(data)
                    {
                        if(data !== 'Ok')
                        {
                            console.log("Error adding data source");
                            console.log(data);
                            $('#addDsLoadingMsg').hide();
                            $('#addDsLoadingIcon').hide();
                            $('#addDsKoMsg').show();
                            $('#addDsKoIcon').show();
                            setTimeout(function(){
                                $('#addDsKoMsg').hide();
                                $('#addDsKoIcon').hide();
                                $('div.addDsDataRow').show();
                                $('#addDsModalFooter').show();
                            }, 3000);
                        }
                        else
                        {
                            $('#addDsLoadingMsg').hide();
                            $('#addDsLoadingIcon').hide();
                            $('#addDsOkMsg').show();
                            $('#addDsOkIcon').show();
                            
                            setTimeout(function(){
                                $('#modalAddDs').modal('hide');
                                buildMainTable(true);
                                
                                setTimeout(function(){
                                    $('#addDsOkMsg').hide();
                                    $('#addDsOkIcon').hide();
                                    $('#dsName').val("");
                                    $('#dsUrl').val("");
                                    $('#dsDbType').val("");
                                    $('#dsDbName').val("");
                                    $('#dsDbUsr').val("");
                                    $('#dsDbPwd').val("");
                                    $('div.addDsDataRow').show();
                                    $('#addDsModalFooter').show();
                                }, 500);
                            }, 3000);
                        }
                    },
                    error: function(errorData)
                    {
                        $('#addDsLoadingMsg').hide();
                        $('#addDsLoadingIcon').hide();
                        $('#addDsKoMsg').show();
                        $('#addDsKoIcon').show();
                        setTimeout(function(){
                            $('#addDsKoMsg').hide();
                            $('#addDsKoIcon').hide();
                            $('div.addDsDataRow').show();
                            $('#addDsModalFooter').show();
                        }, 3000);
                        console.log("Error adding widget type");
                        console.log(errorData);
                    }
                });  
            });
            
            $('#editDsConfirmBtn').click(function(){
                $('div.editDsDataRow').hide();
                $('#editDsModalFooter').hide();
                $('#editDsLoadingMsg div.col-xs-12').html("Saving data, please wait");
                $('#editDsLoadingMsg').show();
                $('#editDsLoadingIcon').show();
                
                $.ajax({
                    url: "process-form.php",
                    data: {
                        updateDs: true,
                        id: $('#dsIdToEdit').val(),
                        name: $('#dsNameM').val(),
                        url: $('#dsUrlM').val(),
                        dbType: $('#dsDbTypeM').val(),
                        dbName: $('#dsDbNameM').val(),
                        dbUsr: $('#dsDbUsrM').val(),
                        dbPwd: $('#dsDbUsrM').val()
                    },
                    type: "POST",
                    datatype: 'json',
                    async: true,
                    success: function(data)
                    {
                        if(data !== 'Ok')
                        {
                            console.log("Error updating data source");
                            console.log(data);
                            $('#editDsLoadingMsg').hide();
                            $('#editDsLoadingIcon').hide();
                            $('#editDsKoMsg div.col-xs-12').html("Error updating data source");
                            $('#editDsKoMsg').show();
                            $('#editDsKoIcon').show();
                            setTimeout(function(){
                                $('#editDsKoMsg').hide();
                                $('#editDsKoIcon').hide();
                                $('div.editDsDataRow').show();
                                $('#editDsModalFooter').show();
                            }, 3000);
                        }
                        else
                        {
                            $('#editDsLoadingMsg').hide();
                            $('#editDsLoadingIcon').hide();
                            $('#editDsOkMsg').show();
                            $('#editDsOkIcon').show();
                            
                            setTimeout(function(){
                                $('#modalEditDs').modal('hide');
                                buildMainTable(true);
                                
                                setTimeout(function(){
                                    $('#dsNameM').val();
                                    $('#dsUrlM').val();
                                    $('#dsDbTypeM').val();
                                    $('#dsDbNameM').val();
                                    $('#dsDbUsrM').val();
                                    $('#dsDbUsrM').val();
                                    $('#editDsOkMsg').hide();
                                    $('#editDsOkIcon').hide();
                                    $('div.editDsDataRow').show();
                                    $('#editDsModalFooter').show();
                                }, 500);
                            }, 3000);
                        }
                    },
                    error: function(errorData)
                    {
                        console.log("Error updating data source");
                        console.log(errorData);
                        $('#editDsLoadingMsg').hide();
                        $('#editDsLoadingIcon').hide();
                        $('#editDsKoMsg div.col-xs-12').html("Error updating data source");
                        $('#editDsKoMsg').show();
                        $('#editDsKoIcon').show();
                        setTimeout(function(){
                            $('#editDsKoMsg').hide();
                            $('#editDsKoIcon').hide();
                            $('div.editDsDataRow').show();
                            $('#editDsModalFooter').show();
                        }, 3000);
                    }
                });  
            });
            
            $('#delDsConfirmBtn').click(function(){
                $('div.delDsDataRow').hide();
                $('#delDsModalFooter').hide();
                $('#delDsLoadingMsg').show();
                $('#delDsLoadingIcon').show();
                
                console.log("ID: " + $('#dsIdToDelete').val());
                
                $.ajax({
                    url: "process-form.php",
                    data: {
                        delDs: true,
                        id: $('#dsIdToDelete').val()
                    },
                    type: "POST",
                    async: true,
                    success:function(data)
                    {
                        if(data !== 'Ok')
                        {
                            console.log("Error deleting datasource");
                            console.log(data);
                            $('#delDsLoadingMsg').hide();
                            $('#delDsLoadingIcon').hide();
                            $('#delDsKoMsg').show();
                            $('#delDsKoIcon').show();
                            
                            setTimeout(function(){
                                $('#modalDelDs').modal('hide');
                                setTimeout(function(){
                                    $('#delDsKoMsg').hide();
                                    $('#delDsKoIcon').hide();
                                    $('div.delDsDataRow').show();
                                    $('#delDsModalFooter').show();
                                }, 500);
                            }, 3000);
                        }
                        else
                        {
                            $('#delDsLoadingMsg').hide();
                            $('#delDsLoadingIcon').hide();
                            $('#delDsOkMsg').show();
                            $('#delDsOkIcon').show();
                            
                            setTimeout(function(){
                                $('#modalDelDs').modal('hide');
                                buildMainTable(true);
                                
                                setTimeout(function(){
                                    $('#delDsOkMsg').hide();
                                    $('#delDsOkIcon').hide();
                                    $('div.delDsDataRow').show();
                                    $('#delDsModalFooter').show();
                                }, 500);
                            }, 3000);
                        }
                    },
                    error: function(errorData)
                    {
                        console.log("Error deleting datasource");
                        console.log(errorData);
                        $('#delDsLoadingMsg').hide();
                        $('#delDsLoadingIcon').hide();
                        $('#delDsKoMsg').show();
                        $('#delDsKoIcon').show();

                        setTimeout(function(){
                            $('#modalDelDs').modal('hide');
                            setTimeout(function(){
                                $('#delDsKoMsg').hide();
                                $('#delDsKoIcon').hide();
                                $('div.delDsDataRow').show();
                                $('#delDsModalFooter').show();
                            }, 500);
                        }, 3000);
                    }
                });  
            });
            
            function buildMainTable(destroyOld)
            {
                if(destroyOld)
                {
                    $('#dataSourcesTable').bootstrapTable('destroy');
                    tableFirstLoad = true;
                }
                
                $.ajax({
                    url: "get_data.php",
                    data: {action: "getDataSources"},
                    type: "GET",
                    async: true,
                    datatype: 'json',
                    success: function (data)
                    {
                        $('#dataSourcesTable').bootstrapTable({
                                columns: [{
                                    field: 'Id',
                                    title: 'Name',
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
                                    field: 'url',
                                    title: 'url',
                                    sortable: true,
                                    valign: "middle",
                                    align: "center",
                                    halign: "center",
                                    formatter: function(value, row, index)
                                    {
                                        if(value !== null)
                                        {
                                            if(value.length > 90)
                                            {
                                               return value.substr(0, 90) + " ...";
                                            }
                                            else
                                            {
                                               return value;
                                            } 
                                        }
                                    }
                                },
                                {
                                    field: 'database',
                                    title: 'Database',
                                    sortable: true,
                                    valign: "middle",
                                    align: "center",
                                    halign: "center",
                                },
                                {
                                    field: 'databaseType',
                                    title: 'Database type',
                                    sortable: true,
                                    valign: "middle",
                                    align: "center",
                                    halign: "center",
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
                                    title: "Delete",
                                    align: "center",
                                    valign: "middle",
                                    align: "center",
                                    halign: "center",
                                    formatter: function(value, row, index)
                                    {
                                        return '<span class="glyphicon glyphicon-remove"></span>'; 
                                    }
                                }],
                                data: data,
                                search: true,
                                pagination: true,
                                pageSize: 10,
                                locale: 'en-US',
                                searchAlign: 'left',
                                uniqueId: "intId",
                                striped: true,
                                onPostBody: function()
                                {
                                    if(tableFirstLoad)
                                    {
                                        //Caso di primo caricamento della tabella
                                        tableFirstLoad = false;
                                        var addDsDiv = $('<div class="pull-right"><i id="addDsBtn" data-toggle="modal" data-target="#modalAddDs" class="fa fa-plus-square" style="font-size:36px; color: #ffcc00"></i></div>');
                                        $('div.fixed-table-toolbar').append(addDsDiv);
                                        addDsDiv.css("margin-top", "10px");
                                        addDsDiv.find('i.fa-plus-square').off('hover');
                                        addDsDiv.find('i.fa-plus-square').hover(function(){
                                            $(this).css('color', 'red');
                                            $(this).css('cursor', 'pointer');
                                        }, 
                                        function(){
                                            $(this).css('color', '#ffcc00');
                                            $(this).css('cursor', 'normal');
                                        });
                                    }
                                    else
                                    {
                                        //Casi di cambio pagina
                                    }

                                    //Istruzioni da eseguire comunque
                                    $('#dataSourcesTable span.glyphicon-cog').css('color', '#337ab7');
                                    $('#dataSourcesTable span.glyphicon-cog').css('font-size', '20px');

                                    $('#dataSourcesTable span.glyphicon-cog').off('hover');
                                    $('#dataSourcesTable span.glyphicon-cog').hover(function(){
                                        $(this).css('color', '#ffcc00');
                                        $(this).css('cursor', 'pointer');
                                    }, 
                                    function(){
                                        $(this).css('color', '#337ab7');
                                        $(this).css('cursor', 'normal');
                                    });
                                    
                                    $('#dataSourcesTable span.glyphicon-cog').off('click');
                                    $('#dataSourcesTable span.glyphicon-cog').click(function(){
                                        $('#dsIdToEdit').val($(this).parents('tr').attr("data-uniqueid"));
                                        $('div.editDsDataRow').hide();
                                        $('#editDsConfirmBtn').hide();
                                        $('#editDsLoadingMsg div.col-xs-12').html('Retrieving datasource details, please wait');
                                        $('#editDsLoadingMsg').show();
                                        $('#editDsLoadingIcon').show();
                                        $('#modalEditDs').modal('show');
                                        
                                        $.ajax({
                                            url: "get_data.php",
                                            data: {
                                                action: "getSingleDataSource",
                                                id: $(this).parents('tr').attr('data-uniqueid')
                                            },
                                            type: "GET",
                                            async: true,
                                            datatype: 'json',
                                            success: function(data)
                                            {
                                                $('#editDsLoadingMsg').hide();
                                                $('#editDsLoadingIcon').hide();
                                                
                                                if(data.result !== "Ok")
                                                {
                                                    console.log("Error getting datasource details");
                                                    console.log(data);
                                                    $('#editDsConfirmBtn').show();
                                                    $('#editDsModalFooter').hide();
                                                    $('#editDsKoMsg').show();
                                                    $('#editDsKoMsg div.col-xs-12').html('Error retrieving datasource details');
                                                    $('#editDsKoIcon').show();
                                                    
                                                    setTimeout(function(){
                                                        $('#modalEditDs').modal('hide');
                                                        
                                                        setTimeout(function(){
                                                            $('#editDsKoMsg').hide();
                                                            $('#editDsKoIcon').hide();
                                                            $('div.editDsDataRow').show();
                                                            $('#editDsModalFooter').show();
                                                        }, 500);
                                                    }, 3000);
                                                }
                                                else
                                                {
                                                    $('div.editDsDataRow').show();
                                                    $('#editDsConfirmBtn').show();
                                                    $('#editDsModalFooter').show();
                                                    
                                                    $('#dsNameM').val(data.data.Id);
                                                    $('#dsUrlM').val(data.data.url);
                                                    $('#dsDbTypeM').val(data.data.databaseType);
                                                    $('#dsDbNameM').val(data.data.database);
                                                    $('#dsDbUsrM').val(data.data.username);
                                                    $('#dsDbPwdM').val(data.data.password);
                                                }
                                            },
                                            error: function(errorData)
                                            {
                                                console.log("Error getting datasource details");
                                                console.log(data);
                                                $('#editDsConfirmBtn').show();
                                                $('#editDsModalFooter').hide();
                                                $('#editDsKoMsg').show();
                                                $('#editDsKoMsg div.col-xs-12').html('Error retrieving datasource details');
                                                $('#editDsKoIcon').show();

                                                setTimeout(function(){
                                                    $('#modalEditDs').modal('hide');

                                                    setTimeout(function(){
                                                        $('#editDsKoMsg').hide();
                                                        $('#editDsKoIcon').hide();
                                                        $('div.editDsDataRow').show();
                                                        $('#editDsModalFooter').show();
                                                    }, 500);
                                                }, 3000);
                                            }
                                        });
                                    });

                                    $('#dataSourcesTable span.glyphicon-remove').css('color', 'red');
                                    $('#dataSourcesTable span.glyphicon-remove').css('font-size', '20px');

                                    $('#dataSourcesTable span.glyphicon-remove').off('hover');
                                    $('#dataSourcesTable span.glyphicon-remove').hover(function(){
                                        $(this).css('color', '#ffcc00');
                                        $(this).css('cursor', 'pointer');
                                    }, 
                                    function(){
                                        $(this).css('color', 'red');
                                        $(this).css('cursor', 'normal');
                                    });
                                    
                                    $('#dataSourcesTable span.glyphicon-remove').off('click');
                                    $('#dataSourcesTable span.glyphicon-remove').click(function(){
                                        $('#dsIdToDelete').val($(this).parents('tr').attr("data-uniqueid"));
                                        $('#delDsName').html($(this).parents('tr').find('td').eq(0).text());
                                        $('#modalDelDs').modal('show');
                                    });
                                }
                            });
                        }
                });
            }
        });
    </script>
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
                <a class="navbar-brand" href="index.html">Dashboard Management System</a>
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
                        <a href="../management/dashboard_mng.php" class="internalLink"> Dashboards management</a>
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
                                echo '<li class="active"><a class="internalLink" href="../management/dataSources_mng.php" id="link_sources_mng">Data sources management</a></li>';
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
                <div class="row" style="margin-top: 50px">
                    <div class="col-xs-12 centerWithFlex mainPageTitleContainer">
                        Data sources
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-xs-12">
                        <table id="dataSourcesTable"></table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Modale aggiunta datasource -->
    <div class="modal fade" id="modalAddDs" tabindex="-1" role="dialog" aria-labelledby="modalAddDsLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
          <div class="modal-content">
            <div class="modal-header centerWithFlex">
              <h5 class="modal-title" id="modalAddDsLabel">Add new datasource</h5>
            </div>
            
            <div id="addDsModalBody" class="modal-body">
                <div class="row addDsDataRow">
                    <div class="col-xs-6">
                        <div class="addUserFormSubfieldContainer">Name</div>
                        <div class="addUserFormSubfieldContainer">
                            <input type="text" id="dsName" name="dsName" class="form-control" required>
                        </div> 
                    </div>
                    <div class="col-xs-6">
                        <div class="addUserFormSubfieldContainer">URL</div>
                        <div class="addUserFormSubfieldContainer">
                            <input type="text" class="form-control" name="dsUrl" id="dsUrl" required> 
                        </div>
                    </div>
                </div>
                <div class="row addDsDataRow">
                    <div class="col-xs-6">
                        <div class="addUserFormSubfieldContainer">Database type</div>
                        <div class="addUserFormSubfieldContainer">
                            <input type="text" class="form-control" name="dsDbType" id="dsDbType" required> 
                        </div>
                    </div>
                   <div class="col-xs-6">
                        <div class="addUserFormSubfieldContainer">Database name</div>
                        <div class="addUserFormSubfieldContainer">
                            <input type="text" class="form-control" name="dsDbName" id="dsDbName" required> 
                        </div>
                    </div>
                </div>
                <div class="row addDsDataRow">
                    <div class="col-xs-6">
                        <div class="addUserFormSubfieldContainer">Database username</div>
                        <div class="addUserFormSubfieldContainer">
                            <input type="text" class="form-control" name="dsDbUsr" id="dsDbUsr" required> 
                        </div> 
                    </div>
                    <div class="col-xs-6">
                        <div class="addUserFormSubfieldContainer">Database password</div>
                        <div class="addUserFormSubfieldContainer">
                            <input type="password" class="form-control" name="dsDbPwd" id="dsDbPwd" required> 
                        </div> 
                    </div>
                </div>
                <div class="row" id="addDsLoadingMsg">
                    <div class="col-xs-12 centerWithFlex">Adding datasource, please wait</div>
                </div>
                <div class="row" id="addDsLoadingIcon">
                    <div class="col-xs-12 centerWithFlex"><i class="fa fa-circle-o-notch fa-spin" style="font-size:36px;"></i></div>
                </div>
                <div class="row" id="addDsOkMsg">
                    <div class="col-xs-12 centerWithFlex">Datasource added successfully</div>
                </div>
                <div class="row" id="addDsOkIcon">
                    <div class="col-xs-12 centerWithFlex"><i class="fa fa-thumbs-o-up" style="font-size:36px"></i></div>
                </div>
                <div class="row" id="addDsKoMsg">
                    <div class="col-xs-12 centerWithFlex">Error adding datasource</div>
                </div>
                <div class="row" id="addDsKoIcon">
                    <div class="col-xs-12 centerWithFlex"><i class="fa fa-thumbs-o-down" style="font-size:36px"></i></div>
                </div>
            </div>
            <div id="addDsModalFooter" class="modal-footer">
              <button type="button" id="addDsCancelBtn" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
              <button type="button" id="addDsConfirmBtn" name="addDs" class="btn btn-primary internalLink">Confirm</button>
            </div>
          </div>
        </div>
    </div>
    
    <!-- Modale modifica datasource -->
    <div class="modal fade" id="modalEditDs" tabindex="-1" role="dialog" aria-labelledby="modalEditDsLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
          <div class="modal-content">
            <div class="modal-header centerWithFlex">
              <h5 class="modal-title" id="modalEditDsLabel">Update datasource</h5>
            </div>
            <input type="hidden" id="dsIdToEdit" >
            <div id="editDsModalBody" class="modal-body">
                <div class="row editDsDataRow">
                    <div class="col-xs-6">
                        <div class="addUserFormSubfieldContainer">Name</div>
                        <div class="addUserFormSubfieldContainer">
                            <input type="text" id="dsNameM" name="dsNameM" class="form-control" required>
                        </div> 
                    </div>
                    <div class="col-xs-6">
                        <div class="addUserFormSubfieldContainer">URL</div>
                        <div class="addUserFormSubfieldContainer">
                            <input type="text" class="form-control" name="dsUrlM" id="dsUrlM" required> 
                        </div>
                    </div>
                </div>
                <div class="row editDsDataRow">
                    <div class="col-xs-6">
                        <div class="addUserFormSubfieldContainer">Database type</div>
                        <div class="addUserFormSubfieldContainer">
                            <input type="text" class="form-control" name="dsDbTypeM" id="dsDbTypeM" required> 
                        </div>
                    </div>
                   <div class="col-xs-6">
                        <div class="addUserFormSubfieldContainer">Database name</div>
                        <div class="addUserFormSubfieldContainer">
                            <input type="text" class="form-control" name="dsDbNameM" id="dsDbNameM" required> 
                        </div>
                    </div>
                </div>
                <div class="row editDsDataRow">
                    <div class="col-xs-6">
                        <div class="addUserFormSubfieldContainer">Database username</div>
                        <div class="addUserFormSubfieldContainer">
                            <input type="text" class="form-control" name="dsDbUsrM" id="dsDbUsrM" required> 
                        </div> 
                    </div>
                    <div class="col-xs-6">
                        <div class="addUserFormSubfieldContainer">Database password</div>
                        <div class="addUserFormSubfieldContainer">
                            <input type="password" class="form-control" name="dsDbPwdM" id="dsDbPwdM" required> 
                        </div> 
                    </div>
                </div>
                <div class="row" id="editDsLoadingMsg">
                    <div class="col-xs-12 centerWithFlex"></div>
                </div>
                <div class="row" id="editDsLoadingIcon">
                    <div class="col-xs-12 centerWithFlex"><i class="fa fa-circle-o-notch fa-spin" style="font-size:36px;"></i></div>
                </div>
                <div class="row" id="editDsOkMsg">
                    <div class="col-xs-12 centerWithFlex">Datasource updated successfully</div>
                </div>
                <div class="row" id="editDsOkIcon">
                    <div class="col-xs-12 centerWithFlex"><i class="fa fa-thumbs-o-up" style="font-size:36px"></i></div>
                </div>
                <div class="row" id="editDsKoMsg">
                    <div class="col-xs-12 centerWithFlex"></div>
                </div>
                <div class="row" id="editDsKoIcon">
                    <div class="col-xs-12 centerWithFlex"><i class="fa fa-thumbs-o-down" style="font-size:36px"></i></div>
                </div>
            </div>
            <div id="editDsModalFooter" class="modal-footer">
              <button type="button" id="editDsCancelBtn" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
              <button type="button" id="editDsConfirmBtn" name="addDs" class="btn btn-primary internalLink">Confirm</button>
            </div>
          </div>
        </div>
    </div>
    
    <!-- Modale cancellazione datasource -->
    <div class="modal fade" id="modalDelDs" tabindex="-1" role="dialog" aria-labelledby="modalDelDsLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
          <div class="modal-content">
            <div class="modal-header centerWithFlex">
              <h5 class="modal-title" id="modalAddDsLabel">Delete widget type</h5>
            </div>
            <input type="hidden" id="dsIdToDelete" />
            <div id="delDsModalBody" class="modal-body">
                <div class="row delDsDataRow">
                    <div class="col-xs-12">
                        <div class="addUserFormSubfieldContainer">Do you want to confirm cancellation of the following datasource?</div>
                        <div class="addUserFormSubfieldContainer" id="delDsName"></div> 
                    </div>
                </div>
                <div class="row" id="delDsLoadingMsg">
                    <div class="col-xs-12 centerWithFlex">Deleting data source, please wait</div>
                </div>
                <div class="row" id="delDsLoadingIcon">
                    <div class="col-xs-12 centerWithFlex"><i class="fa fa-circle-o-notch fa-spin" style="font-size:36px;"></i></div>
                </div>
                <div class="row" id="delDsOkMsg">
                    <div class="col-xs-12 centerWithFlex">Data source deleted successfully</div>
                </div>
                <div class="row" id="delDsOkIcon">
                    <div class="col-xs-12 centerWithFlex"><i class="fa fa-thumbs-o-up" style="font-size:36px"></i></div>
                </div>
                <div class="row" id="delDsKoMsg">
                    <div class="col-xs-12 centerWithFlex">Error deleting data source</div>
                </div>
                <div class="row" id="delDsKoIcon">
                    <div class="col-xs-12 centerWithFlex"><i class="fa fa-thumbs-o-down" style="font-size:36px"></i></div>
                </div>
            </div>
            <div id="delDsModalFooter" class="modal-footer">
              <button type="button" id="delDsCancelBtn" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
              <button type="button" id="delDsConfirmBtn" name="delDs" class="btn btn-primary internalLink">Confirm</button>
            </div>
             
          </div>
        </div>
    </div>
    
</body>
</html>   
   

