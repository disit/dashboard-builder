<?php

include '../config.php';

//$init_flag = 1;
session_start();
//set_error_handler("exception_error_handler");
if (isset($REQUEST["getIcons"])) {
    
    $sql_array = $_REQUEST['sqlData'];
    $stop_flag = 1;
    
}

if (isset($_REQUEST["filterGlobal"])) {
//if (!empty($_REQUEST["filterGlobal"]) && !empty($_REQUEST["value"])) {

    $sql_where = $_REQUEST['filterGlobal'];
  
    $sql_distinct_field = $_REQUEST['distinctField'];
    
    $link = mysqli_connect($host, $username, $password);
    error_reporting(E_ERROR | E_NOTICE);
    
    if (strpos($sql_where, "AND") == 1) {
        $sql_where_ok = explode("AND ", $sql_where.trim())[1];
    } else {
        $sql_where_ok = $sql_where;
    }
    
    if (empty($_REQUEST["filterGlobal"])) {
        $sql_where_ok = 1;
    }
        
    $query = "SELECT DISTINCT ".$sql_distinct_field." FROM Dashboard.DashboardWizard WHERE ".$sql_where_ok." ORDER BY ".$sql_distinct_field." ASC;";
    //  $query = "SELECT * FROM Dashboard.DashboardWizard";
    
    //   echo ($query);
    
    
    $rs = mysqli_query($link, $query);
    
    $result = [];
    
    if($rs)
    {
        $result['table'] = [];
        while($row = mysqli_fetch_assoc($rs))
        {
            array_push($result['table'], $row);
        }
        
        //Eliminiamo i duplicati
        $result = array_unique($result);
        mysqli_close($link);
        $result['detail'] = 'Ok';
        
        echo json_encode($result);
        
    }
    else
    {
        mysqli_close($link);
        $result['detail'] = 'Ko';
    }
    
}


if (!empty($_REQUEST["filterField"]) && !empty($_REQUEST["value"])) {
    
    $stopFlag = 1;
    $sql_filter_field = $_REQUEST['filterField'];
    $sql_filter_value = $_REQUEST['value'];
    $sql_distinct_field = $_GET['filter'];
    
    $link = mysqli_connect($host, $username, $password);
    error_reporting(E_ERROR | E_NOTICE);
    
    $query = "SELECT DISTINCT ".$sql_distinct_field." FROM Dashboard.DashboardWizard WHERE ".$sql_filter_field." LIKE '".$sql_filter_value."' ORDER BY ".$sql_distinct_field." ASC";
    //  $query = "SELECT * FROM Dashboard.DashboardWizard";
    
    //   echo ($query);
    
    
    $rs = mysqli_query($link, $query);
    
    $result = [];
    
    if($rs)
    {
        $result['table'] = [];
        while($row = mysqli_fetch_assoc($rs))
        {
            array_push($result['table'], $row);
        }
        
        //Eliminiamo i duplicati
        $result = array_unique($result);
        mysqli_close($link);
        $result['detail'] = 'Ok';
        
        echo json_encode($result);
        
    }
    else
    {
        mysqli_close($link);
        $result['detail'] = 'Ko';
    }
    
}

