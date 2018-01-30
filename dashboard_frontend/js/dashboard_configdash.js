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

//Globals
var series, widgetType, editors, editorsM, currentEditor, infoJson, currentParamsSingleValueWidget, parametersDiff, 
    addWidgetConditionsArrayLocal, editWidgetConditionsArrayLocal, addGisParametersLocal, editGisParametersLocal, addWidgetSelectorRowRef = null;
    
var gisDefaultColors = [
    {
        color1: "#ffdb4d",
        color2: "#fff5cc"
    },
    {
        color1: "#ff9900",
        color2: "#ffe0b3"
    },
    {
        color1: "#ff6666",
        color2: "#ffcccc"
    },
    {
        color1: "#00e6e6",
        color2: "#99ffff"
    },
    {
        color1: "#33ccff",
        color2: "#99e6ff"
    },
    {
        color1: "#33cc33",
        color2: "#adebad"
    },
    {
        color1: "#009900",
        color2: "#80ff80"
    }
];    

function setAddGisParameters(addGisParametersAtt)
{
    addGisParametersLocal = addGisParametersAtt;
}

function setEditGisParameters(editGisParametersAtt)
{
    editGisParametersLocal = editGisParametersAtt;
}

function addGisQuery()
{
   var newTableRow, newTableCell, newQueryObj, widgetId, widgetTitle = null;
   
   //Gestione caso nessuna soglia pregressa: costruiamo l'object literal per i parametri
   if(addGisParametersLocal === null)
   {
      //Creazione del JSON dei parametri
      addGisParametersLocal = {
            queries: [],
            targets: []
        };
   }
   
   newQueryObj = {
       desc: "",
       query: "",
       color1: gisDefaultColors[($("#addGisQueryTable tr").length - 1)%7].color1,
       color2: gisDefaultColors[($("#addGisQueryTable tr").length - 1)%7].color2,
       targets: []
   };
   
   addGisParametersLocal.queries.push(newQueryObj);

   //Aggiunta record alla tabella GUI delle query
   newTableRow = $('<tr></tr>');
   
   newTableCell = $('<td><input data-param="queryIconOption" checked type="checkbox" /></td>');
   newTableCell.find('input').bootstrapToggle({
      on: 'Auto',
      off: 'Man',
      size: 'small',
      onstyle: 'primary',
      offstyle: 'warning'
   });
   
   newTableRow.append(newTableCell);
   
   newTableCell = $('<td class="centerWithFlex"></td>');
   var imgMaxSize = $('<input type="hidden" name="MAX_FILE_SIZE" value="1000000" />');
   newTableCell.append(imgMaxSize);
   var newControl = $('<input type="file" class="form-control" name="addSelectorLogos[]">');
   newTableCell.append(newControl);
   newTableRow.append(newTableCell);
   newControl.filestyle({
       input: false,
       buttonText: "",
       buttonName: "btn-primary",
       size: "sm",
       disabled: false,
       badge: false
   });
   
   newTableRow.find('div.bootstrap-filestyle').hide();
   
    newControl.change(function() 
    {
        var file = this.files[0];
        var imagefile = file.type;
        var match= ["image/jpeg","image/png","image/jpg", "image/svg+xml"];
        if(!((imagefile === match[0]) || (imagefile === match[1]) || (imagefile === match[2])|| (imagefile === match[3])))
        {
            console.log("Return false");
            return false;
        }
        else
        {
            console.log("Return true");
            var reader = new FileReader();
            reader.onload = function(event){
                newControl.parents('tr').find('div.selectorMenuCustomIcon').html("");
                newControl.parents('tr').find('div.selectorMenuCustomIcon').css("background", "url(" + event.target.result + ")");
                newControl.parents('tr').find('div.selectorMenuCustomIcon').css("background-size", "contain");
                newControl.parents('tr').find('div.selectorMenuCustomIcon').css("background-repeat", "no-repeat");
                newControl.parents('tr').find('div.selectorMenuCustomIcon').css("background-position", "center center");
                console.log("immagine caricata");
            };
            reader.readAsDataURL(this.files[0]);
        }
    });
   
   newTableCell = $('<td><i class="material-icons selectorMenuDefaultIcon" style="font-size: 34px; display: block;">navigation</i><div class="selectorMenuCustomIcon">None</div></td>');
   newTableRow.append(newTableCell);
   
   newTableRow.find('input[data-param=queryIconOption]').change(function(){
       if($(this).prop('checked'))
       {
           newTableRow.find('div.bootstrap-filestyle').hide();
           newTableRow.find('div.selectorMenuCustomIcon').hide();
           newTableRow.find('i.selectorMenuDefaultIcon').show();
           newTableRow.find('div.selectorMenuCustomIcon').hide();
           newTableRow.find('i.selectorMenuDefaultIcon').show();
       }
       else
       {
           newTableRow.find('div.bootstrap-filestyle').show();
           newTableRow.find('i.selectorMenuDefaultIcon').hide();
           newTableRow.find('div.selectorMenuCustomIcon').show();
           newTableRow.find('i.selectorMenuDefaultIcon').hide();
           newTableRow.find('div.selectorMenuCustomIcon').show();
           newTableRow.find('div.selectorMenuCustomIcon').css("width", newTableRow.find('div.selectorMenuCustomIcon').parents('td').width() + "px");
           newTableRow.find('div.selectorMenuCustomIcon').css("height", newTableRow.find('div.selectorMenuCustomIcon').parents('td').height() + "px");
       }
   });
   
   newTableCell = $('<td><a href="#" class="toBeEdited" data-type="text" data-mode="popup" data-param="queryDesc"></a></td>');
   newTableCell.find('a').editable({
       emptytext: "Empty",
       display: function(value, response){
           if(value.length > 16)
           {
               $(this).html(value.substring(0, 13) + "...");
           }
           else
           {
              $(this).html(value); 
           }
       }
   });
   newTableRow.append(newTableCell);
   
   newTableCell = $('<td><a href="#" class="toBeEdited" data-type="text" data-mode="popup" data-param="queryUrl"></td>');
   newTableCell.find('a').editable({
       emptytext: "Empty",
       display: function(value, response){
           if(value.length > 10)
           {
               $(this).html(value.substring(0, 10) + "...");
           }
           else
           {
              $(this).html(value); 
           }
       }
   });
   newTableRow.append(newTableCell);
   
   newTableCell = $('<td><div class="input-group colorPicker" data-param="color1"><input type="text" class="form-control"><span class="input-group-addon"><i class="thePicker"></i></span></div></td>');
   newTableRow.append(newTableCell);
   newTableRow.find('div.colorPicker').colorpicker({color: gisDefaultColors[($("#addGisQueryTable tr").length - 1)%7].color1, format: "rgba"});
   newTableRow.find('div.colorPicker').on('hidePicker', addGisUpdateParams); 
   
   newTableCell = $('<td><div class="input-group colorPicker" data-param="color2"><input type="text" class="form-control"><span class="input-group-addon"><i class="thePicker"></i></span></div></td>');
   newTableRow.append(newTableCell);
   newTableRow.find('div.colorPicker').colorpicker({color: gisDefaultColors[($("#addGisQueryTable tr").length - 1)%7].color2, format: "rgba"});
   newTableRow.find('div.colorPicker').on('hidePicker', addGisUpdateParams);
   
   newTableCell = $('<td><select data-param="targets" class="form-control" multiple></select></td>');
   newTableRow.append(newTableCell);
   
    $("li.gs_w").each(function(){
        if(($(this).attr("id").includes("BarContent"))||($(this).attr("id").includes("ColumnContent"))||($(this).attr("id").includes("GaugeChart"))||($(this).attr("id").includes("PieChart"))||($(this).attr("id").includes("SingleContent"))||($(this).attr("id").includes("Speedometer"))||($(this).attr("id").includes("TimeTrend")))
        {
          widgetId = $(this).attr("id");
          widgetTitle = $(this).find("div.titleDiv").html();
          newTableRow.find('select').append('<option value="' + widgetId + '">' + widgetTitle + '</option>');
        }
    });                               

   
   newTableRow.find('select').selectpicker({
                                       actionsBox: true, 
                                       width: 110
                                    });
   newTableRow.find('select').on('changed.bs.select', addGisUpdateParams);

   newTableCell = $('<td><a><i class="fa fa-close" style="font-size:24px;color:red"></i></a></td>');
   newTableCell.find('i').click(delGisQuery);
   newTableRow.append(newTableCell);
   newTableRow.find('a.toBeEdited').on('save', addGisUpdateParams);
  
   $("#addGisQueryTable").append(newTableRow);    
   $('#parameters').val(JSON.stringify(addGisParametersLocal));
   
   checkAddWidgetConditions();
}

function addGisQueryM()
{
   var newTableRow, newTableCell, newQueryObj, widgetId, widgetTitle = null;

   //Gestione caso nessuna soglia pregressa: costruiamo l'object literal per i parametri
   if(editGisParametersLocal === null)
   {
      //Creazione del JSON dei parametri
      editGisParametersLocal = {
            queries: [],
            targets: []
        };
   }
   
   newQueryObj = {
       desc: "",
       query: "",
       added : true,
       symbolMode : "auto",
       targets : [],
       color1: gisDefaultColors[($("#editGisQueryTable tr").length - 1)%7].color1,
       color2: gisDefaultColors[($("#editGisQueryTable tr").length - 1)%7].color2
   };
   
   editGisParametersLocal.queries.push(newQueryObj);

   //Aggiunta record alla tabella GUI delle query
   newTableRow = $('<tr></tr>');
   
   newTableCell = $('<td><input data-param="queryIconOption" checked type="checkbox" /></td>');
   newTableCell.find('input').bootstrapToggle({
      on: 'Auto',
      off: 'Man',
      size: 'small',
      onstyle: 'primary',
      offstyle: 'warning'
   });
   
   newTableRow.append(newTableCell);
   
   newTableCell = $('<td class="centerWithFlex"></td>');
   var imgMaxSize = $('<input type="hidden" name="MAX_FILE_SIZE" value="1000000" />');
   newTableCell.append(imgMaxSize);
   var newControl = $('<input type="file" class="form-control" name="editSelectorLogos[]">');
   newTableCell.append(newControl);
   newTableRow.append(newTableCell);
   newControl.filestyle({
       input: false,
       buttonText: "",
       buttonName: "btn-primary",
       size: "sm",
       disabled: false,
       badge: false
   });
   
   newTableRow.find('div.bootstrap-filestyle').hide();
   
    newControl.change(function() 
    {
        var file = this.files[0];
        var imagefile = file.type;
        var match= ["image/jpeg","image/png","image/jpg", "image/svg+xml"];
        if(!((imagefile === match[0]) || (imagefile === match[1]) || (imagefile === match[2]) || (imagefile === match[3])))
        {
            return false;
        }
        else
        {
            var reader = new FileReader();
            reader.onload = function(event){
                newControl.parents('tr').find('div.selectorMenuCustomIcon').html("");
                newControl.parents('tr').find('div.selectorMenuCustomIcon').css("background", "url(" + event.target.result + ")");
                newControl.parents('tr').find('div.selectorMenuCustomIcon').css("background-size", "contain");
                newControl.parents('tr').find('div.selectorMenuCustomIcon').css("background-repeat", "no-repeat");
                newControl.parents('tr').find('div.selectorMenuCustomIcon').css("background-position", "center center");
            };
            reader.readAsDataURL(this.files[0]);
        }
    });
   
   newTableCell = $('<td><i class="material-icons selectorMenuDefaultIcon" style="font-size: 34px; display: block;">navigation</i><div class="selectorMenuCustomIcon">None</div></td>');
   newTableRow.append(newTableCell);
   
   newTableRow.find('input[data-param=queryIconOption]').change(function(){
       var index = parseInt($(this).parents('tr').index() - 1);
       if($(this).prop('checked'))
       {
           newTableRow.find('div.bootstrap-filestyle').hide();
           newTableRow.find('div.selectorMenuCustomIcon').hide();
           newTableRow.find('i.selectorMenuDefaultIcon').show();
           editGisParametersLocal.queries[index].symbolMode = "auto";
       }
       else
       {
           newTableRow.find('div.bootstrap-filestyle').show();
           newTableRow.find('i.selectorMenuDefaultIcon').hide();
           newTableRow.find('div.selectorMenuCustomIcon').show();
           newTableRow.find('div.selectorMenuCustomIcon').css("width", newTableRow.find('div.selectorMenuCustomIcon').parents('td').width() + "px");
           newTableRow.find('div.selectorMenuCustomIcon').css("height", newTableRow.find('div.selectorMenuCustomIcon').parents('td').height() + "px");
           editGisParametersLocal.queries[index].symbolMode = "man";
       }
       $('#parametersM').val(JSON.stringify(editGisParametersLocal));
   });
   
   newTableCell = $('<td><a href="#" class="toBeEdited" data-type="text" data-mode="popup" data-param="queryDesc"></a></td>');
   newTableCell.find('a').editable({
       emptytext: "Empty",
       display: function(value, response){
           if(value.length > 16)
           {
               $(this).html(value.substring(0, 13) + "...");
           }
           else
           {
              $(this).html(value); 
           }
       }
   });
   newTableRow.append(newTableCell);
   
   newTableCell = $('<td><a href="#" class="toBeEdited" data-type="text" data-mode="popup" data-param="queryUrl"></td>');
   newTableCell.find('a').editable({
       emptytext: "Empty",
       display: function(value, response){
           if(value.length > 10)
           {
               $(this).html(value.substring(0, 10) + "...");
           }
           else
           {
              $(this).html(value); 
           }
       }
   });
   newTableRow.append(newTableCell);
   
   newTableCell = $('<td><div class="input-group colorPicker" data-param="color1"><input type="text" class="form-control"><span class="input-group-addon"><i class="thePicker"></i></span></div></td>');
   newTableRow.append(newTableCell);
   newTableRow.find('div.colorPicker').colorpicker({color: gisDefaultColors[($("#editGisQueryTable tr").length - 1)%7].color1, format: "rgba"});
   newTableRow.find('div.colorPicker').on('hidePicker', editGisUpdateParams); 
   
   newTableCell = $('<td><div class="input-group colorPicker" data-param="color2"><input type="text" class="form-control"><span class="input-group-addon"><i class="thePicker"></i></span></div></td>');
   newTableRow.append(newTableCell);
   newTableRow.find('div.colorPicker').colorpicker({color: gisDefaultColors[($("#editGisQueryTable tr").length - 1)%7].color2, format: "rgba"});
   newTableRow.find('div.colorPicker').on('hidePicker', editGisUpdateParams);
   
   newTableCell = $('<td><select data-param="targets" class="form-control" multiple></select></td>');
   newTableRow.append(newTableCell);
   
    $("li.gs_w").each(function(){
        if(($(this).attr("id").includes("BarContent"))||($(this).attr("id").includes("ColumnContent"))||($(this).attr("id").includes("GaugeChart"))||($(this).attr("id").includes("PieChart"))||($(this).attr("id").includes("SingleContent"))||($(this).attr("id").includes("Speedometer"))||($(this).attr("id").includes("TimeTrend")))
        {
          widgetId = $(this).attr("id");
          widgetTitle = $(this).find("div.titleDiv").html();
          newTableRow.find('select').append('<option value="' + widgetId + '">' + widgetTitle + '</option>');
        }
    });                               

   newTableRow.find('select').selectpicker({
                                       actionsBox: true, 
                                       width: 110
                                    });
   newTableRow.find('select').on('changed.bs.select', editGisUpdateParams);

   newTableCell = $('<td><a><i class="fa fa-close" style="font-size:24px;color:red"></i></a></td>');
   newTableCell.find('i').click(delGisQueryM);
   newTableRow.append(newTableCell);
   newTableRow.find('a.toBeEdited').on('save', editGisUpdateParams);
  
   $("#editGisQueryTable").append(newTableRow);    
   $('#parametersM').val(JSON.stringify(editGisParametersLocal));
   
   console.log($('#parametersM').val());
   
   checkEditWidgetConditions();
}

function addGisUpdateParams(e, params) 
{
   var param = $(this).attr('data-param');
   var rowIndex = $(this).parents("tr").index() - 1;
   var newValue = null;
   var numberPattern = /^-?\d*\.?\d+$/;
   
   //Aggiornamento dei parametri
   switch(param)
   {
      case 'queryDesc':
         newValue = params.newValue;
         addGisParametersLocal.queries[rowIndex].desc = newValue;
         break;
         
      case 'queryUrl':
         newValue = params.newValue;
         addGisParametersLocal.queries[rowIndex].query = newValue;
         break;   
      
      case 'color1':
          newValue = $(this).colorpicker('getValue');
          addGisParametersLocal.queries[rowIndex].color1 = newValue;
          break;
            
      case 'color2':
          newValue = $(this).colorpicker('getValue');
          addGisParametersLocal.queries[rowIndex].color2 = newValue;
          break;
          
      case 'targets':
          newValue = $(this).val();
          addGisParametersLocal.queries[rowIndex].targets = newValue;
          break;    

       default:
           break;
   }
   
   $('#parameters').val(JSON.stringify(addGisParametersLocal));
   checkAddWidgetConditions();
}

function editGisUpdateParams(e, params) 
{
   var param = $(this).attr('data-param');
   var rowIndex = $(this).parents("tr").index() - 1;
   var newValue = null;
   var numberPattern = /^-?\d*\.?\d+$/;
   
   //Aggiornamento dei parametri
   switch(param)
   {
      case 'queryDesc':
         newValue = params.newValue;
         editGisParametersLocal.queries[rowIndex].desc = newValue;
         break;
         
      case 'queryUrl':
         newValue = params.newValue;
         editGisParametersLocal.queries[rowIndex].query = newValue;
         break;   
      
      case 'color1':
          newValue = $(this).colorpicker('getValue');
          editGisParametersLocal.queries[rowIndex].color1 = newValue;
          break;
            
      case 'color2':
          newValue = $(this).colorpicker('getValue');
          editGisParametersLocal.queries[rowIndex].color2 = newValue;
          break;
          
      case 'targets':
          newValue = $(this).val();
          editGisParametersLocal.queries[rowIndex].targets = newValue;
          break;    

       default:
           break;
   }
   
   $('#parametersM').val(JSON.stringify(editGisParametersLocal));
   checkEditWidgetConditions();
}

function delGisQuery(e)
{
   var delIndex = parseInt($(this).parents('tr').index() - 1);
   
   //Cancellazione della riga dalla tabella 
   $(this).parents('tr').remove();
   
   //Aggiornamento JSON parametri
   addGisParametersLocal.queries.splice(delIndex, 1);
   
   $('#parameters').val(JSON.stringify(addGisParametersLocal));
   checkAddWidgetConditions();
}

function delGisQueryM(e)
{
   var delIndex = parseInt($(this).parents('tr').index() - 1);
   
   //Cancellazione della riga dalla tabella 
   //$(this).parents('tr').remove();
   $(this).parents('tr').hide();
   
   //Aggiornamento JSON parametri
   //editGisParametersLocal.queries.splice(delIndex, 1);
   
   editGisParametersLocal.queries[delIndex].deleted = true;
   
   $('#parametersM').val(JSON.stringify(editGisParametersLocal));
   checkEditWidgetConditions();
}

function addWidgetServerStatusNotificatorAndThresholdFields()
{
   var newFormRow, newLabel, newInnerDiv, newSelect = null;
   
   //Non cancellarla
   currentParamsSingleValueWidget = null;
                             
   //Nuova riga
   newFormRow = $('<div class="row"></div>');
   newLabel = $('<label for="addWidgetRegisterGen" class="col-md-2 control-label">Register to Notificator</label>');
   newInnerDiv = $('<div class="col-md-3"></div>');
   newSelect = $('<select name="addWidgetRegisterGen" class="form-control" id="addWidgetRegisterGen"></select>');
   newSelect.append('<option value="yes">Yes</option>');
   newSelect.append('<option value="no">No</option>');
   newSelect.val("no");
   newInnerDiv.append(newSelect);
   newFormRow.append(newLabel);
   newFormRow.append(newInnerDiv);
   
   //Set thresholds
   newLabel = $('<label for="alrThrSel" class="col-md-2 control-label">Set thresholds</label>');
   newInnerDiv = $('<div class="col-md-3"></div>');
   newSelect = $('<select class="form-control" id="alrThrSel" name="alrThrSel" required>');
   newSelect.append('<option value="yes">Yes</option>');
   newSelect.append('<option value="no">No</option>');
   newSelect.val('no');
   newInnerDiv.append(newSelect);
   newFormRow.append(newLabel);
   newFormRow.append(newInnerDiv);
   
   $("#specificWidgetPropertiesDiv").append(newFormRow);
   
   //Listener per settaggio/desettaggio soglie relativo alla select "Set thresholds"
   $('#alrThrSel').change(function(){
      if($(this).val() === "no")
      {
         $("#addWidgetRangeTableContainer").hide();
         $("label[for=alrThrSel]").css("color", "black");
         delete addWidgetConditionsArrayLocal["thrQt"];
      }
      else
      {
         $("#addWidgetRangeTableContainer").show();
         if(countAddWidgetThrConditions() > 0)
         {
            addWidgetConditionsArrayLocal["thrQt"] = true;
            $("label[for=alrThrSel]").css("color", "black");
         }
         else
         {
            addWidgetConditionsArrayLocal["thrQt"] = false;
            $("label[for=alrThrSel]").css("color", "red");
         }
      }
      
      checkAddWidgetConditions();
   });
   
   //Nuova riga
   //Contenitore per tabella delle soglie
   var addWidgetRangeTableContainer = $('<div id="addWidgetRangeTableContainer" class="row rowCenterContent"></div>');
   var addWidgetRangeTable = $("<table id='addWidgetRangeTable' class='table table-bordered table-condensed thrRangeTable'><col style='width:10%'><col style='width:30%'><col style='width:30%'><col style='width:20%'><col style='width:10%'><tr><td>Notify events</td><td>Thr operator</td><td>Status</td><td>Short description</td><td><a href='#'><i class='fa fa-plus' style='font-size:24px;color:#337ab7'></i></a></td></tr></table>");
   addWidgetRangeTableContainer.append(addWidgetRangeTable);
   addWidgetRangeTableContainer.hide();
   $("#specificWidgetPropertiesDiv").append(addWidgetRangeTableContainer);
   
   $("#addWidgetRangeTable i.fa-plus").click(addStatusRangeSingleValueWidget);
}

function addStatusRangeSingleValueWidget()
{
   var newTableRow, newTableCell, newRangeObj = null;

   //Gestione caso nessuna soglia pregressa: costruiamo l'object literal per i parametri
   if(currentParamsSingleValueWidget === null)
   {
      //Creazione del JSON dei parametri
      currentParamsSingleValueWidget = {
         thresholdObject: []
      };
   }
   
   newRangeObj = {
       notifyEvents: false,
       op: "notEqual",
       thr1: "token found",
       desc: ""
   };
   
   currentParamsSingleValueWidget.thresholdObject.push(newRangeObj);

   //Aggiunta record alla thrTable dell'asse di appartenenza
   newTableRow = $('<tr></tr>');
   
   newTableCell = $('<td><input data-param="notifyEvents" type="checkbox" /></td>');
   newTableCell.find("input").change(updateParamsSingleValueWidget);
   newTableRow.append(newTableCell);
   
   newTableCell = $('<td><select data-param="op" class="thrOpSelect"></select></td>');
   newTableCell.find("select").append('<option value="notEqual">&ne;</option>');
   newTableCell.find("select").append('<option value="equal">&equals;</option>');  
   newTableRow.append(newTableCell);
   
   newTableCell = $('<td><select data-param="thr1" class="thrOpSelect"></select></td>');
   newTableCell.find("select").append('<option value="token found">token found</option>');
   newTableCell.find("select").append('<option value="token not found">token not found</option>');
   newTableCell.find("select").append('<option value="error">error</option>');
   newTableCell.find("select").append('<option value="no response">no response</option>');
   newTableRow.append(newTableCell);
  
   newTableCell = $('<td><a href="#" class="toBeEdited" data-type="text" data-mode="popup" data-param="desc"></a></td>');
   newTableCell.find('a').editable();
   newTableRow.append(newTableCell);
   newTableCell = $('<td><a><i class="fa fa-close" style="font-size:24px;color:red"></i></a></td>');
   newTableCell.find('i').click(delThrRangeingleValueWidget);
   newTableRow.append(newTableCell);
   newTableRow.find('a.toBeEdited').on('save', updateParamsSingleValueWidget);
   
   newTableRow.find('td select.thrOpSelect').change(updateParamsSingleValueWidget);
   
   $("#addWidgetRangeTable").append(newTableRow);    
   $('#parameters').val(JSON.stringify(currentParamsSingleValueWidget));
   
   var rowCount = countAddWidgetThrConditions();
   
   if(rowCount > 0)
   {
      addWidgetConditionsArrayLocal["thrQt"] = true;
      $("label[for=alrThrSel]").css("color", "black");
      addWidgetConditionsArrayLocal["thrVal" + (rowCount - 1)] = true;
   }
   else
   {
      addWidgetConditionsArrayLocal["thrQt"] = false;
      $("label[for=alrThrSel]").css("color", "red");
   }
   
   checkAddWidgetConditions();
}

