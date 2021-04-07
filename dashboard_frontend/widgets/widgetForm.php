
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

<script type='text/javascript'>

	$(document).ready(function <?= $_REQUEST['name_w'] ?>(firstLoad, metricNameFromDriver, widgetTitleFromDriver, widgetHeaderColorFromDriver, widgetHeaderFontColorFromDriver, fromGisExternalContent, fromGisExternalContentServiceUri, fromGisExternalContentField, fromGisExternalContentRange) {
		
        <?php
        $titlePatterns = array();
        $titlePatterns[0] = '/_/';
        $titlePatterns[1] = '/\'/';
        $replacements = array();
        $replacements[0] = ' ';
        $replacements[1] = '&apos;';
        $title = $_REQUEST['title_w'];
        $link = mysqli_connect($host, $username, $password);
        if (checkWidgetNameInDashboard($link, $_REQUEST['name_w'], $_REQUEST['id_dashboard']) === false) {
            eventLog("Returned the following ERROR in widgetSelectorTech.php for the widget ".escapeForHTML($_REQUEST['name_w'])." is not instantiated or allowed in this dashboard.");
            exit();
        }
        ?>
        var scroller, widgetProperties, styleParameters, serviceUri, queryType,
            eventName, newRow, symbolMode, symbolFile, widgetTargetList, originalHeaderColor, fontFamily,
            originalBorderColor,
            eventName, serviceUri, queriesNumber, widgetWidth, shownHeight, rowPercHeight, contentHeightPx,
            eventContentWPerc,
            mapPtrContainer, pinContainer, queryDescContainer, activeFontColor, rowHeight, iconSize,
            queryDescContainerWidth,
            queryDescContainerWidthPerc, pinContainerWidthPerc, defaultOption, pendingSelection, selected, accepted, formArray = null;

        var fontSize = "<?= escapeForJS($_REQUEST['fontSize']) ?>";
        var speed = 65;
        var hostFile = "<?= escapeForJS($_REQUEST['hostFile']) ?>";
        var widgetName = "<?= escapeForJS($_REQUEST['name_w']) ?>";
        var divContainer = $("#<?= escapeForJS($_REQUEST['name_w']) ?>_mainContainer");
        var widgetContentColor = "<?= escapeForJS($_REQUEST['color_w']) ?>";
        var widgetHeaderColor = "<?= escapeForJS($_REQUEST['frame_color_w']) ?>";
        var widgetHeaderFontColor = "<?= escapeForJS($_REQUEST['headerFontColor']) ?>";
        var linkElement = $('#<?= $_REQUEST['name_w'] ?>_link_w');
        var fontColor = "<?= escapeForJS($_REQUEST['fontColor']) ?>";
        var elToEmpty = $("#<?= $_REQUEST['name_w'] ?>_formContainer");
        var url = "<?= escapeForJS($_REQUEST['link_w']) ?>";
        var embedWidget = <?= $_REQUEST['embedWidget']=='true' ? 'true' : 'false' ?>;
        var embedWidgetPolicy = '<?= escapeForJS($_REQUEST['embedWidgetPolicy']) ?>';
        var headerHeight = 25;
        var showTitle = "<?= escapeForJS($_REQUEST['showTitle']) ?>";
        var showHeader = null;
        var defaultOptionUsed = false;
        var pinContainerWidth = 40;
        var hasTimer = "<?= escapeForJS($_REQUEST['hasTimer']) ?>";
        console.log("Form Widget loaded: " + widgetName);
        globalMapView = false;
		if(Window.webSockets == undefined) 
			Window.webSockets = {};
		

        if (url === "null") {
            url = null;
        }

        if (((embedWidget === true) && (embedWidgetPolicy === 'auto')) || ((embedWidget === true) && (embedWidgetPolicy === 'manual') && (showTitle === "no")) || ((embedWidget === false) && (showTitle === "no"))) {
            showHeader = false;
        }
        else {
            showHeader = true;
        }

        //Definizioni di funzione

        function populateWidget(choices) {
            //$('#<?= $_REQUEST['name_w'] ?>_formContainer').empty();
			// It will be actually populated when options will be received via socket.
        }

        function resizeWidget() {
            setWidgetLayout(hostFile, widgetName, widgetContentColor, widgetHeaderColor, widgetHeaderFontColor, showHeader, headerHeight, hasTimer);
        }

        //Fine definizioni di funzione

        setWidgetLayout(hostFile, widgetName, widgetContentColor, widgetHeaderColor, widgetHeaderFontColor, showHeader, headerHeight, hasTimer);
        $('#<?= $_REQUEST['name_w'] ?>_div').parents('li.gs_w').off('resizeWidgets');
        $('#<?= $_REQUEST['name_w'] ?>_div').parents('li.gs_w').on('resizeWidgets', resizeWidget);

        if (firstLoad === false) {
            showWidgetContent(widgetName);
        }
        else {
            setupLoadingPanel(widgetName, widgetContentColor, firstLoad);
        }

		$("#<?= $_REQUEST['name_w'] ?>_formContainer form").submit(function(){ 			
			try {
				$("#<?= $_REQUEST['name_w'] ?>_formContainer form").css("opacity",0.5);
				var valid = true;
				var v = []; $.each($("#<?= $_REQUEST['name_w'] ?>_formContainer form input:not([type=\"submit\"])"), function(i,e) { 
					if("checkbox" == $(e).attr("type")) {
						v.push($(e).is(":checked")?"checked":"");
						if(formArray["options"][i]["required"] && !$(e).is(":checked")) {
							valid = false;
							$(e).parent().css("border","2px solid red");
						}
					}
					else if("switch" == $(e).attr("type")) {
						v.push($(e).val()?"on":""); 
						if(formArray["options"][i]["required"] && !$(e).val()) {
							valid = false;
							$(e).siblings("div.switch").css("border","2px solid red");
						}
					}
					else {
						v.push($(e).val()); 
						if(formArray["options"][i]["required"] && !$(e).val()) {
							valid = false;
							$(e).css("border-color","red");
						}
					}
					
				});			
				if(valid) {
					stdSend({ options: formArray["options"], selected: v } ); 
				}
				else {
					$("#<?= $_REQUEST['name_w'] ?>_formContainer form").css("opacity",1);
					$("#<?= $_REQUEST['name_w'] ?>_formContainer form").css("background-color","red");
					setTimeout(function(){$("#<?= $_REQUEST['name_w'] ?>_formContainer form").css("background-color","transparent");},1000);
				}
			}
			catch(e) {
				console.log(e);
			}			
			return false;
		});

        $.ajax({
            url: getParametersWidgetUrl,
            type: "GET",
            data: {"nomeWidget": [widgetName]},
            async: true,
            dataType: 'json',
            success: function (data) {
                widgetProperties = data;
                if ((widgetProperties !== null) && (widgetProperties !== undefined)) {
					//Inizio eventuale codice ad hoc basato sulle proprietà del widget
					try {
						if(widgetProperties.param && widgetProperties.param.currentValue) {
							formArray = JSON.parse(widgetProperties.param.currentValue);
							$("#<?= $_REQUEST['name_w'] ?>_formContainer form").empty();
							$("#<?= $_REQUEST['name_w'] ?>_formContainer form").append("<table>");
							$.each(formArray["options"], function(i, choice) { 
								addToWidgetForm(choice,formArray["selected"][i]); 
							});
							if(formArray["selected"]) $.each(formArray["selected"], function(i, v) { 																
								if("checkbox" == $("#<?= $_REQUEST['name_w'] ?>_formContainer form input").eq(i).attr("type")) $("#<?= $_REQUEST['name_w'] ?>_formContainer form input").eq(i).prop("checked",v);
								else if("switch" == $("#<?= $_REQUEST['name_w'] ?>_formContainer form input").eq(i).attr("type")) {
									$("#<?= $_REQUEST['name_w'] ?>_formContainer form input").eq(i).val(v);
									if(v) switchOn($("#<?= $_REQUEST['name_w'] ?>_formContainer form input").eq(i).siblings("div.switch").eq(0));
									else switchOff($("#<?= $_REQUEST['name_w'] ?>_formContainer form input").eq(i).siblings("div.switch").eq(0));
								}
								else $("#<?= $_REQUEST['name_w'] ?>_formContainer form input").eq(i).val(v);
							});
							$("#<?= $_REQUEST['name_w'] ?>_formContainer form").append("<p><input type=\"submit\" value=\"Submit\"></p>");
						}
					}
					catch(e) {
						console.log("Form Widget Error. Could not parse initial status due to the following:"); console.log(e);
					}
                    //Fine eventuale codice ad hoc basato sulle proprietà del widget
                    fontFamily = widgetProperties.param.fontFamily;
					if(fontFamily != null && fontFamily != "default" && fontFamily != "Auto") $("#<?= $_REQUEST['name_w'] ?>_formContainer form").css("font-family",fontFamily);
					fontSize = widgetProperties.param.fontSize;
					if(fontSize != null && fontSize != "") $("#<?= $_REQUEST['name_w'] ?>_formContainer form").css("font-size",fontSize+"px");
					fontColor = widgetProperties.param.fontColor;
					if(fontColor != null && fontColor != "") $("#<?= $_REQUEST['name_w'] ?>_formContainer form").css("color",fontColor);
                    widgetWidth = $('#<?= $_REQUEST['name_w'] ?>_div').width();
                    shownHeight = $('#<?= $_REQUEST['name_w'] ?>_div').height() - 25;

                    rowPercHeight = 100 / queriesNumber;
                    contentHeightPx = queriesNumber * 100;
                    eventContentWPerc = null;

                    populateWidget();
					
					setWidgetContentVisibility("<?= $_REQUEST['name_w'] ?>","<?= escapeForJS($_REQUEST['showContent']) ?>");
					
                }
                else {
                    console.log("Form Widget Anomaly. Proprietà widget = null");
                    $("#<?= $_REQUEST['name_w'] ?>_mainContainer").hide();
                }
            },
            error: function (errorData) {
                console.log("Form Widget Error. Received data error:");
                console.log(JSON.stringify(errorData));
                showWidgetContent(widgetName);
                if (firstLoad !== false) {
                    $("#<?= $_REQUEST['name_w'] ?>_mainContainer").hide();
                }
            }
        });

        $("#<?= $_REQUEST['name_w'] ?>").on('customResizeEvent', function (event) {
            resizeWidget();
        });

        $(document).on('resizeHighchart_' + widgetName, function (event) {
            showHeader = event.showHeader;
        });
		
		
		// Web Socket
		
		var openWs = function(widget) // this one is the only function that is called from the above 
        {
            try
            {
                <?php
                    $genFileContent = parse_ini_file("../conf/environment.ini");
                    $wsServerContent = parse_ini_file("../conf/webSocketServer.ini");
                    $wsServerAddress = $wsServerContent["wsServerAddressWidgets"][$genFileContent['environment']['value']];
                    $wsServerPort = $wsServerContent["wsServerPort"][$genFileContent['environment']['value']];
                    $wsPath = $wsServerContent["wsServerPath"][$genFileContent['environment']['value']];
                    $wsProtocol = $wsServerContent["wsServerProtocol"][$genFileContent['environment']['value']];
                    $wsRetryActive = $wsServerContent["wsServerRetryActive"][$genFileContent['environment']['value']];
                    $wsRetryTime = $wsServerContent["wsServerRetryTime"][$genFileContent['environment']['value']];
                    echo 'wsRetryActive = "' . $wsRetryActive . '";'."\n";
                    echo 'wsRetryTime = ' . $wsRetryTime . ';'."\n";
                    echo 'wsUrl="' . $wsProtocol . '://' . $wsServerAddress . ':' . $wsServerPort . '/' . $wsPath . '";'."\n";
                ?>
				// console.log(wsUrl);
                initWebsocket(widget, wsUrl, null, wsRetryTime*1000, function(socket){
                    // console.log('socket initialized!');
                    openWsConn(widget);
                }, function(){
                    console.log('Form Widget error: could not connect to this socket:'); console.log(wsUrl);
                });                                          
            }
            catch(e)
            {
                wsClosed();
            }
        };
        
        var manageIncomingWsMsg = function(msg)
        {            
			try {
				console.log("I have received:"); console.log(msg);
				
				var data = JSON.parse(msg.data);
				
				if(data.msgType == "SendToEmitter" && data.result == "Ok" && data.widgetUniqueName == "<?= $_REQUEST['name_w'] ?>") {
					accepted = data.msgId;
					// console.log("My message # "+accepted+" has been accepted by the socket server, that is now attempting to deliver it to the IoT App.");
					return;
				}
				
				if(data.msgType == "DataToEmitterAck" && data.msgId == accepted && data.widgetUniqueName == "<?= $_REQUEST['name_w'] ?>") {
					// console.log("My message # "+accepted+" has been acknowledged by the IoT App.");
					accepted = null; 
					var webSocket = Window.webSockets[data.widgetUniqueName];
                    if (!webSocket.ackReceived) {
                        clearTimeout(webSocket.timeout);
                        webSocket.ackReceived = true;
                        // console.log(data.widgetUniqueName + " ACK ackReceived:" + webSocket.ackReceived)
                        webSocket.onAck({result: "Ok", widgetName: data.widgetUniqueName});
                    }
					$("#<?= $_REQUEST['name_w'] ?>_formContainer form").css("opacity",1);
					return;
				}
				
				var newValue = data.newValue;
				try { newValue = JSON.parse(newValue); } catch(e) {}
				if(!newValue) return;				
				formArray = newValue;

				$("#<?= $_REQUEST['name_w'] ?>_formContainer form").empty();				
				$("#<?= $_REQUEST['name_w'] ?>_formContainer form").append("<table>");
				$.each(formArray["options"], function(i, choice) { 
					addToWidgetForm(choice,formArray["selected"][i]);
				});
				if(formArray["selected"]) $.each(formArray["selected"], function(i, v) { 
					if("checkbox" == $("#<?= $_REQUEST['name_w'] ?>_formContainer form input").eq(i).attr("type")) $("#<?= $_REQUEST['name_w'] ?>_formContainer form input").eq(i).prop("checked",v);
					else if("switch" == $("#<?= $_REQUEST['name_w'] ?>_formContainer form input").eq(i).attr("type")) {
						$("#<?= $_REQUEST['name_w'] ?>_formContainer form input").eq(i).val(v);
						if(v) switchOn($("#<?= $_REQUEST['name_w'] ?>_formContainer form input").eq(i).siblings("div.switch").eq(0));
						else switchOff($("#<?= $_REQUEST['name_w'] ?>_formContainer form input").eq(i).siblings("div.switch").eq(0));
					}
					else $("#<?= $_REQUEST['name_w'] ?>_formContainer form input").eq(i).val(v);
				});
				$("#<?= $_REQUEST['name_w'] ?>_formContainer form").append("<p><input type=\"submit\" value=\"Submit\"></p>");
				
			}
			catch(e) {
				console.log(e);
			}
			
        };
        
        timeToReload=200;
        var openWsConn = function(widget) {
            var webSocket = Window.webSockets[widget];
            var wsRegistration = {
                msgType: "ClientWidgetRegistration",
                userType: "widgetInstance",
                widgetUniqueName: "<?= $_REQUEST['name_w'] ?>"
            };
            webSocket.send(JSON.stringify(wsRegistration));              
            webSocket.addEventListener('message', manageIncomingWsMsg);
        };
        
        var wsClosed = function(e)
        {
            var webSocket = Window.webSockets["<?= $_REQUEST['name_w'] ?>"];
            webSocket.removeEventListener('message', manageIncomingWsMsg);
            if(wsRetryActive === 'yes')
            {
                setTimeout(openWs, parseInt(wsRetryTime*1000));
            }	
        };
		
		var stdSend = function(newValue) {
			var data = {
				  "msgType": "SendToEmitter",
				  "widgetUniqueName": widgetName,
				  "value": JSON.stringify(newValue),
				  "inputName": widgetProperties.param.name,
				  "dashboardId": widgetProperties.param.id_dashboard,
				  "username" : $('#authForm #hiddenUsername').val(),
				  "nrInputId": widgetProperties.param.nrInputId
			};
			// console.log("Sending..."); console.log(data); 
			var webSocket = Window.webSockets[widgetName];
			webSocket.ackReceived=false;
			webSocket.onAck = function(data) {
				// console.log(widgetName+" SUCCESS ackReceived:"+webSocket.ackReceived)
				//clearInterval(setUpdatingMsgInterval);
				switch(data.result)
				{
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
			if(webSocket.readyState==webSocket.OPEN) {
				webSocket.send(JSON.stringify(data));
				console.log("I have sent:"); console.log(JSON.stringify(data));
				webSocket.timeout = setTimeout(function() {
				  if(!webSocket.ackReceived) {
					console.log(widgetName+" ERR1 ackReceived:"+webSocket.ackReceived)
					showUpdateResult("API KO");
					console.log("Update value KO");
				  }
				},60000)
			} else {
				console.log(widgetName+" ERR1 socket not OPEN");
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
                  setTimeout(function(){
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
	  
	  var addToWidgetForm = function(choice,v) {
		  if(v) choice.value = v;
		  if("checkbox" == choice.type) $("#<?= $_REQUEST['name_w'] ?>_formContainer form table").append("<tr><td style=\"padding:0.2em; "+(choice.required && !choice.value?"border:2px solid red;":"")+"\" colspan=\"2\"><input "+(choice.required?"onchange=\"if(!this.checked) this.parentElement.style.border = '2px solid red'; else this.parentElement.style.border = '0px';\"":"")+" type=\""+ choice.type + "\" "+(choice.value?"checked":"")+" value=\"selected\"/>&nbsp;"+choice.label+"</td></tr>");
		  else if("switch" == choice.type) $("#<?= $_REQUEST['name_w'] ?>_formContainer form table").append("<tr><td style=\"padding:0.2em;\">"+choice.label+"</td><td style=\"padding:0.2em;\"><div class=\"switch\" data-value=\""+choice.value+"\" data-required=\""+choice.required+"\" onclick=\"toggle(this)\" style=\"cursor: pointer; width: 101px; height: 21px; overflow: hidden; "+(choice.required && !choice.value?"border:2px solid red;":"border: 1px solid gray;")+"\"><div style=\""+(choice.value?"width: 50%; height: 100%; background: black; margin-left: 50%; margin-bottom:1px; transition: all 1s;":"width: 50%; height: 100%; background: gray; margin-left: 0px; margin-bottom:1px; transition: all 1s;")+"\"></div></div><input class=\"switch\" style=\"display:none;\" type=\"switch\" value=\""+choice.value+"\"></td></tr>");
		  else $("#<?= $_REQUEST['name_w'] ?>_formContainer form table").append("<tr><td style=\"padding:0.2em;\">"+choice.label+"</td><td style=\"padding:0.2em;\"><input "+(choice.required?"oninput=\"if(!this.value) this.style.borderColor = 'red'; else this.style.borderColor = null;\"":"")+" type=\""+ choice.type + "\" value=\""+choice.value+"\" "+(choice.required && !choice.value?"style=\"border-color:red;\"":"")+"/></td></tr>");		  
	  };
	  openWs(widgetName);
	
	});//Fine document ready
	
  var switchOn = function(e) {
	  $(e).children().css("margin-left","50%");
	  $(e).children().css("background","black");
	  $(e).data("value","on");
	  $(e).siblings("input.switch").val("on");
	  $(e).css("border","1px solid gray");
  }
  
  var switchOff = function(e) {
	  $(e).children().css("margin-left","0px");
	  $(e).children().css("background","gray");	
	  $(e).data("value","");
	  $(e).siblings("input.switch").val("");
	  if($(e).data("required")) $(e).css("border","2px solid red");
  }
  
  var toggle = function(item){
	  if($(item).data("value")) switchOff(item); else switchOn(item);		  
  };
</script>

<div class="widget" id="<?= $_REQUEST['name_w'] ?>_div">
    <div class='ui-widget-content'>
        <?php include '../widgets/widgetHeader.php'; ?>
        <?php include '../widgets/widgetCtxMenu.php'; ?>
        <div id="<?= $_REQUEST['name_w'] ?>_content" class="content">
            <?php include '../widgets/commonModules/widgetDimControls.php'; ?>
            <div style="padding-top:0.5em;" id="<?= $_REQUEST['name_w'] ?>_mainContainer" class="chartContainer">
				<div id="<?= $_REQUEST['name_w'] ?>_formContainer">
					<form></form>
				</div>
            </div>
        </div>
    </div>
</div>