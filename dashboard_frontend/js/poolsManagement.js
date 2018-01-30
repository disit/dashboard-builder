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

var admin, addNewPoolConditionsArray, existingPools, poolIdToDelete, poolNameToDelete, selectedPoolId, destinationPage, poolNamesEdited, poolCompositionsEdited = null;

function setGlobals(adminAtt, poolNamesEditedAtt, poolCompositionsEditedAtt)
{
    admin = adminAtt;
    poolNamesEdited = poolNamesEditedAtt;
    poolCompositionsEdited = poolCompositionsEditedAtt;
    addNewPoolConditionsArray = new Array();
    addNewPoolConditionsArray['poolName'] = false;
}

//Funzione di disabilitazione dei link laterali: bisogna registrarci un opportuno listener che faccia preventDefault() se trova data-disabled settato
function disableMainLinks()
{
    $('#navbarLinks li a').click(disableMainLinksInnerFunction);
}

function disableMainLinksInnerFunction(event)
{
    event.preventDefault();
    destinationPage = event.target.href;
    $('#editPoolsLeavePageModal').modal('show');
}

//Funzione di abilitazione dei link laterali: bisogna rimuovere il listener di cui sopra se si trova data-disabled non settato
function enableMainLinks()
{
    $('#navbarLinks li a').off('click');
}

//Funzione di conferma cambio pagina senza salvare le modifiche ai dati degli utenti
function confirmPageChange(event)
{
    enableMainLinks();
    $('#editPoolsLeavePageModal').modal('hide');
    window.location.href = destinationPage;
}

function getPoolsCompositions()
{
    $.ajax({
        url: "getExistingPoolsCompositions.php",
        type: "POST",
        async: false,
        dataType: 'JSON',
        success: function (data) 
        {
           existingPools = data;
        },
        error: function (data) 
        {
            console.log("Get existing pools compositions KO");
            console.log(JSON.stringify(data));
        }
    });
}