function editWidgetServerStatusGeneratorRegisterField(notificatorRegistered, notificatorEnabled, parameters)
{
   var newFormRow, newLabel, newInnerDiv, newSelect, newTableRow, newTableCell, op, thr1,desc, notifyEvents, newDiffObj = null;
   
   //Nuova riga
   newFormRow = $('<div class="row"></div>');
   newLabel = $('<label for="editWidgetRegisterGen" class="col-md-2 control-label">Register to Notificator</label>');
   newInnerDiv = $('<div class="col-md-3"></div>');
   newSelect = $('<select name="editWidgetRegisterGen" class="form-control" id="editWidgetRegisterGen"></select>');
   newSelect.append('<option value="yes">Yes</option>');
   newSelect.append('<option value="no">No</option>');
   
   if(notificatorRegistered === "no")
   {
      newSelect.val("no");
   }
   else
   {
      newSelect.val(notificatorEnabled);
   }
   
   newInnerDiv.append(newSelect);
   newFormRow.append(newLabel);
   newFormRow.append(newInnerDiv);
   
   //Set thresholds
   newLabel = $('<label for="alrThrSelM" class="col-md-2 control-label">Set thresholds</label>');
   newInnerDiv = $('<div class="col-md-3"></div>');
   newSelect = $('<select class="form-control" id="alrThrSelM" name="alrThrSelM" required>');
   newSelect.append('<option value="yes">Yes</option>');
   newSelect.append('<option value="no">No</option>');
   
   if(parameters === null)
   {
      newSelect.val("no");
      currentParamsSingleValueWidget = null;
      parametersDiff = null;
   }
   else
   {
      newSelect.val("yes");
      $('#parametersM').val(parameters);
      currentParamsSingleValueWidget = JSON.parse(parameters);
      //Costruzione dell'oggetto che terrà traccia delle modifiche ai tipi di evento
      parametersDiff = {
         addedChangedKept: [],
         deleted: []
      };
       
      for(var i in currentParamsSingleValueWidget.thresholdObject)
      {
         newDiffObj = {
            op: null,
            opNew: null,
            thr1: null,
            thr1New: null,
            desc: null,
            descNew: null,
            added: false,
            changed: false,
            deleted: false
         };
         
         newDiffObj.op = currentParamsSingleValueWidget.thresholdObject[i].op;
         newDiffObj.opNew = currentParamsSingleValueWidget.thresholdObject[i].op;
         newDiffObj.thr1 = currentParamsSingleValueWidget.thresholdObject[i].thr1;
         newDiffObj.thr1New = currentParamsSingleValueWidget.thresholdObject[i].thr1;
         newDiffObj.thr1 = currentParamsSingleValueWidget.thresholdObject[i].thr1;
         newDiffObj.thr1New = currentParamsSingleValueWidget.thresholdObject[i].thr1;
         newDiffObj.desc = currentParamsSingleValueWidget.thresholdObject[i].desc;
         newDiffObj.descNew = currentParamsSingleValueWidget.thresholdObject[i].desc;
         
         parametersDiff.addedChangedKept.push(newDiffObj);
      }
      
      editWidgetConditionsArrayLocal["thrQt"] = true;
      
      $('#parametersDiff').val(JSON.stringify(parametersDiff));
   }
   
   newInnerDiv.append(newSelect);
   newFormRow.append(newLabel);
   newFormRow.append(newInnerDiv);
   
   $("#specificParamsM").append(newFormRow);
   
   //Contenitore per tabella delle soglie
   var editWidgetRangeTableContainer = $('<div id="editWidgetRangeTableContainer" class="row rowCenterContent"></div>');
   var editWidgetRangeTable = $("<table id='editWidgetRangeTable' class='table table-bordered table-condensed thrRangeTable'><col style='width:10%'><col style='width:30%'><col style='width:30%'><col style='width:20%'><col style='width:10%'><tr><td>Notify events</td><td>Thr operator</td><td>Status</td><td>Short description</td><td><a href='#'><i class='fa fa-plus' style='font-size:24px;color:#337ab7'></i></a></td></tr></table>");
   editWidgetRangeTableContainer.append(editWidgetRangeTable);
   
   $("#specificParamsM").append(editWidgetRangeTableContainer);
   
   if(parameters === null)
   {
      editWidgetRangeTableContainer.hide();
   }
   else
   {
      //Caricamento tabella
      for(var k = 0; k < currentParamsSingleValueWidget.thresholdObject.length; k++)
      {
         editWidgetConditionsArrayLocal["thrVal" + k] = true;
         
         notifyEvents = currentParamsSingleValueWidget.thresholdObject[k].notifyEvents;
         op = currentParamsSingleValueWidget.thresholdObject[k].op;
         thr1 = currentParamsSingleValueWidget.thresholdObject[k].thr1;
        
          desc = currentParamsSingleValueWidget.thresholdObject[k].desc;

          //Aggiunta a tabella
          newTableRow = $('<tr></tr>');
          
          newTableCell = $('<td><input data-param="notifyEvents" type="checkbox" /></td>');
          if(notifyEvents)
          {
             newTableCell.find("input").prop("checked", true);
          }
          newTableCell.find("input").change(updateParamsSingleValueWidgetM);
          newTableRow.append(newTableCell);
          
          newTableCell = $('<td><select data-param="op" class="thrOpSelect"></select></td>');
         
          newTableCell.find("select").append('<option value="equal">&equals;</option>');
          newTableCell.find("select").append('<option value="notEqual">&ne;</option>');
          newTableRow.append(newTableCell);
          
          newTableCell.find("select").val(currentParamsSingleValueWidget.thresholdObject[k].op);
          
          
          newTableCell = $('<td><select data-param="thr1" class="thrOpSelect"></select></td>');
          newTableCell.find("select").append('<option value="token found">token found</option>');
          newTableCell.find("select").append('<option value="token not found">token not found</option>');
          newTableCell.find("select").append('<option value="error">error</option>');
          newTableCell.find("select").append('<option value="no response">no response</option>');
          newTableRow.append(newTableCell);
          
          newTableCell.find("select").val(currentParamsSingleValueWidget.thresholdObject[k].thr1);

          newTableCell = $('<td><a href="#" class="toBeEdited" data-type="text" data-mode="popup" data-param="desc">' + desc + '</a></td>');
          newTableRow.append(newTableCell);

          newTableCell = $('<td><a href="#"><i class="fa fa-close" style="font-size:24px;color:red"></i></a></td>');
          newTableCell.find('i').click(delThrRangeingleValueWidgetM);
          newTableRow.append(newTableCell);
          
          newTableRow.find("a.toBeEdited").editable();
          editWidgetRangeTable.append(newTableRow);
          newTableRow.find("a.toBeEdited").on('save', updateParamsSingleValueWidgetM);
      }
      
      editWidgetRangeTableContainer.show();
      
      $('#editWidgetRangeTable tr td select.thrOpSelect').change(updateParamsSingleValueWidgetM);
   }
   
   $("#editWidgetRangeTable i.fa-plus").click(addStatusRangeSingleValueWidgetM);
   
   //Listener per settaggio/desettaggio soglie relativo alla select "Set thresholds"
   $('#alrThrSelM').change(function(){
      if($(this).val() === "no")
      {
         $("#editWidgetRangeTableContainer").hide();
         $("label[for=alrThrSelM]").css("color", "black");
         delete editWidgetConditionsArrayLocal["thrQt"];
      }
      else
      {
         $("#editWidgetRangeTableContainer").show();
         if(countEditWidgetThrConditions() > 0)
         {
            editWidgetConditionsArrayLocal["thrQt"] = true;
            $("label[for=alrThrSelM]").css("color", "black");
         }
         else
         {
            editWidgetConditionsArrayLocal["thrQt"] = false;
            $("label[for=alrThrSelM]").css("color", "red");
         }
      }

      checkEditWidgetConditions();
   });
   
   checkEditWidgetConditions();
   
   /*console.log("Params:");
   console.log(JSON.stringify($('#parametersM').val()));
   console.log("------------------");
   console.log("Diff:");
   console.log(JSON.stringify($('#parametersDiff').val()));*/
}

function addStatusRangeSingleValueWidgetM()
{
   var newTableRow, newTableCell, newRangeObj, newDiffObj = null;

   //Gestione caso nessuna soglia pregressa: costruiamo l'object literal per i parametri
   if(currentParamsSingleValueWidget === null)
   {
      //Creazione del JSON dei parametri
      currentParamsSingleValueWidget = {
         thresholdObject: []
      };
      
      parametersDiff = {
         addedChangedKept: [],
         deleted: []
      };
   }

   //Aggiungiamo un elemento alle thrSeries in esame, di modo che poi possa accogliere i nuovi valori dal save di XEditor
   newRangeObj = {
       notifyEvents: false,
       op: "notEqual",
       thr1: "token found",
       desc: ""
   };
   
   newDiffObj = {
      op: "notEqual",
      opNew: "notEqual",
      thr1: "token found",
      thr1New: "token found",
      desc: "",
      descNew: "",
      added: true,
      changed: false,
      deleted: false
   };
   
   currentParamsSingleValueWidget.thresholdObject.push(newRangeObj);
   parametersDiff.addedChangedKept.push(newDiffObj);
   
   //Aggiunta record alla thrTable dell'asse di appartenenza
   newTableRow = $('<tr></tr>');
   
   newTableCell = $('<td><input data-param="notifyEvents" type="checkbox" /></td>');
   newTableCell.find("input").change(updateParamsSingleValueWidgetM);
   newTableRow.append(newTableCell);
   
   newTableCell = $('<td><select data-param="op" class="thrOpSelect"></select></td>');  
   newTableCell.find("select").append('<option value="notEqual">&ne;</option>'); 
   newTableCell.find("select").append('<option value="equal">&equals;</option>'); 
   newTableRow.append(newTableCell);
   
   newTableCell = $('<td><select data-param="thr1" class="thrOpSelect"></select></td>');
   newTableCell.find("select").append('<option value="token found">token found</option>');
   newTableCell.find("select").append('<option value="token not found">token not found</option>');
   newTableCell.find("select").append('<option value="error">error</option>');
   newTableCell.find("select").append('<option value="no response">no response</option>');
   newTableRow.append(newTableCell);
   
   
   newTableCell = $('<td><a href="#" class="toBeEdited" data-type="text" data-mode="popup" data-param="desc"></a></td>');
   newTableCell.find('a').editable();
   newTableRow.append(newTableCell);
   newTableCell = $('<td><a><i class="fa fa-close" style="font-size:24px;color:red"></i></a></td>');
   newTableCell.find('i').click(delThrRangeingleValueWidgetM);
   newTableRow.append(newTableCell);
   newTableRow.find('a.toBeEdited').on('save', updateParamsSingleValueWidgetM);
   newTableRow.find('select.thrOpSelect').change(updateParamsSingleValueWidgetM);
   newTableRow.find('select.thrOpSelect').change(function(){
     if(($(this).val() === "intervalOpen")||($(this).val() === "intervalClosed")||($(this).val() === "intervalLeftOpen")||($(this).val() === "intervalRightOpen"))
     {
        newTableRow.find('a[data-param=thr2]').editable('enable');
        newTableRow.find('a[data-param=thr2]').editable('setValue', 0);
     }
     else
     {
        newTableRow.find('a[data-param=thr2]').editable('disable');
        newTableRow.find('a[data-param=thr2]').html("");
     }
   });
   $("#editWidgetRangeTable").append(newTableRow);    
   $('#parametersM').val(JSON.stringify(currentParamsSingleValueWidget));
   $('#parametersDiff').val(JSON.stringify(parametersDiff));
   
   if(countEditWidgetThrConditions() > 0)
   {
      editWidgetConditionsArrayLocal["thrQt"] = true;
      $("label[for=alrThrSelM]").css("color", "black");
   }
   else
   {
      editWidgetConditionsArrayLocal["thrQt"] = false;
      $("label[for=alrThrSelM]").css("color", "red");
   }
   
   checkEditWidgetConditions();
   
   /*console.log("Params:");
   console.log(JSON.stringify($('#parametersM').val()));
   console.log("------------------");
   console.log("Diff:");
   console.log(JSON.stringify($('#parametersDiff').val()));*/
}

function setAddWidgetConditionsArray(addWidgetConditionsArrayAtt)
{
   addWidgetConditionsArrayLocal = addWidgetConditionsArrayAtt;
}

function setEditWidgetConditionsArray(editWidgetConditionsArrayAtt)
{
   editWidgetConditionsArrayLocal = editWidgetConditionsArrayAtt;
}

function checkAddWidgetConditions()
{
    var enableButton = true;

    for(var key in addWidgetConditionsArrayLocal) 
    {
        if(addWidgetConditionsArrayLocal[key] === false)
        {
            enableButton = false;
            break;
        }
    }

    if(enableButton)
    {
        $("#button_add_widget").attr("disabled", false);
    }
    else
    {
        $("#button_add_widget").attr("disabled", true);
    }
}

function checkEditWidgetConditions()
{
    var enableButton = true;

    for(var key in editWidgetConditionsArrayLocal) 
    {
        if(editWidgetConditionsArrayLocal[key] === false)
        {
            enableButton = false;
            break;
        }
    }

    if(enableButton)
    {
        $("#button_modify_widget").attr("disabled", false);
    }
    else
    {
        $("#button_modify_widget").attr("disabled", true);
    }
}

function countAddWidgetThrConditions()
{
   return $("#addWidgetRangeTable tbody tr td input[type=checkbox]").length;
}

function countEditWidgetThrConditions()
{
   return $("#editWidgetRangeTable tbody tr td input[type=checkbox]").length;
}

function addWidgetNotificatorAndThresholdFields()
{
   var newFormRow, newLabel, newInnerDiv, newSelect = null;
   
   //Non cancellarla
   currentParamsSingleValueWidget = null;
                             
   //Nuova riga
   newFormRow = $('<div class="row"></div>');
   newLabel = $('<label for="addWidgetRegisterGen" class="col-md-2 control-label">Register to Notificator</label>');
   newInnerDiv = $('<div class="col-md-3"></div>');
   newSelect = $('<select name="addWidgetRegisterGen" class="form-control" id="addWidgetRegisterGen"></select>');
   newSelect.append('<option value="yes">Yes</option>');
   newSelect.append('<option value="no">No</option>');
   newSelect.val("no");
   newInnerDiv.append(newSelect);
   newFormRow.append(newLabel);
   newFormRow.append(newInnerDiv);
   
   //Set thresholds
   newLabel = $('<label for="alrThrSel" class="col-md-2 control-label">Set thresholds</label>');
   newInnerDiv = $('<div class="col-md-3"></div>');
   newSelect = $('<select class="form-control" id="alrThrSel" name="alrThrSel" required>');
   newSelect.append('<option value="yes">Yes</option>');
   newSelect.append('<option value="no">No</option>');
   newSelect.val('no');
   newInnerDiv.append(newSelect);
   newFormRow.append(newLabel);
   newFormRow.append(newInnerDiv);
   
   $("#specificWidgetPropertiesDiv").append(newFormRow);
   
   //Nuova riga
   /*newFormRow = $('<div class="row"></div>');
   newLabel = $('<label for="addWidgetShowNotificator" class="col-md-2 control-label">Open Notificator after widget insertion</label>');
   newInnerDiv = $('<div class="col-md-3"></div>');
   newSelect = $('<select name="addWidgetShowNotificator" class="form-control" id="addWidgetShowNotificator"></select>');
   newSelect.append('<option value="0">No</option>');
   newSelect.append('<option value="1">Yes</option>');
   newSelect.val("0");
   newInnerDiv.append(newSelect);
   newFormRow.append(newLabel);
   newFormRow.append(newInnerDiv);
   
   $("#specificWidgetPropertiesDiv").append(newFormRow);
   newFormRow.hide();*/
   
   //Listener per settaggio/desettaggio soglie relativo alla select "Set thresholds"
   $('#alrThrSel').change(function(){
      if($(this).val() === "no")
      {
         //$("label[for=addWidgetShowNotificator]").parents('div.row').hide();
         //$("#addWidgetShowNotificator").val("0"); 
         $("#addWidgetRangeTableContainer").hide();
         $("label[for=alrThrSel]").css("color", "black");
         delete addWidgetConditionsArrayLocal["thrQt"];
      }
      else
      {
         //$("label[for=addWidgetShowNotificator]").parents('div.row').show();
         //$("#addWidgetShowNotificator").val("1"); 
         $("#addWidgetRangeTableContainer").show();
         if(countAddWidgetThrConditions() > 0)
         {
            addWidgetConditionsArrayLocal["thrQt"] = true;
            $("label[for=alrThrSel]").css("color", "black");
         }
         else
         {
            addWidgetConditionsArrayLocal["thrQt"] = false;
            $("label[for=alrThrSel]").css("color", "red");
         }
      }
      
      checkAddWidgetConditions();
   });
   
   //Nuova riga
   //Contenitore per tabella delle soglie
   var addWidgetRangeTableContainer = $('<div id="addWidgetRangeTableContainer" class="row rowCenterContent"></div>');
   var addWidgetRangeTable = $("<table id='addWidgetRangeTable' class='table table-bordered table-condensed thrRangeTable'><col style='width:10%'><col style='width:20%'><col style='width:10%'><col style='width:10%'><col style='width:20%'><col style='width:20%'><col style='width:10%'><tr><td>Notify events</td><td>Thr operator</td><td>Thr 1</td><td>Thr 2</td><td>Range color</td><td>Short description</td><td><a href='#'><i class='fa fa-plus' style='font-size:24px;color:#337ab7'></i></a></td></tr></table>");
   addWidgetRangeTableContainer.append(addWidgetRangeTable);
   addWidgetRangeTableContainer.hide();
   $("#specificWidgetPropertiesDiv").append(addWidgetRangeTableContainer);
   
   $("#addWidgetRangeTable i.fa-plus").click(addThrRangeSingleValueWidget);
}

function addThrRangeSingleValueWidget()
{
   var newTableRow, newTableCell, newRangeObj = null;

   //Gestione caso nessuna soglia pregressa: costruiamo l'object literal per i parametri
   if(currentParamsSingleValueWidget === null)
   {
      //Creazione del JSON dei parametri
      currentParamsSingleValueWidget = {
         thresholdObject: []
      };
   }
   
   newRangeObj = {
       notifyEvents: false,
       op: "less",
       thr1: 0,
       thr2: 0,
       color: "#FFFFFF",
       desc: ""
   };
   
   currentParamsSingleValueWidget.thresholdObject.push(newRangeObj);

   //Aggiunta record alla thrTable dell'asse di appartenenza
   newTableRow = $('<tr></tr>');
   
   newTableCell = $('<td><input data-param="notifyEvents" type="checkbox" /></td>');
   newTableCell.find("input").change(updateParamsSingleValueWidget);
   newTableRow.append(newTableCell);
   
   newTableCell = $('<td><select data-param="op" class="thrOpSelect"></select></td>');
   newTableCell.find("select").append('<option value="less">&lt;</option>');
   newTableCell.find("select").append('<option value="lessEqual">&le;</option>');
   newTableCell.find("select").append('<option value="greater">&gt;</option>');
   newTableCell.find("select").append('<option value="greaterEqual">&ge;</option>');
   newTableCell.find("select").append('<option value="equal">&equals;</option>');
   newTableCell.find("select").append('<option value="notEqual">&ne;</option>');
   newTableCell.find("select").append('<option value="intervalOpen">a &lt; val &lt; b</option>');
   newTableCell.find("select").append('<option value="intervalClosed">a &le; val &le; b</option>');
   newTableCell.find("select").append('<option value="intervalLeftOpen">a &lt; val &le; b</option>');
   newTableCell.find("select").append('<option value="intervalRightOpen">a &le; val &lt; b</option>');
   newTableRow.append(newTableCell);
   
   newTableCell = $('<td><a href="#" class="toBeEdited" data-type="text" data-mode="popup" data-param="thr1">0</a></td>');
   newTableCell.find('a').editable();
   newTableRow.append(newTableCell);
   
   newTableCell = $('<td><a href="#" class="toBeEdited" data-type="text" data-mode="popup" data-param="thr2"></td>');
   newTableCell.find('a').editable({
      disabled: true
   });
   newTableRow.append(newTableCell);

   newTableCell = $('<td><div class="input-group colorPicker" data-param="color"><input type="text" class="form-control"><span class="input-group-addon"><i class="thePicker"></i></span></div></td>');
   newTableRow.append(newTableCell);
   newTableRow.find('div.colorPicker').colorpicker({color: "#FFFFFF", format: "rgba"});
   newTableRow.find('div.colorPicker').on('hidePicker', updateParamsSingleValueWidget); 

   newTableCell = $('<td><a href="#" class="toBeEdited" data-type="text" data-mode="popup" data-param="desc"></a></td>');
   newTableCell.find('a').editable();
   newTableRow.append(newTableCell);
   newTableCell = $('<td><a><i class="fa fa-close" style="font-size:24px;color:red"></i></a></td>');
   newTableCell.find('i').click(delThrRangeingleValueWidget);
   newTableRow.append(newTableCell);
   newTableRow.find('a.toBeEdited').on('save', updateParamsSingleValueWidget);
   
   newTableRow.find('td select.thrOpSelect').change(updateParamsSingleValueWidget);
   
   $("#addWidgetRangeTable").append(newTableRow);    
   $('#parameters').val(JSON.stringify(currentParamsSingleValueWidget));
   
   var rowCount = countAddWidgetThrConditions();
   
   if(rowCount > 0)
   {
      addWidgetConditionsArrayLocal["thrQt"] = true;
      $("label[for=alrThrSel]").css("color", "black");
      addWidgetConditionsArrayLocal["thrVal" + (rowCount - 1)] = true;
   }
   else
   {
      addWidgetConditionsArrayLocal["thrQt"] = false;
      $("label[for=alrThrSel]").css("color", "red");
   }
   
   checkAddWidgetConditions();
}

