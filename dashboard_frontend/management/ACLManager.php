<?php
/* Dashboard Builder.
  Copyright (C) 2025 DISIT Lab https://www.disit.org - University of Florence

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


/*TLDR: get data from editACL.php (+ dashboardUserControllers.php for org list), list in datatables, buttons to edit and delete
Routes used:
get_list_AD,add_AD,edit_AD,delete_AD
*/


include('process-form.php');
header("Cache-Control: private, max-age=$cacheControlMaxAge");

session_start();
//checkSession('RootAdmin');
//$_SESSION['loggedRole'] = "RootAdmin";

$link = mysqli_connect($host, $username, $password);
mysqli_select_db($link, $dbname);
error_reporting(E_ERROR);

?>
<!DOCTYPE HTML>
<html>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>ACL Manager</title>

    <!-- Bootstrap Core CSS -->
    <link href="../css/bootstrap.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/styles_gridster.css" type="text/css" />
    <link rel="stylesheet" href="../css/style_widgets.css?v=<?php echo time(); ?>" type="text/css" />
    <link href="../css/bootstrap-colorpicker.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/chat.css" type="text/css" />
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Cabin:400,500,600,700|Catamaran|Varela+Round" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Montserrat" rel="stylesheet">
    <!-- jQuery -->
    <script src="../js/jquery-2.2.4.min.js"></script>
    <!-- CKEditor -->
    <script src="../js/ckeditor/ckeditor.js"></script>
    <link rel="stylesheet" href="../js/ckeditor/skins/moono/editor.css">
    <!-- Bootstrap Core JavaScript -->
    <script src="../js/bootstrap.min.js"></script>
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.0/css/select2.min.css">
    <!-- Font awesome icons -->
    <link rel="stylesheet" href="../js/fontAwesome/css/font-awesome.min.css">
    <!-- Custom CSS -->
    <link href="../css/dashboard.css?v=<?php echo time(); ?>" rel="stylesheet">
    <link href="../css/dashboardView.css?v=<?php echo time(); ?>" rel="stylesheet">
    <link href="../css/addWidgetWizard2.css?v=<?php echo time(); ?>" rel="stylesheet">
    <link href="../css/addDashboardTab.css?v=<?php echo time(); ?>" rel="stylesheet">
    <link href="../css/dashboard_configdash.css?v=<?php echo time(); ?>" rel="stylesheet">
    <link href="../css/widgetCtxMenu_1.css?v=<?php echo time(); ?>" rel="stylesheet">
    <link href="../css/widgetDimControls_1.css?v=<?php echo time(); ?>" rel="stylesheet">
    <link href="../css/widgetHeader_1.css?v=<?php echo time(); ?>" rel="stylesheet">
    <script src="../js/widgetsCommonFunctions.js?v=<?php echo time(); ?>" type="text/javascript" charset="utf-8"></script>
    <script src="../js/dashboard_configdash.js?v=<?php echo time(); ?>" type="text/javascript" charset="utf-8"></script>
    <script src="../widgets/trafficEventsTypes.js?v=<?php echo time(); ?>" type="text/javascript" charset="utf-8"></script>
    <script src="../widgets/alarmTypes.js?v=<?php echo time(); ?>" type="text/javascript" charset="utf-8"></script>
    <script src="../widgets/fakeGeoJsons.js?v=<?php echo time(); ?>" type="text/javascript" charset="utf-8"></script>
    <link href="../css/chat.css?v=<?php echo time(); ?>" rel="stylesheet">
    <script src="../js/bootstrap-ckeditor-.js?v=<?php echo time(); ?>" type="text/javascript" charset="utf-8"></script>
   <!-- DataTables -->
    <script type="text/javascript" charset="utf8" src="../js/DataTables/datatables.js"></script>
    <link rel="stylesheet" type="text/css" href="../js/DataTables/datatables.css">
    <script type="text/javascript" charset="utf8" src="../js/DataTables/dataTables.bootstrap.min.js"></script>
    <script type="text/javascript" charset="utf8" src="../js/DataTables/dataTables.responsive.min.js"></script>
    <script type="text/javascript" charset="utf8" src="../js/DataTables/responsive.bootstrap.min.js"></script>
    <link rel="stylesheet" type="text/css" href="../css/DataTables/dataTables.bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="../css/DataTables/responsive.bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="../css/DataTables/jquery.dataTables.min.css">
    <script type="text/javascript" charset="utf8" src="../js/DataTables/Select-1.2.5/js/dataTables.select.min.js"></script>
    <link rel="stylesheet" type="text/css" href="../js/DataTables/Select-1.2.5/css/select.dataTables.min.css">