function buildPoolCompositionTables() 
{  
    $("#editPoolsButtonsContainer").show();
    
    $("#outerUsersTableContainer").empty();
    $("#innerUsersTableContainer").empty();
    
    selectedPoolId = $("#delPoolTable input[data-selected=true]").val();
    
    var outerMembersTable = $('<table id="outerMembersTable"></table>');
    outerMembersTable.append("<tr><th>Select</th><th>Make admin</th><th>User name</th><th>User role</th></tr>");
    var innerMembersTable = $('<table id="innerMembersTable"></table>');
    innerMembersTable.append("<tr><th>Select</th><th>Is admin</th><th>User name</th><th>User role</th></tr>");

    for(var i = 0; i < existingPools.length; i++)
    {
        if(existingPools[i].poolId === selectedPoolId)
        {
            for(var j = 0; j < existingPools[i].outerMembers.length; j++)
            {
               if(existingPools[i].outerMembers[j].userRole === 'AreaManager')
               {
                  outerMembersTable.append('<tr><td><input type="checkbox" /></td><td><input type="checkbox" /></td><td>' + existingPools[i].outerMembers[j].username + '</td><td>' + existingPools[i].outerMembers[j].userRole + '</td></tr>');
               }
               else
               {
                  outerMembersTable.append('<tr><td><input type="checkbox" /></td><td><input type="checkbox" disabled/></td><td>' + existingPools[i].outerMembers[j].username + '</td><td>' + existingPools[i].outerMembers[j].userRole + '</td></tr>');
               }
            }
            
            for(var k = 0; k < existingPools[i].innerMembers.length; k++)
            {
               
               
                if(existingPools[i].innerMembers[k].isAdmin === '1')
                {
                  if(existingPools[i].innerMembers[k].userRole === 'AreaManager')
                  {
                     innerMembersTable.append('<tr><td><input type="checkbox" /></td><td><input type="checkbox" checked="checked"/></td><td>' + existingPools[i].innerMembers[k].username + '</td><td>' + existingPools[i].innerMembers[k].userRole + '</td></tr>');
                  }
                  else
                  {
                     innerMembersTable.append('<tr><td><input type="checkbox" /></td><td><input type="checkbox" checked="checked" disabled/></td><td>' + existingPools[i].innerMembers[k].username + '</td><td>' + existingPools[i].innerMembers[k].userRole + '</td></tr>');
                  }
                }
                else
                {
                  if(existingPools[i].innerMembers[k].userRole === 'AreaManager')
                  {
                     innerMembersTable.append('<tr><td><input type="checkbox" /></td><td><input type="checkbox"/></td><td>' + existingPools[i].innerMembers[k].username + '</td><td>' + existingPools[i].innerMembers[k].userRole + '</td></tr>');
                  }
                  else
                  {
                     innerMembersTable.append('<tr><td><input type="checkbox" /></td><td><input type="checkbox" disabled/></td><td>' + existingPools[i].innerMembers[k].username + '</td><td>' + existingPools[i].innerMembers[k].userRole + '</td></tr>');
                  }
                }
            }
        }
    }
    
    $("#outerUsersTableContainer").append(outerMembersTable);
    $("#innerUsersTableContainer").append(innerMembersTable);
    $("#poolManagementRow").show();
    
    $("#outerMembersTable input").click(function(){
        if($(this).attr("checked") === "checked")
        {
            $(this).removeAttr("checked");
        }
        else
        {
            $(this).attr("checked", "true");
        }
    });
    
    $("#innerMembersTable tr").each(function(){
        $(this).find("td").eq(0).find("input").click(function(){
            if($(this).attr("checked") === "checked")
            {
                $(this).removeAttr("checked");
            }
            else
            {
                $(this).attr("checked", "true");
            }
        });
        
        $(this).find("td").eq(1).find("input").click(function()
        {
            var username = $(this).parent().parent().find("td").eq(2).html();
            var selectedPoolIdLocal = $("#delPoolTable input[data-selected=true]").val();
            var adminChangedUser = null;
            disableMainLinks();
            poolCompositionsEdited = true;
            
            if($(this).attr("checked") === "checked")
            {
                $(this).removeAttr("checked");
                
                adminChangedUser = 
                {
                    "username": username,
                    "isAdmin": '0'
                };
                
                for(var i = 0; i < existingPools.length; i++)
                {
                    if(existingPools[i].poolId === selectedPoolIdLocal)
                    {
                        existingPools[i].edited = true;
                        
                        var found = false;
                        var z = 0;
                        
                        while((found === false)&&(z < existingPools[i].adminChangedMembers.length))
                        {
                            if(existingPools[i].adminChangedMembers[z].username === adminChangedUser.username)
                            {
                                existingPools[i].adminChangedMembers[z].isAdmin = adminChangedUser.isAdmin;
                                found = true;
                            }
                            else
                            {
                                z++;
                            }
                        }
                        
                        if(found === false)
                        {
                            existingPools[i].adminChangedMembers.push(adminChangedUser);
                        }
                        
                        for(var k = 0; k < existingPools[i].innerMembers.length; k++)
                        {
                            if(existingPools[i].innerMembers[k].username === username)
                            {
                                existingPools[i].innerMembers[k].isAdmin = '0';
                            }
                        }
                    }
                }
            }
            else
            {
                $(this).attr("checked", "true");
                
                adminChangedUser = 
                {
                    "username": username,
                    "isAdmin": '1'
                };
                
                for(var i = 0; i < existingPools.length; i++)
                {
                    if(existingPools[i].poolId === selectedPoolIdLocal)
                    {
                        existingPools[i].edited = true;
                        
                        var found = false;
                        var z = 0;
                        
                        while((found === false)&&(z < existingPools[i].adminChangedMembers.length))
                        {
                            if(existingPools[i].adminChangedMembers[z].username === adminChangedUser.username)
                            {
                                existingPools[i].adminChangedMembers[z].isAdmin = adminChangedUser.isAdmin;
                                found = true;
                            }
                            else
                            {
                                z++;
                            }
                        }
                        
                        if(found === false)
                        {
                            existingPools[i].adminChangedMembers.push(adminChangedUser);
                        }
                        
                        for(var k = 0; k < existingPools[i].innerMembers.length; k++)
                        {
                            if(existingPools[i].innerMembers[k].username === username)
                            {
                                existingPools[i].innerMembers[k].isAdmin = '1';
                            }
                        }
                    }
                }
            }
        });
    });
}

