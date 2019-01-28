<?php

class WidgetButtonFactory extends aGenericWidgetFactory
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
                    $this->startParams->link_w = $row['parameters'];
                    $this->startParams->title_w = $row['sub_nature'];
                }
            }
            else
            {
                //Qui non prevista
            }
            
        }
        else
        {
            //TBD - Esiste?
        }
        
        mysqli_close($link);
        return $this->startParams;
    }
}
