<?php

class WidgetMapFactory extends aGenericWidgetFactory
{
    //Sovrascrive l'originaria
    function completeWidget()
    {
        if($this->widgetTypeDbRow['mono_multi'] == 'Mono')
        {
            if($this->widgetRole == 'main')
            {
                $link = mysqli_connect($this->host, $this->username, $this->password);
                mysqli_select_db($link, $this->schema);

                $rowId = str_replace("row", "", $this->selectedRowKey);
                $q = "SELECT * FROM Dashboard.DashboardWizard WHERE id = $rowId";
                $r = mysqli_query($link, $q);

                if($r)
                {
                    $row = mysqli_fetch_assoc($r);
                    switch($row['high_level_type'])
                    {
                        case "External Service":
                            $this->startParams->link_w = $row['parameters'];
                            break;

                        case "MicroApplication":
                            $this->startParams->link_w = $row['parameters'] . "&coordinates=" . $this->mapCenterLat . ";" . $this->mapCenterLng;
                            break;

                        default:
                            $this->startParams->link_w = $row['parameters'];
                            break;
                    }

                    $this->startParams->title_w = $row['sub_nature'];
                    mysqli_close($link);
                }
            }
            else
            {
                //TBD - Esiste? Per ora no
            }
        }
        else
        {
            //IN CORSO
            if($this->widgetRole == 'main')
            {
                //IN CORSO - Per ora esiste solo come ServiceMap che insiste sui POI
                switch($this->widgetTypeDbRow['icon'])
                {
                    case "servicemap.png":
                        $this->selection = str_replace(",", "%3B", $this->selection);
                        
                        $categories = "";
                        
                        $count = 0;
                        foreach($this->selectedRows as $selectedRowKey => $selectedRow) 
                        {
                            if ($count == 0) {
                                $categories = $selectedRow['sub_nature'];
                                $count++;
                            } else {
                                $categories = $categories . "%3B" . $selectedRow['sub_nature'];
                                $count++;
                            }
                        }
                        
                        $categories = str_replace("categories=%3B", "categories=", $categories);
                        $baseUrlKb = "https://servicemap.disit.org/WebAppGrafo/api/v1/?selection=";
                        if (isset($_SESSION['orgKbUrl'])) {
                            $baseUrlKb = $_SESSION['orgKbUrl']."?selection=";
                        }
                        $this->startParams->link_w = $baseUrlKb . $this->selection . "&categories=" . $categories . "&maxResults=200&format=html";
                        break;
                    
                    default:
                        break;
                }
            }
            else
            {
                if($this->startParams->link_w == 'gisTarget' || $this->startParams->link_w == 'none' || $this->startParams->link_w == null)
                {
                    $this->startParams->parameters = "{\"latLng\":[" . $this->mapCenterLat . "," . $this->mapCenterLng . "],\"zoom\":" . $this->mapZoom . "}"; 
                }
                
                $this->startParams->id_metric = "Map";
                $this->startParams->name_w = str_replace("ToBeReplacedByFactory", "Map", $this->startParams->name_w);
            }
        }
        
        
        return $this->startParams;
    }
}
