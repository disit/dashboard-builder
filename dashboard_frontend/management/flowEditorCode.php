<link rel="stylesheet" href="../css/drawflow.min.css">
<link rel="stylesheet" type="text/css" href="../css/editor.css" />

<script src="../js/drawFlow.js"></script>

<div class="wrapper">
    <div id="drawflow">
        <div class="btn-export" onclick=closeEditor()>Save & Exit</div>
        <a id="btn-legenda" data-toggle="collapse" href="#legenda" role="button" >
          <div class="btn-legenda">Legend</div>
        </a>
        <div class="legenda collapse" id="legenda">
          <p><b><u>Output Port Types</u></b></p>
          <p>
            🔴 ListSURI<br>   
            🟢 DateTime Interval<br>   
            🔵 SURI<br>   
            🟡 DateTime<br>  
            🟠 Action<br>  
            🟣 GPS Coordinates<br>   
            🟤 ResetCommand<br>   
            ⚪ JSON<br> 
          </p>     
          <p><b><u>Shortkeys</u></b></p>        
          <p>   
            💠 Mouse Left Click == Move and Drag<br>
          <!--  ❌ Mouse Right Click == Delete Option<br>    -->
            🔍 Ctrl + Wheel == Zoom<br>        
          </p>
          <p><b><u>Info</u></b></p>        
          <p>   
            Every widget have standard variables : <br>
            <ul>
              <li><b>e</b> : It represents a JSON of parameters passed to JS after an event occur.</li>
              <li><b>connections</b> : It represents a JSON of connections table.</li>
            </ul>
          </p>
        </div>
        <div class="bar-zoom">
          <i class="fa fa-search-minus" onclick="editor.zoom_out()"></i>
          <i class="fa fa-search" onclick="editor.zoom_reset()"></i>
          <i class="fa fa-search-plus" onclick="editor.zoom_in()"></i>
        </div>
    </div>
</div>