function updateParamsSingleValueWidget(e, params) 
{
   var param = $(this).attr('data-param');
   var rowIndex = $(this).parents("tr").index() - 1;
   var newValue = null;
   //var numberPattern = /^\d*\.?\d+$/;
   var numberPattern = /^-?\d*\.?\d+$/;
   
   //Aggiornamento dei parametri
   switch(param)
   {
      case 'notifyEvents':
         newValue = $(this).prop("checked");
         currentParamsSingleValueWidget.thresholdObject[rowIndex].notifyEvents = newValue;
         break;
         
      case 'op':
         newValue = $(this).val();
         currentParamsSingleValueWidget.thresholdObject[rowIndex].op = newValue;
         
         if(($(this).val() === "intervalOpen")||($(this).val() === "intervalClosed")||($(this).val() === "intervalLeftOpen")||($(this).val() === "intervalRightOpen"))
         {
            $(this).parents("tr").find('td a[data-param=thr2]').editable('enable');
            $(this).parents("tr").find('td a[data-param=thr2]').editable('setValue', 0);
            
            if(numberPattern.test($(this).parents("tr").find('td a[data-param=thr1]').editable('getValue', true)))
            {  
               //Coerenza fra thr1 e thr2
               if(parseFloat($(this).parents("tr").find('td a[data-param=thr1]').editable('getValue', true)) < parseFloat($(this).parents("tr").find("td a[data-param=thr2]").editable('getValue', true)))
               {
                  addWidgetConditionsArrayLocal["thrVal" + rowIndex] = true;
                  $(this).parents("tr").find("td a[data-param=thr1]").css("color", "#337ab7");
                  $(this).parents("tr").find("td a[data-param=thr2]").css("color", "#337ab7");
               }
               else
               {
                  addWidgetConditionsArrayLocal["thrVal" + rowIndex] = false;
                  $(this).parents("tr").find("td a[data-param=thr1]").css("color", "red");
                  $(this).parents("tr").find("td a[data-param=thr2]").css("color", "red");
               }
            }
            else
            {
               addWidgetConditionsArrayLocal["thrVal" + rowIndex] = false;
               $(this).parents("tr").find("td a[data-param=thr1]").css("color", "red");
            }
         }
         else
         {
             if($('#select-widget').val() !== 'widgetServerStatus')
             {
                $(this).parents("tr").find('td a[data-param=thr2]').editable('disable');
                $(this).parents("tr").find('td a[data-param=thr2]').html("");

                if(numberPattern.test($(this).parents("tr").find('td a[data-param=thr1]').editable('getValue', true)))
                {  
                   addWidgetConditionsArrayLocal["thrVal" + rowIndex] = true;
                   $(this).parents("tr").find("td a[data-param=thr1]").css("color", "#337ab7");
                   $(this).parents("tr").find("td a[data-param=thr2]").css("color", "#337ab7");
                }
                else
                {
                   addWidgetConditionsArrayLocal["thrVal" + rowIndex] = false;
                   $(this).parents("tr").find("td a[data-param=thr1]").css("color", "red");
                } 
             }
         }
         break;   
      
       case 'thr1':
           if($('#select-widget').val() !== 'widgetServerStatus')
           {
               newValue = params.newValue;
           }
           else
           {
               newValue = $(this).val();
           }
           
           currentParamsSingleValueWidget.thresholdObject[rowIndex].thr1 = newValue;
           
           if($('#select-widget').val() !== 'widgetServerStatus')
           {
                if(numberPattern.test(newValue))
                {
                    if(($(this).parents("tr").find("td select.thrOpSelect").val() === "intervalOpen")||($(this).parents("tr").find("td select.thrOpSelect").val() === "intervalClosed")||($(this).parents("tr").find("td select.thrOpSelect").val() === "intervalLeftOpen")||($(this).parents("tr").find("td select.thrOpSelect").val() === "intervalRightOpen"))
                    {
                       //Coerenza con thr2 
                       if(numberPattern.test($(this).parents("tr").find("td a[data-param=thr2]").editable('getValue', true)))
                       {
                          if(parseFloat(newValue) < parseFloat($(this).parents("tr").find("td a[data-param=thr2]").editable('getValue', true)))
                          {
                             addWidgetConditionsArrayLocal["thrVal" + rowIndex] = true;
                             $(this).css("color", "#337ab7");
                             $(this).parents("tr").find("td a[data-param=thr2]").css("color", "#337ab7");
                          }
                          else
                          {
                             addWidgetConditionsArrayLocal["thrVal" + rowIndex] = false;
                             $(this).css("color", "red");
                             $(this).parents("tr").find("td a[data-param=thr2]").css("color", "red");
                          }
                       }
                       else
                       {
                          addWidgetConditionsArrayLocal["thrVal" + rowIndex] = false;
                          $(this).parents("tr").find("td a[data-param=thr2]").css("color", "red");
                       }
                    }
                    else
                    {
                       addWidgetConditionsArrayLocal["thrVal" + rowIndex] = true;
                       $(this).css("color", "#337ab7");
                    }
                }
                else
                {
                   addWidgetConditionsArrayLocal["thrVal" + rowIndex] = false;
                   $(this).css("color", "red");
                }
           }
           break;

       case 'thr2':
           newValue = params.newValue;
           currentParamsSingleValueWidget.thresholdObject[rowIndex].thr2 = newValue;
           
           if(numberPattern.test(newValue))
           {
               if(($(this).parents("tr").find("td select.thrOpSelect").val() === "intervalOpen")||($(this).parents("tr").find("td select.thrOpSelect").val() === "intervalClosed")||($(this).parents("tr").find("td select.thrOpSelect").val() === "intervalLeftOpen")||($(this).parents("tr").find("td select.thrOpSelect").val() === "intervalRightOpen"))
               {
                  //Coerenza con thr2 
                  if(numberPattern.test($(this).parents("tr").find("td a[data-param=thr1]").editable('getValue', true)))
                  {
                     if(parseFloat(newValue) > parseFloat($(this).parents("tr").find("td a[data-param=thr1]").editable('getValue', true)))
                     {
                        addWidgetConditionsArrayLocal["thrVal" + rowIndex] = true;
                        $(this).parents("tr").find("td a[data-param=thr1]").css("color", "#337ab7");
                        $(this).css("color", "#337ab7");
                     }
                     else
                     {
                        addWidgetConditionsArrayLocal["thrVal" + rowIndex] = false;
                        $(this).parents("tr").find("td a[data-param=thr1]").css("color", "red");
                        $(this).css("color", "red");
                     }
                  }
                  else
                  {
                     addWidgetConditionsArrayLocal["thrVal" + rowIndex] = false;
                     $(this).parents("tr").find("td a[data-param=thr1]").css("color", "red");
                  }
               }
               else
               {
                  addWidgetConditionsArrayLocal["thrVal" + rowIndex] = true;
                  $(this).css("color", "#337ab7");
               }
           }
           else
           {
              addWidgetConditionsArrayLocal["thrVal" + rowIndex] = false;
              $(this).css("color", "red");
           }
           break;

       case 'color':
           newValue = $(this).colorpicker('getValue');
           currentParamsSingleValueWidget.thresholdObject[rowIndex].color = newValue;
           break;

       case 'desc':
           newValue = params.newValue;
           currentParamsSingleValueWidget.thresholdObject[rowIndex].desc = newValue;
           break;    

       default:
           console.log("Default");
           break;
   }
   
   $('#parameters').val(JSON.stringify(currentParamsSingleValueWidget));
   checkAddWidgetConditions();
}

function delThrRangeingleValueWidget(e)
{
   var delIndex = parseInt($(this).parents('tr').index() - 1);
   var oldIndex = null;
   var tempArray = new Array();
   
   //Cancellazione della riga dalla tabella 
   $(this).parents('tr').remove();
   delete addWidgetConditionsArrayLocal["thrVal" + delIndex];
   
   for(var key in addWidgetConditionsArrayLocal)
   {
      if(key.includes("thrVal"))
      {
         oldIndex = parseInt(key.substr(6));
         
         if(oldIndex > delIndex)
         {
            tempArray["thrVal" + parseInt(oldIndex - 1)] = addWidgetConditionsArrayLocal[key];
         }
         else
         {
            tempArray[key] = addWidgetConditionsArrayLocal[key];
         }
      }
      else
      {
         tempArray[key] = addWidgetConditionsArrayLocal[key];
      }
   }
   
   addWidgetConditionsArrayLocal = null;
   addWidgetConditionsArrayLocal = tempArray;
   
   //Aggiornamento JSON parametri
   currentParamsSingleValueWidget.thresholdObject.splice(delIndex, 1);
   
   $('#parameters').val(JSON.stringify(currentParamsSingleValueWidget));
   if(countAddWidgetThrConditions() > 0)
   {
      addWidgetConditionsArrayLocal["thrQt"] = true;
      $("label[for=alrThrSel]").css("color", "black");
   }
   else
   {
      addWidgetConditionsArrayLocal["thrQt"] = false;
      $("label[for=alrThrSel]").css("color", "red");
   }
   checkAddWidgetConditions();
}

function editWidgetGeneratorRegisterField(notificatorRegistered, notificatorEnabled, parameters)
{
   var newFormRow, newLabel, newInnerDiv, newSelect, newTableRow, newTableCell, op, thr1, thr2, color, desc, notifyEvents, newDiffObj = null;
   
   //Nuova riga
   newFormRow = $('<div class="row"></div>');
   newLabel = $('<label for="editWidgetRegisterGen" class="col-md-2 control-label">Register to Notificator</label>');
   newInnerDiv = $('<div class="col-md-3"></div>');
   newSelect = $('<select name="editWidgetRegisterGen" class="form-control" id="editWidgetRegisterGen"></select>');
   newSelect.append('<option value="yes">Yes</option>');
   newSelect.append('<option value="no">No</option>');
   
   if(notificatorRegistered === "no")
   {
      newSelect.val("no");
   }
   else
   {
      newSelect.val(notificatorEnabled);
   }
   
   newInnerDiv.append(newSelect);
   newFormRow.append(newLabel);
   newFormRow.append(newInnerDiv);
   
   //Set thresholds
   newLabel = $('<label for="alrThrSelM" class="col-md-2 control-label">Set thresholds</label>');
   newInnerDiv = $('<div class="col-md-3"></div>');
   newSelect = $('<select class="form-control" id="alrThrSelM" name="alrThrSelM" required>');
   newSelect.append('<option value="yes">Yes</option>');
   newSelect.append('<option value="no">No</option>');
   
   if(parameters === null)
   {
      newSelect.val("no");
      currentParamsSingleValueWidget = null;
      parametersDiff = null;
   }
   else
   {
      newSelect.val("yes");
      $('#parametersM').val(parameters);
      currentParamsSingleValueWidget = JSON.parse(parameters);
      //Costruzione dell'oggetto che terrà traccia delle modifiche ai tipi di evento
      parametersDiff = {
         addedChangedKept: [],
         deleted: []
      };
       
      for(var i in currentParamsSingleValueWidget.thresholdObject)
      {
         newDiffObj = {
            op: null,
            opNew: null,
            thr1: null,
            thr1New: null,
            thr2: null,
            thr2New: null,
            desc: null,
            descNew: null,
            added: false,
            changed: false,
            deleted: false
         };
         
         newDiffObj.op = currentParamsSingleValueWidget.thresholdObject[i].op;
         newDiffObj.opNew = currentParamsSingleValueWidget.thresholdObject[i].op;
         newDiffObj.thr1 = currentParamsSingleValueWidget.thresholdObject[i].thr1;
         newDiffObj.thr1New = currentParamsSingleValueWidget.thresholdObject[i].thr1;
         newDiffObj.thr1 = currentParamsSingleValueWidget.thresholdObject[i].thr1;
         newDiffObj.thr1New = currentParamsSingleValueWidget.thresholdObject[i].thr1;
         newDiffObj.thr2 = currentParamsSingleValueWidget.thresholdObject[i].thr2;
         newDiffObj.thr2New = currentParamsSingleValueWidget.thresholdObject[i].thr2;
         newDiffObj.desc = currentParamsSingleValueWidget.thresholdObject[i].desc;
         newDiffObj.descNew = currentParamsSingleValueWidget.thresholdObject[i].desc;
         
         parametersDiff.addedChangedKept.push(newDiffObj);
      }
      
      editWidgetConditionsArrayLocal["thrQt"] = true;
      
      $('#parametersDiff').val(JSON.stringify(parametersDiff));
   }
   
   newInnerDiv.append(newSelect);
   newFormRow.append(newLabel);
   newFormRow.append(newInnerDiv);
   
   $("#specificParamsM").append(newFormRow);
   
   //Nuova riga
   /*newFormRow = $('<div class="row"></div>');
   newLabel = $('<label for="editWidgetShowNotificator" class="col-md-2 control-label">Open Notificator after widget edit</label>');
   newInnerDiv = $('<div class="col-md-3"></div>');
   newSelect = $('<select name="editWidgetShowNotificator" class="form-control" id="editWidgetShowNotificator"></select>');
   newSelect.append('<option value="0">No</option>');
   newSelect.append('<option value="1">Yes</option>');
   newSelect.val("0");
   newInnerDiv.append(newSelect);
   newFormRow.append(newLabel);
   newFormRow.append(newInnerDiv);
   
   $("#specificParamsM").append(newFormRow);
   
   if(parameters === null)
   {
       newFormRow.hide();
       $('#editWidgetShowNotificator').val("0");
   }
   else
   {
       newFormRow.show();
       $('#editWidgetShowNotificator').val("1");
   }*/
   
   //Contenitore per tabella delle soglie
   var editWidgetRangeTableContainer = $('<div id="editWidgetRangeTableContainer" class="row rowCenterContent"></div>');
   var editWidgetRangeTable = $("<table id='editWidgetRangeTable' class='table table-bordered table-condensed thrRangeTable'><col style='width:10%'><col style='width:20%'><col style='width:10%'><col style='width:10%'><col style='width:20%'><col style='width:20%'><col style='width:10%'><tr><td>Notify events</td><td>Thr operator</td><td>Thr 1</td><td>Thr 2</td><td>Range color</td><td>Short description</td><td><a href='#'><i class='fa fa-plus' style='font-size:24px;color:#337ab7'></i></a></td></tr></table>");
   editWidgetRangeTableContainer.append(editWidgetRangeTable);
   
   $("#specificParamsM").append(editWidgetRangeTableContainer);
   
   if(parameters === null)
   {
      editWidgetRangeTableContainer.hide();
   }
   else
   {
      //Caricamento tabella
      for(var k = 0; k < currentParamsSingleValueWidget.thresholdObject.length; k++)
      {
         editWidgetConditionsArrayLocal["thrVal" + k] = true;
         
         
         notifyEvents = currentParamsSingleValueWidget.thresholdObject[k].notifyEvents;
         op = currentParamsSingleValueWidget.thresholdObject[k].op;
         thr1 = parseFloat(currentParamsSingleValueWidget.thresholdObject[k].thr1);
         
         if((op === "intervalOpen")||(op === "intervalClosed")||(op === "intervalLeftOpen")||(op === "intervalRightOpen"))
         {
            thr2 = parseFloat(currentParamsSingleValueWidget.thresholdObject[k].thr2);
         }
         else
         {
            thr2 = 0;
         }
          
          color = currentParamsSingleValueWidget.thresholdObject[k].color;
          desc = currentParamsSingleValueWidget.thresholdObject[k].desc;

          //Aggiunta a tabella
          newTableRow = $('<tr></tr>');
          
          newTableCell = $('<td><input data-param="notifyEvents" type="checkbox" /></td>');
          if(notifyEvents)
          {
             newTableCell.find("input").prop("checked", true);
          }
          newTableCell.find("input").change(updateParamsSingleValueWidgetM);
          newTableRow.append(newTableCell);
          
          newTableCell = $('<td><select data-param="op" class="thrOpSelect"></select></td>');
          newTableCell.find("select").append('<option value="less">&lt;</option>');
          newTableCell.find("select").append('<option value="lessEqual">&le;</option>');
          newTableCell.find("select").append('<option value="greater">&gt;</option>');
          newTableCell.find("select").append('<option value="greaterEqual">&ge;</option>');
          newTableCell.find("select").append('<option value="equal">&equals;</option>');
          newTableCell.find("select").append('<option value="notEqual">&ne;</option>');
          newTableCell.find("select").append('<option value="intervalOpen">a &lt; val &lt; b</option>');
          newTableCell.find("select").append('<option value="intervalClosed">a &le; val &le; b</option>');
          newTableCell.find("select").append('<option value="intervalLeftOpen">a &lt; val &le; b</option>');
          newTableCell.find("select").append('<option value="intervalRightOpen">a &le; val &lt; b</option>');
          newTableRow.append(newTableCell);
          
          newTableCell.find("select").val(currentParamsSingleValueWidget.thresholdObject[k].op);
          
          newTableCell = $('<td><a href="#" class="toBeEdited" data-type="text" data-mode="popup" data-param="thr1">' + thr1 + '</a></td>');
          newTableRow.append(newTableCell);

          newTableCell = $('<td><a href="#" class="toBeEdited" data-type="text" data-mode="popup" data-param="thr2"></a></td>');
          newTableRow.append(newTableCell);

          newTableCell = $('<td><div class="input-group colorPicker" data-param="color"><input type="text" class="form-control"><span class="input-group-addon"><i class="thePicker"></i></span></div></td>');
          newTableRow.append(newTableCell);
          newTableRow.find('div.colorPicker').colorpicker({color: color, format: "rgba"});
          newTableRow.find('div.colorPicker').on('hidePicker', updateParamsSingleValueWidgetM);

          newTableCell = $('<td><a href="#" class="toBeEdited" data-type="text" data-mode="popup" data-param="desc">' + desc + '</a></td>');
          newTableRow.append(newTableCell);

          newTableCell = $('<td><a href="#"><i class="fa fa-close" style="font-size:24px;color:red"></i></a></td>');
          newTableCell.find('i').click(delThrRangeingleValueWidgetM);
          newTableRow.append(newTableCell);
          
          newTableRow.find("a.toBeEdited").editable();
          if((op === "intervalOpen")||(op === "intervalClosed")||(op === "intervalLeftOpen")||(op === "intervalRightOpen"))
          {
              newTableRow.find('a[data-param=thr2]').editable('enable');
              newTableRow.find('a[data-param=thr2]').editable('setValue', thr2);
          }
          else
          {
              newTableRow.find('a[data-param=thr2]').editable('disable');
              newTableRow.find('a[data-param=thr2]').html("");
          }
          
          editWidgetRangeTable.append(newTableRow);
          newTableRow.find("a.toBeEdited").on('save', updateParamsSingleValueWidgetM);
      }
      
      editWidgetRangeTableContainer.show();
      
      $('#editWidgetRangeTable tr td select.thrOpSelect').change(updateParamsSingleValueWidgetM);
   }
   
   $("#editWidgetRangeTable i.fa-plus").click(addThrRangeSingleValueWidgetM);
   
   //Listener per settaggio/desettaggio soglie relativo alla select "Set thresholds"
   $('#alrThrSelM').change(function(){
      if($(this).val() === "no")
      {
         $("label[for=editWidgetShowNotificator]").parents('div.row').hide(); 
         $('#editWidgetShowNotificator').val("0");
         $("#editWidgetRangeTableContainer").hide();
         $("label[for=alrThrSelM]").css("color", "black");
         delete editWidgetConditionsArrayLocal["thrQt"];
      }
      else
      {
         $("label[for=editWidgetShowNotificator]").parents('div.row').show();
         $('#editWidgetShowNotificator').val("1");
         $("#editWidgetRangeTableContainer").show();
         if(countEditWidgetThrConditions() > 0)
         {
            editWidgetConditionsArrayLocal["thrQt"] = true;
            $("label[for=alrThrSelM]").css("color", "black");
         }
         else
         {
            editWidgetConditionsArrayLocal["thrQt"] = false;
            $("label[for=alrThrSelM]").css("color", "red");
         }
      }

      checkEditWidgetConditions();
   });
   
   checkEditWidgetConditions();
   
   /*console.log("Params:");
   console.log(JSON.stringify($('#parametersM').val()));
   console.log("------------------");
   console.log("Diff:");
   console.log(JSON.stringify($('#parametersDiff').val()));*/
}

function updateParamsSingleValueWidgetM(e, params) 
{
   var param = $(this).attr('data-param');
   var rowIndex = $(this).parents("tr").index() - 1;
   var newValue = null;
   var numberPattern = /^-?\d*\.?\d+$/;
   
   if(currentParamsSingleValueWidget === null)
   {
      currentParamsSingleValueWidget = {
         thresholdObject: []
      };
      
      parametersDiff = {
         addedChangedKept: [],
         deleted: []
      };
   }
   
   //Aggiornamento dei parametri
   switch(param)
   {
       case 'notifyEvents':
         newValue = $(this).prop("checked");
         currentParamsSingleValueWidget.thresholdObject[rowIndex].notifyEvents = newValue;
         break;
         
       case 'op':
         newValue = $(this).val();
         currentParamsSingleValueWidget.thresholdObject[rowIndex].op = newValue;
         parametersDiff.addedChangedKept[rowIndex].opNew = newValue;
         parametersDiff.addedChangedKept[rowIndex].changed = true;
         
         if(($(this).val() === "intervalOpen")||($(this).val() === "intervalClosed")||($(this).val() === "intervalLeftOpen")||($(this).val() === "intervalRightOpen"))
         {
            $(this).parents("tr").find('td a[data-param=thr2]').editable('enable');
            $(this).parents("tr").find('td a[data-param=thr2]').editable('setValue', 0);
            
            if(numberPattern.test($(this).parents("tr").find('td a[data-param=thr1]').editable('getValue', true)))
            {  
               //Coerenza fra thr1 e thr2
               if(parseFloat($(this).parents("tr").find('td a[data-param=thr1]').editable('getValue', true)) < parseFloat($(this).parents("tr").find("td a[data-param=thr2]").editable('getValue', true)))
               {
                  editWidgetConditionsArrayLocal["thrVal" + rowIndex] = true;
                  $(this).parents("tr").find("td a[data-param=thr1]").css("color", "#337ab7");
                  $(this).parents("tr").find("td a[data-param=thr2]").css("color", "#337ab7");
               }
               else
               {
                  editWidgetConditionsArrayLocal["thrVal" + rowIndex] = false;
                  $(this).parents("tr").find("td a[data-param=thr1]").css("color", "red");
                  $(this).parents("tr").find("td a[data-param=thr2]").css("color", "red");
               }
            }
            else
            {
               editWidgetConditionsArrayLocal["thrVal" + rowIndex] = false;
               $(this).parents("tr").find("td a[data-param=thr1]").css("color", "red");
            }
         }
         else
         {
            if($('#select-widget-m').val() !== 'widgetServerStatus')
            {
                $(this).parents("tr").find('td a[data-param=thr2]').editable('disable');
                $(this).parents("tr").find('td a[data-param=thr2]').html("");

                if(numberPattern.test($(this).parents("tr").find('td a[data-param=thr1]').editable('getValue', true)))
                {  
                   editWidgetConditionsArrayLocal["thrVal" + rowIndex] = true;
                   $(this).parents("tr").find("td a[data-param=thr1]").css("color", "#337ab7");
                   $(this).parents("tr").find("td a[data-param=thr2]").css("color", "#337ab7");
                }
                else
                {
                   editWidgetConditionsArrayLocal["thrVal" + rowIndex] = false;
                   $(this).parents("tr").find("td a[data-param=thr1]").css("color", "red");
                } 
            }
         }
         
         break;   
      
       case 'thr1':
           if($('#select-widget-m').val() !== 'widgetServerStatus')
           {
               newValue = params.newValue;
           }
           else
           {
               newValue = $(this).val();
           }
           
           currentParamsSingleValueWidget.thresholdObject[rowIndex].thr1 = newValue;
           parametersDiff.addedChangedKept[rowIndex].thr1New = newValue;
           parametersDiff.addedChangedKept[rowIndex].changed = true;
           
           if($('#select-widget-m').val() !== 'widgetServerStatus')
           {
                if(numberPattern.test(newValue))
                {
                    if(($(this).parents("tr").find("td select.thrOpSelect").val() === "intervalOpen")||($(this).parents("tr").find("td select.thrOpSelect").val() === "intervalClosed")||($(this).parents("tr").find("td select.thrOpSelect").val() === "intervalLeftOpen")||($(this).parents("tr").find("td select.thrOpSelect").val() === "intervalRightOpen"))
                    {
                       //Coerenza con thr2 
                       if(numberPattern.test($(this).parents("tr").find("td a[data-param=thr2]").editable('getValue', true)))
                       {
                          if(parseFloat(newValue) < parseFloat($(this).parents("tr").find("td a[data-param=thr2]").editable('getValue', true)))
                          {
                             editWidgetConditionsArrayLocal["thrVal" + rowIndex] = true;
                             $(this).css("color", "#337ab7");
                             $(this).parents("tr").find("td a[data-param=thr2]").css("color", "#337ab7");
                          }
                          else
                          {
                             editWidgetConditionsArrayLocal["thrVal" + rowIndex] = false;
                             $(this).css("color", "red");
                             $(this).parents("tr").find("td a[data-param=thr2]").css("color", "red");
                          }
                       }
                       else
                       {
                          editWidgetConditionsArrayLocal["thrVal" + rowIndex] = false;
                          $(this).parents("tr").find("td a[data-param=thr2]").css("color", "red");
                       }
                    }
                    else
                    {
                       editWidgetConditionsArrayLocal["thrVal" + rowIndex] = true;
                       $(this).css("color", "#337ab7");
                    }
                }
                else
                {
                   editWidgetConditionsArrayLocal["thrVal" + rowIndex] = false;
                   $(this).css("color", "red");
                }
           }
           break;

       case 'thr2':
           newValue = params.newValue;
           currentParamsSingleValueWidget.thresholdObject[rowIndex].thr2 = newValue;
           parametersDiff.addedChangedKept[rowIndex].thr2New = newValue;
           parametersDiff.addedChangedKept[rowIndex].changed = true;
           
           if(numberPattern.test(newValue))
           {
               if(($(this).parents("tr").find("td select.thrOpSelect").val() === "intervalOpen")||($(this).parents("tr").find("td select.thrOpSelect").val() === "intervalClosed")||($(this).parents("tr").find("td select.thrOpSelect").val() === "intervalLeftOpen")||($(this).parents("tr").find("td select.thrOpSelect").val() === "intervalRightOpen"))
               {
                  //Coerenza con thr2 
                  if(numberPattern.test($(this).parents("tr").find("td a[data-param=thr1]").editable('getValue', true)))
                  {
                     if(parseFloat(newValue) > parseFloat($(this).parents("tr").find("td a[data-param=thr1]").editable('getValue', true)))
                     {
                        editWidgetConditionsArrayLocal["thrVal" + rowIndex] = true;
                        $(this).parents("tr").find("td a[data-param=thr1]").css("color", "#337ab7");
                        $(this).css("color", "#337ab7");
                     }
                     else
                     {
                        editWidgetConditionsArrayLocal["thrVal" + rowIndex] = false;
                        $(this).parents("tr").find("td a[data-param=thr1]").css("color", "red");
                        $(this).css("color", "red");
                     }
                  }
                  else
                  {
                     editWidgetConditionsArrayLocal["thrVal" + rowIndex] = false;
                     $(this).parents("tr").find("td a[data-param=thr1]").css("color", "red");
                  }
               }
               else
               {
                  editWidgetConditionsArrayLocal["thrVal" + rowIndex] = true;
                  $(this).css("color", "#337ab7");
               }
           }
           else
           {
              editWidgetConditionsArrayLocal["thrVal" + rowIndex] = false;
              $(this).css("color", "red");
           }
           break;  

       case 'color':
           newValue = $(this).colorpicker('getValue');
           currentParamsSingleValueWidget.thresholdObject[rowIndex].color = newValue;
           break;

       case 'desc':
           newValue = params.newValue;
           currentParamsSingleValueWidget.thresholdObject[rowIndex].desc = newValue;
           parametersDiff.addedChangedKept[rowIndex].descNew = newValue;
           parametersDiff.addedChangedKept[rowIndex].changed = true;
           break;    

       default:
           console.log("Default");
           break;
   }
   
   $('#parametersM').val(JSON.stringify(currentParamsSingleValueWidget));
   $('#parametersDiff').val(JSON.stringify(parametersDiff));
   
   checkEditWidgetConditions();
   
   /*console.log("Params:");
   console.log(JSON.stringify($('#parametersM').val()));
   console.log("------------------");
   console.log("Diff:");
   console.log(JSON.stringify($('#parametersDiff').val()));*/
}

