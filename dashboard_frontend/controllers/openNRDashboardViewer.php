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
                        //Esistente
                        $row = mysqli_fetch_assoc($result);
                        
                        $dashboardId = $row['Id'];
                        mysqli_close($link);
                        header("location: ../view/index.php?iddasboard=" . base64_encode($dashboardId));
                    }
                    else
                    {
                        //TBD - Dashboard non esistente
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
