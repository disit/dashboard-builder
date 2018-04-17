<div class="hidden-xs hidden-sm col-md-2" id="mainMenuCnt">
    <div id="headerClaimCnt" class="col-md-12 centerWithFlex">Snap4City</div>
    <div class="col-md-12 mainMenuUsrCnt">
        <div class="row">
            <div class="col-md-12 centerWithFlex" id="mainMenuIconCnt">
                <img src="../img/mainMenuIcons/user.ico" />
            </div>
            <div class="col-md-12 centerWithFlex" id="mainMenuUsrCnt">
                <?php echo $_SESSION['loggedUsername']; ?>
            </div>
            <div class="col-md-12 centerWithFlex" id="mainMenuUsrDetCnt">
                <?php echo $_SESSION['loggedRole'] . " | " . $_SESSION['loggedType']; ?>
            </div>
            <div class="col-md-12 centerWithFlex" id="mainMenuUsrLogoutCnt">
                Logout
            </div>
        </div>
    </div>
    
    <?php
        include 'config.php';
        
        error_reporting(E_ERROR | E_NOTICE);
        date_default_timezone_set('Europe/Rome');
        
        $link = mysqli_connect($host, $username, $password);
        mysqli_select_db($link, $dbname);
        
        $menuQuery = "SELECT * FROM Dashboard.MainMenu ORDER BY id ASC";
        $r = mysqli_query($link, $menuQuery);

        if($r)
        {
            while($row = mysqli_fetch_assoc($r))
            {
                $menuItemId = $row['id'];
                $linkUrl = $row['linkUrl'];
                
                if($linkUrl == 'submenu')
                {
                    $linkUrl = '#';
                }
                
                $linkId = $row['linkId'];
                $icon = $row['icon'];  
                $text = $row['text'];
                $privileges = $row['privileges'];      
                $userType = $row['userType']; 
                $externalApp = $row['externalApp'];
                $openMode = $row['openMode'];
                $iconColor = $row['iconColor'];
                $pageTitle = $row['pageTitle'];
                $externalApp = $row['externalApp'];
                
                if($externalApp == 'yes')
                {
                    if($openMode == 'newTab')
                    {
                        $newItem =  '<a href="' . $linkUrl . '" id="' . $linkId . '" data-externalApp="' . $externalApp . '" data-openMode="' . $openMode . '" data-linkUrl="' . $linkUrl . '" data-pageTitle="' . $pageTitle . '" data-submenuVisible="false" class="internalLink moduleLink mainMenuLink" target="_blank">' .
                                        '<div class="col-md-12 mainMenuItemCnt">' .
                                            '<i class="' . $icon . '" style="color: ' . $iconColor . '"></i>&nbsp;&nbsp;&nbsp;' . $text .
                                        '</div>' .
                                    '</a>';
                    }
                    else
                    {
                        //CASO IFRAME
                        $newItem =  '<a href="' . $linkUrl . '" id="' . $linkId . '" data-externalApp="' . $externalApp . '" data-openMode="' . $openMode . '" data-linkUrl="' . $linkUrl . '" data-pageTitle="' . $pageTitle . '" data-submenuVisible="false" class="internalLink moduleLink mainMenuLink mainMenuIframeLink">' .
                                        '<div class="col-md-12 mainMenuItemCnt">' .
                                            '<i class="' . $icon . '" style="color: ' . $iconColor . '"></i>&nbsp;&nbsp;&nbsp;' . $text .
                                        '</div>' .
                                    '</a>';
                    }
                }
                else
                {
                    $newItem =  '<a href="' . $linkUrl . '" id="' . $linkId . '" data-externalApp="' . $externalApp . '" data-openMode="' . $openMode . '" data-linkUrl="' . $linkUrl . '" data-pageTitle="' . $pageTitle . '" data-submenuVisible="false" class="internalLink moduleLink mainMenuLink">' .
                                    '<div class="col-md-12 mainMenuItemCnt">' .
                                        '<i class="' . $icon . '" style="color: ' . $iconColor . '"></i>&nbsp;&nbsp;&nbsp;' . $text .
                                    '</div>' .
                                '</a>';
                }
                
                if((strpos($privileges, $_SESSION['loggedRole']) !== false)&&(($userType == 'any')||(($userType != 'any')&&($userType == $_SESSION['loggedType'])))) 
                {
                    echo $newItem;
                }
                
                $submenuQuery = "SELECT * FROM Dashboard.MainMenuSubmenus WHERE menu = '$menuItemId' ORDER BY id ASC";
                $r2 = mysqli_query($link, $submenuQuery);
                
                if($r2)
                {
                    while($row2 = mysqli_fetch_assoc($r2))
                    {
                        $linkUrl2 = $row2['linkUrl'];
                        $linkId2 = $row2['linkId'];
                        $icon2 = $row2['icon'];        
                        $text2 = $row2['text'];
                        
                        $newSubmenuItem =  '<a href="' . $linkUrl2 . '" id="' . $linkId2 . '" data-fatherMenuId="' . $linkId . '" class="internalLink moduleLink mainMenuSubItemLink">' .
                                                '<div class="col-md-12 mainMenuSubItemCnt">' .
                                                    '&nbsp;&nbsp;&nbsp;<i class="' . $icon2 . '"></i>&nbsp;&nbsp;&nbsp;' . $text2 .
                                                '</div>' .
                                            '</a>';
                        
                        echo $newSubmenuItem;
                    }
                }
            }
            
        }
    ?>
    
    
    
    
    
