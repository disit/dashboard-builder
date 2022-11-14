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
  </script>