//Controllo di accettabilità nome nuovo pool in fase di aggiunta utente admin
function checkNewPoolName()
{
    var message = null;
    
    if($("#addPoolNewPoolName").val().length < 3)
    {
        message = 'Pool name must be at least 3 chars long';
        addNewPoolConditionsArray['poolName'] = false;
        $("#addPoolNewPoolNameMsg").css("color", "red");
    }
    else
    {
        //Controlla che non sia già in uso
        var inUse = false;
        
        for(var i = 0; i < existingPools.length; i++)
        {
            if(existingPools[i].poolName.toLowerCase() === $("#addPoolNewPoolName").val().toLowerCase())
            {
                inUse = true;
            }
        }
        
        if(inUse)
        {
            message = 'Pool name is already in use';
            addNewPoolConditionsArray['poolName'] = false;
            $("#addPoolNewPoolNameMsg").css("color", "red");
        }
        else
        {
            message = 'Ok';
            addNewPoolConditionsArray['poolName'] = true;
            $("#addPoolNewPoolNameMsg").css("color", "#337ab7");
        }
    }
    
    $("#addPoolNewPoolNameMsg").html(message);
}

//Controllo delle condizioni sui dati del nuovo utente per abilitarne l'inserimento
function checkAddNewPoolConditions()
{
    var enableButton = true;
    
    for(var key in addNewPoolConditionsArray) 
    {
        if(addNewPoolConditionsArray[key] === false)
        {
            enableButton = false;
            break;
        }
    }
    
    if(enableButton)
    {
        $("#addNewPoolConfirmBtn").attr("disabled", false);
    }
    else
    {
        $("#addNewPoolConfirmBtn").attr("disabled", true);
    }
}

//Funzione per aggiunta nuovo pool al sistema
function addNewPool()
{
    var newPoolJson = {
        poolName: $("#addPoolNewPoolName").val(),
        usersAddedToNewPool: []
    };
    
    $("#addPoolNewPoolUsersTable tr").each(function(i){
        if(i > 0)
        {
            if($(this).find("td").eq(0).find("input").attr("checked") === "checked")
            {
                var isAdmin = null;
            
                if($(this).find("td").eq(1).find("input").attr("checked") === "checked")
                {
                    isAdmin = 1;
                }
                else
                {
                    isAdmin = 0;
                }

                var user = {
                    username: $(this).find("td").eq(2).html(),
                    isAdmin: isAdmin
                };

                newPoolJson.usersAddedToNewPool.push(user);
            }
        }
    });
    
    //Chiamata API di inserimento nuovo pool
    $.ajax({
        url: "addPool.php",
        data: {newPoolJson: JSON.stringify(newPoolJson)},
        type: "POST",
        async: false,
        success: function (data) 
        {
            switch(data)
            {
                case '0':
                    $("#addPoolKoModalInnerDiv1").html('<h5>Pool <b>' + newPoolJson.poolName + '</b> couldn\'t be registered because of a database failure while inserting data, please try again</h5>');
                    $("#addPoolKoModal").modal('show');
                    setTimeout(addNewPoolKoTimeout, 2000);
                    break;
                    
                case '1':
                    $("#addPoolOkModalInnerDiv1").html('<h5>Pool <b>' + newPoolJson.poolName + '</b> successfully registered</h5>');
                    $("#addPoolOkModal").modal('show');
                    setTimeout(addNewPoolOkTimeout, 2000);
                    break;
                
                case '2':
                    $("#addPoolKoModalInnerDiv1").html('<h5>Pool <b>' + newPoolJson.poolName + '</b> couldn\'t be registered: this pool name is already in use, please change it and try again</h5>');
                    $("#addPoolKoModal").modal('show');
                    setTimeout(addNewPoolKoTimeout, 2000);
                    break;  
                
                default:
                    break;
            }
        },
        error: function (data) 
        {
            $("#addPoolKoModalInnerDiv1").html('<h5>Pool <b>' + newPoolJson.poolName + '</b> couldn\'t be registered because of an API call failure, please try again</h5>');
            $("#addPoolKoModal").modal('show');
            setTimeout(addNewPoolKoTimeout, 2000);
        }
    });
}

