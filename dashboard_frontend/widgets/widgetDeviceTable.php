
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

if (isset($_COOKIE["device_table_rows_".$_REQUEST['name_w']])) {
	$device_table_rows[$_REQUEST['name_w']] = $_COOKIE["device_table_rows_".$_REQUEST['name_w']];
}else{
	if (PHP_VERSION_ID < 70300) { 
		setcookie("device_table_rows_".$_REQUEST['name_w'], 5, time() + (30 * 24 * 60 * 60), "/" . "; samesite='Strict'", $cookieDomain, false); 
	} else { 
		setcookie("device_table_rows_".$_REQUEST['name_w'], 5, [ 'expires' => time() + (30 * 24 * 60 * 60), // Set the cookie for 30 days 
			'path' => '/', 
			'domain' => $cookieDomain, 
			'secure' => true, //NALDI -> in microx set to false
			'samesite' => 'Strict'
		]); 
	}
	$device_table_rows[$_REQUEST['name_w']] = 5;
}

?>
<script src="../js/DataTables/datatables.min.js" type="text/javascript"></script>
<script src="../js/DataTables/datatables.js" type="text/javascript"></script>
<script src="../js/DataTables/dataTables.responsive.min.js" type="text/javascript"></script>
<script src="../js/DataTables/dataTables.bootstrap.min.js" type="text/javascript"></script>
<link rel="stylesheet" href="../js/DataTables/datatables.min.css">
<link rel="stylesheet" href="../js/DataTables/datatables.css">
<style>
    .pag-hidden {
    display: none;
}
</style>
<script type="text/javascript">
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
	//NALDI -> init the row number select
	$('#n_rows_<?= $_REQUEST['name_w'] ?>').val(<?php echo $device_table_rows[$_REQUEST['name_w']] ?>);
	var n_rows_<?= $_REQUEST['name_w'] ?> = $('#n_rows_<?= $_REQUEST['name_w'] ?>').val();
	//
	$('#start_value_<?= $_REQUEST['name_w'] ?>').val(0);
	$('#order_<?= $_REQUEST['name_w'] ?>').val('desc');
	$('#current_page_<?= $_REQUEST['name_w'] ?>').val(0);
    var columnsToShow_<?= $_REQUEST['name_w'] ?> = {}
	var columns_list_<?= $_REQUEST['name_w'] ?> = [];
	//url_<?= $_REQUEST['name_w'] ?> var responsive_table_<?= $_REQUEST['name_w'] ?> = true;
	var columnTitles_<?= $_REQUEST['name_w'] ?>= [];
	var rowsToShow_<?= $_REQUEST['name_w'] ?> = [5,10,20];
	

    var dt_actions_<?= $_REQUEST['name_w'] ?> = {
        pin: "hidden"
    }
    var dt_hoverMessage_<?= $_REQUEST['name_w'] ?> = [];

	var isCreated_<?= $_REQUEST['name_w'] ?> = false;

    $(document).ready(function <?= $_REQUEST['name_w'] ?>(firstLoad, metricNameFromDriver, widgetTitleFromDriver, widgetHeaderColorFromDriver, widgetHeaderFontColorFromDriver, fromGisExternalContent, fromGisExternalContentServiceUri, fromGisExternalContentField, fromGisExternalContentRange, /*randomSingleGeoJsonIndex,*/ fromGisMarker, fromGisMapRef) { <?php
        $link = mysqli_connect($host, $username, $password);
        if (checkWidgetNameInDashboard($link, $_REQUEST['name_w'], $_REQUEST['id_dashboard']) === false) {
            eventLog("Returned the following ERROR in widgetEvent.php for the widget " . escapeForHTML($_REQUEST['name_w']) . " is not instantiated or allowed in this dashboard.");
            exit();
        }

        $widgetCodeFromDb = null;
        $widgetNameSql = mysqli_real_escape_string($link, $_REQUEST['name_w']);
        $dashboardIdSql = mysqli_real_escape_string($link, $_REQUEST['id_dashboard']);
        $widgetCodeQuery = "SELECT code FROM Dashboard.Config_widget_dashboard WHERE name_w = '$widgetNameSql' AND id_dashboard = '$dashboardIdSql' LIMIT 1";
        $widgetCodeResult = mysqli_query($link, $widgetCodeQuery);
        if ($widgetCodeResult) {
            $widgetCodeRow = mysqli_fetch_assoc($widgetCodeResult);
            if ($widgetCodeRow && array_key_exists('code', $widgetCodeRow)) {
                $widgetCodeFromDb = $widgetCodeRow['code'];
            }
        }?> var hostFile = "<?= escapeForJS($_REQUEST['hostFile']) ?>";
        var widgetCodeFromDb = <?= json_encode($widgetCodeFromDb, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;
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
        function getWidgetCode(fallbackCode)
        {
            if (widgetCodeFromDb !== null && widgetCodeFromDb !== "null" && widgetCodeFromDb !== "") {
                return widgetCodeFromDb;
            }

            return fallbackCode;
        }

		$(document).off('showDeviceTableFromExternalContent_' + widgetName);
			$(document).on('showDeviceTableFromExternalContent_' + widgetName, function(event){
			console.log('showDeviceTableFromExternalContent_Code!');
					if(encodeURIComponent(metricName) === encodeURIComponent(metricName))
						{
							var newValue_<?= $_REQUEST['name_w'] ?> = event.passedData;
							//
							current_page_<?= $_REQUEST['name_w'] ?> = 0;
							$('#current_page_<?= $_REQUEST['name_w'] ?>').val(0);
							//$('#maintable_<?= $_REQUEST['name_w'] ?>').DataTable().destroy();
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
			console.log('CHANGE PAGE → start:', start, 'end:', end, 'current:', current);
			current_page_<?= $_REQUEST['name_w'] ?> = current;
			$('#current_page_<?= $_REQUEST['name_w'] ?>').val(current);
			//$('#maintable_<?= $_REQUEST['name_w'] ?>').DataTable().destroy();
			populateWidget(save_value_<?= $_REQUEST['name_w'] ?>_);
			
		}
		// Funzione debounce per ritardare l’esecuzione della ricerca
			function debounce(func, delay) {
				let debounceTimer;
				return function() {
					clearTimeout(debounceTimer);
					debounceTimer = setTimeout(() => func.apply(this, arguments), delay);
				};
			}

			// Funzione per eseguire la ricerca
			function handleSearch() {
				action_<?= $_REQUEST['name_w'] ?> = 'Searching';
				let searchLabel = $('#searchlabel_<?= $_REQUEST['name_w'] ?>').val();
				current_page_<?= $_REQUEST['name_w'] ?> = 0;

				if (typeof searchLabel === 'string') {
					$('#current_page_<?= $_REQUEST['name_w'] ?>').val(0);
					$('#start_value_<?= $_REQUEST['name_w'] ?>').val(0);
					$('#paging_table_<?= $_REQUEST['name_w'] ?>').empty();
					let search_end = $('#n_rows_<?= $_REQUEST['name_w'] ?>').val();
					console.log(search_end);
					change_page(0, search_end, 0);
				}
			}

			// Applicazione del debounce all’evento keyup con un ritardo di 300ms
			$('#searchlabel_<?= $_REQUEST['name_w'] ?>').on('keyup', debounce(handleSearch, 300));

		$('#n_rows_<?= $_REQUEST['name_w'] ?>').on('change', function() {
					action_<?= $_REQUEST['name_w'] ?> = 'ChangeNumberRows';
					maintable_length = parseInt($('#n_rows_<?= $_REQUEST['name_w'] ?>').val());
					current_page_<?= $_REQUEST['name_w'] ?> = 0;
					//$('#maintable_<?= $_REQUEST['name_w'] ?>').DataTable().destroy();
					$('#paging_table_<?= $_REQUEST['name_w'] ?>').empty();
					populateWidget(save_value_<?= $_REQUEST['name_w'] ?>_);
					$('.paginate_button.active').removeClass('active');
					$('#page_<?= $_REQUEST['name_w'] ?>0').addClass('active');
					current_page_<?= $_REQUEST['name_w'] ?> = 0;

					// NALDI -> save new value in cookie
					const value = parseInt($('#n_rows_<?= $_REQUEST['name_w'] ?>').val());
					const days = 30;
  					const expires = new Date(Date.now() + days * 24 * 60 * 60 * 1000).toUTCString();
  					const cookieDomain = "<?php echo $cookieDomain; ?>";
  					document.cookie =
  					  	`device_table_rows_<?= $_REQUEST['name_w'] ?>=${value}; ` +
  					  	`expires=${expires}; ` +
  					  	`path=/; ` +
  					  	`domain=${cookieDomain}; ` + 
					  	`Secure; ` + //NALDI -> in microx comment this
					  	`samesite=Strict`;
			});	
		

	function createTable() {
    const name_w = '<?= $_REQUEST['name_w'] ?>';
    const $table = $('#maintable_' + name_w);

    // pulizia sicura della table precedente
    if ($.fn.DataTable.isDataTable($table)) {
        $table.DataTable().clear().destroy();
        // rimuovo wrapper generato da DataTables (se presente)
        $table.removeClass('dataTable').empty();
    } 
    $table.empty();

    //var dataSet = window['dataSet_' + name_w];

    var rawDataSet = window['dataSet_' + name_w];
    var dataSet = Array.isArray(rawDataSet) ? rawDataSet : [];

    var columnsToShow = window['columnsToShow_' + name_w] || {};
    var arr_c = window['columnTitles_' + name_w] || [];
    var columns_list = window['columns_list_' + name_w] || []; // attenzione ai nomi: usa lo stesso ovunque
    var dt_actions = window['dt_actions_' + name_w] || {};

    // trovo title per device
    var title_dv = "device";
    if (arr_c.length > 0) {
        arr_c.forEach(function(it){
            if (it.value === 'device') title_dv = it.name;
        });
    }

    // costruisco columns in modo robusto (array compatto, senza buchi)
    var arr_col = [];
    arr_col.push({ data: "device", className: "expand-content all dt-center", orderable: true, title: title_dv });

    // prendo le keys della prima riga dati (assumo oggetti {key: value})
    console.log(dataSet);
    if (dataSet.length > 0){
            var k = Object.keys(dataSet[0]);
            for (var i = 0; i < k.length; i++) {
                var key = k[i];
                if (key === 'device' || key === 'serviceUri') continue;

                var class_n = columns_list.includes(key) ? '' : 'none';

                // ricerca titolo personalizzato
                var title = key;
                if (arr_c.length > 0) {
                    for (var y = 0; y < arr_c.length; y++) {
                        if (arr_c[y].value == key) { title = arr_c[y].name; break; }
                    }
                }
                arr_col.push({ title: title, data: key, className: class_n });
            }
        }else{
        columns_list.forEach(function (key) {
                    if (key === 'device' || key === 'serviceUri') return;

                    var class_n = columnsToShow[key] !== undefined ? '' : 'none';

                    var title = key;
                    arr_c.forEach(function (it) {
                        if (it.value === key) title = it.name;
                    });

                    arr_col.push({
                        title: title,
                        data: key,
                        className: class_n
                    });
                });
        }

    // colonna actions (se prevista)
    //var showActions = !(Object.keys(dt_actions).length === 1 && dt_actions.pin === 'hidden');
    const showActions = Object.entries(dt_actions).some(([key, value]) => {
                return (key === "pin" && value === "show") || (key !== "pin");
            });
    if (showActions) {
        var title_act = "Actions";
        var arrayHover = dt_hoverMessage_<?= $_REQUEST['name_w'] ?>;
        console.log(arrayHover);
        arr_c.forEach(function(it){ if (it.value === 'Actions') title_act = it.name; });
        console.log('dt_actions', dt_actions);
        arr_col.push({
            data: "actions",
            className: "all dt-center",
            orderable: false,
            title: title_act,
                render: function (data, type, row, meta) {
                    var body = "";
                    let hoverIndex = 0; // 👈 indice reale per arrayHover
                    Object.entries(dt_actions)
    .filter(([key, value]) => !(key === "pin" && value !== "show"))
    .forEach(([key, value]) => {

        let hover = arrayHover[hoverIndex];
        if (key === "pin") {
            body += '<button id="pin" class="btn actionButton' + name_w + '" style="margin-left:10px"' +
                (hover ? ' title="' + hover + '"' : '') +
                '><i class="fa fa-map-marker"></i></button>';
        } else {
            body += '<button id="' + key + '" class="btn actionButton' + name_w + '" style="margin-left:10px"' +
                (hover ? ' title="' + hover + '"' : '') +
                '><img style="max-width:20px" src="' + value + '" /></button>';
        }

        hoverIndex++;
    });
                    return body;
                }
        });
    }

    // ORDINE: prendo l'indice in modo sicuro (numero)
    var name_column = $('#order_column_' + name_w).val();
    var indexCol = 0; // default
    for (var idx = 0; idx < arr_col.length; idx++) {
        if (arr_col[idx].title === name_column) {
            indexCol = idx;
            break;
        }
    }
    // assicurarsi che sia numero (DataTables vuole numeri)
    indexCol = parseInt(indexCol, 10);
    if (isNaN(indexCol) || indexCol < 0 || indexCol >= arr_col.length) {
        indexCol = 0;
    }

    // sort direction safe
    var order_sort = $('#order_' + name_w).val() || 'desc';
    if (order_sort !== 'asc' && order_sort !== 'desc') order_sort = 'desc';

    // debug (se vuoi, commenta)
    //console.log('createTable - columns count:', arr_col.length);
    //console.log('createTable - sample data keys:', Object.keys(dataSet[0]));
    //console.log('createTable - indexCol:', indexCol);

    // init DataTable - uso initComplete per adjust/draw
    var table = $table.DataTable({
        data: dataSet,
        scrollResize: true,
        scrollY: '100px',
        scrollCollapse: true,
        paging: false,
        info: false,
        searching: false,
        responsive: { details: window['responsive_table_' + name_w] || false },
        ordering: true,
        order: [[indexCol, order_sort]],
        columns: arr_col,
        rowCallback: function (row, data, index) {
            $('.dataTables_scrollBody').css('overflow-x', 'hidden');
        },
        initComplete: function () {
            // assicurarsi che l'adeguamento delle colonne venga fatto quando la tabella è pronta
            this.api().columns.adjust().draw(false);
        }
    });

    // event handlers per bottoni azione
    $('.actionButton' + name_w).off('click').on('click', function () {
        var data = table.row($(this).closest('tr')).data();
        var order = table.order();
        var ordering = order[0][0];

        var dataToSend = {
            device: data ? data.device : null,
            query: $("#url_" + name_w).val(),
            ordering: (Object.keys(columnsToShow))[ordering],
            action: this.id
        };

        if ((typeof code !== 'undefined') && code) {
            data.action = this.id;
            // execute_... chiamerà la funzione specifica
            window['execute_' + name_w] && window['execute_' + name_w](data);
        }
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

			//////////
			// Funzione generica per la gestione dell'ordinamento
				function handleSorting(event, direction) {
					const name_w = '<?= $_REQUEST['name_w'] ?>';
					let text = $(event.target).text();

					// Verifica se ci sono titoli personalizzati e imposta il testo corretto
					if (columnTitles_<?= $_REQUEST['name_w'] ?>.length > 0) {
						columnTitles_<?= $_REQUEST['name_w'] ?>.forEach(item => {
							if (item.name === text) {
								text = item.value;
							}
						});
					}

					// Trova l’indice della colonna da ordinare
					let order_column_n = column_list_<?= $_REQUEST['name_w'] ?>.indexOf(text);
					if (text === 'device') order_column_n = 0;

					// Verifica se l'ordinamento è applicabile
					if (order_column_n >= 0 || text !== 'Actions') {
						$('#num_column_<?= $_REQUEST['name_w'] ?>').val(order_column_n);
						$('#current_page_<?= $_REQUEST['name_w'] ?>').val(0);
						//$('#maintable_<?= $_REQUEST['name_w'] ?>').DataTable().destroy();
						$('#paging_table_<?= $_REQUEST['name_w'] ?>').empty();

						// Gestione della direzione di ordinamento
						if (text === $('#order_column_<?= $_REQUEST['name_w'] ?>').val()) {
							$('#order_<?= $_REQUEST['name_w'] ?>').val(direction === 'asc' ? 'desc' : 'asc');
						} else {
							$('#order_column_<?= $_REQUEST['name_w'] ?>').val(text);
							$('#order_<?= $_REQUEST['name_w'] ?>').val(direction);
						}

						$('#start_value_<?= $_REQUEST['name_w'] ?>').val(0);
						$('.paginate_button.active').removeClass('active');
						$('#page_<?= $_REQUEST['name_w'] ?>0').addClass('active');
						current_page_<?= $_REQUEST['name_w'] ?> = 0;

						const n_rows_1 = $('#n_rows_<?= $_REQUEST['name_w'] ?>').val();
						change_page(0, n_rows_1, 0);
					} else {
						console.log('Do not sort on this');
					}
				}

				// Assegnazione della funzione agli eventi, specificando la direzione
				// Assegnazione della funzione agli eventi, specificando la direzione, con .off() per evitare duplicati
				$(document).off('click', '.sorting_<?= $_REQUEST['name_w'] ?>').on('click', '.sorting_<?= $_REQUEST['name_w'] ?>', function(event) {
					handleSorting(event, 'desc');
				});
				$(document).off('click', '.sorting_asc_<?= $_REQUEST['name_w'] ?>').on('click', '.sorting_asc_<?= $_REQUEST['name_w'] ?>', function(event) {
					handleSorting(event, 'asc');
				});
				$(document).off('click', '.sorting_desc_<?= $_REQUEST['name_w'] ?>').on('click', '.sorting_desc_<?= $_REQUEST['name_w'] ?>', function(event) {
					handleSorting(event, 'desc');
				});
					///////////////////END SORTING DeSC //////////////
					
        }
			//////
		
        async function populateWidget(newValue_<?= $_REQUEST['name_w'] ?>) {
			console.log(
					'POPULATE → current_page:',
					$('#current_page_<?= $_REQUEST['name_w'] ?>').val(),
					'start:',
					$('#start_value_<?= $_REQUEST['name_w'] ?>').val()
					);

			//
			$('#maintable_<?= $_REQUEST['name_w'] ?> tbody').empty();
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
			//console.log('start_value: '+start_value);
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
			//
			var query_filtered = $('#url_<?= $_REQUEST['name_w'] ?>').val();
			var query_corrected = query_filtered.replace(/&maxResults=[^&]*/g, '');
			query_filtered = query_corrected +ordering_data+filter_start+filter_max+filter_text;
			console.log(query_filtered);

			//ADDITIONAL HEADERS 
			var additional_headers = newValue_<?= $_REQUEST['name_w'] ?>.additionalHeaders || {}
			
			//
                $('#<?= $_REQUEST['name_w'] ?>_noDataAlert').hide();
                  await $.ajax({
                        //
						url: query_filtered,
                        type: "GET",
                        //async: false,
                        timeout: 0,
                        dataType: 'json',
						headers: additional_headers,
                                success: function (data) {
                                    console.log(data);

                                    const features = data.features || [];
                                    const fullCount = data.fullCount || 0;
                                    const n_feat = features.length;

                                    $("#totalResults_<?= $_REQUEST['name_w'] ?>").val(fullCount);

                                    setResponsiveFlag();

                                    // SEMPRE uscire dallo stato di loading
                                    $('#<?= $_REQUEST['name_w'] ?>_loading').hide();
                                    $('#<?= $_REQUEST['name_w'] ?>_content').show();

                                    // reset dataset
                                    dataSet_<?= $_REQUEST['name_w'] ?> = [];

                                    if (fullCount === 0) {
                                        // mostra messaggio
                                        $('#<?= $_REQUEST['name_w'] ?>_noDataAlert').show();

                                        // CREA comunque la tabella (solo header)
                                        createTable();

                                        // pulisci paginazione
                                        $('#paging_table_<?= $_REQUEST['name_w'] ?>').empty();
                                        return;
                                    }

                                    // caso con dati
                                    $('#<?= $_REQUEST['name_w'] ?>_noDataAlert').hide();

                                    const n_rows = parseInt($('#n_rows_<?= $_REQUEST['name_w'] ?>').val(), 10);
                                    dataSet_<?= $_REQUEST['name_w'] ?> = processFeatures(features, n_rows);

                                    createTable();
                                    setupPagination(fullCount, current_page_<?= $_REQUEST['name_w'] ?>, maintable_length);
                                },
                        error: function () {
                            count--;
                            if (count === 0) {
                                createTable();
                            }
                        }
                    });

          ////
		  $('th[aria-controls="maintable_<?= $_REQUEST['name_w'] ?>"]').each(function() {
			$(this).removeClass('expand-content all dt-center');
		  });
		  //expand-content all dt-center 
		  $('th.sorting[aria-controls="maintable_<?= $_REQUEST['name_w'] ?>"]').each(function() {
				//$(this).removeClass('sorting').addClass('sorting_<?= $_REQUEST['name_w'] ?>');
				$(this).addClass('sorting_<?= $_REQUEST['name_w'] ?>');
			});
			$('th.sorting_desc[aria-controls="maintable_<?= $_REQUEST['name_w'] ?>"]').each(function() {
				//$(this).removeClass('sorting_desc').addClass('sorting_desc_<?= $_REQUEST['name_w'] ?>');
				$(this).addClass('sorting_desc_<?= $_REQUEST['name_w'] ?>');
			});
			$('th.sorting_asc[aria-controls="maintable_<?= $_REQUEST['name_w'] ?>"]').each(function() {
				//$(this).removeClass('sorting_asc').addClass('sorting_asc_<?= $_REQUEST['name_w'] ?>');
				$(this).addClass('sorting_asc_<?= $_REQUEST['name_w'] ?>');
			});
		  ///

        }
		
		///ProcessFeatures DANIELE
		function processFeatures(features, n_rows) {
						const processed = [];
						features.slice(0, n_rows).forEach(f => {
							if (!f || !f.properties || !f.properties.values) return;
							const temp = { ...f.properties.values };
							temp.device = f.properties.deviceName;
							temp.serviceUri = f.properties.serviceUri;

							if (columnTitles_<?= $_REQUEST['name_w'] ?>.length > 0) {
								columnTitles_<?= $_REQUEST['name_w'] ?>.forEach(col => {
									if (temp[col.value] && col.format) {
										const d = new Date(temp[col.value]);
										temp[col.value] = moment(d).format(col.format);
									}
								});
							}

							processed.push(temp);
						});
						return processed;
					}
		////
		//Pagination DANIELE
function setupPagination(fullCount, current_page, maintable_length) {

    const n_page = Math.ceil(fullCount / maintable_length);
    const maxVisible = 5;
    const half = Math.floor(maxVisible / 2);

    let startPage = Math.max(0, current_page - half);
    let endPage = Math.min(n_page - 1, current_page + half);

    // assicura maxVisible
    if (endPage - startPage + 1 < maxVisible) {
        if (startPage === 0) {
            endPage = Math.min(n_page - 1, startPage + maxVisible - 1);
        } else if (endPage === n_page - 1) {
            startPage = Math.max(0, endPage - maxVisible + 1);
        }
    }

    let list_links = '';

    // ellissi iniziale
    if (startPage > 0) {
        list_links += `<li class="paginate_button disabled"><span>…</span></li>`;
    }

    for (let i = startPage; i <= endPage; i++) {
        const first_int = i * maintable_length;
        const end_int = first_int + maintable_length;
        const n = i + 1;

        list_links += `
            <li class="paginate_button n_lnk pag_<?= $_REQUEST['name_w'] ?>"
                id="page_<?= $_REQUEST['name_w'] ?>${i}"
                start="${first_int}" end="${end_int}" current="${i}">
                <a href="#" tabindex="0">${n}</a>
            </li>`;
    }

    // ellissi finale
    if (endPage < n_page - 1) {
        list_links += `<li class="paginate_button disabled"><span>…</span></li>`;
    }

    $('#paging_table_<?= $_REQUEST['name_w'] ?>').html(`
        <ul class="pagination">
            <li class="paginate_button first first_<?= $_REQUEST['name_w'] ?>" start="0" end="${maintable_length}" current="0">
                <a href="#">First</a>
            </li>
            <li class="paginate_button previous previous_<?= $_REQUEST['name_w'] ?>" current="${current_page - 1}">
                <a href="#">&lt;&lt; Prev</a>
            </li>
            ${list_links}
            <li class="paginate_button next next_<?= $_REQUEST['name_w'] ?>" current="${current_page + 1}">
                <a href="#">Next &gt;&gt;</a>
            </li>
            <li class="paginate_button last last_<?= $_REQUEST['name_w'] ?>" current="${n_page - 1}">
                <a href="#">Last</a>
            </li>
        </ul>
    `);

    $('#page_<?= $_REQUEST['name_w'] ?>' + current_page).addClass('active');
    attachPaginationEvents();
}


//Pagination RESPONSIVE DANIELE

			function setResponsiveFlag() {
				if (
					typeof newValue_<?= $_REQUEST['name_w'] ?> !== 'undefined' &&
					newValue_<?= $_REQUEST['name_w'] ?> !== null &&
					typeof newValue_<?= $_REQUEST['name_w'] ?>.responsive !== 'undefined'
				) {
					responsive_table_<?= $_REQUEST['name_w'] ?> =
						(newValue_<?= $_REQUEST['name_w'] ?>.responsive !== 'false' &&
						newValue_<?= $_REQUEST['name_w'] ?>.responsive !== false);
				} else {
					responsive_table_<?= $_REQUEST['name_w'] ?> = true;
				}
			}

////Evento ClickPaginazione DANIELE
function attachPaginationEvents() {
   $('.pag_<?= $_REQUEST['name_w'] ?>').off('click').on('click', function() {
        var start = parseInt($(this).attr('start'));
        var end = parseInt($(this).attr('end'));
        var current = parseInt($(this).attr('current'));

        $('#start_value_<?= $_REQUEST['name_w'] ?>').val(start);
        $('.paginate_button.active').removeClass('active');
		$(this).closest('.paginate_button').addClass('active');
        //$(this).addClass('active');

        // aggiorna next/prev
        var n_rows = parseInt($('#n_rows_<?= $_REQUEST['name_w'] ?>').val());

        action_<?= $_REQUEST['name_w'] ?> = 'ChangePage';
        change_page(start, end, current);
    });

    // First / Last / Next / Previous
    $('.first_<?= $_REQUEST['name_w'] ?>').off('click').on('click', function() {
        var start = 0;
        var end = parseInt($('#n_rows_<?= $_REQUEST['name_w'] ?>').val());
        //change_page(start, end, 0);
		goToPage(0);
    });

$('.last_<?= $_REQUEST['name_w'] ?>').off('click').on('click', function() {
    var total = parseInt($('#totalResults_<?= $_REQUEST['name_w'] ?>').val());
    var n_rows = parseInt($('#n_rows_<?= $_REQUEST['name_w'] ?>').val());
    var lastPage = Math.max(0, Math.ceil(total / n_rows) - 1);
    goToPage(lastPage);
});


$('.next_<?= $_REQUEST['name_w'] ?>').off('click').on('click', function() {
    if ($(this).hasClass('disabled')) return;

    var current = parseInt($('#current_page_<?= $_REQUEST['name_w'] ?>').val());
    goToPage(current + 1);
});


$('.previous_<?= $_REQUEST['name_w'] ?>').off('click').on('click', function() {
    if ($(this).hasClass('disabled')) return;

    var current = parseInt($('#current_page_<?= $_REQUEST['name_w'] ?>').val());
    goToPage(current - 1);
});

}

function updatePaginationUI(page) {
    $('.paginate_button').removeClass('active');
    $('#page_<?= $_REQUEST['name_w'] ?>' + page).addClass('active');
}

function goToPage(page) {

    var fullCount = parseInt($('#totalResults_<?= $_REQUEST['name_w'] ?>').val());
    var n_rows = parseInt($('#n_rows_<?= $_REQUEST['name_w'] ?>').val());

    if (!n_rows || n_rows <= 0) return;

    var maxPage = Math.max(0, Math.ceil(fullCount / n_rows) - 1);

    // 🔒 clamp della pagina
    page = Math.max(0, Math.min(page, maxPage));

    var start = page * n_rows;

    current_page_<?= $_REQUEST['name_w'] ?> = page;
    $('#current_page_<?= $_REQUEST['name_w'] ?>').val(page);
    $('#start_value_<?= $_REQUEST['name_w'] ?>').val(start);

    action_<?= $_REQUEST['name_w'] ?> = 'ChangePage';

    change_page(start, n_rows, page);
}



		
        //Definizioni di funzione specifiche del widget

        function resizeWidget() {
            setWidgetLayout(hostFile, widgetName, widgetContentColor, widgetHeaderColor, widgetHeaderFontColor, showHeader, headerHeight, hasTimer);
        }

        //Fine definizioni di funzione

        $(document).off('changeMetricFromButton_' + widgetName);
        $(document).on('changeMetricFromButton_' + widgetName, function (event) {
            if ((event.targetWidget === widgetName) && (event.newMetricName !== "noMetricChange")) {
                clearInterval(countdownRef);
                //$("#<?= $_REQUEST['name_w'] ?>_content").hide(); <?= $_REQUEST['name_w'] ?>(true, event.newMetricName, event.newTargetTitle, event.newHeaderAndBorderColor, event.newHeaderFontColor, false, null, null, /*null,*/ null, null);
            }
        });

        $(document).off('resizeHighchart_' + widgetName);
        $(document).on('resizeHighchart_' + widgetName, function (event) {
            showHeader = event.showHeader;
            $('#<?= $_REQUEST['name_w'] ?>_chartContainer').highcharts().reflow();
        });

        function addButtonsListerners(){
          

            //$('#tbody_<?= $_REQUEST['name_w'] ?>').on('click', 'td.expand-content', function () {
			$('#maintable_<?= $_REQUEST['name_w'] ?> tbody').on('click', 'td.expand-content', function () {
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
                    action: action_<?= $_REQUEST['name_w'] ?> };

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
				code = getWidgetCode(widgetData.params.code);

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
					console.log(newValue_<?= $_REQUEST['name_w'] ?>.actions);
                    for (let i = 0; i < newValue_<?= $_REQUEST['name_w'] ?>.actions.length; i++) {
                        if(dt_actions_<?= $_REQUEST['name_w'] ?>[newValue_<?= $_REQUEST['name_w'] ?>.actions[i]] !== undefined){
                            dt_actions_<?= $_REQUEST['name_w'] ?>[newValue_<?= $_REQUEST['name_w'] ?>.actions[i]] = "show";
                        }else{
                            dt_actions_<?= $_REQUEST['name_w'] ?>["custom" + customActionNumber] = newValue_<?= $_REQUEST['name_w'] ?>.actions[i];
                            customActionNumber++;
                        }
                    }

                    console.log(newValue_<?= $_REQUEST['name_w'] ?>);
                    if(newValue_<?= $_REQUEST['name_w'] ?>.hoverIcons){
                        console.log('hoverIcons');
                        console.log(newValue_<?= $_REQUEST['name_w'] ?>.hoverIcons);
                        for (let i = 0; i < newValue_<?= $_REQUEST['name_w'] ?>.hoverIcons.length; i++) {
                            dt_hoverMessage_<?= $_REQUEST['name_w'] ?>.push(newValue_<?= $_REQUEST['name_w'] ?>.hoverIcons[i]);
                         }
                    }

                    for (let i = 0; i < newValue_<?= $_REQUEST['name_w'] ?>.columnsToShow.length; i++) {
                        columnsToShow_<?= $_REQUEST['name_w'] ?>[newValue_<?= $_REQUEST['name_w'] ?>.columnsToShow[i]] = "";
                    }
                }
				///////
				//$('#current_page_<?= $_REQUEST['name_w'] ?>').val(0);
				//current_page_<?= $_REQUEST['name_w'] ?> = 0;
				if ($('#current_page_<?= $_REQUEST['name_w'] ?>').val() === '') {
						$('#current_page_<?= $_REQUEST['name_w'] ?>').val(0);
						current_page_<?= $_REQUEST['name_w'] ?> = 0;
					}

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
            try { <?php
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
                ?> // console.log(wsUrl);
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

                        dt_actions_<?= $_REQUEST['name_w'] ?> = {pin: "hidden"};

                        var customActionNumber = 1;
                        for (let i = 0; i < newValue_<?= $_REQUEST['name_w'] ?>.actions.length; i++) {
                            if(dt_actions_<?= $_REQUEST['name_w'] ?>[newValue_<?= $_REQUEST['name_w'] ?>.actions[i]] !== undefined){
                                dt_actions_<?= $_REQUEST['name_w'] ?>[newValue_<?= $_REQUEST['name_w'] ?>.actions[i]] = "show";
                            }else{
                                dt_actions_<?= $_REQUEST['name_w'] ?>["custom" + customActionNumber] = newValue_<?= $_REQUEST['name_w'] ?>.actions[i];
                                customActionNumber++;
                            }
                        }

                        if(newValue_<?= $_REQUEST['name_w'] ?>.hoverIcons){
                            console.log('hoverIcons');
                            console.log(newValue_<?= $_REQUEST['name_w'] ?>.hoverIcons);
                            for (let i = 0; i < newValue_<?= $_REQUEST['name_w'] ?>.hoverIcons.length; i++) {
                                dt_hoverMessage_<?= $_REQUEST['name_w'] ?>.push(newValue_<?= $_REQUEST['name_w'] ?>.hoverIcons[i]);
                            }
                        }else{
                            console.log('NO HOVER');
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
                Window.webSockets[widget] = websocket;
                websocket.onopen = function () {
                    success(websocket);
                };
                websocket.onclose = function () {
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
				<div style="display:none;">
					Order Column name:<input type="text" id="order_column_<?= $_REQUEST['name_w'] ?>">
					<br>Sorting Order: <input type="text" id="order_<?= $_REQUEST['name_w'] ?>">
					<br>Current page: <input type="text" id="current_page_<?= $_REQUEST['name_w'] ?>">
					<br>Column number:<input type="text" id="num_column_<?= $_REQUEST['name_w'] ?>">
					<br>start_value:<input type="text" id="start_value_<?= $_REQUEST['name_w'] ?>">
					<br>url:<input type="text" id="url_<?= $_REQUEST['name_w'] ?>">
					<br>total results: <input type="text" id="totalResults_<?= $_REQUEST['name_w'] ?>">
			</div>
<div class="table-list-header-pag">
<label class="mod2">Show	<select id="n_rows_<?= $_REQUEST['name_w'] ?>" aria-controls="maintable" class="form-control input-sm"><option value=5>5</option><option value=10>10</option><option value=20>20</option></select> </label>
         <div class="pull-right mod2"><div id="maintable_filter_<?= $_REQUEST['name_w'] ?>" class="dataTables_filter"><label>Search:<input type="search" class="form-control input-sm" placeholder="" aria-controls="maintable" id="searchlabel_<?= $_REQUEST['name_w'] ?>"></label></div></div>
			<div id="paging_table_<?= $_REQUEST['name_w'] ?>" class="mod2"></div>
</div>
            <table id="maintable_<?= $_REQUEST['name_w'] ?>" class="table table-striped table-bordered display responsive" cellspacing="0" style="width:100%">
				   </table>
        </div>
    </div>
	<div id="<?= $_REQUEST['name_w'] ?>_code"></div>
</div>