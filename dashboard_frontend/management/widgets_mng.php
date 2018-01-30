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
        var array_widget = new Array();
        var array_metrics = new Array();
        var array_types = new Array();
        var tipi_compatibili;

        $(document).ready(function () 
        {
            var internalDest = false;
            var tableFirstLoad = true;
            
            buildMainTable(false);
            
            $('#minWidth').bootstrapSlider({
                tooltip_position:'left'
            });
            
            $('#maxWidth').bootstrapSlider({
                tooltip_position: 'left'
            });
            
            $('#minHeight').bootstrapSlider({
                tooltip_position:'left'
            });
            
            $('#maxHeight').bootstrapSlider({
                tooltip_position: 'left'
            });
            
            $('#minWidthM').bootstrapSlider({
                tooltip_position:'left'
            });
            
            $('#maxWidthM').bootstrapSlider({
                tooltip_position: 'left'
            });
            
            $('#minHeightM').bootstrapSlider({
                tooltip_position:'left'
            });
            
            $('#maxHeightM').bootstrapSlider({
                tooltip_position: 'left'
            });
            
            $('#addWidgetTypeConfirmBtn').click(function(){
                $('div.addWidgetTypeDataRow').hide();
                $('#addWidgetTypeModalFooter').hide();
                $('#addWidgetTypeLoadingMsg').show();
                $('#addWidgetTypeLoadingIcon').show();
                
                $.ajax({
                    url: "process-form.php",
                    data: {
                        addWidgetType: true,
                        widgetName: $('#widgetName').val(),
                        phpFilename: $('#phpFilename').val(),
                        metricsNumber: $('#metricsNumber').val(),
                        metricType: $('#metricType').val(),
                        uniqueMetric: $('#uniqueMetric').val(),
                        minWidth: $('#minWidth').bootstrapSlider('getValue'),
                        maxWidth: $('#maxWidth').bootstrapSlider('getValue'),
                        minHeight: $('#minHeight').bootstrapSlider('getValue'),
                        maxHeight: $('#maxHeight').bootstrapSlider('getValue')
                    },
                    type: "POST",
                    async: true,
                    success: function(data)
                    {
                        if(data !== 'Ok')
                        {
                            console.log("Error adding widget type");
                            console.log(data);
                            $('#addWidgetTypeLoadingMsg').hide();
                            $('#addWidgetTypeLoadingIcon').hide();
                            $('#addWidgetTypeKoMsg').show();
                            $('#addWidgetTypeKoIcon').show();
                            setTimeout(function(){
                                $('#addWidgetTypeKoMsg').hide();
                                $('#addWidgetTypeKoIcon').hide();
                                $('div.addWidgetTypeDataRow').show();
                                $('#addWidgetTypeModalFooter').show();
                            }, 3000);
                        }
                        else
                        {
                            $('#addWidgetTypeLoadingMsg').hide();
                            $('#addWidgetTypeLoadingIcon').hide();
                            $('#addWidgetTypeOkMsg').show();
                            $('#addWidgetTypeOkIcon').show();
                            
                            setTimeout(function(){
                                $('#modalAddWidgetType').modal('hide');
                                buildMainTable(true);
                                
                                setTimeout(function(){
                                    $('#addWidgetTypeOkMsg').hide();
                                    $('#addWidgetTypeOkIcon').hide();
                                    $('#widgetName').val("");
                                    $('#phpFilename').val("");
                                    $('#metricsNumber').val("");
                                    $('#metricType').val("");
                                    $('#uniqueMetric').val("");
                                    $('#minWidth').bootstrapSlider('setValue', 1);
                                    $('#maxWidth').bootstrapSlider('setValue', 50);
                                    $('#minHeight').bootstrapSlider('setValue', 2);
                                    $('#maxHeight').bootstrapSlider('setValue', 50);
                                    $('div.addWidgetTypeDataRow').show();
                                    $('#addWidgetTypeModalFooter').show();
                                }, 500);
                            }, 3000);
                        }
                    },
                    error: function(errorData)
                    {
                        $('#addWidgetTypeLoadingMsg').hide();
                        $('#addWidgetTypeLoadingIcon').hide();
                        $('#addWidgetTypeKoMsg').show();
                        $('#addWidgetTypeKoIcon').show();
                        setTimeout(function(){
                            $('#addWidgetTypeKoMsg').hide();
                            $('#addWidgetTypeKoIcon').hide();
                            $('div.addWidgetTypeDataRow').show();
                            $('#addWidgetTypeModalFooter').show();
                        }, 3000);
                        console.log("Error adding widget type");
                        console.log(errorData);
                    }
                });  
            });
            
            $('#editWidgetTypeConfirmBtn').click(function(){
                $('div.editWidgetTypeDataRow').hide();
                $('#editWidgetTypeModalFooter').hide();
                $('#editWidgetTypeLoadingMsg div.col-xs-12').html("Saving data, please wait");
                $('#editWidgetTypeLoadingMsg').show();
                $('#editWidgetTypeLoadingIcon').show();
                
                $.ajax({
                    url: "process-form.php",
                    data: {
                        editWidgetType: true,
                        id: $('#widgetIdToEdit').val(),
                        widgetName: $('#widgetNameM').val(),
                        phpFilename: $('#phpFilenameM').val(),
                        metricsNumber: $('#metricsNumberM').val(),
                        metricType: $('#metricTypeM').val(),
                        uniqueMetric: $('#uniqueMetricM').val(),
                        minWidth: $('#minWidthM').bootstrapSlider('getValue'),
                        maxWidth: $('#maxWidthM').bootstrapSlider('getValue'),
                        minHeight: $('#minHeightM').bootstrapSlider('getValue'),
                        maxHeight: $('#maxHeightM').bootstrapSlider('getValue')
                    },
                    type: "POST",
                    datatype: 'json',
                    async: true,
                    success: function(data)
                    {
                        if(data !== 'Ok')
                        {
                            console.log("Error updating widget type");
                            console.log(data);
                            $('#editWidgetTypeLoadingMsg').hide();
                            $('#editWidgetTypeLoadingIcon').hide();
                            $('#editWidgetTypeKoMsg div.col-xs-12').html("Error updating widget type");
                            $('#editWidgetTypeKoMsg').show();
                            $('#editWidgetTypeKoIcon').show();
                            setTimeout(function(){
                                $('#editWidgetTypeKoMsg').hide();
                                $('#editWidgetTypeKoIcon').hide();
                                $('div.editWidgetTypeDataRow').show();
                                $('#editWidgetTypeModalFooter').show();
                            }, 3000);
                        }
                        else
                        {
                            $('#editWidgetTypeLoadingMsg').hide();
                            $('#editWidgetTypeLoadingIcon').hide();
                            $('#editWidgetTypeOkMsg').show();
                            $('#editWidgetTypeOkIcon').show();
                            
                            setTimeout(function(){
                                $('#modalEditWidgetType').modal('hide');
                                buildMainTable(true);
                                
                                setTimeout(function(){
                                    $('#editWidgetTypeOkMsg').hide();
                                    $('#editWidgetTypeOkIcon').hide();
                                    $('#widgetNameM').val("");
                                    $('#phpFilenameM').val("");
                                    $('#metricsNumberM').val("");
                                    $('#metricTypeM').val("");
                                    $('#uniqueMetricM').val("");
                                    $('#minWidthM').bootstrapSlider('setValue', 1);
                                    $('#maxWidthM').bootstrapSlider('setValue', 50);
                                    $('#minHeightM').bootstrapSlider('setValue', 2);
                                    $('#maxHeightM').bootstrapSlider('setValue', 50);
                                    $('div.editWidgetTypeDataRow').show();
                                    $('#editWidgetTypeModalFooter').show();
                                }, 500);
                            }, 3000);
                        }
                    },
                    error: function(errorData)
                    {
                        console.log("Error updating widget type");
                        console.log(errorData);
                        $('#editWidgetTypeLoadingMsg').hide();
                        $('#editWidgetTypeLoadingIcon').hide();
                        $('#editWidgetTypeKoMsg div.col-xs-12').html("Error updating widget type");
                        $('#editWidgetTypeKoMsg').show();
                        $('#editWidgetTypeKoIcon').show();
                        setTimeout(function(){
                            $('#editWidgetTypeKoMsg').hide();
                            $('#editWidgetTypeKoIcon').hide();
                            $('div.editWidgetTypeDataRow').show();
                            $('#editWidgetTypeModalFooter').show();
                        }, 3000);
                    }
                });  
            });
            
            $('#delWidgetTypeConfirmBtn').click(function(){
                $('div.delWidgetTypeDataRow').hide();
                $('#delWidgetTypeModalFooter').hide();
                $('#delWidgetTypeLoadingMsg').show();
                $('#delWidgetTypeLoadingIcon').show();
                
                $.ajax({
                    url: "process-form.php",
                    data: {
                        delWidgetType: true,
                        id: $('#widgetIdToDelete').val()
                    },
                    type: "POST",
                    async: true,
                    success: function(data)
                    {
                        if(data !== 'Ok')
                        {
                            console.log("Error deleting widget type");
                            console.log(data);
                            $('#delWidgetTypeLoadingMsg').hide();
                            $('#delWidgetTypeLoadingIcon').hide();
                            $('#delWidgetTypeKoMsg').show();
                            $('#delWidgetTypeKoIcon').show();
                            
                            setTimeout(function(){
                                $('#modalDelWidgetType').modal('hide');
                                setTimeout(function(){
                                    $('#delWidgetTypeKoMsg').hide();
                                    $('#delWidgetTypeKoIcon').hide();
                                    $('div.delWidgetTypeDataRow').show();
                                    $('#delWidgetTypeModalFooter').show();
                                }, 500);
                            }, 3000);
                        }
                        else
                        {
                            $('#delWidgetTypeLoadingMsg').hide();
                            $('#delWidgetTypeLoadingIcon').hide();
                            $('#delWidgetTypeOkMsg').show();
                            $('#delWidgetTypeOkIcon').show();
                            
                            setTimeout(function(){
                                $('#modalDelWidgetType').modal('hide');
                                buildMainTable(true);
                                
                                setTimeout(function(){
                                    $('#delWidgetTypeOkMsg').hide();
                                    $('#delWidgetTypeOkIcon').hide();
                                    $('div.delWidgetTypeDataRow').show();
                                    $('#delWidgetTypeModalFooter').show();
                                }, 500);
                            }, 3000);
                        }
                    },
                    error: function(errorData)
                    {
                        console.log("Error deleting widget type");
                        console.log(errorData);
                        $('#delWidgetTypeLoadingMsg').hide();
                        $('#delWidgetTypeLoadingIcon').hide();
                        $('#delWidgetTypeKoMsg').show();
                        $('#delWidgetTypeKoIcon').show();

                        setTimeout(function(){
                            $('#modalDelWidgetType').modal('hide');
                            setTimeout(function(){
                                $('#delWidgetTypeKoMsg').hide();
                                $('#delWidgetTypeKoIcon').hide();
                                $('div.delWidgetTypeDataRow').show();
                                $('#delWidgetTypeModalFooter').show();
                            }, 500);
                        }, 3000);
                    }
                });  
            });
            
            function buildMainTable(destroyOld)
            {
                if(destroyOld)
                {
                    $('#list_widgets').bootstrapTable('destroy');
                    tableFirstLoad = true;
                }
                
                $.ajax({
                    url: "get_data.php",
                    data: {action: "get_widget_types"},
                    type: "GET",
                    async: true,
                    datatype: 'json',
                    success: function (data)
                    {
                        $('#list_widgets').bootstrapTable({
                                columns: [{
                                    field: 'id_type_widget',
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
                                    field: 'min_row',
                                    title: 'Min height',
                                    sortable: true,
                                    align: "center",
                                    halign: "center",
                                    valign: "middle"
                                },
                                {
                                    field: 'max_row',
                                    title: 'Max height',
                                    sortable: true,
                                    align: "center",
                                    halign: "center",
                                    valign: "middle"
                                },
                                {
                                    field: 'min_col',
                                    title: 'Min width',
                                    sortable: true,
                                    align: "center",
                                    halign: "center",
                                    valign: "middle"
                                },
                                {
                                    field: 'max_col',
                                    title: 'Max width',
                                    sortable: true,
                                    align: "center",
                                    halign: "center",
                                    valign: "middle"
                                },
                                {
                                    field: 'widgetType',
                                    title: "Data type(s)",
                                    align: "center",
                                    sortable: true,
                                    align: "center",
                                    halign: "center",
                                    valign: "middle"
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
                                uniqueId: "id",
                                striped: true,
                                onPostBody: function()
                                {
                                    if(tableFirstLoad)
                                    {
                                        //Caso di primo caricamento della tabella
                                        tableFirstLoad = false;
                                        var addWidgetDiv = $('<div class="pull-right"><i id="addWidgetTypeBtn" data-toggle="modal" data-target="#modalAddWidgetType" class="fa fa-plus-square" style="font-size:36px; color: #ffcc00"></i></div>');
                                        $('div.fixed-table-toolbar').append(addWidgetDiv);
                                        addWidgetDiv.css("margin-top", "10px");
                                        addWidgetDiv.find('i.fa-plus-square').off('hover');
                                        addWidgetDiv.find('i.fa-plus-square').hover(function(){
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
                                    $('#list_widgets span.glyphicon-cog').css('color', '#337ab7');
                                    $('#list_widgets span.glyphicon-cog').css('font-size', '20px');

                                    $('#list_widgets span.glyphicon-cog').off('hover');
                                    $('#list_widgets span.glyphicon-cog').hover(function(){
                                        $(this).css('color', '#ffcc00');
                                        $(this).css('cursor', 'pointer');
                                    }, 
                                    function(){
                                        $(this).css('color', '#337ab7');
                                        $(this).css('cursor', 'normal');
                                    });
                                    
                                    $('#list_widgets span.glyphicon-cog').off('click');
                                    $('#list_widgets span.glyphicon-cog').click(function(){
                                        $('#widgetIdToEdit').val($(this).parents('tr').attr("data-uniqueid"));
                                        $('div.editWidgetTypeDataRow').hide();
                                        $('#editWidgetTypeConfirmBtn').hide();
                                        $('#editWidgetTypeLoadingMsg div.col-xs-12').html('Retrieving widget type data, please wait');
                                        $('#editWidgetTypeLoadingMsg').show();
                                        $('#editWidgetTypeLoadingIcon').show();
                                        $('#modalEditWidgetType').modal('show');
                                        
                                        $.ajax({
                                            url: "get_data.php",
                                            data: {
                                                action: "get_single_widget_type",
                                                id: $(this).parents('tr').attr('data-uniqueid')
                                            },
                                            type: "GET",
                                            async: true,
                                            datatype: 'json',
                                            success: function(data)
                                            {
                                                $('#editWidgetTypeLoadingMsg').hide();
                                                $('#editWidgetTypeLoadingIcon').hide();
                                                
                                                if(data.result !== "Ok")
                                                {
                                                    console.log("Error getting widget type data");
                                                    console.log(data);
                                                    $('#editWidgetTypeConfirmBtn').show();
                                                    $('#editWidgetTypeModalFooter').hide();
                                                    $('#editWidgetTypeKoMsg').show();
                                                    $('#editWidgetTypeKoMsg div.col-xs-12').html('Error retrieving widget type data');
                                                    $('#editWidgetTypeKoIcon').show();
                                                    
                                                    setTimeout(function(){
                                                        $('#modalEditWidgetType').modal('hide');
                                                        
                                                        setTimeout(function(){
                                                            $('#editWidgetTypeKoMsg').hide();
                                                            $('#editWidgetTypeKoIcon').hide();
                                                            $('div.editWidgetTypeDataRow').show();
                                                            $('#editWidgetTypeModalFooter').show();
                                                        }, 500);
                                                    }, 3000);
                                                }
                                                else
                                                {
                                                    $('div.editWidgetTypeDataRow').show();
                                                    $('#editWidgetTypeConfirmBtn').show();
                                                    $('#editWidgetTypeModalFooter').show();
                                                    
                                                    $('#widgetNameM').val(data.data.id_type_widget);
                                                    $('#phpFilenameM').val(data.data.source_php_widget);
                                                    $('#metricsNumberM').val(data.data.number_metrics_widget);
                                                    $('#metricTypeM').val(data.data.widgetType);
                                                    $('#uniqueMetricM').val(data.data.unique_metric);
                                                    $('#minWidthM').bootstrapSlider('setValue', data.data.min_col);
                                                    $('#maxWidthM').bootstrapSlider('setValue', data.data.max_col);
                                                    $('#minHeightM').bootstrapSlider('setValue', data.data.min_row);
                                                    $('#maxHeightM').bootstrapSlider('setValue', data.data.max_row);
                                                }
                                            },
                                            error: function(errorData)
                                            {
                                                console.log("Error getting widget type data");
                                                console.log(data);
                                                $('#editWidgetTypeLoadingMsg').hide();
                                                $('#editWidgetTypeLoadingIcon').hide();
                                                $('#editWidgetTypeConfirmBtn').show();
                                                $('#editWidgetTypeModalFooter').hide();
                                                $('#editWidgetTypeKoMsg').show();
                                                $('#editWidgetTypeKoMsg div.col-xs-12').html('Error retrieving widget type data');
                                                $('#editWidgetTypeKoIcon').show();

                                                setTimeout(function(){
                                                    $('#modalEditWidgetType').modal('hide');

                                                    setTimeout(function(){
                                                        $('#editWidgetTypeKoMsg').hide();
                                                        $('#editWidgetTypeKoIcon').hide();
                                                        $('div.editWidgetTypeDataRow').show();
                                                        $('#editWidgetTypeModalFooter').show();
                                                    }, 500);
                                                }, 3000);
                                            }
                                        });
                                        
                                    });

                                    $('#list_widgets span.glyphicon-remove').css('color', 'red');
                                    $('#list_widgets span.glyphicon-remove').css('font-size', '20px');

                                    $('#list_widgets span.glyphicon-remove').off('hover');
                                    $('#list_widgets span.glyphicon-remove').hover(function(){
                                        $(this).css('color', '#ffcc00');
                                        $(this).css('cursor', 'pointer');
                                    }, 
                                    function(){
                                        $(this).css('color', 'red');
                                        $(this).css('cursor', 'normal');
                                    });
                                    
                                    $('#list_widgets span.glyphicon-remove').off('click');
                                    $('#list_widgets span.glyphicon-remove').click(function(){
                                        $('#widgetIdToDelete').val($(this).parents('tr').attr("data-uniqueid"));
                                        $('#delWidgetName').html($(this).parents('tr').find('td').eq(0).text());
                                        $('#modalDelWidgetType').modal('show');
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
        else if($_SESSION['loggedRole'] != "ToolAdmin")
        {
            echo '<script type="text/javascript">';
            echo 'window.location.href = "unauthorizedUser.php";';
            echo '</script>';
        }
    ?>
    <div id="wrapper">
        <nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-ex1-collapse">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="index.html">Dashboard Management System</a>
            </div>
            <ul class="nav navbar-right top-nav">
                <?php
                    if(isset($_SESSION['loggedUsername']))
                    {
                        echo '<li><a href="#"><span class="glyphicon glyphicon-user" aria-hidden="true"></span>' . $_SESSION["loggedUsername"] . '</a></li>';
                        echo '<li><a href="logout.php">Logout</a></li>';
                    }
                ?>
            </ul>
            <div class="collapse navbar-collapse navbar-ex1-collapse">
                <ul class="nav navbar-nav side-nav">
                    <li>
                        <a href="../management/dashboard_mng.php"> Dashboards management</a>
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
                                echo '<li class="active"><a class="internalLink" href="../management/widgets_mng.php" id="link_widgets_mng">Widgets management</a></li>';
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
        </nav>

        <div id="page-wrapper">
            <div class="container-fluid">
                <div class="row" style="margin-top: 50px">
                    <div class="col-xs-12 centerWithFlex mainPageTitleContainer">
                        Widgets
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-xs-12">
                        <table id="list_widgets"></table>
                    </div>
                </div>
            </div>
        </div>    
    </div>
    
    <!-- Modale aggiunta tipo di widget -->
    <div class="modal fade" id="modalAddWidgetType" tabindex="-1" role="dialog" aria-labelledby="modalAddWidgetTypeLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
          <div class="modal-content">
            <div class="modal-header centerWithFlex">
              <h5 class="modal-title" id="modalAddWidgetTypeLabel">Add new widget type</h5>
            </div>
            
            <div id="addWidgetTypeModalBody" class="modal-body">
                    <div class="row addWidgetTypeDataRow">
                        <div class="col-xs-6 col-xs-offset-3">
                            <div class="addUserFormSubfieldContainer">Widget name</div>
                            <div class="addUserFormSubfieldContainer">
                                <input type="text" id="widgetName" name="widgetName" class="form-control" required>
                            </div> 
                        </div>
                    </div>
                    <div class="row addWidgetTypeDataRow">
                        <div class="col-xs-6">
                            <div class="addUserFormSubfieldContainer">PHP filename</div>
                            <div class="addUserFormSubfieldContainer">
                                <input type="text" class="form-control" name="phpFilename" id="phpFilename" required> 
                            </div>
                        </div>
                       <div class="col-xs-6">
                            <div class="addUserFormSubfieldContainer">Number of managed metrics</div>
                            <div class="addUserFormSubfieldContainer">
                                <input type="text" class="form-control" name="metricsNumber" id="metricsNumber" value="1" required> 
                            </div>
                        </div>
                    </div>
                    <div class="row addWidgetTypeDataRow">
                        <div class="col-xs-6">
                            <div class="addUserFormSubfieldContainer">Metric type(s)</div>
                            <div class="addUserFormSubfieldContainer">
                                <input type="text" class="form-control" name="metricType" id="metricType" required> 
                            </div> 
                        </div>
                        <div class="col-xs-6">
                            <div class="addUserFormSubfieldContainer">Unique metric managed</div>
                            <div class="addUserFormSubfieldContainer">
                                <input type="text" class="form-control" name="uniqueMetric" id="uniqueMetric">
                            </div>
                        </div>
                    </div>
                    <div class="row addWidgetTypeDataRow">
                        <div class="col-xs-6">
                            <div class="addUserFormSubfieldContainer">Min width</div>
                            <div class="addUserFormSubfieldContainer">
                                <input id="minWidth" name="minWidth" data-slider-id="minWidthSlider" type="text" data-slider-min="1" data-slider-max="100" data-slider-step="1" data-slider-value="1"/>
                            </div> 
                        </div>
                        <div class="col-xs-6">
                            <div class="addUserFormSubfieldContainer">Max width</div>
                            <div class="addUserFormSubfieldContainer">
                                <input id="maxWidth" name="maxWidth" data-slider-id="maxWidthSlider" type="text" data-slider-min="1" data-slider-max="100" data-slider-step="1" data-slider-value="50"/>
                            </div> 
                        </div>
                    </div>
                    <div class="row addWidgetTypeDataRow">
                        <div class="col-xs-6">
                            <div class="addUserFormSubfieldContainer">Min height</div>
                            <div class="addUserFormSubfieldContainer">
                                <input id="minHeight" name="minHeight" data-slider-id="minHeightSlider" type="text" data-slider-min="1" data-slider-max="100" data-slider-step="1" data-slider-value="2"/>
                            </div> 
                        </div>
                        <div class="col-xs-6">
                            <div class="addUserFormSubfieldContainer">Max height</div>
                            <div class="addUserFormSubfieldContainer">
                                <input id="maxHeight" name="maxHeight" data-slider-id="maxHeightSlider" type="text" data-slider-min="1" data-slider-max="100" data-slider-step="1" data-slider-value="50"/>
                            </div> 
                        </div>
                    </div>
                    <div class="row" id="addWidgetTypeLoadingMsg">
                        <div class="col-xs-12 centerWithFlex">Adding widget type, please wait</div>
                    </div>
                    <div class="row" id="addWidgetTypeLoadingIcon">
                        <div class="col-xs-12 centerWithFlex"><i class="fa fa-circle-o-notch fa-spin" style="font-size:36px;"></i></div>
                    </div>
                    <div class="row" id="addWidgetTypeOkMsg">
                        <div class="col-xs-12 centerWithFlex">Widget type added successfully</div>
                    </div>
                    <div class="row" id="addWidgetTypeOkIcon">
                        <div class="col-xs-12 centerWithFlex"><i class="fa fa-thumbs-o-up" style="font-size:36px"></i></div>
                    </div>
                    <div class="row" id="addWidgetTypeKoMsg">
                        <div class="col-xs-12 centerWithFlex">Error adding widget type</div>
                    </div>
                    <div class="row" id="addWidgetTypeKoIcon">
                        <div class="col-xs-12 centerWithFlex"><i class="fa fa-thumbs-o-down" style="font-size:36px"></i></div>
                    </div>
            </div>
            <div id="addWidgetTypeModalFooter" class="modal-footer">
              <button type="button" id="addWidgetTypeCancelBtn" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
              <button type="button" id="addWidgetTypeConfirmBtn" name="addWidgetType" class="btn btn-primary internalLink">Confirm</button>
            </div>
          </div>
        </div>
    </div>
    
    <!-- Modale modifica tipo di widget -->
    <div class="modal fade" id="modalEditWidgetType" tabindex="-1" role="dialog" aria-labelledby="modalEditWidgetTypeLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
          <div class="modal-content">
            <div class="modal-header centerWithFlex">
              <h5 class="modal-title" id="modalEditWidgetTypeLabel">Edit widget type</h5>
            </div>
            
            <div id="editWidgetTypeModalBody" class="modal-body">
                <input type="hidden" id="widgetIdToEdit" />
                    <div class="row editWidgetTypeDataRow">
                        <div class="col-xs-6 col-xs-offset-3">
                            <div class="addUserFormSubfieldContainer">Widget name</div>
                            <div class="addUserFormSubfieldContainer">
                                <input type="text" id="widgetNameM" name="widgetNameM" class="form-control" required>
                            </div> 
                        </div>
                    </div>
                    <div class="row editWidgetTypeDataRow">
                        <div class="col-xs-6">
                            <div class="addUserFormSubfieldContainer">PHP filename</div>
                            <div class="addUserFormSubfieldContainer">
                                <input type="text" class="form-control" name="phpFilenameM" id="phpFilenameM" required> 
                            </div>
                        </div>
                       <div class="col-xs-6">
                            <div class="addUserFormSubfieldContainer">Number of managed metrics</div>
                            <div class="addUserFormSubfieldContainer">
                                <input type="text" class="form-control" name="metricsNumberM" id="metricsNumberM" required> 
                            </div>
                        </div>
                    </div>
                    <div class="row editWidgetTypeDataRow">
                        <div class="col-xs-6">
                            <div class="addUserFormSubfieldContainer">Metric type(s)</div>
                            <div class="addUserFormSubfieldContainer">
                                <input type="text" class="form-control" name="metricTypeM" id="metricTypeM" required> 
                            </div> 
                        </div>
                        <div class="col-xs-6">
                            <div class="addUserFormSubfieldContainer">Unique metric managed</div>
                            <div class="addUserFormSubfieldContainer">
                                <input type="text" class="form-control" name="uniqueMetricM" id="uniqueMetricM">
                            </div>
                        </div>
                    </div>
                    <div class="row editWidgetTypeDataRow">
                        <div class="col-xs-6">
                            <div class="addUserFormSubfieldContainer">Min width</div>
                            <div class="addUserFormSubfieldContainer">
                                <input id="minWidthM" name="minWidthM" data-slider-id="minWidthSliderM" type="text" data-slider-min="1" data-slider-max="100" data-slider-step="1"/>
                            </div> 
                        </div>
                        <div class="col-xs-6">
                            <div class="addUserFormSubfieldContainer">Max width</div>
                            <div class="addUserFormSubfieldContainer">
                                <input id="maxWidthM" name="maxWidthM" data-slider-id="maxWidthSliderM" type="text" data-slider-min="1" data-slider-max="100" data-slider-step="1"/>
                            </div> 
                        </div>
                    </div>
                    <div class="row editWidgetTypeDataRow">
                        <div class="col-xs-6">
                            <div class="addUserFormSubfieldContainer">Min height</div>
                            <div class="addUserFormSubfieldContainer">
                                <input id="minHeightM" name="minHeightM" data-slider-id="minHeightSliderM" type="text" data-slider-min="1" data-slider-max="100" data-slider-step="1"/>
                            </div> 
                        </div>
                        <div class="col-xs-6">
                            <div class="addUserFormSubfieldContainer">Max height</div>
                            <div class="addUserFormSubfieldContainer">
                                <input id="maxHeightM" name="maxHeightM" data-slider-id="maxHeightSliderM" type="text" data-slider-min="1" data-slider-max="100" data-slider-step="1"/>
                            </div> 
                        </div>
                    </div>
                    <div class="row" id="editWidgetTypeLoadingMsg">
                        <div class="col-xs-12 centerWithFlex">t</div>
                    </div>
                    <div class="row" id="editWidgetTypeLoadingIcon">
                        <div class="col-xs-12 centerWithFlex"><i class="fa fa-circle-o-notch fa-spin" style="font-size:36px;"></i></div>
                    </div>
                    <div class="row" id="editWidgetTypeOkMsg">
                        <div class="col-xs-12 centerWithFlex">Widget type updated successfully</div>
                    </div>
                    <div class="row" id="editWidgetTypeOkIcon">
                        <div class="col-xs-12 centerWithFlex"><i class="fa fa-thumbs-o-up" style="font-size:36px"></i></div>
                    </div>
                    <div class="row" id="editWidgetTypeKoMsg">
                        <div class="col-xs-12 centerWithFlex"></div>
                    </div>
                    <div class="row" id="editWidgetTypeKoIcon">
                        <div class="col-xs-12 centerWithFlex"><i class="fa fa-thumbs-o-down" style="font-size:36px"></i></div>
                    </div>
            </div>
            <div id="editWidgetTypeModalFooter" class="modal-footer">
              <button type="button" id="editWidgetTypeCancelBtn" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
              <button type="button" id="editWidgetTypeConfirmBtn" name="editWidgetType" class="btn btn-primary internalLink">Confirm</button>
            </div>
          </div>
        </div>
    </div>
    
    <!-- Modale cancellazione tipo di widget -->
    <div class="modal fade" id="modalDelWidgetType" tabindex="-1" role="dialog" aria-labelledby="modalDelWidgetTypeLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
          <div class="modal-content">
            <div class="modal-header centerWithFlex">
              <h5 class="modal-title" id="modalAddWidgetTypeLabel">Delete widget type</h5>
            </div>
            
            <div id="delWidgetTypeModalBody" class="modal-body">
                <input type="hidden" id="widgetIdToDelete" />
                <div class="row delWidgetTypeDataRow">
                    <div class="col-xs-12">
                        <div class="addUserFormSubfieldContainer">Do you want to confirm cancellation of the following widget type?</div>
                        <div class="addUserFormSubfieldContainer" id="delWidgetName"></div> 
                    </div>
                </div>
                <div class="row" id="delWidgetTypeLoadingMsg">
                    <div class="col-xs-12 centerWithFlex">Deleting widget type, please wait</div>
                </div>
                <div class="row" id="delWidgetTypeLoadingIcon">
                    <div class="col-xs-12 centerWithFlex"><i class="fa fa-circle-o-notch fa-spin" style="font-size:36px;"></i></div>
                </div>
                <div class="row" id="delWidgetTypeOkMsg">
                    <div class="col-xs-12 centerWithFlex">Widget type deleted successfully</div>
                </div>
                <div class="row" id="delWidgetTypeOkIcon">
                    <div class="col-xs-12 centerWithFlex"><i class="fa fa-thumbs-o-up" style="font-size:36px"></i></div>
                </div>
                <div class="row" id="delWidgetTypeKoMsg">
                    <div class="col-xs-12 centerWithFlex">Error deleting widget type</div>
                </div>
                <div class="row" id="delWidgetTypeKoIcon">
                    <div class="col-xs-12 centerWithFlex"><i class="fa fa-thumbs-o-down" style="font-size:36px"></i></div>
                </div>
            </div>
            <div id="delWidgetTypeModalFooter" class="modal-footer">
              <button type="button" id="delWidgetTypeCancelBtn" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
              <button type="button" id="delWidgetTypeConfirmBtn" name="delWidgetType" class="btn btn-primary internalLink">Confirm</button>
            </div>
          </div>
        </div>
    </div>
    
    
</body>
</html>   



