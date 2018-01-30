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

var editAccountConditionsArray, enableAccountConditionsArray;

function enableAccount(username, email, password, hash, userRole)
{
   $('#enableAccountFormContainer h5').hide(); 
   $('#accountActivationFormRow').hide(); 
   $("#accountActivationBtnRow").hide();
   $("#accountActivationActivatingRow").show();
   
   $.ajax({
      url: "accountEnableApi.php",
      data:{username: username, email: email, password: password, hash: hash},
      type: "POST",
      async: true,
      success: function (data) 
      {
         console.log("Esito: " + data); 
          
         if(data === "1")
         {
            $("#accountActivationActivatingRow").hide();
            $("#accountActivationOkRow").show();
         }
         else
         {
            $("#accountActivationActivatingRow").hide();
            $("#accountActivationKoRow").show();
            setTimeout(function(){
                $("#accountActivationKoRow").hide();
                $('#enableAccountFormContainer h5').show();
                $('#accountActivationFormRow').show();
                $("#accountActivationBtnRow").show();
            }, 3500);
         }
      },
      error: function (data)
      {
        console.log("KO");
        console.log(data);
        $("#accountActivationActivatingRow").hide();
        $("#accountActivationKoRow").show();
        setTimeout(function(){
            $("#accountActivationKoRow").hide();
            $('#enableAccountFormContainer h5').show();
            $('#accountActivationFormRow').show();
            $("#accountActivationBtnRow").show();
        }, 3500);
      }
   });
}

function enableAccountPageSetup()
{
   enableAccountConditionsArray = new Array();
   enableAccountConditionsArray['passwordContent'] = false;
   enableAccountConditionsArray['passwordConfirm'] = false;
    
   $("#accountActivationPwd").on('input', checkPassword);
   $("#accountActivationPwd").on('input', checkEnableAccountConditions);
    
   $("#accountActivationConfirmPwd").on('input', checkPasswordConfirm);
   $("#accountActivationConfirmPwd").on('input', checkEnableAccountConditions);
    
   checkPassword();
   checkPasswordConfirm();
}

function checkEnableAccountConditions()
{
    var enableButton = true;
    
    for(var key in enableAccountConditionsArray) 
    {
        if(enableAccountConditionsArray[key] === false)
        {
            enableButton = false;
            break;
        }
    }
    
    if(enableButton)
    {
        $("#accountActivationBtn").attr("disabled", false);
    }
    else
    {
        $("#accountActivationBtn").attr("disabled", true);
    }
}

function editAccountPageSetup()
{
   editAccountConditionsArray = new Array();
   editAccountConditionsArray['passwordContent'] = false;
   editAccountConditionsArray['passwordConfirm'] = false;
   editAccountConditionsArray['nameSurnameCompany'] = false;
   editAccountConditionsArray['email'] = false;
    
   $("#accountPassword").on('input', checkPasswordEditAccount);
   $("#accountPassword").on('input', checkEditAccountConditions);
    
   $("#accountPasswordConfirmation").on('input', checkPasswordConfirmEditAccount);
   $("#accountPasswordConfirmation").on('input', checkEditAccountConditions);
    
   $("#accountFirstName").on('input', checkNameSurnameCompany);
   $("#accountFirstName").on('input', checkEditAccountConditions);
   $("#accountLastName").on('input', checkNameSurnameCompany);
   $("#accountLastName").on('input', checkEditAccountConditions);
   $("#accountOrganization").on('input', checkNameSurnameCompany);
   $("#accountOrganization").on('input', checkEditAccountConditions);
    
   $("#accountEmail").on('input', checkEmail);
   $("#accountEmail").on('input', checkEditAccountConditions);
    
   checkPasswordEditAccount();
   checkPasswordConfirmEditAccount();
   checkNameSurnameCompany();
   checkEmail(); 
}

function checkEditAccountConditions()
{
    var enableButton = true;
    
    for(var key in editAccountConditionsArray) 
    {
        if(editAccountConditionsArray[key] === false)
        {
            enableButton = false;
            break;
        }
    }
    
    if(enableButton)
    {
        $("#editAccountConfirmBtn").attr("disabled", false);
    }
    else
    {
        $("#editAccountConfirmBtn").attr("disabled", true);
    }
}

