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
checkSession('RootAdmin');
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
            <button type="button" class="btn btn-primary new_org" id="new_ACD_button" data-toggle="modal" data-target="#myModal_new" style="float:left; margin-right: 5px;">
                <i class="fa fa-plus"></i> 
                Create New Access Control
            </button>
            <button type="button" class="btn btn-info new_org" id="show_Profile_button" data-toggle="modal" data-target="#profileModal_show" style="float:left; margin-right: 5px;">
                <i class="fa fa-plus"></i> 
                Show ACL Profiles
            </button>
            <button type="button" class="btn btn-secondary new_org" id="new_Profile_button" data-toggle="modal" data-target="#profileModal_new" style="float:left; margin-right: 5px;">
                <i class="fa fa-plus"></i> 
                Create New ACL Profile
            </button>
        </div>
        <div id="table_div" style="margin-left: 5%; margin-right: 5%">
            <table id="value_table" class="table table-striped table-bordered" style="width: 100%">
                <thead class="dashboardsTableHeader">
                    <th>ID</th>
                    <th>AC name</th>
                    <th>Possible organizations</th>
                    <th>Menu ID</th>
                    <th>Dashboard ID</th>
                    <th>Collection ID</th>
                    <th>Max/Day</th>
                    <th>Max/Month</th>
                    <th>Max Total</th>
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
                <button type="button" class="btn btn-primary show-menus-btn" id="show_menus1"><?= _("Show list of menus+IDs") ?></button><br />
                <div class="input-group">
                    <span class="input-group-addon">Dashboard ID:</span>
                    <input id="dashboard_id" type="text" class="form-control">
                </div><br />
                <div class="input-group">
                    <span class="input-group-addon">Collection ID:</span>
                    <input id="collection_id" type="text" class="form-control">
                </div><br />
                <div class="input-group">
                    <span class="input-group-addon">Max/Day:</span>
                    <input id="max_by_day" type="number" class="form-control">
                </div><br />
                <div class="input-group">
                    <span class="input-group-addon">Max/Month:</span>
                    <input id="max_by_month" type="number" class="form-control">
                </div><br />
                <div class="input-group">
                    <span class="input-group-addon">Max/Total:</span>
                    <input id="max_total" type="number" class="form-control">
                </div><br />
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
        <input type="hidden" id="original_dashboard_id">
        <input type="hidden" id="original_collection_id">
        <input type="hidden" id="original_maxbyday">
        <input type="hidden" id="original_maxbymonth">
        <input type="hidden" id="original_maxtotal">
        <div class="input-group"><span class="input-group-addon"><?= _("Access name") ?>:</span><input id="edit_access_name" type="text" class="form-control" disabled></div><br />
        <div class="input-group"><span class="input-group-addon"><?= _("Menu ID") ?>:</span><input id="edit_menu_id" type="text" class="form-control"></div><br />
        <button type="button" class="btn btn-primary show-menus-btn" id="show_menus2"><?= _("Show list of menus+IDs") ?></button><br />
        <div class="input-group">
            <span class="input-group-addon">Dashboard ID:</span>
            <input id="edit_dashboard_id" type="text" class="form-control">
        </div><br />
        <div class="input-group">
            <span class="input-group-addon">Collection ID:</span>
            <input id="edit_collection_id" type="text" class="form-control">
        </div><br />
        <div class="input-group">
            <span class="input-group-addon">Max/Day:</span>
            <input id="edit_max_by_day" type="number" class="form-control">
        </div><br />
        <div class="input-group">
            <span class="input-group-addon">Max/Month:</span>
            <input id="edit_max_by_month" type="number" class="form-control">
        </div><br />
        <div class="input-group">
            <span class="input-group-addon">Max/Total:</span>
            <input id="edit_max_total" type="number" class="form-control">
        </div><br />
        <span class="input-group-addon"><?= _("Possible organizations") ?></span>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn cancelBtn" id="button_cancel_edit_footer" data-dismiss="modal"><?= _("Cancel") ?></button>
        <button type="button" class="btn btn-primary" id="conf_edit"><?= _("Confirm") ?></button>
      </div>
    </div>
  </div>
