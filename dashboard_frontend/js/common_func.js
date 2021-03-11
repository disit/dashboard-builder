function initSessionExpiringPopup(sessionEndTime) {
    $(function () {
        $('#sessionExpiringPopup').css(
            "top",
            parseInt($('body').height() - $('#sessionExpiringPopup').height()) + "px"
        );
        $('#sessionExpiringPopup').css(
            "left",
            parseInt($('body').width() - $('#sessionExpiringPopup').width()) + "px"
        );

        setInterval(function () {
            var now = parseInt(new Date().getTime() / 1000);
            var difference = sessionEndTime - now;

            if (difference === 300) {
                $('#sessionExpiringPopupTime').html("5 minutes");
                $('#sessionExpiringPopup').show();
                $('#sessionExpiringPopup').css("opacity", "1");
                setTimeout(function () {
                    $('#sessionExpiringPopup').css("opacity", "0");
                    setTimeout(function () {
                        $('#sessionExpiringPopup').hide();
                    }, 1000);
                }, 4000);
            }

            if (difference === 120) {
                $('#sessionExpiringPopupTime').html("2 minutes");
                $('#sessionExpiringPopup').show();
                $('#sessionExpiringPopup').css("opacity", "1");
                setTimeout(function () {
                    $('#sessionExpiringPopup').css("opacity", "0");
                    setTimeout(function () {
                        $('#sessionExpiringPopup').hide();
                    }, 1000);
                }, 4000);
            }

            if ((difference > 0) && (difference <= 60)) {
                $('#sessionExpiringPopup').show();
                $('#sessionExpiringPopup').css("opacity", "1");
                $('#sessionExpiringPopupTime').html(difference + " seconds");
            }

            if (difference <= 0) {
                location.href = "logout.php?sessionExpired=true";
            }
        }, 1000);

        $(window).resize(function () {
            $('#sessionExpiringPopup').css(
                "top",
                parseInt($('body').height() - $('#sessionExpiringPopup').height()) + "px"
            );
            $('#sessionExpiringPopup').css(
                "left",
                parseInt($('body').width() - $('#sessionExpiringPopup').width()) + "px"
            );
        });
    });
}

function initPageContent() {
    $('#mainContentCnt').height($('#mainMenuCnt').height() - $('#headerTitleCnt').height());
    $(window).resize(function () {
        $('#mainContentCnt').height($('#mainMenuCnt').height() - $('#headerTitleCnt')
            .height());
    });
}

function initMenu(linkId) {
    $(`#mainMenuCnt .mainMenuLink[id=${linkId}] div.mainMenuItemCnt`)
        .addClass("mainMenuItemCntActive");
    $(`#mobMainMenuPortraitCnt .mainMenuLink[id=${linkId}] .mobMainMenuItemCnt`)
        .addClass("mainMenuItemCntActive");
    $(`#mobMainMenuLandCnt .mainMenuLink[id=${linkId}] .mobMainMenuItemCnt`)
        .addClass("mainMenuItemCntActive");

    if ($('div.mainMenuSubItemCnt').parents(`a[id=${linkId}]`).length > 0) {
        var fatherMenuId = $('div.mainMenuSubItemCnt')
            .parents(`a[id=${linkId}]`)
            .attr('data-fathermenuid');
        
        $(`#${fatherMenuId}`).attr('data-submenuVisible', 'true');
        $(`#mainMenuCnt a.mainMenuSubItemLink[data-fatherMenuId=${fatherMenuId}]`).show();
        $(`#${fatherMenuId}`)
            .find('.submenuIndicator')
            .removeClass('fa-caret-down');
        $(`#${fatherMenuId}`)
            .find('.submenuIndicator')
            .addClass('fa-caret-up');
        $('div.mainMenuSubItemCnt').parents(`a[id=${linkId}]`)
            .find('div.mainMenuSubItemCnt')
            .addClass("subMenuItemCntActive");
    }
}