//Controllo della presenza di almeno uno fra la coppia nome-cognome e il nome dell'organizzazione
function checkNameSurnameCompany()
{
    var message = null;
    
    if((($("#accountFirstName").val() === '')&&($("#accountLastName").val() === '')&&($("#accountOrganization").val() === ''))||(($("#accountFirstName").val() !== '')&&($("#accountLastName").val() === '')&&($("#accountOrganization").val() === ''))||(($("#accountFirstName").val() === '')&&($("#accountLastName").val() !== '')&&($("#accountOrganization").val() === '')))
    {
        message = 'One between First name-Last name and Organization is mandatory';
        $("#accountFirstNameMsg").html(message);
        $("#accountLastNameMsg").html(message);
        $("#accountOrganizationMsg").html(message);
        editAccountConditionsArray['nameSurnameCompany'] = false;
        $("#accountFirstNameMsg").css("color", "red");
        $("#accountLastNameMsg").css("color", "red");
        $("#accountOrganizationMsg").css("color", "red");
    }
    else
    {
        message = 'Ok';
        $("#accountFirstNameMsg").html(message);
        $("#accountLastNameMsg").html(message);
        $("#accountOrganizationMsg").html(message);
        editAccountConditionsArray['nameSurnameCompany'] = true;
        $("#accountFirstNameMsg").css("color", "rgba(0, 162, 211, 1)");
        $("#accountLastNameMsg").css("color", "rgba(0, 162, 211, 1)");
        $("#accountOrganizationMsg").css("color", "rgba(0, 162, 211, 1)");
    }
}