</div>

<!-- Menu list Modal -->
<div class="modal fade" id="menuListModal" tabindex="-1" role="dialog" aria-labelledby="menuListModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="menuListModalLabel">Available Menus</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <!--Search bar -->
        <input type="text" class="form-control" id="menuSearchInput" placeholder="Search menus..." autocomplete="off">
        <div id="menuListContainer" style="max-height: 300px; overflow-y: auto; margin-top: 10px;">
          <p class="text-center text-muted">Loading menus…</p>
        </div>
      </div> 
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">
          Close
        </button>
      </div>
    </div>
  </div> 
</div>
<!-- Error Modal -->
<div class="modal fade" id="errorModal" tabindex="-1" role="dialog" aria-labelledby="errorModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="errorModalLabel">Error</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body" id="errorModalBody"></div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
<!-- Show ACL Profiles Modal -->
<div class="modal fade" id="profileModal_show" tabindex="-1" role="dialog" aria-labelledby="profileShowLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="profileShowLabel">ACL Profiles</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <table id="profiles_table" class="table table-striped table-bordered" style="width:100%">
          <thead class="dashboardsTableHeader">
            <tr>
              <th>ID</th>
              <th>Profile Name</th>
              <th>Included ACLs</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
          </tbody>
        </table>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<!-- New ACL Profile Modal -->
<div class="modal fade" id="profileModal_new" tabindex="-1" role="dialog" aria-labelledby="profileNewLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="profileNewLabel">Create New ACL Profile</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close" id="button_cancel_profile_new">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="input-group">
          <span class="input-group-addon">Profile Name:</span>
          <input id="new_profile_name" type="text" class="form-control">
        </div>
        <br />
        <div id="new_profile_acl_list" class="form-group">
          <div id="new_profile_acl_container" 
          style="max-height: 250px; overflow-y: auto; padding-right: 8px;">
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal" id="cancel_profile_new">Cancel</button>
        <button type="button" class="btn btn-primary" id="confirm_profile_new">Create Profile</button>
      </div>
    </div>
  </div>
</div>

<!-- Edit ACL Profile Modal -->
<div class="modal fade" id="profileModal_edit" tabindex="-1" role="dialog" aria-labelledby="profileEditLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="profileEditLabel">Edit ACL Profile</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close" id="button_cancel_profile_edit_header">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <input type="hidden" id="edit_profile_id">
        <div class="input-group">
          <span class="input-group-addon">Profile Name:</span>
          <input id="edit_profile_name" type="text" class="form-control" disabled>
        </div>
        <br />
        <div id="edit_profile_acl_list" class="form-group">
          <label>Included ACL Definitions:</label>
          <div id="edit_profile_acl_container" 
          style="max-height: 250px; overflow-y: auto; padding-right: 8px;">
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal" id="button_cancel_profile_edit_footer">Cancel</button>
        <button type="button" class="btn btn-primary" id="confirm_profile_edit">Save Changes</button>
      </div>
    </div>
  </div>
