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

var admin, destinationPage, usernamesArray, addUserConditionsArray, editUserConditionsArray, existingPoolsJson = null;

function setGlobals(adminAtt, existingPoolsJsonAtt)
{
    admin = adminAtt;
    existingPoolsJson = existingPoolsJsonAtt;
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
    $('#pageChangeModal').modal('show');
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
    $('#pageChangeModal').modal('hide');
    window.location.href = destinationPage;
}

//Funzione che apre il modale di aggiunta nuovo utente e prepara i dati ad esso necessario
function showAddUserModal()
{
    usernamesArray = new Array();
    addUserConditionsArray = new Array();
    addUserConditionsArray['username'] = false;
    //addUserConditionsArray['passwordContent'] = false;
    //addUserConditionsArray['passwordConfirm'] = false;
    addUserConditionsArray['nameSurnameCompany'] = false;
    addUserConditionsArray['email'] = false;
    
    $("#usersTable tbody tr").each(function(i){
        if($(this).find("td").length > 0)
        {
            usernamesArray.push($(this).find("td").eq(0).html().toLowerCase());
        }
    });
    
    $("#addUserModal #username").on('input', checkUsername);
    $("#addUserModal #username").on('input', checkAddUserConditions);
    
    //$("#addUserModal #password").on('input', checkPassword);
    //$("#addUserModal #password").on('input', checkAddUserConditions);
    
    //$("#addUserModal #passwordConfirm").on('input', checkPasswordConfirm);
    //$("#addUserModal #passwordConfirm").on('input', checkAddUserConditions);
    
    $("#addUserModal #firstName").on('input', checkNameSurnameCompany);
    $("#addUserModal #firstName").on('input', checkAddUserConditions);
    $("#addUserModal #lastName").on('input', checkNameSurnameCompany);
    $("#addUserModal #lastName").on('input', checkAddUserConditions);
    $("#addUserModal #organization").on('input', checkNameSurnameCompany);
    $("#addUserModal #organization").on('input', checkAddUserConditions);
    
    $("#addUserModal #email").on('input', checkEmail);
    $("#addUserModal #email").on('input', checkAddUserConditions);
    
    checkUsername();
    //checkPassword();
    //checkPasswordConfirm();
    checkNameSurnameCompany();
    checkEmail();
    
    $("#addUserModal").modal('show');
}

function showEditUserModalBody()
{
   editUserConditionsArray = new Array();
   //editUserConditionsArray['passwordContent'] = false;
   //editUserConditionsArray['passwordConfirm'] = false;
   editUserConditionsArray['nameSurnameCompany'] = false;
   editUserConditionsArray['email'] = false;
    
   //$("#editUserModal #passwordM").on('input', checkPasswordM);
   //$("#editUserModal #passwordM").on('input', checkEditUserConditions);
    
   //$("#editUserModal #passwordConfirmM").on('input', checkPasswordConfirmM);
   //$("#editUserModal #passwordConfirmM").on('input', checkEditUserConditions);
    
   $("#editUserModal #firstNameM").on('input', checkNameSurnameCompanyM);
   $("#editUserModal #firstNameM").on('input', checkEditUserConditions);
   
   $("#editUserModal #lastNameM").on('input', checkNameSurnameCompanyM);
   $("#editUserModal #lastNameM").on('input', checkEditUserConditions);
   
   $("#editUserModal #organizationM").on('input', checkNameSurnameCompanyM);
   $("#editUserModal #organizationM").on('input', checkEditUserConditions);
    
   $("#editUserModal #emailM").on('input', checkEmailM);
   $("#editUserModal #emailM").on('input', checkEditUserConditions);
    
   //checkPasswordM();
   //checkPasswordConfirmM();
   checkNameSurnameCompanyM();
   checkEmailM();
   checkEditUserConditions();
   
   $("#editUserModalBody").show();
}