function addNewPoolOkTimeout()
{
   $("#addPoolOkModal").modal('hide');
   location.href = "pools.php?showManagementTab=true&selectedPoolId=-1";
}

function addNewPoolKoTimeout()
{
   $("#addPoolKoModal").modal('hide');
}

function setPoolIdToDelete(poolIdToDeleteAtt, poolNameToDeleteAtt)
{
   poolIdToDelete = poolIdToDeleteAtt;
   poolNameToDelete = poolNameToDeleteAtt;
   
   console.log("poolIdToDelete: " + poolIdToDelete + " - poolNameToDelete: " + poolNameToDelete);
}

function deletePool()
{
    $.ajax({
        url: "delPool.php",
        data: {poolIdToDelete: poolIdToDelete},
        type: "POST",
        async: false,
        success: function (data) 
        {
            switch(data)
            {
                case '0':
                    $("#delPoolKoModalInnerDiv1").html('<h5>Pool <b>' + poolNameToDelete + '</b> couldn\'t be deleted because of a database failure, please try again</h5>');
                    $("#deletePoolModal").modal('hide');
                    $("#delPoolKoModal").modal('show');
                    setTimeout(delPoolKoTimeout, 2000);
                    break;
                    
                case '1':
                    getPoolsCompositions();
                    $("#delPoolOkModalInnerDiv1").html('<h5>Pool <b>' + poolNameToDelete + '</b> successfully deleted</h5>');
                    $("#delPoolTable tr[data-poolId='" + poolIdToDelete + "']").remove();
                    $("#deletePoolModal").modal('hide');
                    $("#delPoolOkModal").modal('show');
                    $("#outerUsersTableContainer").empty();
                    $("#innerUsersTableContainer").empty();
                    $("#poolManagementRow").hide();
                    $("#editPoolsButtonsContainer").hide();
                    setTimeout(delPoolOkTimeout, 2000);
                    break;
                
                default:
                    break;
            }
        },
        error: function (data) 
        {
            $("#delPoolKoModalInnerDiv1").html('<h5>Pool <b>' + poolNameToDelete + '</b> couldn\'t be deleted because of an API call failure, please try again</h5>');
            $("#deletePoolModal").modal('hide');
            $("#delPoolKoModal").modal('show');
            setTimeout(delPoolKoTimeout, 2000);
        }
    });
}

function delPoolKoTimeout()
{
   $("#delPoolKoModal").modal('hide');
}

function delPoolOkTimeout()
{
   $("#delPoolOkModal").modal('hide');
}

