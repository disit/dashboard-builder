<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Dashboard Management System</title>
    </head>
    <body>
        <?php
            include('../config.php');
            
            $link = mysqli_connect($host, $username, $password);
            mysqli_select_db($link, $dbname);
            
            if((isset($_GET['dashboardTitle']))&&(!empty($_GET['dashboardTitle']))&&(isset($_GET['username']))&&(!empty($_GET['username'])))
            {
                $response = [];
                $dashboardTitle = urldecode($_GET['dashboardTitle']);
                $dashboardSubtitle = "";

                $username = $_GET['username'];

                $query = "SELECT * FROM Dashboard.Config_dashboard WHERE title_header = '$dashboardTitle' AND user = '$username'";
                $result = mysqli_query($link, $query);

                if($result)
                {
                    $dashboardParams = [];
                    if(mysqli_num_rows($result) > 0)
                    {
                        //Dashboard già esistente
                        $row = mysqli_fetch_assoc($result);
                        
                        $dashboardId = $row['Id'];
                        $dashboardAuthor = $row['user'];
                        $dashboardEditor = $username;
                        mysqli_close($link);
                        header("location: ../management/dashboard_configdash.php?dashboardId=" . $dashboardId . "&dashboardAuthorName=" . $dashboardAuthor . "&dashboardEditorName=" . $dashboardEditor . "&dashboardTitle=" . urlencode($dashboardTitle));
                    }
                    else
                    {
                        //Dashboard non esistente, viene creata
                        $nCols = 10;
                        $width = ($nCols * 78) + 10;

                        $query2 = "INSERT INTO Dashboard.Config_dashboard " . 
                                  "(name_dashboard, title_header, subtitle_header, color_header, width, height, num_rows, num_columns, user, status_dashboard, creation_date, color_background, external_frame_color, headerFontColor, headerFontSize, logoFilename, logoLink, widgetsBorders, widgetsBordersColor, visibility, headerVisible, embeddable, authorizedPagesJson, viewMode, fromNodeRed, gridColor) " .
                                  "VALUES ('$dashboardTitle', '$dashboardTitle', '$dashboardSubtitle', 'rgba(0, 0, 0, 1)', $width, 0, 0, $nCols, '$username', 1, now(), 'rgba(255, 255, 255, 1)', 'rgba(255, 255, 255, 1)', 'rgba(0,240,255,1)', 28, NULL, '', 'yes', 'rgba(0, 0, 0, 1)', 'public', 1, 'no', '[]', 'mediumResponsive', 'yes', 'rgba(238, 238, 238, 1)')";

                        $result2 = mysqli_query($link, $query2);

                        if($result2)
                        {
                            $dashboardId = mysqli_insert_id($link);
                            $dashboardAuthor = $username;
                            $dashboardEditor = $username;
                            mysqli_close($link);
                            header("location: ../management/dashboard_configdash.php?dashboardId=" . $dashboardId . "&dashboardAuthorName=" . $dashboardAuthor . "&dashboardEditorName=" . $dashboardEditor . "&dashboardTitle=" . urlencode($dashboardTitle));
                        }
                        else
                        {
                            //TBD - Caso di KO
                        }
                    }
                }
                else
                {
                    //TBD - Caso di KO
                }
            }
        ?>
    </body>
</html>