//Controllo di accettabilità username durante digitazione 
function checkUsername()
{
    var message = null;
    
    if($("#addUserModal #username").val().length === 0)
    {
        $("#usernameMsg").css("color", "red");
        message = 'Username is mandatory';
        addUserConditionsArray['username'] = false;
    }
    else if($("#addUserModal #username").val().length < 3)
    {
        $("#usernameMsg").css("color", "red");
        message = 'Username must be at least 3 chars long';
        addUserConditionsArray['username'] = false;
    }
    else
    {
        if(($.inArray($("#addUserModal #username").val().toLowerCase(), usernamesArray, 0) > 0)||($("#addUserModal #username").val().toLowerCase() === usernamesArray[0]))
        {
            $("#usernameMsg").css("color", "red");
            message = 'Username already used';
            addUserConditionsArray['username'] = false;
        }
        else
        {
            $("#usernameMsg").css("color", "#337ab7");
            message = 'Ok';
            addUserConditionsArray['username'] = true;
        }
    }
    
    $("#usernameMsg").html(message);
}

//Controllo di accettabilità password durante la digitazione
/*function checkPassword()
{
    var message = null;
    
    if($("#addUserModal #password").val().length === 0)
    {
        $("#passwordMsg").css("color", "red");
        message = 'Password is mandatory';
        addUserConditionsArray['passwordContent'] = false;
    }
    else if($("#addUserModal #password").val().length < 8)
    {
        $("#passwordMsg").css("color", "red");
        message = 'Password must be at least 8 chars long';
        addUserConditionsArray['passwordContent'] = false;
    }
    else
    {
        if((/\d/.test($("#addUserModal #password").val())) && (/\D/.test($("#addUserModal #password").val())))
        {
            $("#passwordMsg").css("color", "#337ab7");
            message = 'Ok';
            addUserConditionsArray['passwordContent'] = true;
        }
        else
        {
            $("#passwordMsg").css("color", "red");
            message = 'Password must contain at least 1 digit and 1 char';
            addUserConditionsArray['passwordContent'] = false;
        }
    }
    
    checkPasswordConfirm();
    $("#passwordMsg").html(message);
}*/

function checkPasswordM()
{
    var message = null;
    
    if($("#editUserModal #passwordM").val().length === 0)
    {
        $("#passwordMsgM").css("color", "red");
        message = 'Password is mandatory';
        editUserConditionsArray['passwordContent'] = false;
    }
    else if($("#editUserModal #passwordM").val().length < 8)
    {
        $("#passwordMsgM").css("color", "red");
        message = 'Password must be at least 8 chars long';
        editUserConditionsArray['passwordContent'] = false;
    }
    else
    {
        if((/\d/.test($("#editUserModal #passwordM").val())) && (/\D/.test($("#editUserModal #passwordM").val())))
        {
            $("#passwordMsgM").css("color", "#337ab7");
            message = 'Ok';
            editUserConditionsArray['passwordContent'] = true;
        }
        else
        {
            $("#passwordMsgM").css("color", "red");
            message = 'Password must contain at least 1 digit and 1 char';
            editUserConditionsArray['passwordContent'] = false;
        }
    }
    
    checkPasswordConfirmM();
    $("#passwordMsgM").html(message);
}

//Controllo di coerenza fra password e conferma password
/*function checkPasswordConfirm()
{
    var message = null;
    
    if(addUserConditionsArray['passwordContent'] === false)
    {
        message = 'Password not confirmable until it\'s not valid';
        addUserConditionsArray['passwordConfirm'] = false;
        $("#passwordConfirmMsg").css("color", "red");
    }
    else
    {
        if($("#addUserModal #passwordConfirm").val() === $("#addUserModal #password").val())
        {
            message = 'Ok';
            addUserConditionsArray['passwordConfirm'] = true;
            $("#passwordConfirmMsg").css("color", "#337ab7");
        }
        else
        {
            message = 'Password and confirmation value don\'t match';
            addUserConditionsArray['passwordConfirm'] = false;
            $("#passwordConfirmMsg").css("color", "red");
        }
    }
    
    $("#passwordConfirmMsg").html(message);
}*/

