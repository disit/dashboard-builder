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

        if ($zoom < 11) {
            $this->table_name = "10GraphDynamic";
            $this->table_name2 = "10GraphStatic";
        }
        else if ($zoom == 11) {
            $this->table_name = "11GraphDynamic";
            $this->table_name2 = "11GraphStatic";
        }
        else if ($zoom == 12) {
            $this->table_name = "12GraphDynamic";
            $this->table_name2 = "12GraphStatic";
        }
        else if ($zoom == 13) {
            $this->table_name = "13GraphDynamic";
            $this->table_name2 = "13GraphStatic";
        }
        else if ($zoom == 14) {
            $this->table_name = "14GraphDynamic";
            $this->table_name2 = "14GraphStatic";
        }
        else if ($zoom == 15) {
            $this->table_name = "15GraphDynamic";
            $this->table_name2 = "15GraphStatic";
        }
        else if ($zoom == 16) {
            $this->table_name = "16GraphDynamic";
            $this->table_name2 = "16GraphStatic";
        }
        else if ($zoom == 17) {
            $this->table_name = "highDetailedGraphDynamic";
            $this->table_name2 = "highDetailedGraphStatic";
        }
        else if ($zoom == 18) {
            $this->table_name = "highDetailedGraphDynamic";
            $this->table_name2 = "highDetailedGraphStatic";
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