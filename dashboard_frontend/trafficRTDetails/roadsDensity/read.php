<?php
// required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

// include database and object files
include_once '../config/Database.php';
include_once '../objects/roadsDensity.php';


$sLat = $_REQUEST['sLat'];
$sLong = $_REQUEST['sLong'];
$eLat = $_REQUEST['eLat'];
$eLong = $_REQUEST['eLong'];
$zoom = $_REQUEST['zoom'];

// instantiate database and product object
$database = new Database();
$db = $database->getConnection();

// initialize object
$road = new Roads($db);


// query products
// $stmt = $road->read($sLat, $sLong, $eLat, $eLong, $zoom);
$stmt = $road->read($sLat, $sLong, $eLat, $eLong, $zoom, $db);  // MOD DB
// $num = $stmt->rowCount();
$num = mysqli_num_rows($stmt);


// $firstRow = $road->firstRow($sLat, $sLong, $eLat, $eLong, $zoom)->fetch(PDO::FETCH_ASSOC)["firstRow"];     // MOD DB
$firstRow = $road->firstRow($sLat, $sLong, $eLat, $eLong, $zoom, $db);
$firstRow = mysqli_fetch_assoc($firstRow);


// products array
$roads_arr = array();
$segments_arr = array();
$road = null;

// check if more than 0 record found
if ($num > 0) {

  //  while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {     // MOD DB
    while ($row = mysqli_fetch_assoc($stmt)) {

        //$row = $stmt->fetchAll(PDO::FETCH_ASSOC)
        extract($row);

        if ($road === $roadID) {

            $start_item = array(
                "long" => $StartLong,
                "lat" => $StartLat,
            );

            $end_item = array(
                "long" => $EndLong,
                "lat" => $EndLat,
            );

            $segment_item = array(
                "id" => $segmentID,
                "start" => $start_item,
                "end" => $end_item,
                "Lanes" => $Lanes,
                "FIPILI" => $FIPILI,
                "density" => $density
            );

            array_push($segments_arr, $segment_item);
        }

        if ($row["id"] === $firstRow) {

            $start_item = array(
                "long" => $StartLong,
                "lat" => $StartLat,
            );

            $end_item = array(
                "long" => $EndLong,
                "lat" => $EndLat,
            );

            $segment_item = array(
                "id" => $segmentID,
                "start" => $start_item,
                "end" => $end_item,
                "Lanes" => $Lanes,
                "FIPILI" => $FIPILI,
                "density" => $density
            );

            array_push($segments_arr, $segment_item);
            $road = $roadID;
        }


        if ($row["id"] !== $firstRow && $road !== $roadID) {

            $road_item = array(
                "road" => $road,
                "segments" => $segments_arr
            );

            array_push($roads_arr, $road_item);

            $road = $roadID;
            $segments_arr = array();

            $start_item = array(
                "long" => $StartLong,
                "lat" => $StartLat,
            );

            $end_item = array(
                "long" => $EndLong,
                "lat" => $EndLat,
            );

            $segment_item = array(
                "id" => $segmentID,
                "start" => $start_item,
                "end" => $end_item,
                "Lanes" => $Lanes,
                "FIPILI" => $FIPILI,
                "density" => $density
            );

            array_push($segments_arr, $segment_item);
        }
    }

    //print_r(count($roads_arr));


    echo json_encode($roads_arr);
} else {
    echo json_encode(
        array("message" => "No roads found.")
    );
}
?>
