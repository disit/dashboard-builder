<?php

include '../config.php';
require '../sso/autoload.php';

use Jumbojett\OpenIDConnectClient;

function returnManagedStringForDb($original)
{
    if($original == NULL)
    {
        return "NULL";
    }
    else
    {
        return "'" . $original . "'";
    }
}

session_start();
checkSession('Manager');


$link = mysqli_connect($host, $username, $password);

$id_Widget = $_POST['widgetId'];

mysqli_select_db($link, $dbname);
/*$query0 = "SELECT Config_dashboard.logoFilename FROM Dashboard.Config_dashboard WHERE Config_dashboard.Id = $id_Dashboard";
$result0 = mysqli_query($link, $query0);

if($result0)
{
   $row0 = mysqli_fetch_array($result0);
   $sourceDashLogoFilename = $row0['logoFilename'];
}
else
{   
   echo "originalDashRecordQueryKo";
   exit();
}

$query1 = "SELECT * FROM Dashboard.Config_widget_dashboard WHERE Config_widget_dashboard.Id= $id_Widget";
$result1 = mysqli_query($link, $query1) or die(mysqli_error($link));
*/


//if($result1) {
    
    //$row1 = mysqli_fetch_assoc($result1);
     
    //unset($row1['Id']);

    $query4 = "SELECT * FROM Dashboard.Config_widget_dashboard WHERE Id = $id_Widget";
    $result4 = mysqli_query($link, $query4);

    $columns = array();
    $row4_ = array();
    if($result4) {
        while($column4 = mysqli_fetch_field($result4)){
            $columns[] = $column4->name;
        }
        
        while ($row4 = mysqli_fetch_assoc($result4)) {
            // Creare un array associativo con i nomi delle colonne come chiavi
            $riga_assoc = array();

            foreach ($columns as $colonna) {
                $riga_assoc[$colonna] = $row4[$colonna];
            }
            // Aggiungere l'array associativo all'array row4_
            
            $row4_[] = $riga_assoc;
        }

        foreach ($row4_ as &$widget) {
            unset($widget['Id']);
        }
        #$json = json_encode($row4_);
        $mergedArray = array(
            'Widget' => $row4_
        );

        $mergedJSON = json_encode($mergedArray);
        header('Content-Type: application/json');
       
        echo $mergedJSON;
    }

//}
/*else
{
   echo "originalDashRecordQueryKo";
   exit();
}*/
