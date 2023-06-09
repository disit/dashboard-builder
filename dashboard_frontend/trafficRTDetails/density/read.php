<?php
// required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

// include database and object files
include_once '../config/Database.php';
include_once '../objects/density.php';

// instantiate database and product object
$database = new Database();
$db = $database->getConnection();

// initialize object
$road = new Density($db);

$sLat = $_REQUEST['sLat'];
$sLong = $_REQUEST['sLong'];
$eLat = $_REQUEST['eLat'];
$eLong = $_REQUEST['eLong'];
$zoom = $_REQUEST['zoom'];

// query products
//$stmt = $road->read($sLat, $sLong, $eLat, $eLong, $zoom);
$stmt = $road->read($sLat, $sLong, $eLat, $eLong, $zoom, $db);  // MOD DB
//  $num = $stmt->rowCount();
$num = mysqli_num_rows($stmt);  // MOD DB

// products array
$roads_arr = array();
$segments_arr = array();
$density_arr = array();
$segUnit_arr = array();

$segments = Array();

$rE = Array();
$sE = Array();

$r = null;


// check if more than 0 record found
if ($num > 0) {

//    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {     // MOD DB
    while ($row = mysqli_fetch_assoc($stmt)) {

        extract($row);

        array_push($roads_arr, $row["roadID"]);
        array_push($segments_arr, $row["segmentID"]);
        array_push($density_arr, $row["density"]);

        $matches = Array();
        preg_match('/^([^.]+)/', $row["roadSegmentUnit"], $matches);

        array_push($segUnit_arr, $matches[0]);
    }


    for ($i = 0; $i < count($roads_arr); $i++) {
        if ($r !== $roads_arr[$i]){
            $segments = Array();
            $seg = Array();
            $dens = Array();

            $firstSegment = $segUnit_arr[$i];

            for ($j = 0; $j < count($segments_arr); $j++) {

                if ($firstSegment === $segUnit_arr[$j]) {
                    array_push($seg, $segments_arr[$j]);
                    array_push($dens, $density_arr[$j]);
                }
            }

            $object = new stdClass();

            for ($k = 0; $k < count($seg); $k++) {
                $s = $seg[$k];
                $d = $dens[$k];

                $object->$s = $d;
            }

            array_push($segments, $object);

            $segment_item = array(
                "data" => $segments
            );

            array_push($rE, $roads_arr[$i]);
            array_push($sE, $segment_item);
        }

        $r = $roads_arr[$i];
    }

    $roads = new stdClass();

    for ($h = 0; $h < count($rE); $h++) {
        $a = $rE[$h];
        $b = $sE[$h];

        $roads->$a = $b;
    }

    echo json_encode($roads);
} else {
    echo json_encode(
        array("message" => "No roads found.")
    );
}
?>