function addThrRangeSingleValueWidgetM()
{
   var newTableRow, newTableCell, newRangeObj, newDiffObj = null;

   //Gestione caso nessuna soglia pregressa: costruiamo l'object literal per i parametri
   if(currentParamsSingleValueWidget === null)
   {
      //Creazione del JSON dei parametri
      currentParamsSingleValueWidget = {
         thresholdObject: []
      };
      
      parametersDiff = {
         addedChangedKept: [],
         deleted: []
      };
   }

   //Aggiungiamo un elemento alle thrSeries in esame, di modo che poi possa accogliere i nuovi valori dal save di XEditor
   newRangeObj = {
       notifyEvents: false,
       op: "less",
       thr1: 0,
       thr2: 0,
       color: "#FFFFFF",
       desc: ""
   };
   
   newDiffObj = {
      op: "less",
      opNew: "less",
      thr1: 0,
      thr1New: 0,
      thr2: 0,
      thr2New: 0,
      desc: "",
      descNew: "",
      added: true,
      changed: false,
      deleted: false
   };
   
   currentParamsSingleValueWidget.thresholdObject.push(newRangeObj);
   parametersDiff.addedChangedKept.push(newDiffObj);
   
   //Aggiunta record alla thrTable dell'asse di appartenenza
   newTableRow = $('<tr></tr>');
   
   newTableCell = $('<td><input data-param="notifyEvents" type="checkbox" /></td>');
   newTableCell.find("input").change(updateParamsSingleValueWidgetM);
   newTableRow.append(newTableCell);
   
   newTableCell = $('<td><select data-param="op" class="thrOpSelect"></select></td>');
   newTableCell.find("select").append('<option value="less">&lt;</option>');
   newTableCell.find("select").append('<option value="lessEqual">&le;</option>');
   newTableCell.find("select").append('<option value="greater">&gt;</option>');
   newTableCell.find("select").append('<option value="greaterEqual">&ge;</option>');
   newTableCell.find("select").append('<option value="equal">&equals;</option>');
   newTableCell.find("select").append('<option value="notEqual">&ne;</option>');
   newTableCell.find("select").append('<option value="intervalOpen">a &lt; val &lt; b</option>');
   newTableCell.find("select").append('<option value="intervalClosed">a &le; val &le; b</option>');
   newTableCell.find("select").append('<option value="intervalLeftOpen">a &lt; val &le; b</option>');
   newTableCell.find("select").append('<option value="intervalRightOpen">a &le; val &lt; b</option>');
   newTableRow.append(newTableCell);
   
   newTableCell = $('<td><a href="#" class="toBeEdited" data-type="text" data-mode="popup" data-param="thr1">0</a></td>');
   newTableCell.find('a').editable();
   newTableRow.append(newTableCell);
   
   newTableCell = $('<td><a href="#" class="toBeEdited" data-type="text" data-mode="popup" data-param="thr2"></td>');
   newTableCell.find('a').editable({
      disabled: true
   });
   newTableRow.append(newTableCell);

   newTableCell = $('<td><div class="input-group colorPicker" data-param="color"><input type="text" class="form-control"><span class="input-group-addon"><i class="thePicker"></i></span></div></td>');
   newTableRow.append(newTableCell);
   newTableRow.find('div.colorPicker').colorpicker({color: "#FFFFFF", format: "rgba"});
   newTableRow.find('div.colorPicker').on('hidePicker', updateParamsSingleValueWidgetM); 

   newTableCell = $('<td><a href="#" class="toBeEdited" data-type="text" data-mode="popup" data-param="desc"></a></td>');
   newTableCell.find('a').editable();
   newTableRow.append(newTableCell);
   newTableCell = $('<td><a><i class="fa fa-close" style="font-size:24px;color:red"></i></a></td>');
   newTableCell.find('i').click(delThrRangeingleValueWidgetM);
   newTableRow.append(newTableCell);
   newTableRow.find('a.toBeEdited').on('save', updateParamsSingleValueWidgetM);
   newTableRow.find('select.thrOpSelect').change(updateParamsSingleValueWidgetM);
   newTableRow.find('select.thrOpSelect').change(function(){
     if(($(this).val() === "intervalOpen")||($(this).val() === "intervalClosed")||($(this).val() === "intervalLeftOpen")||($(this).val() === "intervalRightOpen"))
     {
        newTableRow.find('a[data-param=thr2]').editable('enable');
        newTableRow.find('a[data-param=thr2]').editable('setValue', 0);
     }
     else
     {
        newTableRow.find('a[data-param=thr2]').editable('disable');
        newTableRow.find('a[data-param=thr2]').html("");
     }
   });
   $("#editWidgetRangeTable").append(newTableRow);    
   $('#parametersM').val(JSON.stringify(currentParamsSingleValueWidget));
   $('#parametersDiff').val(JSON.stringify(parametersDiff));
   
   if(countEditWidgetThrConditions() > 0)
   {
      editWidgetConditionsArrayLocal["thrQt"] = true;
      $("label[for=alrThrSelM]").css("color", "black");
   }
   else
   {
      editWidgetConditionsArrayLocal["thrQt"] = false;
      $("label[for=alrThrSelM]").css("color", "red");
   }
   
   checkEditWidgetConditions();
   
   /*console.log("Params:");
   console.log(JSON.stringify($('#parametersM').val()));
   console.log("------------------");
   console.log("Diff:");
   console.log(JSON.stringify($('#parametersDiff').val()));*/
}

function delThrRangeingleValueWidgetM(e)
{
   var delIndex = parseInt($(this).parents('tr').index() - 1);
   var oldIndex = null;
   var tempArray = new Array();
   //Cancellazione della riga dalla tabella 
   $(this).parents('tr').remove();
   delete editWidgetConditionsArrayLocal["thrVal" + delIndex];
   
   for(var key in editWidgetConditionsArrayLocal)
   {
      if(key.includes("thrVal"))
      {
         oldIndex = parseInt(key.substr(6));
         
         if(oldIndex > delIndex)
         {
            tempArray["thrVal" + parseInt(oldIndex - 1)] = editWidgetConditionsArrayLocal[key];
         }
         else
         {
            tempArray[key] = editWidgetConditionsArrayLocal[key];
         }
      }
      else
      {
         tempArray[key] = editWidgetConditionsArrayLocal[key];
      }
   }
   
   editWidgetConditionsArrayLocal = null;
   editWidgetConditionsArrayLocal = tempArray;
   
   //Aggiornamento JSON parametri
   currentParamsSingleValueWidget.thresholdObject.splice(delIndex, 1);
   parametersDiff.addedChangedKept[delIndex].deleted = true;
   parametersDiff.deleted.push(parametersDiff.addedChangedKept[delIndex]);
   parametersDiff.addedChangedKept.splice(delIndex, 1);
   
   $('#parametersM').val(JSON.stringify(currentParamsSingleValueWidget));
   $('#parametersDiff').val(JSON.stringify(parametersDiff));
   
   if(countEditWidgetThrConditions() > 0)
   {
      editWidgetConditionsArrayLocal["thrQt"] = true;
      $("label[for=alrThrSelM]").css("color", "black");
   }
   else
   {
      editWidgetConditionsArrayLocal["thrQt"] = false;
      $("label[for=alrThrSelM]").css("color", "red");
   }
   
   checkEditWidgetConditions();
   
   /*console.log("Params:");
   console.log(JSON.stringify($('#parametersM').val()));
   console.log("------------------");
   console.log("Diff:");
   console.log(JSON.stringify($('#parametersDiff').val()));*/
}

//Usata in tutti i case dell'ascoltatore di cambio tipo widget in aggiunta widget
function removeWidgetProcessGeneralFields(op)
{
    switch(op)
    {
        case "addWidget":
            //Rimozione menu SCHEDULER
            $("#schedulerRow").css("display", "");
            $("label[for='inputSchedulerWidget']").css("display", "none");
            $('#inputSchedulerWidgetDiv').css("display", "none");
            $('#inputSchedulerWidgetGroupDiv').css("display", "none");
            $('#inputSchedulerWidget').css("display", "none");
            //Rimozione menu JOBS AREA
            $('#jobsAreasRow').css("display", "none");
            $("label[for='inputJobsAreasWidget']").css("display", "none");
            $('#inputJobsAreasWidgetDiv').css("display", "none");
            $('#inputJobsAreasWidgetGroupDiv').css("display", "none");
            $('#inputJobsAreasWidget').css("display", "none");
            //Rimozione menu JOB GROUPS
            $('#jobsGroupsRow').css("display", "none");
            $("label[for='inputJobsGroupsWidget']").css("display", "none");
            $('#inputJobsGroupsWidgetDiv').css("display", "none");
            $('#inputJobsGroupsWidgetGroupDiv').css("display", "none");
            $('#inputJobsGroupsWidget').css("display", "none");
            //Rimozione menu JOB NAMES
            $('#jobsNamesRow').css("display", "none");
            $("label[for='inputJobsNamesWidget']").css("display", "none");
            $('#inputJobsNamesWidgetDiv').css("display", "none");
            $('#inputJobsNamesWidgetGroupDiv').css("display", "none");
            $('#inputJobsNamesWidget').css("display", "none");
            break;
            
        case "editWidget":
            //Rimozione menu SCHEDULER
            $("#schedulerRowM").css("display", "");
            $("label[for='inputSchedulerWidgetM']").css("display", "none");
            $('#inputSchedulerWidgetDivM').css("display", "none");
            $('#inputSchedulerWidgetGroupDivM').css("display", "none");
            $('#inputSchedulerWidgetM').css("display", "none");
            //Rimozione menu JOBS AREA
            $('#jobsAreasRowM').css("display", "none");
            $("label[for='inputJobsAreasWidgetM']").css("display", "none");
            $('#inputJobsAreasWidgetDivM').css("display", "none");
            $('#inputJobsAreasWidgetGroupDivM').css("display", "none");
            $('#inputJobsAreasWidgetM').css("display", "none");
            //Rimozione menu JOB GROUPS
            $('#jobsGroupsRowM').css("display", "none");
            $("label[for='inputJobsGroupsWidgetM']").css("display", "none");
            $('#inputJobsGroupsWidgetDivM').css("display", "none");
            $('#inputJobsGroupsWidgetGroupDivM').css("display", "none");
            $('#inputJobsGroupsWidgetM').css("display", "none");
            //Rimozione menu JOB NAMES
            $('#jobsNamesRowM').css("display", "none");
            $("label[for='inputJobsNamesWidgetM']").css("display", "none");
            $('#inputJobsNamesWidgetDivM').css("display", "none");
            $('#inputJobsNamesWidgetGroupDivM').css("display", "none");
            $('#inputJobsNamesWidgetM').css("display", "none");
            break;
    }
}

//Usata solo in showInfoWCkeditors e showInfoWCkeditorsM
function hideCkEditors(event)
{
    $(".cke").hide();
    event.removeListener();
}


//Usata in tutti i case dell'ascoltatore di cambio tipo widget in aggiunta widget
function showInfoWCkeditors(widgetType, editorsArray, seriesObject)
{
    var name, editor, infoNamesJsonFirstAxis, infoNamesJsonSecondAxis = null;
    series = seriesObject;
    
    $("#infoTextareaDiv textarea[id!='widgetInfoEditor']").remove();
    
    //Distruggiamo tutte le istanze precedenti dei CKEDITOR per le info
    for (var key in editorsArray) 
    {
        name = editorsArray[key].attr('id');
        editor = CKEDITOR.instances[name.toString()];
        if(editor)
        {
            editor.destroy(true);
            $('#infoTextareaDiv').find('#' + name).remove();
        }
    }
    
    //Workaround per duplice natura del piechart, sia singoli valori che series
    if(widgetType === 'widgetPieChart')
    {
        if(seriesObject !== null)
        {
            widgetType = "widgetPieChartSeries";
        }
    }
    
    switch (widgetType)
    {
        case "widgetTable": case "widgetBarSeries": case "widgetScatterSeries": case "widgetLineSeries": case "widgetCurvedLineSeries": case "widgetPieChartSeries": case "widgetRadarSeries": case "widgetFirstAid":
            var id = null;
            
            $("#infoMainSelect").empty();
            $("#infoAxisSelect").empty();
            $("#infoFieldSelect").empty();
            $("#infoMainSelect").off();
            $("#infoAxisSelect").off();
            $("#infoFieldSelect").off();
            $('label[for="infoMainSelect"]').hide();
            $('label[for="infoAxisSelect"]').hide();
            $('label[for="infoFieldSelect"]').hide();
            $("#infoMainSelect").hide();
            $("#infoAxisSelect").hide();
            $("#infoFieldSelect").hide();
            $("#widgetInfoCkEditorTitle").empty();
            $("#widgetInfoCkEditorTitleRow").hide();
            
            infoNamesJsonFirstAxis = new Array();
            infoNamesJsonSecondAxis = new Array();
            
            $('label[for="infoMainSelect"]').show();
            $("#infoMainSelect").show();
            
            editorsArray['widgetInfoEditor'] = $('<textarea id ="widgetInfoEditor" name="widgetInfoEditor" rows="1"></textarea>');
            editorsArray['widgetInfoEditor'].addClass("ckeditor");
            editorsArray['widgetInfoEditor'].css("display", "none");
            $('#infoTextareaDiv').append(editorsArray['widgetInfoEditor']);
            CKEDITOR.replace(editorsArray['widgetInfoEditor'].attr('id'), {
                allowedContent: true,
                language: 'en',
                width: '100%',
                height: '100'
            });
            
            CKEDITOR.instances[editorsArray['widgetInfoEditor'].attr('id')].on("instanceReady", hideCkEditors);
            
            //Creazione editor di base per tutti i campi di entrambi gli assi
            for(var i in series.firstAxis.labels)
            {
                //Sostituzione di eventuali spazi con '_' per creare gli id
                id = series.firstAxis.labels[i].toString().replace(/\s/g, '_');
                
                //Aggiunta del nome all'infoNamesJson
                infoNamesJsonFirstAxis.push(id);
                
                //Creazione textarea di base
                editorsArray[id] = $('<textarea id ="' + id + '" name="' + id + '" rows="1"></textarea>');
                editorsArray[id].addClass("ckeditor");
                editorsArray[id].css("display", "none");
                $('#infoTextareaDiv').append(editorsArray[id]);
            }
            
            for(var i in series.secondAxis.labels)
            {
                //Sostituzione di eventuali spazi con '_' per creare gli id
                id = series.secondAxis.labels[i].toString().replace(/\s/g, '_');
                
                //Aggiunta del nome all'infoNamesJson
                infoNamesJsonSecondAxis.push(id);
                
                //Creazione textarea di base
                editorsArray[id] = $('<textarea id ="' + id + '" name="' + id + '" rows="1"></textarea>');
                editorsArray[id].addClass("ckeditor");   
                editorsArray[id].css("display", "none");
                $('#infoTextareaDiv').append(editorsArray[id]);
            }
            
            //Storage dell'infoNamesJson nel campo hidden omonimo nel form
            $("#infoNamesJsonFirstAxis").val(JSON.stringify(infoNamesJsonFirstAxis));
            $("#infoNamesJsonSecondAxis").val(JSON.stringify(infoNamesJsonSecondAxis));
            
            //Popolamento della select del target
            $("#infoMainSelect").append('<option value="widgetInfo">Widget informations</option>');
            $("#infoMainSelect").append('<option value="fieldsInfo">Data informations</option>');
            $("#infoMainSelect").val(-1);
            
            $("#infoMainSelect").off("change");
            $("#infoAxisSelect").off("change");
            $("#infoFieldSelect").off("change");
            $("#infoMainSelect").change(widgetInfoMainSelectHandler);    
            $("#infoAxisSelect").change(widgetInfoAxisHandler);
            $("#infoFieldSelect").change(widgetInfoFieldHandler);
            
            editors = editorsArray;
            break;
        
        default:
            $("#infoMainSelect").off();
            $("#infoAxisSelect").off();
            $("#infoFieldSelect").off();
            $("#infoMainSelect").empty();
            $("#infoAxisSelect").empty();
            $('label[for="infoMainSelect"]').hide();
            $('label[for="infoAxisSelect"]').hide();
            $('label[for="infoFieldSelect"]').hide();
            $("#infoMainSelect").hide();
            $("#infoAxisSelect").hide();
            $("#infoFieldSelect").hide();
            
            //L'editor delle info widget viene creato per tutti i tipi di widget
            editorsArray['widgetInfoEditor'] = $('<textarea id ="widgetInfoEditor" name="widgetInfoEditor" rows="1"></textarea>');
            editorsArray['widgetInfoEditor'].addClass("ckeditor");
            editorsArray['widgetInfoEditor'].css("display", "none");
            $('#infoTextareaDiv').append(editorsArray['widgetInfoEditor']);
            CKEDITOR.replace(editorsArray['widgetInfoEditor'].attr('id'), {
                allowedContent: true,
                language: 'en',
                width: '100%',
                height: '100'
            });
            break;
    }
}

