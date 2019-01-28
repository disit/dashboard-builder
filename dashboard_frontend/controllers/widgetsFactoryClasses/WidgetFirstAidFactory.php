<?php

class WidgetFirstAidFactory extends aGenericWidgetFactory
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
                    $this->startParams->serviceUri = $row['parameters'];
                    $this->startParams->title_w = "First Aid - " . $row['last_value'];
                }
            }
            else
            {
                //Qui non usata
            }
        }
        else
        {
            //OK - Caso più ospedali in singolo widget
            $hospitalList = [];
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
                    array_push($hospitalList, $row['parameters']);
                }
                $count++;
            }
            
            $this->startParams->hospitalList = json_encode($hospitalList);
            $this->startParams->size_rows = $count*2;
        }
        
        $this->startParams->id_metric = 'FirstAid';
        $this->startParams->name_w = str_replace('ToBeReplacedByFactory', 'FirstAid', $this->startParams->name_w);
        
        mysqli_close($link);
        return $this->startParams;
    }
}