if (!empty($_REQUEST["filterDistinct"])) {

        $sql_filter = $_GET['filter'];
        
        if (strcmp($sql_filter, "High-Level Type") == 0) {
            
            $sql_filter = "high_level_type";
            
        } else if (strcmp($sql_filter, "Nature") == 0) {
            
            $sql_filter = "nature";
            
        } else if (strcmp($sql_filter, "Subnature") == 0) {
            
            $sql_filter = "sub_nature";
            
        } else if (strcmp($sql_filter, "Value Type") == 0) {
            
            $sql_filter = "low_level_type";
            
        } else if (strcmp($sql_filter, "Value Name") == 0) {
            
            $sql_filter = "unique_name_id";
            
        } else if (strcmp($sql_filter, "Instance URI") == 0) {
            
            $sql_filter = "instance_uri";
            
        } else if (strcmp($sql_filter, "Data Type") == 0) {
            
            $sql_filter = "unit";
            
        } else if (strcmp($sql_filter, "Last Date") == 0) {
            
            $sql_filter = "last_date";
            
        } else if (strcmp($sql_filter, "Last Value") == 0) {
            
            $sql_filter = "last_value";
            
        } else if (strcmp($sql_filter, "Healthiness") == 0) {
            
            $sql_filter = "healthiness";
            
        } else if (strcmp($sql_filter, "Widgets") == 0) {
            
            $sql_filter = "icon1";
            
        } else if (strcmp($sql_filter, "Widget2") == 0) {
            
            $sql_filter = "icon2";
            
        } else if (strcmp($sql_filter, "Widget3") == 0) {
            
            $sql_filter = "icon3";
            
        } else if (strcmp($sql_filter, "Widget4") == 0) {
            
            $sql_filter = "icon4";
            
        } else if (strcmp($sql_filter, "Widget5") == 0) {
            
            $sql_filter = "icon5";
            
        } 
        
     //   echo ($sql_filter);
        
        $link = mysqli_connect($host, $username, $password);
        error_reporting(E_ERROR | E_NOTICE);

        $query = "SELECT DISTINCT ".$sql_filter." FROM Dashboard.DashboardWizard ORDER BY ".$sql_filter." ASC";
      //  $query = "SELECT * FROM Dashboard.DashboardWizard";

     //   echo ($query);

        
        $rs = mysqli_query($link, $query);

        $result = [];

        if($rs) 
        {
            $result['table'] = [];
            while($row = mysqli_fetch_assoc($rs)) 
            {
                array_push($result['table'], $row);
            }

            //Eliminiamo i duplicati
            $result = array_unique($result);
            mysqli_close($link);
            $result['detail'] = 'Ok';
            
            echo json_encode($result);

        } 
        else 
        {
            mysqli_close($link);
            $result['detail'] = 'Ko';
        }
    }
    


if(isset($_REQUEST['getDashboardWizardData'])) 
{
    $stop_flag = 1;
    
    $link = mysqli_connect($host, $username, $password);
    error_reporting(E_ERROR | E_NOTICE);
    
    $query = "SELECT * FROM Dashboard.DashboardWizard";
    $rs = mysqli_query($link, $query);

    $result = [];

    if($rs) 
    {
        $result['table'] = [];
        while($row = mysqli_fetch_assoc($rs)) 
        {
            array_push($result['table'], $row);
        }

        //Eliminiamo i duplicati
        $result = array_unique($result);
        mysqli_close($link);
        $result['detail'] = 'Ok';

    } 
    else 
    {
        mysqli_close($link);
        $result['detail'] = 'Ko';
    }
    
    echo json_encode($result);
}

if(isset($_REQUEST['getDashboardWizardDataFiltered']))  {
    
    if (!empty($_REQUEST["filter"])) {
        session_start();
        $sql_filter = $_REQUEST["filter"];
        
     //   echo ($sql_filter);
        
        $link = mysqli_connect($host, $username, $password);
        error_reporting(E_ERROR | E_NOTICE);

        $query = "SELECT * FROM Dashboard.DashboardWizard WHERE ".$sql_filter;
      //  $query = "SELECT * FROM Dashboard.DashboardWizard";

     //   echo ($query);

        
        $rs = mysqli_query($link, $query);

        $result = [];

        if($rs) 
        {
            $result['table'] = [];
            while($row = mysqli_fetch_assoc($rs)) 
            {
                array_push($result['table'], $row);
            }

            //Eliminiamo i duplicati
            $result = array_unique($result);
            mysqli_close($link);
            $result['detail'] = 'Ok';

        } 
        else 
        {
            mysqli_close($link);
            $result['detail'] = 'Ko';
        }
    } else {
        
        
    }
    
    echo json_encode($result);
    $flag = 1;
    
}

if(isset($_REQUEST['getDashboardWizardIcons'])) 
{
    $link = mysqli_connect($host, $username, $password);
    error_reporting(E_ERROR | E_NOTICE);

    $query = "SELECT * FROM Dashboard.WidgetsIconsMap";
    $rs = mysqli_query($link, $query);

    $result = [];

    if($rs) 
    {
        $result['table'] = [];
        while($row = mysqli_fetch_assoc($rs)) 
        {
            array_push($result['table'], $row);
        }

        mysqli_close($link);
        $result['detail'] = 'Ok';
    } 
    else 
    {
        mysqli_close($link);
        $result['detail'] = 'Ko';
    }
    echo json_encode($result);
}