//Controllo accettabilità email
function checkEmail()
{
    var message = null;
    var pattern = /[a-z0-9!#$%&'*+/=?^_`{|}~-]+(?:\.[a-z0-9!#$%&'*+/=?^_`{|}~-]+)*@(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\.)+[a-z0-9](?:[a-z0-9-]*[a-z0-9])?/;
    
    if($("#accountEmail").val() === '')
    {
        message = 'E-Mail is mandatory';
        editAccountConditionsArray['email'] = false;
        $("#accountEmailMsg").css("color", "red");
    }
    else if(!pattern.test($("#accountEmail").val()))
    {
        message = 'E-Mail format is not correct (mailbox@domain.ext)';
        editAccountConditionsArray['email'] = false;
        $("#accountEmailMsg").css("color", "red");
    }
    else if(pattern.test($("#accountEmail").val()))
    {
        message = 'Ok';
        editAccountConditionsArray['email'] = true;
        $("#accountEmailMsg").css("color", "rgba(0, 162, 211, 1)");
    }
    
    $("#accountEmailMsg").html(message);
}

//Controllo di accettabilità password durante la digitazione
function checkPassword()
{
    var message = null;
    
    if($("#accountActivationPwd").val().length === 0)
    {
        $("#accountActivationPwdMsg").css("color", "red");
        message = 'Password is mandatory';
        enableAccountConditionsArray['passwordContent'] = false;
    }
    else if($("#accountActivationPwd").val().length < 8)
    {
        $("#accountActivationPwdMsg").css("color", "red");
        message = 'Password must be at least 8 chars long';
        enableAccountConditionsArray['passwordContent'] = false;
    }
    else
    {
        if((/\d/.test($("#accountActivationPwd").val())) && (/\D/.test($("#accountActivationPwd").val())))
        {
            $("#accountActivationPwdMsg").css("color", "rgba(0, 162, 211, 1)");
            message = 'Ok';
            enableAccountConditionsArray['passwordContent'] = true;
        }
        else
        {
            $("#accountActivationPwdMsg").css("color", "red");
            message = 'Password must contain at least 1 digit and 1 char';
            enableAccountConditionsArray['passwordContent'] = false;
        }
    }
    
    checkPasswordConfirm();
    $("#accountActivationPwdMsg").html(message);
}

function checkPasswordConfirm()
{
    var message = null;
    
    if(enableAccountConditionsArray['passwordContent'] === false)
    {
        message = 'Password not confirmable until it\'s not valid';
        enableAccountConditionsArray['passwordConfirm'] = false;
        $("#accountActivationConfirmPwdMsg").css("color", "red");
    }
    else
    {
        if($("#accountActivationConfirmPwd").val() === $("#accountActivationPwd").val())
        {
            message = 'Ok';
            enableAccountConditionsArray['passwordConfirm'] = true;
            $("#accountActivationConfirmPwdMsg").css("color", "rgba(0, 162, 211, 1)");
        }
        else
        {
            message = 'Password and confirmation value don\'t match';
            enableAccountConditionsArray['passwordConfirm'] = false;
            $("#accountActivationConfirmPwdMsg").css("color", "red");
        }
    }
    
    $("#accountActivationConfirmPwdMsg").html(message);
}


function checkPasswordEditAccount()
{
    var message = null;
    
    if($("#accountPassword").val().length === 0)
    {
        $("#accountPasswordMsg").css("color", "red");
        message = 'Password is mandatory';
        editAccountConditionsArray['passwordContent'] = false;
    }
    else if($("#accountPassword").val().length < 8)
    {
        $("#accountPasswordMsg").css("color", "red");
        message = 'Password must be at least 8 chars long';
        editAccountConditionsArray['passwordContent'] = false;
    }
    else
    {
        if((/\d/.test($("#accountPassword").val())) && (/\D/.test($("#accountPassword").val())))
        {
            $("#accountPasswordMsg").css("color", "rgba(0, 162, 211, 1)");
            message = 'Ok';
            editAccountConditionsArray['passwordContent'] = true;
        }
        else
        {
            $("#accountPasswordMsg").css("color", "red");
            message = 'Password must contain at least 1 digit and 1 char';
            editAccountConditionsArray['passwordContent'] = false;
        }
    }
    
    checkPasswordConfirmEditAccount();
    $("#accountPasswordMsg").html(message);
}

//Controllo di coerenza fra password e conferma password
function checkPasswordConfirmEditAccount()
{
    var message = null;
    
    if(editAccountConditionsArray['passwordContent'] === false)
    {
        message = 'Password not confirmable until it\'s not valid';
        editAccountConditionsArray['passwordConfirm'] = false;
        $("#accountPasswordConfirmationMsg").css("color", "red");
    }
    else
    {
        if($("#accountPasswordConfirmation").val() === $("#accountPassword").val())
        {
            message = 'Ok';
            editAccountConditionsArray['passwordConfirm'] = true;
            $("#accountPasswordConfirmationMsg").css("color", "rgba(0, 162, 211, 1)");
        }
        else
        {
            message = 'Password and confirmation value don\'t match';
            editAccountConditionsArray['passwordConfirm'] = false;
            $("#accountPasswordConfirmationMsg").css("color", "red");
        }
    }
    
    $("#accountPasswordConfirmationMsg").html(message);
}

function editAccount(username)
{
    var accountJson = {
        username: username,
        password: $("#accountPassword").val(),
        firstName: $("#accountFirstName").val(),
        lastName: $("#accountLastName").val(),
        organization: $("#accountOrganization").val(),
        email: $("#accountEmail").val()
    };
    
     $.ajax({
        url: "editUser.php",
        data:{operation: "updateAccountFromAccountPage", accountJson: JSON.stringify(accountJson)},
        type: "POST",
        async: true,
        success: function (data) 
        {
          console.log(data);
          if(data == "1")
          {
             $("#editAccountOkModal").modal('show');
             setTimeout(function(){
               $("#editAccountOkModal").modal('hide');
               setTimeout(function()
               {
                  location.reload();
               }, 500);
             }, 2000);
          }
          else
          {
             $("#editAccountKoModal").modal('show');
             setTimeout(function(){
               $("#editAccountKoModal").modal('hide');
             }, 2000);
          }
        },
        error: function (data)
        {
           console.log("Update account data KO");
           console.log(data);
           
           $("#editAccountKoModal").modal('show');
           setTimeout(function(){
              $("#editAccountKoModal").modal('hide');
           }, 2000);
        }
     });
}

