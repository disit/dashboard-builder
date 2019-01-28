<?php

class WidgetTrackerFactory extends aGenericWidgetFactory
{
    //Sovrascrive l'originaria
    function completeWidget()
    {
        $widgetParameters = [];
        foreach($this->selectedRows as $selectedRowKey => $selectedRow) 
        {
            $newTrackSrc = [];
            $newTrackSrc['appName'] = $selectedRow['nature'];
            $newTrackSrc['motivation'] = $selectedRow['low_level_type'];
            $newTrackSrc['variableName'] = $selectedRow['unique_name_id'];
            
            array_push($widgetParameters, $newTrackSrc);
        }
        
        $this->startParams->parameters = json_encode($widgetParameters);
        $this->startParams->id_metric = "Tracker";
        $this->startParams->name_w = str_replace("ToBeReplacedByFactory", "Tracker", $this->startParams->name_w);
        $this->startParams->sm_based = "myPersonalData";
        $this->startParams->title_w = "Tracker";
        return $this->startParams;
    }
}
