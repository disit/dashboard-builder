<?php
class Roads{

    // database connection and table name
    private $conn;
    private $table_name;

    // object properties
    public $id;
    public $roadSegmentUnit;
    public $roadID;
    public $segmentID;
    public $StartLong;
    public $StartLat;
    public $EndLong;
    public $EndLat;
    public $Lanes;
    public $FIPILI;

    // constructor with $db as database connection
    public function __construct($db){
        $this->conn = $db;
    }

    // read products
    function read($startLat, $startLong, $endLat, $endLong, $zoom, $db){    // MOD DB

       if ($zoom < 11) {
            $this->table_name = "10GraphStatic";
        }
        else if ($zoom == 11) {
            $this->table_name = "11GraphStatic";
        }
        else if ($zoom == 12) {
            $this->table_name = "12GraphStatic";
        }
        else if ($zoom == 13) {
            $this->table_name = "13GraphStatic";
        }
        else if ($zoom == 14) {
            $this->table_name = "14GraphStatic";
        }
        else if ($zoom == 15) {
            $this->table_name = "15GraphStatic";
        }
        else if ($zoom == 16) {
            $this->table_name = "16GraphStatic";
        }
        else if ($zoom == 17) {
            $this->table_name = "highDetailedGraphStatic";
        }
        else if ($zoom == 18) {
            $this->table_name = "highDetailedGraphStatic";
        }  

        // select all query
        $query = "SELECT
                p.id, p.roadSegmentUnit, p.roadID, p.segmentID, p.StartLong, p.StartLat, p.EndLong, p.EndLat, p.Lanes, p.FIPILI
            FROM
                " . $this->table_name . " p
                WHERE p.StartLat > '" . $startLat . "' and p.StartLong > '" . $startLong . "' and p.EndLat < '" . $endLat . "' and p.EndLong < '" . $endLong . "' ORDER BY p.roadID
           ";


        // prepare query statement
      //  $stmt = $this->conn->prepare($query);   // MOD DB
        $stmt = mysqli_query($db, $query);

        // execute query
      //  $stmt->execute();     // MOD DB

        return $stmt;
    }

    function firstRow($startLat, $startLong, $endLat, $endLong, $zoom, $db){    // MOD DB

        if ($zoom < 11) {
            $this->table_name = "10GraphStatic";
        }
        else if ($zoom == 11) {
            $this->table_name = "11GraphStatic";
        }
        else if ($zoom == 12) {
            $this->table_name = "12GraphStatic";
        }
        else if ($zoom == 13) {
            $this->table_name = "13GraphStatic";
        }
        else if ($zoom == 14) {
            $this->table_name = "14GraphStatic";
        }
        else if ($zoom == 15) {
            $this->table_name = "15GraphStatic";
        }
        else if ($zoom == 16) {
            $this->table_name = "16GraphStatic";
        }
        else if ($zoom == 17) {
            $this->table_name = "highDetailedGraphStatic";
        }
        else if ($zoom == 18) {
            $this->table_name = "highDetailedGraphStatic";
        }  
        
        // select all query
        $query = "SELECT
                p.id as firstRow
            FROM
                " . $this->table_name . " p
                WHERE p.StartLat > '" . $startLat . "' and p.StartLong > '" . $startLong . "' and p.EndLat < '" . $endLat . "' and p.EndLong < '" . $endLong . "' ORDER BY p.roadID LIMIT 1
           ";

        // prepare query statement
    //    $stmt = $this->conn->prepare($query);     // MOD DB

        // execute query
    //    $stmt->execute();     // MOD DB
        $stmt = mysqli_query($db, $query);

        return $stmt;
    }
}

?>
