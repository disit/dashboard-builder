<?php
class OperatorEventsAndMap extends aGenericComboFactory
{
    function finalizeCombo()
    {
        $link = mysqli_connect($this->host, $this->username, $this->password);
        mysqli_select_db($link, $this->schema);
        
        $mainWidgetName = $this->newWidgetDbRowMain->name_w;
        $comboParameters = "{\"targetEventsJson\":[\"" . $this->newWidgetDbRowTarget[0]->name_w . "\"]}";
                           
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