//Listener al pulsante di aggiunta utenti ad un pool: li aggiunge solo al JSON, senza fare commit delle modifiche
function addUsersToPool()
{
    $("#outerMembersTable tr").each(function(){
        $(this).find("td").eq(0).find("input[checked='checked']").each(function(){
            var isAdmin = null;
            if($(this).parent().parent().find("td").eq(1).find("input").attr("checked") === "checked")
            {
                isAdmin = '1';
            }
            else
            {
                isAdmin = '0';
            }

            var username = $(this).parent().parent().find("td").eq(2).html();
            var userRole = $(this).parent().parent().find("td").eq(3).html();
            var usersToRemove = [];
            var poolIndex = null;

            for(var i = 0; i < existingPools.length; i++)
            {
                if(existingPools[i].poolId === selectedPoolId)
                {
                    poolIndex = i;

                    //Rimozione dalla lista degli utenti esterni: produzione degli indici
                    for(var j = 0; j < existingPools[i].outerMembers.length; j++)
                    {
                        if(existingPools[i].outerMembers[j].username === username)
                        {
                            usersToRemove.push(j);
                        }
                    }

                    //Aggiunta dell'utente al pool
                    newUser = {
                        "username": username,
                        "isAdmin": isAdmin,
                        "status": 'added',
                        "userRole": userRole
                    };

                    existingPools[i].innerMembers.push(newUser);
                    existingPools[i].addedMembers.push(newUser);
                    
                    //Rimozione (eventuale, non è detto che ci sia) dalla lista degli utenti rimossi
                    var flag = false;
                    var z = 0;

                    while((flag === false)&&(z < existingPools[i].removedMembers.length))
                    {
                        if(existingPools[i].removedMembers[z].username === newUser.username)
                        {
                            existingPools[i].removedMembers.splice(z, 1);
                            flag = true;
                        }
                        else
                        {
                            z++;
                        }
                    }
                }
            }
            
            //Rimozione dalla lista degli utenti esterni
            for(var k = 0; k < usersToRemove.length; k++)
            {
                existingPools[poolIndex].outerMembers.splice(usersToRemove[k], 1);
            }
            
            existingPools[poolIndex].edited = true;
        });
    });
    buildPoolCompositionTables();
    disableMainLinks();
    poolCompositionsEdited = true;
}

//Listener al pulsante di rimozione utenti ad un pool: li aggiunge solo al JSON, senza fare commit delle modifiche
function delUsersFromPool()
{
    $("#innerMembersTable tr").each(function(){
        $(this).find("td").eq(0).find("input[checked='checked']").each(function(){
            var username = $(this).parent().parent().find("td").eq(2).html();
            var userRole = $(this).parent().parent().find("td").eq(3).html();
            var usersToRemove = [];
            var poolIndex = null;

            for(var i = 0; i < existingPools.length; i++)
            {
                if(existingPools[i].poolId === selectedPoolId)
                {
                    poolIndex = i;

                    //Rimozione dalla lista degli utenti interni
                    for(var j = 0; j < existingPools[i].innerMembers.length; j++)
                    {
                        if(existingPools[i].innerMembers[j].username === username)
                        {
                            usersToRemove.push(j);
                        }
                    }

                    //Aggiunta dell'utente all'elenco degli utenti esterni al pool
                    newUser = {
                        "username": username,
                        "status": 'removed',
                        "userRole": userRole
                    };

                    existingPools[i].outerMembers.push(newUser);
                    existingPools[i].removedMembers.push(newUser);
                    
                    //Rimozione (eventuale, non è detto che ci sia) dalla lista degli utenti aggiunti
                    var flag = false;
                    var z = 0;

                    while((flag === false)&&(z < existingPools[i].addedMembers.length))
                    {
                        if(existingPools[i].addedMembers[z].username === newUser.username)
                        {
                            existingPools[i].addedMembers.splice(z, 1);
                            flag = true;
                        }
                        else
                        {
                            z++;
                        }
                    }
                }
            }

            for(var k = 0; k < usersToRemove.length; k++)
            {
                existingPools[poolIndex].innerMembers.splice(usersToRemove[k], 1);
            }
            
            existingPools[poolIndex].edited = true;
        });
    });
    buildPoolCompositionTables();
    disableMainLinks();
    poolCompositionsEdited = true;
}

