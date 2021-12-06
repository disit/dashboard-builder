<?php

class WidgetSelectorWebFactory extends aGenericWidgetFactory
{
    //Sovrascrive l'originaria
    function completeWidget()
    {
        $defaultColors1 = ["#ffdb4d", "#ff9900", "#ff6666", "#00e6e6", "#33ccff", "#33cc33", "#009900"];
        $defaultColors2 = ["#fff5cc", "#ffe0b3", "#ffcccc", "#99ffff", "#99e6ff", "#adebad", "#80ff80"];
    //    $myIconText = "Text Description";
        $scaleFactor = 3;
        
        if($this->widgetTypeDbRow['mono_multi'] == 'Mono')
        {
            //Qui non usata 
        }
        else
        {
            $selectorParameters = json_decode($this->startParams->parameters);
            
            $this->startParams->id_metric = 'SelectorWeb';
            $this->startParams->name_w = str_replace("Selector_", "SelectorWeb_", $this->startParams->name_w);
            
            $count = 0;
            foreach($this->selectedRows as $selectedRowKey => $selectedRow) 
            {
                $rowId = str_replace("row", "", $selectedRowKey);
                $q = "SELECT * FROM Dashboard.DashboardWizard WHERE id = $rowId";
                $link = mysqli_connect($this->host, $this->username, $this->password);
                mysqli_select_db($link, $this->schema);
                $r = mysqli_query($link, $q);

                if($r)
                {
                    $row = mysqli_fetch_assoc($r);
                    switch($row['high_level_type'])
                    {
                        case "BIM Device":
                        case "BIM View":
                        case "External Service":
                            $rowQuery = $row['parameters'];
                            break;
                        
                        case "MicroApplication":
                            $rowQuery = $row['parameters'] . "&coordinates=" . $this->mapCenterLat . ";" . $this->mapCenterLng;
                            break;

                        case "POI":
                            //Aspettiamo che Claudio ampli la WebApp per subnature, intanto usiamo ServiceMap
                            //$rowQuery = "https://www.km4city.org/webapp-new/?operation=aroundyou/" . $row['sub_nature'] . "&coordinates=" . $this->mapCenterLat . ";" . $this->mapCenterLng;
                            $baseUrlKb = "https://servicemap.disit.org/WebAppGrafo/api/v1/";
                            if (isset($_SESSION['orgKbUrl'])) {
                                $baseUrlKb = $_SESSION['orgKbUrl'];
                            }
                            if (isset($_SESSION['orgGpsCentreLatLng'])) {
                                $orgGpsCentreLatLng = $_SESSION['orgGpsCentreLatLng'];
                                $orgGpsLat = trim(explode(",", $orgGpsCentreLatLng)[0]);
                                $orgGpsLng = trim(explode(",", $orgGpsCentreLatLng)[1]);
                            } else {
                                // Se è di organizzazione "Other" o nessuna dà le coordinate del centro di Firenze di default
                                $orgGpsLat = "43.769789";
                                $orgGpsLng = "11.255694";
                            }
                            if (isset($_SESSION['orgZoomLevel'])) {
                                $orgZoomLevel = $_SESSION['orgZoomLevel'];
                            }
                            $rowQuery = $baseUrlKb . "?selection=" . ($orgGpsLat-0.125) . ";" . ($orgGpsLng-0.25) . ";" . ($orgGpsLat+0.125) .";". ($orgGpsLng+0.25) . "&categories=" . $selectedRow['sub_nature'] . "&maxResults=200&format=html";
                        //    $rowQuery = "https://servicemap.disit.org/WebAppGrafo/api/v1/?selection=" . $this->selection . "&categories=" . $categories . "&maxResults=200&format=html";
                            break;
                    }

                    if ($row['high_level_type'] == 'BIM Device' || $row['high_level_type'] == 'BIM View') {
                        $desc = $row['unique_name_id'];
                    } else {
                        $desc = $selectedRow['sub_nature'];
                    }
                    $newQueryObj = ["color1" => $defaultColors1[$count%7], 
                            "color2" => $defaultColors2[$count%7], 
                            "defaultOption" => false, 
                         //   "desc" => $selectedRow['sub_nature'],
                            "desc" => $desc,
                            "display" => "pins",
                        //    "iconText" => $myIconText,
                            "query" => $rowQuery, 
                            "symbolMode" => "auto", 
                            "targets" => "[]",];
                        
                    array_push($selectorParameters->queries, $newQueryObj);
                }
                $count++;
            }
            
            $this->startParams->parameters = json_encode($selectorParameters);
            $this->startParams->size_rows = ($count + 1) * $scaleFactor;
            $this->startParams->title_w = "Selector Web";
        }
        
        return $this->startParams;
    }
}
