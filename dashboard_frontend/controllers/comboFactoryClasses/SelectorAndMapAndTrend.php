<?php
class SelectorAndMapAndTrend extends aGenericComboFactory
{
    function finalizeCombo()
    {
        $link = mysqli_connect($this->host, $this->username, $this->password);
        mysqli_select_db($link, $this->schema);
        
        $mainWidgetName = $this->newWidgetDbRowMain->name_w;
        $mapWidgetName = $this->newWidgetDbRowTarget[0]->name_w;
        $trendWidgetName = $this->newWidgetDbRowTarget[1]->name_w;
        
        $finalTrendWidgetName = str_replace("ToBeReplacedByFactory", "DCTemp1", $this->newWidgetDbRowTarget[1]->name_w);
        $finalTrendMetricName = "DCTemp1";
        $finalTrendMetricTitle = "Selector - Trend";
        
        $trendNRow = $this->newWidgetDbRowTarget[0]->n_row + $this->newWidgetDbRowTarget[0]->size_rows;
        $trendNCol = $this->newWidgetDbRowTarget[0]->n_column;
        
        $q = "UPDATE Dashboard.Config_widget_dashboard SET name_w = '$finalTrendWidgetName', id_metric = '$finalTrendMetricName', title_w = '$finalTrendMetricTitle', n_row = '$trendNRow', n_column = '$trendNCol' WHERE name_w = '$trendWidgetName'";
        $r = mysqli_query($link, $q);

        if($r)
        {
            $selectorParameters = json_decode($this->newWidgetDbRowMain->parameters);
        
            $selectorParameters->targets = [$mapWidgetName];
            
            for($i = 0; $i < count($selectorParameters->queries); $i++)
            {
                $selectorParameters->queries[$i]->targets = [$finalTrendWidgetName];
            }

            $selectorParametersJson = json_encode($selectorParameters);
            
            $q2 = "UPDATE Dashboard.Config_widget_dashboard SET parameters = '$selectorParametersJson' WHERE name_w = '$mainWidgetName'";
            $r2 = mysqli_query($link, $q2);
            
            if($r2)
            {
                mysqli_close($link);
                return true;
            }
            else
            {
                mysqli_close($link);
                return false;   
            }
        }
        else
        {
            mysqli_close($link);
            return false;
        }
    }
}