<script>
    //IMPORTANT for acceding some data of the node for update, like the HTML or others, we need to use <--editor.drawflow.drawflow[module].data[id]-->.
    let df = document.getElementById("drawflow");
    let editor = new Drawflow(df);
    let module = "Home";
    let dashboard;
    var alertSaveCsblEditor = false;
    editor.reroute = true;
    editor.reroute_fix_curvature = true;
    editor.start();

    function encodeHTMLEntities(text) {
    /*    let textArea = document.createElement('textarea');
        textArea.innerText = text;
        return textArea.innerHTML;  */

        return String(str).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
    }

    function openEditor(){
        editor.clear();
        editor.nodeId = 1;
        $.ajax({
            url: "../management/get_data.php",
            data: {
                action: "getDashboardParamsAndWidgetsNR",
                notBySession: "true",
                dashboardId: '<?= escapeForJS($_GET["dashboardId"]) ?>',
                username: "<?= $dashboardAuthorName ?>"
            },
            type: "GET",
            async: true,
            cache: false,
            dataType: 'json',
            success: function (data) {
                dashboard = data.dashboardWidgets;
                dashboard = convertToEditorAPI(dashboard);
                setTimeout(function(){
                    importDashboard(dashboard, data.dashboardParams.drawFlowEditor)
                },1000);
            },
            error: function (errorData) {
                alert('Error');
            }
        });
    }

    function convertToEditorAPI(dash_widget_list) {
        let new_dashboard = [];
        let element_schema = {"widget_name": "", "widget_type": "", "ck_editor": "", "compatibilityFlag": ""};
        let new_element;
        Object.keys(dash_widget_list).forEach(i => {
            if(widget_data.hasOwnProperty(dash_widget_list[i].type_w)){
                new_element = JSON.parse(JSON.stringify(element_schema));

                new_element.widget_name = dash_widget_list[i].name_w;
                new_element.widget_type = dash_widget_list[i].type_w;

                if (dash_widget_list[i].code != null) {
                //    if (dash_widget_list[i].connections == null && dash_widget_list[i].code.includes("var connections = ")) {
                        new_element.ck_editor = dash_widget_list[i].code;
                //     } else {
                //        new_element.ck_editor = insertSubstring(dash_widget_list[i].code, (dash_widget_list[i].connections.replace('\\[', '[')).replace('\\]', ']'), 'JSON.parse(param);\n\t');
                //     }
                } else {
                    new_element.ck_editor = "function execute(){\n\/\/events_code_start\n\/\/events_code_part_start\n\/\/events_code_part_end\n\/\/events_code_end\n}";
                }

                new_dashboard.push(new_element);
            }
        });
        return new_dashboard;
    }

    function closeEditor() {
        var confirmSaveWarning = true;
        if (alertSaveCsblEditor) {
            confirmSaveWarning = confirm("The CSBL code in one or more widgets of this dashboard IS NOT COMPLIANT with " +
                "the format of this CSBL Editor. Saving from this CSBL Editor may lead to NOT PROPER saving of your CSBL " +
                "code, and therefore your CSBL MAY NOT WORK PROPERYL. Click 'OK' if you want to confirm your operation " +
                "and Save.");
        }
        if (confirmSaveWarning) {
            let exit_dashboard = exportDashboard(dashboard);
            $.ajax({
                url: "../controllers/updateDashboard.php",
                data: {
                    action: "updateDrawFlowEditor",
                    dashboardId: <?= escapeForJS($_REQUEST['dashboardId']) ?>,
                    drawFlowJson: JSON.stringify(editor.drawflow.drawflow.Home.data),
                },
                type: "POST",
                async: true,
                dataType: 'json',
                success: function(data)
                {
                    if(data.detail === 'Ok') {
                        // alert('CSBL Flow Saved.');
                    } else if (data.detail === 'queryKo') {
                        alert('Error saving CSBL Flow.');
                    } else if (data.detail === 'notValidJson') {
                        alert('Error saving CSBL Flow: CSBL Flow results to be a NOT VALID Json.');
                    }
                },
                error: function(errorData)
                {
                    alert('Error saving CSBL Flow (generic).');
                }
            });
            for (let i = 0; i < exit_dashboard.length; i++) {
                //let text = encodeHTMLEntities(exit_dashboard[i].ck_editor);
                let connections = extractSubstring(exit_dashboard[i].ck_editor, "var connections = \\[", "\\];");
                if (connections != null) {
                    let ck_text = exit_dashboard[i].ck_editor.replace((connections.replace('\\[', '[')).replace('\\]', ']'), '');
                } else {
                    let ck_text = exit_dashboard[i].ck_editor;
                }
                //let ck_text = removeSubstring(exit_dashboard[i].ck_editor, connections);
                let action = "";
                exit_dashboard[i].compatibilityFlag ? action = "updateCsblEditor" : action="updateCkEditor";
                $.ajax({
                    url: "../controllers/updateWidget.php",
                    data: {
                        action: action,
                        widgetName: exit_dashboard[i].widget_name,
                        newText: exit_dashboard[i].ck_editor,
                        //newText: ck_text,
                        connections: connections,
                    },
                    type: "POST",
                    async: true,
                    dataType: 'json',
                    success: function (data) {
                        if (data.detail === 'Ok') {
                            //alert('Saved ck_editor of ' + exit_dashboard[i].widget_name);
                        } else {
                            alert('Error Saving ck_editor of ' + exit_dashboard[i].widget_name);
                        }
                    },
                    error: function (errorData) {
                        alert('Error');
                    }
                });
            }
            alert('CSBL saved!');
            $('#flowEditor').modal('hide');
            location.reload();
        }
    }

    //COPY MODELS

    /**HOW TO CONSTRUCT A FUNCTION
    function sendFunction(port_name, ....){

        //data code
        ....
        //end data code
        
        //Find and check validity of the specific port of an event of a widget
        let check_validity = false,conn_id;
        Object.keys(connections).forEach(id => {
            if(connections[id].port_name == port_name){
                if(connections[id].output_type == "JSON"){
                    conn_id = id;
                    check_validity = true;
                }
            }       
        });

        if(check_validity){
            let widget_type,widget_name;
            for(let i=0; i<connections[conn_id].linked_target_widgets.length;i++){
                    
                widget_type = connections[conn_id].linked_target_widgets[i].widget_type;
                widget_name = connections[conn_id].linked_target_widgets[i].widget_name;
                    
                switch (widget_type) {
                    case 'widgetRadarSeries':
                        break;
            
                    case 'widgetTimeTrend':
                        break;
            
                    case 'widgetCurvedLineSeries':
                        break;
            
                    case 'widgetPieChart':
                        break;
            
                    case 'widgetBarSeries':
                        break;
            
                    case 'widgetMap':
                        break;
            
                    case 'widgetSpeedometer':
                        break;
            
                    case 'widgetGaugeChart':
                        break;
            
                    case 'widgetKnob':
                        break;
            
                    case 'widgetNumericKeyboard':
                        break;
            
                    case 'widgetSingleContent':
                        break;
            
                    case 'widgetExternalContent':
                        break;
            
                    case 'widgetTable':
                        break;
            
                    case 'widgetDeviceTable':
                        break;
            
                    case 'widgetEventTable':
                        break;
            
                    case 'widgetButton':
                        break;
            
                    case 'widgetOnOffButton':
                        break;
            
                    case 'widgetImpulseButton':
                        break;
            
                    default:
                }

            }
        }
    }
    */
    let functions = [
        /*
        {"func_name":"" +
                "", "func_code":`function sendSURI(port_name, jsonData){
            //data code
            var data = [];
            data[0] = {};
            var coordsAndType = [];
            coordsAndType[0] = {};
            var serviceUri = "";
            if (jsonData.value.metricName.includes(":")) {
                serviceUri = "<?= $baseServiceURI ?>" + jsonData.value.metricName.split(":")[1] + "/" + jsonData.value.metricName.split(":")[0] + "/" + jsonData.value.metricName.split(":")[2];
                data[0].metricId = "<?= $kbUrlSuperServiceMap ?>?serviceUri=" + serviceUri + "&format=json";
                data[0].metricHighLevelType = "IoT Device Variable";
                coordsAndType[0].query = "<?= $kbUrlSuperServiceMap ?>?serviceUri=" + serviceUri + "&format=json";
                coordsAndType[0].queryType = "Default";
            } else {
                serviceUri = jsonData.value.metricId;
                data[0].metricId = serviceUri;
                data[0].metricHighLevelType = "MyKPI";
                coordsAndType[0].query = "datamanager/api/v1/poidata/" + serviceUri;
                coordsAndType[0].queryType = "MyPOI";
            }
            data[0].metricName = jsonData.value.metricName;
            data[0].metricType = jsonData.value.metricType;
            data[0].smField = jsonData.value.metricType;
            data[0].serviceUri = serviceUri;

            coordsAndType[0].desc = data[0].metricName;
            coordsAndType[0].color1 = "#ebb113";
            coordsAndType[0].color2 = "#eb8a13";
            //end data code

            csblTrigger("SURI", port_name, data, e.event, null, null);
        }`},
        {"func_name":"sendListSURIandMetrics", "func_code":`function sendListSURIandMetrics(port_name, jsonData){
            //data code
            var coordsAndType = [];
            var data = [];
            var h = 0;
            var i = 0;
            var serviceUri = "";
            for (var l in jsonData.layers) {
                if (jsonData.layers[l].visible == true) {
                    coordsAndType[i] = {};
                    coordsAndType[i].desc = jsonData.layers[l].name;
                    coordsAndType[i].color1 = "#ebb113";
                    coordsAndType[i].color2 = "#eb8a13";
                    if (!jsonData.metrics || jsonData.metrics.length<1) {
                        if (jsonData.layers[l].realtimeAttributes) {
                            jsonData.metrics = Object.keys(jsonData.layers[l].realtimeAttributes);
                        }
                        if (jsonData.layers[l].kpidata) {
                            jsonData.metrics = jsonData.layers[l].name;
                        }
                    }
                    for (var m in jsonData.metrics) {
                        data[h] = {};
                        if (jsonData.layers[l].name.includes(":")) {
                            serviceUri = "<?= $baseServiceURI ?>" + jsonData.layers[l].name.split(":")[1] + "/" + jsonData.layers[l].name.split(":")[0] + "/" + jsonData.layers[l].name.split(":")[2];
                            data[h].metricId = "<?= $kbUrlSuperServiceMap ?>?serviceUri=" + serviceUri + "&format=json";
                            data[h].metricHighLevelType = "IoT Device Variable";
                            coordsAndType[i].query = "<?= $kbUrlSuperServiceMap ?>?serviceUri=" + serviceUri + "&format=json";
                            coordsAndType[i].queryType = "Default";
                        } else if ((jsonData.layers[l].brokerName && jsonData.layers[l].brokerName != "") && (jsonData.layers[l].organization && jsonData.layers[l].organization != "")) {
                            serviceUri = "<?= $baseServiceURI ?>"+ jsonData.layers[l].brokerName + "/" + jsonData.layers[l].organization + "/" + jsonData.layers[l].name;
                            data[h].metricId = "<?= $kbUrlSuperServiceMap ?>?serviceUri=" + serviceUri + "&format=json";
                            data[h].metricHighLevelType = "IoT Device Variable";
                            coordsAndType[i].query = "<?= $kbUrlSuperServiceMap ?>?serviceUri=" + serviceUri + "&format=json";
                            coordsAndType[i].queryType = "Default";
                        } else if (jsonData.layers[l].serviceUri && jsonData.layers[l].serviceUri != "") {
                            serviceUri = jsonData.layers[l].serviceUri;
                            data[h].metricId = "<?= $kbUrlSuperServiceMap ?>?serviceUri=" + serviceUri + "&format=json";
                            data[h].metricHighLevelType = "IoT Device Variable";
                            coordsAndType[i].query = "<?= $kbUrlSuperServiceMap ?>?serviceUri=" + serviceUri + "&format=json";
                            coordsAndType[i].queryType = "Default";
                        } else {
                            if (jsonData.layers[l].name.includes("_")) {
                                serviceUri = "datamanager/api/v1/poidata/" + jsonData.layers[l].name.split("_")[1];
                            } else {
                                serviceUri = "datamanager/api/v1/poidata/" + jsonData.layers[l].name;
                            }
                            data[h].metricId = serviceUri;
                            data[h].metricHighLevelType = "MyKPI";
                            coordsAndType[i].query = serviceUri;
                            coordsAndType[i].queryType = "MyPOI";
                        }
                        data[h].metricName = jsonData.layers[l].name;
                        data[h].metricType = jsonData.metrics[m];
                        data[h].smField = jsonData.metrics[m];
                        data[h].serviceUri = serviceUri;

                        h++;
                    }
                    i++;
                }
            }
            //end data code

            csblTrigger("ListSURI", port_name, data, e.event, null, null);
        }`},
        {"func_name":"sendTimeRange", "func_code":`function sendTimeRange(port_name, jsonData){

            var minT = jsonData["t1"];
            var maxT = jsonData["t2"];
            var dt1 = new Date(minT);
            var dt1_iso = dt1.toISOString().split(".")[0];
            var dt2 = new Date(maxT);
            var dt2_iso = dt2.toISOString().split(".")[0];

            //Find and check validity of the specific port of an event of a widget
            let check_validity = false,conn_id;
            Object.keys(connections).forEach(id => {
                if(connections[id].port_name == port_name){
                    if(connections[id].output_type == "DateTime_Interval"){
                        conn_id = id;
                        check_validity = true;
                    }
                }
            });

            if(check_validity){
                let widget_type,widget_name;
                for(let i=0; i<connections[conn_id].linked_target_widgets.length;i++){

                    widget_type = connections[conn_id].linked_target_widgets[i].widget_type;
                    widget_name = connections[conn_id].linked_target_widgets[i].widget_name;

                    switch (widget_type) {
                        case 'widgetRadarSeries':
                            break;

                        case 'widgetTimeTrend':
                            break;

                        case 'widgetCurvedLineSeries':
                            $('body').trigger({
                                type: "showCurvedLinesFromExternalContent_" + widget_name,
                                targetWidget: widget_name,
                                t1: dt1_iso,
                                t2: dt2_iso
                            });
                            break;

                        case 'widgetPieChart':
                            break;

                        case 'widgetBarSeries':
                            break;

                        case 'widgetMap':
                            break;

                        case 'widgetSpeedometer':
                            break;

                        case 'widgetGaugeChart':
                            break;

                        case 'widgetKnob':
                            break;

                        case 'widgetNumericKeyboard':
                            break;

                        case 'widgetSingleContent':
                            break;

                        case 'widgetExternalContent':
                            break;

                        case 'widgetTable':
                            break;

                        case 'widgetDeviceTable':
                            break;

                        case 'widgetEventTable':
                            break;

                        case 'widgetButton':
                            break;

                        case 'widgetOnOffButton':
                            break;

                        case 'widgetImpulseButton':
                            break;

                        default:
                    }

                }
            }
        }`},
        {"func_name":"sendJSON", "func_code":`function sendJSON(port_name, jsonData){

            //Find and check validity of the specific port of an event of a widget
            let check_validity = false,conn_id;
            Object.keys(connections).forEach(id => {
                if(connections[id].port_name == port_name){
                    if(connections[id].output_type == "JSON"){
                        conn_id = id;
                        check_validity = true;
                    }
                }
            });

            if(check_validity){
                let widget_type,widget_name;
                for(let i=0; i<connections[conn_id].linked_target_widgets.length;i++){

                    widget_type = connections[conn_id].linked_target_widgets[i].widget_type;
                    widget_name = connections[conn_id].linked_target_widgets[i].widget_name;

                    switch (widget_type) {
                        case 'widgetRadarSeries':
                            break;

                        case 'widgetTimeTrend':
                            break;

                        case 'widgetCurvedLineSeries':
                            $('body').trigger({
                                type: "showCurvedLinesFromExternalContent_" + widget_name,
                                targetWidget: widget_name,
                                passedData: jsonData
                            });
                            break;

                        case 'widgetPieChart':
                            break;

                        case 'widgetBarSeries':
                            break;

                        case 'widgetMap':
                            break;

                        case 'widgetSpeedometer':
                            break;

                        case 'widgetGaugeChart':
                            break;

                        case 'widgetKnob':
                            break;

                        case 'widgetNumericKeyboard':
                            break;

                        case 'widgetSingleContent':
                            break;

                        case 'widgetExternalContent':
                            break;

                        case 'widgetTable':
                            break;

                        case 'widgetDeviceTable':
                            break;

                        case 'widgetEventTable':
                            break;

                        case 'widgetButton':
                            break;

                        case 'widgetOnOffButton':
                            break;

                        case 'widgetImpulseButton':
                            break;

                        default:
                    }

                }
            }
        }`},
        */
    ]

    let widget_data = {
        "widgetRadarSeries" : {"widget_ports":"IN/OUT", "widget_type":"widgetRadarSeries", "next_port_box": 0, "port_boxes":{}, "events":{"external_commands":{"ev_name":"externalCommands","code":""},"click":{"ev_name":"click","code":""},"legend_item_click":{"ev_name":"legendItemClick","code":""}}},
        "widgetTimeTrend" : {"widget_ports":"IN/OUT", "widget_type":"widgetTimeTrend", "next_port_box": 0, "port_boxes":{}, "events":{"external_commands":{"ev_name":"externalCommands","code":""}}},
        "widgetCurvedLineSeries" : {"widget_ports":"IN/OUT", "widget_type":"widgetCurvedLineSeries", "next_port_box": 0, "port_boxes":{}, "events":{"external_commands":{"ev_name":"externalCommands","code":""},"click":{"ev_name":"click","code":""},"legend_item_click":{"ev_name":"legendItemClick","code":""}, "time_zoom": {"ev_name":"zoom","code":""}, "reset_zoom": {"ev_name":"reset zoom","code":""}}},
        "widgetPieChart" : {"widget_ports":"IN/OUT", "widget_type":"widgetPieChart", "next_port_box": 0, "port_boxes":{}, "events":{"external_commands":{"ev_name":"externalCommands","code":""},"click":{"ev_name":"click","code":""},"legend_item_click":{"ev_name":"legendItemClick","code":""}}},
        "widgetBarSeries" : {"widget_ports":"IN/OUT", "widget_type":"widgetBarSeries", "next_port_box": 0, "port_boxes":{}, "events":{"external_commands":{"ev_name":"externalCommands","code":""},"click":{"ev_name":"click","code":""},"legend_item_click":{"ev_name":"legendItemClick","code":""}}},
        "widgetMap" : {"widget_ports":"IN/OUT", "widget_type":"widgetMap", "next_port_box": 0, "port_boxes":{}, "events":{"external_commands":{"ev_name":"externalCommands","code":""},"click":{"ev_name":"click","code":""},"geo_drill_down":{"ev_name":"zoom","code":""}}},
        "widgetSpeedometer" : {"widget_ports":"IN", "widget_type":"widgetSpeedometer", "next_port_box": 0, "port_boxes":{}, "events":{"external_commands":{"ev_name":"externalCommands","code":""}}},
        "widgetGaugeChart" : {"widget_ports":"IN", "widget_type":"widgetGaugeChart", "next_port_box": 0, "port_boxes":{}, "events":{"external_commands":{"ev_name":"externalCommands","code":""}}},
        //"widgetKnob" : {"widget_ports":"IN/OUT", "widget_type":"widgetKnob", "next_port_box": 0, "port_boxes":{}, "events":{"external_commands":{"ev_name":"externalCommands","code":""}}},
        //"widgetNumericKeyboard" : {"widget_ports":"IN/OUT", "widget_type":"widgetNumericKeyboard", "next_port_box": 0, "port_boxes":{}, "events":{"external_commands":{"ev_name":"externalCommands","code":""}}},
        "widgetSingleContent" : {"widget_ports":"IN", "widget_type":"widgetSingleContent", "next_port_box": 0, "port_boxes":{}, "events":{"external_commands":{"ev_name":"externalCommands","code":""}}},
        "widgetExternalContent" : {"widget_ports":"IN", "widget_type":"widgetExternalContent", "next_port_box": 0, "port_boxes":{}, "events":{"external_commands":{"ev_name":"externalCommands","code":""}}},
        //"widgetExternalContent" : {"widget_ports":"IN/OUT", "widget_type":"widgetExternalContent", "next_port_box": 0, "port_boxes":{}, "events":{"external_commands":{"ev_name":"externalCommands","code":""}}},
        "widgetTable" : {"widget_ports":"IN", "widget_type":"widgetTable", "next_port_box": 0, "port_boxes":{}, "events":{"external_commands":{"ev_name":"externalCommands","code":""}}},
        "widgetDeviceTable" : {"widget_ports":"IN/OUT", "widget_type":"widgetDeviceTable", "next_port_box": 0, "port_boxes":{}, "events":{"external_commands":{"ev_name":"externalCommands","code":""}}},
        //"widgetEventTable" : {"widget_ports":"OUT", "widget_type":"widgetEventTable", "next_port_box": 0, "port_boxes":{}, "events":{"click":{"ev_name":"click","code":""}}},
        "widgetButton" : {"widget_ports":"OUT", "widget_type":"widgetButton", "next_port_box": 0, "port_boxes":{}, "events":{"click":{"ev_name":"click","code":""}}},
        //"widgetOnOffButton" : {"widget_ports":"IN/OUT", "widget_type":"widgetOnOffButton", "next_port_box": 0, "port_boxes":{}, "events":{"external_commands":{"ev_name":"externalCommands","code":""}}},
        "widgetImpulseButton" : {"widget_ports":"OUT", "widget_type":"widgetImpulseButton", "next_port_box": 0, "port_boxes":{}, "events":{"click":{"ev_name":"click","code":""}}},
        "widgetCalendar" : {"widget_ports":"IN", "widget_type":"widgetCalendar", "next_port_box": 0, "port_boxes":{}, "events":{"external_commands":{"ev_name":"externalCommands","code":""}}},
    };

    let port_box = {"port_name": "", "associated_output_node": 0, "port_type":{}};

    let port_types = [
        {"output_type": "JSON", "perform_widgets_typologies":takeWidgetTypeListFromPortTypes(["IN/OUT","OUT"]), "target_widgets_typologies":takeWidgetTypeListFromPortTypes(["IN/OUT","IN"]), "color_class": "port-white"},
        {"output_type": "ListSURI", "perform_widgets_typologies":takeWidgetTypeListFromPortTypes(["IN/OUT","OUT"]), "target_widgets_typologies":takeWidgetTypeListFromPortTypes(["IN/OUT","IN"]), "color_class": "port-red"},
        {"output_type": "DateTime_Interval", "perform_widgets_typologies":takeWidgetTypeListFromPortTypes(["IN/OUT","OUT"]), "target_widgets_typologies":takeWidgetTypeListFromPortTypes(["IN/OUT","IN"]), "color_class": "port-green",},
        {"output_type": "SURI", "perform_widgets_typologies":takeWidgetTypeListFromPortTypes(["IN/OUT","OUT"]), "target_widgets_typologies":takeWidgetTypeListFromPortTypes(["IN/OUT","IN"]), "color_class": "port-blue"},
        {"output_type": "DateTime", "perform_widgets_typologies":takeWidgetTypeListFromPortTypes(["IN/OUT","OUT"]), "target_widgets_typologies":takeWidgetTypeListFromPortTypes(["IN/OUT","IN"]), "color_class": "port-yellow"},
        {"output_type": "Action", "perform_widgets_typologies":takeWidgetTypeListFromPortTypes(["IN/OUT","OUT"]), "target_widgets_typologies":takeWidgetTypeListFromPortTypes(["IN/OUT","IN"]), "color_class": "port-orange"},
        {"output_type": "GPS_coordinates", "perform_widgets_typologies":takeWidgetTypeListFromPortTypes(["IN/OUT","OUT"]), "target_widgets_typologies":takeWidgetTypeListFromPortTypes(["IN/OUT","IN"]), "color_class": "port-purple"},
        {"output_type": "ResetCommand", "perform_widgets_typologies":takeWidgetTypeListFromPortTypes(["IN/OUT","OUT"]), "target_widgets_typologies":takeWidgetTypeListFromPortTypes(["IN/OUT","IN"]), "color_class": "port-brown"}
        //,"color_class": "port-black"
    ];

    //EDITOR EVENTS


    editor.on('nodeSelected', function (id) {
        //console.log("Node selected")
        //console.log(editor.getNodeFromId(id));
    })

    editor.on('connectionCreated', function (connection) {
        let input_id = connection.input_id;
        let input_class = connection.input_class;
        let output_id = connection.output_id;
        let output_class = connection.output_class;
        Object.keys(editor.getNodeFromId(output_id).data.port_boxes).forEach(port_box_id =>{
            if(output_class == "output_"+editor.getNodeFromId(output_id).data.port_boxes[port_box_id].associated_output_node){
                let output_port_type = editor.getNodeFromId(output_id).data.port_boxes[port_box_id].port_type;
                if(!output_port_type.target_widgets_typologies.includes(editor.getNodeFromId(input_id).data.widget_type)){
                    editor.removeSingleConnection(output_id,input_id,output_class,input_class);
                }
            }
        })
        editor.updateConnectionNodes("node-"+input_id);
        console.log("Connection created");
    })

    editor.on('connectionRemoved', function (connection) {
        console.log("Connection removed");
    })


    //FUNCTIONAL METHODS


    //an easy way function for take widget typologies based on port types
    function takeWidgetTypeListFromPortTypes(port_types){
        let arr = [];
        Object.keys(widget_data).forEach(widget_type =>{
            if(port_types.includes(widget_data[widget_type].widget_ports))
                arr.push(widget_type);
        });
        return arr;
    }

    //update editor field HTML of a node
    function updateHTMLFromNodeId(id){
        let nodeContent = document.getElementById("node-"+id).childNodes[1];
        editor.drawflow.drawflow[module].data[id].html = nodeContent.innerHTML;

        //console.log("updateHTMLFromNodeId("+id+")");
        //console.log(editor);
    }


    function findPositionInDrawFlow(widgetName, drawFlowEditor) {
        let position = {};
        let keys = Object.keys(drawFlowEditor);
        for (let i=0; i < keys.length; i++) {
            let key = keys[i];
            if (widgetName === drawFlowEditor[key].name) {
                if (drawFlowEditor[key].pos_x && drawFlowEditor[key].pos_y) {
                    position = { pos_x: drawFlowEditor[key].pos_x, pos_y: drawFlowEditor[key].pos_y };
                    break;
                }
            }
        }
        return position;
    }

    //IMPORT METHODS

    //convert our model API into node //TODO to finish for importing splitted CKeditor
    function importDashboard(dashboard, drawFlowEditorStr){

        let drawFlowEditor = "";
        if (IsJsonString(drawFlowEditorStr)) {
            drawFlowEditor = JSON.parse(drawFlowEditorStr);
        }
        let splitted_ck_editor;
        let widgetPos;
        for(var i=0;i<dashboard.length;i++){
            if (dashboard[i].ck_editor.indexOf("\/\/events_code_start") == -1 && dashboard[i].ck_editor.lastIndexOf("\/\/events_code_end") == -1) {
                dashboard[i].compatibilityFlag = false;
                splitted_ck_editor =  [];
                splitted_ck_editor[0] = {"ev_name":"externalCommands","code":dashboard[i].ck_editor};
                alertSaveCsblEditor = true;
                alert("WARNING: CSBL code in widget '" + dashboard[i].widget_name + "' is not in the proper format to be managed and " +
                    "edited by the CSBL Editor. It may have been created directly in the CK Editor (in the widget " +
                    "\"More Options\" panel), therefore it MAY NOT WORK PROPERLY if edited and saved in this CSBL Editor.");
                //splitted_ck_editor = dashboard[i].ck_editor;
            } else {
                dashboard[i].compatibilityFlag = true;
                splitted_ck_editor = splitCKeditorCode(dashboard[i].ck_editor, dashboard[i].widget_name, dashboard[i].compatibilityFlag);
            }
            //splitted_ck_editor = splitCKeditorCode(dashboard[i].ck_editor, dashboard[i].widget_name, dashboard[i].compatibilityFlag);
            if (drawFlowEditor) {
                widgetPos = findPositionInDrawFlow(dashboard[i].widget_name, drawFlowEditor);
                addNodeToEditor(i + 1, dashboard[i].widget_type, dashboard[i].widget_name, splitted_ck_editor, widgetPos["pos_x"], widgetPos["pos_y"], dashboard[i].compatibilityFlag);
            } else {
                addNodeToEditor(i+1, dashboard[i].widget_type, dashboard[i].widget_name, splitted_ck_editor,50+800*(i%3),50+600*(Math.floor(i/3)), dashboard[i].compatibilityFlag);
            }
        }
        for(var i=0;i<dashboard.length;i++){
            addExistingPortsToNode(i+1, dashboard[i].ck_editor);
        }

        console.log("importDashboard(dashboard)");
        console.log(dashboard);
        //console.log(editor);
    }

    //add node into our editor based on widget type //TODO to finish
    function addNodeToEditor(id, type, name, splitted_ck_editor , pos_x, pos_y, compatibilityFlag){
        var html;
        if (compatibilityFlag) {
            var html = `<div class="title-box"> ` + type + ` ` + "(" + widget_data[type].widget_ports + ")" + `</div>`;
        } else {
            var html = `<div class="title-box" style="background-color: #fce06d; color: #b52407;"> ` + type + ` ` + "(" + widget_data[type].widget_ports + ")" + `</div>`;
        }
        var data = JSON.parse(JSON.stringify(widget_data[type]));

        //Update node HTML
        if(widget_data[type].widget_ports != "IN"){
            html += `
            <div class="box-full">
                <div class="widget-body">
                    <div class="widget-body-sixty">
                        <div class="widget-body-element-name bottom-separator">
                            <label for="widgetname-`+id+`">WidgetName</label>
                            <input id="widgetname-`+id+`" type="text" value=`+name+` readonly></input>
                        </div>
                        <div id="events-room-`+id+`" class="widget-body-element-events-room">
                            <div class="widget-body-element-event-select bottom-separator">
                                <label for="events-select-`+id+`">Event</label>
                                <select id="events-select-`+id+`" onchange="switchEventDisplayed(`+id+`)"></select>
                            </div>
                        </div>
                    </div>
                    <div class="widget-body-forty">
                        <div id="ports-room-`+id+`" class="ports-room"></div>
                        <div id="create-ports-`+id+`" class="create-ports">
                            <label>Output Port Creator</label>
                            <input id="new-port-name-`+id+`" type="text" class="bottom-separator" placeholder="name"></input>
                            <button onclick="addPortBox(`+id+`, document.getElementById('new-port-name-`+id+`').value)" class="btn-add-port-box">add output port</button>
                        </div>
                    </div>
                </div>
            </div>
            `;
            
            if(widget_data[type].widget_ports == "IN/OUT"){
                editor.addNode(name, 1, 0, pos_x, pos_y, widget_data[type].widget_ports , data, html);
            }else if(widget_data[type].widget_ports == "OUT"){
                editor.addNode(name, 0, 0, pos_x, pos_y, widget_data[type].widget_ports , data, html);
            }

        }else{
            html += `
            <div class="box-full">
                <div class="widget-body">
                    <div class="widget-body-one-hundred">
                        <div class="widget-body-element-name bottom-separator">
                            <label for="widgetname-`+id+`">WidgetName</label>
                            <input id="widgetname-`+id+`" type="text" value=`+name+` readonly></input>
                        </div>
                        <div id="events-room-`+id+`" class="widget-body-element-events-room">
                            <div class="widget-body-element-event-select bottom-separator">
                                <label for="events-select-`+id+`">Event</label>
                                <select id="events-select-`+id+`" onchange="switchEventDisplayed(`+id+`)"></select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            `;
            editor.addNode(name, 1, 0, pos_x, pos_y, widget_data[type].widget_ports , data, html);
        }

        //add all events of a widget
        addEventsToNodes(id);

        Object.keys(splitted_ck_editor).forEach(ck_id =>{
            let editorKeys = Object.keys(editor.drawflow.drawflow[module].data[id].data.events);
            let index = editorKeys.indexOf("external_commands");
            if (index !== -1) {
                editorKeys.splice(index, 1);
                editorKeys.push("external_commands");
            }
            editorKeys.forEach(ev_id =>{
            //Object.keys(editor.drawflow.drawflow[module].data[id].data.events).forEach(ev_id =>{
                if(splitted_ck_editor[ck_id].ev_name == editor.drawflow.drawflow[module].data[id].data.events[ev_id].ev_name){
                    editor.drawflow.drawflow[module].data[id].data.events[ev_id].code = splitted_ck_editor[ck_id].code;
                    document.getElementById("code-"+id+"-"+ev_id).innerHTML = splitted_ck_editor[ck_id].code;
                }
            });
        });

        //console.log("addNodeToEditor("+id+","+type+","+name+","+"splitted_ck_editor"+","+pos_x+","+pos_y+")");
        //console.log(editor);
    }

    //add events to the nodes 
    function addEventsToNodes(id){
        let node_event_room = document.getElementById("events-room-"+id);
        let node_select_event = document.getElementById("events-select-"+id);

        let editorKeys = Object.keys(editor.getNodeFromId(id).data.events);
        let index = editorKeys.indexOf("external_commands");
        if (index !== -1) {
            editorKeys.splice(index, 1);
            editorKeys.push("external_commands");
        }
        editorKeys.forEach(ev_name => {
        //Object.keys(editor.getNodeFromId(id).data.events).forEach(ev_name => {
            node_select_event.insertAdjacentHTML("beforeend",`<option value=`+ev_name+`>`+ev_name+`</option>`);
            node_event_room.insertAdjacentHTML("beforeend",`
                    <div id="code-room-`+id+`-`+ev_name+`" class="widget-body-element-code-room">
                        <label for="code-`+id+`-`+ev_name+`">Code</label>
                        <textarea id="code-`+id+`-`+ev_name+`" df-events-`+ev_name+`-code class="widget-body-element-code-box"></textarea>
                    </div>
                `);
        });

        switchEventDisplayed(id);

        updateHTMLFromNodeId(id);

        //console.log("addEventsToNodes("+id+")");
        //console.log(editor);
    }

    //switch widget event to display in a widget node
    function switchEventDisplayed(id){
        let switch_ev_name = document.getElementById("events-select-"+id).value;
        let node_event_editor;
        Object.keys(editor.getNodeFromId(id).data.events).forEach(ev_name =>{
            node_event_editor = document.getElementById("code-room-"+id+"-"+ev_name);
            if(ev_name!=switch_ev_name){
                if(!node_event_editor.classList.contains("hide"))
                    node_event_editor.classList.add("hide");
            } else {
                if(node_event_editor.classList.contains("hide"))
                    node_event_editor.classList.remove("hide");
            }
        });

        //console.log("switchEventDisplayed("+id+")");
        //console.log(editor);
    }

    //split code of a ck editor for finding events,triggers ecc. 
    //TODO incompleted, we have to assume that "e.event" and "else" are only in certain parts of the code (otherwise it doesn't work)
    function splitCKeditorCode(ck_editor, widget_name, compatibilityFlag){
        let arr = [];
        let splitted = {"ev_name":"","code":""};
        let event_counter = 0;
        let new_splitted = "";

        //removing function execute(){}
        ck_editor = ck_editor.trim();
        if (ck_editor.includes("\\t")) {
            ck_editor = replaceTabsWithSpaces(ck_editor);
        }
        if (!compatibilityFlag) {
            alertSaveCsblEditor = true;
            alert("WARNING: CSBL code in widget '" + widget_name + "' is not in the proper format to be managed and " +
                "edited by the CSBL Editor. It may have been created directly in the CK Editor (in the widget " +
                "\"More Options\" panel), therefore it MAY NOT WORK PROPERLY if edited and saved in this CSBL Editor.");
        } else {
            ck_editor = ck_editor.slice(ck_editor.indexOf("\/\/events_code_start") + 19, ck_editor.lastIndexOf("\/\/events_code_end"));
        }
        ck_editor = ck_editor.trim();

        //take events codes and triggers
        while(ck_editor.indexOf("e.event")!=-1){
            new_splitted = JSON.parse(JSON.stringify(splitted));
            ck_editor = ck_editor.slice(ck_editor.indexOf("e.event"));
            ck_editor = ck_editor.slice(ck_editor.indexOf("\"")+1);

            new_splitted["ev_name"] = ck_editor.slice(0,ck_editor.indexOf("\""));

            takeEventCode();
            if (event_counter >= 1 && compareObj(new_splitted, arr[event_counter-1])) {
                break;
            }
            arr.push(new_splitted);
            event_counter++;
        }

        //take externalCommands codes and triggers
        new_splitted = JSON.parse(JSON.stringify(splitted));
        new_splitted["ev_name"] = "externalCommands";

        if(event_counter != 0){
            ck_editor = ck_editor.slice(ck_editor.indexOf("else{")+5);
        }
        takeEventCode();
        arr.push(new_splitted);
        event_counter++;
        

        //Internal function for take code and triggers code
        function takeEventCode(){
            let ev_code = ck_editor.slice(ck_editor.indexOf("\/\/events_code_part_start")+24,ck_editor.indexOf("\/\/events_code_part_end"));
            ev_code = ev_code.trim();
            let code_strings = [];
            code_strings = ev_code.split("\n");
            for (let i=0 ; i < code_strings.length; i++){
                new_splitted["code"] += code_strings[i].trim()+"\n";
            };
        }

        //console.log("splitCKeditorCode(ck_editor)");
        //console.log(arr);

        return arr;
    }

    //add existing ports to a node event finded into a splitted code
    function addExistingPortsToNode(id,ck_editor){
        let tableConnections = ck_editor.slice(ck_editor.indexOf("var connections"));
        tableConnections = tableConnections.slice(tableConnections.indexOf("=")+1,tableConnections.indexOf(";"));
        if(tableConnections != ""){
            let jsonPortsConnections = JSON.parse(tableConnections);
            Object.keys(jsonPortsConnections).forEach(port_id =>{
                for(let i=0;i<port_types.length;i++)
                    if(port_types[i].output_type == jsonPortsConnections[port_id].output_type){
                        addPortBox(id,jsonPortsConnections[port_id].port_name,port_types[i]);
                        let output_class = parseInt(port_id)+1;
                        Object.keys(jsonPortsConnections[port_id].linked_target_widgets).forEach(target_id =>{
                            for(let input_id=1;input_id<=Object.keys(editor.drawflow.drawflow[module].data).length;input_id++)
                                if(editor.drawflow.drawflow[module].data[input_id].name == jsonPortsConnections[port_id].linked_target_widgets[target_id].widget_name){
                                    // editor.addConnection(id,input_id,"output_"+output_class,"input_1",port_types[i].color_class+"_conn");
                                    editor.addConnection(id,input_id,"output_"+output_class,"input_1");
                                    // document.getElementById("node_" + parseInt(id) + "_output_" + parseInt(input_id) + "_conn_" + "output_"+output_class).classList.add(port_types[i].color_class+"_conn");
                                    // Seleziona l'elemento SVG specifico tramite il suo ID
                                    /*
                                    var svgElement = document.getElementById("node_" + parseInt(id) + "_output_" + parseInt(input_id) + "_conn_output_"+output_class);

                                    // Crea il popup
                                    var popup = document.createElement('div');
                                    popup.textContent = 'Questo è un popup!';
                                    popup.style.display = 'none';
                                    popup.style.position = 'relative';

                                    // Aggiungi il popup al body
                                    document.body.appendChild(popup);

                                    // Aggiungi l'evento mouseover
                                    svgElement.addEventListener('mouseover', function(event) {
                                        // Mostra il popup e posizionalo vicino al mouse
                                        popup.style.display = 'block';
                                        //popup.style.left = event.pageX + 'px';
                                        //popup.style.top = event.pageY + 'px';
                                        popup.style.left = event.pageX;
                                        popup.style.top = event.pageY;
                                    });

                                    // Aggiungi l'evento mouseout
                                    svgElement.addEventListener('mouseout', function() {
                                        // Nasconde il popup
                                        popup.style.display = 'none';
                                    });
                                     */

                                    break;
                                }
                        });
                        break;
                    }
            });
        }
        //console.log("addExistingTriggersToNode("+id+",ck_editor)");
        //console.log(editor);
    }


    //EXPORT METHODS


    //convert node data into our model API
    function exportDashboard(dash, node){

        function checkCompatibilityFlowNodeInDash(dashb, node) {
            for (id = 0; id < dash.length; id++) {
                if (dashb[id].widget_name === node.name) {
                    return dashb[id].compatibilityFlag;
                }
            }
            return false;
        }

        function chekDashId(dashb, node) {
            let id;
            for (id = 0; id < dash.length; id++) {
                if (dashb[id].widget_name === node.name) {
                    break;
                }
            }
            return id;
        }

        let dashboard=[];
        let exportDrawflowNodes = editor.export().drawflow.Home.data;
        let currentCkEditorCode;
        for(let id=1;id<=Object.keys(exportDrawflowNodes).length;id++){
            //if (checkCompatibilityFlowNodeInDash(dash, exportDrawflowNodes[id])) {
                currentCkEditorCode = dash[chekDashId(dash, exportDrawflowNodes[id])].ck_editor;
                dashboard.push({
                    "widget_name": exportDrawflowNodes[id].name,
                    "widget_type": exportDrawflowNodes[id].data.widget_type,
                    "ck_editor": rebuildCKeditorCode(id, checkCompatibilityFlowNodeInDash(dash, exportDrawflowNodes[id]), exportDrawflowNodes[id].data.widget_type, currentCkEditorCode),
                //    "compatibilityFlag" : true
                    "compatibilityFlag" : checkCompatibilityFlowNodeInDash(dash, exportDrawflowNodes[id])
                });
           /* } else {
                dashboard.push({
                    "widget_name": exportDrawflowNodes[id].name,
                    "widget_type": exportDrawflowNodes[id].data.widget_type,
                    "ck_editor": dash[chekDashId(dash, exportDrawflowNodes[id])].ck_editor,
                    "compatibilityFlag" : false
                });
            } */

            //if (currentCkEditorCode !== dashboard[dashboard.length - 1].ck_editor) {
            //    dashboard[dashboard.length - 1].compatibilityFlag = true;
            //}
        }

        //console.log("exportDashboard()");
        //console.log(editor);
        //console.log(dashboard);

        return dashboard;
    }

    //rebuild code of a ck editor into a defined structure //TODO
    function rebuildCKeditorCode(id, compatibilityFlag, widgetType, currentCode){       // GESTIRE EXTERNAL_COMMANDS QUANDO COMPATIBILITY è FALSE MA CI SONO OUTPUTS
        let node = editor.getNodeFromId(id);
        let rebuildedEnc;
        if (compatibilityFlag || Object.keys(node.data.port_boxes).length !== 0) {
            let first_event = true, tab_counter = 1, event_counter = 0;
            let rebuilded = "function execute(){\n\n";
            rebuilded += "\t".repeat(tab_counter) + "var connections = " + JSON.stringify(getConnectionsTableByNodeId(id)) + ";\n\n";
            rebuilded += "\t".repeat(tab_counter) + "var e = readInput(param, connections);\n";
            /*rebuilded += "\t".repeat(tab_counter) + "if (IsJsonString(param)) {\n";
            rebuilded += "\t".repeat(tab_counter) + "\t".repeat(tab_counter) + "e = JSON.parse(param);\n";
            rebuilded += "\t".repeat(tab_counter) + " } else {\n";
            rebuilded += "\t".repeat(tab_counter) + "\t".repeat(tab_counter) + "e = param;\n";
            rebuilded += "\t".repeat(tab_counter) + " }\n";*/
            Object.keys(functions).forEach(id => {
                formatCode(functions[id].func_code, false);
            });
            rebuilded += "\n";
            rebuilded += "\t".repeat(tab_counter) + "\/\/events_code_start\n";
            Object.keys(node.data.events).forEach(ev_id => {
                if (ev_id != "external_commands") {
                    if (first_event) {
                        rebuilded += "\t".repeat(tab_counter) + "if(e.event == \"" + node.data.events[ev_id].ev_name + "\"){\n";
                        first_event = false;
                    } else {
                        rebuilded += "\t".repeat(tab_counter) + "}else if(e.event == \"" + node.data.events[ev_id].ev_name + "\"){\n";
                    }

                    tab_counter++;
                    formatCode(node.data.events[ev_id].code, true);
                    tab_counter--;

                    event_counter++;
                }
            });

            //manage external commands code
            if (event_counter > 0) {
                if (node.data.events["external_commands"] !== undefined) {
                    rebuilded += "\t".repeat(tab_counter) + "}else{\n";
                    tab_counter++;
                    formatCode(node.data.events["external_commands"].code, true);
                    tab_counter--;
                }
                rebuilded += "\t".repeat(tab_counter) + "}\n";
            } else {
                if (node.data.events["external_commands"] !== undefined) {
                    formatCode(node.data.events["external_commands"].code, true);
                }
            }
            rebuilded += "\t".repeat(tab_counter) + "\/\/events_code_end\n";
            rebuilded += "}";

            function formatCode(code, is_events_code_part) {
                let code_strings = [];
                code = code.trim();
                code_strings = code.split("\n");
                if (is_events_code_part)
                    rebuilded += "\t".repeat(tab_counter) + "\/\/events_code_part_start\n";
                for (let i = 0; i < code_strings.length; i++) {
                    rebuilded += "\t".repeat(tab_counter) + code_strings[i] + "\n";
                }
                ;
                if (is_events_code_part)
                    rebuilded += "\t".repeat(tab_counter) + "\/\/events_code_part_end\n";
            }

            rebuildedEnc = htmlEncode(rebuilded);
        } else {
            var text_ck_area = document.createElement("text_ck_area");
            text_ck_area.style.display = "none";

            document.body.appendChild(text_ck_area);

            //text_ck_area.innerHTML = removeTags(node.data.events["external_commands"].code);
            if (widgetType === "widgetExternalContent" && node.data.events["external_commands"].code !== currentCode) {
                text_ck_area.innerHTML = encodeMixedHTML(node.data.events["external_commands"].code);
            } else {
                if (node.data.events["external_commands"] != null) {
                    text_ck_area.innerHTML = node.data.events["external_commands"].code;
                } else {
                    text_ck_area.innerHTML = "";
                }
            }
            rebuildedEnc = text_ck_area.innerHTML;
            document.body.removeChild(text_ck_area);
        }
        //console.log("rebuildCKeditorCode("+id+")");
        //console.log(rebuilded);
        //console.log(rebuildedEnc);
        return rebuildedEnc;
    }


    //PORTBOX METHODS

    //add a port box to a node event
    function addPortBox(id,port_name,port_type=port_types[0]){
        let check_name_validity = true;

        //Check if port name is available and valid
        if(port_name == ""){
            check_name_validity = false;
        }
        Object.keys(editor.drawflow.drawflow[module].data[id].data.port_boxes).forEach(port_box_id =>{
            if(editor.drawflow.drawflow[module].data[id].data.port_boxes[port_box_id].port_name == port_name){
                check_name_validity = false;
            }
        });

        if(check_name_validity){
            //Add a new port box to node.data.port_boxes
            let port_box_id = editor.drawflow.drawflow[module].data[id].data.next_port_box;
            let new_port_box = JSON.parse(JSON.stringify(port_box));
            new_port_box.port_type = JSON.parse(JSON.stringify(port_type));
            editor.drawflow.drawflow[module].data[id].data.port_boxes[port_box_id] = new_port_box;
            editor.drawflow.drawflow[module].data[id].data.next_port_box += 1;
            addPortOutput(id,port_box_id);
            editor.drawflow.drawflow[module].data[id].data.port_boxes[port_box_id].port_name = port_name;

            //Modify node HTML with a new port box form
            let portBoxRoom = document.getElementById("ports-room-"+id);
            portBoxRoom.insertAdjacentHTML("beforeend",`
            <div class="port-box accordion-item" id="port-box-`+id+"-"+port_box_id+`">
                <div class="accordion-header">
                    <button id="port-button-`+id+"-"+port_box_id+`" class="accordion-button port-box-button" type="button" data-toggle="collapse" data-target="#collapse`+id+"-"+port_box_id+`">
                        <input id="port-name-`+id+"-"+port_box_id+`" type="text" value="`+port_name+`" df-port_boxes-`+port_box_id+`-port_name class="port-box-name" readonly></input>
                    </button>
                </div>
                <div id="collapse`+id+"-"+port_box_id+`" class="accordion-collapse collapse" data-parent="#ports-room-`+id+`">
                    <div class="accordion-body">
                        <div class="separator">
                            <label for="output-type-`+id+"-"+port_box_id+`">OutputType</label>
                            <select onchange="changePortBoxType(`+id+`,`+port_box_id+`)" id="output-type-`+id+"-"+port_box_id+`" type="text"></select>
                        </div>
                        <button onclick="deletePortBox(`+id+`,`+port_box_id+`)" class="btn-delete-port-box">delete</button>
                    </div>
                </div>
            </div>
            `);

            document.getElementById("port-button-"+id+"-"+port_box_id).classList.add(port_type.color_class);
            document.getElementById("node_"+ id + "_output_" + editor.drawflow.drawflow[module].data[id].data.port_boxes[port_box_id].associated_output_node).classList.add(port_type.color_class);

            //take port box output type list based on widget type
            let output_type_select = document.getElementById("output-type-"+id+"-"+port_box_id);
            for(let i=0; i< port_types.length;i++){
                if(port_types[i].perform_widgets_typologies.includes(editor.getNodeFromId(id).data.widget_type))
                    if(port_types[i].output_type == port_type.output_type){
                        output_type_select.insertAdjacentHTML("beforeend",`<option value="`+port_types[i].output_type+`" selected>`+port_types[i].output_type+`</option>`);
                    } else {
                        output_type_select.insertAdjacentHTML("beforeend",`<option value="`+port_types[i].output_type+`">`+port_types[i].output_type+`</option>`);
                    }
            }

            updateHTMLFromNodeId(id);

        }else{
            alert("Il seguente nome per il widget scelto è gia stato utilizzato oppure non è valido. Riprovare con un altro nome.")
        }

        //console.log("addPortBox("+id+","+port_name+",port_box)");
        //console.log(editor);
    }

    //delete a port box from a node event
    function deletePortBox(id,port_box_id){
        let portBoxRoom = document.getElementById("port-box-"+id+"-"+port_box_id);

        //Remove node HTML of a port box
        removePortOutput(id,port_box_id);

        portBoxRoom.remove();

        //remove code that use this port box
        Object.keys(editor.getNodeFromId(id).data.events).forEach(ev_id => {
            let old_code = editor.getNodeFromId(id).data.events[ev_id].code;
            let new_code = "";
            let del_port_name = "";
            let parenthesis = /\(".+".*\);/g;
            Object.keys(functions).forEach(f_id =>{
                let reg = new RegExp(functions[f_id].func_name + parenthesis.source, "g");
                while(old_code.search(reg) != -1){
                    new_code += old_code.slice(0,old_code.search(reg));
                    old_code = old_code.slice(old_code.search(reg));
                    del_port_name = old_code.slice(old_code.indexOf("(")+2,old_code.indexOf(",")-1);
                    if(del_port_name != editor.drawflow.drawflow[module].data[id].data.port_boxes[port_box_id].port_name){
                        new_code += old_code.slice(0,old_code.indexOf(";")+1);
                    }
                    old_code = old_code.slice(old_code.indexOf(";")+1);
                }
                new_code += old_code;
                old_code = new_code;
                new_code = "";
            });
            new_code = old_code;
            editor.drawflow.drawflow[module].data[id].data.events[ev_id].code = new_code;
            document.getElementById("code-"+id+"-"+ev_id).value = editor.drawflow.drawflow[module].data[id].data.events[ev_id].code 
        });

        //Delete a port box from node.data.port_boxes
        delete editor.drawflow.drawflow[module].data[id].data.port_boxes[port_box_id];

        updateHTMLFromNodeId(id);

        //console.log("deletePortBox("+id+","+port_box_id+")");
        //console.log(editor);
    }

    //change a port box composition based on the choice of a specified port box
    function changePortBoxType(id, port_box_id){
        //generating new port box composition
        Object.keys(port_types).forEach(pbt_id =>{
            if(document.getElementById("port-button-"+id+"-"+port_box_id).classList.contains(port_types[pbt_id].color_class)){
                document.getElementById("port-button-"+id+"-"+port_box_id).classList.remove(port_types[pbt_id].color_class);
                document.getElementById("node_"+ id + "_output_" + editor.drawflow.drawflow[module].data[id].data.port_boxes[port_box_id].associated_output_node).classList.remove(port_types[pbt_id].color_class);
            }
            if(port_types[pbt_id].output_type==document.getElementById("output-type-"+id+"-"+port_box_id).value){
                editor.drawflow.drawflow[module].data[id].data.port_boxes[port_box_id].port_type = JSON.parse(JSON.stringify(port_types[pbt_id]));
                document.getElementById("port-button-"+id+"-"+port_box_id).classList.add(port_types[pbt_id].color_class);
                document.getElementById("node_"+ id + "_output_" + editor.drawflow.drawflow[module].data[id].data.port_boxes[port_box_id].associated_output_node).classList.add(port_types[pbt_id].color_class);
            }
        });

        Object.keys(editor.getNodeFromId(id).outputs).forEach(output_class =>{
            if(output_class == "output_"+editor.drawflow.drawflow[module].data[id].data.port_boxes[port_box_id].associated_output_node){
                let input_id,input_class;
                for(let conn_id = editor.drawflow.drawflow[module].data[id].outputs[output_class].connections.length-1; conn_id >= 0; conn_id--){
                    input_id = parseInt(editor.drawflow.drawflow[module].data[id].outputs[output_class].connections[conn_id].node);
                    input_class = editor.drawflow.drawflow[module].data[id].outputs[output_class].connections[conn_id].output;
                    editor.removeSingleConnection(id,input_id,output_class,input_class);
                }
            }
        });

        updateHTMLFromNodeId(id);

        //console.log("changePortBoxComposition("+id+","+port_box_id+")");
        //console.log(editor);
    }


    //OUTPUT PORTS METHODS


    //add an output port based on output_type of a certain port box
    function addPortOutput(id,port_box_id){
        let output_number = Object.keys(editor.getNodeFromId(id).outputs).length+1;
        
        editor.drawflow.drawflow[module].data[id].data.port_boxes[port_box_id].associated_output_node = output_number;
        
        editor.addNodeOutput(id);

        //console.log("addOutputPort("+id+","+port_box_id+")");
        //console.log(editor);
    }

    //remove an output port linked with a certain port box
    function removePortOutput(id,port_box_id){

        Object.keys(editor.getNodeFromId(id).data.port_boxes).forEach(iter_port_box_id => {
            if(editor.drawflow.drawflow[module].data[id].data.port_boxes[port_box_id].associated_output_node < editor.drawflow.drawflow[module].data[id].data.port_boxes[iter_port_box_id].associated_output_node){
                editor.drawflow.drawflow[module].data[id].data.port_boxes[iter_port_box_id].associated_output_node -= 1;
            }
        });
        
        editor.removeNodeOutput(id, "output_"+editor.getNodeFromId(id).data.port_boxes[port_box_id].associated_output_node);

        var steps = Object.keys(editor.getNodeFromId(id).data.port_boxes).length - port_box_id - 1;
        for (let n=0; n<steps; n++) {   // shift output div elements according to corresponding port boxes
            var divToCorrect = document.getElementById("node_1_output_" + parseInt(Object.keys(editor.getNodeFromId(id).data.port_boxes).length-n));
            var currentId = divToCorrect.id;
            var numberPart = currentId.split('_').pop();
            var number = parseInt(numberPart);
            var newNumber = number - 1;
            var newId = "node_1_output_" + newNumber;
            divToCorrect.id = newId;
        }

        //console.log("removeOutputPort("+id+","+port_box_id+")");
        //console.log(editor);
    }


    //CONNECTIONS METHODS

    //take a modelled connections table of a widget
    function getConnectionsTableByNodeId(id){
        let singleConn = {"port_name":"","output_type":"","linked_target_widgets":[]};
        let target_widget = {"widget_name":"","widget_type":""};
        let connections = [];
        Object.keys(editor.getNodeFromId(id).data.port_boxes).forEach(port_box_id =>{
            let new_conn = JSON.parse(JSON.stringify(singleConn)),new_target;
            let output_class = "output_"+editor.getNodeFromId(id).data.port_boxes[port_box_id].associated_output_node;
            let input_id;

            new_conn.port_name = editor.getNodeFromId(id).data.port_boxes[port_box_id].port_name;
            new_conn.output_type = editor.getNodeFromId(id).data.port_boxes[port_box_id].port_type.output_type;

            Object.keys(editor.getNodeFromId(id).outputs[output_class].connections).forEach(conn_id =>{
                input_id=editor.getNodeFromId(id).outputs[output_class].connections[conn_id].node;

                new_target = JSON.parse(JSON.stringify(target_widget));
                new_target.widget_name = editor.getNodeFromId(input_id).name;
                new_target.widget_type = editor.getNodeFromId(input_id).data.widget_type;

                new_conn.linked_target_widgets.push(new_target);
            });

            connections.push(new_conn);
        });
        //console.log("getConnectionsTableByNodeId("+id+")");
        //console.log(connections);
        return connections;
    }
</script>