</div>

<script type='text/javascript'>
    $(document).ready(function () 
    {
        $('div.mainMenuUsrCnt').hover(function(){
            $(this).css("background", "rgba(0, 162, 211, 1)");
            $(this).css("cursor", "pointer");
            $('#mainMenuUsrDetCnt').hide();
            $('#mainMenuUsrLogoutCnt').show();
        }, function(){
            $(this).css("background", "transparent");
            $(this).css("cursor", "normal");
            $('#mainMenuUsrLogoutCnt').hide();
            $('#mainMenuUsrDetCnt').show();
        });
        
        $('div.mainMenuUsrCnt').click(function(){
            location.href = "logout.php";
            /*$.ajax({
                url: "iframeProxy.php",
                action: "notificatorRemoteLogout",
                async: true,
                success: function()
                {

                },
                error: function(errorData)
                {
                    console.log("Remote logout from Notificator failed");
                    console.log(JSON.stringify(errorData));
                },
                complete: function()
                {
                    location.href = "logout.php";
                }
            });*/
        });
        
        $('#mainMenuCnt a.mainMenuSubItemLink').hide();
        
        $('#mainMenuCnt .mainMenuLink').click(function(event){
            if(($(this).attr('data-openMode') === 'iframe') && ($(this).attr('data-externalApp') === 'yes'))
            {
                event.preventDefault();
                var pageTitle = $(this).attr('data-pageTitle');
                var linkId = $(this).attr('id');
                var linkUrl = $(this).attr('data-linkUrl');
                
                location.href = "iframeApp.php?linkUrl=" + encodeURI(linkUrl) + "&linkId=" + linkId + "&pageTitle=" + pageTitle;
            }
            else
            {
                if($(this).attr('data-submenuVisible') === 'false')
                {
                    $(this).attr('data-submenuVisible', 'true');
                    $('#mainMenuCnt a.mainMenuSubItemLink[data-fatherMenuId=' + $(this).attr('id') + ']').show();
                }
                else
                {
                    $(this).attr('data-submenuVisible', 'false');
                    $('#mainMenuCnt a.mainMenuSubItemLink[data-fatherMenuId=' + $(this).attr('id') + ']').hide();
                }
            }
        });
    });
</script>    

