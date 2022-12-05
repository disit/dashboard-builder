<?php if(!$_SESSION['isPublic']) : ?>
		<nav class="user-menu" role="navigation">
			<ul class="user-related-menu">
			<!--	<li><a href="#">Test 1</a></li>
				<li><a href="#">Test 2</a></li>
				<li><a href="#">Test 3</a></li>
				<li><a href="#">Test 4</a></li>
				<li><a href="#">Test 5</a></li>
				<li><a href="#">Test 6</a></li>
				<li><a href="#">Test 7</a></li>
				<li><a href="#">Test 8</a></li> -->
                <?php
                    buildMenu($link, $domainId, "userprofileLink", "userProfile", $curr_lang);
                ?>
			</ul>
		</nav>
<?php endif; ?> 
<div class="login-panel">
	<div class="login-container">
  <?php if(!$_SESSION['isPublic']) : ?>                  
					  <div id="mainMenuUsrLog">
						  <?php if (($_SESSION['loggedOrganization'] == 'None' || $_SESSION['loggedOrganization'] == 'none')) {
							  if ($_SESSION['loggedUsername'] == 'roottooladmin1') {
								  echo "User: " . $_SESSION['loggedUsername'] . ", Org: " . $_SESSION['loggedOrganization'];
							  } else {
								  echo "User: " . $_SESSION['loggedUsername'] . ", Org: None";
							  }
						  } else {
							  echo "User: " . $_SESSION['loggedUsername'] . ", Org: " . $_SESSION['loggedOrganization'];
						  }?>
					  </div>
					  <div id="mainMenuUsrDetCnt">
						  <?php echo "Role: " . $_SESSION['loggedRole'] . ", Level: " . $_SESSION['loggedUserLevel']; ?>
					  </div>
					<!--  <div class="col-md-12 centerWithFlex" id="mainMenuUsrLogoutCnt">
						  Logout<br/>
					  </div>  -->
					  <div id="mainMenuUsrLogoutCnt">
						  <button type="button" id="mainMenuUsrLogoutBtn">logout</button>
						  
						  
					  </div>
  <?php else : ?>                 
				   <!--   <div class="col-md-12 centerWithFlex" id="mainMenuUsrLoginCnt">
						  <br/>Login<br/>
					  </div>  -->
					  <div id="mainMenuUsrLogoutCnt">
						  <button type="button" id="mainMenuUsrLoginBtn">login</button>
						  <?php 
						 //  if(strpos($localizationsRoles, "Public")){
							if ($localizationEnabled){
	   echo('<a href="#" id="mainMenuSelectLanguageBtn" style="font-size: 10px;"><img src="'.$flagicon.'" id="flagicon" alt="'.$curr_lang.'" style="padding:5px; height: 24px;  width: 31px;"></a>');
	   }
	   ?>
						  <script> </script>
					  </div>
  <?php endif; ?>                  
	</div>
	<div class="langSelector">
		<?php 
			if (strpos($localizationsRoles, $_SESSION['loggedRole'])) {
			  echo('<a href="#" id="mainMenuSelectLanguageBtn" style="font-size: 10px;"><img src="'.$flagicon.'" id="flagicon" alt="'.$_SESSION['lang'].'" style="padding:5px; height: 24px;  width: 31px;"></a>');
		  }
		  ?>
	</div>
	<div class="switchLegacy">
        <?php if(isset($_SESSION['loggedRole'])) {
            $stop_flag = 1;
        } ?>
        <a href="../management/index.php?switchNewLayout=false"><i class="fa-solid fa-reply"></i>Switch to Legacy<br>Layout</a>
    </div>
    <!-- <div class="switchLegacy"><a href="../switchLayout.php?switchNewLayout=true"><i class="fa-solid fa-reply"></i>Switch to legacy</a></div>   -->
	<?php if($_SESSION['loggedRole']) : ?>
		<div class="user-menu-btn"><i class="fa-solid fa-ellipsis-vertical"></i></div>
	<?php endif; ?> 
</div>
<script>
$('.user-menu-btn').click(function(){
	$(this).toggleClass("click");
	$('.user-menu').toggleClass("show");
 });

$('#mainMenuSelectLanguageBtn').click(function () {
    //alert("Confirmed");
    $(".select_lang").prop("checked", false);
    //
    //Ajax su translate//
    $('#translate-modal').modal('show');

    //
});

function select_lang(lang, role){
    //$(".select_lang").prop("checked", false);

    $.ajax({
        async: true,
        type: 'POST',
        url: 'setlocale.php',
        data: {
            lang: lang
        },
        success: function (data) {
            //alert(role);
            //location.reload();
            if ((role === null)||(role === "")||(role === undefined)){
                //location.search = location.search.replace(/lang=[^&$]*/i, 'lang='+lang);
                //window.location.search = jQuery.query.set("lang", lang);
                //window.location.search += '&lang='+lang;
                // window.location.search;
                // Construct URLSearchParams object instance from current URL querystring.
                var queryParams = new URLSearchParams(window.location.search);
                // Set new or modify existing parameter value.
                queryParams.set("lang",lang);
                // Replace current querystring with the new one.
                history.replaceState(null, null, "?"+queryParams.toString());
                location.reload();
            }else{
                location.reload();
            }
        }
    });
}

  </script>

<?php
//if (strpos($localizationsRoles, $_SESSION['loggedRole'])) {
if ((strpos($localizationsRoles, $_SESSION['loggedRole']))||  ($localizationEnabled)) {
    $control_role = 'true';
    $obj = json_decode($localizations, true);
    $languages = $obj['languages'];
    $tot_leng = count($languages);
    if ($tot_leng > 0){
        echo ('<div id="translate-modal" class="modal fade bd-example-modal-sm" role="dialog">
        <div class="modal-dialog modal-sm" role="document">
    <div class="modal-content changeLanguage-modal">
        <div class="modal-body">
            <button type="button" class="close" data-dismiss="modal">&times;</button>');
        for($i=0; $i<$tot_leng; $i++){
            $lang = $obj['languages'][$i];
            echo('<input class="form-check-input select_lang" type="checkbox" value="col-md-12 mainMenuItemCnt mainMenuItemCntActive" onclick="select_lang(\''.$lang['code'].'\',\''.$_SESSION['loggedRole'].'\')"> <img src="../img/flagicons/'.$lang['code'].'.png" alt="alternatetext" style="padding:5px; height: 24px; width: 31px;"> <span>'.$lang['lang'].'</span><br />');
        }
        echo ('</div>                      
    </div>
  </div>
    </div>');
    }
}
?>