</head>
<style type="text/css">
    .dashboardsTableHeader{
        background-color: #337ab7;
        color: white;
    }
    .has-error .form-control {
    border-color: #a94442;
    box-shadow: inset 0 1px 1px rgba(0,0,0,.075);
    }
</style>
<body style="overflow-y: hidden !important">
<?php include "../cookie_banner/cookie-banner.php"; ?>
<div class="modal-content modalContentWizardForm">
    <div id="addWidgetWizardLabelBody" class="body">
        <div id="select_element_type" style="margin-left: 5%; margin: 2%;  float: left">
            <button type="button" class="btn btn-warning new_org" id="new_ACD_button" data-toggle="modal" data-target="#myModal_new" style="float:left; margin-right: 5px;">
                <i class="fa fa-plus"></i> 
                Create New Access Control
            </button>
        </div>
        <div id="table_div" style="margin-left: 5%; margin-right: 5%">
            <table id="value_table" class="table table-striped table-bordered" style="width: 100%">
                <thead class="dashboardsTableHeader">
                    <th>ID</th>
                    <th>Access name</th>
                    <th>Possible organizations</th>
                    <th>Menu ID</th>
                    <th>Actions</th>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- New Modal -->
<div class="modal fade" id="myModal_new" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" id="button_conf_new">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h5 class="modal-title" id="exampleModalLabel"><?= _("New ACL Definition") ?></h5>
            </div>
            <div class="modal-body" id="myModalBody">
                <div class="input-group"><span class="input-group-addon"><?= _("Access name") ?>:</span><input id="access_name" type="text" placeholder="" class="form-control"></div><br />
                <div class="input-group"><span class="input-group-addon"><?= _("Menu ID") ?>:</span><input id="menu_id" type="text" placeholder="" class="form-control"></div><br />
                <span class="input-group-addon"><?= _("Possible organizations") ?></span>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn cancelBtn" id="cancel_btn" data-dismiss="modal"><?= _("Cancel") ?></button>
                <button type="button" class="btn btn-primary" id="conf_new"><?= _("Confirm") ?></button>
            </div>
        </div>
    </div>
</div>

<!-- Edit Modal -->
<div class="modal fade" id="myModal_edit" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" id="button_conf_edit_header">
          <span aria-hidden="true">&times;</span>
        </button>
        <h5 class="modal-title" id="editModalLabel"><?= _("Edit ACL Definition") ?></h5>
      </div>
      <div class="modal-body" id="myModalBody_edit">
        <input type="hidden" id="original_id">
        <input type="hidden" id="original_name">
        <input type="hidden" id="original_menu_id">
        <input type="hidden" id="original_orgs">
        <div class="input-group"><span class="input-group-addon"><?= _("Access name") ?>:</span><input id="edit_access_name" type="text" class="form-control"></div><br />
        <div class="input-group"><span class="input-group-addon"><?= _("Menu ID") ?>:</span><input id="edit_menu_id" type="text" class="form-control"></div><br />
        <span class="input-group-addon"><?= _("Possible organizations") ?></span>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn cancelBtn" id="button_cancel_edit_footer" data-dismiss="modal"><?= _("Cancel") ?></button>
        <button type="button" class="btn btn-primary" id="conf_edit"><?= _("Confirm") ?></button>
      </div>
    </div>
  </div>
</div>