function checkPasswordConfirmM()
{
    var message = null;
    
    if(editUserConditionsArray['passwordContent'] === false)
    {
        message = 'Password not confirmable until it\'s not valid';
        editUserConditionsArray['passwordConfirm'] = false;
        $("#passwordConfirmMsgM").css("color", "red");
    }
    else
    {
        if($("#editUserModal #passwordConfirmM").val() === $("#editUserModal #passwordM").val())
        {
            message = 'Ok';
            editUserConditionsArray['passwordConfirm'] = true;
            $("#passwordConfirmMsgM").css("color", "#337ab7");
        }
        else
        {
            message = 'Password and confirmation value don\'t match';
            editUserConditionsArray['passwordConfirm'] = false;
            $("#passwordConfirmMsgM").css("color", "red");
        }
    }
    
    $("#passwordConfirmMsgM").html(message);
}

//Controllo della presenza di almeno uno fra la coppia nome-cognome e il nome dell'organizzazione
function checkNameSurnameCompany()
{
    var message = null;
    
    if((($("#addUserModal #firstName").val() === '')&&($("#addUserModal #lastName").val() === '')&&($("#addUserModal #organization").val() === ''))||(($("#addUserModal #firstName").val() !== '')&&($("#addUserModal #lastName").val() === '')&&($("#addUserModal #organization").val() === ''))||(($("#addUserModal #firstName").val() === '')&&($("#addUserModal #lastName").val() !== '')&&($("#addUserModal #organization").val() === '')))
    {
        message = 'One between First name-Last name and Organization is mandatory';
        $("#firstNameMsg").html(message);
        $("#lastNameMsg").html(message);
        $("#organizationMsg").html(message);
        addUserConditionsArray['nameSurnameCompany'] = false;
        $("#firstNameMsg").css("color", "red");
        $("#lastNameMsg").css("color", "red");
        $("#organizationMsg").css("color", "red");
    }
    else
    {
        message = 'Ok';
        $("#firstNameMsg").html(message);
        $("#lastNameMsg").html(message);
        $("#organizationMsg").html(message);
        addUserConditionsArray['nameSurnameCompany'] = true;
        $("#firstNameMsg").css("color", "#337ab7");
        $("#lastNameMsg").css("color", "#337ab7");
        $("#organizationMsg").css("color", "#337ab7");
    }
}

function checkNameSurnameCompanyM()
{
    var message = null;
    
    if((($("#editUserModal #firstNameM").val() === '')&&($("#editUserModal #lastNameM").val() === '')&&($("#editUserModal #organizationM").val() === ''))||(($("#editUserModal #firstNameM").val() !== '')&&($("#editUserModal #lastNameM").val() === '')&&($("#editUserModal #organizationM").val() === ''))||(($("#editUserModal #firstNameM").val() === '')&&($("#editUserModal #lastNameM").val() !== '')&&($("#editUserModal #organizationM").val() === '')))
    {
        message = 'One between First name-Last name and Organization is mandatory';
        $("#firstNameMsgM").html(message);
        $("#lastNameMsgM").html(message);
        $("#organizationMsgM").html(message);
        editUserConditionsArray['nameSurnameCompany'] = false;
        $("#firstNameMsgM").css("color", "red");
        $("#lastNameMsgM").css("color", "red");
        $("#organizationMsgM").css("color", "red");
    }
    else
    {
        message = 'Ok';
        $("#firstNameMsgM").html(message);
        $("#lastNameMsgM").html(message);
        $("#organizationMsgM").html(message);
        editUserConditionsArray['nameSurnameCompany'] = true;
        $("#firstNameMsgM").css("color", "#337ab7");
        $("#lastNameMsgM").css("color", "#337ab7");
        $("#organizationMsgM").css("color", "#337ab7");
    }
}