//Usata in tutti i case dell'ascoltatore di tipo widget in modifica widget
function showInfoWCkeditorsM(widgetType, editorsArray, seriesObject, infoJsonObject, infoWidget)
{
    var name, editor, infoNamesJsonFirstAxis, infoNamesJsonSecondAxis = null;
    //var series = seriesObject;
    series = seriesObject;
    editorsM = editorsArray;
    var infoJson = infoJsonObject;

    console.log("Avviata");

    infoNamesJsonFirstAxis = new Array();
    infoNamesJsonSecondAxis = new Array();
    
    //Distruggiamo tutte le istanze precedenti dei CKEDITOR per le info
    for(var key in editorsM) 
    {
        name = editorsM[key].attr('id');
        editor = CKEDITOR.instances[name.toString()];
        
        console.log("Editor in distruzione: " + name);
        
        if(editor)
        {
            editor.destroy(true);
            $('#infoTextareaDivM').find('#' + name).remove();
        }
    }
    
    //Workaround per duplice natura del piechart, sia singoli valori che series
    if(widgetType === 'widgetPieChart')
    {
        if(seriesObject !== null)
        {
            widgetType = "widgetPieChartSeries";
        }
    }
    
    switch(widgetType)
    {
      case "widgetFirstAid":
         var id = null;
            
         $('label[for="infoMainSelectM"]').show();
         $("#infoMainSelectM").show();

         editorsM['widgetInfoEditorM'] = $('<textarea id ="widgetInfoEditorM" name="widgetInfoEditorM" rows="1"></textarea>');
         editorsM['widgetInfoEditorM'].addClass("ckeditor");
         editorsM['widgetInfoEditorM'].css("display", "none");
         editorsM['widgetInfoEditorM'].val(infoWidget);
         $('#infoTextareaDivM').append(editorsM['widgetInfoEditorM']);
         CKEDITOR.replace(editorsM['widgetInfoEditorM'].attr('id'), {
             allowedContent: true,
             language: 'en',
             width: '100%',
             height: '100'
         });

         CKEDITOR.instances[editorsM['widgetInfoEditorM'].attr('id')].on("instanceReady", hideCkEditors);

         //Creazione editor di base per tutti i campi di entrambi gli assi
         for(var i in series.firstAxis.labels)
         {
             //Sostituzione di eventuali spazi con '_' per creare gli id
             id = series.firstAxis.labels[i].toString().replace(/\s/g, '_');

             //Aggiunta del nome all'infoNamesJson
             infoNamesJsonFirstAxis.push(id);

             //Creazione textarea di base
             editorsM[id] = $('<textarea id ="' + id + '" name="' + id + '" rows="1"></textarea>');
             editorsM[id].addClass("ckeditor");
             editorsM[id].val(infoJson.firstAxis[id]);
             $('#infoTextareaDivM').append(editorsM[id]);
             editorsM[id].css("display", "none");
         }

         for(var i in series.secondAxis.labels)
         {
             //Sostituzione di eventuali spazi con '_' per creare gli id
             id = series.secondAxis.labels[i].toString().replace(/\s/g, '_');

             //Aggiunta del nome all'infoNamesJson
             infoNamesJsonSecondAxis.push(id);

             //Creazione textarea di base
             editorsM[id] = $('<textarea id ="' + id + '" name="' + id + '" rows="1"></textarea>');
             editorsM[id].addClass("ckeditor"); 
             editorsM[id].val(infoJson.secondAxis[id]);
             $('#infoTextareaDivM').append(editorsM[id]);
             editorsM[id].css("display", "none");
         }

         //Storage dell'infoNamesJson nel campo hidden omonimo nel form
         $("#infoNamesJsonFirstAxisM").val(JSON.stringify(infoNamesJsonFirstAxis));
         $("#infoNamesJsonSecondAxisM").val(JSON.stringify(infoNamesJsonSecondAxis));

         //Popolamento della select del target
         $("#infoMainSelectM").empty();
         $("#infoMainSelectM").append('<option value="widgetInfo">Widget informations</option>');
         $("#infoMainSelectM").append('<option value="fieldsInfo">Data informations</option>');
         $("#infoMainSelectM").val(-1);
         $('label[for="infoAxisSelectM"').hide();
         $("#infoAxisSelectM").parent().hide();
         $('label[for="infoFieldSelectM"').hide();
         $("#infoFieldSelectM").parent().hide();

         $("#infoMainSelectM").change(widgetInfoMainSelectHandlerM);    
         $("#infoAxisSelectM").change(widgetInfoAxisHandlerM);
         $("#infoFieldSelectM").change(widgetInfoFieldHandlerM);
         break;
      
         case "widgetTable":
            var id = null;
            
            $('label[for="infoMainSelectM"]').show();
            $("#infoMainSelectM").show();
            
            editorsM['widgetInfoEditorM'] = $('<textarea id ="widgetInfoEditorM" name="widgetInfoEditorM" rows="1"></textarea>');
            editorsM['widgetInfoEditorM'].addClass("ckeditor");
            editorsM['widgetInfoEditorM'].css("display", "none");
            editorsM['widgetInfoEditorM'].val(infoWidget);
            $('#infoTextareaDivM').append(editorsM['widgetInfoEditorM']);
            CKEDITOR.replace(editorsM['widgetInfoEditorM'].attr('id'), {
                allowedContent: true,
                language: 'en',
                width: '100%',
                height: '100'
            });
            
            CKEDITOR.instances[editorsM['widgetInfoEditorM'].attr('id')].on("instanceReady", hideCkEditors);
            
            //Creazione editor di base per tutti i campi di entrambi gli assi
            for(var i in series.firstAxis.labels)
            {
                //Sostituzione di eventuali spazi con '_' per creare gli id
                id = series.firstAxis.labels[i].toString().replace(/\s/g, '_');
                
                //Aggiunta del nome all'infoNamesJson
                infoNamesJsonFirstAxis.push(id);
                
                //Creazione textarea di base
                editorsM[id] = $('<textarea id ="' + id + '" name="' + id + '" rows="1"></textarea>');
                editorsM[id].addClass("ckeditor");
                editorsM[id].val(infoJson.firstAxis[id]);
                $('#infoTextareaDivM').append(editorsM[id]);
                editorsM[id].css("display", "none");
            }
            
            for(var i in series.secondAxis.labels)
            {
                //Sostituzione di eventuali spazi con '_' per creare gli id
                id = series.secondAxis.labels[i].toString().replace(/\s/g, '_');
                
                //Aggiunta del nome all'infoNamesJson
                infoNamesJsonSecondAxis.push(id);
                
                //Creazione textarea di base
                editorsM[id] = $('<textarea id ="' + id + '" name="' + id + '" rows="1"></textarea>');
                editorsM[id].addClass("ckeditor"); 
                editorsM[id].val(infoJson.secondAxis[id]);
                $('#infoTextareaDivM').append(editorsM[id]);
                editorsM[id].css("display", "none");
            }
            
            //Storage dell'infoNamesJson nel campo hidden omonimo nel form
            $("#infoNamesJsonFirstAxisM").val(JSON.stringify(infoNamesJsonFirstAxis));
            $("#infoNamesJsonSecondAxisM").val(JSON.stringify(infoNamesJsonSecondAxis));
            
            //Popolamento della select del target
            $("#infoMainSelectM").empty();
            $("#infoMainSelectM").append('<option value="widgetInfo">Widget informations</option>');
            $("#infoMainSelectM").append('<option value="fieldsInfo">Data informations</option>');
            $("#infoMainSelectM").val(-1);
            
            $("#infoMainSelectM").change(widgetInfoMainSelectHandlerM);    
            $("#infoAxisSelectM").change(widgetInfoAxisHandlerM);
            $("#infoFieldSelectM").change(widgetInfoFieldHandlerM);
            break;
            
        case "widgetBarSeries":
            var id = null;
            
            $('label[for="infoMainSelectM"]').show();
            $("#infoMainSelectM").show();
            
            editorsM['widgetInfoEditorM'] = $('<textarea id ="widgetInfoEditorM" name="widgetInfoEditorM" rows="1"></textarea>');
            editorsM['widgetInfoEditorM'].addClass("ckeditor");
            editorsM['widgetInfoEditorM'].css("display", "none");
            editorsM['widgetInfoEditorM'].val(infoWidget);
            $('#infoTextareaDivM').append(editorsM['widgetInfoEditorM']);
            CKEDITOR.replace(editorsM['widgetInfoEditorM'].attr('id'), {
                allowedContent: true,
                language: 'en',
                width: '100%',
                height: '100'
            });
            
            CKEDITOR.instances[editorsM['widgetInfoEditorM'].attr('id')].on("instanceReady", hideCkEditors);
            
            //Creazione editor di base per tutti i campi di entrambi gli assi
            for(var i in series.firstAxis.labels)
            {
                //Sostituzione di eventuali spazi con '_' per creare gli id
                id = series.firstAxis.labels[i].toString().replace(/\s/g, '_');
                
                //Aggiunta del nome all'infoNamesJson
                infoNamesJsonFirstAxis.push(id);
                
                //Creazione textarea di base
                editorsM[id] = $('<textarea id ="' + id + '" name="' + id + '" rows="1"></textarea>');
                editorsM[id].addClass("ckeditor");
                editorsM[id].val(infoJson.firstAxis[id]);
                $('#infoTextareaDivM').append(editorsM[id]);
                editorsM[id].css("display", "none");
            }
            
            for(var i in series.secondAxis.labels)
            {
                //Sostituzione di eventuali spazi con '_' per creare gli id
                id = series.secondAxis.labels[i].toString().replace(/\s/g, '_');
                
                //Aggiunta del nome all'infoNamesJson
                infoNamesJsonSecondAxis.push(id);
                
                //Creazione textarea di base
                editorsM[id] = $('<textarea id ="' + id + '" name="' + id + '" rows="1"></textarea>');
                editorsM[id].addClass("ckeditor"); 
                editorsM[id].val(infoJson.secondAxis[id]);
                $('#infoTextareaDivM').append(editorsM[id]);
                editorsM[id].css("display", "none");
            }
            
            //Storage dell'infoNamesJson nel campo hidden omonimo nel form
            $("#infoNamesJsonFirstAxisM").val(JSON.stringify(infoNamesJsonFirstAxis));
            $("#infoNamesJsonSecondAxisM").val(JSON.stringify(infoNamesJsonSecondAxis));
            
            //Popolamento della select del target
            $("#infoMainSelectM").empty();
            $("#infoMainSelectM").append('<option value="widgetInfo">Widget informations</option>');
            $("#infoMainSelectM").append('<option value="fieldsInfo">Data informations</option>');
            $("#infoMainSelectM").val(-1);
            
            $("#infoMainSelectM").change(widgetInfoMainSelectHandlerM);    
            $("#infoAxisSelectM").change(widgetInfoAxisHandlerM);
            $("#infoFieldSelectM").change(widgetInfoFieldHandlerM);
            break;
            
        case "widgetRadarSeries":
            var id = null;
            
            $('label[for="infoMainSelectM"]').show();
            $("#infoMainSelectM").show();
            
            editorsM['widgetInfoEditorM'] = $('<textarea id ="widgetInfoEditorM" name="widgetInfoEditorM" rows="1"></textarea>');
            editorsM['widgetInfoEditorM'].addClass("ckeditor");
            editorsM['widgetInfoEditorM'].css("display", "none");
            editorsM['widgetInfoEditorM'].val(infoWidget);
            $('#infoTextareaDivM').append(editorsM['widgetInfoEditorM']);
            CKEDITOR.replace(editorsM['widgetInfoEditorM'].attr('id'), {
                allowedContent: true,
                language: 'en',
                width: '100%',
                height: '100'
            });
            
            CKEDITOR.instances[editorsM['widgetInfoEditorM'].attr('id')].on("instanceReady", hideCkEditors);
            
            //Creazione editor di base per tutti i campi di entrambi gli assi
            for(var i in series.firstAxis.labels)
            {
                //Sostituzione di eventuali spazi con '_' per creare gli id
                id = series.firstAxis.labels[i].toString().replace(/\s/g, '_');
                
                //Aggiunta del nome all'infoNamesJson
                infoNamesJsonFirstAxis.push(id);
                
                //Creazione textarea di base
                editorsM[id] = $('<textarea id ="' + id + '" name="' + id + '" rows="1"></textarea>');
                editorsM[id].addClass("ckeditor");
                editorsM[id].val(infoJson.firstAxis[id]);
                $('#infoTextareaDivM').append(editorsM[id]);
                editorsM[id].css("display", "none");
            }
            
            for(var i in series.secondAxis.labels)
            {
                //Sostituzione di eventuali spazi con '_' per creare gli id
                id = series.secondAxis.labels[i].toString().replace(/\s/g, '_');
                
                //Aggiunta del nome all'infoNamesJson
                infoNamesJsonSecondAxis.push(id);
                
                //Creazione textarea di base
                editorsM[id] = $('<textarea id ="' + id + '" name="' + id + '" rows="1"></textarea>');
                editorsM[id].addClass("ckeditor"); 
                editorsM[id].val(infoJson.secondAxis[id]);
                $('#infoTextareaDivM').append(editorsM[id]);
                editorsM[id].css("display", "none");
            }
            
            //Storage dell'infoNamesJson nel campo hidden omonimo nel form
            $("#infoNamesJsonFirstAxisM").val(JSON.stringify(infoNamesJsonFirstAxis));
            $("#infoNamesJsonSecondAxisM").val(JSON.stringify(infoNamesJsonSecondAxis));
            
            //Popolamento della select del target
            $("#infoMainSelectM").empty();
            $("#infoMainSelectM").append('<option value="widgetInfo">Widget informations</option>');
            $("#infoMainSelectM").append('<option value="fieldsInfo">Data informations</option>');
            $("#infoMainSelectM").val(-1);
            
            $("#infoMainSelectM").change(widgetInfoMainSelectHandlerM);    
            $("#infoAxisSelectM").change(widgetInfoAxisHandlerM);
            $("#infoFieldSelectM").change(widgetInfoFieldHandlerM);
            break;
            
        case "widgetScatterSeries":
            var id = null;
            
            $('label[for="infoMainSelectM"]').show();
            $("#infoMainSelectM").show();
            
            editorsM['widgetInfoEditorM'] = $('<textarea id ="widgetInfoEditorM" name="widgetInfoEditorM" rows="1"></textarea>');
            editorsM['widgetInfoEditorM'].addClass("ckeditor");
            editorsM['widgetInfoEditorM'].css("display", "none");
            editorsM['widgetInfoEditorM'].val(infoWidget);
            $('#infoTextareaDivM').append(editorsM['widgetInfoEditorM']);
            CKEDITOR.replace(editorsM['widgetInfoEditorM'].attr('id'), {
                allowedContent: true,
                language: 'en',
                width: '100%',
                height: '100'
            });
            
            CKEDITOR.instances[editorsM['widgetInfoEditorM'].attr('id')].on("instanceReady", hideCkEditors);
            
            //Creazione editor di base per tutti i campi di entrambi gli assi
            for(var i in series.firstAxis.labels)
            {
                //Sostituzione di eventuali spazi con '_' per creare gli id
                id = series.firstAxis.labels[i].toString().replace(/\s/g, '_');
                
                //Aggiunta del nome all'infoNamesJson
                infoNamesJsonFirstAxis.push(id);
                
                //Creazione textarea di base
                editorsM[id] = $('<textarea id ="' + id + '" name="' + id + '" rows="1"></textarea>');
                editorsM[id].addClass("ckeditor");
                editorsM[id].val(infoJson.firstAxis[id]);
                $('#infoTextareaDivM').append(editorsM[id]);
                editorsM[id].css("display", "none");
            }
            
            for(var i in series.secondAxis.labels)
            {
                //Sostituzione di eventuali spazi con '_' per creare gli id
                id = series.secondAxis.labels[i].toString().replace(/\s/g, '_');
                
                //Aggiunta del nome all'infoNamesJson
                infoNamesJsonSecondAxis.push(id);
                
                //Creazione textarea di base
                editorsM[id] = $('<textarea id ="' + id + '" name="' + id + '" rows="1"></textarea>');
                editorsM[id].addClass("ckeditor"); 
                editorsM[id].val(infoJson.secondAxis[id]);
                $('#infoTextareaDivM').append(editorsM[id]);
                editorsM[id].css("display", "none");
            }
            
            //Storage dell'infoNamesJson nel campo hidden omonimo nel form
            $("#infoNamesJsonFirstAxisM").val(JSON.stringify(infoNamesJsonFirstAxis));
            $("#infoNamesJsonSecondAxisM").val(JSON.stringify(infoNamesJsonSecondAxis));
            
            //Popolamento della select del target
            $("#infoMainSelectM").empty();
            $("#infoMainSelectM").append('<option value="widgetInfo">Widget informations</option>');
            $("#infoMainSelectM").append('<option value="fieldsInfo">Data informations</option>');
            $("#infoMainSelectM").val(-1);
            
            $("#infoMainSelectM").change(widgetInfoMainSelectHandlerM);    
            $("#infoAxisSelectM").change(widgetInfoAxisHandlerM);
            $("#infoFieldSelectM").change(widgetInfoFieldHandlerM);
            break;
        
        case "widgetLineSeries": case "widgetCurvedLineSeries": case "widgetPieChartSeries":
            var id = null;
            
            $('label[for="infoMainSelectM"]').show();
            $("#infoMainSelectM").show();
            
            editorsM['widgetInfoEditorM'] = $('<textarea id ="widgetInfoEditorM" name="widgetInfoEditorM" rows="1"></textarea>');
            editorsM['widgetInfoEditorM'].addClass("ckeditor");
            editorsM['widgetInfoEditorM'].css("display", "none");
            editorsM['widgetInfoEditorM'].val(infoWidget);
            $('#infoTextareaDivM').append(editorsM['widgetInfoEditorM']);
            CKEDITOR.replace(editorsM['widgetInfoEditorM'].attr('id'), {
                allowedContent: true,
                language: 'en',
                width: '100%',
                height: '100'
            });
            
            CKEDITOR.instances[editorsM['widgetInfoEditorM'].attr('id')].on("instanceReady", hideCkEditors);
            
            //Creazione editor di base per tutti i campi di entrambi gli assi
            for(var i in series.firstAxis.labels)
            {
                //Sostituzione di eventuali spazi con '_' per creare gli id
                id = series.firstAxis.labels[i].toString().replace(/\s/g, '_');
                
                //Aggiunta del nome all'infoNamesJson
                infoNamesJsonFirstAxis.push(id);
                
                //Creazione textarea di base
                editorsM[id] = $('<textarea id ="' + id + '" name="' + id + '" rows="1"></textarea>');
                editorsM[id].addClass("ckeditor");
                editorsM[id].val(infoJson.firstAxis[id]);
                $('#infoTextareaDivM').append(editorsM[id]);
                editorsM[id].css("display", "none");
            }
            
            for(var i in series.secondAxis.labels)
            {
                //Sostituzione di eventuali spazi con '_' per creare gli id
                id = series.secondAxis.labels[i].toString().replace(/\s/g, '_');
                
                //Aggiunta del nome all'infoNamesJson
                infoNamesJsonSecondAxis.push(id);
                
                //Creazione textarea di base
                editorsM[id] = $('<textarea id ="' + id + '" name="' + id + '" rows="1"></textarea>');
                editorsM[id].addClass("ckeditor"); 
                editorsM[id].val(infoJson.secondAxis[id]);
                $('#infoTextareaDivM').append(editorsM[id]);
                editorsM[id].css("display", "none");
            }
            
            //Storage dell'infoNamesJson nel campo hidden omonimo nel form
            $("#infoNamesJsonFirstAxisM").val(JSON.stringify(infoNamesJsonFirstAxis));
            $("#infoNamesJsonSecondAxisM").val(JSON.stringify(infoNamesJsonSecondAxis));
            
            //Popolamento della select del target
            $("#infoMainSelectM").empty();
            $("#infoMainSelectM").append('<option value="widgetInfo">Widget informations</option>');
            $("#infoMainSelectM").append('<option value="fieldsInfo">Data informations</option>');
            $("#infoMainSelectM").val(-1);
            
            $("#infoMainSelectM").change(widgetInfoMainSelectHandlerM);    
            $("#infoAxisSelectM").change(widgetInfoAxisHandlerM);
            $("#infoFieldSelectM").change(widgetInfoFieldHandlerM);
            break; 
        
        default:
            console.log("Entrato nel default");
            $("#infoMainSelectM").off();
            $("#infoAxisSelectM").off();
            $("#infoFieldSelectM").off();
            $("#infoMainSelectM").empty();
            $("#infoAxisSelectM").empty();
            $('label[for="infoMainSelectM"]').hide();
            $('label[for="infoAxisSelectM"]').hide();
            $('label[for="infoFieldSelectM"]').hide();
            $("#infoMainSelectM").hide();
            $("#infoAxisSelectM").hide();
            $("#infoFieldSelectM").hide();
            
            //L'editor delle info widget viene creato per tutti i tipi di widget
            editorsM['widgetInfoEditorM'] = $('<textarea id ="widgetInfoEditorM" name="widgetInfoEditorM" rows="1"></textarea>');
            editorsM['widgetInfoEditorM'].addClass("ckeditor");
            editorsM['widgetInfoEditorM'].css("display", "none");
            editorsM['widgetInfoEditorM'].val(infoWidget);
            $('#infoTextareaDivM').append(editorsM['widgetInfoEditorM']);
            CKEDITOR.replace(editorsM['widgetInfoEditorM'].attr('id'), {
                allowedContent: true,
                language: 'en',
                width: '100%',
                height: '100'
            });
            break;
    }
}

//Usata solo in showInfoWCkeditors
function widgetInfoMainSelectHandler()
{
    switch($(this).val())
    {
        case "widgetInfo":
            $(".cke").hide();
            $("#infoAxisSelect").empty();
            $('label[for="infoAxisSelect"]').hide();
            $('label[for="infoFieldSelect"]').hide();
            $("#infoAxisSelect").hide();
            $("#infoFieldSelect").hide();
            $("#widgetInfoCkEditorTitle").html("Rich text editor for <b>widget general info</b>");
            $("#widgetInfoCkEditorTitleRow").show();
            $("#widgetInfoCkEditorTitle").show();
            $("#cke_widgetInfoEditor").show();
            break;
            
        case "fieldsInfo":
            $(".cke").hide();
            $("#widgetInfoCkEditorTitle").html("");
            $("#widgetInfoCkEditorTitleRow").hide();
            $("#widgetInfoCkEditorTitle").hide();
            //Popolamento della select degli assi
            $("#infoAxisSelect").append('<option value="' + series.firstAxis.desc + '">' + series.firstAxis.desc + '</option>');
            
            if($("#addWidgetFirstAidMode").length === 0)
            {
               $("#infoAxisSelect").append('<option value="' + series.secondAxis.desc + '">' + series.secondAxis.desc + '</option>');
            }
            else
            {
               if($("#addWidgetFirstAidMode").val() !== "singleSummary")
               {
                  $("#infoAxisSelect").append('<option value="' + series.secondAxis.desc + '">' + series.secondAxis.desc + '</option>');
               }
            }
            
            $('label[for="infoAxisSelect"]').show();
            $("#infoAxisSelect").show();
            $("#infoAxisSelect").val(-1);
            break;
            
        default:
            $('label[for="infoAxisSelect"]').hide();
            $('label[for="infoFieldSelect"]').hide();
            $("#infoAxisSelect").hide();
            $("#infoFieldSelect").hide();
            break;
    }
}

//Usata solo in showInfoWCkeditorsM
function widgetInfoMainSelectHandlerM()
{
    switch($(this).val())
    {
        case "widgetInfo":
            $(".cke").hide();
            $("#infoAxisSelectM").empty();
            $('label[for="infoAxisSelectM"]').hide();
            $('label[for="infoFieldSelectM"]').hide();
            $("#infoAxisSelectM").hide();
            $("#infoFieldSelectM").hide();
            $("#widgetInfoCkEditorTitleM").html("Rich text editor for <b>widget general info</b>");
            $("#widgetInfoCkEditorTitleRowM").show();
            $("#widgetInfoCkEditorTitleM").show();
            $("#cke_widgetInfoEditorM").show();
            break;
            
        case "fieldsInfo":
            $("#cke_widgetInfoEditorM").hide();
            $("#widgetInfoCkEditorTitleM").html("");
            $("#widgetInfoCkEditorTitleRowM").hide();
            $("#widgetInfoCkEditorTitleM").hide();
            //Popolamento della select degli assi
            $("#infoAxisSelectM").empty();
            $("#infoAxisSelectM").append('<option value="' + series.firstAxis.desc + '">' + series.firstAxis.desc + '</option>');
            
            if($("#editWidgetFirstAidMode").length === 0)
            {
               $("#infoAxisSelectM").append('<option value="' + series.secondAxis.desc + '">' + series.secondAxis.desc + '</option>');
            }
            else
            {
               if($("#editWidgetFirstAidMode").val() !== "singleSummary")
               {
                  $("#infoAxisSelectM").append('<option value="' + series.secondAxis.desc + '">' + series.secondAxis.desc + '</option>');
               }
            }
            $('label[for="infoAxisSelectM"]').show();
            $("#infoAxisSelectM").parent().show();
            $("#infoAxisSelectM").show();
            $("#infoAxisSelectM").val(-1);
            break;
            
        default:
            $('label[for="infoAxisSelectM"]').hide();
            $('label[for="infoFieldSelectM"]').hide();
            $("#infoAxisSelectM").hide();
            $("#infoFieldSelectM").hide();
            break;
    }
}

//Usata solo in showInfoWCkeditors
function widgetInfoAxisHandler()
{
    $("#infoFieldSelect").empty();
    $("#widgetInfoCkEditorTitle").html("");
    $("#widgetInfoCkEditorTitleRow").hide();
    $("#widgetInfoCkEditorTitle").hide();
    $('label[for="infoFieldSelect"]').show();
    $("#infoFieldSelect").parent().show();
    $("#infoFieldSelect").show();
    
    //Vengono nascosti i ckeditors pregressi
    $(".cke").hide();
    
    //Caricamento campi del primo asse
    if($("#infoAxisSelect").val() === series.firstAxis.desc)
    {
        for(var i in series.firstAxis.labels)
        {
            //Aggiunta della voce alla select dei campi
            $("#infoFieldSelect").append('<option value="' + series.firstAxis.labels[i] + '">' + series.firstAxis.labels[i] + '</option>');
        }
    }//Caricamento campi del secondo asse
    else if($("#infoAxisSelect").val() === series.secondAxis.desc)
    {
        for(var i in series.secondAxis.labels)
        {
            $("#infoFieldSelect").append('<option value="' + series.secondAxis.labels[i] + '">' + series.secondAxis.labels[i] + '</option>');
        }
    }
    $("#infoFieldSelect").val(-1);
}

//Usata solo in showInfoWCkeditorsM
function widgetInfoAxisHandlerM()
{
    $("#infoFieldSelectM").empty();
    $("#widgetInfoCkEditorTitleM").html("");
    $("#widgetInfoCkEditorTitleRowM").hide();
    $("#widgetInfoCkEditorTitleM").hide();
    $('label[for="infoFieldSelectM"]').show();
    $("#infoFieldSelectM").parent().show();
    $("#infoFieldSelectM").show();
    
    //Vengono nascosti i ckeditors pregressi
    $(".cke").hide();
    
    //Caricamento campi del primo asse
    if($("#infoAxisSelectM").val() === series.firstAxis.desc)
    {
        for(var i in series.firstAxis.labels)
        {
            //Aggiunta della voce alla select dei campi
            $("#infoFieldSelectM").append('<option value="' + series.firstAxis.labels[i] + '">' + series.firstAxis.labels[i] + '</option>');
        }
    }//Caricamento campi del secondo asse
    else if($("#infoAxisSelectM").val() === series.secondAxis.desc)
    {
        for(var i in series.secondAxis.labels)
        {
            $("#infoFieldSelectM").append('<option value="' + series.secondAxis.labels[i] + '">' + series.secondAxis.labels[i] + '</option>');
        }
    }
    $("#infoFieldSelectM").val(-1);
}

//Usata solo in showInfoWCkeditors
function widgetInfoFieldHandler()
{
    var editorName, editorInstance = null;
    var id = $(this).val().replace(/\s/g, '_');
    
    $("#widgetInfoCkEditorTitle").html("Rich text editor for details about field <b>" + $(this).val() + "</b>");
    $("#widgetInfoCkEditorTitleRow").show();
    $("#widgetInfoCkEditorTitle").show();
    
    for (var key in editors) 
    {
        editorName = editors[key].attr('id');
        editorInstance = CKEDITOR.instances[editorName.toString()];
        if(id === editorName)
        {
            if(editorInstance)
            {
                $("#cke_" + editorName).show();
            }
            else
            {
                CKEDITOR.replace(id, {
                    allowedContent: true,
                    language: 'en',
                    width: '100%',
                    height: '100'
                });
            }
        }
        else
        {
            if(editorInstance)
            {
                $("#cke_" + editorName).hide();
            }
        }
    }
}

//Usata solo in showInfoWCkeditorsM
function widgetInfoFieldHandlerM()
{
    var editorName, editorInstance = null;
    var id = $(this).val().replace(/\s/g, '_');
    
    $("#widgetInfoCkEditorTitleM").html("Rich text editor for details about field <b>" + $(this).val() + "</b>");
    $("#widgetInfoCkEditorTitleRowM").show();
    $("#widgetInfoCkEditorTitleM").show();
    
    for(var key in editorsM) 
    {
        editorName = editorsM[key].attr('id');
        
        editorInstance = CKEDITOR.instances[editorName.toString()];
        
        if(id === editorName)
        {
            if(editorInstance)
            {
                $("#cke_" + editorName).show();
            }
            else
            {
                CKEDITOR.replace(id, {
                    allowedContent: true,
                    language: 'en',
                    width: '100%',
                    height: '100'
                });
            }
        }
        else
        {
            if(editorInstance)
            {
                $("#cke_" + editorName).hide();
            }
        }
    }
}

//Funzioni specifiche per gli widget basati su serie ma comuni fra tali widget

//Globals per tali funzioni
var currentParams, thrSimpleTables, valuePerc, descriptions, thrTables1, thrTables2, thrTable, series = null;

function setGlobals(currentParamsAtt, thrTables1Att, thrTables2Att, seriesAtt, widgetTypeAtt)
{
    currentParams = currentParamsAtt;
    thrTables1 = thrTables1Att;
    thrTables2 = thrTables2Att;
    series = seriesAtt;
    widgetType = widgetTypeAtt;
}

function setGlobalsRadar(currentParamsAtt, thrTableAtt, seriesAtt, widgetTypeAtt)
{
    currentParams = currentParamsAtt;
    thrTable = thrTableAtt;
    series = seriesAtt;
    widgetType = widgetTypeAtt;
}

function setSimpleGlobals(currentParamsAtt, thrSimpleTablesAtt, valuePercAtt, descriptionsAtt)
{
    currentParams = currentParamsAtt;
    thrSimpleTables = thrSimpleTablesAtt;
    valuePerc = valuePercAtt;
    descriptions = descriptionsAtt;
}

function updateParamsSimple(e, params) {
    var field = parseInt($(this).attr('data-field'));
    var series = parseInt($(this).attr('data-series'));
    var param = $(this).attr('data-param');
    var newValue = null;
    //Aggiornamento dei parametri
    switch(param)
    {
        case 'min':
            newValue = params.newValue;
            currentParams.thresholdObject.fields[field].thrSeries[series].min = newValue;
            break;

        case 'max':
            newValue = params.newValue;
            currentParams.thresholdObject.fields[field].thrSeries[series].max = newValue;
            break;

        case 'color':
            newValue = $(this).colorpicker('getValue');
            currentParams.thresholdObject.fields[field].thrSeries[series].color = newValue;
            break;
            
        case 'desc':
            newValue = params.newValue;
            currentParams.thresholdObject.fields[field].thrSeries[series].desc = newValue;
            break;    

        default:
            console.log("Default");
            break;
     }
    $('#parameters').val(JSON.stringify(currentParams));
}