</body>
<script type='text/javascript'>
    async function get_AD_list(build){
        $.ajax({
            url: 'editACL.php',
            type: 'POST',
            dataType: 'json',
            async: true,
            data: { action: 'get_list_AD' }
        })
        .done(function(data) {
            if(build){
                build_AD_table(data);
            }
        });
    }

    async function build_AD_table(list){
        console.log(list);
        for (var i = 0; i < list.length; i++) {
            $('#value_table').append(
                '<tr>' +
                  '<td>' + list[i]['ID'] + '</td>' +
                  '<td>' + list[i]['authname'] + '</td>' +
                  '<td>' + list[i]['org'] + '</td>' +
                  '<td>' + list[i]['menuID'] + '</td>' +
                  '<td>' +
                    '<button class="btn btn-sm btn-primary edit-btn" ' +
                      'data-id="' + list[i]['ID'] + '" ' +
                      'data-name="' + list[i]['authname'] + '" ' +
                      'data-org="' + list[i]['org'] + '" ' +
                      'data-menuid="' + list[i]['menuID'] + '">Edit</button> ' +
                    '<button class="btn btn-sm btn-danger delete-btn" ' +
                      'data-id="' + list[i]['ID'] + '">Delete</button>' +
                  '</td>' +
                '</tr>'
            );
        }
        $('#value_table').DataTable({
            "searching": true,
            "paging": true,
            "ordering": true,
            "info": false,
            "responsive": true,
            "lengthMenu": [5, 10, 20, 50],
            "iDisplayLength": 5,
            "pagingType": "full_numbers",
            "dom": '<"pull-left"l><"pull-right"f>tip',
            "language": {"paginate": {
                    "first": "First",
                    "last": "Last",
                    "next": "Next >>",
                    "previous": "<< Prev"
                },
                "lengthMenu": "Show _MENU_ "
            }
        });
    }

    function org_list_checkboxes(data){
        console.log(data);
        if ($('#myModalBody .OrgCheckbox').length > 0) {
            return;
        }
        var br = "<br />";
        var allorg = `<div class="input-group"><span class="input-group-addon">*</span> ` +
                      `<input class="OrgCheckbox" type="checkbox" value="*" /></div>`;
        $('#myModalBody').append(allorg);
        for(var i = 0; i < data.length; i++){
            var box = `<div class="input-group"><span class="input-group-addon">${data[i]}:</span> ` +
                      `<input class="OrgCheckbox" type="checkbox" value="${data[i]}" /></div>`;
            $('#myModalBody').append(box);
            if(i % 3 == 0 && i > 0) { $('#myModalBody').append(br); }
        }
    }

    function org_list_checkboxes_edit(data){
        if ($('#myModalBody_edit .OrgCheckboxEdit').length > 0) {
            return;
        }
        var br = "<br />";
        var allorg = `<div class="input-group"><span class="input-group-addon">*</span> ` +
                      `<input class="OrgCheckboxEdit" type="checkbox" value="*" /></div>`;
        $('#myModalBody_edit').append(allorg);
        for(var i = 0; i < data.length; i++){
            var box = `<div class="input-group"><span class="input-group-addon">${data[i]}:</span> ` +
                      `<input class="OrgCheckboxEdit" type="checkbox" value="${data[i]}" /></div>`;
            $('#myModalBody_edit').append(box);
            if(i % 3 == 0 && i > 0) { $('#myModalBody_edit').append(br); }
        }
    }

    $(document).ready(function () {
        var cachedOrgs = null;

        get_AD_list(true);

        // New modal handlers
        $('#button_conf_new').click(function () {
            $('#myModal_new').modal('hide');
        });
        $('#new_ACD_button').click(function () {
            if (!cachedOrgs) {
                $.ajax({
                    url: "dashboardUserControllers.php",
                    data: { action: "list_org" },
                    type: "GET",
                    dataType: "json"
                })
                .done(function (jdata) {
                    cachedOrgs = jdata;
                    org_list_checkboxes(jdata);
                });
            } else {
                org_list_checkboxes(cachedOrgs);
            }
        });
        $('#conf_new').click(function () {
            var name    = $('#access_name').val(),
                menu_id = $('#menu_id').val();
            var orgs = $('.OrgCheckbox:checked').map(function() {
                return this.value;
            }).get();
            if(!name){
                $('#access_name').closest('.input-group').addClass('has-error');
                return;
            }
            $('#access_name').closest('.input-group').removeClass('has-error');
            $.ajax({
                url: 'editACL.php',
                type: 'POST',
                dataType: 'json',
                data: { 
                    action: "add_AD",
                    name: name,
                    menu_id: menu_id,
                    orgs: orgs
                },
            })
            .done(function () {
                $('#access_name, #menu_id').val('');
                $('.OrgCheckbox').prop('checked', false);
                if ($.fn.DataTable.isDataTable('#value_table')) {
                    $('#value_table').DataTable().clear().destroy();
                }
                $('#value_table tbody').empty();
                get_AD_list(true);
                $('#myModal_new').modal('hide');
            });
        });
        $('#cancel_btn').click(function () {
            $('#access_name').closest('.input-group').removeClass('has-error');
        });

        // Edit modal handlers
        $('#button_conf_edit_header').click(function () {
            $('#myModal_edit').modal('hide');
        });
        $('#button_cancel_edit_footer').click(function () {
            $('#edit_access_name').closest('.input-group').removeClass('has-error');
        });
        $(document).on('click', '.edit-btn', function(){
            var id     = $(this).data('id'),
                name   = $(this).data('name'),
                org    = $(this).data('org'),
                menuid = $(this).data('menuid');

            $('#original_id').val(id);
            $('#original_name').val(name);
            $('#original_orgs').val(org);
            $('#original_menu_id').val(menuid);

            $('#edit_access_name').val(name);
            $('#edit_menu_id').val(menuid);

            if (!cachedOrgs) {
                $.ajax({
                    url: "dashboardUserControllers.php",
                    data: { action: "list_org" },
                    type: "GET",
                    dataType: "json"
                })
                .done(function (jdata) {
                    cachedOrgs = jdata;
                    org_list_checkboxes_edit(jdata);
                    $('.OrgCheckboxEdit').prop('checked', false);
                    var selected = org ? org.split(',') : [];
                    selected.forEach(function(val){
                        $('.OrgCheckboxEdit[value="'+val+'"]').prop('checked', true);
                    });
                    $('#myModal_edit').modal('show');
                });
            } else {
                org_list_checkboxes_edit(cachedOrgs);
                $('.OrgCheckboxEdit').prop('checked', false);
                var selected = org ? org.split(',') : [];
                selected.forEach(function(val){
                    $('.OrgCheckboxEdit[value="'+val+'"]').prop('checked', true);
                });
                $('#myModal_edit').modal('show');
            }
        });
        $('#conf_edit').click(function () {
            var id            = $('#original_id').val(),
                original_name = $('#original_name').val(),
                name          = $('#edit_access_name').val(),
                menu_id       = $('#edit_menu_id').val();
            var orgs = $('.OrgCheckboxEdit:checked').map(function() {
                return this.value;
            }).get();
            if(!name){
                $('#edit_access_name').closest('.input-group').addClass('has-error');
                return;
            }
            $('#edit_access_name').closest('.input-group').removeClass('has-error');
            $.ajax({
                url: 'editACL.php',
                type: 'POST',
                dataType: 'json',
                data: {
                    action:        "edit_AD",
                    id:            id,
                    original_name: original_name,
                    name:          name,
                    menu_id:       menu_id,
                    orgs:          orgs
                }
            })
            .done(function () {
                $('#edit_access_name, #edit_menu_id').val('');
                $('.OrgCheckboxEdit').prop('checked', false);
                if ($.fn.DataTable.isDataTable('#value_table')) {
                    $('#value_table').DataTable().clear().destroy();
                }
                $('#value_table tbody').empty();
                get_AD_list(true);
                $('#myModal_edit').modal('hide');
            });
        });

        // Delete handler
        $(document).on('click', '.delete-btn', function(){
            var id = $(this).data('id');
            if (confirm('Are you sure you want to delete this access definition?')) {
                $.ajax({
                    url: 'editACL.php',
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        action: "delete_AD",
                        id:     id
                    }
                })
                .done(function () {
                    if ($.fn.DataTable.isDataTable('#value_table')) {
                        $('#value_table').DataTable().clear().destroy();
                    }
                    $('#value_table tbody').empty();
                    get_AD_list(true);
                });
            }
        });
        $(document).on('change', '.OrgCheckboxEdit', function() {
        //if *
        if ($(this).val() === '*' && $(this).is(':checked')) {
            $('.OrgCheckboxEdit').not(this).prop('checked', false);
        }
        //elif * on and another checked
        else if ($(this).val() !== '*' && $(this).is(':checked')) {
            $('.OrgCheckboxEdit[value="*"]').prop('checked', false);
        }
        });
        $(document).on('change', '.OrgCheckbox', function() {
        //if *
        if ($(this).val() === '*' && $(this).is(':checked')) {
            $('.OrgCheckbox').not(this).prop('checked', false);
        }
        //elif * on and another checked
        else if ($(this).val() !== '*' && $(this).is(':checked')) {
            $('.OrgCheckbox[value="*"]').prop('checked', false);
        }
        });
    });
</script>
</html>
