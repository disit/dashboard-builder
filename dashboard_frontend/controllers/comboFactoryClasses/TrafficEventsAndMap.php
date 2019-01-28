<?php
class TrafficEventsAndMap extends aGenericComboFactory
{
    function finalizeCombo()
    {
        $link = mysqli_connect($this->host, $this->username, $this->password);
        mysqli_select_db($link, $this->schema);
        
        $mainWidgetName = $this->newWidgetDbRowMain->name_w;
        $comboParameters = "{\"" . $this->newWidgetDbRowTarget[0]->name_w . "\":[\"1\",\"2\",\"3\",\"4\",\"5\",\"6\",\"7\",\"8\",\"9\",\"10\",\"11\",\"12\",\"13\",\"14\",\"15\",\"16\",\"17\",\"18\",\"19\",\"20\",\"21\",\"22\",\"23\",\"24\",\"25\",\"26\",\"27\",\"28\",\"29\",\"30\",\"31\",\"32\",\"33\"]}";  
        
        $q = "UPDATE Dashboard.Config_widget_dashboard SET parameters = '$comboParameters' WHERE name_w = '$mainWidgetName'";
        $r = mysqli_query($link, $q);

        if($r)
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
}