//Funzione di salvataggio delle composizioni dei pools utenti editate da GUI
function savePoolsCompositions()
{
    $("#editPoolsModal div.modal-body").empty();
    $("#editPoolsModal div.modal-footer").hide();
    $("#editPoolsModal div.modal-body").removeClass("centerWithFlex");
    $("#editPoolsModal div.modal-body").append('<div class="poolsManagementSubfieldContainer">Saving configuration, please wait</div>');
    $("#editPoolsModal div.modal-body").append('<div class="poolsManagementSubfieldContainer"><i class="fa fa-spinner fa-spin" style="font-size:42px"></i></div>');
    
    var selectedPoolIdLocal = $("#delPoolTable input[data-selected=true]").val();
    
    $.ajax({
        url: "savePoolsCompositions.php",
        data: {poolsJson: JSON.stringify(existingPools)},
        type: "POST",
        async: false,
        success: function(data) 
        {
            switch(data)
            {
                case '0':
                    $("#editPoolsModal div.modal-body").empty();
                    $("#editPoolsModal div.modal-body").append('<div class="poolsManagementSubfieldContainer">Configuration couldn\'t be saved correctly because of a database failure, please try again</div>');
                    $("#editPoolsModal div.modal-body").append('<div class="poolsManagementSubfieldContainer"><i class="fa fa-frown-o" style="font-size:42px"></i></div>');

                    setTimeout(function(){
                        $("#editPoolsModal").modal('hide');
                        $("#editPoolsModal div.modal-body").empty();
                        $("#editPoolsModal div.modal-body").addClass("centerWithFlex");            
                        $("#editPoolsModal div.modal-body").html('Do you want to save edited pools compositions?');
                        $("#editPoolsModal div.modal-footer").show();
                    }, 2000);
                    break;

                case '1':
                    $("#editPoolsModal div.modal-body").empty();
                    $("#editPoolsModal div.modal-body").append('<div class="poolsManagementSubfieldContainer">Configuration saved correctly</div>');
                    $("#editPoolsModal div.modal-body").append('<div class="poolsManagementSubfieldContainer"><i class="fa fa-check" style="font-size:42px"></i></div>');

                    setTimeout(function(){
                        $("#editPoolsModal").modal('hide');
                        $("#editPoolsModal div.modal-body").empty();
                        $("#editPoolsModal div.modal-body").addClass("centerWithFlex");            
                        $("#editPoolsModal div.modal-body").html('Do you want to save edited pools compositions?');
                        $("#editPoolsModal div.modal-footer").show();
                        var index = window.location.href.indexOf("?");
                        var locationBase = window.location.href.substring(0, index);
                        window.location.href = locationBase + "?showManagementTab=true&selectedPoolId=" + selectedPoolIdLocal;
                    }, 2000);
                    break;

                default:
                    break;
            }
        },
        error: function(data) 
        {
            $("#editPoolsModal div.modal-body").empty();
            $("#editPoolsModal div.modal-body").append('<div class="poolsManagementSubfieldContainer">Configuration couldn\'t be saved correctly because of an API call failure, please try again</div>');
            $("#editPoolsModal div.modal-body").append('<div class="poolsManagementSubfieldContainer"><i class="fa fa-frown-o" style="font-size:42px"></i></div>');

            setTimeout(function(){
                $("#editPoolsModal").modal('hide');
                $("#editPoolsModal div.modal-body").empty();
                $("#editPoolsModal div.modal-body").addClass("centerWithFlex");            
                $("#editPoolsModal div.modal-body").html('Do you want to save edited pools compositions?');
                $("#editPoolsModal div.modal-footer").show();
            }, 2000);
        }
    });
}

