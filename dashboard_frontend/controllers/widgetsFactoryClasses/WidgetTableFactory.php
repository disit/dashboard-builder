<?php

class WidgetTableFactory extends aGenericWidgetFactory
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
                    $myServiceUri = null;
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
                    break;
            }
            
            array_push($styleParameters->barsColors, $defaultColors1[$count%7]);

            if ($myKPIFlag != 1) {
                $newQueryObj = ["metricId" => $myMetricId,
                    "metricHighLevelType" => $selectedRow['high_level_type'],
                    "metricName" => $myMetricName,
                    "metricType" => $myMetricType,
                    "serviceUri" => $myServiceUri];
            } else {
                $newQueryObj = ["metricId" => $myMetricId,
                    "metricHighLevelType" => $selectedRow['high_level_type'],
                    "metricName" => $myMetricName,
                    "metricType" => $myMetricType,
                    "serviceUri" => $myServiceUri];
            }
            array_push($rowParameters, $newQueryObj);
            $count++;
        }
        
        $this->startParams->styleParameters = json_encode($styleParameters);
        $this->startParams->rowParameters = json_encode($rowParameters);
        //$this->startParams->size_rows = $count;
        
        return $this->startParams;
    }
}