if(isset($_REQUEST['filterUnitByIcon']))
{
    $sql_unit = $_GET["unit"];
    $stop_flag = 1;
}

if(isset($_REQUEST['updateWizardIcons']))
{
    $link = mysqli_connect($host, $username, $password);
    error_reporting(E_ERROR | E_NOTICE);
    $sql_field = $_GET["filterField"];
    $sql_value = $_GET["filterValue"];
    
    $query_out = "SELECT DISTINCT unit FROM Dashboard.DashboardWizard WHERE ".$sql_field ." LIKE '".$sql_value."';";

    $rs_out = mysqli_query($link, $query_out);

    $result_out = [];

    if($rs_out) 
    {
        $result_out['table'] = [];
        $unit_filter = "";
            while($row1 = mysqli_fetch_assoc($rs_out)) 
            {
                array_push($result_out['table'], $row1);
                $unit_filter = "'".$unit_filter."' OR ";
                
            }

         //   mysqli_close($link);
         //   $result['detail'] = 'Ok';
        } 
        else 
        {
            mysqli_close($link);
            $result['detail'] = 'Ko';
        }

        $unit_filter = substr($unit_filter,-4);
        $query = "SELECT * FROM Dashboard.WidgetsIconsMap WHERE snap4CityType LIKE '".$unit_filter."';";
        $rs = mysqli_query($link, $query);

        $result = [];

        if($rs) 
        {
            $result['table'] = [];
            while($row = mysqli_fetch_assoc($rs)) 
            {
                array_push($result['table'], $row);
            }

            mysqli_close($link);
            $result['detail'] = 'Ok';
        } 
        else 
        {
            mysqli_close($link);
            $result['detail'] = 'Ko';
        }
        echo json_encode($result);
}

if(isset($_REQUEST["initWidgetWizard"])) {
    
    if(($_REQUEST["initWidgetWizard"]) == 'true') {
        /* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
         * Easy set variables
         */

        // DB table to use
        $table = 'DashboardWizard';

        // Table's primary key
        $primaryKey = 'id';

        // Array of database columns which should be read and sent back to DataTables.
        // The `db` parameter represents the column name in the database, while the `dt`
        // parameter represents the DataTables column identifier. In this case simple
        // indexes
        $columns = array(
            array( 'db' => 'high_level_type', 'dt' => 0 ),
            array( 'db' => 'nature',  'dt' => 1 ),
            array( 'db' => 'sub_nature',   'dt' => 2 ),
            array( 'db' => 'low_level_type',     'dt' => 3 ),
            array( 'db' => 'unique_name_id',     'dt' => 4 ),
            array( 'db' => 'instance_uri',     'dt' => 5 ),
            array( 'db' => 'unit',     'dt' => 6 ),
            array(
                'db'        => 'last_date',
                'dt'        => 7,
                'formatter' => function( $d, $row ) {
                    if ($d != null) {
                        return date( 'Y-m-d H:i:s', strtotime($d));
                    } else {
                        return null;
                    }
                }
            ),
            array(
                'db'        => 'last_value',
                'dt'        => 8,
                'formatter' => function( $d, $row ) {
                    if ($d != null) {
                        return number_format($d);
                    } else {
                        return null;
                    }
                }
            ),  
            array( 'db' => 'healthiness',     'dt' => 9 ),
            array( 'db' => 'instance_uri',     'dt' => 10 ),
            array( 'db' => 'parameters',     'dt' => 11 )
        );

        // SQL server connection information
        $sql_details = array(
            'user' => $username,
            'pass' => $password,
            'db'   => 'Dashboard',
            'host' => $host
        );


        /* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
         * If you just want to use the basic configuration for DataTables with PHP
         * server-side, there is no need to edit below this line.
         */

        require('dashboardWizardControllerSSP.class.php');

        //echo json_encode(dashboardWizardControllerSSP::simple( $_GET, $sql_details, $table, $primaryKey, $columns ));

        $out = dashboardWizardControllerSSP::simple( $_GET, $sql_details, $table, $primaryKey, $columns );
        $out_json = json_encode($out);
        echo $out_json;
    }
}