<?php

class WidgetDeviceTableFactory extends aGenericWidgetFactory
{
    //Sovrascrive l'originaria
    function completeWidget()
    {
        $myQuery = null;
        $myDesc = null;
        $myQueryType = null;
        $defaultColors1 = ["#ffdb4d", "#ff9900", "#ff6666", "#00e6e6", "#33ccff", "#33cc33", "#009900"];
        $styleParameters = json_decode($this->startParams->styleParameters);
        $styleParameters->barsColors = [];
        $rowParameters = [];
        $listSu = null;

        //$rowParameters =  json_decode({"ordering":"dateObserved","query":"https://www.snap4city.org/superservicemap/api/v1/iot-search/?selection=43.843588;11.138763427&maxDists=300&format=json&model=AlertMilestone&valueFilters=Severity:Red;status:init","actions":["pin"],"columnsToShow":["id","dateObserved"]});
        $this->startParams->id_metric = "AggregationSeries";
        $this->startParams->name_w = str_replace("ToBeReplacedByFactory", "AggregationSeries", $this->startParams->name_w);
        $myKPIFlag = 0;

        $count = 0;
        foreach($this->selectedRows as $selectedRowKey => $selectedRow) 
        {
            switch($selectedRow['high_level_type'])
            {
                case "KPI":
                    $myMetricId = $selectedRow['unique_name_id'];
                    $mySmField = null;
                   // $myServiceUri = null;
                    $myServiceUri = $selectedRow['get_instances'];
                    $link = mysqli_connect($this->host, $this->username, $this->password);
                    mysqli_select_db($link, $this->schema);

                    $q = "SELECT * FROM Dashboard.Descriptions WHERE IdMetric = '$myMetricId'";
                    $r = mysqli_query($link, $q);

                    if($r)
                    {
                        $row = mysqli_fetch_assoc($r);
                        $myMetricName = $row['description_short'];
                    }
                    else
                    {
                        $myMetricName = $myMetricId;
                    }
                    break;

                case "IoT Device Variable":
                case "Data Table Variable":
                case "Mobile Device Variable":
                case "Sensor":
                case "sensor_map":
                    $myMetricId = $selectedRow['get_instances'];
                    $myMetricName = $selectedRow['unique_name_id'];
                    $myMetricType = $selectedRow['low_level_type'];
                    $myServiceUri = $selectedRow['get_instances'];
                    break;

                case "MyKPI":
                    $myKPIFlag = 1;
                    if($selectedRow['parameters']) {
                        $myMetricId = $selectedRow['parameters'];
                    } else if($selectedRow['get_instances']) {
                        $myMetricId = $selectedRow['get_instances'];
                    }

                    $myMetricName = $selectedRow['unique_name_id'];
                    $myMetricType = $selectedRow['low_level_type'];
                    $myServiceUri = $selectedRow['get_instances'];
                    break;

                default:
                    //Per ora aggiungiamo solo i KPI, poi si specializzerà
                    $myMetricName = $selectedRow['unique_name_id'];
                    $myMetricType = $selectedRow['low_level_type'];
                    $myServiceUri = $selectedRow['get_instances'];
                    break;
            }
            
            array_push($styleParameters->barsColors, $defaultColors1[$count%7]);
            if($listSu == null){
                $myServiceUri1 = $myServiceUri;
                $listSu = $myServiceUri1;
            }else{
                $myServiceUri1 = $myServiceUri;
                $listSu = $listSu .';'.$myServiceUri1; 
            }
            $count++;
        }
            if ($myKPIFlag != 1) {
                /*$newQueryObj = ["metricId" => $myMetricId,
                    "metricHighLevelType" => $selectedRow['high_level_type'],
                    "metricName" => $myMetricName,
                    "metricType" => $myMetricType,
                    "serviceUri" => $myServiceUri];*/
                    $newQueryObj = [
                        "ordering" => "dateObserved",
                        "query" =>"https://www.snap4city.org/superservicemap/api/v1/iot-search/?selection=43.77;11.2&maxDists=2000.2&serviceUri=".$listSu."&format=json",
                        "actions"=>["pin"],
                        "columnsToShow"=>["id","dateObserved"]
                    ];
            } else {
               /* $newQueryObj = ["metricId" => $myMetricId,
                    "metricHighLevelType" => $selectedRow['high_level_type'],
                    "metricName" => $myMetricName,
                    "metricType" => $myMetricType,
                    "serviceUri" => $myServiceUri];*/
                    $newQueryObj = [
                        "ordering" => "dateObserved",
                        "query" =>"https://www.snap4city.org/superservicemap/api/v1/iot-search/?selection=43.77;11.2&maxDists=2000.2&serviceUri=".$listSu."&format=json",
                        "actions"=>["pin"],
                        "columnsToShow"=>["id","dateObserved"]
                    ];
            }
           // array_push($rowParameters, $newQueryObj);
           
            
        
        $rowParameters = $newQueryObj;
        
        $this->startParams->styleParameters = json_encode($styleParameters);
        $this->startParams->rowParameters = json_encode($rowParameters);
        //$this->startParams->size_rows = $count;
        
        return $this->startParams;
    }
}