function savePoolsNames()
{
    var poolNamesJson = [];
    
    $("#editPoolsNamesModal div.modal-body").empty();
    $("#editPoolsNamesModal div.modal-footer").hide();
    $("#editPoolsNamesModal div.modal-body").removeClass("centerWithFlex");
    $("#editPoolsNamesModal div.modal-body").append('<div class="poolsManagementSubfieldContainer">Saving pool names, please wait</div>');
    $("#editPoolsNamesModal div.modal-body").append('<div class="poolsManagementSubfieldContainer"><i class="fa fa-spinner fa-spin" style="font-size:42px"></i></div>');
    
    $("#delPoolTable tr").each(function(i)
    {
        if($(this).attr("data-edited") === 'true')
        {
            var pool = {
                poolId: $(this).attr("data-poolid"),
                poolName: $(this).find("td").eq(1).find("a").html() 
            };
            
            poolNamesJson.push(pool);
        }
    });
   
    $.ajax({
        url: "savePoolsNames.php",
        data: {poolsNamesJson: JSON.stringify(poolNamesJson)},
        type: "POST",
        async: false,
        success: function(data) 
        {
            switch(data)
            {
                case '0':
                    $("#editPoolsNamesModal div.modal-body").empty();
                    $("#editPoolsNamesModal div.modal-body").removeClass("centerWithFlex");
                    $("#editPoolsNamesModal div.modal-body").append('<div class="poolsManagementSubfieldContainer">Pools names couldn\'t be saved correctly because of a database failure, please try again</div>');
                    $("#editPoolsNamesModal div.modal-body").append('<div class="poolsManagementSubfieldContainer"><i class="fa fa-frown-o" style="font-size:42px"></i></div>');

                    setTimeout(function(){
                        $("#editPoolsNamesModal").modal('hide');
                        $("#editPoolsNamesModal div.modal-body").empty();
                        $("#editPoolsNamesModal div.modal-body").addClass("centerWithFlex");            
                        $("#editPoolsNamesModal div.modal-body").html('Do you want to save edited pools names?');
                        $("#editPoolsNamesModal div.modal-footer").show();
                    }, 2000);
                    break;

                case '1':
                    $("#editPoolsNamesModal div.modal-body").empty();
                    $("#editPoolsNamesModal div.modal-body").removeClass("centerWithFlex");
                    $("#editPoolsNamesModal div.modal-body").append('<div class="poolsManagementSubfieldContainer">Pool names saved correctly</div>');
                    $("#editPoolsNamesModal div.modal-body").append('<div class="poolsManagementSubfieldContainer"><i class="fa fa-check" style="font-size:42px"></i></div>');

                    setTimeout(function(){
                        $("#editPoolsNamesModal").modal('hide');
                        $("#editPoolsNamesModal div.modal-body").empty();
                        $("#editPoolsNamesModal div.modal-body").addClass("centerWithFlex");            
                        $("#editPoolsNamesModal div.modal-body").html('Do you want to save edited pools names?');
                        $("#editPoolsNamesModal div.modal-footer").show();
                        var index = window.location.href.indexOf("?");
                        var locationBase = window.location.href.substring(0, index);
                        window.location.href = locationBase + "?showManagementTab=true&selectedPoolId=-1";
                    }, 2000);
                    break;

                default:
                    break;
            }
        },
        error: function(data) 
        {
            $("#editPoolsNamesModal div.modal-body").empty();
            $("#editPoolsNamesModal div.modal-body").removeClass("centerWithFlex");
            $("#editPoolsNamesModal div.modal-body").append('<div class="poolsManagementSubfieldContainer">Pools names couldn\'t be saved correctly because of an API call failure, please try again</div>');
            $("#editPoolsNamesModal div.modal-body").append('<div class="poolsManagementSubfieldContainer"><i class="fa fa-frown-o" style="font-size:42px"></i></div>');

            setTimeout(function(){
                $("#editPoolsNamesModal").modal('hide');
                $("#editPoolsNamesModal div.modal-body").empty();
                $("#editPoolsNamesModal div.modal-body").addClass("centerWithFlex");            
                $("#editPoolsNamesModal div.modal-body").html('Do you want to save edited pools names?');
                $("#editPoolsNamesModal div.modal-footer").show();
            }, 2000);
        }
    });
}