</div>
</body>
<script type='text/javascript'>
    var menus = null;
    let allACLDefs = [];
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
            allACLDefs = data; 
        });
    }
    async function get_menu_list(){
        $.ajax({
            url: 'editACL.php',
            type: 'POST',
            dataType: 'json',
            async: true,
            data: { action: 'get_list_menus' }
        })
        .done(function(data) {
            menus = data;
            console.log(data)
        });
    }
    function showErrorModal(msg) {
    $('#errorModalBody').text(msg);
    $('#errorModal').appendTo(document.body).modal('show');
    }

    async function build_AD_table(list){
        console.log(list);
        for (var i = 0; i < list.length; i++) {
            $('#value_table tbody').append(
                '<tr>' +
                  '<td>' + list[i]['ID'] + '</td>' +
                  '<td>' + list[i]['authname'] + '</td>' +
                  '<td>' + list[i]['org'] + '</td>' +
                  '<td>' + list[i]['menuID'] + '</td>' +
                  '<td>' + list[i]['dashboardID'] + '</td>' +
                  '<td>' + list[i]['collectionID'] + '</td>' +
                  '<td>' + list[i]['maxbyday'] + '</td>' +
                  '<td>' + list[i]['maxbymonth'] + '</td>' +
                  '<td>' + list[i]['maxtotal'] + '</td>' +
                  '<td>' +
                    '<button class="btn btn-sm btn-primary edit-btn" ' +
                    'data-id="'            + list[i]['ID']            + '" '+
                    'data-name="'          + list[i]['authname']      + '" '+
                    'data-org="'           + list[i]['org']           + '" '+
                    'data-menuid="'        + list[i]['menuID']        + '" '+
                    'data-dashboardid="'   + list[i]['dashboardID']   + '" '+
                    'data-collectionid="'  + list[i]['collectionID']  + '" '+
                    'data-maxbyday="'      + list[i]['maxbyday']      + '" '+
                    'data-maxbymonth="'      + list[i]['maxbymonth']      + '" '+
                    'data-maxtotal="'    + list[i]['maxtotal']    + '">Edit</button>'+
                    //'<button class="btn btn-sm btn-danger delete-btn" ' +
                    //  'data-id="' + list[i]['ID'] + '">Delete</button>' +
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
            "iDisplayLength": 10,
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
    function showMenuListModal() {
    var $container = $('#menuListContainer');
    $('#menuSearchInput').val('');
    $container.empty();
    if (Array.isArray(menus) && menus.length > 0) {
      var $list = $('<ul class="list-group"></ul>');
      menus.forEach(function(item) {
        var listText = 'ID: ' + item.ID + '  –  ' + item.pageTitle;
        var $li = $('<li class="list-group-item"></li>').text(listText);
        $list.append($li);
      });
      $container.append($list);
    } else {
      $container.append('<p class="text-muted text-center">No menus available.</p>');
    }
    $('#menuListModal').modal('show');
  }
    $(document).ready(function () {
        var cachedOrgs = null;

        get_AD_list(true);
        get_menu_list();

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
            var dashboardID  = $('#dashboard_id').val(),
            collectionID = $('#collection_id').val(),
            maxbyday     = $('#max_by_day').val(),
            maxbymonth   = $('#max_by_month').val();
            maxtotal   = $('#max_total').val();
            $.ajax({
                url: 'editACL.php',
                type: 'POST',
                dataType: 'json',
                data: { 
                    action: "add_AD",
                    name: name,
                    menu_id: menu_id,
                    dashboard_id:  dashboardID,
                    collection_id: collectionID,
                    maxbyday:      maxbyday,
                    maxbymonth:    maxbymonth,
                    maxtotal:    maxtotal,
                    orgs: orgs
                },
            })
            .done(function (resp) {
                if (resp.error) {
                    showErrorModal(resp.error);
                    return;
                }
                $('#access_name, #menu_id').val('');
                $('.OrgCheckbox').prop('checked', false);
                if ($.fn.DataTable.isDataTable('#value_table')) {
                    $('#value_table').DataTable().clear().destroy();
                }
                $('#value_table tbody').empty();
                get_AD_list(true);
                $('#myModal_new').modal('hide');
            })
            .fail(function (xhr, status, err) {
                console.log(err)
                showErrorModal("Request failed: " + (xhr.responseText || status));
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


            var dashboardID  = $(this).data('dashboardid'),
            collectionID = $(this).data('collectionid'),
            maxbyday     = $(this).data('maxbyday'),
            maxbymonth   = $(this).data('maxbymonth');
            maxtotal   = $(this).data('maxtotal');

            $('#original_dashboard_id').val(dashboardID);
            $('#original_collection_id').val(collectionID);
            $('#original_maxbyday').val(maxbyday);
            $('#original_maxbymonth').val(maxbymonth);

            $('#edit_dashboard_id').val(dashboardID);
            $('#edit_collection_id').val(collectionID);
            $('#edit_max_by_day').val(maxbyday);
            $('#edit_max_by_month').val(maxbymonth);
            $('#edit_max_total').val(maxtotal);

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
            var dashboardID  = $('#edit_dashboard_id').val(),
            collectionID = $('#edit_collection_id').val(),
            maxbyday     = $('#edit_max_by_day').val(),
            maxbymonth   = $('#edit_max_by_month').val();
            maxtotal   = $('#edit_max_total').val();
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
                    dashboard_id:  dashboardID,
                    collection_id: collectionID,
                    maxbyday:      maxbyday,
                    maxbymonth:    maxbymonth,
                    maxtotal:      maxtotal,
                    orgs:          orgs
                }
            })
            .done(function (resp) {
                if (resp.error) {
                    showErrorModal(resp.error);
                    return;
                }
                $('#edit_access_name, #edit_menu_id').val('');
                $('.OrgCheckboxEdit').prop('checked', false);
                if ($.fn.DataTable.isDataTable('#value_table')) {
                    $('#value_table').DataTable().clear().destroy();
                }
                $('#value_table tbody').empty();
                get_AD_list(true);
                $('#myModal_edit').modal('hide');
            })
            .fail(function (xhr, status, err) {
                showErrorModal("Request failed: " + (xhr.responseText || status));
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
        $(document).on('click', '.show-menus-btn', function() {
        if (menus && Array.isArray(menus)) {
            //if data
            showMenuListModal();
            } else {
            //else fetch
            get_menu_list().done(function() {
                showMenuListModal();
            })
            .fail(function(xhr, status, err) {
                console.error("Unable to fetch menus:", err);
                alert("Error loading menu list. Please try again.");
            });
            }
        });
        $(document).on('input', '#menuSearchInput', function() {
            var query = $(this).val().toLowerCase();
            $('#menuListContainer ul li').each(function() {
            var text = $(this).text().toLowerCase();
            if (text.indexOf(query) !== -1) {
                $(this).show();
            } else {
                $(this).hide();
            }
            });
        });
        //ACL PROFILES
        $('#profileModal_show').on('show.bs.modal', function () {
            if ( $.fn.DataTable.isDataTable('#profiles_table') ) {
                $('#profiles_table').DataTable().clear().destroy();
            }
            $('#profiles_table tbody').empty();
            $.ajax({
                url: 'editACL.php',
                type: 'POST',
                dataType: 'json',
                data: { action: 'get_list_profiles' }
            })
            .done(function(profiles) {
                profiles.forEach(function(prof) {
                    // map IDs to names
                    var aclList = '';
                    if (prof.authIDs) {
                    var ids = prof.authIDs.split(',').filter(Boolean);
                    var names = ids.map(function(id){
                        var def = allACLDefs.find(function(d){
                        return d.ID.toString() === id;
                        });
                        return def ? def.authname : id;
                    });
                    aclList = names.join(', ');
                    }
                    $('#profiles_table tbody').append(
                    '<tr>' +
                        '<td>' + prof.ID + '</td>' +
                        '<td>' + prof.profilename + '</td>' +
                        '<td>' + aclList + '</td>' +
                        '<td>' +
                        '<button class="btn btn-sm btn-primary edit-profile-btn" ' +
                            'data-id="' + prof.ID + '" ' +
                            'data-name="' + prof.profilename + '" ' +
                            'data-aclids="' + prof.authIDs + '">' +
                            'Edit' +
                        '</button>' +
                        '</td>' +
                    '</tr>'
                    );
                });
                $('#profiles_table').DataTable({
                    searching:   true,
                    paging:      true,
                    ordering:    true,
                    info:        false,
                    responsive:  true,
                    lengthMenu:  [5, 10, 20, 50],
                    iDisplayLength: 10,
                    pagingType:  'full_numbers',
                    dom:         '<"pull-left"l><"pull-right"f>tip',
                    language: {
                    paginate: {
                        first:    'First',
                        last:     'Last',
                        next:     'Next >>',
                        previous: '<< Prev'
                    },
                    lengthMenu: 'Show _MENU_ '
                    }
                });
            })
            .fail(function(xhr, status, err) {
                showErrorModal('Failed to load ACL profiles: ' 
                + (xhr.responseText||status));
            });
        });
        $(document).on('click', '.edit-profile-btn', function() {
            const profileId   = $(this).data('id');
            const profileName = $(this).data('name');
            const existing    = $(this).data('aclids')
                                    .toString()
                                    .split(',')
                                    .filter(x=>x);
            $('#edit_profile_id').val(profileId);
            $('#edit_profile_name').val(profileName);
            $.when(get_AD_list(false)).done(function() {
                const list = $('#edit_profile_acl_container').empty();
                allACLDefs.forEach(def => {
                const checked = existing.includes(def.ID.toString()) ? 'checked' : '';
                const html = `
                    <div class="checkbox">
                    <label>
                        <input type="checkbox" class="acl-prof-def" 
                            value="${def.ID}" ${checked}>
                        ${def.authname}
                    </label>
                    </div>`;
                list.append(html);
                });
                $('#profileModal_edit').modal('show');
            });
        });
        $('#confirm_profile_edit').click(function() {
            const id      = $('#edit_profile_id').val();
            const aclIDs  = $('.acl-prof-def:checked').map(function(){
                return this.value;
            }).get();
            $.ajax({
                url: 'editACL.php',
                type: 'POST',
                dataType: 'json',
                data: {
                action:   'edit_profile',
                id:       id,
                authIDs:  aclIDs  // send as array
                }
            })
            .done(function(resp) {
                if (resp.error) return showErrorModal(resp.error);
                $('#profileModal_edit').modal('hide');
                if ( $('#profileModal_show').is(':visible') ) {
                $('#profileModal_show').trigger('show.bs.modal');
                }
            })
            .fail(function(xhr, status){
                showErrorModal("Failed to save profile: "+(xhr.responseText||status));
            });
        });
        $('#profileModal_new').on('show.bs.modal', function() {
            $('#new_profile_name').val('')
                .closest('.input-group').removeClass('has-error');
            $('#new_profile_acl_container').empty();
            $('#new_profile_acl_container')
                .append('<label>Select ACL Definitions:</label>');
            // load ACL defs then render
            $.when(get_AD_list(false)).done(function() {
                allACLDefs.forEach(function(def) {
                const html = `
                    <div class="checkbox">
                    <label>
                        <input type="checkbox" class="acl-new-def" value="${def.ID}">
                        ${def.authname}
                    </label>
                    </div>`;
                $('#new_profile_acl_container').append(html);
                });
            }).fail(function(xhr, status){
                showErrorModal('Failed to load ACL definitions: ' + (xhr.responseText||status));
            });
        });
        $('#confirm_profile_new').click(function() {
            const name   = $('#new_profile_name').val().trim();
            if (!name) {
                $('#new_profile_name').closest('.input-group')
                .addClass('has-error');
                return;
            }
            $('.input-group').removeClass('has-error');
            // collect checked ACL IDs
            const authIDs = $('.acl-new-def:checked')
                                .map(function(){ return this.value; })
                                .get();
            $.ajax({
                url: 'editACL.php',
                type: 'POST',
                dataType: 'json',
                data: {
                action:  'add_profile',
                name:    name,
                authIDs: authIDs
                }
            })
            .done(function(resp) {
                if (resp.error) {
                return showErrorModal(resp.error);
                }
                // success!
                $('#profileModal_new').modal('hide');
                if ($('#profileModal_show').is(':visible')) {
                $('#profileModal_show').trigger('show.bs.modal');
                }
            })
            .fail(function(xhr,status){
                showErrorModal('Failed to create profile: '+(xhr.responseText||status));
            });
            });

    });
</script>
</html>