//Controllo accettabilità email
function checkEmail()
{
    var message = null;
    var pattern = /[a-z0-9!#$%&'*+/=?^_`{|}~-]+(?:\.[a-z0-9!#$%&'*+/=?^_`{|}~-]+)*@(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\.)+[a-z0-9](?:[a-z0-9-]*[a-z0-9])?/;
    
    if($("#addUserModal #email").val() === '')
    {
        message = 'E-Mail is mandatory';
        addUserConditionsArray['email'] = false;
        $("#emailMsg").css("color", "red");
    }
    else if(!pattern.test($("#addUserModal #email").val()))
    {
        message = 'E-Mail format is not correct (mailbox@domain.ext)';
        addUserConditionsArray['email'] = false;
        $("#emailMsg").css("color", "red");
    }
    else if(pattern.test($("#addUserModal #email").val()))
    {
        message = 'Ok';
        addUserConditionsArray['email'] = true;
        $("#emailMsg").css("color", "#337ab7");
    }
    
    $("#emailMsg").html(message);
}

function checkEmailM()
{
    var message = null;
    var pattern = /[a-z0-9!#$%&'*+/=?^_`{|}~-]+(?:\.[a-z0-9!#$%&'*+/=?^_`{|}~-]+)*@(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\.)+[a-z0-9](?:[a-z0-9-]*[a-z0-9])?/;
    
    if($("#editUserModal #emailM").val() === '')
    {
        message = 'E-Mail is mandatory';
        editUserConditionsArray['email'] = false;
        $("#emailMsgM").css("color", "red");
    }
    else if(!pattern.test($("#editUserModal #emailM").val()))
    {
        message = 'E-Mail format is not correct (mailbox@domain.ext)';
        editUserConditionsArray['email'] = false;
        $("#emailMsgM").css("color", "red");
    }
    else if(pattern.test($("#editUserModal #emailM").val()))
    {
        message = 'Ok';
        editUserConditionsArray['email'] = true;
        $("#emailMsgM").css("color", "#337ab7");
    }
    
    $("#emailMsgM").html(message);
}

//Controllo delle condizioni sui dati del nuovo utente per abilitarne l'inserimento
function checkAddUserConditions()
{
    var enableButton = true;
    
    for(var key in addUserConditionsArray) 
    {
        if(addUserConditionsArray[key] === false)
        {
            enableButton = false;
            break;
        }
    }
    
    if(enableButton)
    {
        $("#addNewUserConfirmBtn").attr("disabled", false);
    }
    else
    {
        $("#addNewUserConfirmBtn").attr("disabled", true);
    }
}

function checkEditUserConditions()
{
    var enableButton = true;
    
    for(var key in editUserConditionsArray) 
    {
        if(editUserConditionsArray[key] === false)
        {
            enableButton = false;
            break;
        }
    }
    
    if(enableButton)
    {
       $("#editUserConfirmBtn").attr("disabled", false);
    }
    else
    {
       $("#editUserConfirmBtn").attr("disabled", true);
    }
}

/*function updateAccount(event)
{
   $("#editUserModalBody").hide();
   $("#editUserModalFooter").hide();
   $("#editUserModalUpdating").show();
   
    accountJson = {
        username: $("#editUserForm #usernameM").val(),
        firstName: $("#editUserForm #firstNameM").val(),
        lastName: $("#editUserForm #lastNameM").val(),
        organization: $("#editUserForm #organizationM").val(),
        userType: $("#editUserForm #userTypeM").val(),
        userStatus: $("#editUserForm #userStatusM").val(),
        email: $("#editUserForm #emailM").val(),
        pools: []
    };
    
    switch(accountJson.userType)
    {
        case 'Observer': case 'Manager':
            $("#editUserPoolsTable tr").each(function(i){
                if($(this).find(".editUserPoolsTableMakeMemberCheckbox input").prop("checked"))
                {
                    var poolItem = {
                       poolId: $(this).find(".editUserPoolsTableMakeMemberCheckbox input").attr("data-poolid"),
                       makeAdmin: false
                    };
                    accountJson.pools.push(poolItem);
                }
            });
            break;
            
        case 'AreaManager':
            $("#editUserPoolsTable tr").each(function(){
                if($(this).find(".editUserPoolsTableMakeMemberCheckbox input").prop("checked"))
                {
                    var poolItem = {
                       poolId: $(this).find(".editUserPoolsTableMakeMemberCheckbox input").attr("data-poolid"),
                       makeAdmin: false
                    };
                    accountJson.pools.push(poolItem);
                }
                
                if($(this).find(".editUserPoolsTableMakeAdminCheckbox input").prop("checked"))
                {
                   var poolItem = {
                       poolId: $(this).find(".editUserPoolsTableMakeMemberCheckbox input").attr("data-poolid"),
                       makeAdmin: true
                    };
                    accountJson.pools.push(poolItem);
                }
            });
            break;
            
        default://Se superadmin non si fa niente di specifico su GUI - I superadmin non vengono più scritti come admin dei pool su DB
            break;
    }
    
    console.log(JSON.stringify(accountJson));
    
    //Chiamata API di inserimento nuovo utente
    $.ajax({
        url: "editUser.php",
        data:{operation: "updateAccount", accountJson: JSON.stringify(accountJson)},
        type: "POST",
        async: true,
        success: function (data) 
        {
           console.log("Ok");
           console.log(JSON.stringify(data));
            switch(data)
            {
                case '0':
                    $("#editUserModal").modal('hide');
                    $("#editUserKoModalInnerDiv1").html('<h5>Account <b>' + accountJson.username + '</b> couldn\'t be updated because of a database failure while inserting data, please try again</h5>');
                    $("#editUserKoModal").modal('show');
                    $("#editUserModalUpdating").hide();
                    $("#editUserModalBody").show();
                    $("#editUserModalFooter").show();
                    break;
                    
                case '1':
                    $("#editUserModal").modal('hide');
                    $("#editUserOkModalInnerDiv1").html('<h5>Account <b>' + accountJson.username + '</b> successfully updated</h5>');
                    $("#editUserOkModal").modal('show');
                    setTimeout(updateAccountTimeout, 2000);
                    break;
                
                case '4':
                    $("#editUserModal").modal('hide');
                    $("#editUserKoModalInnerDiv1").html('<h5>Account <b>' + accountJson.username + '</b> couldn\'t be updated: password is less than 8 chars long and/or doesn\'t have at least 1 char and 1 digit, please change it and try again</h5>');
                    $("#editUserKoModal").modal('show');
                    $("#editUserModalUpdating").hide();
                    $("#editUserModalBody").show();
                    $("#editUserModalFooter").show();
                    break;
                    
                case '5':
                    $("#editUserModal").modal('hide');
                    $("#editUserKoModalInnerDiv1").html('<h5>Account <b>' + accountJson.username + '</b> couldn\'t be updated: password and password confirmation don\'t match, please fix and try again</h5>');
                    $("#editUserKoModal").modal('show');
                    $("#editUserModalUpdating").hide();
                    $("#editUserModalBody").show();
                    $("#editUserModalFooter").show();
                    break;
                
                case '6':
                    $("#editUserModal").modal('hide');
                    $("#editUserKoModalInnerDiv1").html('<h5>Account <b>' + accountJson.username + '</b> couldn\'t be updated: one between (first name - last name) and organization must be given, please fix and try again</h5>');
                    $("#editUserKoModal").modal('show');
                    $("#editUserModalUpdating").hide();
                    $("#editUserModalBody").show();
                    $("#editUserModalFooter").show();
                    break;
                    
                case '7':
                    $("#editUserModal").modal('hide');
                    $("#editUserKoModalInnerDiv1").html('<h5>Account <b>' + accountJson.username + '</b> couldn\'t be updated: e-mail address doesn\'t respect mailbox@domain.ext pattern, please fix and try again</h5>');
                    $("#editUserKoModal").modal('show');
                    $("#editUserModalUpdating").hide();
                    $("#editUserModalBody").show();
                    $("#editUserModalFooter").show();
                    break;
                
                default:
                    break;
            }
        },
        error: function (data) 
        {
            console.log("Ko result: " + data);
            $("#editUserModal").modal('hide');
            $("#editUserKoModalInnerDiv1").html('<h5>Account <b>' + accountJson.username + '</b> couldn\'t be updated because of an API call failure, please try again</h5>');
            $("#editUserKoModal").modal('show');
            $("#editUserModalUpdating").hide();
            $("#editUserModalBody").show();
            $("#editUserModalFooter").show();
        }
    });
}*/


