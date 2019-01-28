<?php
include '../config.php';

class aGenericWidgetFactory 
{
    public $startParams;
    public $selectedRows;
    public $selectedRowKey;
    public $widgetTypeDbRow;
    public $mapCenterLat; 
    public $mapCenterLng;
    public $widgetRole;
    public $selection;
    public $mapZoom;
    public $host;
    public $schema;
    public $username;
    public $password;
    
    function __construct($startParams, $selectedRowKey, $widgetTypeDbRow, $mapCenterLat, $mapCenterLng, $widgetRole, $selectedRows, $selection, $mapZoom) 
    {
        $this->startParams = $startParams;
        $this->selectedRowKey = $selectedRowKey;
        $this->widgetTypeDbRow = $widgetTypeDbRow;
        $this->mapCenterLat = $mapCenterLat;
        $this->mapCenterLng = $mapCenterLng;
        $this->widgetRole = $widgetRole;
        $this->selectedRows = $selectedRows;
        $this->selection = $selection;
        $this->mapZoom = $mapZoom;
        
        $envFileContent = parse_ini_file("../conf/environment.ini"); 
        $activeEnv = $envFileContent["environment"]["value"];
        $generalContent = parse_ini_file("../conf/general.ini");
        $databaseContent = parse_ini_file("../conf/database.ini"); 
        $this->host = $generalContent["host"][$activeEnv];
        $this->schema = $databaseContent["dbname"][$activeEnv];
        $this->username = $databaseContent["username"][$activeEnv];
        $this->password = $databaseContent["password"][$activeEnv];
    }
    
    function getSelection() 
    {
        return $this->selection;
    }

    function getMapZoom() 
    {
        return $this->mapZoom;
    }

    function setSelection($selection) 
    {
        $this->selection = $selection;
    }

    function setMapZoom($mapZoom) 
    {
        $this->mapZoom = $mapZoom;
    }
    
    function getWidgetRole() 
    {
        return $this->widgetRole;
    }

    function setWidgetRole($widgetRole) 
    {
        $this->widgetRole = $widgetRole;
    }
    
    function getStartParams() 
    {
        return $this->startParams;
    }

    function setStartParams($startParams) 
    {
        $this->startParams = $startParams;
    }

    function getSelectedRowKey() 
    {
        return $this->selectedRowKey;
    }

    function setSelectedRowKey($selectedRowKey) 
    {
        $this->selectedRowKey = $selectedRowKey;
    }
    
    function getWidgetTypeDbRow() 
    {
        return $this->widgetTypeDbRow;
    }

    function setWidgetTypeDbRow($widgetTypeDbRow) 
    {
        $this->startParams = $widgetTypeDbRow;
    }
    
    function getMapCenterLat() 
    {
        return $this->mapCenterLat;
    }

    function getMapCenterLng() 
    {
        return $this->mapCenterLng;
    }

    function setMapCenterLat($mapCenterLat) 
    {
        $this->mapCenterLat = $mapCenterLat;
    }

    function setMapCenterLng($mapCenterLng) 
    {
        $this->mapCenterLng = $mapCenterLng;
    }
    
    function setSelectedRows($selectedRows) 
    {
        $this->selectedRows = $selectedRows;
    }
    
    function getSelectedRows() 
    {
        return $this->selectedRows;
    }
    
    //Ogni widget per cui è necessario ne fa l'override
    function completeWidget()
    {
        return $this->startParams;
    }
}