function updateParamsSimpleM(e, params) {
    var field = parseInt($(this).attr('data-field'));
    var series = parseInt($(this).attr('data-series'));
    var param = $(this).attr('data-param');
    var newValue = null;
    //Aggiornamento dei parametri
    switch(param)
    {
        case 'min':
            newValue = params.newValue;
            currentParams.thresholdObject.fields[field].thrSeries[series].min = newValue;
            break;

        case 'max':
            newValue = params.newValue;
            currentParams.thresholdObject.fields[field].thrSeries[series].max = newValue;
            break;

        case 'color':
            newValue = $(this).colorpicker('getValue');
            currentParams.thresholdObject.fields[field].thrSeries[series].color = newValue;
            break;
            
        case 'desc':
            newValue = params.newValue;
            currentParams.thresholdObject.fields[field].thrSeries[series].desc = newValue;
            break;    

        default:
            console.log("Default");
            break;
     }
    $('#parametersM').val(JSON.stringify(currentParams));
}

function updateParamsFirstAxis(e, params) {
    var axis = $(this).attr('data-axis');
    var field = parseInt($(this).attr('data-field'));
    var series = parseInt($(this).attr('data-series'));
    var param = $(this).attr('data-param');
    var newValue = null;
    //Aggiornamento dei parametri
    switch(param)
    {
        case 'min':
            newValue = params.newValue;
            currentParams.thresholdObject.firstAxis.fields[field].thrSeries[series].min = newValue;
            break;

        case 'max':
            newValue = params.newValue;
            currentParams.thresholdObject.firstAxis.fields[field].thrSeries[series].max = newValue;
            break;

        case 'color':
            newValue = $(this).colorpicker('getValue');
            currentParams.thresholdObject.firstAxis.fields[field].thrSeries[series].color = newValue;
            break;
            
        case 'desc':
            newValue = params.newValue;
            currentParams.thresholdObject.firstAxis.fields[field].thrSeries[series].desc = newValue;
            break;    

        default:
            console.log("Default");
            break;
     }
    $('#parameters').val(JSON.stringify(currentParams));
}

function updateParamsSecondAxis(e, params) {
    var axis = $(this).attr('data-axis');
    var field = parseInt($(this).attr('data-field'));
    var series = parseInt($(this).attr('data-series'));
    var param = $(this).attr('data-param');
    var newValue = null;

    //Aggiornamento dei parametri
    switch(param)
    {
        case 'min':
            newValue = params.newValue;
            currentParams.thresholdObject.secondAxis.fields[field].thrSeries[series].min = newValue;
            break;

        case 'max':
            newValue = params.newValue;
            currentParams.thresholdObject.secondAxis.fields[field].thrSeries[series].max = newValue;
            break;

        case 'color':
            newValue = $(this).colorpicker('getValue');
            currentParams.thresholdObject.secondAxis.fields[field].thrSeries[series].color = newValue;
            break;
            
        case 'desc':
            newValue = params.newValue;
            currentParams.thresholdObject.secondAxis.fields[field].thrSeries[series].desc = newValue;
            break;        

        default:
            console.log("Default");
            break;
     }
    $('#parameters').val(JSON.stringify(currentParams));
}

//Funzione che aggiorna tutti i data-series delle thrTables per widgets NON serie
function updateDataSeriesSimple()
{
    var newIndex = 0;
    var tableName = $('#alrFieldSel').val();
    var i, l, row = null;

    l = thrSimpleTables[tableName].find('tr').length;

    for(i = 0; i < l; i++)
    {
        row = thrSimpleTables[tableName].find('tr').eq(i);
        if(row.find('a.toBeEdited').length > 0)
        {
            row.find('a.toBeEdited').each(function() {
                $(this).attr('data-series', newIndex);
            });
            row.find('div.colorPicker').each(function() {
                $(this).attr('data-series', newIndex);
            });
            newIndex++;
        }
    }
}

//Funzione che aggiorna tutti i data-series delle thrTables per widgets NON serie
function updateDataSeriesSimpleM()
{
    var newIndex = 0;
    var tableName = $('#alrFieldSelM').val();
    var i, l, row = null;

    l = thrSimpleTables[tableName].find('tr').length;

    for(i = 0; i < l; i++)
    {
        row = thrSimpleTables[tableName].find('tr').eq(i);
        if(row.find('a.toBeEdited').length > 0)
        {
            row.find('a.toBeEdited').each(function() {
                $(this).attr('data-series', newIndex);
            });
            row.find('div.colorPicker').each(function() {
                $(this).attr('data-series', newIndex);
            });
            newIndex++;
        }
    }
}

//Funzione che aggiorna tutti i data-index della thrTable per widget radar
function updateDataIndexesRadar()
{
    var newIndex = 0;
    var i, l, row = null;
    
    l = thrTable.find('tr').length;

    for(i = 0; i < l; i++)
    {
        row = thrTable.find('tr').eq(i);
        if(i > 0)
        {
            row.find('i.fa-close').each(function() {
                $(this).attr('data-index', newIndex);
            });
            
            row.find('a.toBeEdited').each(function() {
                $(this).attr('data-index', newIndex);
            });
            
            row.find('div.colorPicker').each(function() {
                $(this).attr('data-index', newIndex);
            });
            
            newIndex++;
        }
    }
}

//Funzione che aggiorna tutti i data-series delle thrTables
function updateDataSeries()
{
    var newIndex = 0;
    var tableName = $('#alrFieldSel').val();
    var tableTarget, i, l, row = null;

    if($('#alrAxisSel').val() === series.firstAxis.desc)
    {
        tableTarget = thrTables1;
    }
    else if($('#alrAxisSel').val() === series.secondAxis.desc)
    {
        tableTarget = thrTables2;
    }

    l = tableTarget[tableName].find('tr').length;

    for(i = 0; i < l; i++)
    {
        row = tableTarget[tableName].find('tr').eq(i);
        if(row.find('a.toBeEdited').length > 0)
        {
            row.find('a.toBeEdited').each(function() {
                $(this).attr('data-series', newIndex);
            });
            row.find('div.colorPicker').each(function() {
                $(this).attr('data-series', newIndex);
            });
            newIndex++;
        }
    }
}

//Funzione di cancellazione di un range, risponde al click dei pulsanti di eliminazione per widget NON series
function delThrRangeSimple(e)
{
    var field = $(this).parent().attr('data-field');
    var index = parseInt($(this).parents('tr').index() - 1);
    //Cancellazione della riga dalla tabella 
    $(this).parents('tr').remove();

    //Riscalatura dei data-series
    updateDataSeriesSimple();

    //Aggiornamento JSON parametri
    currentParams.thresholdObject.fields[field].thrSeries.splice(index, 1);
    $('#parameters').val(JSON.stringify(currentParams));
}

//Funzione di cancellazione di un range, risponde al click dei pulsanti di eliminazione per widget NON series in edit widget
function delThrRangeSimpleM(e)
{
    var field = $(this).parent().attr('data-field');
    var index = parseInt($(this).parents('tr').index() - 1);
    //Cancellazione della riga dalla tabella 
    $(this).parents('tr').remove();

    //Riscalatura dei data-series
    updateDataSeriesSimpleM();

    //Aggiornamento JSON parametri
    currentParams.thresholdObject.fields[field].thrSeries.splice(index, 1);
    $('#parametersM').val(JSON.stringify(currentParams));
}

//Funzione di cancellazione di un range, risponde al click dei pulsanti di eliminazione
function delThrRange(e)
{
    var field = $(this).parent().attr('data-field');
    var index = parseInt($(this).parents('tr').index() - 1);
    //Cancellazione della riga dalla tabella 
    $(this).parents('tr').remove();

    //Riscalatura dei data-series
    updateDataSeries();

    //Aggiornamento JSON parametri
    if(currentParams.thresholdObject.target === currentParams.thresholdObject.firstAxis.desc)
    {
        currentParams.thresholdObject.firstAxis.fields[field].thrSeries.splice(index, 1);
    }
    else if(currentParams.thresholdObject.target === currentParams.thresholdObject.secondAxis.desc)
    {
        currentParams.thresholdObject.secondAxis.fields[field].thrSeries.splice(index, 1);
    }
    $('#parameters').val(JSON.stringify(currentParams));
}

//Costruzione THRTable per widget semplice (non series)
function buildEmptySimpleThrTables()
{
    for(i = 0; i < descriptions.length; i++)
    {
        thrSimpleTables[descriptions[i]] = $("<table class='table table-bordered table-condensed thrRangeTable'><tr><td>Range min</td><td>Range max</td><td>Range color</td><td>Short description</td><td><a href='#'><i class='fa fa-plus' style='font-size:24px;color:#337ab7'></i></a></td></tr></table>");
    }
}

//Costruzione THRTables vuote
function buildEmptyThrTables()
{
    for(i = 0; i < series.firstAxis.labels.length; i++)
    {
        if($("#select-widget").val() !== "widgetFirstAid")
        {
           thrTables1[series.firstAxis.labels[i]] = $("<table class='table table-bordered table-condensed thrRangeTable'><tr><td>Range min</td><td>Range max</td><td>Range color</td><td>Short description</td><td><a href='#'><i class='fa fa-plus' style='font-size:24px;color:#337ab7'></i></a></td></tr></table>");
        }
        else
        {
           thrTables1[series.firstAxis.labels[i]] = $("<table class='table table-bordered table-condensed thrRangeTable'><tr><td>Range min</td><td>Range max</td><td>Short description</td><td><a href='#'><i class='fa fa-plus' style='font-size:24px;color:#337ab7'></i></a></td></tr></table>");
        }
    }

    for(var i = 0; i < series.secondAxis.labels.length; i++)
    {
        
        if($("#select-widget").val() !== "widgetFirstAid")
        {
           thrTables2[series.secondAxis.labels[i]] = $("<table class='table table-bordered table-condensed thrRangeTable'><tr><td>Range min</td><td>Range max</td><td>Range color</td><td>Short description</td><td><a><i class='fa fa-plus' style='font-size:24px;color:#337ab7'></i></a></td></tr></table>");
        }
        else
        {
           thrTables2[series.secondAxis.labels[i]] = $("<table class='table table-bordered table-condensed thrRangeTable'><tr><td>Range min</td><td>Range max</td><td>Short description</td><td><a><i class='fa fa-plus' style='font-size:24px;color:#337ab7'></i></a></td></tr></table>");
        }
    } 
}

//Listener per settaggio/desettaggio soglie relativo alla select "Set thresholds" per widget NON series
function alrThrSelListenerSimple()
{
    if($('#alrThrSel').val() === "yes")
    {
        //Svuotamento preventivo della select dei campi
        $('#alrFieldSel').empty();
        
        //Popolamento della select dei campi
        for(var i = 0; i < descriptions.length; i++)
        {
            newField = $("<option value='" + descriptions[i] + "'>" + descriptions[i] + "</option>");
            $('#alrFieldSel').append(newField);
        }
        
        //La select dei campi viene mostrata
        $("label[for='alrFieldSel']").show();
        $('#alrFieldSel').parent().show();
        $('#alrFieldSel').show();
        $('#alrFieldSel').val(-1);
        $('#alrFieldSel').change(alrFieldSelListenerSimple);
    }
    else
    {
        $('#alrFieldSel').off();
        $("label[for='alrFieldSel']").hide();
        $('#alrFieldSel').hide();
    }
    $('#addWidgetRangeTableContainer').empty();
    $('#addWidgetRangeTableContainer').hide();
}

//Listener per settaggio/desettaggio soglie relativo alla select "Set thresholds" per widget series
function alrThrSelListener()
{
    $('#alrAxisSel').val(-1);

    if($('#alrThrSel').val() === "yes")
    {
        $('#parameters').val(JSON.stringify(currentParams));
        $('label[for="alrAxisSel"]').show();
        $('#alrAxisSel').parent().show();
        $('#alrAxisSel').show();
        //Listener per settaggio/desettaggio campi in base ad asse selezionato
        $('#alrAxisSel').change(alrAxisSelListener);
    }
    else
    {
        //In questo caso settiamo a null il campo nascosto, di modo da mantenere in currentParameters i parametri attuali se l'utente ci ripensa prima di sottomettere il form
        $('#parameters').val('');
        $('#alrAxisSel').off();
        $("label[for='alrAxisSel']").hide();
        $('#alrAxisSel').parent().hide();
        $('#alrAxisSel').hide();
        $("label[for='alrFieldSel']").hide();
        $('#alrFieldSel').hide();
        $('#addWidgetRangeTableContainer').empty();
        $('#addWidgetRangeTableContainer').hide();
    }
}

//Listener per settaggio/desettaggio soglie relativo alla select "Set thresholds" per widget radar series
function alrThrSelListenerRadar()
{
    if($('#alrThrSel').val() === "yes")
    {
        $('#parameters').val(JSON.stringify(currentParams));
        $("label[for='alrThrLinesWidth']").show();
        $('#alrThrLinesWidth').parent().show();
        $('#alrThrLinesWidth').show();
        $('#addWidgetRangeTableContainer').append(thrTable);
        $('#addWidgetRangeTableContainer').show();
        refreshTableListenersRadar();
    }
    else
    {
        //In questo caso settiamo a null il campo nascosto, di modo da mantenere in currentParameters i parametri attuali se l'utente ci ripensa prima di sottomettere il form
        $('#parameters').val('');
        $("label[for='alrThrLinesWidth']").hide();
        $('#alrThrLinesWidth').hide();
        $('#alrThrLinesWidth').parent().hide();
        $('#addWidgetRangeTableContainer').find("table.thrRangeTableRadar").find("i.fa-plus").off();
        $('#addWidgetRangeTableContainer').empty();
        $('#addWidgetRangeTableContainer').hide();
    }
}

//Listener per settaggio/desettaggio soglie relativo alla select "Set thresholds" per widget radar series in edit
function alrThrSelListenerRadarM()
{
    if($('#alrThrSelM').val() === "yes")
    {
        $('#parametersM').val(JSON.stringify(currentParams));
        $("label[for='alrThrLinesWidthM']").show();
        $('#alrThrLinesWidthM').parent().show();
        $('#alrThrLinesWidthM').show();
        $('#addWidgetRangeTableContainerM').append(thrTable);
        $('#addWidgetRangeTableContainerM').show();
        refreshTableListenersRadarM();
    }
    else
    {
        //In questo caso settiamo a null il campo nascosto, di modo da mantenere in currentParameters i parametri attuali se l'utente ci ripensa prima di sottomettere il form
        $('#parametersM').val('');
        $("label[for='alrThrLinesWidthM']").hide();
        $('#alrThrLinesWidthM').parent().hide();
        $('#alrThrLinesWidthM').hide();
        $('#addWidgetRangeTableContainerM').find("table.thrRangeTableRadar").find("i.fa-plus").off();
        $('#addWidgetRangeTableContainerM').empty();
        $('#addWidgetRangeTableContainerM').hide();
    }
}

function alrAxisSelListener()
{
    var newField = null;
    
    $('#addWidgetRangeTableContainer').empty();

    //Popolamento select dei campi
    if($('#alrAxisSel').val() === series.firstAxis.desc)
    {
        $('#alrFieldSel').empty();
        for(var i = 0; i < series.firstAxis.labels.length; i++)
        {
            newField = $("<option value='" + series.firstAxis.labels[i] + "'>" + series.firstAxis.labels[i] + "</option>");
            $('#alrFieldSel').append(newField);
        }
    }
    else if($('#alrAxisSel').val() === series.secondAxis.desc)
    {
        $('#alrFieldSel').empty();
        for(var i = 0; i < series.secondAxis.labels.length; i++)
        {
            newField = $("<option value='" + series.secondAxis.labels[i] + "'>" + series.secondAxis.labels[i] + "</option>");
            $('#alrFieldSel').append(newField);
        }
    }
    
    $('label[for="alrFieldSel"]').show();
    $('#alrFieldSel').parent().show();
    $('#alrFieldSel').show();
    $('#alrFieldSel').val(-1);
    //Listener per selezione campo
    $('#alrFieldSel').off();
    $('#alrFieldSel').change(alrFieldSelListener);
    
    if((widgetType === "widgetLineSeries")||(widgetType === "widgetCurvedLineSeries"))
    {
        $('label[for="alrLook"]').show();
        $('#alrLook').parent().show();
        $('#alrLook').show();
        $('#alrLook').val("lines");
    }
}

//Funzione che ripristina tutti i gestori di eventi tabella ad ogni cambio di campo
function refreshTableListenersSimple(i)
{
    var field, series, currentColor = null;
    thrSimpleTables[i].find('a.toBeEdited').each(function() {
       $( this ).off('save');
    });
    thrSimpleTables[i].find('i.fa-close').each(function() {
       $( this ).off();
    });
    thrSimpleTables[i].find('i.fa-plus').each(function() {
       $( this ).off();
    });
    

    thrSimpleTables[i].find('a.toBeEdited').each(function() {
       $( this ).editable();
       $( this ).on('save', updateParamsSimple);
    });
    thrSimpleTables[i].find('i.fa-close').each(function() {
       $( this ).click(delThrRangeSimple);
    });
    thrSimpleTables[i].find('i.fa-plus').each(function() {
       $( this ).click(addThrRangeSimple);
    });
    thrSimpleTables[i].find('div.colorPicker').each(function() {
        field = $( this ).attr('data-field');
        series = $( this ).attr('data-series');
        currentColor = currentParams.thresholdObject.fields[field].thrSeries[series].color;
        $( this ).colorpicker({color: currentColor, format: "rgba"});
        $( this ).on('hidePicker', updateParamsSimple);
    });     
}

//Funzione che ripristina tutti i gestori di eventi tabella ad ogni cambio di campo in edit widget
function refreshTableListenersSimpleM(i)
{
    var field, series, currentColor = null;
    thrSimpleTables[i].find('a.toBeEdited').each(function() {
       $( this ).off('save');
    });
    thrSimpleTables[i].find('i.fa-close').each(function() {
       $( this ).off();
    });
    thrSimpleTables[i].find('i.fa-plus').each(function() {
       $( this ).off();
    });
    

    thrSimpleTables[i].find('a.toBeEdited').each(function() {
       $( this ).editable();
       $( this ).on('save', updateParamsSimpleM);
    });
    thrSimpleTables[i].find('i.fa-close').each(function() {
       $( this ).click(delThrRangeSimpleM);
    });
    thrSimpleTables[i].find('i.fa-plus').each(function() {
       $( this ).click(addThrRangeSimpleM);
    });
    thrSimpleTables[i].find('div.colorPicker').each(function() {
        field = $( this ).attr('data-field');
        series = $( this ).attr('data-series');
        currentColor = currentParams.thresholdObject.fields[field].thrSeries[series].color;
        $( this ).colorpicker({color: currentColor, format: "rgba"});
        $( this ).on('hidePicker', updateParamsSimpleM);
    });     
}

//Funzione che ripristina tutti i gestori di eventi tabella per widget radarSeries
function refreshTableListenersRadar()
{
    //Disattivazione listener pregressi
    thrTable.find('a.toBeEdited').each(function() {
        $( this ).off('save');
    });
    
    thrTable.find('i.fa-close').each(function() {
        $( this ).off();
    });
    
    thrTable.find('i.fa-plus').each(function() {
       $( this ).off();
    });
    
    //Attivazione nuovi listener
    thrTable.find('a.toBeEdited').each(function() {
        $( this ).editable();
        $( this ).on('save', updateParamsRadar);
    });
     
    thrTable.find('i.fa-close').each(function() {
       $( this ).click(delThrRangeRadar);
    });
     
    thrTable.find('i.fa-plus').each(function() {
       $( this ).click(addThrRangeRadar);
    });
     
    thrTable.find('div.colorPicker').each(function(i) {
        currentColor = currentParams.thresholdArray[i].color;
        $( this ).colorpicker({color: currentColor, format: "rgba"});
        $( this ).on('hidePicker', updateParamsRadar);
    });
}

//Funzione che ripristina tutti i gestori di eventi tabella per widget radarSeries in edit widget
function refreshTableListenersRadarM()
{
    //Disattivazione listener pregressi
    thrTable.find('a.toBeEdited').each(function() {
        $( this ).off('save');
    });
    
    thrTable.find('i.fa-close').each(function() {
        $( this ).off();
    });
    
    thrTable.find('i.fa-plus').each(function() {
       $( this ).off();
    });
    
    //Attivazione nuovi listener
    thrTable.find('a.toBeEdited').each(function() {
        $( this ).editable();
        $( this ).on('save', updateParamsRadarM);
    });
     
    thrTable.find('i.fa-close').each(function() {
       $( this ).click(delThrRangeRadarM);
    });
     
    thrTable.find('i.fa-plus').each(function() {
       $( this ).click(addThrRangeRadarM);
    });
     
    thrTable.find('div.colorPicker').each(function(i) {
        currentColor = currentParams.thresholdArray[i].color;
        $( this ).colorpicker({color: currentColor, format: "rgba"});
        $( this ).on('hidePicker', updateParamsRadarM);
    });
}


//Funzione che ripristina tutti i gestori di eventi tabella ad ogni cambio di campo
function refreshTableListeners(set, i)
{
    var field, series, currentColor = null;
    if(set === 'firstAxis')
    {
        thrTables1[i].find('a.toBeEdited').each(function() {
           $( this ).off('save');
        });
        thrTables1[i].find('i.fa-close').each(function() {
           $( this ).off();
        });
        thrTables1[i].find('i.fa-plus').each(function() {
           $( this ).off();
        });

        thrTables1[i].find('a.toBeEdited').each(function() {
           $( this ).editable();
           $( this ).on('save', updateParamsFirstAxis);
        });
        thrTables1[i].find('i.fa-close').each(function() {
           $( this ).click(delThrRange);
        });
        thrTables1[i].find('i.fa-plus').each(function() {
           $(this).click(addThrRange);
           /*if($("#select-widget").val() !== "widgetFirstAid")
           {
              $(this).click(addThrRange);
           }
           else
           {
              if($("#addWidgetRangeTableContainer table tr").length === 1)
              {
                 $(this).click(addThrRange);
              }
           }*/
           
        });
        thrTables1[i].find('div.colorPicker').each(function() {
            field = $( this ).attr('data-field');
            series = $( this ).attr('data-series');
            currentColor = currentParams.thresholdObject.firstAxis.fields[field].thrSeries[series].color;
            $( this ).colorpicker({color: currentColor, format: "rgba"});
            $( this ).on('hidePicker', updateParamsFirstAxis);
        });             
    }
    else
    {
        thrTables2[i].find('a.toBeEdited').each(function() {
           $( this ).off('save');
        });
        thrTables2[i].find('i.fa-close').each(function() {
           $( this ).off();
        });
        thrTables2[i].find('i.fa-plus').each(function() {
           $( this ).off();
        });
        thrTables2[i].find('a.toBeEdited').each(function() {
           $( this ).editable();
           $( this ).on('save', updateParamsFirstAxis);
        });
        thrTables2[i].find('i.fa-close').each(function() {
           $( this ).click(delThrRange);
        });
        thrTables2[i].find('i.fa-plus').each(function() {
           $( this ).click(addThrRange);
        });
        thrTables2[i].find('div.colorPicker').each(function() {
            field = $( this ).attr('data-field');
            series = $( this ).attr('data-series');
            currentColor = currentParams.thresholdObject.secondAxis.fields[field].thrSeries[series].color;
            $( this ).colorpicker({color: currentColor, format: "rgba"});
            $( this ).on('hidePicker', updateParamsSecondAxis);
        });
    }
}

function alrFieldSelListenerSimple()
{
    $('#addWidgetRangeTableContainer').empty();
    $('#addWidgetRangeTableContainer').append(thrSimpleTables[$('#alrFieldSel').val()]);
    $('#addWidgetRangeTableContainer').show();
    thrSimpleTables[$('#alrFieldSel').val()].show();
    refreshTableListenersSimple($('#alrFieldSel').val());
}   
                                                                                                                                                                
function alrFieldSelListener()
{
    $('#addWidgetRangeTableContainer').empty();
    if($('#alrAxisSel').val() === series.firstAxis.desc)
    {
        $('#addWidgetRangeTableContainer').append(thrTables1[$('#alrFieldSel').val()]);
        $('#addWidgetRangeTableContainer').show();
        thrTables1[$('#alrFieldSel').val()].show();
        refreshTableListeners('firstAxis', $('#alrFieldSel').val());
    }
    else if($('#alrAxisSel').val() === series.secondAxis.desc)
    {
        $('#addWidgetRangeTableContainer').append(thrTables2[$('#alrFieldSel').val()]);
        $('#addWidgetRangeTableContainer').show();
        thrTables2[$('#alrFieldSel').val()].show();
        refreshTableListeners('secondAxis', $('#alrFieldSel').val());
    }
}

