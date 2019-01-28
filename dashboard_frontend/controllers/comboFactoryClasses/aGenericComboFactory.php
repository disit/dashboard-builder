<?php
include '../config.php';

class aGenericComboFactory 
{
    public $host;
    public $schema;
    public $username;
    public $password;
    public $newWidgetDbRowMain;
    public $newWidgetDbRowTarget;
    
    function __construct($newWidgetDbRowMain, $newWidgetDbRowTarget) 
    {
        $this->newWidgetDbRowMain = $newWidgetDbRowMain;
        $this->newWidgetDbRowTarget = $newWidgetDbRowTarget;
        
        $envFileContent = parse_ini_file("../conf/environment.ini"); 
        $activeEnv = $envFileContent["environment"]["value"];
        $generalContent = parse_ini_file("../conf/general.ini");
        $databaseContent = parse_ini_file("../conf/database.ini"); 
        $this->host = $generalContent["host"][$activeEnv];
        $this->schema = $databaseContent["dbname"][$activeEnv];
        $this->username = $databaseContent["username"][$activeEnv];
        $this->password = $databaseContent["password"][$activeEnv];
    }
    
    
    function getNewWidgetDbRowMain() 
    {
        return $this->newWidgetDbRowMain;
    }

    function setNewWidgetDbRowMain($newWidgetDbRowMain) 
    {
        $this->newWidgetDbRowMain = $newWidgetDbRowMain;
    }
    
    function getNewWidgetDbRowTarget() 
    {
        return $this->newWidgetDbRowTarget;
    }

    function setNewWidgetDbRowTarget($newWidgetDbRowTarget) 
    {
        $this->newWidgetDbRowTarget = $newWidgetDbRowTarget;
    }

    //Ogni combo per cui è necessario ne fa l'override
    function finalizeCombo()
    {
        return true;
    }
}
