<?php

class Density{
    // database connection and table name
    private $conn;
    private $table_name;
    private $table_name2;

    // object properties
    public $roadID;
    public $segmentID;
    public $density;

    // constructor with $db as database connection
    public function __construct($db){
        $this->conn = $db;
    }

    // read products
    // function read($startLat, $startLong, $endLat, $endLong, $zoom){
    function read($startLat, $startLong, $endLat, $endLong, $zoom, $db){    // MOD DB

        if ($zoom > 1) {
            $this->table_name = "lowDetailedGraphDynamic";
            $this->table_name2 = "lowDetailedGraphStatic";
        }

        // select all query
        $query = "SELECT
                a.segmentID, a.density, b.roadID, b.roadSegmentUnit
            FROM
                " . $this->table_name . " as a join " . $this->table_name2 . " as b on a.segmentID = b.segmentID 
                WHERE b.StartLat > '" . $startLat . "' and b.StartLong > '" . $startLong . "' and b.EndLat < '" . $endLat . "' and b.EndLong < '" . $endLong . "'
           ";

        // prepare query statement
    //    $stmt = $this->conn->prepare($query);     // MOD DB

        // execute query
    //    $stmt->execute();
        $stmt = mysqli_query($db, $query);  // MOD DB

        return $stmt;
    }
}

?>