//Listener per pulsante di aggiunta range per widget NON series
function addThrRangeSimple()
{
    var i, newTableRow, newTableCell, newRangeObj = null;
    currentFieldIndex = $('#alrFieldSel')[0].selectedIndex;
    currentSeriesIndex = null;

    //Gestione caso nessuna soglia pregressa: costruiamo l'object literal per i parametri
    if(currentParams === null)
    {
        currentSeriesIndex = 0;
        //Creazione del JSON dei parametri
        currentParams = {
            "thresholdObject": 
            {
                "fields" : new Array()   
            }
        };

        var newThrObj = null;

        for(var i = 0; i < descriptions.length; i++)
        {
            newThrObj = {
               "fieldName" : descriptions[i],
               "thrSeries" : new Array()
            };

            currentParams.thresholdObject.fields.push(newThrObj);
        }
    }
    else
    {
        currentSeriesIndex = currentParams.thresholdObject.fields[currentFieldIndex].thrSeries.length;
    }
    
    //Aggiungiamo un elemento alle thrSeries in esame, di modo che poi possa accogliere i nuovi valori dal save di XEditor
    newRangeObj = {
        "min":"0",
        "max":"0",
        "color":"#FFFFFF",
        "desc":""
    };
    currentParams.thresholdObject.fields[currentFieldIndex].thrSeries.push(newRangeObj);

    //Aggiunta record alla thrTable dell'asse di appartenenza
    newTableRow = $('<tr></tr>');
    newTableCell = $('<td><a href="#" class="toBeEdited" data-type="text" data-mode="popup" data-field="' + currentFieldIndex + '" data-series="' + currentSeriesIndex + '" data-param="min">0</a></td>');
    newTableCell.find('a').editable();
    newTableRow.append(newTableCell);
    newTableCell = $('<td><a href="#" class="toBeEdited" data-type="text" data-mode="popup" data-field="' + currentFieldIndex + '" data-series="' + currentSeriesIndex + '" data-param="max">0</td>');
    newTableCell.find('a').editable();
    newTableRow.append(newTableCell);
    newTableCell = $('<td><div class="input-group colorPicker" data-field="' + currentFieldIndex + '" data-series="' + currentSeriesIndex + '" data-param="color"><input type="text" class="form-control"><span class="input-group-addon"><i class="thePicker"></i></span></div></td>');
    newTableRow.append(newTableCell);
    newTableRow.find('div.colorPicker').colorpicker({color: "#FFFFFF", format: "rgba"});
    newTableRow.find('div.colorPicker').on('hidePicker', updateParamsSimple);                
    newTableRow.append(newTableCell);
    newTableCell = $('<td><a href="#" class="toBeEdited" data-type="text" data-mode="popup" data-field="' + currentFieldIndex + '" data-series="' + currentSeriesIndex + '" data-param="desc"></a></td>');
    newTableCell.find('a').editable();
    newTableRow.append(newTableCell);
    newTableCell = $('<td><a data-field="' + currentFieldIndex + '" data-series="' + currentSeriesIndex + '"><i class="fa fa-close" style="font-size:24px;color:red"></i></a></td>');
    newTableCell.find('i').click(delThrRangeSimple);
    newTableRow.append(newTableCell);
    newTableRow.find('a.toBeEdited').on('save', updateParamsSimple);
    thrSimpleTables[$('#alrFieldSel').val()].append(newTableRow);
    $('#parameters').val(JSON.stringify(currentParams));
}

//Listener per pulsante di aggiunta range per widget NON series in edit widget
function addThrRangeSimpleM()
{
    var i, newTableRow, newTableCell, newRangeObj = null;
    currentFieldIndex = $('#alrFieldSelM')[0].selectedIndex;
    currentSeriesIndex = null;

    //Gestione caso nessuna soglia pregressa: costruiamo l'object literal per i parametri
    if(currentParams === null)
    {
        currentSeriesIndex = 0;
        //Creazione del JSON dei parametri
        currentParams = {
            "thresholdObject": 
            {
                "fields" : new Array()   
            }
        };

        var newThrObj = null;

        for(var i = 0; i < descriptions.length; i++)
        {
            newThrObj = {
               "fieldName" : descriptions[i],
               "thrSeries" : new Array()
            };

            currentParams.thresholdObject.fields.push(newThrObj);
        }
    }
    else
    {
        currentSeriesIndex = currentParams.thresholdObject.fields[currentFieldIndex].thrSeries.length;
    }
    
    //Aggiungiamo un elemento alle thrSeries in esame, di modo che poi possa accogliere i nuovi valori dal save di XEditor
    newRangeObj = {
        "min":"0",
        "max":"0",
        "color":"#FFFFFF",
        "desc":""
    };
    currentParams.thresholdObject.fields[currentFieldIndex].thrSeries.push(newRangeObj);

    //Aggiunta record alla thrTable dell'asse di appartenenza
    newTableRow = $('<tr></tr>');
    newTableCell = $('<td><a href="#" class="toBeEdited" data-type="text" data-mode="popup" data-field="' + currentFieldIndex + '" data-series="' + currentSeriesIndex + '" data-param="min">0</a></td>');
    newTableCell.find('a').editable();
    newTableRow.append(newTableCell);
    newTableCell = $('<td><a href="#" class="toBeEdited" data-type="text" data-mode="popup" data-field="' + currentFieldIndex + '" data-series="' + currentSeriesIndex + '" data-param="max">0</td>');
    newTableCell.find('a').editable();
    newTableRow.append(newTableCell);
    newTableCell = $('<td><div class="input-group colorPicker" data-field="' + currentFieldIndex + '" data-series="' + currentSeriesIndex + '" data-param="color"><input type="text" class="form-control"><span class="input-group-addon"><i class="thePicker"></i></span></div></td>');
    newTableRow.append(newTableCell);
    newTableRow.find('div.colorPicker').colorpicker({color: "#FFFFFF", format: "rgba"});
    newTableRow.find('div.colorPicker').on('hidePicker', updateParamsSimpleM);                
    newTableRow.append(newTableCell);
    newTableCell = $('<td><a href="#" class="toBeEdited" data-type="text" data-mode="popup" data-field="' + currentFieldIndex + '" data-series="' + currentSeriesIndex + '" data-param="desc"></a></td>');
    newTableCell.find('a').editable();
    newTableRow.append(newTableCell);
    newTableCell = $('<td><a data-field="' + currentFieldIndex + '" data-series="' + currentSeriesIndex + '"><i class="fa fa-close" style="font-size:24px;color:red"></i></a></td>');
    newTableCell.find('i').click(delThrRangeSimpleM);
    newTableRow.append(newTableCell);
    newTableRow.find('a.toBeEdited').on('save', updateParamsSimpleM);
    thrSimpleTables[$('#alrFieldSelM').val()].append(newTableRow);
    $('#parametersM').val(JSON.stringify(currentParams));
}

//Listener per pulsante di aggiunta range
function addThrRangeRadar()
{
    var i, index, newTableRow, newTableCell, newThrObj, fieldName = null;

    //Gestione caso nessuna soglia pregressa: costruiamo l'object literal per i parametri
    if(currentParams === null)
    {
        //Creazione del JSON dei parametri VUOTO
        currentParams = {
            "thresholdArray": new Array()
        };
    }
    
    newThrObj = {
        "color": "#FFFFFF",
        "desc": ""
    };
    
    index = currentParams.thresholdArray.length;

    //Aggiunta record alla thrTable
    newTableRow = $('<tr></tr>');
    
    //Cella con pulsante rimozione riga
    newTableCell = $('<td><a><i class="fa fa-close" data-index="' + index + '" data-field="delBtn" style="font-size:24px;color:red"></i></a></td>');
    newTableCell.find('i').click(delThrRangeRadar);
    newTableRow.append(newTableCell);
    
    //Cella per color picker
    newTableCell = $('<td><div style="width: 130px" class="input-group colorPicker" style="width: 140px" data-index="' + index + '" + data-field="color"><input type="text" class="form-control"><span class="input-group-addon"><i class="thePicker"></i></span></div></td>');
    newTableRow.append(newTableCell);
    newTableRow.find('div.colorPicker').colorpicker({color: "#FFFFFF", format: "rgba"});
    newTableRow.find('div.colorPicker').on('hidePicker', updateParamsRadar);                
    newTableRow.append(newTableCell);
    
    //Cella per short description
    newTableCell = $('<td class="descValue"><a href="#" data-mode="popup" data-index="' + index + '" data-field="desc" class="toBeEdited"></a></td>');
    newTableCell.find('a').editable();
    newTableRow.append(newTableCell);
    
    //Celle per gli upper bound per ogni campo
    for(var i = 0; i < series.firstAxis.labels.length; i++)
    {
        fieldName = series.firstAxis.labels[i];
        newThrObj[fieldName] = 0;
        newTableCell = $('<td class="boundValue"><a href="#" data-mode="popup" data-index="' + index + '" data-field="' + fieldName + '" class="toBeEdited">0</a></td>');
        newTableCell.find('a').editable();
        newTableRow.append(newTableCell);
    }
    
    currentParams.thresholdArray.push(newThrObj);
    
    newTableRow.find('a.toBeEdited').on('save', updateParamsRadar);
    thrTable.append(newTableRow);
    $('#parameters').val(JSON.stringify(currentParams));
}

function addThrRangeRadarM()
{
    var i, index, newTableRow, newTableCell, newThrObj, fieldName = null;

    //Gestione caso nessuna soglia pregressa: costruiamo l'object literal per i parametri
    if(currentParams === null)
    {
        //Creazione del JSON dei parametri VUOTO
        currentParams = {
            "thresholdArray": new Array()
        };
    }
    
    newThrObj = {
        "color": "#FFFFFF",
        "desc": ""
    };
    
    index = currentParams.thresholdArray.length;

    //Aggiunta record alla thrTable
    newTableRow = $('<tr></tr>');
    
    //Cella con pulsante rimozione riga
    newTableCell = $('<td><a><i class="fa fa-close" data-index="' + index + '" data-field="delBtn" style="font-size:24px;color:red"></i></a></td>');
    newTableCell.find('i').click(delThrRangeRadar);
    newTableRow.append(newTableCell);
    
    //Cella per color picker
    newTableCell = $('<td><div style="width: 130px" class="input-group colorPicker" data-index="' + index + '" + data-field="color"><input type="text" class="form-control"><span class="input-group-addon"><i class="thePicker"></i></span></div></td>');
    newTableRow.append(newTableCell);
    newTableRow.find('div.colorPicker').colorpicker({color: "#FFFFFF", format: "rgba"});
    newTableRow.find('div.colorPicker').on('hidePicker', updateParamsRadar);                
    newTableRow.append(newTableCell);
    
    //Cella per short description
    newTableCell = $('<td class="descValue"><a href="#" data-mode="popup" data-index="' + index + '" data-field="desc" class="toBeEdited"></a></td>');
    newTableCell.find('a').editable();
    newTableRow.append(newTableCell);
    
    //Celle per gli upper bound per ogni campo
    for(var i = 0; i < series.firstAxis.labels.length; i++)
    {
        fieldName = series.firstAxis.labels[i];
        newThrObj[fieldName] = 0;
        newTableCell = $('<td class="boundValue"><a href="#" data-mode="popup" data-index="' + index + '" data-field="' + fieldName + '" class="toBeEdited">0</a></td>');
        newTableCell.find('a').editable();
        newTableRow.append(newTableCell);
    }
    
    currentParams.thresholdArray.push(newThrObj);
    
    newTableRow.find('a.toBeEdited').on('save', updateParamsRadarM);
    thrTable.append(newTableRow);
    $('#parametersM').val(JSON.stringify(currentParams));
}

function delThrRangeRadar()
{
    var index = parseInt($(this).attr('data-index'));
    
    //Aggiornamento JSON parametri
    currentParams.thresholdArray.splice(index, 1);
    $('#parameters').val(JSON.stringify(currentParams));

    //Cancellazione della riga dalla tabella 
    $(this).parents('tr').remove();

    //Riscalatura dei data-index
    updateDataIndexesRadar();
}

function delThrRangeRadarM()
{
    var index = parseInt($(this).attr('data-index'));
    
    //Aggiornamento JSON parametri
    currentParams.thresholdArray.splice(index, 1);
    $('#parametersM').val(JSON.stringify(currentParams));

    //Cancellazione della riga dalla tabella 
    $(this).parents('tr').remove();

    //Riscalatura dei data-index
    updateDataIndexesRadar();
}


function updateParamsRadar(e, params)
{
    var newValue = null;
    
    //E' il numero di riga
    var index = parseInt($(this).attr('data-index'));
    //E' il campo della riga della tabella. Valori possibili: delBtn, color, desc, LABELS del primo asse
    var field = $(this).attr('data-field');
    
    //Aggiornamento dei parametri
    switch(field)
    {
        case 'color':
            newValue = $(this).colorpicker('getValue');
            currentParams.thresholdArray[index][field] = newValue;
            break;
            
        case 'desc':
            newValue = params.newValue;
            currentParams.thresholdArray[index][field] = newValue;
            break; 
        
        case 'delBtn':
            break;

        //Update di uno degli upperbound    
        default:
            newValue = params.newValue;
            currentParams.thresholdArray[index][field] = newValue;
            break;
    }
    
    $('#parameters').val(JSON.stringify(currentParams));
}

function updateParamsRadarM(e, params)
{
    var newValue = null;
    
    //E' il numero di riga
    var index = parseInt($(this).attr('data-index'));
    //E' il campo della riga della tabella. Valori possibili: delBtn, color, desc, LABELS del primo asse
    var field = $(this).attr('data-field');
    
    //Aggiornamento dei parametri
    switch(field)
    {
        case 'color':
            newValue = $(this).colorpicker('getValue');
            currentParams.thresholdArray[index][field] = newValue;
            break;
            
        case 'desc':
            newValue = params.newValue;
            currentParams.thresholdArray[index][field] = newValue;
            break; 
        
        case 'delBtn':
            break;

        //Update di uno degli upperbound    
        default:
            newValue = params.newValue;
            currentParams.thresholdArray[index][field] = newValue;
            break;
    }
    
    $('#parametersM').val(JSON.stringify(currentParams));
}


//Listener per pulsante di aggiunta range
function addThrRange()
{
    var i, newTableRow, newTableCell, newRangeObj = null;
    currentFieldIndex = $('#alrFieldSel')[0].selectedIndex;
    currentSeriesIndex = null;

    //Gestione caso nessuna soglia pregressa: costruiamo l'object literal per i parametri
    if(currentParams === null)
    {
        currentSeriesIndex = 0;
        //Creazione del JSON dei parametri
        currentParams = {
            "thresholdObject": 
            {
                "target" : $('#alrAxisSel').val(),
                "firstAxis" : 
                {
                    "desc" : series.firstAxis.desc,
                    "fields" : new Array()
                },
                "secondAxis" : 
                {
                    "desc" : series.secondAxis.desc,
                    "fields" : new Array()
                }    
            }
        };

        var newThrObj = null;

        for(var i = 0; i < series.firstAxis.labels.length; i++)
        {
            newThrObj = {
               "fieldName" : series.firstAxis.labels[i],
               "thrSeries" : new Array()
            };

            currentParams.thresholdObject.firstAxis.fields.push(newThrObj);
        }

        for(var j = 0; j < series.secondAxis.labels.length; j++)
        {
            newThrObj = {
               "fieldName" : series.secondAxis.labels[j],
               "thrSeries" : new Array()
            };

            currentParams.thresholdObject.secondAxis.fields.push(newThrObj);
        }
    }
    else
    {
        if($('#alrAxisSel').val() === series.firstAxis.desc)
        {
            currentSeriesIndex = currentParams.thresholdObject.firstAxis.fields[currentFieldIndex].thrSeries.length;
        }
        else
        {
            currentSeriesIndex = currentParams.thresholdObject.secondAxis.fields[currentFieldIndex].thrSeries.length;
        }
    }

    if($('#alrAxisSel').val() === series.firstAxis.desc)
    {
       if(($("#select-widget").val() !== "widgetFirstAid")|| (($("#select-widget").val() === "widgetFirstAid")&&(currentParams.thresholdObject.firstAxis.fields[currentFieldIndex].thrSeries.length === 0)))
       {
         //Aggiungiamo un elemento alle thrSeries in esame, di modo che poi possa accogliere i nuovi valori dal save di XEditor
         newRangeObj = {
             "min":"0",
             "max":"0",
             "color":"#FFFFFF",
             "desc":""
         };
         currentParams.thresholdObject.firstAxis.fields[currentFieldIndex].thrSeries.push(newRangeObj);

         //Aggiunta record alla thrTable dell'asse di appartenenza
         newTableRow = $('<tr></tr>');
         newTableCell = $('<td><a href="#" class="toBeEdited" data-type="text" data-mode="popup" data-axis="firstAxis" data-field="' + currentFieldIndex + '" data-series="' + currentSeriesIndex + '" data-param="min">0</a></td>');
         newTableCell.find('a').editable();
         newTableRow.append(newTableCell);
         newTableCell = $('<td><a href="#" class="toBeEdited" data-type="text" data-mode="popup" data-axis="firstAxis" data-field="' + currentFieldIndex + '" data-series="' + currentSeriesIndex + '" data-param="max">0</td>');
         newTableCell.find('a').editable();
         newTableRow.append(newTableCell);

         if($("#select-widget").val() !== "widgetFirstAid")
         {
            newTableCell = $('<td><div class="input-group colorPicker" data-axis="firstAxis" data-field="' + currentFieldIndex + '" data-series="' + currentSeriesIndex + '" data-param="color"><input type="text" class="form-control"><span class="input-group-addon"><i class="thePicker"></i></span></div></td>');
            newTableRow.append(newTableCell);
            newTableRow.find('div.colorPicker').colorpicker({color: "#FFFFFF", format: "rgba"});
            newTableRow.find('div.colorPicker').on('hidePicker', updateParamsFirstAxis);                
         }

         newTableCell = $('<td><a href="#" class="toBeEdited" data-type="text" data-mode="popup" data-axis="firstAxis" data-field="' + currentFieldIndex + '" data-series="' + currentSeriesIndex + '" data-param="desc"></a></td>');
         newTableCell.find('a').editable();
         newTableRow.append(newTableCell);
         newTableCell = $('<td><a data-axis="firstAxis" data-field="' + currentFieldIndex + '" data-series="' + currentSeriesIndex + '"><i class="fa fa-close" style="font-size:24px;color:red"></i></a></td>');
         newTableCell.find('i').click(delThrRange);
         newTableRow.append(newTableCell);
         newTableRow.find('a.toBeEdited').on('save', updateParamsFirstAxis);
         thrTables1[$('#alrFieldSel').val()].append(newTableRow);
       }
    }
    else if($('#alrAxisSel').val() === series.secondAxis.desc)
    {
        //Aggiungiamo un elemento alle thrSeries in esame, di modo che poi possa accogliere i nuovi valori dal save di XEditor
        newRangeObj = {
            "min":"0",
            "max":"0",
            "color":"#FFFFFF"
        };
        currentParams.thresholdObject.secondAxis.fields[currentFieldIndex].thrSeries.push(newRangeObj);
        //Aggiunta record alla thrTable dell'asse di appartenenza
        newTableRow = $('<tr></tr>');
        newTableCell = $('<td><a href="#" class="toBeEdited" data-type="text" data-mode="popup" data-axis="secondAxis" data-field="' + currentFieldIndex + '" data-series="' + currentSeriesIndex + '" data-param="min">0</a></td>');
        newTableCell.find('a').editable();
        newTableRow.append(newTableCell);
        newTableCell = $('<td><a href="#" class="toBeEdited" data-type="text" data-mode="popup" data-axis="secondAxis" data-field="' + currentFieldIndex + '" data-series="' + currentSeriesIndex + '" data-param="max">0</td>');
        newTableCell.find('a').editable();
        newTableRow.append(newTableCell);
        newTableCell = $('<td><div class="input-group colorPicker" data-axis="secondAxis" data-field="' + currentFieldIndex + '" data-series="' + currentSeriesIndex + '" data-param="color"><input type="text" class="form-control"><span class="input-group-addon"><i class="thePicker"></i></span></div></td>');
        newTableRow.append(newTableCell);
        newTableRow.find('div.colorPicker').colorpicker({color: "#FFFFFF", format: "rgba"});
        newTableRow.find('div.colorPicker').on('hidePicker', updateParamsSecondAxis);                
        newTableRow.append(newTableCell);
        newTableCell = $('<td><a href="#" class="toBeEdited" data-type="text" data-mode="popup" data-axis="secondAxis" data-field="' + currentFieldIndex + '" data-series="' + currentSeriesIndex + '" data-param="desc"></a></td>');
        newTableCell.find('a').editable();
        newTableRow.append(newTableCell);
        newTableCell = $('<td><a data-axis="secondAxis" data-field="' + currentFieldIndex + '" data-series="' + currentSeriesIndex + '"><i class="fa fa-close" style="font-size:24px;color:red"></i></a></td>');
        newTableCell.find('i').click(delThrRange);
        newTableRow.append(newTableCell);
        newTableRow.find('a.toBeEdited').on('save', updateParamsSecondAxis);
        thrTables2[$('#alrFieldSel').val()].append(newTableRow);
    }
    $('#parameters').val(JSON.stringify(currentParams));
}

function updateParamsFirstAxisM(e, params) 
{
    var axis = $(this).attr('data-axis');
    var field = parseInt($(this).attr('data-field'));
    var series = parseInt($(this).attr('data-series'));
    var param = $(this).attr('data-param');
    var newValue = null;

    //Aggiornamento dei parametri
    switch(param)
    {
        case 'min':
            newValue = params.newValue;
            currentParams.thresholdObject.firstAxis.fields[field].thrSeries[series].min = newValue;
            break;

        case 'max':
            newValue = params.newValue;
            currentParams.thresholdObject.firstAxis.fields[field].thrSeries[series].max = newValue;
            break;

        case 'color':
            newValue = $(this).colorpicker('getValue');
            currentParams.thresholdObject.firstAxis.fields[field].thrSeries[series].color = newValue;
            break;

        case 'desc':
            newValue = params.newValue;
            currentParams.thresholdObject.firstAxis.fields[field].thrSeries[series].desc = newValue;
            break;    

        default:
            console.log("Default");
            break;
     }
    $('#parametersM').val(JSON.stringify(currentParams)); 
}

function updateParamsSecondAxisM(e, params) {
    var axis = $(this).attr('data-axis');
    var field = parseInt($(this).attr('data-field'));
    var series = parseInt($(this).attr('data-series'));
    var param = $(this).attr('data-param');
    var newValue = null;

    //Aggiornamento dei parametri
    switch(param)
    {
        case 'min':
            newValue = params.newValue;
            currentParams.thresholdObject.secondAxis.fields[field].thrSeries[series].min = newValue;
            break;

        case 'max':
            newValue = params.newValue;
            currentParams.thresholdObject.secondAxis.fields[field].thrSeries[series].max = newValue;
            break;

        case 'color':
            newValue = $(this).colorpicker('getValue');
            currentParams.thresholdObject.secondAxis.fields[field].thrSeries[series].color = newValue;
            break;
            
        case 'desc':
            newValue = params.newValue;
            currentParams.thresholdObject.secondAxis.fields[field].thrSeries[series].desc = newValue;
            break;        

        default:
            console.log("Default");
            break;
     }
    $('#parametersM').val(JSON.stringify(currentParams)); 
}

function updateDataSeriesM()
{
    var newIndex = 0;
    var tableName = $('#alrFieldSelM').val();
    var tableTarget, i, l, row = null;

    if($('#alrAxisSelM').val() === series.firstAxis.desc)
    {
        tableTarget = thrTables1;
    }
    else if($('#alrAxisSelM').val() === series.secondAxis.desc)
    {
        tableTarget = thrTables2;
    }

    l = tableTarget[tableName].find('tr').length;

    for(i = 0; i < l; i++)
    {
        row = tableTarget[tableName].find('tr').eq(i);
        if(row.find('a.toBeEdited').length > 0)
        {
            row.find('a.toBeEdited').each(function() {
                $(this).attr('data-series', newIndex);
            });
            row.find('div.colorPicker').each(function() {
                $(this).attr('data-series', newIndex);
            });
            newIndex++;
        }
    }
}

function delThrRangeM(e)
{
    var field = $(this).parent().attr('data-field');
    var index = parseInt($(this).parents('tr').index() - 1);

    //Cancellazione della riga dalla tabella 
    $(this).parents('tr').remove();

    //Riscalatura dei data-series
    updateDataSeriesM();

    //Aggiornamento JSON parametri
    if(currentParams.thresholdObject.target === currentParams.thresholdObject.firstAxis.desc)
    {
        currentParams.thresholdObject.firstAxis.fields[field].thrSeries.splice(index, 1);
    }
    else if(currentParams.thresholdObject.target === currentParams.thresholdObject.secondAxis.desc)
    {
        currentParams.thresholdObject.secondAxis.fields[field].thrSeries.splice(index, 1);
    }
    $('#parametersM').val(JSON.stringify(currentParams));
}

