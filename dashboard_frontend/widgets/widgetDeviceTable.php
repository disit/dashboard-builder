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
include('../config.php');
header("Cache-Control: private, max-age=$cacheControlMaxAge");
?>

<script src="../js/DataTables/datatables.min.js" type="text/javascript"></script>
<script src="../js/DataTables/datatables.js" type="text/javascript"></script>
<script src="../js/DataTables/dataTables.responsive.min.js" type="text/javascript"></script>
<script src="../js/DataTables/dataTables.bootstrap.min.js" type="text/javascript"></script>
<link rel="stylesheet" href="../js/DataTables/datatables.min.css">
<link rel="stylesheet" href="../js/DataTables/datatables.css">
<script type='text/javascript'>
    var dataSet_<?= $_REQUEST['name_w'] ?> = [];
    var devices_<?= $_REQUEST['name_w'] ?> = [];
	var column_list_<?= $_REQUEST['name_w'] ?> = [];
    var temp = {};
    var specificData = "";
    var count = devices_<?= $_REQUEST['name_w'] ?>.length;
    var prefix = "http://www.disit.org/km4city/resource/iot/orionUNIFI/DISIT/";
    var ordering = 1;
	var query_to_send = '';
	var save_value_<?= $_REQUEST['name_w'] ?>_ =$('#url_<?= $_REQUEST['name_w'] ?>').val();
	var action_<?= $_REQUEST['name_w'] ?> = "";
	
	//Paging_data;
	var fullCount = 0;
	var maintable_length = 5;
	var maintable_length = parseInt($('#n_rows_<?= $_REQUEST['name_w'] ?>').val());
	var current_page_<?= $_REQUEST['name_w'] ?> = 0;
	var sortOnValue = '';
	//var order_sort = 'desc';
	var order_column = '';
	var order_column_n = 0;
	var total_result = 0;
	var n_rows_<?= $_REQUEST['name_w'] ?> = $('#n_rows_<?= $_REQUEST['name_w'] ?>').val();
	//
	$('#start_value_<?= $_REQUEST['name_w'] ?>').val(0);
	$('#order_<?= $_REQUEST['name_w'] ?>').val('desc');
	$('#current_page_<?= $_REQUEST['name_w'] ?>').val(0);
    var columnsToShow_<?= $_REQUEST['name_w'] ?> = {}
	var columns_list_<?= $_REQUEST['name_w'] ?> = [];
	//url_<?= $_REQUEST['name_w'] ?>
	var responsive_table_<?= $_REQUEST['name_w'] ?> = true;
	var columnTitles_<?= $_REQUEST['name_w'] ?>= [];
	var rowsToShow_<?= $_REQUEST['name_w'] ?> = [5,10,20];
	
	

    var actions = {
        pin: "hidden"
    }

    $(document).ready(function <?= $_REQUEST['name_w'] ?>(firstLoad, metricNameFromDriver, widgetTitleFromDriver, widgetHeaderColorFromDriver, widgetHeaderFontColorFromDriver, fromGisExternalContent, fromGisExternalContentServiceUri, fromGisExternalContentField, fromGisExternalContentRange, /*randomSingleGeoJsonIndex,*/ fromGisMarker, fromGisMapRef) {
        <?php
        $link = mysqli_connect($host, $username, $password);
        if (checkWidgetNameInDashboard($link, $_REQUEST['name_w'], $_REQUEST['id_dashboard']) === false) {
            eventLog("Returned the following ERROR in widgetEvent.php for the widget " . escapeForHTML($_REQUEST['name_w']) . " is not instantiated or allowed in this dashboard.");
            exit();
        }?>

        var hostFile = "<?= escapeForJS($_REQUEST['hostFile']) ?>";
        var widgetName = "<?= $_REQUEST['name_w'] ?>";
        var widgetContentColor = "<?= escapeForJS($_REQUEST['color_w']) ?>";
        var widgetHeaderColor = "<?= escapeForJS($_REQUEST['frame_color_w']) ?>";
        var widgetHeaderFontColor = "<?= escapeForJS($_REQUEST['headerFontColor']) ?>";
        var timeToReload = <?= sanitizeInt('frequency_w') ?>;
        var metricName = "<?= escapeForJS($_REQUEST['id_metric']) ?>";
        var elToEmpty = $("#<?= $_REQUEST['name_w'] ?>_table");
        var metricData, metricType, series, index, styleParameters, chartSeriesObject, fontSize, fontColor, legendWidth,
            xAxisCategories, chartType, highchartsChartType, chartColor, dataLabelsFontColor,
            dataLabelsRotation, dataLabelsAlign, dataLabelsVerticalAlign, dataLabelsY, legendItemClickValue,
            stackingOption, dataLabelsFontSize, chartLabelsFontSize,
            widgetHeight, metricName, aggregationGetData, getDataFinishCount, widgetTitle, countdownRef, chartRef,
            widgetParameters, infoJson, thresholdsJson, rowParameters, chartLabelsFontColor, appId, flowId,
            nrMetricType, groupByAttr, nrInputId, nodeRedInputName = null;
        var headerHeight = 25;
        var embedWidget = <?= $_REQUEST['embedWidget'] == 'true' ? 'true' : 'false' ?>;
        var embedWidgetPolicy = '<?= escapeForJS($_REQUEST['embedWidgetPolicy']) ?>';
        var showTitle = "<?= escapeForJS($_REQUEST['showTitle']) ?>";
        var hasTimer = "<?= escapeForJS($_REQUEST['hasTimer']) ?>";
        var showHeader = null;
        var pattern = /Percentuale\//;
        var seriesDataArray = [];
        var serviceUri = "";
        var flipFlag = false;
        var table = null;
		var code = null;
        var dashboard_id = null;
        var isFirstLoad = 1;
        var missingFieldsDevices = {
            error: "Missing minimum fields in one or more devices",
            missingFieldsPerDevice: null
        };
		
		//
		$(document).off('showDeviceTableFromExternalContent_' + widgetName);
			$(document).on('showDeviceTableFromExternalContent_' + widgetName, function(event){
			console.log('showDeviceTableFromExternalContent_Code!');
					if(encodeURIComponent(metricName) === encodeURIComponent(metricName))
						{
							var newValue_<?= $_REQUEST['name_w'] ?> = event.passedData;
							//
							current_page_<?= $_REQUEST['name_w'] ?> = 0;
							$('#current_page_<?= $_REQUEST['name_w'] ?>').val(0);
							$('#maintable_<?= $_REQUEST['name_w'] ?>').DataTable().destroy();
							//						
							populateWidget(newValue_<?= $_REQUEST['name_w'] ?>);

						}
			});
		//

        console.info(missingFieldsDevices);
        var test = {};
        console.info(test);

        if (Window.webSockets == undefined)
            Window.webSockets = {};

        console.log("Entrato in widgetEvent --> " + widgetName);
			
		
		function change_page(start, end, current){
			current_page_<?= $_REQUEST['name_w'] ?> = current;
			$('#current_page_<?= $_REQUEST['name_w'] ?>').val(current);
			$('#maintable_<?= $_REQUEST['name_w'] ?>').DataTable().destroy();
			populateWidget(save_value_<?= $_REQUEST['name_w'] ?>_);
			
		}
		
		$('#searchlabel_<?= $_REQUEST['name_w'] ?>').on('keyup', function() {
					action_<?= $_REQUEST['name_w'] ?> = 'Searching';
					var searchlabel_<?= $_REQUEST['name_w'] ?> = $('#searchlabel_<?= $_REQUEST['name_w'] ?>').val();
					current_page_<?= $_REQUEST['name_w'] ?> = 0;
					$('#tbody_<?= $_REQUEST['name_w'] ?>').empty();
					$('#maintable_<?= $_REQUEST['name_w'] ?>').DataTable().destroy();
					$('#paging_table_<?= $_REQUEST['name_w'] ?>').empty();
					populateWidget(save_value_<?= $_REQUEST['name_w'] ?>_);
					
				});
				
	

		$('#n_rows_<?= $_REQUEST['name_w'] ?>').on('change', function() {
					action_<?= $_REQUEST['name_w'] ?> = 'ChangeNumberRows';
					maintable_length = parseInt($('#n_rows_<?= $_REQUEST['name_w'] ?>').val());
					current_page_<?= $_REQUEST['name_w'] ?> = 0;
					$('#maintable_<?= $_REQUEST['name_w'] ?>').DataTable().destroy();
					$('#paging_table_<?= $_REQUEST['name_w'] ?>').empty();
					populateWidget(save_value_<?= $_REQUEST['name_w'] ?>_);
					$('#page_<?= $_REQUEST['name_w'] ?>0').addClass('active');
					current_page_<?= $_REQUEST['name_w'] ?> = 0;
					
				});
				
			
		

        function createTable() {
			//
			//
			var l = Object.keys(columnsToShow_<?= $_REQUEST['name_w'] ?>);
			var k = Object.keys(dataSet_<?= $_REQUEST['name_w'] ?>[0]);
			var arr_c = columnTitles_<?= $_REQUEST['name_w'] ?>;
			//console.log(arr_c[1]);
			//
			var title_dv = "device";
			if (arr_c.length > 0){
					for(var y=0; y<arr_c.length; y++){
						if (arr_c[y].value == 'device'){
							title_dv = arr_c[y].name;
						}
					}
			}
			//
			var arr_col_<?= $_REQUEST['name_w'] ?> = [{"data": "device", className: "expand-content all dt-center", orderable: true, title: title_dv}];
				
				for (var i=0; i<k.length; i++){
					var class_n = 'none';
					if (columns_list_<?= $_REQUEST['name_w'] ?>.includes(k[i])){
						class_n = '';
					}
					if ((k[i] !== 'device')&&(k[i] !== 'serviceUri')){
						var title = k[i];
					//arr_col_<?= $_REQUEST['name_w'] ?>.push({title: k[i], "data": k[i], className: class_n});
					//
					if (arr_c.length > 0){
					for(var y=0; y<arr_c.length; y++){
						if (arr_c[y].value == k[i]){
							title = arr_c[y].name;
						}
					}
					}
						arr_col_<?= $_REQUEST['name_w'] ?>.push({"title": title, "data": k[i], "className": class_n });
					
					//
					}
					
				}
				var title_act = "Actions";
				if (arr_c.length > 0){
						for(var y=0; y<arr_c.length; y++){
							if (arr_c[y].value == 'Actions'){
								title_act = arr_c[y].name;
							}
						}
				}
				arr_col_<?= $_REQUEST['name_w'] ?>.push({
                        "data": null, className: "all dt-center", orderable: false, title: title_act,
                        "render": function (data, type, row, meta) {
                            var body = "";
                            for (let key in actions) {
                                if(key === "pin" && actions[key] === "show"){
                                    body += '<button id = "pin" class="btn actionButton_<?= $_REQUEST['name_w'] ?>" style="margin-left: 10px"><i style="font-size: 20px" class="fa fa-map-marker" aria-hidden="true"></i></button>';
                                }else{
									if (key !== "pin"){
                                    body += '<button id = "' + key + '" class="btn actionButton_<?= $_REQUEST['name_w'] ?>" style="margin-left: 10px"><img style="width:20px" src="' + actions[key] + '"/></button>';
									}
                                }
                            }
                            return body;
                        }
                    });
				//
				var order_column_n_<?= $_REQUEST['name_w'] ?> = parseInt($('#num_column_<?= $_REQUEST['name_w'] ?>').val());
				var order_sort = $('#order_<?= $_REQUEST['name_w'] ?>').val();
				if (order_sort == ""){
					order_sort = "desc";
				}
				//
				//
            table = $('#maintable_<?= $_REQUEST['name_w'] ?>').DataTable({
                data: dataSet_<?= $_REQUEST['name_w'] ?>,
               scrollResize: true,
               scrollY: '100px',
			    //sScrollY: '400px',
				//scrollX: true,
                scrollCollapse: true,
                paging: false,
				info: false,
				searching: false,
				responsive: {
					details: responsive_table_<?= $_REQUEST['name_w'] ?>
				},
				ordering: true,
				order: [[order_column_n_<?= $_REQUEST['name_w'] ?>, order_sort]],
               columns: arr_col_<?= $_REQUEST['name_w'] ?>,
				//columnDefs: [	{ width: "200px", targets: "_all" } ],	
                rowCallback: function (row, data, index) {
                    $('.dataTables_scrollBody').css('overflow-x', 'hidden');
                }
            }).columns.adjust();

			
			$('#thead_<?= $_REQUEST['name_w'] ?> tr th').addClass('sorting_<?= $_REQUEST['name_w'] ?>');
			//$('#container').css( 'display', 'block' );
	
			//table.columns.adjust().draw();
	
			//
			 $('.actionButton_<?= $_REQUEST['name_w'] ?>').off('click');
             $('.actionButton_<?= $_REQUEST['name_w'] ?>').click(function () {
                var data = table.row($(this).parents('tr')).data();
                var order = table.order();
                ordering = order[0][0];
				
                var dataToSend = {
                    device: data.device,
                    //prefix: prefix,
					query: $("#url_<?= $_REQUEST['name_w'] ?>").val(),
                    ordering: (Object.keys(columnsToShow_<?= $_REQUEST['name_w'] ?>))[ordering],
                    action: this.id
                };
				///ACTIVE SCRIPT///
				if((code !== null)&&(code !== '')){
					//console.log('serviceUri: '+serviceUri);
					
					data.action = this.id;
					execute_<?= $_REQUEST['name_w'] ?>(data);
				}
				//////////////////
                stdSend(dataToSend);
			
            });


            if(isFirstLoad) {
                addButtonsListerners()
                isFirstLoad = 0;
            }

            showWidgetContent(widgetName);
            table.columns.adjust().draw();
            $("#maintable_<?= $_REQUEST['name_w'] ?>_filter").find("label").css("color", "black");
			//console.log('current_page: '+current_page_<?= $_REQUEST['name_w'] ?>);
			$('#current_page_<?= $_REQUEST['name_w'] ?>').val(current_page_<?= $_REQUEST['name_w'] ?>);
			
			////////
                    $('.sorting_<?= $_REQUEST['name_w'] ?>').off('click');
					$('.sorting_<?= $_REQUEST['name_w'] ?>').click(function (){
						console.log('click sorting');
						action_<?= $_REQUEST['name_w'] ?> = 'changedOrdering';
						var text = $(this).text();
						//CHECK_COLUMN_TITLE
						if (columnTitles_<?= $_REQUEST['name_w'] ?>.length > 0){
							var text0 = text;
							for(var r=0; r<columnTitles_<?= $_REQUEST['name_w'] ?>.length; r++){
								if (columnTitles_<?= $_REQUEST['name_w'] ?>[r].name == text0){
									text = columnTitles_<?= $_REQUEST['name_w'] ?>[r].value;
								}
							}
						}
						//
						order_column_n = column_list_<?= $_REQUEST['name_w'] ?>.indexOf(text);
						
						$('#num_column_<?= $_REQUEST['name_w'] ?>').val(order_column_n);
						if ((text == 'device')||(text == 'deviceName')){
							$('#num_column_<?= $_REQUEST['name_w'] ?>').val(0);
						}
						$('#current_page_<?= $_REQUEST['name_w'] ?>').val(0);
						$('#maintable_<?= $_REQUEST['name_w'] ?>').DataTable().destroy();
						$('#paging_table_<?= $_REQUEST['name_w'] ?>').empty();
						if (text == $('#order_column_<?= $_REQUEST['name_w'] ?>').val()){
							order = $('#order_<?= $_REQUEST['name_w'] ?>').val();
							if (order == 'desc'){
								 $('#order_<?= $_REQUEST['name_w'] ?>').val('asc');
							}else{
								$('#order_<?= $_REQUEST['name_w'] ?>').val('desc');
							}
						}else{
							$('#order_column_<?= $_REQUEST['name_w'] ?>').val(text);
							$('#order_<?= $_REQUEST['name_w'] ?>').val('desc');
						}
						$('#start_value_<?= $_REQUEST['name_w'] ?>').val(0);
						populateWidget(save_value_<?= $_REQUEST['name_w'] ?>_);
						$('#page_<?= $_REQUEST['name_w'] ?>0').addClass('active');
						current_page_<?= $_REQUEST['name_w'] ?> = 0;
						
					});
					
				/////////
        }
		////////
			
			//////
        function populateWidget(newValue_<?= $_REQUEST['name_w'] ?>) {
			//
			
			$('#paging_table_<?= $_REQUEST['name_w'] ?>').empty();
			save_value_<?= $_REQUEST['name_w'] ?>_ = newValue_<?= $_REQUEST['name_w'] ?>;
			
			if (newValue_<?= $_REQUEST['name_w'] ?>.searching){
				if (newValue_<?= $_REQUEST['name_w'] ?>.searching == 'false'){
					$(".dataTables_filter").hide();
				}else{
					$(".dataTables_filter").show();
				}
			}else{
				$(".dataTables_filter").show();
			}
			
			$('#url_<?= $_REQUEST['name_w'] ?>').val(newValue_<?= $_REQUEST['name_w'] ?>.query);
			var query1=  newValue_<?= $_REQUEST['name_w'] ?>.query;
			
			columns_list_<?= $_REQUEST['name_w'] ?> = newValue_<?= $_REQUEST['name_w'] ?>.columnsToShow;
			//
			var get_column_order = $('#order_column_<?= $_REQUEST['name_w'] ?>').val();
			
			if (get_column_order == ""){
				var order_column = newValue_<?= $_REQUEST['name_w'] ?>.ordering;
				$('#order_column_<?= $_REQUEST['name_w'] ?>').val(order_column);
			}
			
			if (newValue_<?= $_REQUEST['name_w'] ?>.columnTitles){
				columnTitles_<?= $_REQUEST['name_w'] ?> = newValue_<?= $_REQUEST['name_w'] ?>.columnTitles;
				
			}
			
			
			var start_value = $('#start_value_<?= $_REQUEST['name_w'] ?>').val();
			if (start_value ==""){
				start_value = 0;
			}
			var filter_start = '&fromResult='+start_value;
			var filter_max = '&maxResults='+$('#n_rows_<?= $_REQUEST['name_w'] ?>').val();
			//
			var order = $('#order_<?= $_REQUEST['name_w'] ?>').val();
			
			//
			var searchlabel_<?= $_REQUEST['name_w'] ?> = $('#searchlabel_<?= $_REQUEST['name_w'] ?>').val();
			//
            dataSet_<?= $_REQUEST['name_w'] ?> = [];
            temp = {};
            specificData = "";
			maintable_length = parseInt($('#n_rows_<?= $_REQUEST['name_w'] ?>').val());
			//
			var ordering_data = '';
			
			if ($('#order_column_<?= $_REQUEST['name_w'] ?>').val() !==""){
				ordering_data = '&sortOnValue='+$('#order_column_<?= $_REQUEST['name_w'] ?>').val()+':'+$('#order_<?= $_REQUEST['name_w'] ?>').val();
				if ($('#order_column_<?= $_REQUEST['name_w'] ?>').val() =="device"){
					ordering_data = '&sortOnValue=deviceName:'+$('#order_<?= $_REQUEST['name_w'] ?>').val()+':string';
				}
			}else{
				ordering_data = '&sortOnValue=deviceName:desc:string';
			}
			
			var filter_text = "";
			if (searchlabel_<?= $_REQUEST['name_w'] ?> !==""){
						filter_text = '&text='+searchlabel_<?= $_REQUEST['name_w'] ?>;	
			}
			//
			query_to_send = $('#url_<?= $_REQUEST['name_w'] ?>').val();
			var query_filtered = $('#url_<?= $_REQUEST['name_w'] ?>').val() +ordering_data+filter_start+filter_max+filter_text;
			
			
			//
                $('#<?= $_REQUEST['name_w'] ?>_noDataAlert').hide();
                    $.ajax({
                        //
						url: query_filtered,
                        type: "GET",
                        async: true,
                        timeout: 0,
                        dataType: 'json',
                        success: function (data) {
							//
							var features = data.features;
							var n_feat = features.length;
							total_result = n_feat;
							fullCount = data.fullCount;
							//if ((n_feat == 0)&&(fullCount == 0)){
							if (fullCount == 0){
							//
							$('#<?= $_REQUEST['name_w'] ?>_noDataAlert').show();
											showWidgetContent(widgetName);
							}else{
							//
							
							$("#maintable_<?= $_REQUEST['name_w'] ?> thead").empty();
							
							//
							if (newValue_<?= $_REQUEST['name_w'] ?>.responsive){
											if ((newValue_<?= $_REQUEST['name_w'] ?>.responsive == 'false')||(newValue_<?= $_REQUEST['name_w'] ?>.responsive == false)){
												//
												responsive_table_<?= $_REQUEST['name_w'] ?> = false;
												//
											}else{
												//dataSet_<?= $_REQUEST['name_w'] ?>.push(temp);
												responsive_table_<?= $_REQUEST['name_w'] ?> = true;
											}
										}else{
											responsive_table_<?= $_REQUEST['name_w'] ?> = true;
										}
							//
							var features = data.features;
							var values = features[0].properties.values;
							var keys = Object.keys(values);
							column_list_<?= $_REQUEST['name_w'] ?> = keys;
							var pos_key = keys.indexOf(order_column);
							if ($('#num_column_<?= $_REQUEST['name_w'] ?>').val() == ''){
										$('#num_column_<?= $_REQUEST['name_w'] ?>').val(pos_key);
							}
							var count = 0;
									for (var i = 0; i < n_feat; i++) {						
										temp = data.features[i].properties.values;
										specificData = "";
										temp.device = data.features[i].properties.deviceName;
										temp.serviceUri = data.features[i].properties.serviceUri;
										//CHECK FORMAT//
										if(columnTitles_<?= $_REQUEST['name_w'] ?>.length > 0){
											for(var z=0; z<columnTitles_<?= $_REQUEST['name_w'] ?>.length; z++){
												var col_val= columnTitles_<?= $_REQUEST['name_w'] ?>[z].value;
												if (temp.hasOwnProperty(col_val)){
													var content_column = temp[col_val];
													if(columnTitles_<?= $_REQUEST['name_w'] ?>[z].format){
														var colformat= columnTitles_<?= $_REQUEST['name_w'] ?>[z].format;
														//console.log('content_column: '+content_column);
														//console.log('colformat: '+colformat);
														var d = new Date(content_column);
														//var d2 = d.toLocaleFormat('%d-%b-%Y');
														var d2 = moment(d).format(colformat);
														temp[col_val] = d2;
														//console.log(d2);
														
													}
												}
											}
										}
										//console.log(temp);
										//
										
										dataSet_<?= $_REQUEST['name_w'] ?>.push(temp);

									}
									//
									//if (count === 0) {
										if(missingFieldsDevices.missingFieldsPerDevice === null){
											createTable();
												///////////////////	
												var n_page = Math.ceil(fullCount/maintable_length);
														var last_page = 0;
														last_page = (fullCount - maintable_length);
														var list_links = '';
														var first_int = 0;
														var end_int = 0;
														//
														list_links = list_links + '<li class="paginate_button pag_hidden1"></li>';
														//
														for (var i=0;i<n_page; i++){
															var n = i+1;
															var active ='';
															var hidden = '';
															//
															first_int = (i * maintable_length);
															end_int= first_int + maintable_length;
															
															list_links = list_links + '<li class="paginate_button n_lnk pag_<?= $_REQUEST['name_w'] ?>" id="page_<?= $_REQUEST['name_w'] ?>'+i+'" start="'+first_int+'" end="'+end_int+'"current='+i+'><a href="#"  id="page_<?= $_REQUEST['name_w'] ?>link_<?= $_REQUEST['name_w'] ?>_'+i+'" aria-controls="maintable" data-dt-idx="2" tabindex="0" start="'+first_int+'" end="'+end_int+'">'+n+'</a></li>';
														}
														list_links = list_links + '<li class="paginate_button pag_hidden2"></li>';
														
														var dis_next = "";
														if (current_page_<?= $_REQUEST['name_w'] ?> == i-1){dis_next = "disabled";}else{dis_next = "";}
														var dis_prev = "";
														if (current_page_<?= $_REQUEST['name_w'] ?> == 0){ dis_prev = "disabled"; }else{ dis_prev = "";}
														var next = current_page_<?= $_REQUEST['name_w'] ?>++;
														var prev = current_page_<?= $_REQUEST['name_w'] ?>-1;
														$('#paging_table_<?= $_REQUEST['name_w'] ?>').html('<ul class="pagination"><li class="paginate_button '+dis_prev+' first '+dis_prev+' first_<?= $_REQUEST['name_w'] ?>" id="maintable_first" tabindex="0" start="0" end="'+maintable_length+'" current="0"><a href="#" aria-controls="maintable" data-dt-idx="0" tabindex="0" start="0" end="'+maintable_length+'">First</a></li><li class="paginate_button '+dis_prev+' previous '+dis_prev+' previous_<?= $_REQUEST['name_w'] ?>" id="maintable_previous" start="'+first_int+'" end="'+end_int+'" current='+(prev-1)+'><a href="#" aria-controls="maintable" data-dt-idx="1" tabindex="0">&lt;&lt; Prev</a></li>'+list_links+'<li class="paginate_button'+dis_next+' next_<?= $_REQUEST['name_w'] ?> '+dis_next+' " id="maintable_next_<?= $_REQUEST['name_w'] ?>" start="'+first_int+'" end="'+end_int+'"current="'+(next+1)+'"><a href="#" aria-controls="maintable" data-dt-idx="3" tabindex="0">Next &gt;&gt;</a></li><li class="paginate_button'+dis_next+' last '+dis_next+' last_<?= $_REQUEST['name_w'] ?>" id="maintable_last" start="'+first_int+'" end="'+maintable_length+'"current='+(n_page-1)+'><a href="#" aria-controls="maintable" data-dt-idx="4" tabindex="0" start="'+first_int+'" end="'+end_int+'">Last</a></li></ul>');
														$('#page_<?= $_REQUEST['name_w'] ?>'+prev).addClass('active');
														////
														//console.log('CURRENT PAGE:	'+current_page_<?= $_REQUEST['name_w'] ?>);
														//
														$('.n_lnk').each(function() {
															//console.log($(this).text());
															if(isNaN($(this).text())){
															}else{
																var cont = $(this).text();
																if ((cont > current_page_<?= $_REQUEST['name_w'] ?> + 2)||(cont < current_page_<?= $_REQUEST['name_w'] ?> - 2)){
																	$(this).hide();
																	if (cont < current_page_<?= $_REQUEST['name_w'] ?> - 2){
																		$('.pag_hidden1').html('<a>...</a>');
																	}
																	if (cont > current_page_<?= $_REQUEST['name_w'] ?> + 2){
																		$('.pag_hidden2').html('<a>...</a>');
																	}
																}
																
															}
														});
														////
														$('.pag_<?= $_REQUEST['name_w'] ?>').off('click');
														$('.pag_<?= $_REQUEST['name_w'] ?>').click(function () {
															var start = $(this).attr('start');
															var end = $(this).attr('end');
															var current = $(this).attr('current');
															$('#start_value_<?= $_REQUEST['name_w'] ?>').val(start);
															//
															var n_rows = $('#n_rows_<?= $_REQUEST['name_w'] ?>').val();
															var new_start = start+n_rows;
															var new_end = end+n_rows;
															$('.next_<?= $_REQUEST['name_w']?>').attr('start', new_start);
															$('.previous_<?= $_REQUEST['name_w']?>').attr('end', new_end);
															//
															action_<?= $_REQUEST['name_w'] ?> = 'ChangePage';
															change_page(start, end, current);
															//
															var first_p = current-1;
															if (first_p == -1){
																$('#page_<?= $_REQUEST['name_w'] ?>0').addClass('active');
															}
															console.log('current in paginate:'+first_p+ '	#page_<?= $_REQUEST['name_w'] ?>'+first_p);
															$('#page_<?= $_REQUEST['name_w'] ?>'+first_p).addClass('active');
															
														});
														$('.first_<?= $_REQUEST['name_w'] ?>').off('click');
														$('.first_<?= $_REQUEST['name_w'] ?>').click(function () {
															var start = $(this).attr('start');
															var end = $(this).attr('end');
															var current = $(this).attr('current');
															$('#start_value_<?= $_REQUEST['name_w'] ?>').val(start);
															//
															var n_rows = $('#n_rows_<?= $_REQUEST['name_w'] ?>').val();
															var new_start = start+n_rows;
															var new_end = end+n_rows;
															$('.next_<?= $_REQUEST['name_w']?>').attr('start', new_start);
															$('.previous_<?= $_REQUEST['name_w']?>').attr('end', new_end);
															//
															action_<?= $_REQUEST['name_w'] ?> = 'FirstPage';
															change_page(start, end, current);
															//
															var first_p = current-1;
															if (first_p == -1){
																$('#page_<?= $_REQUEST['name_w'] ?>0').addClass('active');
															}
															console.log('current in paginate:'+first_p+ '	#page_<?= $_REQUEST['name_w'] ?>'+first_p);
															$('#page_<?= $_REQUEST['name_w'] ?>'+first_p).addClass('active');
															
														});
														$('.last_<?= $_REQUEST['name_w'] ?>').off('click');
														$('.last_<?= $_REQUEST['name_w'] ?>').click(function () {
															var start = $(this).attr('start');
															var end = $(this).attr('end');
															var current = $(this).attr('current');
															$('#start_value_<?= $_REQUEST['name_w'] ?>').val(start);
															//
															var n_rows = $('#n_rows_<?= $_REQUEST['name_w'] ?>').val();
															var new_start = start+n_rows;
															var new_end = end+n_rows;
															$('.next_<?= $_REQUEST['name_w']?>').attr('start', new_start);
															$('.previous_<?= $_REQUEST['name_w']?>').attr('end', new_end);
															//
															action_<?= $_REQUEST['name_w'] ?> = 'LastPage';
															change_page(start, end, current);
															//
															var first_p = current-1;
															if (first_p == -1){
																$('#page_<?= $_REQUEST['name_w'] ?>0').addClass('active');
															}
															console.log('current in paginate:'+first_p+ '	#page_<?= $_REQUEST['name_w'] ?>'+first_p);
															$('#page_<?= $_REQUEST['name_w'] ?>'+first_p).addClass('active');
															
														});
														$('.next_<?= $_REQUEST['name_w'] ?>').off('click');
														$('.next_<?= $_REQUEST['name_w'] ?>').click(function () {
															if (!$(this).hasClass( "disabled" )){
															var start = $('#start_value_<?= $_REQUEST['name_w'] ?>').val();
															var end = $('.active').attr('end');
															var current = $('#current_page_<?= $_REQUEST['name_w'] ?>').val();
															var next_page = parseInt(current) +1;
															var next_end =$('#n_rows_<?= $_REQUEST['name_w'] ?>').val();
															var next_start =	parseInt(start) + parseInt(next_end);
															console.log('next_start: '+next_start);
															console.log('next_end: '+next_end);
															console.log('next: '+next);
															action_<?= $_REQUEST['name_w'] ?> = 'NextPage';
															$('#start_value_<?= $_REQUEST['name_w'] ?>').val(next_start);
															change_page(next_start, next_end, next_page);
															}
															//
														});
														$('.previous_<?= $_REQUEST['name_w'] ?>').off('click');
														$('.previous_<?= $_REQUEST['name_w'] ?>').click(function () {
															if (!$(this).hasClass( "disabled" )){
															var start = $('#start_value_<?= $_REQUEST['name_w'] ?>').val();
															var end = $('.active').attr('end');
															var current = $('#current_page_<?= $_REQUEST['name_w'] ?>').val();
															var next_page = parseInt(current) -1;
															var next_end =$('#n_rows_<?= $_REQUEST['name_w'] ?>').val();
															var next_start =	parseInt(start) - parseInt(next_end);
															console.log('next_start: '+next_start);
															console.log('next_end: '+next_end);
															console.log('next: '+next);
															action_<?= $_REQUEST['name_w'] ?> = 'PreviousPage';
															$('#start_value_<?= $_REQUEST['name_w'] ?>').val(next_start);
															change_page(next_start, next_end, next_page);
															}
															//
														});
												/////////////////////////////		
										}else{
											stdSend(missingFieldsDevices);
											$('#<?= $_REQUEST['name_w'] ?>_noDataAlert').show();
											showWidgetContent(widgetName);
										}
									//}
							}

                        },

                        error: function () {
                            count--;
                            if (count === 0) {
                                createTable();
                            }
                        }
                    });

          

        }
		
		
        //Definizioni di funzione specifiche del widget

        function resizeWidget() {
            setWidgetLayout(hostFile, widgetName, widgetContentColor, widgetHeaderColor, widgetHeaderFontColor, showHeader, headerHeight, hasTimer);
            //$('#<?= $_REQUEST['name_w'] ?>_chartContainer').highcharts().reflow();
        }

        //Fine definizioni di funzione

        $(document).off('changeMetricFromButton_' + widgetName);
        $(document).on('changeMetricFromButton_' + widgetName, function (event) {
            if ((event.targetWidget === widgetName) && (event.newMetricName !== "noMetricChange")) {
                clearInterval(countdownRef);
                $("#<?= $_REQUEST['name_w'] ?>_content").hide();
                <?= $_REQUEST['name_w'] ?>(true, event.newMetricName, event.newTargetTitle, event.newHeaderAndBorderColor, event.newHeaderFontColor, false, null, null, /*null,*/ null, null);
            }
        });

        $(document).off('resizeHighchart_' + widgetName);
        $(document).on('resizeHighchart_' + widgetName, function (event) {
            showHeader = event.showHeader;
            $('#<?= $_REQUEST['name_w'] ?>_chartContainer').highcharts().reflow();
        });

        function addButtonsListerners(){
          

            $('#tbody_<?= $_REQUEST['name_w'] ?>').on('click', 'td.expand-content', function () {
                var data = table.row($(this).parents('tr')).data();
                var tr = $(this).closest('tr');
                var row = table.row(tr);
                var order = table.order();
                //ordering = order[0][0];
				ordering = "";

                var action;
                if(!row.child.isShown()){
                    action = "closed";
                }else{
                    action = "expanded";
                }

                var dataToSend = {
                    device: data.device,
                    //prefix: prefix,
					query: $("#url_<?= $_REQUEST['name_w'] ?>").val(),
                    ordering: (Object.keys(columnsToShow_<?= $_REQUEST['name_w'] ?>))[ordering],	
                    action: action
                };

                stdSend(dataToSend);
            });

            $('#maintable_<?= $_REQUEST['name_w'] ?>').on( 'order.dt', function () {
                if(table !== null){
                 var order = table.order();
                    //ordering = order[0][0];
					ordering = "";
					if ((action_<?= $_REQUEST['name_w'] ?> == '')){
						action_<?= $_REQUEST['name_w'] ?> = "changedOrdering";
					}else{
						
					}
                 var dataToSend = {
                    device: " ",
                    //prefix: prefix,
					query: $("#url_<?= $_REQUEST['name_w'] ?>").val(),
                    ordering: (Object.keys(columnsToShow_<?= $_REQUEST['name_w'] ?>))[ordering],
                    action: action_<?= $_REQUEST['name_w'] ?>
                 };

                 stdSend(dataToSend);
                }
            } );
        }
		
        //Nuova versione
        $.ajax({
            url: "../controllers/getWidgetParams.php",
            type: "GET",
            data: {
                widgetName: "<?= $_REQUEST['name_w'] ?>"
            },
            async: true,
            dataType: 'json',
            success: function (widgetData) {
                showTitle = widgetData.params.showTitle;
                widgetContentColor = widgetData.params.color_w;
                fontSize = widgetData.params.fontSize;
                fontColor = widgetData.params.fontColor;
                timeToReload = widgetData.params.frequency_w;
                hasTimer = widgetData.params.hasTimer;
                chartColor = widgetData.params.chartColor;
                dataLabelsFontSize = widgetData.params.dataLabelsFontSize;
                dataLabelsFontColor = widgetData.params.dataLabelsFontColor;
                chartLabelsFontSize = widgetData.params.chartLabelsFontSize;
                chartLabelsFontColor = widgetData.params.chartLabelsFontColor;
                appId = widgetData.params.appId;
                flowId = widgetData.params.flowId;
                nrMetricType = widgetData.params.nrMetricType;
                serviceUri = widgetData.params.serviceUri;
                nrInputId = widgetData.params.nrInputId;
                nodeRedInputName = widgetData.params.name;
                dashboard_id = widgetData.params.id_dashboard;
				code = widgetData.params.code;

                if (((embedWidget === true) && (embedWidgetPolicy === 'auto')) || ((embedWidget === true) && (embedWidgetPolicy === 'manual') && (showTitle === "no")) || ((embedWidget === false) && (showTitle === "no"))) {
                    showHeader = false;
                } else {
                    showHeader = true;
                }

                if ((metricNameFromDriver === "undefined") || (metricNameFromDriver === undefined) || (metricNameFromDriver === "null") || (metricNameFromDriver === null)) {
                    metricName = "<?= escapeForJS($_REQUEST['id_metric']) ?>";
                    widgetTitle = "<?= sanitizeTitle($_REQUEST['title_w']) ?>";
                    widgetHeaderColor = "<?= escapeForJS($_REQUEST['frame_color_w']) ?>";
                    widgetHeaderFontColor = "<?= escapeForJS($_REQUEST['headerFontColor']) ?>";
                    rowParameters = widgetData.params.rowParameters;
                } else {
                    metricName = metricNameFromDriver;
                    widgetTitleFromDriver.replace(/_/g, " ");
                    widgetTitleFromDriver.replace(/\'/g, "&apos;");
                    widgetTitle = widgetTitleFromDriver;
                    $("#" + widgetName).css("border-color", widgetHeaderColorFromDriver);
                    widgetHeaderColor = widgetHeaderColorFromDriver;
                    widgetHeaderFontColor = widgetHeaderFontColorFromDriver;
                }

                openWs(widgetName);
                setWidgetLayout(hostFile, widgetName, widgetContentColor, widgetHeaderColor, widgetHeaderFontColor, showHeader, headerHeight, hasTimer);

                if (rowParameters !== null) {
                    var newValue_<?= $_REQUEST['name_w'] ?> = JSON.parse(rowParameters);

                    setupLoadingPanel(widgetName, widgetContentColor, true);

                    ordering = (Object.keys(columnsToShow_<?= $_REQUEST['name_w'] ?>)).indexOf(newValue_<?= $_REQUEST['name_w'] ?>.ordering);
                    //prefix = newValue_<?= $_REQUEST['name_w'] ?>.prefix;
					query: newValue_<?= $_REQUEST['name_w'] ?>.query;
                    devices = newValue_<?= $_REQUEST['name_w'] ?>.devices;

                    var customActionNumber = 1;
                    for (let i = 0; i < newValue_<?= $_REQUEST['name_w'] ?>.actions.length; i++) {
                        if(actions[newValue_<?= $_REQUEST['name_w'] ?>.actions[i]] !== undefined){
                            actions[newValue_<?= $_REQUEST['name_w'] ?>.actions[i]] = "show";
                        }else{
                            actions["custom" + customActionNumber] = newValue_<?= $_REQUEST['name_w'] ?>.actions[i];
                            customActionNumber++;
                        }
                    }

                    for (let i = 0; i < newValue_<?= $_REQUEST['name_w'] ?>.columnsToShow.length; i++) {
                        columnsToShow_<?= $_REQUEST['name_w'] ?>[newValue_<?= $_REQUEST['name_w'] ?>.columnsToShow[i]] = "";
                    }
                }
				///////
				$('#current_page_<?= $_REQUEST['name_w'] ?>').val(0);
				current_page_<?= $_REQUEST['name_w'] ?> = 0;
				//////ATTUATORE/////////
				if (code != null && code != "null") {
					
						
                        //let code = widgetProperties.param.code;
                        var text_ck_area = document.createElement("text_ck_area");
                        text_ck_area.innerHTML = code;
                        var newInfoDecoded = text_ck_area.innerText;
                        newInfoDecoded = newInfoDecoded.replaceAll("function execute()","function execute_" + "<?= $_REQUEST['name_w'] ?>(param)");

                        var elem = document.createElement('script');
                        elem.type = 'text/javascript';
                        // elem.id = "<?= $_REQUEST['name_w'] ?>_code";
                        // elem.src = newInfoDecoded;
                        elem.innerHTML = newInfoDecoded;
                        $('#<?= $_REQUEST['name_w'] ?>_code').append(elem);

                        $('#<?= $_REQUEST['name_w'] ?>_code').css("display", "none");
                    }
				////////////////
                populateWidget(newValue_<?= $_REQUEST['name_w'] ?>);
            },
            error: function (errorData) {
                console.log("Error in widget params retrieval");
                console.log(JSON.stringify(errorData));
                showWidgetContent(widgetName);
                $('#<?= $_REQUEST['name_w'] ?>_noDataAlert').show();
            }
        });


        $("#<?= $_REQUEST['name_w'] ?>").on('customResizeEvent', function (event) {
            resizeWidget();
        });

        $("#<?= $_REQUEST['name_w'] ?>").off('updateFrequency');
        $("#<?= $_REQUEST['name_w'] ?>").on('updateFrequency', function (event) {
            clearInterval(countdownRef);
            timeToReload = event.newTimeToReload;
            countdownRef = startCountdown(widgetName, timeToReload, <?= $_REQUEST['name_w'] ?>, metricNameFromDriver, widgetTitleFromDriver, widgetHeaderColorFromDriver, widgetHeaderFontColorFromDriver, fromGisExternalContent, fromGisExternalContentServiceUri, fromGisExternalContentField, fromGisExternalContentRange, /*randomSingleGeoJsonIndex,*/ fromGisMarker, fromGisMapRef);
        });

        countdownRef = startCountdown(widgetName, timeToReload, <?= $_REQUEST['name_w'] ?>, metricNameFromDriver, widgetTitleFromDriver, widgetHeaderColorFromDriver, widgetHeaderFontColorFromDriver, fromGisExternalContent, fromGisExternalContentServiceUri, fromGisExternalContentField, fromGisExternalContentRange, /*randomSingleGeoJsonIndex,*/ fromGisMarker, fromGisMapRef);
        //Fine del codice core del widget


        var openWs = function (widget) // this one is the only function that is called from the above
        {
            try {
                <?php
                $genFileContent = parse_ini_file("../conf/environment.ini");
                $wsServerContent = parse_ini_file("../conf/webSocketServer.ini");
                $wsServerAddress = $wsServerContent["wsServerAddressWidgets"][$genFileContent['environment']['value']];
                $wsServerPort = $wsServerContent["wsServerPort"][$genFileContent['environment']['value']];
                $wsPath = $wsServerContent["wsServerPath"][$genFileContent['environment']['value']];
                $wsProtocol = $wsServerContent["wsServerProtocol"][$genFileContent['environment']['value']];
                $wsRetryActive = $wsServerContent["wsServerRetryActive"][$genFileContent['environment']['value']];
                $wsRetryTime = $wsServerContent["wsServerRetryTime"][$genFileContent['environment']['value']];
                echo 'wsRetryActive = "' . $wsRetryActive . '";' . "\n";
                echo 'wsRetryTime = ' . $wsRetryTime . ';' . "\n";
                echo 'wsUrl="' . $wsProtocol . '://' . $wsServerAddress . ':' . $wsServerPort . '/' . $wsPath . '";' . "\n";
                ?>
                // console.log(wsUrl);
                initWebsocket(widget, wsUrl, null, wsRetryTime * 1000, function (socket) {
                    // console.log('socket initialized!');
                    openWsConn(widget);
                }, function () {
                    console.log('Form Widget error: could not connect to this socket:');
                    console.log(wsUrl);
                });
            } catch (e) {
                wsClosed();
            }
        };

        var manageIncomingWsMsg = function (msg) {
            var data = JSON.parse(msg.data);
			console.log(data);
            try {
                console.log("I have received:");
                console.log(msg);


                if (data.msgType === "SendToEmitter" && data.result === "Ok" && data.widgetUniqueName === "<?= $_REQUEST['name_w'] ?>") {
                    accepted = data.msgId;
                    // console.log("My message # "+accepted+" has been accepted by the socket server, that is now attempting to deliver it to the IoT App.");
                    return;
                }

                if (data.msgType === "DataToEmitter" && data.widgetUniqueName === "<?= $_REQUEST['name_w'] ?>") {
                    var newValue_<?= $_REQUEST['name_w'] ?> = JSON.parse(data.newValue);

                    //console.info(newValue_<?= $_REQUEST['name_w'] ?>);

                //    if(newValue_<?= $_REQUEST['name_w'] ?>.devices && newValue_<?= $_REQUEST['name_w'] ?>.actions && newValue_<?= $_REQUEST['name_w'] ?>.prefix && newValue_<?= $_REQUEST['name_w'] ?>.ordering && newValue_<?= $_REQUEST['name_w'] ?>.columnsToShow) {
                    if(newValue_<?= $_REQUEST['name_w'] ?>.actions && newValue_<?= $_REQUEST['name_w'] ?>.ordering && newValue_<?= $_REQUEST['name_w'] ?>.columnsToShow) {
                        setupLoadingPanel(widgetName, widgetContentColor, true);

                        ordering = (Object.keys(columnsToShow_<?= $_REQUEST['name_w'] ?>)).indexOf(newValue_<?= $_REQUEST['name_w'] ?>.ordering);
                        //prefix = newValue_<?= $_REQUEST['name_w'] ?>.prefix;
						query: newValue_<?= $_REQUEST['name_w'] ?>.query;
                        devices = newValue_<?= $_REQUEST['name_w'] ?>.devices;

                        actions = {pin: "hidden"};

                        var customActionNumber = 1;
                        for (let i = 0; i < newValue_<?= $_REQUEST['name_w'] ?>.actions.length; i++) {
                            if(actions[newValue_<?= $_REQUEST['name_w'] ?>.actions[i]] !== undefined){
                                actions[newValue_<?= $_REQUEST['name_w'] ?>.actions[i]] = "show";
                            }else{
                                actions["custom" + customActionNumber] = newValue_<?= $_REQUEST['name_w'] ?>.actions[i];
                                customActionNumber++;
                            }
                        }

                        for (let key in columnsToShow_<?= $_REQUEST['name_w'] ?>) {
                            columnsToShow_<?= $_REQUEST['name_w'] ?>[key] = "none";
                        }

                        for (let i = 0; i < newValue_<?= $_REQUEST['name_w'] ?>.columnsToShow.length; i++) {
                            columnsToShow_<?= $_REQUEST['name_w'] ?>[newValue_<?= $_REQUEST['name_w'] ?>.columnsToShow[i]] = "";
                        }

                        if (table !== null) {
                            table.clear();
                            table.destroy();

                            //$("#tbody_<?= $_REQUEST['name_w'] ?>").empty();
                            $("#maintable_<?= $_REQUEST['name_w'] ?> thead").empty();
                            table = null;
                        }

                        missingFieldsDevices.missingFieldsPerDevice = null;
						///////
						/////
						$('#current_page_<?= $_REQUEST['name_w'] ?>').val(0);
						current_page_<?= $_REQUEST['name_w'] ?> = 0;
                        populateWidget(newValue_<?= $_REQUEST['name_w'] ?>);
                    }
                    return;
                }

                if (data.msgType === "DataToEmitterAck" && data.msgId === accepted && data.widgetUniqueName === "<?= $_REQUEST['name_w'] ?>") {
                    // console.log("My message # "+accepted+" has been acknowledged by the IoT App.");
                    accepted = null;
                    var webSocket = Window.webSockets[data.widgetUniqueName];
                    if (!webSocket.ackReceived) {
                        clearTimeout(webSocket.timeout);
                        webSocket.ackReceived = true;
                        // console.log(data.widgetUniqueName + " ACK ackReceived:" + webSocket.ackReceived)
                        webSocket.onAck({result: "Ok", widgetName: data.widgetUniqueName});
                    }
                    $("#<?= $_REQUEST['name_w'] ?>_formContainer form").css("opacity", 1);
                    return;
                }


            } catch (e) {
                console.log(e);
            }

        };

        timeToReload = 200;
        var openWsConn = function (widget) {
            var webSocket = Window.webSockets[widget];
            var wsRegistration = {
                msgType: "ClientWidgetRegistration",
                userType: "widgetInstance",
                widgetUniqueName: "<?= $_REQUEST['name_w'] ?>"
            };
            webSocket.send(JSON.stringify(wsRegistration));
            webSocket.addEventListener('message', manageIncomingWsMsg);
        };

        var wsClosed = function (e) {
            var webSocket = Window.webSockets["<?= $_REQUEST['name_w'] ?>"];
            webSocket.removeEventListener('message', manageIncomingWsMsg);
            if (wsRetryActive === 'yes') {
                setTimeout(openWs, parseInt(wsRetryTime * 1000));
            }
        };

        var stdSend = function (newValue_<?= $_REQUEST['name_w'] ?>) {
			//console.log('New Value:		');
			//console.log(newValue_<?= $_REQUEST['name_w'] ?>);
            var data = {
                "msgType": "SendToEmitter",
                "widgetUniqueName": widgetName,
                "value": JSON.stringify(newValue_<?= $_REQUEST['name_w'] ?>),
                "inputName": "",
                "dashboardId": dashboard_id,
                "username": $('#authForm #hiddenUsername').val(),
                "nrInputId": nrInputId
            };
			//console.log('nrInputId: '+nrInputId);
            console.log("Sending...");
            console.log(data);
            var webSocket = Window.webSockets[widgetName];
            webSocket.ackReceived = false;
            webSocket.onAck = function (data) {
                // console.log(widgetName+" SUCCESS ackReceived:"+webSocket.ackReceived)
                //clearInterval(setUpdatingMsgInterval);
                switch (data.result) {
                    case "insertQueryKo":
                        showUpdateResult("DB KO");
                        break;

                    case "updateBlockKo":
                        showUpdateResult("Device KO");
                        break;

                    case "updateBlockAndUpdateQueryKo":
                        showUpdateResult("DB and device KO");
                        break;

                    case "updateQueryKo":
                        showUpdateResult("DB KO");
                        break;

                    case "Ok":
                        showUpdateResult("Device OK");
                        break;
                }
            }
            //console.log(widgetName+" SEND ackReceived:"+webSocket.ackReceived)
            if (webSocket.readyState == webSocket.OPEN) {
                webSocket.send(JSON.stringify(data));
                console.log("I have sent:");
                console.log(JSON.stringify(data));
                webSocket.timeout = setTimeout(function () {
                    if (!webSocket.ackReceived) {
                        console.log(widgetName + " ERR1 ackReceived:" + webSocket.ackReceived)
                        showUpdateResult("API KO");
                        console.log("Update value KO");
                    }
                }, 60000)
            } else {
                console.log(widgetName + " ERR1 socket not OPEN");
                showUpdateResult("API KO");
            }
        };

        function initWebsocket(widget, url, existingWebsocket, retryTimeMs, success, failed) {
            if (!existingWebsocket || existingWebsocket.readyState != existingWebsocket.OPEN) {
                if (existingWebsocket) {
                    existingWebsocket.close();
                }
                var websocket = new WebSocket(url);
                websocket.widget = widget;
                // console.log("store websocket for "+widget)
                Window.webSockets[widget] = websocket;
                websocket.onopen = function () {
                    // console.info('websocket opened! url: ' + url);
                    success(websocket);
                };
                websocket.onclose = function () {
                    // console.info('websocket closed! url: ' + url + " reconnect in "+retryTimeMs+"ms");
                    //reconnect after a retryTime
                    setTimeout(function () {
                        initWebsocket(widget, url, existingWebsocket, retryTimeMs, success, failed);
                    }, retryTimeMs);
                };
                websocket.onerror = function (e) {
                    console.info('websocket error! url: ' + url);
                    console.info(e);
                };
            } else {
                success(existingWebsocket);
            }

            return;
        };

        function showUpdateResult(msg) {

        }

    });
</script>

<div class="widget" id="<?= $_REQUEST['name_w'] ?>_div">
    <div class='ui-widget-content'>
        <?php include '../widgets/widgetHeader.php'; ?>
        <?php include '../widgets/widgetCtxMenu.php'; ?>

        <div id="<?= $_REQUEST['name_w'] ?>_loading" class="loadingDiv">
            <div class="loadingTextDiv">
                <p>Loading data...</p>
            </div>
            <div class="loadingIconDiv">
                <i class='fa fa-spinner fa-spin'></i>
            </div>
        </div>

        <div id="<?= $_REQUEST['name_w'] ?>_content" class="content">
            <?php include '../widgets/commonModules/widgetDimControls.php'; ?>
            <p id="<?= $_REQUEST['name_w'] ?>_noDataAlert" style='text-align: center; font-size: 18px; display:none'>No
                Data Available</p>
			<div style='display:none'>
				Order Column name:<input type="text" id="order_column_<?= $_REQUEST['name_w'] ?>" /><br />
				Sorting Order: <input type="text" id="order_<?= $_REQUEST['name_w'] ?>" /><br />
				Current page: <input type="text" id="current_page_<?= $_REQUEST['name_w'] ?>" /><br />
				Column number:<input type="text" id="num_column_<?= $_REQUEST['name_w'] ?>" /><br />
				start_value:<input type="text" id="start_value_<?= $_REQUEST['name_w'] ?>" />
				url:<input type="text" id="url_<?= $_REQUEST['name_w'] ?>" />
			</div>


<div class="table-list-header-pag">


<label class="mod2">Show	<select id="n_rows_<?= $_REQUEST['name_w'] ?>" aria-controls="maintable" class="form-control input-sm"><option value=5>5</option><option value=10>10</option><option value=20>20</option></select> </label>
         <div class="pull-right mod2"><div id="maintable_filter_<?= $_REQUEST['name_w'] ?>" class="dataTables_filter"><label>Search:<input type="search" class="form-control input-sm" placeholder="" aria-controls="maintable" id="searchlabel_<?= $_REQUEST['name_w'] ?>"></label></div></div>
			<div id="paging_table_<?= $_REQUEST['name_w'] ?>" class="mod2"></div>


</div>


            <table id="maintable_<?= $_REQUEST['name_w'] ?>" class="table table-striped table-bordered display responsive" cellspacing="0" style="width:100%">
					<thead id="thead_<?= $_REQUEST['name_w'] ?>" ></thead>
					<tbody id="tbody_<?= $_REQUEST['name_w'] ?>" ></tbody>
				   </table>

        </div>
    </div>
	<div id="<?= $_REQUEST['name_w'] ?>_code"></div>
</div>