//Costruzione THRTables dai parametri provenienti da DB (vuote se non ci sono soglie per quel campo, anche nel caso di nessuna soglia settata in assoluto per widget NON series
function buildThrTablesForEditWidgetSimple()
{
    var min, max, color, desc = null;
    for(var i = 0; i < descriptions.length; i++)
    {
        thrSimpleTables[descriptions[i]] = $("<table class='table table-bordered table-condensed thrRangeTable'><tr><td>Range min</td><td>Range max</td><td>Range color</td><td>Short description</td><td><a href='#'><i class='fa fa-plus' style='font-size:24px;color:#337ab7'></i></a></td></tr></table>"); 
        
        if(currentParams !== null)
        {
            thrSeries = currentParams.thresholdObject.fields[i].thrSeries;
            for(var k = 0; k < thrSeries.length; k++)
            {
                min = parseInt(thrSeries[k].min);
                max = parseInt(thrSeries[k].max);
                color = thrSeries[k].color;
                desc = thrSeries[k].desc;

                //Aggiunta a tabella
                newTableRow = $('<tr></tr>');
                newTableCell = $('<td><a href="#" class="toBeEdited" data-type="text" data-mode="popup" data-field="' + i + '" data-series="' + k + '" data-param="min">' + min + '</a></td>');
                newTableRow.append(newTableCell);

                newTableCell = $('<td><a href="#" class="toBeEdited" data-type="text" data-mode="popup" data-field="' + i + '" data-series="' + k + '" data-param="max">' + max + '</a></td>');
                newTableRow.append(newTableCell);

                newTableCell = $('<td><div class="input-group colorPicker" data-field="' + i + '" data-series="' + k + '" data-param="color"><input type="text" class="form-control"><span class="input-group-addon"><i class="thePicker"></i></span></div></td>');
                newTableRow.append(newTableCell);
                newTableRow.find('div.colorPicker').colorpicker({color: color, format: "rgba"});
                newTableRow.find('div.colorPicker').on('hidePicker', updateParamsSimpleM);

                newTableCell = $('<td><a href="#" class="toBeEdited" data-type="text" data-mode="popup" data-field="' + i + '" data-series="' + k + '" data-param="desc">' + desc + '</a></td>');
                newTableRow.append(newTableCell);

                newTableCell = $('<td><a href="#" data-field="' + i + '" data-series="' + k + '"><i class="fa fa-close" style="font-size:24px;color:red"></i></a></td>');
                newTableRow.append(newTableCell);
                thrSimpleTables[descriptions[i]].append(newTableRow);
            }
        }
    }
}

//Costruzione THRTables dai parametri provenienti da DB (vuote se non ci sono soglie per quel campo, anche nel caso di nessuna soglia settata in assoluto
function buildThrTablesForEditWidget()
{
    var min, max, color, desc = null;
    for(i = 0; i < series.firstAxis.labels.length; i++)
    {
        if($("#select-widget-m").val() !== "widgetFirstAid")
        {
           thrTables1[series.firstAxis.labels[i]] = $("<table class='table table-bordered table-condensed thrRangeTable'><tr><td>Range min</td><td>Range max</td><td>Range color</td><td>Short description</td><td><a href='#'><i class='fa fa-plus' style='font-size:24px;color:#337ab7'></i></a></td></tr></table>"); 
        }
        else
        {
           thrTables1[series.firstAxis.labels[i]] = $("<table class='table table-bordered table-condensed thrRangeTable'><tr><td>Range min</td><td>Range max</td><td>Short description</td><td><a href='#'><i class='fa fa-plus' style='font-size:24px;color:#337ab7'></i></a></td></tr></table>"); 
        }
        
        if(currentParams !== null)
        {
            if(currentParams.thresholdObject.target === currentParams.thresholdObject.firstAxis.desc)
            {
                for(j = 0; j < currentParams.thresholdObject.firstAxis.fields.length; j++)
                {
                    if(series.firstAxis.labels[i] === currentParams.thresholdObject.firstAxis.fields[j].fieldName)
                    {
                        thrSeries = currentParams.thresholdObject.firstAxis.fields[j].thrSeries;
                        for(k = 0; k < thrSeries.length; k++)
                        {
                            min = parseInt(thrSeries[k].min);
                            max = parseInt(thrSeries[k].max);
                            
                            if($("#select-widget-m").val() !== "widgetFirstAid")
                            {
                               color = thrSeries[k].color;
                               desc = thrSeries[k].desc;
                            }

                            //Aggiunta a tabella
                            newTableRow = $('<tr></tr>');
                            newTableCell = $('<td><a href="#" class="toBeEdited" data-type="text" data-mode="popup" data-axis="firstAxis" data-field="' + j + '" data-series="' + k + '" data-param="min">' + min + '</a></td>');
                            newTableRow.append(newTableCell);

                            newTableCell = $('<td><a href="#" class="toBeEdited" data-type="text" data-mode="popup" data-axis="firstAxis" data-field="' + j + '" data-series="' + k + '" data-param="max">' + max + '</a></td>');
                            newTableRow.append(newTableCell);
                            
                            if($("#select-widget-m").val() !== "widgetFirstAid")
                            {
                               newTableCell = $('<td><div class="input-group colorPicker" data-axis="firstAxis" data-field="' + j + '" data-series="' + k + '" data-param="color"><input type="text" class="form-control"><span class="input-group-addon"><i class="thePicker"></i></span></div></td>');
                               newTableRow.append(newTableCell);
                               newTableRow.find('div.colorPicker').colorpicker({color: color, format: "rgba"});
                               newTableRow.find('div.colorPicker').on('hidePicker', updateParamsFirstAxisM);
                            }
                            
                            newTableCell = $('<td><a href="#" class="toBeEdited" data-type="text" data-mode="popup" data-axis="firstAxis" data-field="' + j + '" data-series="' + k + '" data-param="desc">' + desc + '</a></td>');
                            newTableRow.append(newTableCell);

                            newTableCell = $('<td><a href="#" data-axis="firstAxis" data-field="' + j + '" data-series="' + k + '"><i class="fa fa-close" style="font-size:24px;color:red"></i></a></td>');
                            newTableRow.append(newTableCell);
                            thrTables1[series.firstAxis.labels[i]].append(newTableRow);
                        }
                    }
                }
            }
        }
    }

    for(var i = 0; i < series.secondAxis.labels.length; i++)
    {
        if($("#select-widget-m").val() !== "widgetFirstAid")
        {
           thrTables2[series.secondAxis.labels[i]] = $("<table class='table table-bordered table-condensed thrRangeTable'><tr><td>Range min</td><td>Range max</td><td>Range color</td><td>Short description</td><td><a><i class='fa fa-plus' style='font-size:24px;color:#337ab7'></i></a></td></tr></table>"); 
        }
        else
        {
           thrTables2[series.secondAxis.labels[i]] = $("<table class='table table-bordered table-condensed thrRangeTable'><tr><td>Range min</td><td>Range max</td><td>Range color</td><td><a><i class='fa fa-plus' style='font-size:24px;color:#337ab7'></i></a></td></tr></table>"); 
        }
      
        if(currentParams !== null)
        {
            if(currentParams.thresholdObject.target === currentParams.thresholdObject.secondAxis.desc)
            {
                for(j = 0; j < currentParams.thresholdObject.secondAxis.fields.length; j++)
                {
                    if(series.secondAxis.labels[i] === currentParams.thresholdObject.secondAxis.fields[j].fieldName)
                    {
                        thrSeries = currentParams.thresholdObject.secondAxis.fields[j].thrSeries;
                        for(k = 0; k < thrSeries.length; k++)
                        {
                            min = parseInt(thrSeries[k].min);
                            max = parseInt(thrSeries[k].max);
                            if($("#select-widget-m").val() !== "widgetFirstAid")
                            {
                               color = thrSeries[k].color;
                               desc = thrSeries[k].desc;
                            }

                            //Aggiunta a tabella
                            newTableRow = $('<tr></tr>');
                            
                            newTableCell = $('<td><a href="#" class="toBeEdited" data-type="text" data-mode="popup" data-axis="secondAxis" data-field="' + j + '" data-series="' + k + '" data-param="min">' + min + '</a></td>');
                            newTableRow.append(newTableCell);
                            
                            newTableCell = $('<td><a href="#" class="toBeEdited" data-type="text" data-mode="popup" data-axis="secondAxis" data-field="' + j + '" data-series="' + k + '" data-param="max">' + max + '</a></td>');
                            newTableRow.append(newTableCell);
                            
                            if($("#select-widget-m").val() !== "widgetFirstAid")
                            {
                              newTableCell = $('<td><div class="input-group colorPicker" data-axis="secondAxis" data-field="' + j + '" data-series="' + k + '" data-param="color"><input type="text" class="form-control"><span class="input-group-addon"><i class="thePicker"></i></span></div></td>');
                              newTableRow.find('div.colorPicker').colorpicker({color: color, format: "rgba"});
                              newTableRow.find('div.colorPicker').on('hidePicker', updateParamsSecondAxisM);
                              newTableRow.append(newTableCell);
                            }
                            
                            newTableCell = $('<td><a href="#" class="toBeEdited" data-type="text" data-mode="popup" data-axis="secondAxis" data-field="' + j + '" data-series="' + k + '" data-param="desc">' + desc + '</a></td>');
                            newTableRow.append(newTableCell);

                            newTableCell = $('<td><a data-axis="secondAxis" data-field="' + j + '" data-series="' + k + '"><i class="fa fa-close" style="font-size:24px;color:red"></i></a></td>');
                            newTableRow.append(newTableCell);
                            
                            thrTables2[series.secondAxis.labels[i]].append(newTableRow);
                        }
                    }
                }
            }
        }
    }
}

function alrThrFlagMListenerSimple()
{  
    if($('#alrThrSelM').val() === "yes")
    {
        $('#parametersM').val(JSON.stringify(currentParams));
        //POPOLAMENTO DELLA SELECT COI CAMPI
        $('#alrFieldSelM').empty();
        for(var i = 0; i < descriptions.length; i++)
        {
            $('#alrFieldSelM').append("<option value='" + descriptions[i] + "'>" + descriptions[i] + "</option>");
        }
        $('#alrFieldSelM').val(-1);
        $("label[for='alrFieldSelM']").show();
        $('#alrFieldSelM').parent().show();
        $('#alrFieldSelM').show();
        $('#addWidgetRangeTableContainerM').show();
        //Listener per selezione campo
        $('#alrFieldSelM').off();
        $('#alrFieldSelM').change(alrFieldSelMListenerSimple);
    }
    else
    {
        //In questo caso settiamo a null il campo nascosto, di modo da mantenere in currentParameters i parametri attuali se l'utente ci ripensa prima di sottomettere il form
        $('#parametersM').val('');
        $("label[for='alrFieldSelM']").hide();
        $('#alrFieldSelM').parent().hide();
        $('#alrFieldSelM').hide();
        $('#addWidgetRangeTableContainerM').empty();
        $('#addWidgetRangeTableContainerM').hide();
    }
}  
                                                                                                                                                              
function alrThrFlagMListener()
{  
    if($('#alrThrSelM').val() === "yes")
    {
        $('#parametersM').val(JSON.stringify(currentParams));
        $("label[for='alrAxisSelM']").show();
        $('#alrAxisSelM').parent().show();
        $('#alrAxisSelM').show();
        //POPOLAMENTO DELLA SELECT COI CAMPI
        alrAxisSelMListener();
        //Listener per selezione asse
        $('#alrAxisSelM').change(alrAxisSelMListener);
        $("label[for='alrFieldSelM']").show();
        $('#alrFieldSelM').parent().show();
        $('#alrFieldSelM').show();
        //Listener per selezione campo
        $('#alrFieldSelM').change(alrFieldSelMListener);
        if((widgetType === 'widgetLineSeries')||(widgetType === 'widgetCurvedLineSeries'))
        {
            $("label[for='alrLookM']").show();
            $('#alrLookM').parent().show();
            $('#alrLookM').show();
        }
    }
    else
    {
        //In questo caso settiamo a null il campo nascosto, di modo da mantenere in currentParameters i parametri attuali se l'utente ci ripensa prima di sottomettere il form
        $('#parametersM').val('');
        $('#alrAxisSelM').off();
        $("label[for='alrAxisSelM']").hide();
        $('#alrAxisSelM').parent().hide();
        $('#alrAxisSelM').hide();
        $("label[for='alrFieldSelM']").hide();
        $('#alrFieldSelM').parent().hide();
        $('#alrFieldSelM').empty();
        $('#alrFieldSelM').hide();
        if((widgetType === 'widgetLineSeries')||(widgetType === 'widgetCurvedLineSeries'))
        {
            $("label[for='alrLookM']").hide();
            $('#alrLookM').parent().hide();
            $('#alrLookM').hide();
        }
        $('#addWidgetRangeTableContainerM').empty();
        $('#addWidgetRangeTableContainerM').hide();
    }
}         

//Listener per settaggio/desettaggio campi in base ad asse selezionato
function alrAxisSelMListener()
{
    var newField = null;
    $('#addWidgetRangeTableContainerM').empty();

    //Gestione caso nessuna soglia pregressa: costruiamo l'object literal per i parametri
    if(currentParams === null)
    {
        currentParams = {
            "thresholdObject": 
            {
                "target" : $('#alrAxisSelM').val(),
                "firstAxis" : 
                {
                    "desc" : series.firstAxis.desc,
                    "fields" : new Array()
                },
                "secondAxis" : 
                {
                    "desc" : series.secondAxis.desc,
                    "fields" : new Array()
                }    
            }
        };

        var newThrObj = null;

        for(var i = 0; i < series.firstAxis.labels.length; i++)
        {
            newThrObj = {
               "fieldName" : series.firstAxis.labels[i],
               "thrSeries" : new Array()
            };

            currentParams.thresholdObject.firstAxis.fields.push(newThrObj);
        }

        for(var j = 0; j < series.secondAxis.labels.length; j++)
        {
            newThrObj = {
               "fieldName" : series.secondAxis.labels[j],
               "thrSeries" : new Array()
            };

            currentParams.thresholdObject.secondAxis.fields.push(newThrObj);
        }
    }

    //Popolamento select dei campi
    if($('#alrAxisSelM').val() === series.firstAxis.desc)
    {
        currentParams.thresholdObject.target = series.firstAxis.desc;
        $('#alrFieldSelM').empty();
        for(var i = 0; i < series.firstAxis.labels.length; i++)
        {
            newField = $("<option value='" + series.firstAxis.labels[i] + "'>" + series.firstAxis.labels[i] + "</option>");
            $('#alrFieldSelM').append(newField);
        }
    }
    else if($('#alrAxisSelM').val() === series.secondAxis.desc)
    {
        currentParams.thresholdObject.target = series.secondAxis.desc;
        $('#alrFieldSelM').empty();
        for(var i = 0; i < series.secondAxis.labels.length; i++)
        {
            newField = $("<option value='" + series.secondAxis.labels[i] + "'>" + series.secondAxis.labels[i] + "</option>");
            $('#alrFieldSelM').append(newField);
        }
    }
   $('#alrFieldSelM').val(-1);
}

//Funzione che ripristina tutti i gestori di eventi tabella ad ogni cambio di campo
function refreshTableListenersM(set, i)
{
    var field, series, currentColor = null;
    if(set === 'firstAxis')
    {
        thrTables1[i].find('a.toBeEdited').each(function() {
           $( this ).off('save');
        });
        thrTables1[i].find('i.fa-close').each(function() {
           $( this ).off();
        });
        thrTables1[i].find('i.fa-plus').each(function() {
           $( this ).off();
        });



        thrTables1[i].find('a.toBeEdited').each(function() {
           $( this ).editable();
           $( this ).on('save', updateParamsFirstAxisM);
        });
        thrTables1[i].find('i.fa-close').each(function() {
           $( this ).click(delThrRangeM);
        });
        thrTables1[i].find('i.fa-plus').each(function() {
           $( this ).click(addThrRangeM);
        });
        thrTables1[i].find('div.colorPicker').each(function() {
            field = $( this ).attr('data-field');
            series = $( this ).attr('data-series');
            currentColor = currentParams.thresholdObject.firstAxis.fields[field].thrSeries[series].color;
            $( this ).colorpicker({color: currentColor, format: "rgba"});
            $( this ).on('hidePicker', updateParamsFirstAxisM);
        });             
    }
    else
    {
        thrTables2[i].find('a.toBeEdited').each(function() {
           $( this ).off('save');
        });
        thrTables2[i].find('i.fa-close').each(function() {
           $( this ).off();
        });
        thrTables2[i].find('i.fa-plus').each(function() {
           $( this ).off();
        });
        thrTables2[i].find('a.toBeEdited').each(function() {
           $( this ).editable();
           $( this ).on('save', updateParamsSecondAxisM);
        });
        thrTables2[i].find('i.fa-close').each(function() {
           $( this ).click(delThrRangeM);
        });
        thrTables2[i].find('i.fa-plus').each(function() {
           $( this ).click(addThrRangeM);
        });
        thrTables2[i].find('div.colorPicker').each(function() {
            field = $( this ).attr('data-field');
            series = $( this ).attr('data-series');
            currentColor = currentParams.thresholdObject.secondAxis.fields[field].thrSeries[series].color;
            $( this ).colorpicker({color: currentColor, format: "rgba"});
            $( this ).on('hidePicker', updateParamsSecondAxisM);
        });
    }
}
//Listener per selezione campo in modifica widget per widget NON series
function alrFieldSelMListenerSimple()
{
    $('#addWidgetRangeTableContainerM').empty();
    $('#addWidgetRangeTableContainerM').append(thrSimpleTables[$('#alrFieldSelM').val()]);
    $('#addWidgetRangeTableContainerM').show();
    thrSimpleTables[$('#alrFieldSelM').val()].show();
    refreshTableListenersSimpleM($('#alrFieldSelM').val());
}  

//Listener per selezione campo in modifica widget per widget series
function alrFieldSelMListener()
{
    $('#addWidgetRangeTableContainerM').empty();
    $('#addWidgetRangeTableContainerM').show();
    if($('#alrAxisSelM').val() === series.firstAxis.desc)
    {
        $('#addWidgetRangeTableContainerM').append(thrTables1[$('#alrFieldSelM').val()]);
        refreshTableListenersM('firstAxis', $('#alrFieldSelM').val());
    }
    else if($('#alrAxisSelM').val() === series.secondAxis.desc)
    {
        $('#addWidgetRangeTableContainerM').append(thrTables2[$('#alrFieldSelM').val()]);
        refreshTableListenersM('secondAxis', $('#alrFieldSelM').val());
    }
}

//Listener per pulsante di aggiunta range
function addThrRangeM(){
    var i, newTableRow, newTableCell, newRangeObj, currentSeriesIndex = null;
    var currentFieldIndex = $('#alrFieldSelM')[0].selectedIndex;

    //Gestione caso nessuna soglia pregressa: costruiamo l'object literal per i parametri
    if(currentParams === null)
    {
        currentParams = {
            "thresholdObject": 
            {
                "target" : $('#alrAxisSelM').val(),
                "firstAxis" : 
                {
                    "desc" : series.firstAxis.desc,
                    "fields" : new Array()
                },
                "secondAxis" : 
                {
                    "desc" : series.secondAxis.desc,
                    "fields" : new Array()
                }    
            }
        };

        var newThrObj = null;

        for(var i = 0; i < series.firstAxis.labels.length; i++)
        {
            newThrObj = {
               "fieldName" : series.firstAxis.labels[i],
               "thrSeries" : new Array()
            };

            currentParams.thresholdObject.firstAxis.fields.push(newThrObj);
        }

        for(var j = 0; j < series.secondAxis.labels.length; j++)
        {
            newThrObj = {
               "fieldName" : series.secondAxis.labels[j],
               "thrSeries" : new Array()
            };

            currentParams.thresholdObject.secondAxis.fields.push(newThrObj);
        }
    }

    if($('#alrAxisSelM').val() === series.firstAxis.desc)
    {
        currentSeriesIndex = currentParams.thresholdObject.firstAxis.fields[currentFieldIndex].thrSeries.length;
    }
    else
    {
        currentSeriesIndex = currentParams.thresholdObject.secondAxis.fields[currentFieldIndex].thrSeries.length;
    }



    if($('#alrAxisSelM').val() === series.firstAxis.desc)
    {
       if(($("#select-widget-m").val() !== "widgetFirstAid")|| (($("#select-widget-m").val() === "widgetFirstAid")&&(currentParams.thresholdObject.firstAxis.fields[currentFieldIndex].thrSeries.length === 0)))
       {
         //Aggiungiamo un elemento alle thrSeries in esame, di modo che poi possa accogliere i nuovi valori dal save di XEditor
         newRangeObj = {
             "min":"0",
             "max":"0",
             "color":"#FFFFFF",
             "desc": ""
         };
         currentParams.thresholdObject.firstAxis.fields[currentFieldIndex].thrSeries.push(newRangeObj);

         //Aggiunta record alla thrTable dell'asse di appartenenza
         newTableRow = $('<tr></tr>');
         newTableCell = $('<td><a href="#" class="toBeEdited" data-type="text" data-mode="popup" data-axis="firstAxis" data-field="' + currentFieldIndex + '" data-series="' + currentSeriesIndex + '" data-param="min">0</a></td>');
         newTableCell.find('a').editable();
         newTableRow.append(newTableCell);
         newTableCell = $('<td><a href="#" class="toBeEdited" data-type="text" data-mode="popup" data-axis="firstAxis" data-field="' + currentFieldIndex + '" data-series="' + currentSeriesIndex + '" data-param="max">0</td>');
         newTableCell.find('a').editable();
         newTableRow.append(newTableCell);
         if($("#select-widget-m").val() !== "widgetFirstAid")
         {
            newTableCell = $('<td><div class="input-group colorPicker" data-axis="firstAxis" data-field="' + currentFieldIndex + '" data-series="' + currentSeriesIndex + '" data-param="color"><input type="text" class="form-control"><span class="input-group-addon"><i class="thePicker"></i></span></div></td>');
            newTableRow.append(newTableCell);
            newTableRow.find('div.colorPicker').colorpicker({color: "#FFFFFF", format: "rgba"});
            newTableRow.find('div.colorPicker').on('hidePicker', updateParamsFirstAxisM);                
            newTableRow.append(newTableCell);
         }
         newTableCell = $('<td><a href="#" class="toBeEdited" data-type="text" data-mode="popup" data-axis="firstAxis" data-field="' + currentFieldIndex + '" data-series="' + currentSeriesIndex + '" data-param="desc"></a></td>');
         newTableCell.find('a').editable();
         newTableRow.append(newTableCell);
         newTableCell = $('<td><a data-axis="firstAxis" data-field="' + currentFieldIndex + '" data-series="' + currentSeriesIndex + '"><i class="fa fa-close" style="font-size:24px;color:red"></i></a></td>');
         newTableCell.find('i').click(delThrRangeM);
         newTableRow.append(newTableCell);
         newTableRow.find('a.toBeEdited').on('save', updateParamsFirstAxisM);
         thrTables1[$('#alrFieldSelM').val()].append(newTableRow);
       }
    }
    else if($('#alrAxisSelM').val() === series.secondAxis.desc)
    {
        //Aggiungiamo un elemento alle thrSeries in esame, di modo che poi possa accogliere i nuovi valori dal save di XEditor
        newRangeObj = {
            "min":"0",
            "max":"0",
            "color":"#FFFFFF",
            "desc": ""
        };
        currentParams.thresholdObject.secondAxis.fields[currentFieldIndex].thrSeries.push(newRangeObj);
        //Aggiunta record alla thrTable dell'asse di appartenenza
        newTableRow = $('<tr></tr>');
        newTableCell = $('<td><a href="#" class="toBeEdited" data-type="text" data-mode="popup" data-axis="secondAxis" data-field="' + currentFieldIndex + '" data-series="' + currentSeriesIndex + '" data-param="min">0</a></td>');
        newTableCell.find('a').editable();
        newTableRow.append(newTableCell);
        newTableCell = $('<td><a href="#" class="toBeEdited" data-type="text" data-mode="popup" data-axis="secondAxis" data-field="' + currentFieldIndex + '" data-series="' + currentSeriesIndex + '" data-param="max">0</td>');
        newTableCell.find('a').editable();
        newTableRow.append(newTableCell);
        if($("#select-widget-m").val() !== "widgetFirstAid")
        {
           newTableCell = $('<td><div class="input-group colorPicker" data-axis="secondAxis" data-field="' + currentFieldIndex + '" data-series="' + currentSeriesIndex + '" data-param="color"><input type="text" class="form-control"><span class="input-group-addon"><i class="thePicker"></i></span></div></td>');
           newTableRow.append(newTableCell);
           newTableRow.find('div.colorPicker').colorpicker({color: "#FFFFFF", format: "rgba"});
           newTableRow.find('div.colorPicker').on('hidePicker', updateParamsSecondAxisM);                
        }
        newTableRow.append(newTableCell);
        newTableCell = $('<td><a href="#" class="toBeEdited" data-type="text" data-mode="popup" data-axis="secondAxis" data-field="' + currentFieldIndex + '" data-series="' + currentSeriesIndex + '" data-param="desc"></a></td>');
        newTableCell.find('a').editable();
        newTableRow.append(newTableCell);
        newTableCell = $('<td><a data-axis="secondAxis" data-field="' + currentFieldIndex + '" data-series="' + currentSeriesIndex + '"><i class="fa fa-close" style="font-size:24px;color:red"></i></a></td>');
        newTableCell.find('i').click(delThrRangeM);
        newTableRow.append(newTableCell);
        newTableRow.find('a.toBeEdited').on('save', updateParamsSecondAxisM);
        thrTables2[$('#alrFieldSelM').val()].append(newTableRow);
    }                                     
}