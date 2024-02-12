<?php
    require_once '../common.php';
    include_once '../locale.php';
    error_reporting(E_ERROR);
    if(!isset($_SESSION))
    {
       session_start();
    }
    checkSession('Manager');
        $valuename_filter = "";
    if (isset ($_GET['valuename'])){
        $valuename_filter = $_GET['valuename'];
        $newstr = filter_var($valuename_filter, FILTER_SANITIZE_STRING);
        $valuename_filter = $newstr;
        unset($_GET['valuename']);
    }
    $type="";
    if (isset ($_GET['high-level-type'])){
        $type0 = $_GET['high-level-type'];
        $type1 = filter_var($type0, FILTER_SANITIZE_STRING);
        $type = $type1;
        unset($_GET['high-level-type']);
    }

    function isCreationWizardUI(){
        if(!empty($_SERVER['HTTP_REFERER']) && strpos($_SERVER['HTTP_REFERER'],'dashboards.php') !== false){

            return true;
        }else{
            return false;
        }
    }
?>

<?php if(isCreationWizardUI()):?>

<link rel="stylesheet" href="../css/style_widgets.css?v=1563354439" type="text/css">
<ul id="wizardTabsContainer" class="nav nav-tabs nav-justified">
    <li id="aTab" class="active"><a data-toggle="tab" href="#mainFeat" class="dashboardWizardTabTxt"><?= _("Dashboard features")?></a></li>
    <li id="bTab"><a href="#dataAndWidgets" data-toggle="no" class="dashboardWizardTabTxt"><?= _("Data and widgets")?></a></li> <!-- data-toggle="tab" -->
    <li id="cTab"><a href="#summary" data-toggle="no" class="dashboardWizardTabTxt"><?= _("Check and summary")?></a></li> <!-- data-toggle="tab" -->
</ul> 
<?php else: ?>
    <style>
        .addWidgetWizardIconClickClass{
            pointer-events: none;
        }
    </style>
<?php endif;?>
<div class="tab-content">
    <div id="mainFeat" class="tab-pane fade in <?php echo isCreationWizardUI() ? 'active' : ''?>">
        <div class="col-xs-12 col-sm-6 col-sm-offset-3">
            <div class="row">
                <div class="col-xs-12 centerWithFlex addWidgetWizardIconsCntLabelBig" style="margin-top: 6px;">
                    <?= _("Dashboard title")?>
                </div>
                <div class="col-xs-12">
                    <input type="text" name="inputTitleDashboard" id="inputTitleDashboard" value="" class="form-control" style="width: 100%;" required> 
                    <input type="hidden" id="inputTitleDashboardStatus" value="empty"/>
                </div>
            </div>
            <div id="modalAddDashboardWizardTitleAlreadyUsedMsg" class="row">
                <div class="col-xs-12 centerWithFlex">
                   <?= _("Dashboard title can't be empty")?> 
                </div>
            </div>   
        </div> 
        
        <div class="col-xs-12 col-sm-6" id="dashboardTemplatesContainer">
            <div class="row">
                <div class="col-xs-12 addWidgetWizardIconsCntLabelBig centerWithFlex"><?= _("Dashboard template")?> </div>
                <div class="col-xs-12 addWidgetWizardIconsCntSublabel centerWithFlex"><?= _("Click on a template to choose it, click on it again to unselect it")?> </div>
            </div>
            <input type="hidden" id="dashboardTemplateStatus" value="empty"/>
            <input type="hidden" id="dashboardDirectStatus" value="empty"/>
            <div class="row" id="dashboardTemplatesInnerCnt">
                <?php
                include '../config.php';

                error_reporting(E_ERROR);
                date_default_timezone_set('Europe/Rome');

                $link = mysqli_connect($host, $username, $password);
                mysqli_select_db($link, $dbname);

                $menuQuery = "SELECT * FROM Dashboard.DashboardTemplates ORDER BY id ASC";
                $r = mysqli_query($link, $menuQuery);

                if($r) 
                {
                    while ($row = mysqli_fetch_assoc($r)) {
                        $templateId = $row['id'];
                        $templateName = $row['name'];
                        $templateTitle = $row['title'];
                        $templateIcon = $row['icon'];
                        $templateAvailable = $row['available'];
                        $widgetType = $row['widgetType'];
                        $highLevelTypeSelection = $row['highLevelTypeSelection'];
                        $natureSelection = $row['natureSelection'];
                        $subnatureSelection = $row['subnatureSelection'];
                        $valueTypeSelection = $row['valueTypeSelection'];
                        $valueNameSelection = $row['valueNameSelection'];
                        $dataTypeSelection = $row['dataTypeSelection'];
                        $healthinessSelection = $row['healthinessSelection'];
                        $ownershipSelection = $row['ownershipSelection'];
                        $valueUnitSelection = $row['valueUnitSelection'];
                        $brokerSelection = $row['brokerSelection'];
                        $modelSelection = $row['modelSelection'];
                        $valueTypeNSelection = $row['valueTypeNSelection'];
                        $valueNameNSelection = $row['valueNameNSelection'];
                        $highLevelTypeVisible = $row['highLevelTypeVisible'];
                        $natureVisible = $row['natureVisible'];
                        $subnatureVisible = $row['subnatureVisible'];
                        $valueTypeVisible = $row['valueTypeVisible'];
                        $valueNameVisible = $row['valueNameVisible'];
                        $dataTypeVisible = $row['dataTypeVisible'];
                        $lastDateVisible = $row['lastDateVisible'];
                        $lastValueVisible = $row['lastValueVisible'];
                        $healthinessVisible = $row['healthinessVisible'];
                        $lastCheckVisible = $row['lastCheckVisible'];
                        $ownershipVisible = $row['ownershipVisible'];
                        $valueUnitVisible = $row['valueUnitVisible'];
                        $brokerVisible = $row['brokerVisible'];
                        $valueTypeNVisible = $row['valueTypeNVisible'];
                        $valueNameNVisible = $row['valueNameNVisible'];
                        $modelVisible = $row['modelVisible'];
                        $hasActuators = $row['hasActuators'];
                        
                        if($widgetType == 'any')
                        {
                            $templateSub = "Manual widget choice";
                        }
                        else
                        {
                            if($widgetType == 'none')
                            {
                                $templateSub = "Empty dashboard";
                            }
                            else
                            {
                                $templateSub = "Preset widget choice";
                            }
                        }

                        $newItem = '<div class="col-xs-10 col-sm-4 col-md-3 modalAddDashboardWizardChoiceCnt" data-hasActuators="' . $hasActuators . '" data-highLevelTypeVisible = "' . $highLevelTypeVisible . '" data-natureVisible = "' . $natureVisible . '" data-subnatureVisible = "' . $subnatureVisible . '" data-valueTypeVisible = "' . $valueTypeVisible . '" data-valueNameVisible = "' . $valueNameVisible . '" data-dataTypeVisible = "' . $dataTypeVisible . '" data-lastDateVisible = "' . $lastDateVisible . '" data-lastValueVisible = "' . $lastValueVisible . '" data-healthinessVisible = "' . $healthinessVisible . '" data-lastCheckVisible = "' . $lastCheckVisible . '" data-ownershipVisible = "' . $ownershipVisible . '" data-valueUnitVisible = "' . $valueUnitVisible . '" data-brokerVisible = "' . $brokerVisible . '"  data-modelVisible = "' . $modelVisible . '" data-valueTypeNVisible = "' . $valueTypeNVisible . '" data-valueNameNVisible = "' . $valueNameNVisible . '" data-dataTypeSel="' . $dataTypeSelection . '" data-valueNameSel="' . $valueNameSelection . '" data-valueTypeSel="' . $valueTypeSelection . '" data-subnatureSel="' . $subnatureSelection . '" data-natureSel="' . $natureSelection . '" data-highLevelSel="' . $highLevelTypeSelection . '" data-healthinessSel="' . $healthinessSelection . '" data-ownershipSel="' . $ownershipSelection . '" data-valueUnitSel="' . $valueUnitSelection . '" data-brokerSel="' . $brokerSelection . '" data-modelSel="' . $modelSelection . '" data-modelSel="' . $modelSelection . '" data-available="' . $templateAvailable . '" data-selected="false" data-templateName="' . $templateName . '" data-widgetType="' . $widgetType . '">
                                        <div class="col-xs-12 modalAddDashboardWizardChoicePic" style="background-image: url(' . $templateIcon . ')"> 

                                        </div>
                                        <div class="col-xs-12 centerWithFlex modalAddDashboardWizardChoiceTxt">
                                            ' . $templateTitle . '
                                        </div>
                                        <div class="col-xs-12 centerWithFlex modalAddDashboardWizardChoiceSubtxt">
                                            ' . $templateSub . '
                                        </div>
                                    </div>';

                        echo $newItem;
                    }
                }
                ?>   
            </div>
            <div id="modalAddDashboardWizardTemplateMsg" class="row">
                <div class="col-xs-12 centerWithFlex"><?= _("You must choose one template")?></div>
            </div>
        </div>
        
    </div>
    <div id="dataAndWidgets" class="tab-pane fade in <?php echo (!isCreationWizardUI()) ? 'active' : ''?>">
        <div id="dataAndWidgetsInnerCnt">
            <div class="row hideFullyCustom">
                <!-- Mappa -->
                <div class="col-xs-12 col-md-6">
                    <div class="col-xs-12 addWidgetWizardIconsCntLabel centerWithFlex">
                       <?= _("Map")?> 
                    </div>
                    <div class="col-xs-12" id="addWidgetWizardMapCnt2">
                    </div>
                </div>

                <!-- Icone -->
                <div class="col-xs-12 col-md-6">
                    <div class="col-xs-12 addWidgetWizardIconsCntLabel dashTemplateHide centerWithFlex">
                        <?= _("Single data widgets")?> 
                    </div>
                    <div class="col-xs-12 addWidgetWizardIconsCnt">
                    </div>
                    <div class="col-xs-12 addWidgetWizardIconsCntLabel dashTemplateHide centerWithFlex">
                        <?= _("Multi data widgets")?>
                    </div>
                    <div class="col-xs-12 addWidgetWizardIconsCnt">
                    </div>
                    <div class="col-xs-12 addWidgetWizardIconsCntLabel dashTemplateHide" style="float: left; margin-top: 5px">
                        <div class="col-xs-12 addWidgetWizardIconsCntLabel dashTemplateHide centerWithFlex" style="float: left"><?= _("Map Controls")?>:</div>
                    
                    <label class="switch" style=" margin-left: 10px; float: left">
                            <input type="checkbox" id="togBtn">
                            <div class="slider round"><!--ADDED HTML --><span class="fixMapon">FixMap</span><span class="fixMapoff" style="color: black"><?= _("FilterMap")?></span><!--END--></div>
                        </label><!--<button type="button" id="FreezeMap" class="btn cancelBtn" style="margin-top: 10px">FreezeMap</button>-->
                        <button type="button" id="GPSUser" class="btn cancelBtn" style=" margin-left: 5px; float: left"><?= _("GPSUser")?></button>
                        <button type="button" id="GPSOrg" class="btn cancelBtn" style=" margin-left: 5px; float: left"><?= _("GPSOrg")?></button>
                    </div>
                    <?php if(!isCreationWizardUI()):?>
                        <?php if(!$synMode) { ?>
                            <div class="col-xs-12 addWidgetWizardIconsCntLabel dashTemplateHide" style="float: left; margin-top: 5px">
                                <div class="col-xs-12 addWidgetWizardIconsCntLabel dashTemplateHide centerWithFlex" style="float: left"><?= _("Now displaying in Standard Mode")?></div>
                                <p><?= _("Switch to the Synoptic Mode to select MyKPIs and sensors that you need for your synoptics.")?></p>
                                <button type="button" id="SynMode" class="btn cancelBtn" style=" margin-left: 5px; float: left"><?= _("Switch now to the Synoptic Mode")?></button>
                            </div>
                        <?php } else { ?>
                            <div class="col-xs-12 addWidgetWizardIconsCntLabel dashTemplateHide" style="float: left; margin-top: 5px">
                                <div class="col-xs-12 addWidgetWizardIconsCntLabel dashTemplateHide centerWithFlex" style="float: left"><?= _("Now displaying in Synoptic Mode")?></div>
                                <p><?= _("Switch back to the Standard Mode to return making the traditional use of the Data Inspector.")?></p>
                                <button type="button" id="StdMode" class="btn cancelBtn" style=" margin-left: 5px; float: left"><?= _("Switch now to the Standard Mode")?></button>
                            </div>					
                        <?php } ?>
                    <?php endif; ?>
                    <div id="addWidgetWizardWidgetAvailableMsg" class="col-xs-12 centerWithFlex">
                    </div>
                </div>
            </div>

            <!-- Campi per attuatori -->
            <div class="row" id="widgetWizardActuatorFieldsRow">
                <div class="col-xs-12 addWidgetWizardIconsCntLabel centerWithFlex">
                   <?= _("Actuator options")?>
                </div>

                <div class="col-xs-3 col-md-2 widgetWizardActuatorCell">
                    <div class="col-xs-12 centerWithFlex wizardActLbl">
                       <?= _("Existent or new target")?> 
                    </div>
                    <div class="col-xs-12 wizardActInputCnt">
                        <select id="actuatorTargetInstance" class="form-control">
                            <option value="existent"><?= _("Existent")?></option>
                            <option value="new"><?= _("New")?></option>
                        </select>
                    </div>  
                </div> 

                <div class="col-xs-3 col-md-2 widgetWizardActuatorCell" id="actuatorTargetCell">
                    <div class="col-xs-12 centerWithFlex wizardActLbl">
                        <?= _("New actuator target type")?>
                    </div>
                    <div class="col-xs-12 wizardActInputCnt">
                        <select id="actuatorTargetWizard" class="form-control">
                            <option value="broker" data-highLevelType="Sensor-Actuator"><?= _("IOT device on broker")?></option>
                            <!--<option value="app" data-highLevelType="Dashboard-IOT App">IOT app</option>-->
                        </select>
                    </div>  
                </div>



                <div class="col-xs-3 col-md-2 widgetWizardActuatorCell" id="actuatorEntityNameCell">
                    <div class="col-xs-12 centerWithFlex wizardActLbl">
                        <?= _("Device name")?>
                    </div>
                    <div class="col-xs-12 wizardActInputCnt">
                        <input type="text" id="actuatorEntityName" class="form-control"></input><br>
                    </div>  
                </div>

                <div class="col-xs-3 col-md-2 widgetWizardActuatorCell" id="actuatorValueTypeCell">
                    <div class="col-xs-12 centerWithFlex wizardActLbl">
                        <?= _("Value type")?>
                    </div>
                    <div class="col-xs-12 wizardActInputCnt" style="overflow:auto;">
                       <!-- <input type="text" id="actuatorValueType" class="form-control"> -->
                            <select id="actuatorValueType" class="form-control"></select>
                      <!--  </input>    -->
                    </div>
                </div>

                <div class="col-xs-3 col-md-2 widgetWizardActuatorCell" id="actuatorMinBaseValueCell">
                    <div class="col-xs-12 centerWithFlex wizardActLbl">
                        <?= _("Min/Base value")?>
                    </div>
                    <div class="col-xs-12 wizardActInputCnt">
                        <input type="text" id="actuatorMinBaseValue" class="form-control"></input>
                    </div>  
                </div>

                <div class="col-xs-3 col-md-2 widgetWizardActuatorCell" id="actuatorMaxBaseValueCell">
                    <div class="col-xs-12 centerWithFlex wizardActLbl">
                       <?= _("Max/Impulse value")?>  
                    </div>
                    <div class="col-xs-12 wizardActInputCnt">
                        <input type="text" id="actuatorMaxImpulseValue" class="form-control"></input>
                    </div>  
                </div>
            </div> 

            <!-- Riga tabella -->
            <div class="row hideIfActuatorNew hideFullyCustom" id="widgetWizardTableRow">
                <div class="col-xs-12 addWidgetWizardIconsCntLabel centerWithFlex">
                   <?= _("Data sources")?>  
                </div>
                <div id="noRowsSelectedAlert" class="col-xs-12 centerWithFlex">
                   <?= _("No rows selected: please select some rows and try again.")?> 
                </div>

                <div id="widgetWizardTableContainer" class="col-xs-12">
                    <table id="widgetWizardTable" class="addWidgetWizardTable table table-striped nowrap">
                        <thead class="widgetWizardColTitle">
                            <tr>  
                                <th id="hihghLevelTypeColTitle" class="widgetWizardTitleCell" data-cellTitle="HighLevelType"><div id="highLevelTypeColumnFilter"></div></th>  <!-- Potrebbe diventare DEVICE TYPE ??? -->
                                <th class="widgetWizardTitleCell" data-cellTitle="Nature"><div id="natureColumnFilter"></div></th>
                                <th class="widgetWizardTitleCell" data-cellTitle="SubNature"><div id="subnatureColumnFilter"></div></th>
                                <th class="widgetWizardTitleCell" data-cellTitle="ValueType"><div id="lowLevelTypeColumnFilter"></div></th>   <!-- Ex LOW_LEVEL_TYPE -->
                                <th class="widgetWizardTitleCell" data-cellTitle="ValueName"><div id="uniqueNameIdColumnFilter"></div></th>      <!-- Ex NAME-ID -->
                                <th class="widgetWizardTitleCell" data-cellTitle="InstanceUri"></th>
                                <th class="widgetWizardTitleCell" data-cellTitle="DeviceModel"><div id="deviceModelColumnFilter"></div></th>
                                <th class="widgetWizardTitleCell" data-cellTitle="Model"><div id="modelNameColumnFilter"></div></th>
                                <th class="widgetWizardTitleCell" data-cellTitle="Broker"><div id="brokerColumnFilter"></div></th>
                                <th class="widgetWizardTitleCell" data-cellTitle="ValueNameN"><div id="valueNameColumnFilter"></div></th>   <!-- New VALUE NAME -->
                                <th class="widgetWizardTitleCell" data-cellTitle="ValueTypeN"><div id="valueTypeColumnFilter"></div></th>
                                <th class="widgetWizardTitleCell" data-cellTitle="DataType"><div id="unitColumnFilter"></th>    <!-- Data Type Ex UNIT -->
                                <th class="widgetWizardTitleCell" data-cellTitle="valueUnit"><div id="valueUnitColumnFilter"></th>
                                <th class="widgetWizardTitleCell" data-cellTitle="LastDate"></th>
                                <th class="widgetWizardTitleCell" data-cellTitle="LastValue"></th>
                                <th class="widgetWizardTitleCell" data-cellTitle="Healthiness"><div id="healthinessColumnFilter"></th>
                                <th class="widgetWizardTitleCell" data-cellTitle="InstanceUri"><?= _("Instance URI")?></th>
                                <th class="widgetWizardTitleCell" data-cellTitle="Parameters"><?= _("Parameters")?></th>
                                <th class="widgetWizardTitleCell" data-cellTitle="Id"><?= _("Id")?></th>
                                <th class="widgetWizardTitleCell" data-cellTitle="Last Check"></th>
                                <th class="widgetWizardTitleCell" data-cellTitle="GetInstances"></th>
                                <th class="widgetWizardTitleCell" data-cellTitle="Ownership"><div id="ownershipColumnFilter"></th>
                                <th class="widgetWizardTitleCell" data-cellTitle="Organizations"></th>
                                <th class="widgetWizardTitleCell" data-cellTitle="Latitude"></th>
                                <th class="widgetWizardTitleCell" data-cellTitle="Longitude"></th>
                                <th class="widgetWizardTitleCell" data-cellTitle="sm_based"></th>
                                <th class="widgetWizardTitleCell" data-cellTitle="ownerHash"></th>
                                <th class="widgetWizardTitleCell" data-cellTitle="delegatedHash"></th>
                                <th class="widgetWizardTitleCell" data-cellTitle="delegatedGroupHash"></th>
                            <!--    <th class="widgetWizardTitleCell" data-cellTitle="sm_based"></th>   -->

                            </tr>  
                            <tr>  
                                <th id="hihghLevelTypeColTitle" class="widgetWizardTitleCell" data-cellTitle="HighLevelType"><?= _("High-Level Type")?></th>  <!-- Potrebbe diventare DEVICE TYPE ??? -->
                                <th class="widgetWizardTitleCell" data-cellTitle="Nature"><?= _("Nature")?></th>
                                <th class="widgetWizardTitleCell" data-cellTitle="SubNature"><?= _("Subnature")?></th>
                                <th class="widgetWizardTitleCell" data-cellTitle="ValueType"><?= _("Value Type")?></th>   <!-- Ex LOW_LEVEL_TYPE -->
                                <th class="widgetWizardTitleCell" data-cellTitle="ValueName"><?= _("Value Name")?></th>      <!-- Ex NAME-ID -->
                                <th class="widgetWizardTitleCell" data-cellTitle="InstanceUri"><?= _("Instance URI")?></th>
                                <th class="widgetWizardTitleCell" data-cellTitle="DeviceModel"><?= _("Device")?></th>
                                <th class="widgetWizardTitleCell" data-cellTitle="Model"><?= _("Model")?></th>
                                <th class="widgetWizardTitleCell" data-cellTitle="Broker"><?= _("Broker")?></th>
                                <th class="widgetWizardTitleCell" data-cellTitle="ValueNameN"><?= _("Value Name")?></th>    <!-- New VALUE NAME -->
                                <th class="widgetWizardTitleCell" data-cellTitle="ValueTypeN"><?= _("Value Type")?></th>
                                <th class="widgetWizardTitleCell" data-cellTitle="DataType"><?= _("Data Type")?></th>    <!-- Ex UNIT -->
                                <th class="widgetWizardTitleCell" data-cellTitle="valueUnit"><?= _("Value Unit")?></th>
                                <th class="widgetWizardTitleCell" data-cellTitle="LastDate"><?= _("Last Date")?></th>
                                <th class="widgetWizardTitleCell" data-cellTitle="LastValue"><?= _("Last Value")?></th>
                                <th class="widgetWizardTitleCell" data-cellTitle="Healthiness"><?= _("Healthiness")?></th>
                                <th class="widgetWizardTitleCell" data-cellTitle="InstanceUri"><?= _("Instance URI")?></th>
                                <th class="widgetWizardTitleCell" data-cellTitle="Parameters"><?= _("Parameters")?></th>
                                <th class="widgetWizardTitleCell" data-cellTitle="Id"><?= _("Id")?></th>
                                <th class="widgetWizardTitleCell" data-cellTitle="LastCheck"><?= _("Last Check")?></th>
                                <th class="widgetWizardTitleCell" data-cellTitle="GetInstances"></th>
                                <th class="widgetWizardTitleCell" data-cellTitle="Ownership"><?= _("Ownership")?></th>
                            <!--    <th class="widgetWizardTitleCell" data-cellTitle="valueUnit">Value Unit</th>    -->
                                <th class="widgetWizardTitleCell" data-cellTitle="Organizations"><?= _("Organizations")?></th>
                                <th class="widgetWizardTitleCell" data-cellTitle="Latitude"><?= _("Latitude")?></th>
                                <th class="widgetWizardTitleCell" data-cellTitle="Longitude"><?= _("Longitude")?></th>
                                <th class="widgetWizardTitleCell" data-cellTitle="sm_based"><?= _("sm_based")?></th>
                                <th class="widgetWizardTitleCell" data-cellTitle="ownerHash"><?= _("ownerHash")?></th>
                                <th class="widgetWizardTitleCell" data-cellTitle="delegatedHash"><?= _("delegatedHash")?></th>
                                <th class="widgetWizardTitleCell" data-cellTitle="delegatedGroupHash"><?= _("delegatedGroupHash")?></th>

                            </tr>  
                        </thead>
                        <tfoot>
                            <?php 
                            /*
                            <tr>
                                <th id="" class="" data-cellTitle="HighLevelType">High-Level Type</th>  <!-- Potrebbe diventare DEVICE TYPE ??? -->
                                <th class="" data-cellTitle="Nature">Nature</th>
                                <th class="" data-cellTitle="SubNature">Subnature</th>
                                <th class="" data-cellTitle="ValueType">Value Type</th>   <!-- Ex LOW_LEVEL_TYPE -->
                                <th class="" data-cellTitle="ValueName">Value Name</th>      <!-- Ex NAME-ID -->
                                <th class="" data-cellTitle="InstanceUri">Instance URI</th>
                                <th class="" data-cellTitle="DeviceModel">Device/Model</th>
                                <th class="" data-cellTitle="Broker">Broker</th>
                                <th class="" data-cellTitle="ValueNameN">Value Name</th>    <!-- New VALUE NAME -->
                                <th class="" data-cellTitle="ValueTypeN">Value Type</th>
                                <th class="" data-cellTitle="DataType">Data Type</th>    <!-- Ex UNIT -->
                                <th class="" data-cellTitle="valueUnit">Value Unit</th>
                                <th class="" data-cellTitle="LastDate">Last Date</th>
                                <th class="" data-cellTitle="LastValue">Last Value</th>
                                <th class="" data-cellTitle="Healthiness">Healthiness</th>
                                <th class="" data-cellTitle="InstanceUri">Instance URI</th>
                                <th class="" data-cellTitle="Parameters">Parameters</th>
                                <th class="" data-cellTitle="Id">Id</th>
                                <th class="" data-cellTitle="LastCheck">Last Check</th>
                                <th class="" data-cellTitle="GetInstances"></th>
                                <th class="" data-cellTitle="Ownership">Ownership</th>
                                <th class="" data-cellTitle="Organizations">Organizations</th>
                                <th class="" data-cellTitle="Latitude">Latitude</th>
                                <th class="" data-cellTitle="Longitude">Longitude</th>
                                <th class="" data-cellTitle="sm_based">sm_based</th>
                                <th class="" data-cellTitle="ownerHash">ownerHash</th>
                                <th class="" data-cellTitle="delegatedHash">delegatedHash</th>
                                <th class="" data-cellTitle="delegatedGroupHash">delegatedGroupHash</th>
                            </tr> */ ?>
                            <tr>
                                <th id="" class="" data-cellTitle="HighLevelType">Search...</th>  <!-- Potrebbe diventare DEVICE TYPE ??? -->
                                <th class="" data-cellTitle="Nature">Search...</th>
                                <th class="" data-cellTitle="SubNature">Search...</th>
                                <th class="" data-cellTitle="ValueType">Search...</th>   <!-- Ex LOW_LEVEL_TYPE -->
                                <th class="" data-cellTitle="ValueName">Search...</th>      <!-- Ex NAME-ID -->
                                <th class="" data-cellTitle="InstanceUri">Search...</th>
                                <th class="" data-cellTitle="DeviceModel">Search...</th>
                                <th class="" data-cellTitle="Model">Search...</th>
                                <th class="" data-cellTitle="Broker">Search...</th>
                                <th class="" data-cellTitle="ValueNameN">Search...</th>    <!-- New VALUE NAME -->
                                <th class="" data-cellTitle="ValueTypeN">Search...</th>
                                <th class="" data-cellTitle="DataType">Search...</th>    <!-- Ex UNIT -->
                                <th class="" data-cellTitle="valueUnit">Search...</th>
                                <th class="" data-cellTitle="LastDate">Search...</th>
                                <th class="" data-cellTitle="LastValue">Search...</th>
                                <th class="" data-cellTitle="Healthiness">Search...</th>
                                <th class="" data-cellTitle="InstanceUri">Search...</th>
                                <th class="" data-cellTitle="Parameters">Search...</th>
                                <th class="" data-cellTitle="Id">Search...</th>
                                <th class="" data-cellTitle="LastCheck">Search...</th>
                                <th class="" data-cellTitle="GetInstances">Search...</th>
                                <th class="" data-cellTitle="Ownership">Search...</th>
                                <th class="" data-cellTitle="Organizations">Search...</th>
                                <th class="" data-cellTitle="Latitude">Search...</th>
                                <th class="" data-cellTitle="Longitude">Search...</th>
                                <th class="" data-cellTitle="sm_based">Search...</th>
                                <th class="" data-cellTitle="ownerHash">Search...</th>
                                <th class="" data-cellTitle="delegatedHash">Search...</th>
                                <th class="" data-cellTitle="delegatedGroupHash">Search...</th>
                            </tr>
                        </tfoot>
 
                    </table>
                </div>           
            </div> <!-- Fine riga tabella principale -->

            <div class="row hideIfActuatorNew hideFullyCustom" style="padding-left: 15px; padding-right: 15px;">
                <!-- Comandi tabella -->
                <div id="widgetWizardTableCommandsContainer" class="col-xs-12">
                    <!-- Comandi nascondi colonne -->
                    <div class="widgetWizardWheelMenuContainer col-xs-12 col-md-2">
                        <div class="col-xs-8 addWidgetWizardIconsCntLabel addWidgetWizardTableLbl centerWithFlex"><?= _("Hide columns")?></div>
                        <div class="col-xs-2 centerWithFlex">
                            <div class="btn-group dropup">
                                <button type="button" class="btn confirmBtn dropdown-toggle" data-toggle="dropdown"><span class="glyphicon glyphicon-cog"></span> <span class="caret"></span></button>

                                <ul class="dropdown-menu" id="widgetWizardTableHideColsMenu">
                                    <li><a href="#" class="small" data-value="option1" tabIndex="-1"><input type="checkbox" class="checkWidgWizCol" data-fieldTitle="high_level_type"/>&nbsp;<?= _("High level type")?></a></li>
                                    <li><a href="#" class="small" data-value="option2" tabIndex="-1"><input type="checkbox" class="checkWidgWizCol" data-fieldTitle="nature"/>&nbsp;<?= _("Nature")?></a></li>
                                    <li><a href="#" class="small" data-value="option3" tabIndex="-1"><input type="checkbox" class="checkWidgWizCol" data-fieldTitle="sub_nature"/>&nbsp;<?= _("Subnature")?></a></li>
                                    <li><a href="#" class="small" data-value="option4" tabIndex="-1"><input type="checkbox" class="checkWidgWizCol" data-fieldTitle="device_model_name"/>&nbsp;<?= _("Device Model")?></a></li>
                                    <li><a href="#" class="small" data-value="option5" tabIndex="-1"><input type="checkbox" class="checkWidgWizCol" data-fieldTitle="broker_name"/>&nbsp;<?= _("Broker")?></a></li>
                                    <li><a href="#" class="small" data-value="option6" tabIndex="-1"><input type="checkbox" class="checkWidgWizCol" data-fieldTitle="value_name"/>&nbsp;<?= _("Value name")?></a></li>
                                    <li><a href="#" class="small" data-value="option7" tabIndex="-1"><input type="checkbox" class="checkWidgWizCol" data-fieldTitle="value_type"/> <?= _("Value type")?></a></li>
                                    <li><a href="#" class="small" data-value="option8" tabIndex="-1"><input type="checkbox" class="checkWidgWizCol" data-fieldTitle="unit"/>&nbsp;<?= _("Data type")?></a></li>
                                    <li><a href="#" class="small" data-value="option9" tabIndex="-1"><input type="checkbox" class="checkWidgWizCol" data-fieldTitle="value_unit"/>&nbsp;<?= _("Value Unit")?></a></li>
                                    <li><a href="#" class="small" data-value="option10" tabIndex="-1"><input type="checkbox" class="checkWidgWizCol" data-fieldTitle="last_date"/>&nbsp;<?= _("Last date")?></a></li>
                                    <li><a href="#" class="small" data-value="option11" tabIndex="-1"><input type="checkbox" class="checkWidgWizCol" data-fieldTitle="last_value"/>&nbsp;<?= _("Last value")?></a></li>
                                    <li><a href="#" class="small" data-value="option12" tabIndex="-1"><input type="checkbox" class="checkWidgWizCol" data-fieldTitle="healthiness"/>&nbsp;<?= _("Healthiness")?></a></li>
                                    <li><a href="#" class="small" data-value="option13" tabIndex="-1"><input type="checkbox" class="checkWidgWizCol" data-fieldTitle="lastCheck"/>&nbsp;<?= _("Last check")?></a></li>
                                    <li><a href="#" class="small" data-value="option14" tabIndex="-1"><input type="checkbox" class="checkWidgWizCol" data-fieldTitle="ownership"/>&nbsp;<?= _("Ownership")?></a></li>
                                </ul>
                            </div>
                        </div>
                    </div>   

                    <!-- Pulsante di reset -->
                    <div class="col-xs-12 col-md-1 centerWithFlex">
                        <button type="button" class="btn cancelBtn" id="resetButton"><?= _("Reset filters")?></button>
                    </div> 

                    <div id="widgetWizardTableSelectedRowsCounter" data-selectedRows="0" class="addWidgetWizardIconsCntLabel addWidgetWizardTableLbl col-xs-12 col-md-2 centerWithFlex">
                        <?= _("Selected rows: 0")?>
                    </div> 
                </div> 
            </div>

           <?php if($synMode || isCreationWizardUI()) { ?>
				<!-- Riga tabella righe selezionate -->
				<div class="row hideIfActuatorNew hideFullyCustom" id="widgetWizardSelectedRowsTableRow">
					<div class="col-xs-12 addWidgetWizardIconsCntLabel centerWithFlex">
						<?= _("Choosen data sources")?>
					</div>
					<div class="col-xs-12 addWidgetWizardIconsCntAlertLabel centerWithFlex" id="wizardNotCompatibleRowsAlert">
						<?= _("Red rows are not compatible with choosen widget type and will not be instantiated")?>
					</div>
					<div id="widgetWizardSelectedRowsTableContainer" class="col-xs-12">
						<table id="widgetWizardSelectedRowsTable" class="addWidgetWizardTableSelected table table-striped nowrap" style="margin-left:-15px">
							<thead class="widgetWizardColTitle">
								<tr>
                                    <th id="hihghLevelTypeColTitle" class="widgetWizardTitleCell" data-cellTitle="HighLevelType"><?= _("High-Level Type")?></th>  <!-- Potrebbe diventare DEVICE TYPE ??? -->
                                    <th class="widgetWizardTitleCell" data-cellTitle="Nature"><?= _("Nature")?></th>
                                    <th class="widgetWizardTitleCell" data-cellTitle="SubNature"><?= _("Subnature")?></th>
                                <!--    <th class="widgetWizardTitleCell" data-cellTitle="ValueType"><?= _("Value Type")?></th> -->   <!-- Ex LOW_LEVEL_TYPE -->
                                <!--    <th class="widgetWizardTitleCell" data-cellTitle="ValueName"><?= _("Value Name")?></th> -->      <!-- Ex NAME-ID -->
                                    <!--<th class="widgetWizardTitleCell" data-cellTitle="InstanceUri">Instance URI</th>-->
                                    <th class="widgetWizardTitleCell" data-cellTitle="DeviceModel"><?= _("Device")?></th>
                                    <th class="widgetWizardTitleCell" data-cellTitle="Model"><?= _("Model")?></th>
                                    <th class="widgetWizardTitleCell" data-cellTitle="Broker"><?= _("Broker")?></th>
                                    <th class="widgetWizardTitleCell" data-cellTitle="ValueNameN"><?= _("Value Name")?></th>    <!-- New VALUE NAME -->
                                    <th class="widgetWizardTitleCell" data-cellTitle="ValueTypeN"><?= _("Value Type")?></th>
                                    <th class="widgetWizardTitleCell" data-cellTitle="DataType"><?= _("Data Type")?></th>    <!-- Ex UNIT -->
                                    <th class="widgetWizardTitleCell" data-cellTitle="valueUnit"><?= _("Value Unit")?></th>
                                    <th class="widgetWizardTitleCell" data-cellTitle="LastDate"><?= _("Last Date")?></th>
                                    <th class="widgetWizardTitleCell" data-cellTitle="LastValue"><?= _("Last Value")?></th>
                                    <th class="widgetWizardTitleCell" data-cellTitle="Healthiness"><?= _("Healthiness")?></th>
                                    <!--<th class="widgetWizardTitleCell" data-cellTitle="Parameters">Parameters</th>
                                    <th class="widgetWizardTitleCell" data-cellTitle="Id">Id</th>-->
                                    <th class="widgetWizardTitleCell" data-cellTitle="LastCheck"><?= _("Last Check")?></th>
                                    <!--<th class="widgetWizardTitleCell" data-cellTitle="GetInstances"></th>-->
                                    <th class="widgetWizardTitleCell" data-cellTitle="Ownership"><?= _("Ownership")?></th>
                                    <th class="widgetWizardTitleCell" data-cellTitle="Remove"><?= _("Remove")?></th>
								</tr>  							
							</thead>
						</table>
					</div>
				</div><!-- Fine riga tabella righe selezionate -->

				<!-- Comandi tabella -->
				<div class="row hideIfActuatorNew hideFullyCustom" style="padding-left: 15px; padding-right: 15px;">
					<div id="widgetWizardSelectedRowsTableCommandsContainer" class="col-xs-12">
						
					</div> 
				</div> 
                <?php if($synMode){ ?>
				    <button id="save" type="button" class="btn confirmBtn" style="float:right; margin-right:15px; margin-top:15px;"><?= _("Save")?></button>
                <?php } ?>
            <?php } ?>
			
            <!-- Messaggi d'errore o di ok -->
            <div class="row" style="padding-left: 15px; padding-right: 15px;">
                <div id="wizardTab1MsgCnt" class="col-xs-12 centerWithFlex">

                </div> 
            </div> 
            
        </div>
    </div>    
                                
    <div id="summary" class="tab-pane fade in">
        <div id="summaryContainer" class="col-xs-12">
            <div class="col-xs-12 addWidgetWizardIconsCntLabelBig centerWithFlex"><?= _("Summary")?></div>
            <div class="col-xs-12 addWidgetWizardIconsCntSublabel centerWithFlex"><?= _("A synthesis of your choices and what is going to be created")?></div>
            <div class="col-xs-12" id="summaryDiv">
                
            </div>   
        </div>
        <div class="col-xs-12 col-sm-4 col-sm-offset-1" id="checkContainer">
            <div class="col-xs-12 addWidgetWizardIconsCntLabelBig centerWithFlex"><?= _("Check")?></div>
            <div class="col-xs-12 addWidgetWizardIconsCntSublabel centerWithFlex"><?= _("Alerts about possible missing or wrong input that deny instantiation")?></div>
            <div class="col-xs-12" id="wrongConditionsDiv">
                
            </div>
        </div>
        <div class="col-xs-12 col-sm-4 col-sm-offset-2" id="instantiateBtnContainer">
            <div class="col-xs-12 addWidgetWizardIconsCntLabelBig centerWithFlex"><?= _("Instantiation")?></div>
            <div class="col-xs-12 addWidgetWizardIconsCntSublabel centerWithFlex"><?= _("Button to proceed with items creation")?></div>
            <div class="col-xs-12 centerWithFlex" id="createBtnDiv">
                <button type="button" id="addWidgetWizardConfirmBtn" name="addWidgetWizardConfirmBtn" class="btn confirmBtn"><i class="fa fa-magic" style="font-size: 90px"></i></button>
            </div>
            <div class="col-xs-12 centerWithFlex">
                <?= _("Create dashboard/widgets")?>
            </div>    
            <div class="col-xs-12 centerWithFlex" id="createBtnAlert">
                <div class="col-xs-12"><div class="col-xs-12 centerWithFlex"><i class="fa fa-exclamation-triangle validityConditionIcon"></i></div><div class="col-xs-12 centerWithFlex"><span class="validityConditionLbl"><?= _("You can't proceed with items creation before fixing wrong or missing inputs")?></span></div></div>
            </div>
        </div>
    </div>    
</div>
            <?php /*
                include('../config.php');
                header("Cache-Control: private, max-age=$cacheControlMaxAge");
                $link = mysqli_connect($host, $username, $password);
                mysqli_select_db($link, $dbname);

                    $menuQuery = "select id, latitude, longitude from Dashboard.DashboardWizard where high_level_type='Sensor' and latitude is not null and longitude is not null and latitude<>'' and longitude<>''";
                    $r = mysqli_query($link, $menuQuery);
                    if($r)
                    {
                        $array=array();
                        $ii=0;
                        while($row = mysqli_fetch_assoc($r))
                        {
                            $idrow=$row['id'];
                            $latitude=$row['latitude'];
                            $longitude=$row['longitude'];
                            $array[$ii]['id']=$idrow;
                            $array[$ii]['latitude']=$latitude;
                            $array[$ii]['longitude']=$longitude;
                            $ii=$ii+1;
       }
       $size = sizeof($array);
              
             
   }
             * 
             */
                ?>

<script type='text/javascript'>
    $(document).ready(function ()
    {
        var widgetWizardTable = null;
        var widgetWizardSelectedRowsTable, addWidgetWizardMapRef, widgetWizardPageLength = null;
        var gisLayersOnMap = {};
        var widgetWizardSelectedRows = {};
        var widgetWizardSelectedSingleRow = null;
        var widgetWizardSelectedUnits = [];
        var choosenWidgetIconName = null;
        var choosenDashboardTemplateName = null;
        var choosenDashboardTemplateIcon = null;
        var widgetWizardMapSelection = null;
        var currentSelectedRowsCounter = 0;
        var selectedTabIndex = 0;
        var firstTabIndex = 0;
        var tabsQt = 3;
        var invisibleCols;
        var orgFilter = "<?php if (isset($_SESSION['loggedOrganization'])){echo $_SESSION['loggedOrganization'];} else {echo "Other";} ?>";
        addWidgetWizardMapMarkers = {};
        currentMarkerId = 0;
        orgId = null;
        orgName = null;
        orgKbUrl = null;
        orgGpsCentreLatLng = null;
        orgZoomLevel = null;
        console.log("Entrato in addWidgetWizardInclusionCodeOS.");
        var orionBrokerValueTypes = null;
        var markersCache = {};
        var FreezeMap = null;
        noPOIFlag = -1;
        poiSubNature = "";
        poiNature = "";
        poiNatureArray = [];
        poiSubNatureArray = [];
        var bounds = null;

        function ObjectSize(obj)
        {
            var size = 0, key;
            for (key in obj) {
                if (obj.hasOwnProperty(key)) size++;
            }
            return size;
        }

        // Check LDAP Organization to centre Wizard Map
        $.ajax({
            url: "../controllers/getOrganizationParameters.php",
            type: "GET",
            data: {
                action: "getAllParameters"
            },
            async: true,
            dataType: 'json',
            success: function (data)
            {
                if (data.detail === 'GetOrganizationParameterOK') {
                    orgId = data.orgId;
                    orgName = data.orgName;
                    orgKbUrl = data.orgKbUrl;
                    orgGpsCentreLatLng = data.orgGpsCentreLatLng;
                    orgZoomLevel = data.orgZoomLevel;
                } else {
                
                }

            },
            error: function (data)
            {
                console.log("Error in retrieving Organization Parameters:");
                console.log(JSON.stringify(data));
            }
        });

        // Check Orion Broker Value types
        $.ajax({
            url: "../controllers/getOrionBrokerValueTypes.php",
            type: "GET",
            data: {
                action: "getAllTypes"
            },
            async: true,
            dataType: 'json',
            success: function (data)
            {
                if (data.detail === 'ValueTypes_OK') {
                    orionBrokerValueTypes = data.valueTypeRows;
                    var $actuatorValuesMenuDrop = $("#actuatorValueType");
                    $.each(orionBrokerValueTypes, function(idx) {
                    //    $actuatorValuesMenuDrop.append($("<option />").val(idx).text(orionBrokerValueTypes[idx]));
                        $actuatorValuesMenuDrop.append($("<option />").val(orionBrokerValueTypes[idx]).text(orionBrokerValueTypes[idx]));
                    });
                } else {

                }
                var stopFlag = 1;

            },
            error: function (data)
            {
                console.log("Error in retrieving Orion Broker Value Types:");
                console.log(JSON.stringify(data));
            }
        });

        if ("<?= $_SESSION['loggedRole'] ?>" == "RootAdmin") {
            invisibleCols = [3, 4, 5, 16, 17, 18, 20, 22, 23, 24, 25, 26, 27, 28];
        } else {
            invisibleCols = [3, 4, 5, 16, 17, 18, 20, 22, 23, 24, 25, 26, 27, 28];
        }

        //False se è violata, true se è rispettata
        var validityConditions = {
            dashTemplateSelected: false,
            widgetTypeSelected: false,
            brokerAndNrRowsTogether: true,
            atLeastOneRowSelected: false,
            actuatorFieldsEmpty: true,
            canProceed: false
        };
        $('#GPSOrg').click(function(){
            if(FreezeMap) {
                $('#togBtn').click();
            }
            //    addWidgetWizardMapRef.setView(L.latLng(43.769710, 11.255751), 11);
            if (orgGpsCentreLatLng != undefined && orgGpsCentreLatLng != null) {
                if (orgZoomLevel != null && orgZoomLevel != undefined) {
                    addWidgetWizardMapRef.setView(L.latLng(orgGpsCentreLatLng.split(",")[0].trim(), orgGpsCentreLatLng.split(",")[1].trim()), orgZoomLevel);
                } else {
                    addWidgetWizardMapRef.setView(L.latLng(orgGpsCentreLatLng.split(",")[0].trim(), orgGpsCentreLatLng.split(",")[1].trim()), 11);
                }
            } else {
                addWidgetWizardMapRef.setView(L.latLng(43.769710, 11.255751), 11);  // Florence coordinates if no organization available
            }

        })
        $('#GPSUser').click(function(){
            if(FreezeMap) {
                $('#togBtn').click();
            }
            if (navigator.geolocation)
                navigator.geolocation.getCurrentPosition(showPosition);
            //navigator.geolocation.watchPosition(showPosition);
        })
		$('#SynMode').click(function(){
			window.location.href='inspectorOS.php?synMode=1';
        })
		$('#StdMode').click(function(){
			window.location.href='inspectorOS.php?synMode=0';
        })
        function showPosition(position) {
            
            var Zoom = addWidgetWizardMapRef.getZoom();
            addWidgetWizardMapRef=addWidgetWizardMapRef.setView(L.latLng(position.coords.latitude, position.coords.longitude), Zoom);
        }
        $('#togBtn').click(function(){

            /*$.ajax({
                url: "../controllers/getPOIFilters.php",
                type: "GET",
                data: {
                    org: orgFilter
                },
                async: true,
                dataType: 'json',
                success: function (data)
                {
                    if (data.detail === 'All Queries OK!') {
                        poiNatureArray = data.nature;
                        poiSubNatureArray = data.sub_nature;
                    } else {

                    }
                },
                error: function (data)
                {
                    console.log("Error in retrieving POI nature and sub_nature:");
                    console.log(JSON.stringify(data));
                }
            });*/
            
           if(FreezeMap){
               addWidgetWizardMapRef.addControl(addWidgetWizardMapRef.zoomControl);
               FreezeMap = null;
               addWidgetWizardMapRef.dragging.enable();
               addWidgetWizardMapRef.keyboard.enable();
               addWidgetWizardMapRef.scrollWheelZoom.enable();
               addWidgetWizardMapRef.doubleClickZoom.enable();
               //disattivo il sensore che ho selezionato prima di ricreare la tabella, altrimenti si genera in seguito un errore nella selezione degli elementi della tabella
           //    if(widgetWizardSelectedSingleRow!==null){
                //    $(widgetWizardSelectedSingleRow).removeClass('selected');
                //    var delesectedUnit = widgetWizardSelectedRows['row' + $(widgetWizardSelectedSingleRow).attr('data-rowid')].unit;
                //    delete widgetWizardSelectedRows['row' + $(widgetWizardSelectedSingleRow).attr('data-rowid')];
                
               //     widgetWizardSelectedRowsTable.row('[data-rowid=' + $(widgetWizardSelectedSingleRow).attr('data-rowid') + ']').remove().draw(false);
                
                    //Aggiornamento unità selezionate
                //    updateSelectedUnits('remove', delesectedUnit);
                //    widgetWizardSelectedSingleRow=null;
           //     }
                resetFilter();
               widgetWizardTable.clear().destroy();
            var oi=document.getElementById('widgetWizardTable_paginate');
            oi.outerHTML="";
            $('#widgetWizardTable_paginate').empty();
            var oi2=document.getElementById('widgetWizardTable_filter');
            oi2.outerHTML="";
               $('#widgetWizardTable_filter').empty();
                 widgetWizardTable = $('#widgetWizardTable').DataTable({
                 
            "bLengthChange": false,
            "bInfo": false,
            "language": {search: ""},
            aaSorting: [[0, 'asc']],
            "processing": true,
            "serverSide": true,
            "pageLength": widgetWizardPageLength,
            "ajax": {
                async: true, 
                url: "../controllers/dashboardWizardControllerOS.php?initWidgetWizard=true",
                data: function(d){
                    d.dashUsername = "<?= $_SESSION['loggedUsername'] ?>";
                    d.dashUserRole = "<?= $_SESSION['loggedRole'] ?>";
                    d.filterOrg = orgFilter,
                    d.poiFlag = noPOIFlag;
                    d.synMode = "<?=$synMode?$synMode:0?>";
                    d.search_bar = using_search_bar;
                    using_search_bar = false;
                    d.custom_search_per_column = custom_search_per_column;
                }
            },
            'createdRow': function (row, data, dataIndex) {
                $(row).attr('data-rowId', data[18]);
                $(row).attr('data-high_level_type', data[0]);
                $(row).attr('data-nature', data[1]);
                $(row).attr('data-sub_nature', data[2]);
                $(row).attr('data-low_level_type', data[3]);
                $(row).attr('data-unique_name_id', data[4]);
                $(row).attr('data-instance_uri', data[5]);
                $(row).attr('data-value_name',data[25]);
                $(row).attr('data-value_type',data[26]);
                $(row).attr('data-device_model_name',data[27]);
                $(row).attr('data-broker_name',data[28]);
                $(row).attr('data-unit', data[11]);
                $(row).attr('data-servicetype', data[2]);
                $(row).attr('data-get_instances', data[20]);
                $(row).attr('data-sm_based', data[25]);
                $(row).attr('data-parameters', data[17]);
                $(row).attr('data-selected', 'false');
                $(row).attr('data-last_value', data[14]);
                $(row).attr('data-latitude', data[23]);
                $(row).attr('data-longitude', data[24]);
                $(row).attr('data-organizations', data[22]);
                $(row).attr('last_date',data[13]);
                $(row).attr('data-valueUnit',data[12]);
                $(row).attr('ownership',data[21]);
				if(data.length > 19) $(row).attr('data-last_check',data[19]);
				if(data.length > 15) $(row).attr('data-healthiness',data[15]);



            },
            "columnDefs": [
                {
                    "targets": 0,
                    "searchable": true,
                    "render": function (data, type, row, meta) {
                        return '<button style="margin-right:2px;" class="dt">DT</button>'+'<span>'+row[0]+'</span>';
                    }
                },
                {
                    "targets": 15,
                    "searchable": true,
                    "render": function (data, type, row, meta) {

                        var imageUrl = null;
                        if (row[15]) {
                            if (row[15] === 'true') {
                                imageUrl = "<i class='fa fa-circle' style='font-size:16px;color:#33cc33'></i>";
                            } else {
                                imageUrl = "<i class='fa fa-circle' style='font-size:16px;color:#ff3300'></i>";
                            }
                        } else {
                            imageUrl = "<i class='fa fa-circle' style='font-size:16px;color:#ff3300'></i>";
                        }
                        return imageUrl;
                    }
                },
                {
                    "targets": 16,
                    "searchable": true,
                    "visible": false
                },
                {
                //    "targets": [5, 12, 13, 15, 17, 18, 19, 20, 21, 22, 23],
                    "targets": invisibleCols,
                    "visible": false
                }
            ],
            initComplete: function () {
                $("#widgetWizardTable_wrapper").css("overflow-x", "auto");
                $("#widgetWizardTable_wrapper").css("overflow-y", "hidden");
            },
            fnDrawCallback: function(){
                $('#widgetWizardTable td').each(function(){
                    $(this).attr("title",$(this).text());
                });

                $("#widgetWizardTable td").each(function(){
                    $('body>.tooltip').remove();
                    $(this).tooltip({container:'body'});
                    if($(this).find("span").length > 0){
                        $(this).attr("data-title",$(this).find("span").text());
                        $(this).attr("data-original-title",$(this).find("span").text());

                    }
                });
            }
        });

        $("#widgetWizardTable_paginate").appendTo("#widgetWizardTableCommandsContainer");
            $("#widgetWizardTable_paginate").addClass("col-xs-12");
            $("#widgetWizardTable_paginate").addClass("col-md-4");
            $('#widgetWizardTable_filter').appendTo("#widgetWizardTableCommandsContainer");
            $("#widgetWizardTable_filter").addClass("col-xs-12");
            $("#widgetWizardTable_filter").addClass("col-md-3");
            $("#widgetWizardTable_filter input").attr("placeholder", "Search");
            $("#widgetWizardTable_paginate .pagination").css("margin-top", "0px !important");
            $("#widgetWizardTable_paginate .pagination").css("margin-bottom", "0px !important");
               
           }else{
               addWidgetWizardMapRef.removeControl(addWidgetWizardMapRef.zoomControl);
               FreezeMap = true;
               addWidgetWizardMapRef.dragging.disable();
               addWidgetWizardMapRef.keyboard.disable();
               addWidgetWizardMapRef.scrollWheelZoom.disable();
               addWidgetWizardMapRef.doubleClickZoom.disable();
               bounds = addWidgetWizardMapRef.getBounds();
               var northEastPointLat = bounds._northEast.lat;
               var northEastPointLat2 = northEastPointLat.toString();
               var northEastPointLng = bounds._northEast.lng;
               var northEastPointLng2 = northEastPointLng.toString();
               var southWestPointLat = bounds._southWest.lat;
               var southWestPointLat2 = southWestPointLat.toString();
               var southWestPointLng = bounds._southWest.lng;
               var southWestPointLng2 = southWestPointLng.toString();
               resetFilter();
               widgetWizardTable.clear().destroy();//la mappa viene distrutta e ricreata ad ogni volta che viene azionato il bottone
               var oi=document.getElementById('widgetWizardTable_paginate');
               oi.outerHTML="";
               $('#widgetWizardTable_paginate').empty();
               var oi2=document.getElementById('widgetWizardTable_filter');
               oi2.outerHTML="";
               $('#widgetWizardTable_filter').empty();
               widgetWizardTable = $('#widgetWizardTable').DataTable({

                    "bLengthChange": false,
                    "bInfo": false,
                    "language": {search: ""},
                    aaSorting: [[0, 'asc']],
                    "processing": true,
                    "serverSide": true,
                    "pageLength": widgetWizardPageLength,
                    "ajax": {
                        async: true,
                        url: "../controllers/dashboardWizardControllerOS.php?initWidgetWizard=true&synMode=<?=$synMode?$synMode:0?>",
                        data: function(d){
                            d.dashUsername = "<?= $_SESSION['loggedUsername'] ?>";
                            d.dashUserRole = "<?= $_SESSION['loggedRole'] ?>";
                            d.northEastPointLat = northEastPointLat;
                            d.northEastPointLng = northEastPointLng;
                            d.southWestPointLat = southWestPointLat;
                            d.southWestPointLng = southWestPointLng;
                            d.filterOrg = orgFilter;
                            d.poiFlag = getPOIFlag();
                            d.poiNature = getPOINature();
                            d.poiSubNature = getPOISubNature();
                            d.search_bar = using_search_bar;
                            using_search_bar = false;
                            d.custom_search_per_column = custom_search_per_column;
                        }
                    },
                    'createdRow': function (row, data, dataIndex) {
                        $(row).attr('data-rowId', data[18]);
                        $(row).attr('data-high_level_type', data[0]);
                        $(row).attr('data-nature', data[1]);
                        $(row).attr('data-sub_nature', data[2]);
                        $(row).attr('data-low_level_type', data[3]);
                        $(row).attr('data-unique_name_id', data[4]);
                        $(row).attr('data-instance_uri', data[5]);
                        $(row).attr('data-value_name',data[25]);
                        $(row).attr('data-value_type',data[26]);
                        $(row).attr('data-device_model_name',data[27]);
                        $(row).attr('data-broker_name',data[28]);
                        $(row).attr('data-unit', data[11]);
                        $(row).attr('data-servicetype', data[2]);
                        $(row).attr('data-get_instances', data[20]);
                        $(row).attr('data-sm_based', data[25]);
                        $(row).attr('data-parameters', data[17]);
                        $(row).attr('data-selected', 'false');
                        $(row).attr('data-last_value', data[14]);
                        $(row).attr('data-latitude', data[23]);
                        $(row).attr('data-longitude', data[24]);
                        $(row).attr('data-organizations', data[22]);
                        $(row).attr('last_date',data[13]);
                        $(row).attr('data-valueUnit',data[12]);
                        $(row).attr('ownership',data[21]);
                        if(data.length > 19) $(row).attr('data-last_check',data[19]);
                        if(data.length > 15) $(row).attr('data-healthiness',data[15]);
                    },
                    "columnDefs": [
                        
                        {
                            "targets": 0,
                            "searchable": true,
                            "render": function (data, type, row, meta) {
                                return '<button style="margin-right:2px;" class="dt">DT</button>'+'<span>'+row[0]+'</span>';
                            }
                        },
                        {
                            "targets": 15,
                            "searchable": true,
                            "render": function (data, type, row, meta) {
                                var imageUrl = null;
                                if (row[15]) {
                                    if (row[15] === 'true') {
                                        imageUrl = "<i class='fa fa-circle' style='font-size:16px;color:#33cc33'></i>";
                                    } else {
                                        imageUrl = "<i class='fa fa-circle' style='font-size:16px;color:#ff3300'></i>";
                                    }
                                } else {
                                    imageUrl = "<i class='fa fa-circle' style='font-size:16px;color:#ff3300'></i>";
                                }
                                return imageUrl;
                            }
                        },
                        {
                            "targets": 16,
                            "searchable": true,
                            "visible": false
                        },
                        {
                        //    "targets": [5, 12, 13, 15, 17, 18, 19, 20, 21, 22, 23],
                            "targets": invisibleCols,
                            "visible": false
                        }
                    ],
                    fnDrawCallback: function(){
                        $('#widgetWizardTable td').each(function(){
                            $(this).attr("title",$(this).text());
                        });

                        $("#widgetWizardTable td").each(function(){
                            $('body>.tooltip').remove();
                            $(this).tooltip({container:'body'});
                            if($(this).find("span").length > 0){
                                $(this).attr("data-title",$(this).find("span").text());
                                $(this).attr("data-original-title",$(this).find("span").text());

                            }
                        });
                    },
                    initComplete: function () {

                        $("#widgetWizardTable_wrapper").css("overflow-x", "auto");
                        $("#widgetWizardTable_wrapper").css("overflow-y", "hidden");

                        $("#widgetWizardTable_paginate").appendTo("#widgetWizardTableCommandsContainer");
                        $("#widgetWizardTable_paginate").addClass("col-xs-12");
                        $("#widgetWizardTable_paginate").addClass("col-md-4");
                        $('#widgetWizardTable_filter').appendTo("#widgetWizardTableCommandsContainer");
                        $("#widgetWizardTable_filter").addClass("col-xs-12");
                        $("#widgetWizardTable_filter").addClass("col-md-3");
                        $("#widgetWizardTable_filter input").attr("placeholder", "Search");
                        $("#widgetWizardTable_paginate .pagination").css("margin-top", "0px !important");
                        $("#widgetWizardTable_paginate .pagination").css("margin-bottom", "0px !important");

                        //window.history.pushState({"northEastPointLat":northEastPointLat,"southWestPointLat":southWestPointLat,"northEastPointLng":northEastPointLng,"southWestPointLng":southWestPointLng},"",url);
                        //window.location.href = window.location.href + "&northEastPointLat="+ northEastPointLat + "&southWestPointLat="+ southWestPointLat + "&northEastPointLng="+ northEastPointLng + "&southWestPointLng="+ southWestPointLng;
                        <?php
                        /*include('../config.php');
                        header("Cache-Control: private, max-age=$cacheControlMaxAge");
                        $link = mysqli_connect($host, $username, $password);
                        mysqli_select_db($link, $dbname);
                        $northEastPointLat= $_GET['northEastPointLat'];
                        $northEastPointLng= $_GET['northEastPointLng'];
                        $southWestPointLat= $_GET['southWestPointLat'];
                        $southWestPointLng= $_GET['southWestPointLng'];
                        $menuQuery = "select id, latitude, longitude from Dashboard.DashboardWizard where latitude is not null and longitude is not null and latitude<>'' and longitude<>'' and latitude <='".mysqli_real_escape_string($link,$northEastPointLat)."' and latitude >='".mysqli_real_escape_string($link,$southWestPointLat)."' and longitude <='".mysqli_real_escape_string($link,$northEastPointLng)."' and longitude >='".mysqli_real_escape_string($link,$southWestPointLng)."'";
                        $r = mysqli_query($link, $menuQuery);
                        if($r)
                        {
                            $array=array();
                            $ii=0;
                            while($row = mysqli_fetch_assoc($r))
                            {
                                $idrow=$row['id'];
                                $latitude=$row['latitude'];
                                $longitude=$row['longitude'];
                                $array[$ii]['id']=$idrow;
                                $array[$ii]['latitude']=$latitude;
                                $array[$ii]['longitude']=$longitude;
                                $ii=$ii+1;
                            }
                            $size = sizeof($array);
                        }*/
                        ?>

                        // Chiamata a funzione per popolare menù multi-select di filtraggio
                        for(var n = 0; n < globalSqlFilter.length; n++) {
                            if(n !== 4 && n != 5) {
                                // populateSelectMenus("none", "oiunqauhalknsufhvnoqwpnvfv", unitSelect, "#unitColumnFilter", n, true, false, FreezeMap, bounds);
                                populateSelectMenus("", "", null, "", n, false, true, FreezeMap, bounds);
                            }
                        }
                    }
                });


            
               
           }
       })
        
        function updateSelectedUnits(mode, deselectedUnit)
        {
            if(mode === 'add')
            {
                console.log(widgetWizardSelectedRows)
                for(var key in widgetWizardSelectedRows)
                {
                    console.log(key,widgetWizardSelectedRows[key])
                    if(!widgetWizardSelectedUnits.includes(widgetWizardSelectedRows[key].unit))
                    {
                        widgetWizardSelectedUnits.push(widgetWizardSelectedRows[key].unit);
                        console.log("Unit added: " + widgetWizardSelectedRows[key].unit);
                    }
                    else
                    {
                        console.log("Unit already present: " + widgetWizardSelectedRows[key].unit);
                    }
                }
            }
            else
            {
                var countSelected = 0;
                
                for(var key in widgetWizardSelectedRows)
                {
                    if(widgetWizardSelectedRows[key].unit === deselectedUnit)
                    {
                        countSelected++;
                    }
                }
                
                console.log("Removal di tipo: " + deselectedUnit + " - Count: " + countSelected);
                
                if(countSelected === 0)
                {
                    var removeIndex = widgetWizardSelectedUnits.indexOf(deselectedUnit);
                    if(removeIndex !== -1)
                    {
                        widgetWizardSelectedUnits.splice(removeIndex, 1);
                        console.log("Unità aggiornate: " + widgetWizardSelectedUnits);
                    }
                }
            }
        }
        
        function countSelectedRows()
        {
            currentSelectedRowsCounter = Object.keys(widgetWizardSelectedRows).length; 
            $('#widgetWizardTableSelectedRowsCounter').attr('data-selectedRows', currentSelectedRowsCounter);
            $('#widgetWizardTableSelectedRowsCounter').html('Selected rows: ' + currentSelectedRowsCounter);
        }
        
        function checkTab1Conditions(wizardSelectedRowsObj)
        {
            if((!location.href.includes("dashboard_configdash"))&&(choosenDashboardTemplateName === 'fullyCustom'))
            {
                //Fully custom
                //Primo stadio: se non selezioni tipo di widget, bloccato
                if(validityConditions.widgetTypeSelected)
                {
                    if($('.addWidgetWizardIconClickClass[data-selected="true"]').attr("data-widgetcategory") === "dataViewer")
                    {
                        //Data viewer: bastano widget type selezionato e righe selezionate
                        if(validityConditions.atLeastOneRowSelected)
                        {
                            $('#addWidgetWizardNextBtn').removeClass('disabled');
                            $('#cTab a').attr("data-toggle", "tab");
                            $('#wizardTab1MsgCnt').css('color', 'white');
                            $('#wizardTab1MsgCnt').html("Selection is OK");
                        }
                        else
                        {
                            $('#addWidgetWizardNextBtn').addClass('disabled');
                            $('#cTab a').attr("data-toggle", "no");
                            $('#wizardTab1MsgCnt').css('color', 'rgb(243, 207, 88)');
                            $('#wizardTab1MsgCnt').html("You must select at least one row");
                        }
                    }
                    else
                    {
                        //Attuatori
                        if($('#actuatorTargetInstance').val() === 'existent')
                        {
                            //Caso existent: va bene selezione righe e widget
                            if(validityConditions.atLeastOneRowSelected)
                            {
                                $('#addWidgetWizardNextBtn').removeClass('disabled');
                                $('#cTab a').attr("data-toggle", "tab");
                                $('#wizardTab1MsgCnt').css('color', 'white');
                                $('#wizardTab1MsgCnt').html("Selection is OK");
                            }
                            else
                            {
                                $('#addWidgetWizardNextBtn').addClass('disabled');
                                $('#cTab a').attr("data-toggle", "no");
                                $('#wizardTab1MsgCnt').css('color', 'rgb(243, 207, 88)');
                                $('#wizardTab1MsgCnt').html("You must select at least one row");
                            }
                        }
                        else
                        {
                            //Caso new
                            if(($('#actuatorTargetWizard').val() === 'broker')||($('#actuatorTargetWizard').val() === 'app'))
                            {
                                if($('#actuatorTargetWizard').val() === 'broker')
                                {
                                    //Caso broker: controlliamo tutti i campi
                                    if(($('#actuatorEntityName').val().trim() !== '')&&($('#actuatorValueType').val().trim() !== '')&&($('#actuatorMinBaseValue').val().trim() !== '')&&($('#actuatorMaxImpulseValue').val().trim() !== ''))
                                    {
                                        $('#addWidgetWizardNextBtn').removeClass('disabled');
                                        $('#cTab a').attr("data-toggle", "tab");
                                        $('#wizardTab1MsgCnt').css('color', 'white');
                                        $('#wizardTab1MsgCnt').html("Selection is OK");
                                    }
                                    else
                                    {
                                        $('#addWidgetWizardNextBtn').addClass('disabled');
                                        $('#cTab a').attr("data-toggle", "no");
                                        $('#wizardTab1MsgCnt').css('color', 'rgb(243, 207, 88)');
                                        $('#wizardTab1MsgCnt').html("Some of the new actuator fields are not filled correctly");
                                    }
                                }
                                else
                                {
                                    //Caso NodeRed: via libera, viene spiegato nel summary che non lo puoi fare
                                    $('#addWidgetWizardNextBtn').removeClass('disabled');
                                    $('#cTab a').attr("data-toggle", "tab");
                                    $('#wizardTab1MsgCnt').css('color', 'white');
                                    $('#wizardTab1MsgCnt').html("Selection is OK");
                                }
                            }
                            else
                            {
                                $('#addWidgetWizardNextBtn').addClass('disabled');
                                $('#cTab a').attr("data-toggle", "no");
                                $('#wizardTab1MsgCnt').css('color', 'rgb(243, 207, 88)');
                                $('#wizardTab1MsgCnt').html("You must select actuator target type");
                            }
                        }
                    }
                }
                else
                {
                    if ($('#modalAddDashboardWizardTemplateMsg')[0].outerText != "Template choosen OK" || $('#modalAddDashboardWizardTitleAlreadyUsedMsg')[0].outerText != "Dashboard title OK") {
                        $('#addWidgetWizardNextBtn').addClass('disabled');
                        $('#cTab a').attr("data-toggle", "no");
                        $('#wizardTab1MsgCnt').css('color', 'rgb(243, 207, 88)');
                        $('#wizardTab1MsgCnt').html("You must select one widget type");
                    }
                }
            }
            else
            {
                if(!location.href.includes("dashboard_configdash"))
                {
                    //TUTTI I CASI DI DASHBOARD WIZARD ESCLUSA FULLY CUSTOM
                    //Dashboard template con tipo di widget preselezionato, controlliamo solo se c'è almeno una riga selezionata
                    if($('.modalAddDashboardWizardChoiceCnt[data-selected="true"]').attr('data-widgettype') !== 'any')
                    {
                        if(validityConditions.atLeastOneRowSelected)
                        {
                            $('#addWidgetWizardNextBtn').removeClass('disabled');
                            $('#cTab a').attr("data-toggle", "tab");
                            $('#wizardTab1MsgCnt').css('color', 'white');
                            $('#wizardTab1MsgCnt').html("Selection is OK");
                        }
                        else
                        {
                            if ($('#modalAddDashboardWizardTemplateMsg')[0].outerText != "Template choosen OK" || $('#modalAddDashboardWizardTitleAlreadyUsedMsg')[0].outerText != "Dashboard title OK") {
                                $('#addWidgetWizardNextBtn').addClass('disabled');
                                $('#cTab a').attr("data-toggle", "no");
                                $('#wizardTab1MsgCnt').css('color', 'rgb(243, 207, 88)');
                                $('#wizardTab1MsgCnt').html("You must select at least one row");
                            }
                        }
                    }
                    else
                    {
                        //Dashboard template con tipo di widget LIBERO, albero dei controlli più articolato
                        
                        //Primo stadio: se non selezioni tipo di widget, bloccato
                        if(validityConditions.widgetTypeSelected)
                        {
                            //Events vs map: va bene selezione righe e widget
                            //if($('.modalAddDashboardWizardChoiceCnt[data-selected="true"]').attr('data-templatename') === 'eventsVsMap')
                            if($('.modalAddDashboardWizardChoiceCnt[data-selected="true"]').attr('data-hasactuators') === 'false')
                            {
                                $('#addWidgetWizardNextBtn').removeClass('disabled');
                                $('#cTab a').attr("data-toggle", "tab");
                                $('#wizardTab1MsgCnt').css('color', 'white');
                                $('#wizardTab1MsgCnt').html("Selection is OK");
                            }
                            else
                            {
                                //Casi iot
                                //if(($('.modalAddDashboardWizardChoiceCnt[data-selected="true"]').attr('data-templatename') === 'iotDevicesBroker')||($('.modalAddDashboardWizardChoiceCnt[data-selected="true"]').attr('data-templatename') === 'iotApps'))
                                //{
                                    if($('#actuatorTargetInstance').val() === 'existent')
                                    {
                                        //Caso existent: va bene selezione righe e widget
                                        if(validityConditions.atLeastOneRowSelected)
                                        {
                                            $('#addWidgetWizardNextBtn').removeClass('disabled');
                                            $('#cTab a').attr("data-toggle", "tab");
                                            $('#wizardTab1MsgCnt').css('color', 'white');
                                            $('#wizardTab1MsgCnt').html("Selection is OK");
                                        }
                                        else
                                        {
                                            $('#addWidgetWizardNextBtn').addClass('disabled');
                                            $('#cTab a').attr("data-toggle", "no");
                                            $('#wizardTab1MsgCnt').css('color', 'rgb(243, 207, 88)');
                                            $('#wizardTab1MsgCnt').html("You must select at least one row");
                                        }
                                    }
                                    else
                                    {
                                        //Caso new
                                        if(($('#actuatorTargetWizard').val() === 'broker')||($('#actuatorTargetWizard').val() === 'app'))
                                        {
                                            if($('#actuatorTargetWizard').val() === 'broker')
                                            {
                                                //Caso broker: controlliamo tutti i campi
                                                if(($('#actuatorEntityName').val().trim() !== '')&&(!$('#actuatorEntityName').val().includes(' '))&&($('#actuatorValueType').val().trim() !== '')&&($('#actuatorMinBaseValue').val().trim() !== '')&&($('#actuatorMaxImpulseValue').val().trim() !== ''))
                                                {
                                                    $('#addWidgetWizardNextBtn').removeClass('disabled');
                                                    $('#cTab a').attr("data-toggle", "tab");
                                                    $('#wizardTab1MsgCnt').css('color', 'white');
                                                    $('#wizardTab1MsgCnt').html("Selection is OK");
                                                }
                                                else
                                                {
                                                    $('#addWidgetWizardNextBtn').addClass('disabled');
                                                    $('#cTab a').attr("data-toggle", "no");
                                                    $('#wizardTab1MsgCnt').css('color', 'rgb(243, 207, 88)');
                                                    $('#wizardTab1MsgCnt').html("Some of the new actuator fields are not filled correctly");
                                                }
                                            }
                                            else
                                            {
                                                //Caso NodeRed: via libera, viene spiegato nel summary che non lo puoi fare
                                                $('#addWidgetWizardNextBtn').removeClass('disabled');
                                                $('#cTab a').attr("data-toggle", "tab");
                                                $('#wizardTab1MsgCnt').css('color', 'white');
                                                $('#wizardTab1MsgCnt').html("Selection is OK");
                                            }
                                        }
                                        else
                                        {
                                            $('#addWidgetWizardNextBtn').addClass('disabled');
                                            $('#cTab a').attr("data-toggle", "no");
                                            $('#wizardTab1MsgCnt').css('color', 'rgb(243, 207, 88)');
                                            $('#wizardTab1MsgCnt').html("You must select actuator target type");
                                        }
                                    }
                                //}
                            }
                        }
                        else
                        {
                            if ($('#modalAddDashboardWizardTemplateMsg')[0].outerText != "Template choosen OK" || $('#modalAddDashboardWizardTitleAlreadyUsedMsg')[0].outerText != "Dashboard title OK") {
                                $('#addWidgetWizardNextBtn').addClass('disabled');
                                $('#cTab a').attr("data-toggle", "no");
                                $('#wizardTab1MsgCnt').css('color', 'rgb(243, 207, 88)');
                                $('#wizardTab1MsgCnt').html("You must select one widget type");
                            }
                        }
                    }
                }
                else
                {
                    //Widget wizard
                    //Primo stadio: se non selezioni tipo di widget, bloccato
                    if(validityConditions.widgetTypeSelected)
                    {
                        if($('.addWidgetWizardIconClickClass[data-selected="true"]').attr("data-widgetcategory") === "dataViewer")
                        {
                            //Data viewer: bastano widget type selezionato e righe selezionate
                            if(validityConditions.atLeastOneRowSelected)
                            {
                                $('#addWidgetWizardNextBtn').removeClass('disabled');
                                $('#cTab a').attr("data-toggle", "tab");
                                $('#wizardTab1MsgCnt').css('color', 'white');
                                $('#wizardTab1MsgCnt').html("Selection is OK");
                            }
                            else
                            {
                                $('#addWidgetWizardNextBtn').addClass('disabled');
                                $('#cTab a').attr("data-toggle", "no");
                                $('#wizardTab1MsgCnt').css('color', 'rgb(243, 207, 88)');
                                $('#wizardTab1MsgCnt').html("You must select at least one row");
                            }
                        }
                        else
                        {
                            //Attuatori
                            if($('#actuatorTargetInstance').val() === 'existent')
                            {
                                //Caso existent: va bene selezione righe e widget
                                if(validityConditions.atLeastOneRowSelected)
                                {
                                    $('#addWidgetWizardNextBtn').removeClass('disabled');
                                    $('#cTab a').attr("data-toggle", "tab");
                                    $('#wizardTab1MsgCnt').css('color', 'white');
                                    $('#wizardTab1MsgCnt').html("Selection is OK");
                                }
                                else
                                {
                                    $('#addWidgetWizardNextBtn').addClass('disabled');
                                    $('#cTab a').attr("data-toggle", "no");
                                    $('#wizardTab1MsgCnt').css('color', 'rgb(243, 207, 88)');
                                    $('#wizardTab1MsgCnt').html("You must select at least one row");
                                }
                            }
                            else
                            {
                                //Caso new
                                if(($('#actuatorTargetWizard').val() === 'broker')||($('#actuatorTargetWizard').val() === 'app'))
                                {
                                    if($('#actuatorTargetWizard').val() === 'broker')
                                    {
                                        //Caso broker: controlliamo tutti i campi
                                        if(($('#actuatorEntityName').val().trim() !== '')&&(!$('#actuatorEntityName').val().includes(' '))&&($('#actuatorValueType').val().trim() !== '')&&($('#actuatorMinBaseValue').val().trim() !== '')&&($('#actuatorMaxImpulseValue').val().trim() !== ''))
                                        {
                                            $('#addWidgetWizardNextBtn').removeClass('disabled');
                                            $('#cTab a').attr("data-toggle", "tab");
                                            $('#wizardTab1MsgCnt').css('color', 'white');
                                            $('#wizardTab1MsgCnt').html('<? _("Selection is OK")?>');
                                        }
                                        else
                                        {
                                            $('#addWidgetWizardNextBtn').addClass('disabled');
                                            $('#cTab a').attr("data-toggle", "no");
                                            $('#wizardTab1MsgCnt').css('color', 'rgb(243, 207, 88)');
                                            $('#wizardTab1MsgCnt').html('<? _("Some of the new actuator fields are not filled correctly")?>');
                                        }
                                    }
                                    else
                                    {
                                        //Caso NodeRed: via libera, viene spiegato nel summary che non lo puoi fare
                                        $('#addWidgetWizardNextBtn').removeClass('disabled');
                                        $('#cTab a').attr("data-toggle", "tab");
                                        $('#wizardTab1MsgCnt').css('color', 'white');
                                        $('#wizardTab1MsgCnt').html('<? _("Selection is OK")?>');
                                    }
                                }
                                else
                                {
                                    $('#addWidgetWizardNextBtn').addClass('disabled');
                                    $('#cTab a').attr("data-toggle", "no");
                                    $('#wizardTab1MsgCnt').css('color', 'rgb(243, 207, 88)');
                                    $('#wizardTab1MsgCnt').html('<? _("You must select actuator target type")?>');
                                }
                            }
                        }
                    }
                    else
                    {
                        if ($('#modalAddDashboardWizardTemplateMsg')[0].outerText != "Template chosen OK" || $('#modalAddDashboardWizardTitleAlreadyUsedMsg')[0].outerText != "Dashboard title OK") {
                            $('#addWidgetWizardNextBtn').addClass('disabled');
                            $('#chosecTab a').attr("data-toggle", "no");
                            $('#wizardTab1MsgCnt').css('color', 'rgb(243, 207, 88)');
                            $('#wizardTab1MsgCnt').html('<? _("You must select one widget type")?>');
                        }
                    }
                }
            }
        }

        if(location.href.includes("dashboard_configdash.php"))
        {
            firstTabIndex = 1;
            selectedTabIndex = 1;
            
            $('#aTab').hide(); 
            $('#mainFeat').hide();
            
            $('#aTab').removeClass('active');
            $('#bTab').addClass('active');
            $('#mainFeat').removeClass('active');
            $('#dataAndWidgets').addClass('active');
            $('#addWidgetWizardNextBtn').addClass('disabled');
            $('#cTab a').attr("data-toggle", "no");
            $('#wizardTab1MsgCnt').css('color', 'rgb(243, 207, 88)');
            $('#wizardTab1MsgCnt').html("You must select one widget type");
        }
        
        $('#wizardTabsContainer.nav-tabs a[href="#mainFeat"]').on('shown.bs.tab', function(event)
        {
            selectedTabIndex = 0;
            $('#addWidgetWizardPrevBtn').addClass('disabled');
            $('#addWidgetWizardNextBtn').removeClass('disabled');
            
            //Gestione pulsanti prev e next
            $('#addWidgetWizardPrevBtn').off('click');
            $('#addWidgetWizardPrevBtn').click(function()
            {
                if(selectedTabIndex > firstTabIndex)
                {
                    $('.nav-tabs > .active').prev('li').find('a').trigger('click');
                }
            });

            $('#addWidgetWizardNextBtn').off('click');
            $('#addWidgetWizardNextBtn').click(function()
            {
                if(selectedTabIndex < parseInt(tabsQt - 1))
                {
                    $('.nav-tabs > .active').next('li').find('a').trigger('click');
                }
            });
        })
        
        $('#wizardTabsContainer.nav-tabs a[href="#summary"]').on('shown.bs.tab', function(event)
        {
            $('#wrongConditionsDiv').empty();
            $('#summaryDiv').empty();
            $('#createBtnDiv').hide();
            $('#createBtnAlert').show();
            var canBuildSummary = true;
            var summaryTable, summaryTableRow, instancesInfoTxt = null;
            
            selectedTabIndex = 2;
            
            $('#addWidgetWizardNextBtn').addClass('disabled');
            $('#addWidgetWizardPrevBtn').removeClass('disabled');
            $('#cTab a').attr("data-toggle", "tab");
            $('#bTab a').attr("data-toggle", "tab");
            
            if(!location.href.includes("dashboard_configdash"))
            {
                $('#aTab a').attr("data-toggle", "tab");
            }
            
            //Gestione pulsanti prev e next
            $('#addWidgetWizardPrevBtn').off('click');
            $('#addWidgetWizardPrevBtn').click(function()
            {
                if ($('#dashboardDirectStatus').val() === "yes") {
                    if(selectedTabIndex > firstTabIndex)
                    {
                        $('.nav-tabs > .active').prev('li').prev('li').find('a').trigger('click');
                    }

                } else if(selectedTabIndex > firstTabIndex)
                {
                    $('.nav-tabs > .active').prev('li').find('a').trigger('click');
                }
            });

            $('#addWidgetWizardNextBtn').off('click');
            $('#addWidgetWizardNextBtn').click(function()
            {
                if(selectedTabIndex < parseInt(tabsQt - 1))
                {
                    $('.nav-tabs > .active').next('li').find('a').trigger('click');
                }
            });
            
            if((!location.href.includes("dashboard_configdash"))&&(choosenDashboardTemplateName === 'fullyCustom'))
            {
                if(!validityConditions.dashTemplateSelected)
                {
                    $('#wrongConditionsDiv').append('<div class="col-xs-12"><div class="col-xs-12 centerWithFlex"><i class="fa fa-exclamation-triangle validityConditionIcon"></i></div><div class="col-xs-12 centerWithFlex"><span class="validityConditionLbl"><?php echo _("No dashboard template selected"); ?></span></div></div>');
                    validityConditions.canProceed = false;
                    canBuildSummary = false;
                }
                else
                {
                    //Se non si seleziona né widget type né righe è la fully custom vuota
                    if((!validityConditions.widgetTypeSelected)&&(!validityConditions.atLeastOneRowSelected))
                    {
                        switch($('#inputTitleDashboardStatus').val())
                        {
                            case 'empty':
                                $('#wrongConditionsDiv').append('<div class="col-xs-12 titleAlert"><div class="col-xs-12 centerWithFlex"><i class="fa fa-exclamation-triangle validityConditionIcon"></i></div><div class="col-xs-12 centerWithFlex"><span class="validityConditionLbl"><?php echo _("Dashboard title can\'t be empty"); ?></span></div></div>');
                                validityConditions.canProceed = false;
                                break;

                            case 'alreadyUsed':
                                $('#wrongConditionsDiv').append('<div class="col-xs-12 titleAlert"><div class="col-xs-12 centerWithFlex"><i class="fa fa-exclamation-triangle validityConditionIcon"></i></div><div class="col-xs-12 centerWithFlex"><span class="validityConditionLbl"><?php echo _("Dashboard title already in use"); ?></span></div></div>');
                                validityConditions.canProceed = false;
                                break;    
                                
                            case 'tooLong':
                                $('#wrongConditionsDiv').append('<div class="col-xs-12 titleAlert"><div class="col-xs-12 centerWithFlex"><i class="fa fa-exclamation-triangle validityConditionIcon"></i></div><div class="col-xs-12 centerWithFlex"><span class="validityConditionLbl"><?php echo _("Dashboard title longer than 300 chars"); ?></span></div></div>');
                                validityConditions.canProceed = false;    

                            default:
                                break;
                        }

                        if(validityConditions.canProceed === false)
                        {
                            canBuildSummary = false;
                        }
                        else
                        {
                            validityConditions.canProceed = true;
                            canBuildSummary = true;
                        }
                    }
                    else
                    {
                        //Se si seleziona almeno uno tra widget type e righe è la fully custom non vuota
                        if(!validityConditions.widgetTypeSelected)
                        {
                            $('#wrongConditionsDiv').append('<div class="col-xs-12"><div class="col-xs-12 centerWithFlex"><i class="fa fa-exclamation-triangle validityConditionIcon"></i></div><div class="col-xs-12 centerWithFlex"><span class="validityConditionLbl"><?php echo _("No widget type selected"); ?></span></div></div>');
                            validityConditions.canProceed = false;
                            canBuildSummary = false;
                        }
                        else
                        {
                            if(!location.href.includes("dashboard_configdash"))
                            {
                                switch($('#inputTitleDashboardStatus').val())
                                {
                                    case 'empty':
                                        $('#wrongConditionsDiv').append('<div class="col-xs-12 titleAlert"><div class="col-xs-12 centerWithFlex"><i class="fa fa-exclamation-triangle validityConditionIcon"></i></div><div class="col-xs-12 centerWithFlex"><span class="validityConditionLbl"><?php echo _("Dashboard title can\'t be empty"); ?></span></div></div>');
                                        validityConditions.canProceed = false;
                                        break;

                                    case 'alreadyUsed':
                                        $('#wrongConditionsDiv').append('<div class="col-xs-12 titleAlert"><div class="col-xs-12 centerWithFlex"><i class="fa fa-exclamation-triangle validityConditionIcon"></i></div><div class="col-xs-12 centerWithFlex"><span class="validityConditionLbl"><?php echo _("Dashboard title already in use"); ?></span></div></div>');
                                        validityConditions.canProceed = false;
                                        break;    
                                    
                                    case 'tooLong':
                                        $('#wrongConditionsDiv').append('<div class="col-xs-12 titleAlert"><div class="col-xs-12 centerWithFlex"><i class="fa fa-exclamation-triangle validityConditionIcon"></i></div><div class="col-xs-12 centerWithFlex"><span class="validityConditionLbl"><?php echo _("Dashboard title longer than 300 chars"); ?></span></div></div>');
                                        validityConditions.canProceed = false;
                                        break;    
                                    
                                    default:
                                        break;
                                }

                                if((!validityConditions.atLeastOneRowSelected)&&((($('.addWidgetWizardIconClickClass[data-selected=true]').attr('data-widgetCategory') === 'actuator')&&($('#actuatorTargetInstance').val() === 'existent'))||($('.addWidgetWizardIconClickClass[data-selected=true]').attr('data-widgetCategory') === 'dataViewer')))
                                {
                                    $('#wrongConditionsDiv').append('<div class="col-xs-12"><div class="col-xs-12 centerWithFlex"><i class="fa fa-exclamation-triangle validityConditionIcon"></i></div><div class="col-xs-12 centerWithFlex"><span class="validityConditionLbl"><?php echo _("You have to select at least one row from data sources table"); ?></span></div></div>');
                                    validityConditions.canProceed = false;
                                    canBuildSummary = false;
                                }
                                else
                                {
                                    if((!validityConditions.brokerAndNrRowsTogether)&&($('.addWidgetWizardIconClickClass[data-selected=true]').attr('data-widgetCategory') === 'actuator')&&($('#actuatorTargetWizard').val() === 'broker')&&($('#actuatorTargetInstance').val() === 'existent'))
                                    {
                                        $('#wrongConditionsDiv').append('<div class="col-xs-12"><div class="col-xs-12 centerWithFlex"><i class="fa fa-exclamation-triangle validityConditionIcon"></i></div><div class="col-xs-12 centerWithFlex"><span class="validityConditionLbl"><?php echo _("You can\'t select rows from both IOT Apps and broker"); ?></span></div></div>');
                                        validityConditions.canProceed = false;
                                    }
                                    else
                                    {
                                        if((!validityConditions.actuatorFieldsEmpty)&&($('.addWidgetWizardIconClickClass[data-selected=true]').attr('data-widgetCategory') === 'actuator')&&($('#actuatorTargetWizard').val() === 'broker')&&($('#actuatorTargetInstance').val() === 'new'))
                                        {
                                            $('#wrongConditionsDiv').append('<div class="col-xs-12"><div class="col-xs-12 centerWithFlex"><i class="fa fa-exclamation-triangle validityConditionIcon"></i></div><div class="col-xs-12 centerWithFlex"><span class="validityConditionLbl"><?php echo _("Some fields for new device creation on broker are empty or wrongly filled"); ?></span></div></div>');
                                            validityConditions.canProceed = false;   
                                        }
                                        else
                                        {
                                            if($('#inputTitleDashboardStatus').val() === 'ok')
                                            {
                                                validityConditions.canProceed = true; 
                                                canBuildSummary = true;
                                            }
                                            else
                                            {
                                                validityConditions.canProceed = false;   
                                                canBuildSummary = false;
                                            }
                                        }
                                    }
                                }
                            }
                            else
                            {
                                if((!validityConditions.atLeastOneRowSelected)&&((($('.addWidgetWizardIconClickClass[data-selected=true]').attr('data-widgetCategory') === 'actuator')&&($('#actuatorTargetInstance').val() === 'existent'))||($('.addWidgetWizardIconClickClass[data-selected=true]').attr('data-widgetCategory') === 'dataViewer')))
                                {
                                    $('#wrongConditionsDiv').append('<div class="col-xs-12"><div class="col-xs-12 centerWithFlex"><i class="fa fa-exclamation-triangle validityConditionIcon"></i></div><div class="col-xs-12 centerWithFlex"><span class="validityConditionLbl"><?php echo _("You have to select at least one row"); ?></span></div></div>');
                                    validityConditions.canProceed = false;
                                    canBuildSummary = false;
                                }
                                else
                                {
                                    if((!validityConditions.brokerAndNrRowsTogether)&&($('.addWidgetWizardIconClickClass[data-selected=true]').attr('data-widgetCategory') === 'actuator')&&($('#actuatorTargetWizard').val() === 'broker')&&($('#actuatorTargetInstance').val() === 'existent'))
                                    {
                                        $('#wrongConditionsDiv').append('<div class="col-xs-12"><div class="col-xs-12 centerWithFlex"><i class="fa fa-exclamation-triangle validityConditionIcon"></i></div><div class="col-xs-12 centerWithFlex"><span class="validityConditionLbl"><?php echo _("You can\'t select rows from both IOT Apps and broker"); ?></span></div></div>');
                                        validityConditions.canProceed = false;
                                    }
                                    else
                                    {
                                        if((!validityConditions.actuatorFieldsEmpty)&&($('.addWidgetWizardIconClickClass[data-selected=true]').attr('data-widgetCategory') === 'actuator')&&($('#actuatorTargetWizard').val() === 'broker')&&($('#actuatorTargetInstance').val() === 'new'))
                                        {
                                            $('#wrongConditionsDiv').append('<div class="col-xs-12"><div class="col-xs-12 centerWithFlex"><i class="fa fa-exclamation-triangle validityConditionIcon"></i></div><div class="col-xs-12 centerWithFlex"><span class="validityConditionLbl"><?php echo _("Some fields for new device creation on broker are empty or wrongly filled"); ?></span></div></div>');
                                            validityConditions.canProceed = false;   
                                        }
                                        else
                                        {
                                            validityConditions.canProceed = true;   
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
            else
            {
                if((!location.href.includes("dashboard_configdash"))&&(!validityConditions.dashTemplateSelected))
                {
                    $('#wrongConditionsDiv').append('<div class="col-xs-12"><div class="col-xs-12 centerWithFlex"><i class="fa fa-exclamation-triangle validityConditionIcon"></i></div><div class="col-xs-12 centerWithFlex"><span class="validityConditionLbl"><?php echo _("No dashboard template selected"); ?></span></div></div>');
                    validityConditions.canProceed = false;
                    canBuildSummary = false;
                }
                else
                {
                    if(!validityConditions.widgetTypeSelected)
                    {
                        $('#wrongConditionsDiv').append('<div class="col-xs-12"><div class="col-xs-12 centerWithFlex"><i class="fa fa-exclamation-triangle validityConditionIcon"></i></div><div class="col-xs-12 centerWithFlex"><span class="validityConditionLbl"><?php echo _("No widget type selected"); ?></span></div></div>');
                        validityConditions.canProceed = false;
                        canBuildSummary = false;
                    }
                    else
                    {
                        if(!location.href.includes("dashboard_configdash"))
                        {
                            switch($('#inputTitleDashboardStatus').val())
                            {
                                case 'empty':
                                    $('#wrongConditionsDiv').append('<div class="col-xs-12 titleAlert"><div class="col-xs-12 centerWithFlex"><i class="fa fa-exclamation-triangle validityConditionIcon"></i></div><div class="col-xs-12 centerWithFlex"><span class="validityConditionLbl"><?php echo _("Dashboard title can\'t be empty"); ?></span></div></div>');
                                    validityConditions.canProceed = false;
                                    break;

                                case 'alreadyUsed':
                                    $('#wrongConditionsDiv').append('<div class="col-xs-12 titleAlert"><div class="col-xs-12 centerWithFlex"><i class="fa fa-exclamation-triangle validityConditionIcon"></i></div><div class="col-xs-12 centerWithFlex"><span class="validityConditionLbl"><?php echo _("Dashboard title already in use"); ?></span></div></div>');
                                    validityConditions.canProceed = false;
                                    break;   
                                    
                                case 'tooLong':
                                    $('#wrongConditionsDiv').append('<div class="col-xs-12 titleAlert"><div class="col-xs-12 centerWithFlex"><i class="fa fa-exclamation-triangle validityConditionIcon"></i></div><div class="col-xs-12 centerWithFlex"><span class="validityConditionLbl"><?php echo _("Dashboard title longer than 300 chars"); ?></span></div></div>');
                                    validityConditions.canProceed = false;    

                                default:
                                    break;
                            }

                            if((!validityConditions.atLeastOneRowSelected)&&((($('.addWidgetWizardIconClickClass[data-selected=true]').attr('data-widgetCategory') === 'actuator')&&($('#actuatorTargetInstance').val() === 'existent'))||($('.addWidgetWizardIconClickClass[data-selected=true]').attr('data-widgetCategory') === 'dataViewer')))
                            {
                                $('#wrongConditionsDiv').append('<div class="col-xs-12"><div class="col-xs-12 centerWithFlex"><i class="fa fa-exclamation-triangle validityConditionIcon"></i></div><div class="col-xs-12 centerWithFlex"><span class="validityConditionLbl"><?php echo _("You have to select at least one row from data sources table"); ?></span></div></div>');
                                validityConditions.canProceed = false;
                                canBuildSummary = false;
                            }
                            else
                            {
                                if((!validityConditions.brokerAndNrRowsTogether)&&($('.addWidgetWizardIconClickClass[data-selected=true]').attr('data-widgetCategory') === 'actuator')&&($('#actuatorTargetWizard').val() === 'broker')&&($('#actuatorTargetInstance').val() === 'existent'))
                                {
                                    $('#wrongConditionsDiv').append('<div class="col-xs-12"><div class="col-xs-12 centerWithFlex"><i class="fa fa-exclamation-triangle validityConditionIcon"></i></div><div class="col-xs-12 centerWithFlex"><span class="validityConditionLbl"><?php echo _("You can\'t select rows from both IOT Apps and broker"); ?></span></div></div>');
                                    validityConditions.canProceed = false;
                                }
                                else
                                {
                                    if((!validityConditions.actuatorFieldsEmpty)&&($('.addWidgetWizardIconClickClass[data-selected=true]').attr('data-widgetCategory') === 'actuator')&&($('#actuatorTargetWizard').val() === 'broker')&&($('#actuatorTargetInstance').val() === 'new'))
                                    {
                                        $('#wrongConditionsDiv').append('<div class="col-xs-12"><div class="col-xs-12 centerWithFlex"><i class="fa fa-exclamation-triangle validityConditionIcon"></i></div><div class="col-xs-12 centerWithFlex"><span class="validityConditionLbl"><?php echo _("Some fields for new device creation on broker are empty or wrongly filled"); ?></span></div></div>');
                                        validityConditions.canProceed = false;   
                                    }
                                    else
                                    {
                                        if($('#inputTitleDashboardStatus').val() === 'ok')
                                        {
                                            validityConditions.canProceed = true; 
                                            canBuildSummary = true;
                                        }
                                        else
                                        {
                                            validityConditions.canProceed = false;   
                                            canBuildSummary = false;
                                        }
                                    }
                                }
                            }
                        }
                        else
                        {
                            if((!validityConditions.atLeastOneRowSelected)&&((($('.addWidgetWizardIconClickClass[data-selected=true]').attr('data-widgetCategory') === 'actuator')&&($('#actuatorTargetInstance').val() === 'existent'))||($('.addWidgetWizardIconClickClass[data-selected=true]').attr('data-widgetCategory') === 'dataViewer')))
                            {
                                $('#wrongConditionsDiv').append('<div class="col-xs-12"><div class="col-xs-12 centerWithFlex"><i class="fa fa-exclamation-triangle validityConditionIcon"></i></div><div class="col-xs-12 centerWithFlex"><span class="validityConditionLbl"><?php echo _("You have to select at least one row"); ?></span></div></div>');
                                validityConditions.canProceed = false;
                                canBuildSummary = false;
                            }
                            else
                            {
                                if((!validityConditions.brokerAndNrRowsTogether)&&($('.addWidgetWizardIconClickClass[data-selected=true]').attr('data-widgetCategory') === 'actuator')&&($('#actuatorTargetWizard').val() === 'broker')&&($('#actuatorTargetInstance').val() === 'existent'))
                                {
                                    $('#wrongConditionsDiv').append('<div class="col-xs-12"><div class="col-xs-12 centerWithFlex"><i class="fa fa-exclamation-triangle validityConditionIcon"></i></div><div class="col-xs-12 centerWithFlex"><span class="validityConditionLbl"><?php echo _("You can\'t select rows from both IOT Apps and broker"); ?></span></div></div>');
                                    validityConditions.canProceed = false;
                                }
                                else
                                {
                                    if((!validityConditions.actuatorFieldsEmpty)&&($('.addWidgetWizardIconClickClass[data-selected=true]').attr('data-widgetCategory') === 'actuator')&&($('#actuatorTargetWizard').val() === 'broker')&&($('#actuatorTargetInstance').val() === 'new'))
                                    {
                                        $('#wrongConditionsDiv').append('<div class="col-xs-12"><div class="col-xs-12 centerWithFlex"><i class="fa fa-exclamation-triangle validityConditionIcon"></i></div><div class="col-xs-12 centerWithFlex"><span class="validityConditionLbl"><?php echo _("Some fields for new device creation on broker are empty or wrongly filled"); ?></span></div></div>');
                                        validityConditions.canProceed = false;   
                                    }
                                    else
                                    {
                                        validityConditions.canProceed = true;   
                                    }
                                }
                            }
                        }
                    }
                }
            }
            
            if(validityConditions.canProceed)
            {
                $('#createBtnAlert').hide();
                $('#createBtnDiv').show();
            }
            else
            {
                $('#createBtnDiv').hide();
                $('#createBtnAlert').show();
            }
            
            var monoMulti = $('.addWidgetWizardIconClickClass[data-selected=true]').attr('data-mono_multi');
            var widgetCategory = $('.addWidgetWizardIconClickClass[data-selected=true]').attr('data-widgetcategory');
            var mainWidget = $('.addWidgetWizardIconClickClass[data-selected=true]').attr('data-mainwidget');
            var targetWidget = $('.addWidgetWizardIconClickClass[data-selected=true]').attr('data-targetwidget');
            var widgetIcon = $('.addWidgetWizardIconClickClass[data-selected=true]').attr('data-icon');
            var widgetDesc = $('.addWidgetWizardIconClickClass[data-selected=true]').attr('data-content');
            var existentOrNew = $('#actuatorTargetInstance').val();
            var brokerOrNr = $('#actuatorTargetWizard').val();
            
            if(canBuildSummary)
            {
                if(!location.href.includes("dashboard_configdash"))
                {
                    var localExtCnt = $('<div class="col-xs-4"></div>');
                    var dashInfoLbl = $('<div class="col-xs-12"><div class="col-xs-12 centerWithFlex"><span class="summaryLbl"><?php echo _("Dashboard template and title"); ?></span></div>');
                    var dashTitleCnt = $('<div class="col-xs-12 centerWithFlex widgetTypeDetails">' + $('#inputTitleDashboard').val().replace(/(<([^>]+)>)/ig,"") + '</div>');
                    var dashIconExtCnt = $('<div class="col-xs-12 centerWithFlex"></div>');
                    var dashIconCnt = $('<div class="singleWidgetIconCnt"></div>');
                    var dashTemplateIconUrl = $('.modalAddDashboardWizardChoiceCnt[data-selected="true"] div.modalAddDashboardWizardChoicePic').css('background-image');
                    dashIconCnt.css("background-image", dashTemplateIconUrl);
                    
                    localExtCnt.append(dashInfoLbl);
                    dashIconExtCnt.append(dashIconCnt)
                    localExtCnt.append(dashIconExtCnt);
                    localExtCnt.append(dashTitleCnt);
                    $('#summaryDiv').append(localExtCnt);
                }
                
                if((!location.href.includes("dashboard_configdash"))&&(choosenDashboardTemplateName === 'fullyCustom'))
                {
                    var widgetInfoLbl = '<div class="col-xs-12 centerWithFlex summaryLbl"><?php echo _("Details"); ?></div>';
                    var widgetInfoCnt = '<div class="col-xs-12 centerWithFlex widgetTypeDetails"><?php echo _("A fully custom dashboard is created empty, so no widget details are available"); ?></div>';
                    $('#summaryDiv').append(widgetInfoLbl);
                    $('#summaryDiv').append(widgetInfoCnt); 
                }
                else
                {
                    if(monoMulti === 'Mono')
                    {
                        //Casi mono - 1 widget per riga
                        if((widgetCategory === 'dataViewer')||((widgetCategory === 'actuator')&&(existentOrNew == 'existent')))
                        {
                            //Dataviewer OPPURE actuator on existent
                            var localExtCnt = $('<div class="col-xs-4"></div>');
                            var widgetInfoLbl = '<div class="col-xs-12 centerWithFlex summaryLbl"><?php echo _("Widget type details"); ?></div>';
                            var widgetInfoCnt = '<div class="col-xs-12 centerWithFlex widgetTypeDetails">' + widgetDesc + '</div>';
                            
                            localExtCnt.append(widgetInfoLbl);
                            localExtCnt.append(widgetInfoCnt);
                            $('#summaryDiv').append(localExtCnt);
                            
                            var localExtCnt = $('<div class="col-xs-4"></div>');
                            var instancesInfoLbl = '<div class="col-xs-12 centerWithFlex summaryLbl"><?php echo _("Instances details"); ?></div>';
                            
                            summaryTable = $('<table id="summaryTable"><thead><th><?php echo _("Widget"); ?></th><th><?php echo _("High-Level Type"); ?></th><th><?php echo _("Nature"); ?></th><th><?php echo _("Subnature"); ?></th><th><?php echo _("Value type"); ?></th><th><?php echo _("Value name"); ?></th><th><?php echo _("Data type"); ?></th></thead><tbody></tbody></table>');

                            var count = 0;
                            for(var key in widgetWizardSelectedRows)
                            {
                                if(widgetWizardSelectedRows[key].widgetCompatible)
                                {
                                    summaryTableRow = $('<tr class="summaryTableRow"><td><div class="iconsMonoSingleIcon"></div></td><td>' + widgetWizardSelectedRows[key].high_level_type + '</td><td>' + widgetWizardSelectedRows[key].nature + '</td><td>' + widgetWizardSelectedRows[key].sub_nature + '</td><td>' + widgetWizardSelectedRows[key].low_level_type + '</td><td>' + widgetWizardSelectedRows[key].unique_name_id + '</td><td>' + widgetWizardSelectedRows[key].unit + '</td></tr>');
                                    summaryTableRow.find('div.iconsMonoSingleIcon').css("background-image", "url(\"../img/widgetIcons/mono/" + widgetIcon + "\")");
                                    summaryTable.find('tbody').append(summaryTableRow);
                                    count++;
                                }
                                else
                                {
                                    //TBD - Aggiungere a righe non istanziate
                                }
                            }
                            
                            if(targetWidget === '')
                            {
                                instancesInfoTxt = count + " single instances of widget will be created";
                            }
                            else
                            {
                                if(targetWidget !== 'widgetTimeTrend')
                                {
                                    instancesInfoTxt = count + " instance(s) of main widget + 1 single instance of a driven target widget will be created";
                                }
                                else
                                {
                                    instancesInfoTxt = count + " couple(s) of widgets will be created: the first one of each couple will show last data value, the second one a value time trend";
                                }
                            }

                            var instancesInfoCnt = '<div class="col-xs-12 centerWithFlex widgetTypeDetails">' + instancesInfoTxt + '</div>';

                            localExtCnt.append(instancesInfoLbl);
                            localExtCnt.append(instancesInfoCnt);
                            $('#summaryDiv').append(localExtCnt);
                            
                            var localExtCnt = $('<div class="col-xs-12"></div>');
                            var tableLbl = '<div class="col-xs-12 centerWithFlex summaryLbl"><?php echo _("Main widget(s) and relative data"); ?></div>';

                            localExtCnt.append(tableLbl);
                            localExtCnt.append(summaryTable);
                            $('#summaryDiv').append(localExtCnt);
                        }
                        else
                        {
                            if((widgetCategory === 'actuator')&&(existentOrNew == 'new')&&(brokerOrNr === 'broker'))
                            {
                                //Actuator on new entity: widget + entity summary
                                var localExtCnt = $('<div class="col-xs-4"></div>');
                                var widgetInfoLbl = $('<div class="col-xs-12 centerWithFlex summaryLbl"><?php echo _("Widget type details"); ?></div>');
                                var widgetInfoCnt = $('<div class="col-xs-12 centerWithFlex widgetTypeDetails">' + widgetDesc + '</div>');
                                var widgetIconExtCnt = $('<div class="col-xs-12 centerWithFlex"></div>');
                                var widgetIconCnt = $('<div class="singleWidgetIconCnt"></div>');
                                widgetIconCnt.css("background-image", "url(\"../img/widgetIcons/mono/" + widgetIcon + "\")");
                                
                                localExtCnt.append(widgetInfoLbl);
                                localExtCnt.append(widgetInfoCnt);
                                widgetIconExtCnt.append(widgetIconCnt)
                                localExtCnt.append(widgetIconExtCnt);
                                $('#summaryDiv').append(localExtCnt);
                                
                                var localExtCnt = $('<div class="col-xs-4"></div>');
                                var instancesInfoLbl = '<div class="col-xs-12 centerWithFlex summaryLbl"><?php echo _("Instances details"); ?></div>';
                                var instancesInfoCnt = '<div class="col-xs-12 centerWithFlex widgetTypeDetails"><?php echo _("One new device entity will be created on context broker and linked to new actuator on dashboard"); ?></div>';
                                localExtCnt.append(instancesInfoLbl);
                                localExtCnt.append(instancesInfoCnt);
                                $('#summaryDiv').append(localExtCnt);
                                
                                var entityInfoLbl = $('<div class="col-xs-12 centerWithFlex summaryLbl"><?php echo _("Device details"); ?></div>');
                                var deviceTable = $('<table id="summaryTable"><thead><th>Property</th><th>Value</th></thead><tbody></tbody></table>');

                                var deviceTableRow = $('<tr class="summaryTableRow"><td>Device name</td><td>' + $('#actuatorEntityName').val() + '</td></tr>');
                                deviceTable.find('tbody').append(deviceTableRow);

                                deviceTableRow = $('<tr class="summaryTableRow"><td>Value type</td><td>' + $('#actuatorValueType').val() + '</td></tr>');
                                deviceTable.find('tbody').append(deviceTableRow);

                                switch(mainWidget)
                                {
                                    case "widgetImpulseButton":
                                        deviceTableRow = $('<tr class="summaryTableRow"><td>Base value</td><td>' + $('#actuatorMinBaseValue').val() + '</td></tr>');
                                        deviceTable.find('tbody').append(deviceTableRow);
                                        deviceTableRow = $('<tr class="summaryTableRow"><td>Impulse value</td><td>' + $('#actuatorMaxImpulseValue').val() + '</td></tr>');
                                        deviceTable.find('tbody').append(deviceTableRow);
                                        break;

                                    case "widgetOnOffButton":
                                        deviceTableRow = $('<tr class="summaryTableRow"><td>Off value</td><td>' + $('#actuatorMinBaseValue').val() + '</td></tr>');
                                        deviceTable.find('tbody').append(deviceTableRow);
                                        deviceTableRow = $('<tr class="summaryTableRow"><td>On value</td><td>' + $('#actuatorMaxImpulseValue').val() + '</td></tr>');
                                        deviceTable.find('tbody').append(deviceTableRow);
                                        break;    

                                    case "widgetKnob":
                                        deviceTableRow = $('<tr class="summaryTableRow"><td>Min value</td><td>' + $('#actuatorMinBaseValue').val() + '</td></tr>');
                                        deviceTable.find('tbody').append(deviceTableRow);
                                        deviceTableRow = $('<tr class="summaryTableRow"><td>Max value</td><td>' + $('#actuatorMaxImpulseValue').val() + '</td></tr>');
                                        deviceTable.find('tbody').append(deviceTableRow);
                                        break;    
                                }

                                
                                $('#summaryDiv').append(widgetInfoCnt);
                                $('#summaryDiv').append(entityInfoLbl);
                                $('#summaryDiv').append(deviceTable);
                            }
                            else
                            {
                                if((widgetCategory === 'actuator')&&(existentOrNew == 'new')&&(brokerOrNr === 'app'))
                                {
                                    //Actuator on new NR: how to NodeRED
                                    var nrInfoLbl = $('<div class="col-xs-12 centerWithFlex summaryLbl"><?php echo _("Instantiation instructions"); ?></div>');
                                    var nrInfoCnt = $('<div class="col-xs-12 widgetTypeDetails"><?php echo _("At the moment it\'s not possible to instantiate a new actuator and its corrispondent block on a IOT personal application. In order to complete this task, please follow this flow: 1) Open NodeRED flow designer of a personal app of your choice"); ?><br>;<?php echo _(" 2) Add a new actuator block (geolocator, dimer, impulsive button, switch, keyboard)"); ?>; <?php echo _("3) Choose the dashboard where you want it to be (or create a new one) via block edit menu"); ?>; <?php echo _("4) Deploy your application"); ?>; <?php echo _("5) Open (or refresh) your dashboard of choice: actuator widget will be automatically be instantiated"); ?></div>');
                                    $('#summaryDiv').append(nrInfoLbl);
                                    $('#summaryDiv').append(nrInfoCnt);
                                    $('#createBtnDiv').hide();
                                }
                            }
                        }
                    }
                    else
                    {
                        //Casi multi
                        var localExtCnt = $('<div class="col-xs-4"></div>');
                        var widgetInfoLbl = '<div class="col-xs-12 centerWithFlex summaryLbl"><?php echo _("Widget type details"); ?></div>';
                        var widgetIconExtCnt = $('<div class="col-xs-12 centerWithFlex"></div>');
                        var widgetIconCnt = $('<div class="singleWidgetIconCnt"></div>');
                        widgetIconCnt.css("background-image", "url(\"../img/widgetIcons/multi/" + widgetIcon + "\")");
                        if ($('#dashboardDirectStatus').val() === "yes") {
                            if (widgetDesc === undefined) {
                                widgetDesc = "";
                            }
                        }
                        var widgetInfoCnt = '<div class="col-xs-12 centerWithFlex widgetTypeDetails">' + widgetDesc + '</div>';
                        
                        localExtCnt.append(widgetInfoLbl);
                        widgetIconExtCnt.append(widgetIconCnt);
                        localExtCnt.append(widgetIconExtCnt);
                        localExtCnt.append(widgetInfoCnt);
                        $('#summaryDiv').append(localExtCnt);
                        
                        var tableLbl = '<div class="col-xs-12 centerWithFlex summaryLbl"><?php echo _("Main widget and relative data"); ?></div>';

                        summaryTable = $('<table id="summaryTable"><thead><th><?php echo _("High-Level Type"); ?></th><th>Nature</th><th>Subnature</th><th><?php echo _("Value type"); ?></th><th><?php echo _("Value name"); ?></th><th><?php echo _("Data type"); ?></th></thead><tbody></tbody></table>');

                        var count = 0;
                        for(var key in widgetWizardSelectedRows)
                        {
                            if(widgetWizardSelectedRows[key].widgetCompatible)
                            {
                                summaryTableRow = $('<tr class="summaryTableRow"><td>' + widgetWizardSelectedRows[key].high_level_type + '</td><td>' + widgetWizardSelectedRows[key].nature + '</td><td>' + widgetWizardSelectedRows[key].sub_nature + '</td><td>' + widgetWizardSelectedRows[key].low_level_type + '</td><td>' + widgetWizardSelectedRows[key].unique_name_id + '</td><td>' + widgetWizardSelectedRows[key].unit + '</td></tr>');
                                summaryTable.find('tbody').append(summaryTableRow);
                                count++;
                            }
                            else
                            {
                                //TBD - Aggiungere a righe non istanziate
                            }
                        }
                        
                        var localExtCnt = $('<div class="col-xs-4"></div>');
                        var instancesInfoLbl = '<div class="col-xs-12 centerWithFlex summaryLbl"><?php echo _("Instances details"); ?></div>';

                        if(targetWidget === '')
                        {
                            instancesInfoTxt = "One single instance of widget will be created: it will handle all the " + count + " selected data sources";
                        }
                        else
                            if ($('#dashboardDirectStatus').val() === "yes") {
                                instancesInfoTxt = "An empty dashboard with no widgets instantiated will be created.";
                            }
                            else
                            {
                                instancesInfoTxt = "One single instance of main widget and one instance of each target widget will be created: the main widget will handle all the " + count + " selected data sources showing their data on the target widget(s)";
                            }

                        var instancesInfoCnt = '<div class="col-xs-12 centerWithFlex widgetTypeDetails">' + instancesInfoTxt + '</div>';
                        
                        localExtCnt.append(instancesInfoLbl);
                        localExtCnt.append(instancesInfoCnt);
                        $('#summaryDiv').append(localExtCnt);
                        
                        var localExtCnt = $('<div class="col-xs-12"></div>');
                        localExtCnt.append(tableLbl);
                        localExtCnt.append(summaryTable);
                        $('#summaryDiv').append(localExtCnt);
                    }
                }
                
                $('#wrongConditionsDiv').append('<div class="col-xs-12"><div class="col-xs-12 centerWithFlex" style="margin-top: 25px !important"><i class="fa fa-thumbs-o-up validityConditionIcon" style="font-size: 100px !important; color: white !important"></i></div><div class="col-xs-12 centerWithFlex"><span class="validityConditionLbl" style="color: white !important;"><?php echo _("Can proceed"); ?></span></div></div>');
            }
            else
            {
                $('#summaryDiv').append('<div class="col-xs-12"><div class="col-xs-12 centerWithFlex"><i class="fa fa-exclamation-triangle validityConditionIcon"></i></div><div class="col-xs-12 centerWithFlex"><span class="validityConditionLbl"><?php echo _("Summary is not available until you fix missing or wrong inputs"); ?></span></div></div>');
            }
        });
        
        $('#actuatorTargetInstance').val("existent");
        $('#actuatorTargetWizard').val(-1);
        
        $('#actuatorTargetInstance').change(function()
        {
            if($(this).val() === 'new')
            {
                $('.hideIfActuatorNew').hide();
                $('#actuatorTargetCell .wizardActLbl').show();
                $('#actuatorTargetCell .wizardActInputCnt').show();
                
                if((!location.href.includes("dashboard_configdash.php")&&!location.href.includes("inspectorOS.php"))&&($('.modalAddDashboardWizardChoiceCnt[data-selected="true"]').attr("data-templatename") === 'iotDevicesBroker'))
                {
                    $('#actuatorTargetWizard').val('broker');
                    $('#actuatorTargetWizard').trigger('change');
                }
                
                checkActuatorFieldsEmpty();
            }
            else
            {
                $('#actuatorTargetCell .wizardActLbl').hide();
                $('#actuatorTargetCell .wizardActInputCnt').hide();
                $('#actuatorEntityNameCell .wizardActLbl').hide();
                $('#actuatorEntityNameCell .wizardActInputCnt').hide();
                $('#actuatorValueTypeCell .wizardActLbl').hide();
                $('#actuatorValueTypeCell .wizardActInputCnt').hide();
                $('#actuatorMinBaseValueCell .wizardActLbl').hide();
                $('#actuatorMinBaseValueCell .wizardActInputCnt').hide();
                $('#actuatorMaxBaseValueCell .wizardActLbl').hide();
                $('#actuatorMaxBaseValueCell .wizardActInputCnt').hide();
                $('#actuatorTargetWizard').val(-1);
                $('#actuatorEntityName').val('');
                $('#actuatorValueType').val('');
                $('#actuatorMinBaseValue').val('');
                $('#actuatorMaxImpulseValue').val('');
                
                $('.hideIfActuatorNew').show();
            }
            
            checkTab1Conditions();
        });
        
        $('#actuatorTargetWizard').change(function()
        {
            if($(this).val() === 'broker')
            {
                $('#actuatorEntityNameCell .wizardActLbl').show();
                $('#actuatorEntityNameCell .wizardActInputCnt').show();
                $('#actuatorValueTypeCell .wizardActLbl').show();
                $('#actuatorValueTypeCell .wizardActInputCnt').show();
                $('#actuatorMinBaseValueCell .wizardActLbl').show();
                $('#actuatorMinBaseValueCell .wizardActInputCnt').show();
                $('#actuatorMaxBaseValueCell .wizardActLbl').show();
                $('#actuatorMaxBaseValueCell .wizardActInputCnt').show();
                checkActuatorFieldsEmpty();
            }
            else
            {
                $('#actuatorEntityNameCell .wizardActLbl').hide();
                $('#actuatorEntityNameCell .wizardActInputCnt').hide();
                $('#actuatorValueTypeCell .wizardActLbl').hide();
                $('#actuatorValueTypeCell .wizardActInputCnt').hide();
                $('#actuatorMinBaseValueCell .wizardActLbl').hide();
                $('#actuatorMinBaseValueCell .wizardActInputCnt').hide();
                $('#actuatorMaxBaseValueCell .wizardActLbl').hide();
                $('#actuatorMaxBaseValueCell .wizardActInputCnt').hide();
            }
            
            checkTab1Conditions();
        });
        
        function checkBrokerAndNrRowsTogether()
        {
            var nrCount = 0;
            var brokerCount = 0;
            
            for(var key in widgetWizardSelectedRows)
            {
                if(widgetWizardSelectedRows[key].nature === 'From Dashboard to IOT Device')
                {
                    nrCount++;
                }
                
                if(widgetWizardSelectedRows[key].nature === 'From Dashboard to IOT App')
                {
                    brokerCount++;
                }        
            }
            
            if((nrCount > 0)&&(brokerCount > 0))
            {
                validityConditions.brokerAndNrRowsTogether = false;
            }
            else
            {
                validityConditions.brokerAndNrRowsTogether = true;
            }
        }
        
        function checkAtLeastOneRowSelected()
        {
            var count = 0;
            
            for(var key in widgetWizardSelectedRows)
            {
                count++;        
            }
            
            if(count > 0)
            {
                validityConditions.atLeastOneRowSelected = true;
            }
            else
            {
                validityConditions.atLeastOneRowSelected = false;
            }
        }
        
        $('#actuatorEntityName').on('input', checkActuatorFieldsEmpty);
        $('#actuatorEntityName').on('input', checkActuatorFieldsSpace);
    //    $('#actuatorEntityName').on('input', checkActuatorFieldsLength);
        $('#actuatorValueType').on('input', checkActuatorFieldsEmpty);
        $('#actuatorMinBaseValue').on('input', checkActuatorFieldsEmpty);
        $('#actuatorMaxImpulseValue').on('input', checkActuatorFieldsEmpty);
        
        function checkActuatorFieldsEmpty()
        {
            var selectedWidgetType = $('.addWidgetWizardIconClickClass[data-selected=true]').attr('data-mainwidget');
            
            if(($('#actuatorTargetInstance').val() === 'new')&&($('#actuatorTargetWizard').val() === 'broker'))
            {
                switch(selectedWidgetType)
                {
                    case "widgetKnob":
                        if(($('#actuatorEntityName').val() === '')||($('#actuatorValueType').val() === '')||($('#actuatorMinBaseValue').val() === '')||($('#actuatorMaxImpulseValue').val() === ''))
                        {
                            validityConditions.actuatorFieldsEmpty = false;
                        }
                        else
                        {
                            validityConditions.actuatorFieldsEmpty = true;
                        }
                        break;

                    case "widgetOnOffButton":
                        if(($('#actuatorEntityName').val() === '')||($('#actuatorValueType').val() === '')||($('#actuatorMinBaseValue').val() === '')||($('#actuatorMaxImpulseValue').val() === ''))
                        {
                            validityConditions.actuatorFieldsEmpty = false;
                        }
                        else
                        {
                            validityConditions.actuatorFieldsEmpty = true;
                        }
                        break; 

                    case "widgetImpulseButton":
                        if(($('#actuatorEntityName').val() === '')||($('#actuatorValueType').val() === '')||($('#actuatorMinBaseValue').val() === '')||($('#actuatorMaxImpulseValue').val() === ''))
                        {
                            validityConditions.actuatorFieldsEmpty = false;
                        }
                        else
                        {
                            validityConditions.actuatorFieldsEmpty = true;
                        }
                        break;

                    case "widgetNumericKeyboard":
                        if($('#actuatorEntityName').val() === '')
                        {
                            validityConditions.actuatorFieldsEmpty = false;
                        }
                        else
                        {
                            validityConditions.actuatorFieldsEmpty = true;
                        }
                        break;  

                    default:
                        validityConditions.actuatorFieldsEmpty = true;
                        break;
                }
            }
            else
            {
                //Caso seconda select ancora a -1, non funziona!
                if(($('#actuatorTargetInstance').val() === 'new')&&($('#actuatorTargetWizard').val() === -1)/*&&($('.addWidgetWizardIconClickClass[data-selected=true]').attr('data-widgetCategory') === 'actuator')*/)
                {
                    validityConditions.actuatorFieldsEmpty = false;
                }
                else
                {
                    validityConditions.actuatorFieldsEmpty = true;
                }
            }
            
            checkTab1Conditions();
        }

        function checkActuatorFieldsSpace()
        {
            if($('#actuatorEntityName').val().includes(' ')) {
                $('#wizardTab1MsgCnt').css('color', 'rgb(243, 207, 88)');
                $('#wizardTab1MsgCnt').html("Some of the new actuator fields are not filled correctly: EMPTY SPACES NOT ALLOWED in Device Name");
            }
        }

        function checkActuatorFieldsLength()
        {
            var selectedWidgetType = $('.addWidgetWizardIconClickClass[data-selected=true]').attr('data-mainwidget');
            var id_dash = "<?php if (isset($_REQUEST['dashboardId'])) {echo escapeForJS($_REQUEST['dashboardId']);} else {echo 1;} ?>";
          //  var deviceIdToBeRegistered = "w_" + $('#actuatorEntityName').val() + "_" + id_dash + ;
            // estimate the lenght of entity to be registered
            if($('#actuatorEntityName').val().length + "<?php echo $_SESSION['orgBroker']; ?>".length + "<?php echo $_SESSION['loggedUsername']; ?>".length + selectedWidgetType.length + 10 > 64) {
                $('#wizardTab1MsgCnt').css('color', 'rgb(243, 207, 88)');
                $('#wizardTab1MsgCnt').html("Device Name TOO LONG. Please shorten your device name.");
            }
        }

        function updateWidgetCompatibleRows()
        {
            var selectedWidget, snap4citytype, snap4citytypeArray, selectedRowUnits, count, globalCount, originalBckColor = null;
            //Repere tipi di dato gestiti dal widget
            if($('.addWidgetWizardIconClickClass[data-selected="true"]').length > 0)
            {
                selectedWidget = $('.addWidgetWizardIconClickClass[data-selected="true"]');
                snap4citytype = selectedWidget.attr('data-snap4citytype');
                snap4citytypeArray = snap4citytype.split(',');
                globalCount = 0;
                console.log("Selected Widget: " + $('.addWidgetWizardIconClickClass[data-selected=true]').attr('data-mainwidget'));
                console.log("Snap4CIty TYPE: " + snap4citytype);
                
                if(Object.keys(widgetWizardSelectedRows).length > 0)
                {
                    for(var key in widgetWizardSelectedRows)
                    {
                        selectedRowUnits = widgetWizardSelectedRows[key].unit.split(',');
                        console.log("Selected ROW UNITS: " + selectedRowUnits);
                        count = 0;
                        
                        originalBckColor = $('#widgetWizardSelectedRowsTable tbody tr[data-rowid=' + key.replace('row', '') + ']').css("background-color");
                        for(var j = 0; j < selectedRowUnits.length; j++)
                        {
                            selectedRowUnits[j] = selectedRowUnits[j].trim();

                            if(snap4citytype.includes(selectedRowUnits[j]))
                            {
                                count++;
                            }
                        }
                        
                        if(count > 0)
                        {
                            //Riga compatibile
                            if (($('.addWidgetWizardIconClickClass[data-selected="true"]').attr("data-mainwidget") == "widgetBarSeries" || $('.addWidgetWizardIconClickClass[data-selected="true"]').attr("data-mainwidget") == "widgetCurvedLineSeries" || $('.addWidgetWizardIconClickClass[data-selected="true"]').attr("data-mainwidget") == "widgetPieChart") &&  widgetWizardSelectedRows[key].widgetCompatible == false) {
                                widgetWizardSelectedRows[key].widgetCompatible = false;
                            } else {
                                widgetWizardSelectedRows[key].widgetCompatible = true;
                            }
                            $('#widgetWizardSelectedRowsTable tr[data-rowid=' + key.replace('row', '') + ']').css("background-color", originalBckColor);
                        }
                        else
                        {
                            console.log("Riga Incompatibile !");
                            //Riga incompatibile
                            globalCount++;
                            widgetWizardSelectedRows[key].widgetCompatible = false;
                            $('#widgetWizardSelectedRowsTable tr[data-rowid=' + key.replace('row', '') + ']').css("background-color", "#ffb3b3");
                        }
                    }
                    
                    if(globalCount > 0)
                    {
                        $('#wizardNotCompatibleRowsAlert').show();
                    }
                    else
                    {
                        $('#wizardNotCompatibleRowsAlert').hide();
                    }
                }
            }
            else
            {
                //Se widget non selezionato le righe son sempre compatibili
                for(var key in widgetWizardSelectedRows)
                {
                    widgetWizardSelectedRows[key].widgetCompatible = true;
                    if($('#widgetWizardSelectedRowsTable tr[data-rowid=' + key.replace('row', '') + ']').hasClass('odd'))
                    {
                        $('#widgetWizardSelectedRowsTable tr[data-rowid=' + key.replace('row', '') + ']').css('background-color', '#f9f9f9');
                    }
                    else
                    {
                        $('#widgetWizardSelectedRowsTable tr[data-rowid=' + key.replace('row', '') + ']').css('background-color', '#ffffff');
                    }
                }
                $('#wizardNotCompatibleRowsAlert').hide();
            }
        }

        function updateBarSeriesWidgetSelectedRows (obj, keyComp) {
            Object.keys(obj).forEach(key => {
                let item = obj[key];
                //if (item.value_unit != (obj[keyComp].value_unit) || item.low_level_type != (obj[keyComp].low_level_type)) {   // Controlla se diversa unità di misura o tipo di metrica
                if (item.value_unit != (obj[keyComp].value_unit)) {    // Controlla se diversa unità di misura (OK metriche diverse ma con stessa unità di misura)
                    widgetWizardSelectedRows[key].widgetCompatible = false;
                    $('#widgetWizardSelectedRowsTable tr[data-rowid=' + key.replace('row', '') + ']').css("background-color", "#ffb3b3");
                }
            });
         /*   for(var item in obj) {
                if (obj.hasOwnProperty(property)) {
                    if (item.value_unit != (key)) {
                        widgetWizardSelectedRows[key].widgetCompatible = false;
                        $('#widgetWizardSelectedRowsTable tr[data-rowid=' + key.replace('row', '') + ']').css("background-color", "#ffb3b3");
                    }
                }
            }*/
        }
        
        function updateIconsFromSelectedRows()
        {
            if(Object.keys(widgetWizardSelectedRows).length > 0)
            {
                $('.addWidgetWizardIconClickClass').each(function (j) 
                {
                    var count = 0;

                    var snap4citytype = $(this).attr('data-snap4citytype');
                    var snap4citytypeArray = snap4citytype.split(',');
                    var selectedRowUnits = null;

                    for(var k = 0; k < snap4citytypeArray.length; k++)
                    {
                        for(var key in widgetWizardSelectedRows)
                        {
                            selectedRowUnits = widgetWizardSelectedRows[key].unit.split(',');
                            for(var j = 0; j < selectedRowUnits.length; j++)
                            {
                                selectedRowUnits[j] = selectedRowUnits[j].trim();

                                if (selectedRowUnits[j] === snap4citytypeArray[k].trim())
                                {
                                    count++;
                                }
                            }
                        }
                    }

                    if(count > 0)
                    {
                        $(this).show();
                    } 
                    else
                    {
                        $(this).hide();
                    }
                });
            }
            else
            {
                //Nessuna riga selezionata
                var unitSelectSnapshotItem = null, unitSelectSnapshot = [];
                
                $("#unitSelect option").each(function(i){
                    unitSelectSnapshotItem = {
                        selected: true,
                        value: $(this).attr('value')
                    };
                    
                    unitSelectSnapshot.push(unitSelectSnapshotItem);
                });
                
                updateIcons(unitSelectSnapshot);
            }
        }

        function deselectAllIcons() {

            $('.addWidgetWizardIconClickClass').each(function (j)
            {
                var count = 0;

            //    if($(this).attr('data-selected') === 'true') {

                    validityConditions.widgetTypeSelected = false;
                    $('#addWidgetWizardWidgetAvailableMsg').html("");
                    $(this).attr('data-selected', 'false');
                    $(this).css('border', 'none');

                    choosenWidgetIconName = null;

                    $('#widgetWizardActuatorFieldsRow').hide();

                    wasSelected = true;

             //   }

            });

        }
        
        //Specifiche per caso widget wizard
        var unitSelect = null;
        var highLevelTypeSelectStartOptions = 0;
        var natureSelectStartOptions = 0;
        var subNatureSelectStartOptions = 0;
        var lowLevelTypeSelectStartOptions = 0;
        var unitSelectStartOptions = 0;
        var healthinessSelectStartOptions = 0;
        var ownershipSelectStartOptions = 0;
        var valueUnitSelectStartOptions = 0;
        var brokerSelectStartOptions = 0;
        var valueNameSelectStartOptions = 0;
        var valueTypeSelectStartOptions = 0;
        var modelNameSelectStartOptions = 0;

        var globalSqlFilter = [
            {
                "field": "high_level_type",
                "value": "",
                "active": "false",
                "selectedVals": [],
                "allSelected": true
            },
            {
                "field": "nature",
                "value": "",
                "active": "false",
                "selectedVals": [],
                "allSelected": true
            },
            {
                "field": "sub_nature",
                "value": "",
                "active": "false",
                "selectedVals": [],
                "allSelected": true
            },
            {
                "field": "low_level_type",
                "value": "",
                "active": "false",
                "selectedVals": [],
                "allSelected": true
            },
            {
                "field": "unique_name_id",
                "value": "",
                "active": "false",
                "selectedVals": [],
                "allSelected": true
            },
            {
                "field": "instance_uri",
                "value": "",
                "active": "false",
                "selectedVals": [],
                "allSelected": true
            },
            {
                "field": "unit",
                "value": "",
                "active": "false",
                "selectedVals": [],
                "allSelected": true
            },
            {
                "field": "healthiness",
                "value": "",
                "active": "false",
                "selectedVals": [],
                "allSelected": true
            },
            {
                "field": "ownership",
                "value": "",
                "active": "false",
                "selectedVals": [],
                "allSelected": true
            },
            {
                "field": "value_unit",
                "value": "",
                "active": "false",
                "selectedVals": [],
                "allSelected": true
            },
            {
                "field": "broker_name",
                "value": "",
                "active": "false",
                "selectedVals": [],
                "allSelected": true
            },
            {
                "field": "value_name",
                "value": "",
                "active": "false",
                "selectedVals": [],
                "allSelected": true
            },
            {
                "field": "value_type",
                "value": "",
                "active": "false",
                "selectedVals": [],
                "allSelected": true
            },
            {
                "field": "model_name",
                "value": "",
                "active": "false",
                "selectedVals": [],
                "allSelected": true
            }
        ];

        function applyHighLevelTypeFilter()
        {
            /*choosenWidgetIconName = null;
            widgetWizardSelectedRows = {};
            widgetWizardSelectedRowsTable.clear().draw(false);
            validityConditions.atLeastOneRowSelected = false;
            checkTab1Conditions();
            countSelectedRows();*/

            var search = [];
            $.each($('#highLevelTypeSelect option:selected'), function () {
                search.push($(this).val());
            });
            var nOptions = 0;
            $.each($('#highLevelTypeSelect option'), function () {
                nOptions++;
            });

            globalSqlFilter[0].allSelected = (search.length == nOptions);
            if (search.length == nOptions)
                search = [];
            globalSqlFilter[0].selectedVals = search;

            search = search.join('|');
            globalSqlFilter[0].value = search;
            if (search == '' && !globalSqlFilter[0].allSelected) {
                search = 'oiunqauhalknsufhvnoqwpnvfv';
            }
            widgetWizardTable.column(0).search(search, false, false).draw();
            globalSqlFilter[0].value = search;

            // Chiamata a funzione per popolare menù multi-select di filtraggio
            for (var n = 0; n < globalSqlFilter.length; n++)
            {
                if (n !== 4 && n != 5)
                {
                    populateSelectMenus("high_level_type", search, $('#highLevelTypeSelect'), "#highLevelTypeColumnFilter", n, false, true, FreezeMap, bounds);
                }
            }

            checkTab1Conditions();
            countSelectedRows();
        }

        function applyNatureFilter()
        {
            /*widgetWizardSelectedRows = {};
            choosenWidgetIconName = null;
            widgetWizardSelectedRowsTable.clear().draw(false);
            validityConditions.atLeastOneRowSelected = false;
            checkTab1Conditions();
            countSelectedRows();*/

            var search = [];
            $.each($('#natureSelect option:selected'), function () {   // CHANGE
                search.push($(this).val());
            });
            var nOptions = 0;
            $.each($('#natureSelect option'), function () {
                nOptions++;
            });

            globalSqlFilter[1].allSelected = (search.length == nOptions);
            if (search.length == nOptions)
                search = [];
            globalSqlFilter[1].selectedVals = search;
            search = search.join('|');

            globalSqlFilter[1].value = search;
            if (search == '' && !globalSqlFilter[1].allSelected) {
                search = 'oiunqauhalknsufhvnoqwpnvfv';
            }
            widgetWizardTable.column(1).search(search, false, false).draw();     // CHANGE
            globalSqlFilter[1].value = search;

            // Chiamata a funzione per popolare menù multi-select di filtraggio
            for (var n = 0; n < globalSqlFilter.length; n++) {
                if (n !== 4 && n != 5) {
                    populateSelectMenus("nature", search, $('#natureSelect'), "#natureColumnFilter", n, false, true, FreezeMap, bounds);
                }
            }

            checkTab1Conditions();
            countSelectedRows();
        }

        function applySubnatureFilter()
        {
            /*widgetWizardSelectedRows = {};
            choosenWidgetIconName = null;
            widgetWizardSelectedRowsTable.clear().draw(false);
            validityConditions.atLeastOneRowSelected = false;
            checkTab1Conditions();
            countSelectedRows();*/

            var search = [];
            $.each($('#subnatureSelect option:selected'), function () {   // CHANGE
                search.push($(this).val());

            });
            var nOptions = 0;
            $.each($('#subnatureSelect option'), function () {
                nOptions++;
            });

            globalSqlFilter[2].allSelected = (search.length == nOptions);
            if (search.length == nOptions)
                search = [];
            globalSqlFilter[2].selectedVals = search;
            search = search.join('|');

            globalSqlFilter[2].value = search;
            if (search == '' && !globalSqlFilter[2].allSelected) {
                search = 'oiunqauhalknsufhvnoqwpnvfv';
            }
            if (search.charAt(0) == '|') {
                search = search.substring(1);
            }
            widgetWizardTable.column(2).search(search, false, false).draw();     // CHANGE
            globalSqlFilter[2].value = search;

            // Chiamata a funzione per popolare menù multi-select di filtraggio
            for (var n = 0; n < globalSqlFilter.length; n++) {
                if (n !== 4 && n != 5) {
                    populateSelectMenus("sub_nature", search, $('#subnatureSelect'), "#subnatureColumnFilter", n, false, true, FreezeMap, bounds);
                }
            }

            checkTab1Conditions();
            countSelectedRows();
        }

        function applyValueTypeFilter()
        {
            /*widgetWizardSelectedRows = {};
            choosenWidgetIconName = null;
            widgetWizardSelectedRowsTable.clear().draw(false);
            validityConditions.atLeastOneRowSelected = false;
            checkTab1Conditions();
            countSelectedRows();*/

            var search = [];
            $.each($('#lowLevelTypeSelect option:selected'), function () {   // CHANGE
                search.push($(this).val());

            });
            var nOptions = 0;
            $.each($('#lowLevelTypeSelect option'), function () {
                nOptions++;
            });

            globalSqlFilter[3].allSelected = (search.length == nOptions);
            if (search.length == nOptions)
                search = [];
            globalSqlFilter[3].selectedVals = search;
            search = search.join('|');

            globalSqlFilter[3].value = search;
            if (search == '' && !globalSqlFilter[3].allSelected) {
                search = 'oiunqauhalknsufhvnoqwpnvfv';
            }
            widgetWizardTable.column(3).search(search, false, false).draw();     // CHANGE
            globalSqlFilter[3].value = search;

            // Chiamata a funzione per popolare menù multi-select di filtraggio
            for (var n = 0; n < globalSqlFilter.length; n++) {
                if (n !== 4 && n != 5) {
                    populateSelectMenus("low_level_type", search, $('#lowLevelTypeSelect'), "#lowLevelTypeColumnFilter", n, false, true, FreezeMap, bounds);
                }
            }

            checkTab1Conditions();
            countSelectedRows();
        }

        function applyDataTypeFilter()
        {
            /*widgetWizardSelectedRows = {};
            choosenWidgetIconName = null;
            widgetWizardSelectedRowsTable.clear().draw(false);
            validityConditions.atLeastOneRowSelected = false;
            checkTab1Conditions();
            countSelectedRows();*/

            var search = [];
            $.each($('#unitSelect option:selected'), function () {
                search.push($(this).val());
            });
            var nOptions = 0;
            $.each($('#unitSelect option'), function () {
                nOptions++;
            });

            globalSqlFilter[6].allSelected = (search.length == nOptions);
            if (search.length == nOptions)
                search = [];
            globalSqlFilter[6].selectedVals = search;
            search = search.join('|');

            globalSqlFilter[6].value = search;
            if (search == '' && !globalSqlFilter[6].allSelected) {
                search = 'oiunqauhalknsufhvnoqwpnvfv';
            }
            widgetWizardTable.column(10).search(search, false, false).draw();
            globalSqlFilter[6].value = search;

            // Chiamata a funzione per popolare menù multi-select di filtraggio
            for (var n = 0; n < globalSqlFilter.length; n++) {
                if (n !== 4 && n != 5) {
                    populateSelectMenus("unit", search, $('#unitSelect'), "#unitColumnFilter", n, false, true, FreezeMap, bounds);
                }
            }

            checkTab1Conditions();
            countSelectedRows();
        }

        function applyHealthinessFilter()
        {
            /*widgetWizardSelectedRows = {};
            choosenWidgetIconName = null;
            widgetWizardSelectedRowsTable.clear().draw(false);
            validityConditions.atLeastOneRowSelected = false;
            checkTab1Conditions();
            countSelectedRows();*/

            var search = [];
            $.each($('#healthinessSelect option:selected'), function () {
                search.push($(this).val());

            });
            var nOptions = 0;
            $.each($('#healthinessSelect option'), function () {
                nOptions++;
            });

            globalSqlFilter[7].allSelected = (search.length == nOptions);
            if (search.length == nOptions)
                search = [];
            globalSqlFilter[7].selectedVals = search;
            search = search.join('|');

            globalSqlFilter[7].value = search;
            if (search == '' && !globalSqlFilter[7].allSelected) {
                search = 'oiunqauhalknsufhvnoqwpnvfv';
            }
            widgetWizardTable.column(7).search(search, false, false).draw();
            globalSqlFilter[7].value = search;

            // Chiamata a funzione per popolare menù multi-select di filtraggio
            for (var n = 0; n < globalSqlFilter.length; n++) {
                if (n !== 4 && n != 5) {
                    populateSelectMenus("healthiness", search, $('#healthinessSelect'), "#healthinessColumnFilter", n, false, true, FreezeMap, bounds);
                }
            }

            checkTab1Conditions();
            countSelectedRows();
        }

        function applyOwnershipFilter()
        {
            /*widgetWizardSelectedRows = {};
            choosenWidgetIconName = null;
            widgetWizardSelectedRowsTable.clear().draw(false);
            validityConditions.atLeastOneRowSelected = false;
            checkTab1Conditions();
            countSelectedRows();*/

            var search = [];
            $.each($('#ownershipSelect option:selected'), function () {
                search.push($(this).val());

            });
            var nOptions = 0;
            $.each($('#ownershipSelect option'), function () {
                nOptions++;
            });

            globalSqlFilter[8].allSelected = (search.length == nOptions);
            if (search.length == nOptions)
                search = [];
            globalSqlFilter[8].selectedVals = search;
            search = search.join('|');

            globalSqlFilter[8].value = search;
            if (search == '' && !globalSqlFilter[8].allSelected) {
                search = 'oiunqauhalknsufhvnoqwpnvfv';
            }
            widgetWizardTable.column(8).search(search, false, false).draw();
            globalSqlFilter[8].value = search;

            // Chiamata a funzione per popolare menù multi-select di filtraggio
            for (var n = 0; n < globalSqlFilter.length; n++) {
                if (n !== 4 && n != 5) {
                    populateSelectMenus("ownership", search, $('#ownershipSelect'), "#ownershipColumnFilter", n, false, true, FreezeMap, bounds);
                }
            }

            checkTab1Conditions();
            countSelectedRows();
        }

        function applyValueUnitFilter()
        {

            var search = [];
            $.each($('#valueUnitSelect option:selected'), function () {
                search.push($(this).val());

            });
            var nOptions = 0;
            $.each($('#valueUnitSelect option'), function () {
                nOptions++;
            });

            globalSqlFilter[9].allSelected = (search.length == nOptions);
            if (search.length == nOptions)
                search = [];
            globalSqlFilter[9].selectedVals = search;
            search = search.join('|');

            globalSqlFilter[9].value = search;
            if (search == '' && !globalSqlFilter[9].allSelected) {
                search = 'oiunqauhalknsufhvnoqwpnvfv';
            }
            widgetWizardTable.column(20).search(search, false, false).draw();
            globalSqlFilter[9].value = search;

            // Chiamata a funzione per popolare menù multi-select di filtraggio
            for (var n = 0; n < globalSqlFilter.length; n++) {
                if (n !== 4 && n != 5) {
                    populateSelectMenus("value_unit", search, $('#valueUnitSelect'), "#valueUnitColumnFilter", n, false, true, FreezeMap, bounds);
                }
            }

            checkTab1Conditions();
            countSelectedRows();
        }

        function applyBrokerFilter()
        {

            var search = [];
            $.each($('#brokerSelect option:selected'), function () {
                search.push($(this).val());

            });
            var nOptions = 0;
            $.each($('#brokerSelect option'), function () {
                nOptions++;
            });

            globalSqlFilter[10].allSelected = (search.length == nOptions);
            if (search.length == nOptions)
                search = [];
            globalSqlFilter[10].selectedVals = search;
            search = search.join('|');

            globalSqlFilter[10].value = search;
            if (search == '' && !globalSqlFilter[10].allSelected) {
                search = 'oiunqauhalknsufhvnoqwpnvfv';
            }
            widgetWizardTable.column(20).search(search, false, false).draw();
            globalSqlFilter[10].value = search;

            // Chiamata a funzione per popolare menù multi-select di filtraggio
            for (var n = 0; n < globalSqlFilter.length; n++) {
                if (n !== 4 && n != 5) {
                    populateSelectMenus("broker_name", search, $('#brokerSelect'), "#brokerColumnFilter", n, false, true, FreezeMap, bounds);
                }
            }

            checkTab1Conditions();
            countSelectedRows();
        }

        function applyValueNameFilter()
        {

            var search = [];
            $.each($('#valueNameSelect option:selected'), function () {
                search.push($(this).val());

            });
            var nOptions = 0;
            $.each($('#valueNameSelect option'), function () {
                nOptions++;
            });

            globalSqlFilter[11].allSelected = (search.length == nOptions);
            if (search.length == nOptions)
                search = [];
            globalSqlFilter[11].selectedVals = search;
            search = search.join('|');

            globalSqlFilter[11].value = search;
            if (search == '' && !globalSqlFilter[11].allSelected) {
                search = 'oiunqauhalknsufhvnoqwpnvfv';
            }
            widgetWizardTable.column(8).search(search, false, false).draw();
            globalSqlFilter[11].value = search;

            // Chiamata a funzione per popolare menù multi-select di filtraggio
            for (var n = 0; n < globalSqlFilter.length; n++) {
                if (n !== 4 && n != 5) {
                    populateSelectMenus("value_name", search, $('#valueNameSelect'), "#valueNameColumnFilter", n, false, true, FreezeMap, bounds);
                }
            }

            checkTab1Conditions();
            countSelectedRows();
        }

        function applyValueTypeFilter()
        {

            var search = [];
            $.each($('#valueTypeSelect option:selected'), function () {
                search.push($(this).val());

            });
            var nOptions = 0;
            $.each($('#valueTypeSelect option'), function () {
                nOptions++;
            });

            globalSqlFilter[12].allSelected = (search.length == nOptions);
            if (search.length == nOptions)
                search = [];
            globalSqlFilter[12].selectedVals = search;
            search = search.join('|');

            globalSqlFilter[12].value = search;
            if (search == '' && !globalSqlFilter[12].allSelected) {
                search = 'oiunqauhalknsufhvnoqwpnvfv';
            }
            widgetWizardTable.column(9).search(search, false, false).draw();
            globalSqlFilter[12].value = search;

            // Chiamata a funzione per popolare menù multi-select di filtraggio
            for (var n = 0; n < globalSqlFilter.length; n++) {
                if (n !== 4 && n != 5) {
                    populateSelectMenus("value_type", search, $('#valueTypeSelect'), "#valueTypeColumnFilter", n, false, true, FreezeMap, bounds);
                }
            }

            checkTab1Conditions();
            countSelectedRows();
        }

        //Caricamento icone add widget wizard
        $.ajax({
            url: "../controllers/dashboardWizardControllerOS.php",
            type: "GET",
            data: {
                getDashboardWizardIcons: true,
				synMode: "<?=$synMode?$synMode:0?>"
            },
            async: true,
            dataType: 'json',
            success: function (data)
            {
                var newIcon = null;
                var spanElement = null;

                for (i = 0; i < data.table.length; i++)
                {
                    if (data.table[i].mainWidget != "widgetForm" && data.table[i].mainWidget != "widgetEvent" && data.table[i].mainWidget != "widgetSnap4D3") {
                        if (data.table[i].mono_multi === 'Mono') {
                            //ICONE MONO
                            newIcon = $('<div data-toggle="popover" data-placement="bottom" data-html="true" data-widgetCategory="' + data.table[i].widgetCategory + '" data-available="' + data.table[i].available + '" data-trigger="hover" data-selected="false" data-content="<span>' + data.table[i].description + '</span>" data-id="' + data.table[i].id + '" data-iconName="' + data.table[i].icon + '" data-mainWidget="' + data.table[i].mainWidget + '" data-targetWidget="' + data.table[i].targetWidget + '" data-snap4CityType="' + data.table[i].snap4CityType + '" data-icon="' + data.table[i].icon + '" data-mono_multi="' + data.table[i].mono_multi + '" data-description="' + data.table[i].description + '" class="iconsMonoSingleIcon addWidgetWizardIconClickClass"></div>');
                            newIcon.css('background-image', 'url("../img/widgetIcons/mono/' + data.table[i].icon + '")');

                            $('.addWidgetWizardIconsCnt').eq(0).append(newIcon);
                            $('body>.tooltip').remove();
                            $('[data-toggle="tooltip"]').tooltip();
                        } else {
                            //ICONE MULTI
                            newIcon = $('<div data-toggle="popover" data-placement="bottom" data-html="true" data-widgetCategory="' + data.table[i].widgetCategory + '" data-available="' + data.table[i].available + '" data-trigger="hover" data-selected="false" data-content="<span>' + data.table[i].description + '</span>" data-selected="false" data-id="' + data.table[i].id + '" data-iconName="' + data.table[i].icon + '" data-mainWidget="' + data.table[i].mainWidget + '" data-targetWidget="' + data.table[i].targetWidget + '" data-snap4CityType="' + data.table[i].snap4CityType + '" data-icon="' + data.table[i].icon + '" data-mono_multi="' + data.table[i].mono_multi + '" data-description="' + data.table[i].description + '" class="iconsMonoMultiIcon addWidgetWizardIconClickClass"></div>');
                            newIcon.css('background-image', 'url("../img/widgetIcons/multi/' + data.table[i].icon + '")');

                            $('.addWidgetWizardIconsCnt').eq(1).append(newIcon);
                        }
                    }
                }

                $('[data-toggle="popover"]').popover();

                // GESTIONE CLICK ICONE DASHBOARD WIZARD
                $('.addWidgetWizardIconClickClass').click(function ()
                {
                    var wasSelected = false;
                    
                    $('#widgetWizardActuatorFieldsRow').hide();
                    $('#actuatorEntityNameCell .wizardActInputCnt').val('');
                    $('#actuatorValueTypeCell .wizardActInputCnt').val('');
                    $('#actuatorMinBaseValueCell .wizardActInputCnt').val('');
                    $('#actuatorMaxBaseValueCell .wizardActInputCnt').val('');
                    $('.hideIfActuatorNew').show();

                    if (($(this).attr('data-mainwidget') == "widgetBarSeries" || $(this).attr('data-mainwidget') == "widgetCurvedLineSeries" || $(this).attr('data-mainwidget') == "widgetPieChart") && ObjectSize(widgetWizardSelectedRows) > 1) {
                        // if ((wizardSelectedRowsObj[Object.keys(wizardSelectedRowsObj)[Object.keys(wizardSelectedRowsObj).length -1]].value_unit == wizardSelectedRowsObj[Object.keys(wizardSelectedRowsObj)[0]].value_unit)) {
                        // OK
                        //   } else {
                        // custom check if widgetBarSeries: check if current selected rows has the same value_unit of first selected row. If not, mark current selected row as incompatible
                        updateBarSeriesWidgetSelectedRows(widgetWizardSelectedRows, Object.keys(widgetWizardSelectedRows)[0]);
                        //   }
                    }

                    if($(this).attr('data-selected') === 'false')
                    {
                        validityConditions.widgetTypeSelected = true;
                        $('#actuatorTargetInstance').val("existent");
                        $('#actuatorTargetWizard').val(-1);
                        
                        switch($(this).attr('data-mainwidget'))
                        {
                            case "widgetKnob":
                                $('#actuatorMinBaseValueCell .wizardActLbl').html("Min value");
                                $('#actuatorMaxBaseValueCell .wizardActLbl').html("Max value");
                                $('#actuatorMinBaseValue').val(0);
                                $('#actuatorMaxImpulseValue').val(100);
                                break;
                                
                            case "widgetOnOffButton":
                                $('#actuatorMinBaseValueCell .wizardActLbl').html("Off value");
                                $('#actuatorMaxBaseValueCell .wizardActLbl').html("On value");
                                $('#actuatorMinBaseValue').val("Off");
                                $('#actuatorMaxImpulseValue').val("On");
                                break; 
                                
                            case "widgetImpulseButton":
                                $('#actuatorMinBaseValueCell .wizardActLbl').html("Base value");
                                $('#actuatorMaxBaseValueCell .wizardActLbl').html("Impulse value");
                                $('#actuatorMinBaseValue').val("Off");
                                $('#actuatorMaxImpulseValue').val("On");
                                break;
                                
                            case "widgetNumericKeyboard":
                                break;  
                                
                            default:
                                break;
                        }
                        
                        $('.addWidgetWizardIconClickClass').each(function (i) {
                            $(this).attr('data-selected', 'false');
                            $(this).css('border', 'none');
                        });
                        $(this).attr('data-selected', 'true');
                        $(this).css('border', '1px solid rgba(0, 162, 211, 1)');
                        
                        if($(this).attr('data-widgetCategory') === 'actuator')
                        {
                            if(!location.href.includes("dashboard_configdash.php"))
                            {
                                if($('.modalAddDashboardWizardChoiceCnt[data-selected="true"]').attr("data-templatename") === 'iotApps')
                                {
                                    $('#actuatorTargetInstance').val('existent');
                                    $('#widgetWizardActuatorFieldsRow').hide();
                                }
                                else
                                {
                                    $('#widgetWizardActuatorFieldsRow').show();
                                }
                            }
                            else
                            {
                                $('#widgetWizardActuatorFieldsRow').show();
                            }
                        }
                        else
                        {
                            $('#widgetWizardActuatorFieldsRow').hide();
                        }
 
                        choosenWidgetIconName = $(this).attr('data-icon');
                    } 
                    else
                    {
                        validityConditions.widgetTypeSelected = false;
                        $('#addWidgetWizardWidgetAvailableMsg').html("");
                        $(this).attr('data-selected', 'false');
                        $(this).css('border', 'none');

                        choosenWidgetIconName = null;
                        
                        $('#widgetWizardActuatorFieldsRow').hide();
                        
                        wasSelected = true;
                    }

                    var selected = $(this).attr('data-selected');
                    
                    console.log("choosenWidgetIconName: " + choosenWidgetIconName + " - Selected: " + selected + "Validity condition: " + validityConditions.widgetTypeSelected);

                    //LOGICA DI GESTIONE DEI CLICK
                    //Versione pregressa: al deselect dell'icona vengono "riticcati" tutti i tipi di dato in quel momento nel menu a tendina delle unit
                    globalSqlFilter[6].allSelected = (selected === "false");     

                    var unit = $(this).attr('data-snap4CityType');
                            
                    $('#unitSelect').multiselect('deselectAll', false);        
                    
                    var unitArray = null;
                    
                    if(selected === "true") 
                    {
                        unitArray = unit.split(',');

                        for(var k = 0; k < unitArray.length; k++) 
                        {
                            $('#unitSelect').multiselect('select', unitArray[k].trim());
                        }
                    }
                    else
                    {
                        unit = '';        
                        unitArray = []; 
                        unitSelect.multiselect('selectAll', false);
                    }

                    var search = [];

                    for(k = 0; k < unitArray.length; k++) 
                    {
                        search.push(unitArray[k].trim());
                    }
                    
                    $.each($('#unitSelect option:selected'), function () {
                        if((!unit.includes($(this).val()))&&(search.indexOf($(this).val()) !== -1)) 
                        {
                            search.push(unit);
                        }
                    });
                    
                    var nOptions = 0;
                    $.each($('#unitSelect option'), function () {
                        nOptions++;
                    });

                    globalSqlFilter[6].allSelected = (search.length === nOptions);
                    if(search.length === nOptions)
                    {
                        search = [];
                    }
                    
                    globalSqlFilter[6].selectedVals = search;
                    search = search.join('|');
                    
                    widgetWizardTable.column(11).search(search, false, false).draw();
                    globalSqlFilter[6].value = search;
                    
                    if(!validityConditions.widgetTypeSelected)
                    {
                        globalSqlFilter[6].allSelected = true;
                    }
                    
                    // Chiamata a funzione per popolare menù multi-select di filtraggio
                    for(var n = 0; n < globalSqlFilter.length; n++)
                    {
                        if(n !== 4 && n != 5)
                        {
                            if(selected === "true")
                            {
                                populateSelectMenus("unit", search, unitSelect, "#unitColumnFilter", n, n === 10, false, FreezeMap, bounds);
                            }
                            else
                            {
                                if(widgetWizardSelectedUnits.length > 0)
                                {
                                    populateSelectMenus("unit", search, unitSelect, "#unitColumnFilter", n, n === 10, false, FreezeMap, bounds);
                                }
                                else
                                {
                                    populateSelectMenus("unit", search, unitSelect, "#unitColumnFilter", n, n === 10, true, FreezeMap, bounds);
                                }
                            }
                        }
                    }
                    
                    if((wasSelected)&&($(this).attr('data-widgetCategory') === 'actuator'))
                    {
                        $('#actuatorTargetInstance').val("existent");
                        $('#actuatorTargetWizard').val(-1);
                        $('#actuatorTargetCell .wizardActLbl').hide();
                        $('#actuatorTargetCell .wizardActInputCnt').hide();
                        $('#actuatorEntityNameCell .wizardActLbl').hide();
                        $('#actuatorEntityNameCell .wizardActInputCnt').hide();
                        $('#actuatorValueTypeCell .wizardActLbl').hide();
                        $('#actuatorValueTypeCell .wizardActInputCnt').hide();
                        $('#actuatorMinBaseValueCell .wizardActLbl').hide();
                        $('#actuatorMinBaseValueCell .wizardActInputCnt').hide();
                        $('#actuatorMaxBaseValueCell .wizardActLbl').hide();
                        $('#actuatorMaxBaseValueCell .wizardActInputCnt').hide();
                        //Reset campi custom attuatori
                        $('#actuatorEntityName').val('');
                        $('#actuatorValueType').val('');
                        $('.hideIfActuatorNew').show();
                    }
                    checkTab1Conditions();
                    updateWidgetCompatibleRows();
                });

            },
            error: function (errorData)
            {

            }
        });

        function doesFileExist(urlToFile) {
            var xhr = new XMLHttpRequest();
            xhr.open('HEAD', urlToFile, false);
            xhr.send();

            if (xhr.status == "404") {
                return false;
            } else {
                return true;
            }
        }

        function addWidgetWizardUserMarker (feature, latlng) {
        if (feature.properties.serviceType === 'IoTDevice_IoTSensor') {
                var mapPinImg = '../img/gisMapIcons/generic.png';
            } else {
                var mapPinImg = '../img/gisMapIcons/' + feature.properties.serviceType + '.png';
            }
            
            var markerIcon = L.icon({
                iconUrl: mapPinImg,
                iconAnchor: [16, 37]
            });

            var marker = new L.Marker(latlng, {icon: markerIcon});
            
            var latLngKey = latlng.lat + "" + latlng.lng;
            latLngKey = latLngKey.replace(".", "");
            latLngKey = latLngKey.replace(".", "");//Incomprensibile il motivo ma con l'espressione regolare /./g non funziona
            markersCache["" + latLngKey + ""] = marker;

            marker.on('mouseover', function (event) {
                if (feature.properties.serviceType === 'IoTDevice_IoTSensor') {
                    var hoverImg = '../img/gisMapIcons/over/generic_over.png';
                } else {
                    var hoverImg = '../img/gisMapIcons/over/' + feature.properties.serviceType + '_over.png';
                }
                var hoverIcon = L.icon({
                    iconUrl: hoverImg
                });
                event.target.setIcon(hoverIcon);
            });

            marker.on('mouseout', function (event) {
                if (feature.properties.serviceType === 'IoTDevice_IoTSensor') {
                    var outImg = '../img/gisMapIcons/generic.png';
                } else {
                    var outImg = '../img/gisMapIcons/' + feature.properties.serviceType + '.png';
                }
                var outIcon = L.icon({
                    iconUrl: outImg
                });
                event.target.setIcon(outIcon);
            });
             
        
        
        }

        //Funzione che prepara icone custom su mappa in base a quelle di ServiceMap
        function addWidgetWizardCreateCustomMarker(feature, latlng) {

            if (feature.properties.serviceType == null) {
                var mapPinImg = '../img/gisMapIcons/generic.png';
            } else {
                if (feature.properties.serviceType === 'IoTDevice_IoTSensor') {
                    var mapPinImg = '../img/gisMapIcons/generic.png';
                } else {
                    var mapPinImg = '../img/gisMapIcons/' + feature.properties.serviceType + '.png';
                }
            }

            if (!doesFileExist(mapPinImg)) {
                mapPinImg = '../img/gisMapIcons/generic.png';
            }

            var markerIcon = L.icon({
                iconUrl: mapPinImg,
                iconAnchor: [16, 37]
            });

            var marker = new L.Marker(latlng, {icon: markerIcon});
            
	    // GP_TEMP a cosa serve questa aggiunta stud?
            var latLngKey = latlng.lat + "" + latlng.lng;
            latLngKey = latLngKey.replace(".", "");
            latLngKey = latLngKey.replace(".", "");//Incomprensibile il motivo ma con l'espressione regolare /./g non funziona
            markersCache["" + latLngKey + ""] = marker;

            var uniqueNameId = feature.properties.name;

            marker.on('mouseover', function (event) {
                if (feature.properties.serviceType == null) {
                    var mapPinImg = '../img/gisMapIcons/over/generic_over.png';
                } else {
                    if (feature.properties.serviceType === 'IoTDevice_IoTSensor') {
                        var hoverImg = '../img/gisMapIcons/over/generic_over.png';
                    } else {
                        var hoverImg = '../img/gisMapIcons/over/' + feature.properties.serviceType + '_over.png';
                    }
                }

                if (!doesFileExist(hoverImg)) {
                    hoverImg = '../img/gisMapIcons/over/generic_over.png';
                }

                var hoverIcon = L.icon({
                    iconUrl: hoverImg
                });
                event.target.setIcon(hoverIcon);
            });

            marker.on('mouseout', function (event) {
                if (feature.properties.serviceType == null) {
                    var mapPinImg = '../img/gisMapIcons/generic.png';
                } else {
                    if (feature.properties.serviceType === 'IoTDevice_IoTSensor') {
                        var outImg = '../img/gisMapIcons/generic.png';
                    } else {
                        var outImg = '../img/gisMapIcons/' + feature.properties.serviceType + '.png';
                    }
                }

                if (!doesFileExist(outImg)) {
                    outImg = '../img/gisMapIcons/generic.png';
                }

                var outIcon = L.icon({
                    iconUrl: outImg
                });
                event.target.setIcon(outIcon);
            });

            marker.on('click', function (event) {
                event.target.unbindPopup();
                newpopup = null;
                var popupText, realTimeData, measuredTime, rtDataAgeSec, targetWidgets, color1, color2 = null;
                var urlToCall, fake, fakeId = null;

                if (feature.properties.fake === 'true')
                {
                    urlToCall = "../serviceMapFake.php?getSingleGeoJson=true&singleGeoJsonId=" + feature.id;
                    fake = true;
                    fakeId = feature.id;
                } else
                {
                    urlToCall = "<?= $superServiceMapProxy ?>api/v1/?serviceUri=" + encodeURI(feature.properties.serviceUri) + "&format=json&fullCount=false";   // URI_ENCODE_FIX
                //    urlToCall = "<?= $superServiceMapProxy; ?>api/v1/?serviceUri=" + feature.properties.serviceUri + "&format=json&fullCount=false";   // PANTALEO DA METTERE SUPERSERVICEMAP ??
                    fake = false;
                }

                var latLngId = event.target.getLatLng().lat + "" + event.target.getLatLng().lng;
                latLngId = latLngId.replace(".", "");
                latLngId = latLngId.replace(".", "");//Incomprensibile il motivo ma con l'espressione regolare /./g non funziona

                $.ajax({
                    url: urlToCall,
                    type: "GET",
                    data: {},
                    async: true,
                    dataType: 'json',
                    success: function (geoJsonServiceData)
                    {
                        var fatherNode = null;
                        if (geoJsonServiceData.hasOwnProperty("BusStop"))
                        {
                            fatherNode = geoJsonServiceData.BusStop;
                        } else
                        {
                            if (geoJsonServiceData.hasOwnProperty("Sensor"))
                            {
                                fatherNode = geoJsonServiceData.Sensor;
                            } else
                            {
                                //Prevedi anche la gestione del caso in cui non c'è nessuna di queste tre, sennò il widget rimane appeso.
                                fatherNode = geoJsonServiceData.Service;
                            }
                        }

                        var serviceProperties = fatherNode.features[0].properties;
                        var underscoreIndex = serviceProperties.serviceType.indexOf("_");
                        var serviceClass = serviceProperties.serviceType.substr(0, underscoreIndex);
                        var serviceSubclass = serviceProperties.serviceType.substr(underscoreIndex);
                        serviceSubclass = serviceSubclass.replace(/_/g, " ");

                        fatherNode.features[0].properties.targetWidgets = feature.properties.targetWidgets;
                        fatherNode.features[0].properties.color1 = feature.properties.color1;
                        fatherNode.features[0].properties.color2 = feature.properties.color2;
                        targetWidgets = ['DCTemp1_24_widgetTimeTrend6351', "SensoreViaBolognese_24_widgetSingleContent6353"]; //feature.properties.targetWidgets;
                        color1 = "#F0F0F0";
                        color2 = "#CCCCCC";

                        //Popup nuovo stile uguali a quelli degli eventi ricreativi
                        popupText = '<h3 class="recreativeEventMapTitle" style="background: ' + color1 + '; background: -webkit-linear-gradient(right, ' + color1 + ', ' + color2 + '); background: -o-linear-gradient(right, ' + color1 + ', ' + color2 + '); background: -moz-linear-gradient(right, ' + color1 + ', ' + color2 + '); background: linear-gradient(to right, ' + color1 + ', ' + color2 + ');">' + serviceProperties.name + '</h3>';
                        if((serviceProperties.serviceUri !== '')&&(serviceProperties.serviceUri !== undefined)&&(serviceProperties.serviceUri !== 'undefined')&&(serviceProperties.serviceUri !== null)&&(serviceProperties.serviceUri !== 'null')) {
                            popupText += '<div class="recreativeEventMapSubTitle" style="background: ' + color1 + '; background: -webkit-linear-gradient(right, ' + color1 + ', ' + color2 + '); background: -o-linear-gradient(right, ' + color1 + ', ' + color2 + '); background: -moz-linear-gradient(right, ' + color1 + ', ' + color2 + '); background: linear-gradient(to right, ' + color1 + ', ' + color2 + ');">' + "Value Name: " + serviceProperties.serviceUri.split("/")[serviceProperties.serviceUri.split("/").length - 1] + '</div>';
                            //  popupText += '<div class="recreativeEventMapSubTitle">' + "Value Name: " + serviceProperties.serviceUri.split("/")[serviceProperties.serviceUri.split("/").length - 1] + '</div>';
                        }
                        popupText += '<div class="recreativeEventMapBtnContainer"><button data-id="' + latLngId + '" class="recreativeEventMapDetailsBtn recreativeEventMapBtn recreativeEventMapBtnActive" type="button" style="background: ' + color1 + '; background: -webkit-linear-gradient(right, ' + color1 + ', ' + color2 + '); background: -o-linear-gradient(right, ' + color1 + ', ' + color2 + '); background: -moz-linear-gradient(right, ' + color1 + ', ' + color2 + '); background: linear-gradient(to right, ' + color1 + ', ' + color2 + ');"><?php echo _("Details"); ?></button><button data-id="' + latLngId + '" class="recreativeEventMapDescriptionBtn recreativeEventMapBtn" type="button" style="background: ' + color1 + '; background: -webkit-linear-gradient(right, ' + color1 + ', ' + color2 + '); background: -o-linear-gradient(right, ' + color1 + ', ' + color2 + '); background: -moz-linear-gradient(right, ' + color1 + ', ' + color2 + '); background: linear-gradient(to right, ' + color1 + ', ' + color2 + ');">Description</button><button data-id="' + latLngId + '" class="recreativeEventMapContactsBtn recreativeEventMapBtn" type="button" style="background: ' + color1 + '; background: -webkit-linear-gradient(right, ' + color1 + ', ' + color2 + '); background: -o-linear-gradient(right, ' + color1 + ', ' + color2 + '); background: -moz-linear-gradient(right, ' + color1 + ', ' + color2 + '); background: linear-gradient(to right, ' + color1 + ', ' + color2 + ');"><?= _("RT data")?></button></div>';

                        popupText += '<div class="recreativeEventMapDataContainer recreativeEventMapDetailsContainer">';

                        popupText += '<table id="' + latLngId + '" class="gisPopupGeneralDataTable">';
                        //Intestazione
                        popupText += '<thead>';
                        popupText += '<th style="background: ' + color2 + '"><?= _("Description")?></th>';
                        popupText += '<th style="background: ' + color2 + '"><?= _("Value")?></th>';
                        popupText += '</thead>';

                        //Corpo
                        popupText += '<tbody>';

                        for (var featureKey in serviceProperties) {
                            if (serviceProperties.hasOwnProperty(featureKey)) {
                                if (serviceProperties[featureKey] != null && serviceProperties[featureKey] !== '' && serviceProperties[featureKey] !== ' ' && featureKey !== 'targetWidgets' && featureKey !== 'color1' && featureKey !== 'color2' && featureKey !== 'realtimeAttributes') {
                                    if (!Array.isArray(serviceProperties[featureKey]) || (Array.isArray(serviceProperties[featureKey] && serviceProperties[featureKey].length > 0))) {
                                        popupText += '<tr><td>' + featureKey + '</td><td>' + serviceProperties[featureKey] + '</td></tr>';
                                    }
                                }
                            }
                        }

                        popupText += '</tbody>';
                        popupText += '</table>';

                        if (geoJsonServiceData.hasOwnProperty('busLines'))
                        {
                            if (geoJsonServiceData.busLines.results.bindings.length > 0)
                            {
                                popupText += '<b>Lines: </b>';
                                for (var i = 0; i < geoJsonServiceData.busLines.results.bindings.length; i++)
                                {
                                    popupText += '<span style="background: ' + color1 + '; background: -webkit-linear-gradient(right, ' + color1 + ', ' + color2 + '); background: -o-linear-gradient(right, ' + color1 + ', ' + color2 + '); background: -moz-linear-gradient(right, ' + color1 + ', ' + color2 + '); background: linear-gradient(to right, ' + color1 + ', ' + color2 + ');">' + geoJsonServiceData.busLines.results.bindings[i].busLine.value + '</span> ';
                                }
                            }
                        }

                        popupText += '</div>';

                        popupText += '<div class="recreativeEventMapDataContainer recreativeEventMapDescContainer">';

                        if((serviceProperties.serviceUri !== '')&&(serviceProperties.serviceUri !== undefined)&&(serviceProperties.serviceUri !== 'undefined')&&(serviceProperties.serviceUri !== null)&&(serviceProperties.serviceUri !== 'null')) {
                            popupText += "Value Name: " + serviceProperties.serviceUri.split("/")[serviceProperties.serviceUri.split("/").length - 1] + "<br>";
                        }

                        if((serviceProperties.serviceType !== '')&&(serviceProperties.serviceType !== undefined)&&(serviceProperties.serviceType !== 'undefined')&&(serviceProperties.serviceType !== null)&&(serviceProperties.serviceType !== 'null')) {
                            popupText += "Nature: " + serviceProperties.serviceType.split(/_(.+)/)[0] + "<br>";
                            popupText += "Subnature: " + serviceProperties.serviceType.split(/_(.+)/)[1] + "<br><br>";
                        }

                        popupText += '<div class="recreativeEventMapDataContainer recreativeEventNameContainer">' + $(this)[0].uniqueNameId + '</div>';

                        if (serviceProperties.hasOwnProperty('description'))
                        {
                            if ((serviceProperties.description !== '') && (serviceProperties.description !== undefined) && (serviceProperties.description !== 'undefined') && (serviceProperties.description !== null) && (serviceProperties.description !== 'null'))
                            {
                                popupText += serviceProperties.description + "<br>";
                            } else
                            {
                                popupText += "No description available";
                            }
                        } else
                        {
                            popupText += 'No description available';
                        }

                        popupText += '</div>';

                        popupText += '<div class="recreativeEventMapDataContainer recreativeEventMapContactsContainer">';

                        var hasRealTime = false;

                        if (geoJsonServiceData.hasOwnProperty("realtime"))
                        {
                            if (!jQuery.isEmptyObject(geoJsonServiceData.realtime))
                            {
                                realTimeData = geoJsonServiceData.realtime;

                                popupText += '<div class="popupLastUpdateContainer centerWithFlex"><b><?= _("Last update")?>:&nbsp;</b><span class="popupLastUpdate" data-id="' + latLngId + '"></span></div>';

                                if ((serviceClass.includes("Emergency")) && (serviceSubclass.includes("First aid")))
                                {
                                    //Tabella ad hoc per First Aid
                                    popupText += '<table id="' + latLngId + '" class="psPopupTable">';
                                    var series = {
                                        "firstAxis": {
                                            "desc": "Priority",
                                            "labels": [
                                                "Red code",
                                                "Yellow code",
                                                "Green code",
                                                "Blue code",
                                                "White code"
                                            ]
                                        },
                                        "secondAxis": {
                                            "desc": "Status",
                                            "labels": [],
                                            "series": []
                                        }
                                    };

                                    var dataSlot = null;

                                    measuredTime = realTimeData.results.bindings[0].measuredTime.value.replace("T", " ").replace("Z", "");

                                    for (var i = 0; i < realTimeData.results.bindings.length; i++)
                                    {
                                        if (realTimeData.results.bindings[i].state.value.indexOf("estinazione") > 0)
                                        {
                                            series.secondAxis.labels.push("Addressed");
                                        }

                                        if (realTimeData.results.bindings[i].state.value.indexOf("ttesa") > 0)
                                        {
                                            series.secondAxis.labels.push("Waiting");
                                        }

                                        if (realTimeData.results.bindings[i].state.value.indexOf("isita") > 0)
                                        {
                                            series.secondAxis.labels.push("In visit");
                                        }

                                        if (realTimeData.results.bindings[i].state.value.indexOf("emporanea") > 0)
                                        {
                                            series.secondAxis.labels.push("Observation");
                                        }

                                        if (realTimeData.results.bindings[i].state.value.indexOf("tali") > 0)
                                        {
                                            series.secondAxis.labels.push("Totals");
                                        }

                                        dataSlot = [];
                                        dataSlot.push(realTimeData.results.bindings[i].redCode.value);
                                        dataSlot.push(realTimeData.results.bindings[i].yellowCode.value);
                                        dataSlot.push(realTimeData.results.bindings[i].greenCode.value);
                                        dataSlot.push(realTimeData.results.bindings[i].blueCode.value);
                                        dataSlot.push(realTimeData.results.bindings[i].whiteCode.value);

                                        series.secondAxis.series.push(dataSlot);
                                    }

                                    var colsQt = parseInt(parseInt(series.firstAxis.labels.length) + 1);
                                    var rowsQt = parseInt(parseInt(series.secondAxis.labels.length) + 1);

                                    for (var i = 0; i < rowsQt; i++)
                                    {
                                        var newRow = $("<tr></tr>");
                                        var z = parseInt(parseInt(i) - 1);

                                        if (i === 0)
                                        {
                                            //Riga di intestazione
                                            for (var j = 0; j < colsQt; j++)
                                            {
                                                if (j === 0)
                                                {
                                                    //Cella (0,0)
                                                    var newCell = $("<td></td>");

                                                    newCell.css("background-color", "transparent");
                                                } else
                                                {
                                                    //Celle labels
                                                    var k = parseInt(parseInt(j) - 1);
                                                    var colLabelBckColor = null;
                                                    switch (k)
                                                    {
                                                        case 0:
                                                            colLabelBckColor = "#ff0000";
                                                            break;

                                                        case 1:
                                                            colLabelBckColor = "#ffff00";
                                                            break;

                                                        case 2:
                                                            colLabelBckColor = "#66ff33";
                                                            break;

                                                        case 3:
                                                            colLabelBckColor = "#66ccff";
                                                            break;

                                                        case 4:
                                                            colLabelBckColor = "#ffffff";
                                                            break;
                                                    }

                                                    newCell = $("<td><span>" + series.firstAxis.labels[k] + "</span></td>");
                                                    newCell.css("font-weight", "bold");
                                                    newCell.css("background-color", colLabelBckColor);
                                                }
                                                newRow.append(newCell);
                                            }
                                        } else
                                        {
                                            //Righe dati
                                            for (var j = 0; j < colsQt; j++)
                                            {
                                                k = parseInt(parseInt(j) - 1);
                                                if (j === 0)
                                                {
                                                    //Cella label
                                                    newCell = $("<td>" + series.secondAxis.labels[z] + "</td>");
                                                    newCell.css("font-weight", "bold");
                                                } else
                                                {
                                                    //Celle dati
                                                    newCell = $("<td>" + series.secondAxis.series[z][k] + "</td>");
                                                    if (i === (rowsQt - 1))
                                                    {
                                                        newCell.css('font-weight', 'bold');
                                                        switch (j)
                                                        {
                                                            case 1:
                                                                newCell.css('background-color', '#ffb3b3');
                                                                break;

                                                            case 2:
                                                                newCell.css('background-color', '#ffff99');
                                                                break;

                                                            case 3:
                                                                newCell.css('background-color', '#d9ffcc');
                                                                break;

                                                            case 4:
                                                                newCell.css('background-color', '#cceeff');
                                                                break;

                                                            case 5:
                                                                newCell.css('background-color', 'white');
                                                                break;
                                                        }
                                                    }
                                                }
                                                newRow.append(newCell);
                                            }
                                        }
                                        popupText += newRow.prop('outerHTML');
                                    }

                                    popupText += '</table>';
                                } else
                                {
                                    //Tabella nuovo stile
                                    popupText += '<table id="' + latLngId + '" class="gisPopupTable">';

                                    //Intestazione
                                    popupText += '<thead>';
                                    popupText += '<th style="background: ' + color1 + '; background: -webkit-linear-gradient(right, ' + color1 + ', ' + color2 + '); background: -o-linear-gradient(right, ' + color1 + ', ' + color2 + '); background: -moz-linear-gradient(right, ' + color1 + ', ' + color2 + '); background: linear-gradient(to right, ' + color1 + ', ' + color2 + ');">Description</th>';
                                    popupText += '<th style="background: ' + color1 + '; background: -webkit-linear-gradient(right, ' + color1 + ', ' + color2 + '); background: -o-linear-gradient(right, ' + color1 + ', ' + color2 + '); background: -moz-linear-gradient(right, ' + color1 + ', ' + color2 + '); background: linear-gradient(to right, ' + color1 + ', ' + color2 + ');">Value</th>';
                                    popupText += '<th colspan="5" style="background: ' + color1 + '; background: -webkit-linear-gradient(right, ' + color1 + ', ' + color2 + '); background: -o-linear-gradient(right, ' + color1 + ', ' + color2 + '); background: -moz-linear-gradient(right, ' + color1 + ', ' + color2 + '); background: linear-gradient(to right, ' + color1 + ', ' + color2 + ');">Buttons</th>';
                                    popupText += '</thead>';

                                    //Corpo
                                    popupText += '<tbody>';
                                    var dataDesc, dataVal, dataLastBtn, data4HBtn, dataDayBtn, data7DayBtn, data30DayBtn, data6MonthsBtn, data1YearBtn = null;
                                    for (var i = 0; i < realTimeData.head.vars.length; i++)
                                    {
                                        if ((realTimeData.results.bindings[0][realTimeData.head.vars[i]]) && (realTimeData.results.bindings[0][realTimeData.head.vars[i]].value.trim() !== '') && (realTimeData.head.vars[i] !== null) && (realTimeData.head.vars[i] !== 'undefined'))
                                        {
                                            if ((realTimeData.head.vars[i] !== 'updating') && (realTimeData.head.vars[i] !== 'measuredTime') && (realTimeData.head.vars[i] !== 'instantTime'))
                                            {
                                                if (!realTimeData.results.bindings[0][realTimeData.head.vars[i]].value.includes('Not Available'))
                                                {
                                                    //realTimeData.results.bindings[0][realTimeData.head.vars[i]].value = '-';
                                                /*    dataDesc = realTimeData.head.vars[i].replace(/([A-Z])/g, ' $1').replace(/^./, function (str) {
                                                        return str.toUpperCase();
                                                    }); */
                                                    dataDesc = realTimeData.head.vars[i];
                                                    dataVal = realTimeData.results.bindings[0][realTimeData.head.vars[i]].value;
                                                    dataLastBtn = '<td><button data-id="' + latLngId + '" type="button" class="lastValueBtn btn btn-sm" data-fake="' + fake + '" data-fakeid="' + fakeId + '" data-id="' + latLngId + '" data-field="' + realTimeData.head.vars[i] + '" data-serviceUri="' + feature.properties.serviceUri + '" data-lastDataClicked="false" data-targetWidgets="' + targetWidgets + '" data-lastValue="' + realTimeData.results.bindings[0][realTimeData.head.vars[i]].value + '" data-color1="' + color1 + '" data-color2="' + color2 + '">Last<br>value</button></td>';
                                                    data4HBtn = '<td><button data-id="' + latLngId + '" type="button" class="timeTrendBtn btn btn-sm" data-fake="' + fake + '" data-fakeid="' + fakeId + '" data-id="' + latLngId + '" data-field="' + realTimeData.head.vars[i] + '" data-serviceUri="' + feature.properties.serviceUri + '" data-timeTrendClicked="false" data-range-shown="4 Hours" data-range="4/HOUR" data-targetWidgets="' + targetWidgets + '" data-color1="' + color1 + '" data-color2="' + color2 + '">Last<br>4 hours</button></td>';
                                                    dataDayBtn = '<td><button data-id="' + latLngId + '" type="button" class="timeTrendBtn btn btn-sm" data-fake="' + fake + '" data-id="' + fakeId + '" data-field="' + realTimeData.head.vars[i] + '" data-serviceUri="' + feature.properties.serviceUri + '" data-timeTrendClicked="false" data-range-shown="Day" data-range="1/DAY" data-targetWidgets="' + targetWidgets + '" data-color1="' + color1 + '" data-color2="' + color2 + '">Last<br>24 hours</button></td>';
                                                    data7DayBtn = '<td><button data-id="' + latLngId + '" type="button" class="timeTrendBtn btn btn-sm" data-fake="' + fake + '" data-id="' + fakeId + '" data-field="' + realTimeData.head.vars[i] + '" data-serviceUri="' + feature.properties.serviceUri + '" data-timeTrendClicked="false" data-range-shown="7 days" data-range="7/DAY" data-targetWidgets="' + targetWidgets + '" data-color1="' + color1 + '" data-color2="' + color2 + '">Last<br>7 days</button></td>';
                                                    data30DayBtn = '<td><button data-id="' + latLngId + '" type="button" class="timeTrendBtn btn btn-sm" data-fake="' + fake + '" data-id="' + fakeId + '" data-field="' + realTimeData.head.vars[i] + '" data-serviceUri="' + feature.properties.serviceUri + '" data-timeTrendClicked="false" data-range-shown="30 days" data-range="30/DAY" data-targetWidgets="' + targetWidgets + '" data-color1="' + color1 + '" data-color2="' + color2 + '">Last<br>30 days</button></td>';
                                                    data6MonthsBtn = '<td><button data-id="' + latLngId + '" type="button" class="timeTrendBtn btn btn-sm" data-fake="' + fake + '" data-id="' + fakeId + '" data-field="' + realTimeData.head.vars[i] + '" data-serviceUri="' + feature.properties.serviceUri + '" data-timeTrendClicked="false" data-range-shown="6 months" data-range="180/DAY" data-targetWidgets="' + targetWidgets + '" data-color1="' + color1 + '" data-color2="' + color2 + '">Last<br>6 months</button></td>';
                                                    data1YearBtn = '<td><button data-id="' + latLngId + '" type="button" class="timeTrendBtn btn btn-sm" data-fake="' + fake + '" data-id="' + fakeId + '" data-field="' + realTimeData.head.vars[i] + '" data-serviceUri="' + feature.properties.serviceUri + '" data-timeTrendClicked="false" data-range-shown="1 year" data-range="365/DAY" data-targetWidgets="' + targetWidgets + '" data-color1="' + color1 + '" data-color2="' + color2 + '">Last<br>1 year</button></td>';
                                                    popupText += '<tr><td>' + dataDesc + '</td><td>' + dataVal + '</td>' + dataLastBtn + data4HBtn + dataDayBtn + data7DayBtn + data30DayBtn + data6MonthsBtn + data1YearBtn + '</tr>';
                                                }
                                            } else
                                            {
                                                measuredTime = realTimeData.results.bindings[0][realTimeData.head.vars[i]].value.replace("T", " ");
                                                var now = new Date();
                                                var measuredTimeDate = new Date(measuredTime);
                                                rtDataAgeSec = Math.abs(now - measuredTimeDate) / 1000;
                                            }
                                        }
                                    }
                                    popupText += '</tbody>';
                                    popupText += '</table>';
                                    popupText += '<p><b>Keep data on target widget(s) after popup close: </b><input data-id="' + latLngId + '" type="checkbox" class="gisPopupKeepDataCheck" data-keepData="false"/></p>';
                                }

                                hasRealTime = true;
                            }
                        }

                        popupText += '</div>';

                        newpopup = L.popup({
                            closeOnClick: false, //Non lo levare, sennò autoclose:false non funziona
                            autoClose: false,
                            offset: [15, 0],
                            minWidth: 435,
                            maxWidth: 435
                        }).setContent(popupText);

                        event.target.bindPopup(newpopup).openPopup();

                        if (hasRealTime)
                        {
                            $('#addWidgetWizardMapCnt2 button.recreativeEventMapContactsBtn[data-id="' + latLngId + '"]').show();
                            $('#addWidgetWizardMapCnt2 button.recreativeEventMapContactsBtn[data-id="' + latLngId + '"]').trigger("click");
                            $('#addWidgetWizardMapCnt2 span.popupLastUpdate[data-id="' + latLngId + '"]').html(measuredTime);
                        } else
                        {
                            $('#addWidgetWizardMapCnt2 button.recreativeEventMapContactsBtn[data-id="' + latLngId + '"]').hide();
                        }

                        $('#addWidgetWizardMapCnt2 button.recreativeEventMapDetailsBtn[data-id="' + latLngId + '"]').off('click');
                        $('#addWidgetWizardMapCnt2 button.recreativeEventMapDetailsBtn[data-id="' + latLngId + '"]').click(function () {
                            $('#addWidgetWizardMapCnt2 div.recreativeEventMapDataContainer').hide();
                            $('#addWidgetWizardMapCnt2 div.recreativeEventMapDetailsContainer').show();
                            $('#addWidgetWizardMapCnt2 button.recreativeEventMapBtn').removeClass('recreativeEventMapBtnActive');
                            $(this).addClass('recreativeEventMapBtnActive');
                        });

                        $('#addWidgetWizardMapCnt2 button.recreativeEventMapDescriptionBtn[data-id="' + latLngId + '"]').off('click');
                        $('#addWidgetWizardMapCnt2 button.recreativeEventMapDescriptionBtn[data-id="' + latLngId + '"]').click(function () {
                            $('#addWidgetWizardMapCnt2 div.recreativeEventMapDataContainer').hide();
                            $('#addWidgetWizardMapCnt2 div.recreativeEventMapDescContainer').show();
                            $('#addWidgetWizardMapCnt2 button.recreativeEventMapBtn').removeClass('recreativeEventMapBtnActive');
                            $(this).addClass('recreativeEventMapBtnActive');
                        });

                        $('#addWidgetWizardMapCnt2 button.recreativeEventMapContactsBtn[data-id="' + latLngId + '"]').off('click');
                        $('#addWidgetWizardMapCnt2 button.recreativeEventMapContactsBtn[data-id="' + latLngId + '"]').click(function () {
                            $('#addWidgetWizardMapCnt2 div.recreativeEventMapDataContainer').hide();
                            $('#addWidgetWizardMapCnt2 div.recreativeEventMapContactsContainer').show();
                            $('#addWidgetWizardMapCnt2 button.recreativeEventMapBtn').removeClass('recreativeEventMapBtnActive');
                            $(this).addClass('recreativeEventMapBtnActive');
                        });

                        if (hasRealTime)
                        {
                            $('#addWidgetWizardMapCnt2 button.recreativeEventMapContactsBtn[data-id="' + latLngId + '"]').trigger("click");
                        }

                        $('#addWidgetWizardMapCnt2 table.gisPopupTable[id="' + latLngId + '"] button.btn-sm').css("background", color2);
                        $('#addWidgetWizardMapCnt2 table.gisPopupTable[id="' + latLngId + '"] button.btn-sm').css("border", "none");
                        $('#addWidgetWizardMapCnt2 table.gisPopupTable[id="' + latLngId + '"] button.btn-sm').css("color", "black");

                        $('#addWidgetWizardMapCnt2 table.gisPopupTable[id="' + latLngId + '"] button.btn-sm').focus(function () {
                            $(this).css("outline", "0");
                        });

                        $('#addWidgetWizardMapCnt2 input.gisPopupKeepDataCheck[data-id="' + latLngId + '"]').off('click');
                        $('#addWidgetWizardMapCnt2 input.gisPopupKeepDataCheck[data-id="' + latLngId + '"]').click(function () {
                            if ($(this).attr("data-keepData") === "false")
                            {
                                $(this).attr("data-keepData", "true");
                            } else
                            {
                                $(this).attr("data-keepData", "false");
                            }
                        });
                        //inizio aggiunto berna
                        $('#addWidgetWizardMapCnt2 button.lastValueBtn').off('mouseenter');
                        $('#addWidgetWizardMapCnt2 button.lastValueBtn').off('mouseleave');
                        $('#addWidgetWizardMapCnt2 button.lastValueBtn[data-id="' + latLngId + '"]').hover(function(){
                            if($(this).attr("data-lastDataClicked") === "false")
                            {
                                $(this).css("background", color1);
                                $(this).css("background", "-webkit-linear-gradient(left, " + color1 + ", " + color2 + ")");
                                $(this).css("background", "background: -o-linear-gradient(left, " + color1 + ", " + color2 + ")");
                                $(this).css("background", "background: -moz-linear-gradient(left, " + color1 + ", " + color2 + ")");
                                $(this).css("background", "background: linear-gradient(to left, " + color1 + ", " + color2 + ")");
                                $(this).css("font-weight", "bold");
                            }

                            var widgetTargetList = $(this).attr("data-targetWidgets").split(',');
                            var colIndex = $(this).parent().index();
                            //var title = $(this).parents("tbody").find("tr").eq(0).find("th").eq(colIndex).html();
                            var title = $(this).parents("tr").find("td").eq(0).html();

                            for(var i = 0; i < widgetTargetList.length; i++)
                            {
                                $.event.trigger({
                                    type: "mouseOverLastDataFromExternalContentGis_" + widgetTargetList[i],
                                    eventGenerator: $(this),
                                    targetWidget: widgetTargetList[i],
                                    value: $(this).attr("data-lastValue"),
                                    color1: $(this).attr("data-color1"),
                                    color2: $(this).attr("data-color2"),
                                    widgetTitle: title
                                }); 
                            }
                        }, 
                        function(){
                            if($(this).attr("data-lastDataClicked")=== "false")
                            {
                                $(this).css("background", color2);
                                $(this).css("font-weight", "normal"); 
                            }
                            var widgetTargetList = $(this).attr("data-targetWidgets").split(',');

                            for(var i = 0; i < widgetTargetList.length; i++)
                            {
                                $.event.trigger({
                                    type: "mouseOutLastDataFromExternalContentGis_" + widgetTargetList[i],
                                    eventGenerator: $(this),
                                    targetWidget: widgetTargetList[i],
                                    value: $(this).attr("data-lastValue"),
                                    color1: $(this).attr("data-color1"),
                                    color2: $(this).attr("data-color2")
                                }); 
                            }
                        });
                        
                        //Disabilitiamo i 4Hours se last update più vecchio di 4 ore
                        if(rtDataAgeSec > 14400)
                        {
                            $('#addWidgetWizardMapCnt2 button.timeTrendBtn[data-id="' + latLngId + '"][data-range="4/HOUR"]').attr("data-disabled", "true");
                            //Disabilitiamo i 24Hours se last update più vecchio di 24 ore
                            if(rtDataAgeSec > 86400)
                            {
                                $('#addWidgetWizardMapCnt2 button.timeTrendBtn[data-id="' + latLngId + '"][data-range="1/DAY"]').attr("data-disabled", "true");
                                //Disabilitiamo i 7 days se last update più vecchio di 7 days
                                if(rtDataAgeSec > 604800)
                                {
                                    $('#addWidgetWizardMapCnt2 button.timeTrendBtn[data-id="' + latLngId + '"][data-range="7/DAY"]').attr("data-disabled", "true");
                                    //Disabilitiamo i 30 days se last update più vecchio di 30 days
                                    //if(rtDataAgeSec > 18144000)
                                    if(rtDataAgeSec > 2592000)
                                    {
                                       $('#addWidgetWizardMapCnt2 button.timeTrendBtn[data-id="' + latLngId + '"][data-range="30/DAY"]').attr("data-disabled", "true");
                                        //Disabilitiamo i 6 months se last update più vecchio di 180 days
                                        if(rtDataAgeSec > 15552000)
                                        {
                                            $('#addWidgetWizardMapCnt2 button.timeTrendBtn[data-id="' + latLngId + '"][data-range="180/DAY"]').attr("data-disabled", "true");
                                            //Disabilitiamo i 1 year se last update più vecchio di 365 days
                                            if(rtDataAgeSec > 31536000)
                                            {
                                                $('#addWidgetWizardMapCnt2 button.timeTrendBtn[data-id="' + latLngId + '"][data-range="365/DAY"]').attr("data-disabled", "true");
                                            }
                                            else
                                            {
                                                $('#addWidgetWizardMapCnt2 button.timeTrendBtn[data-id="' + latLngId + '"][data-range="365/DAY"]').attr("data-disabled", "false");
                                            }
                                        }
                                        else
                                        {
                                            $('#addWidgetWizardMapCnt2 button.timeTrendBtn[data-id="' + latLngId + '"][data-range="180/DAY"]').attr("data-disabled", "false");
                                        }
                                    }
                                    else
                                    {
                                        $('#addWidgetWizardMapCnt2 button.timeTrendBtn[data-id="' + latLngId + '"][data-range="30/DAY"]').attr("data-disabled", "false");
                                    }
                                }
                                else
                                {
                                    $('#addWidgetWizardMapCnt2 button.timeTrendBtn[data-id="' + latLngId + '"][data-range="7/DAY"]').attr("data-disabled", "false");
                                }
                            }
                            else
                            {
                                $('#addWidgetWizardMapCnt2 button.timeTrendBtn[data-id="' + latLngId + '"][data-range="1/DAY"]').attr("data-disabled", "false");
                            }
                        }
                        else
                        {
                            $('#addWidgetWizardMapCnt2 button.timeTrendBtn[data-id="' + latLngId + '"][data-range="4/HOUR"]').attr("data-disabled", "false");
                            $('#addWidgetWizardMapCnt2 button.timeTrendBtn[data-id="' + latLngId + '"][data-range="1/DAY"]').attr("data-disabled", "false");
                            $('#addWidgetWizardMapCnt2 button.timeTrendBtn[data-id="' + latLngId + '"][data-range="7/DAY"]').attr("data-disabled", "false");
                            $('#addWidgetWizardMapCnt2 button.timeTrendBtn[data-id="' + latLngId + '"][data-range="30/DAY"]').attr("data-disabled", "false");
                            $('#addWidgetWizardMapCnt2 button.timeTrendBtn[data-id="' + latLngId + '"][data-range="180/DAY"]').attr("data-disabled", "false");
                            $('#addWidgetWizardMapCnt2 button.timeTrendBtn[data-id="' + latLngId + '"][data-range="365/DAY"]').attr("data-disabled", "false");
                        }

                        $('#addWidgetWizardMapCnt2 button.timeTrendBtn').off('mouseenter');
                        $('#addWidgetWizardMapCnt2 button.timeTrendBtn').off('mouseleave');
                        $('#addWidgetWizardMapCnt2 button.timeTrendBtn[data-id="' + latLngId + '"]').hover(function(){
                            if(isNaN(parseFloat($(this).parents('tr').find('td').eq(1).html()))||($(this).attr("data-disabled") === "true"))
                            {
                                $(this).css("background-color", "#e6e6e6");
                                $(this).off("hover");
                                $(this).off("click");
                            }
                            else
                            {
                                if($(this).attr("data-timeTrendClicked") === "false")
                                {
                                    $(this).css("background", color1);
                                    $(this).css("background", "-webkit-linear-gradient(left, " + color1 + ", " + color2 + ")");
                                    $(this).css("background", "background: -o-linear-gradient(left, " + color1 + ", " + color2 + ")");
                                    $(this).css("background", "background: -moz-linear-gradient(left, " + color1 + ", " + color2 + ")");
                                    $(this).css("background", "background: linear-gradient(to left, " + color1 + ", " + color2 + ")");
                                    $(this).css("font-weight", "bold");
                                }

                                var widgetTargetList = $(this).attr("data-targetWidgets").split(',');
                                //var colIndex = $(this).parent().index();
                                //var title = $(this).parents("tbody").find("tr").eq(0).find("th").eq(colIndex).html() + " - " + $(this).attr("data-range-shown");
                                var title = $(this).parents("tr").find("td").eq(0).html() + " - " + $(this).attr("data-range-shown");

                                for(var i = 0; i < widgetTargetList.length; i++)
                                {
                                    $.event.trigger({
                                        type: "mouseOverTimeTrendFromExternalContentGis_" + widgetTargetList[i],
                                        eventGenerator: $(this),
                                        targetWidget: widgetTargetList[i],
                                        value: $(this).attr("data-lastValue"),
                                        color1: $(this).attr("data-color1"),
                                        color2: $(this).attr("data-color2"),
                                        widgetTitle: title
                                    }); 
                                }
                            }
                        }, 
                        function(){
                            if(isNaN(parseFloat($(this).parents('tr').find('td').eq(1).html()))||($(this).attr("data-disabled") === "true"))
                            {
                                $(this).css("background-color", "#e6e6e6");
                                $(this).off("hover");
                                $(this).off("click");
                            }
                            else
                            {
                                if($(this).attr("data-timeTrendClicked")=== "false")
                                {
                                    $(this).css("background", color2);
                                    $(this).css("font-weight", "normal"); 
                                }

                                var widgetTargetList = $(this).attr("data-targetWidgets").split(',');
                                for(var i = 0; i < widgetTargetList.length; i++)
                                {
                                    $.event.trigger({
                                        type: "mouseOutTimeTrendFromExternalContentGis_" + widgetTargetList[i],
                                        eventGenerator: $(this),
                                        targetWidget: widgetTargetList[i],
                                        value: $(this).attr("data-lastValue"),
                                        color1: $(this).attr("data-color1"),
                                        color2: $(this).attr("data-color2")
                                    }); 
                                }
                            }
                        });

                        $('#addWidgetWizardMapCnt2 button.lastValueBtn[data-id=' + latLngId + ']').off('click');
                        $('#addWidgetWizardMapCnt2 button.lastValueBtn[data-id=' + latLngId + ']').click(function(event){
                            $('#addWidgetWizardMapCnt2 button.lastValueBtn').each(function(i){
                                $(this).css("background", $(this).attr("data-color2"));
                            });
                            $('#addWidgetWizardMapCnt2 button.lastValueBtn').css("font-weight", "normal");
                            $(this).css("background", $(this).attr("data-color1"));
                            $(this).css("font-weight", "bold");
                            $('#addWidgetWizardMapCnt2 button.lastValueBtn').attr("data-lastDataClicked", "false");
                            $(this).attr("data-lastDataClicked", "true");
                            var widgetTargetList = $(this).attr("data-targetWidgets").split(',');
                            widgetTargetList[1]='SensoreViaBolognese_24_widgetSingleContent6353';
                            var colIndex = $(this).parent().index();
                            var title = $(this).parents("tr").find("td").eq(0).html();

                            for(var i = 0; i < widgetTargetList.length; i++)
                            {
                                $.event.trigger({
                                    type: "showLastDataFromExternalContentGis_" + widgetTargetList[i],
                                    eventGenerator: $(this),
                                    targetWidget: widgetTargetList[i],
                                    value: $(this).attr("data-lastValue"),
                                    color1: $(this).attr("data-color1"),
                                    color2: $(this).attr("data-color2"),
                                    widgetTitle: title,
                                    field: $(this).attr("data-field"),
                                    serviceUri: $(this).attr("data-serviceUri"),
                                    marker: markersCache["" + $(this).attr("data-id") + ""],
                                    mapRef: addWidgetWizardMapRef,//gisMapRef,
                                    fake: $(this).attr("data-fake"),
                                    fakeId: $(this).attr("data-fakeId")
                                });
                            }
                        });

                        $('#addWidgetWizardMapCnt2 button.timeTrendBtn').off('click');
                        $('#addWidgetWizardMapCnt2 button.timeTrendBtn').click(function(event){
                            if(isNaN(parseFloat($(this).parents('tr').find('td').eq(1).html()))||($(this).attr("data-disabled") === "true"))
                            {
                                $(this).css("background-color", "#e6e6e6");
                                $(this).off("hover");
                                $(this).off("click");
                            }
                            else
                            {
                                $('#addWidgetWizardMapCnt2 button.timeTrendBtn').css("background", $(this).attr("data-color2"));
                                $('#addWidgetWizardMapCnt2 button.timeTrendBtn').css("font-weight", "normal");
                                $(this).css("background", $(this).attr("data-color1"));
                                $(this).css("font-weight", "bold");
                                $('#addWidgetWizardMapCnt2 button.timeTrendBtn').attr("data-timeTrendClicked", "false");
                                $(this).attr("data-timeTrendClicked", "true");
                                var widgetTargetList = $(this).attr("data-targetWidgets").split(',');
                                var colIndex = $(this).parent().index();
                                var title = $(this).parents("tr").find("td").eq(0).html() + " - " + $(this).attr("data-range-shown");
                                var lastUpdateTime = $(this).parents('div.recreativeEventMapContactsContainer').find('span.popupLastUpdate').html();

                                var now = new Date();
                                var lastUpdateDate = new Date(lastUpdateTime);
                                var diff = parseFloat(Math.abs(now-lastUpdateDate)/1000);
                                var range = $(this).attr("data-range");
                                console.log(widgetTargetList[0]);

                                for(var i = 0; i < widgetTargetList.length; i++)
                                {
                                    $.event.trigger({
                                        type: "showTimeTrendFromExternalContentGis_" + widgetTargetList[i],
                                        eventGenerator: $(this),
                                        targetWidget: widgetTargetList[i],
                                        range: range,
                                        color1: $(this).attr("data-color1"),
                                        color2: $(this).attr("data-color2"),
                                        widgetTitle: title,
                                        field: $(this).attr("data-field"),
                                        serviceUri: $(this).attr("data-serviceUri"),
                                        marker: markersCache["" + $(this).attr("data-id") + ""],
                                        mapRef: addWidgetWizardMapRef,//gisMapRef,
                                        fake: false
                                        //fake: $(this).attr("data-fake")
                                    }); 
                                }

                                $('#addWidgetWizardMapCnt2 button.timeTrendBtn[data-id="' + latLngId + '"]').each(function(i){
                                    if(isNaN(parseFloat($(this).parents('tr').find('td').eq(1).html()))||($(this).attr("data-disabled") === "true"))
                                    {
                                        $(this).css("background-color", "#e6e6e6");
                                        $(this).off("hover");
                                        $(this).off("click");
                                    }
                                });

                            }
                        });
                        
                        $('#addWidgetWizardMapCnt2 button.timeTrendBtn[data-id="' + latLngId + '"]').each(function(i){
                            if(isNaN(parseFloat($(this).parents('tr').find('td').eq(1).html()))||($(this).attr("data-disabled") === "true"))
                            {
                                $(this).css("background-color", "#e6e6e6");
                                $(this).off("hover");
                                $(this).off("click");
                            }
                        });

                        addWidgetWizardMapRef.off('popupclose');//gisMapRef.off('popupclose');
                        addWidgetWizardMapRef.on('popupclose', function(closeEvt) {    // gisMapRef.on('popupclose', function(closeEvt) {
                            var popupContent = $('<div></div>');
                            popupContent.html(closeEvt.popup._content);
                            
                            if(popupContent.find("button.lastValueBtn").length > 0)
                            {
                                var widgetTargetList = popupContent.find("button.lastValueBtn").eq(0).attr("data-targetWidgets").split(',');

                                if(($('#addWidgetWizardMapCnt2 button.lastValueBtn[data-lastDataClicked=true]').length > 0)&&($('input.gisPopupKeepDataCheck').attr('data-keepData') === "false"))
                                {
                                    for(var i = 0; i < widgetTargetList.length; i++)
                                    {
                                        $.event.trigger({
                                            type: "restoreOriginalLastDataFromExternalContentGis_" + widgetTargetList[i],
                                            eventGenerator: $(this),
                                            targetWidget: widgetTargetList[i],
                                            value: $(this).attr("data-lastValue"),
                                            color1: $(this).attr("data-color1"),
                                            color2: $(this).attr("data-color2")
                                        }); 
                                    } 
                                }

                                if(($('#addWidgetWizardMapCnt2 button.timeTrendBtn[data-timeTrendClicked=true]').length > 0)&&($('input.gisPopupKeepDataCheck').attr('data-keepData') === "false"))
                                {
                                    for(var i = 0; i < widgetTargetList.length; i++)
                                    {
                                        $.event.trigger({
                                            type: "restoreOriginalTimeTrendFromExternalContentGis_" + widgetTargetList[i],
                                            eventGenerator: $(this),
                                            targetWidget: widgetTargetList[i]
                                        }); 
                                    } 
                                } 
                            }
                        });

                        $('#addWidgetWizardMapCnt2 div.leaflet-popup').off('click');
                        $('#addWidgetWizardMapCnt2 div.leaflet-popup').on('click', function(){
                            var compLatLngId = $(this).find('input[type=hidden]').val();

                            $('#addWidgetWizardMapCnt2 div.leaflet-popup').css("z-index", "-1");
                            $(this).css("z-index", "999999");

                            $('#addWidgetWizardMapCnt2 input.gisPopupKeepDataCheck').off('click');
                            $('#addWidgetWizardMapCnt2 input.gisPopupKeepDataCheck[data-id="' + compLatLngId + '"]').click(function(){
                            if($(this).attr("data-keepData") === "false")
                                {
                                   $(this).attr("data-keepData", "true"); 
                                }
                                else
                                {
                                   $(this).attr("data-keepData", "false"); 
                                }
                            });

                        });

                        $('#addWidgetWizardMapCnt2 button.timeTrendBtn[data-id="' + latLngId + '"]').each(function(i){
                            if(isNaN(parseFloat($(this).parents('tr').find('td').eq(1).html()))||($(this).attr("data-disabled") === "true"))
                            {
                                $(this).css("background-color", "#e6e6e6");
                                $(this).off("hover");
                                $(this).off("click");
                            }
                        });
                        //aggiunto berna fine
                    },
                    error: function (errorData)
                    {
                        console.log("Error in data retrieval");
                        console.log(JSON.stringify(errorData));
                        var serviceProperties = feature.properties;

                        var underscoreIndex = serviceProperties.serviceType.indexOf("_");
                        var serviceClass = serviceProperties.serviceType.substr(0, underscoreIndex);
                        var serviceSubclass = serviceProperties.serviceType.substr(underscoreIndex);
                        serviceSubclass = serviceSubclass.replace(/_/g, " ");

                        popupText = '<h3 class="gisPopupTitle">' + serviceProperties.name + '</h3>' +
                                '<p><b>Typology: </b>' + serviceClass + " - " + serviceSubclass + '</p>' //+
                            //    '<p><i>Data are limited due to an issue in their retrieval</i></p>';

                        event.target.bindPopup(popupText, {
                            offset: [15, 0],
                            minWidth: 215,
                            maxWidth: 600
                        }).openPopup();
                    }
                });
            });
         //   addWidgetWizardMapMarkers.push(marker);
            var currMarkString = currentMarkerId.toString();
            addWidgetWizardMapMarkers[currMarkString] = marker;
            return marker;
        }


        function clearMarker(id) {
        //    console.log(markers)
        //    var new_markers = [];
          //  if (addWidgetWizardMapMarkers.length == 1) {

          //  } else {
            for (var singleMarkerKey in addWidgetWizardMapMarkers) {
                //    addWidgetWizardMapMarkers.forEach(function(marker) {
                if (singleMarkerKey == id) {
                    addWidgetWizardMapRef.removeLayer(addWidgetWizardMapMarkers[singleMarkerKey]);
                    delete addWidgetWizardMapMarkers[singleMarkerKey];
                } else {
                    //       addWidgetWizardMapMarkers[currentMarkerId] = addWidgetWizardMapMarkers[singleMarkerKey];
                }
            }
         //   }
         //   addWidgetWizardMapMarkers = new_markers;
        }

        function clearAllMarkers() {

            for (var singleMarkerKey in addWidgetWizardMapMarkers) {
                //    addWidgetWizardMapMarkers.forEach(function(marker) {
                    addWidgetWizardMapRef.removeLayer(addWidgetWizardMapMarkers[singleMarkerKey]);
                    delete addWidgetWizardMapMarkers[singleMarkerKey];
            }

        }

        // widgetWizardTable JS LOGIC ************************************************************************
        $('.checkWidgWizCol').change(function (e) {
            e.preventDefault();
            if ($(this).attr('data-fieldTitle') === "high_level_type") {
                var idx = 0;
            } else if ($(this).attr('data-fieldTitle') === "nature") {
                var idx = 1;
            } else if ($(this).attr('data-fieldTitle') === "sub_nature") {
                var idx = 2;
            } else if ($(this).attr('data-fieldTitle') === "low_level_type") {
                var idx = 3;
            } else if ($(this).attr('data-fieldTitle') === "unique_name_id") {
                var idx = 4;
            } else if ($(this).attr('data-fieldTitle') === "unit") {
                var idx = 11;
            } else if ($(this).attr('data-fieldTitle') === "last_date") {
                var idx = 13;
            } else if ($(this).attr('data-fieldTitle') === "last_value") {
                var idx = 14;
            } else if ($(this).attr('data-fieldTitle') === "healthiness") {
                var idx = 15;
            } else if ($(this).attr('data-fieldTitle') === "lastCheck") {
                var idx = 19;
            } else if ($(this).attr('data-fieldTitle') === "value_unit") {
                var idx = 12;
            } else if ($(this).attr('data-fieldTitle') === "ownership") {
                var idx = 21;
            } else if ($(this).attr('data-fieldTitle') === "device_model_name") {
                var idx = 6;
            } else if ($(this).attr('data-fieldTitle') === "broker_name") {
                var idx = 8;
            } else if ($(this).attr('data-fieldTitle') === "value_name") {
                var idx = 9;
            } else if ($(this).attr('data-fieldTitle') === "value_type") {
                var idx = 10;
            }
			
			if ($(this).is(":checked")) {
                // Get the column API object
                var column = widgetWizardTable.column(idx);
                // Toggle the visibility
                column.visible(!column.visible());				
            } else {
                var column = widgetWizardTable.column(idx);
                column.visible(!column.visible());
            }
			
        });


        //$('#uniqueNameIdColumnFilter').append('<input id="widgetWIzardTableSearch" type="text" placeholder="Search Value Name" />');

        // Funzione per il popolamento del menù multi-select di filtraggio tabella widgetWIzardTable
        function populateSelectMenus(field, searchTerm, selectElement, columnFilterDivId, n, fromIconFlag, updateIconsFlag, FreezeMap, bounds)
        {

            globalSqlFilter[n].active = "";
            var distinctField = "";

            if (n == 0) 
            {
                distinctField = "high_level_type";
            } 
            else if (n == 1) 
            {
                distinctField = "nature";
            } 
            else if (n == 2) 
            {
                distinctField = "sub_nature";
            }
            else if (n == 3) 
            {
                distinctField = "low_level_type";
            } else if (n == 6) {
                distinctField = "unit";
            } else if (n == 7) {
                distinctField = "healthiness";
            } else if (n == 8) {
                distinctField = "ownership";
            } else if (n == 9) {
                distinctField = "value_unit";
            } else if (n == 10) {
                distinctField = "broker_name";
            } else if (n == 11) {
                distinctField = "value_name";
            } else if (n == 12) {
                distinctField = "value_type";
            } else if (n == 13) {
                distinctField = "model_name";
            }

            var nActive = 0;
            for (var i = 0, len = globalSqlFilter.length; i < len; i++)
            {
                if (globalSqlFilter[i].value != "") {
                    nActive++;
                }
            }


            if((distinctField !== field || nActive == 0)||fromIconFlag) 
            {
                let neLat, neLng, swLat, swLng = null;
                if (bounds) {
                    neLat = bounds._northEast.lat;
                    neLng = bounds._northEast.lng;
                    swLat = bounds._southWest.lat;
                    swLng = bounds._southWest.lng;
                }

                $.ajax({
                    url: "../controllers/dashboardWizardControllerOS.php",
                    type: "GET",
                    async: true,
                    dataType: 'json',
                    data:
                    {
                        orgFilter: "<?php if (isset($_SESSION['loggedOrganization'])){echo $_SESSION['loggedOrganization'];} else {echo "Other";} ?>",
                        globalSqlFilter: globalSqlFilter,
                        nActive: nActive,
                        n: n,
                        filter: distinctField,
                        //filterGlobal: whereString,
                        distinctField: distinctField,
                        synMode: "<?=$synMode?$synMode:0?>",
                        filterOrg: orgFilter,
                        northEastPointLat: neLat,
                        northEastPointLng: neLng,
                        southWestPointLat: swLat,
                        southWestPointLng: swLng,
                        FreezeMap: FreezeMap,
                    },
                    success: function (data)
                    {

                        var dataNew = [];
                        var select = "";
                        if (distinctField === "high_level_type") {
                            select = $("#highLevelTypeSelect");
                        } else if (distinctField === "nature") {
                            select = $("#natureSelect");
                        } else if (distinctField === "sub_nature") {
                            select = $("#subnatureSelect");
                        } else if (distinctField === "low_level_type") {
                            select = $("#lowLevelTypeSelect");
                        } else if (distinctField === "unit") {
                            select = $("#unitSelect");
                        } else if (distinctField === "healthiness") {
                            select = $("#healthinessSelect");
                        } else if (distinctField === "ownership") {
                            select = $("#ownershipSelect");
                        } else if (distinctField === "value_unit") {
                            select = $("#valueUnitSelect");
                        } else if (distinctField === "broker_name") {
                            select = $("#brokerSelect");
                        } else if (distinctField === "value_name") {
                            select = $("#valueNameSelect");
                        } else if (distinctField === "value_type") {
                            select = $("#valueTypeSelect");
                        } else if (distinctField === "model_name") {
                            select = $("#modelNameSelect");
                        }

                        for (var x = 0; x < data.table.length; x++)
                        {
                            if (x == 0)
                            {
                                select.children('option').remove().end();
                            }

                            var auxVar = data.table[x][Object.keys(data.table[x])[0]];

                            options = '<option value="' + auxVar + '">' + auxVar + '</option>';
                            select.append(options);

                            var selectedFlag = globalSqlFilter[n].allSelected || globalSqlFilter[n].selectedVals.includes(auxVar);
                            dataNew[x] = {label: auxVar, value: auxVar, selected: selectedFlag};
                        }

                        if((n === 6)&&updateIconsFlag)
                        {
                            updateIcons(dataNew);
                        }

                        select.multiselect('dataprovider', dataNew);
                    },
                    error: function (errorData) {
                        console.log("Errore in Populate Select Menu Wizard: ");
                        console.log(JSON.stringify(errorData));
                    }
                });
            }
        }


        function updateIcons(data)
        {
            let hide_all_icons = false;
            $(".multiselect-container.dropdown-menu").each(function(){

                if(
                    $(this).find("input[type=checkbox]").length > 0 &&
                    $(this).find("input[type=checkbox]:checked").length <= 0){
                    hide_all_icons = true;
                }

            });

            $('.addWidgetWizardIconClickClass').each(function () {

                if( hide_all_icons == true){
                    $(this).hide();
                    return true;
                }

                var snap4citytype = $(this).attr('data-snap4citytype');
                var snap4citytypeArray = snap4citytype.split(',');

                for (k = 0; k < snap4citytypeArray.length; k++) {
                    snap4citytypeArray[k] = snap4citytypeArray[k].trim();
                }

                var found = false;

                for (j = 0; j < snap4citytypeArray.length; j++) {

                    for (i = 0; i < data.length; i++)
                    {
                        if (data[i].selected === true)
                        {
                            if (data[i].value !== snap4citytypeArray[j])
                            {
                                // $(this).hide();

                            } else
                            {
                                found = true;
                                //  $(this).show();
                            }
                        } else
                        {
                            //Da verificare
                            //  $(this).hide();
                        }
                    }
                }

                if (found == true) {
                    $(this).show();
                } else {
                    $(this).hide();
                }

            });
        }
        ;

        //Handler del bottone di reset dei filtri
        function resetFilter()
        {
            if(FreezeMap) {
                $('#togBtn').click();
            }
            noPOIFlag = -1;
            poiSubNature = "";
            poiNature = "";
            widgetWizardSelectedSingleRow = null;
            widgetWizardSelectedRows = {};
            choosenWidgetIconName = null;
            var valuename_filter_value= widgetWizardTable.column(4).search("").draw();
            var valuename_filter_value2= widgetWizardTable.column(15).search("").draw();
            $('.addWidgetWizardIconClickClass[data-selected=true]').attr('data-selected', false);
            widgetWizardSelectedRowsTable.clear().draw(false);
            validityConditions.widgetTypeSelected = false;
            validityConditions.brokerAndNrRowsTogether = true;
            validityConditions.atLeastOneRowSelected = false;
            validityConditions.actuatorFieldsEmpty = true;
            validityConditions.canProceed = false;
            checkTab1Conditions();
            countSelectedRows();
            $('#actuatorEntityNameCell .wizardActInputCnt').val('');
            $('#actuatorValueTypeCell .wizardActInputCnt').val('');
            $('#actuatorMinBaseValueCell .wizardActInputCnt').val('');
            $('#actuatorMaxBaseValueCell .wizardActInputCnt').val('');
            
            $('#widgetWizardTable_filter input[type="search"]').val('');
            
            widgetWizardTable.search('').draw();
            widgetWizardSelectedRowsTable.search('').draw();
            
            validityConditions = {
                dashTemplateSelected: true,
                dashboardTitleOk: false,
                widgetTypeSelected: false,
                brokerAndNrRowsTogether: true,
                atLeastOneRowSelected: false,
                actuatorFieldsEmpty: true
            };

            for(var layerKey in gisLayersOnMap)
            {
                addWidgetWizardMapRef.removeLayer(gisLayersOnMap[layerKey]);
            }
            clearAllMarkers();
            deselectAllIcons();
            
            var selectedValsHighLevelType = [];
            var allSelectedHighLevelType = true;
            var searchValueHighLevelType = "";
            
            var selectedValsNature = [];
            var allSelectedNature = true;
            var searchValueNature = "";
            
            var selectedValsSubnature = [];
            var allSelectedSubnature = true;
            var searchValueSubnature = "";
            
            var selectedValsLowLevelType = [];
            var allSelectedLowLevelType = true;
            var searchValueLowLevelType = "";
            
            var selectedValsUnit = [];
            var allSelectedUnit = true;
            var searchValueUnit = "";
            
            var selectedValsHealth = [];
            var allSelectedHealth = true;
            var searchValueHealth = "";
            
            var selectedValsOwnership = [];
            var allSelectedOwnership = true;
            var searchValueOwnership = "";

            var selectedValsValueUnit = [];
            var allSelectedValueUnit = true;
            var searchValueValueUnit = "";

            var selectedValsModel = [];
            var allSelectedModel = true;
            var searchValueModel = "";

            var selectedValsBroker = [];
            var allSelectedBroker = true;
            var searchValueBroker = "";

            var selectedValsValueName = [];
            var allSelectedValueName = true;
            var searchValueValueName = "";

            var selectedValsValueType = [];
            var allSelectedValueType = true;
            var searchValueValueType = "";

            var selectedValsModelName = [];
            var allSelectedModelName = true;
            var searchValueModelName = "";
            
            //Questo if distingue il caso in cui stiamo agendo sui template di dashboard
            if(!location.href.includes("dashboard_configdash.php")&&!location.href.includes("inspectorOS.php"))
            {
                //Gestione del preset high level type da template dashboard
                if($('.modalAddDashboardWizardChoiceCnt[data-selected="true"]').attr('data-highlevelsel') !== 'any')
                {
                    selectedValsHighLevelType = $('.modalAddDashboardWizardChoiceCnt[data-selected="true"]').attr('data-highlevelsel').split('|');
                    allSelectedHighLevelType = false;
                    searchValueHighLevelType = selectedValsHighLevelType.join('|');
                }
                
                //Gestione del preset nature da template dashboard
                if($('.modalAddDashboardWizardChoiceCnt[data-selected="true"]').attr('data-naturesel') !== 'any')
                {
                    selectedValsNature = $('.modalAddDashboardWizardChoiceCnt[data-selected="true"]').attr('data-naturesel').split('|');
                    allSelectedNature = false;
                    searchValueNature = selectedValsNature.join('|');
                }
                
                //Gestione del preset subnature da template dashboard
                if($('.modalAddDashboardWizardChoiceCnt[data-selected="true"]').attr('data-subnaturesel') !== 'any')
                {
                    selectedValsSubnature = $('.modalAddDashboardWizardChoiceCnt[data-selected="true"]').attr('data-subnaturesel').split('|');
                    allSelectedSubnature = false;
                    searchValueSubnature = selectedValsSubnature.join('|');
                }
                
                //Gestione del preset low level type da template dashboard
                if($('.modalAddDashboardWizardChoiceCnt[data-selected="true"]').attr('data-valuetypesel') !== 'any')
                {
                    selectedValsLowLevelType = $('.modalAddDashboardWizardChoiceCnt[data-selected="true"]').attr('data-valuetypesel').split('|');
                    allSelectedLowLevelType = false;
                    searchValueLowLevelType = selectedValsLowLevelType.join('|');
                }
                
                //Gestione del preset unit da template dashboard
                if($('.modalAddDashboardWizardChoiceCnt[data-selected="true"]').attr('data-datatypesel') !== 'any')
                {
                    selectedValsUnit = $('.modalAddDashboardWizardChoiceCnt[data-selected="true"]').attr('data-datatypesel').split('|');
                    allSelectedUnit = false;
                    searchValueUnit = selectedValsUnit.join('|');
                }
                
                //Gestione del preset healthiness da template dashboard
                if($('.modalAddDashboardWizardChoiceCnt[data-selected="true"]').attr('data-healthinesssel') !== 'any')
                {
                    selectedValsHealth = $('.modalAddDashboardWizardChoiceCnt[data-selected="true"]').attr('data-healthinesssel').split('|');
                    allSelectedHealth = false;
                    searchValueHealth = selectedValsHealth.join('|');
                }
                
                //Gestione del preset ownership da template dashboard
                if($('.modalAddDashboardWizardChoiceCnt[data-selected="true"]').attr('data-ownershipsel') !== 'any')
                {
                    selectedValsOwnership = $('.modalAddDashboardWizardChoiceCnt[data-selected="true"]').attr('data-ownershipsel').split('|');
                    allSelectedOwnership = false;
                    searchValueOwnership = selectedValsOwnership.join('|');
                }

                //Gestione del preset valueUnit da template dashboard
                if($('.modalAddDashboardWizardChoiceCnt[data-selected="true"]').attr('data-valueUnitsel') !== 'any')
                {
                    selectedValsValueUnit = $('.modalAddDashboardWizardChoiceCnt[data-selected="true"]').attr('data-valueUnitsel').split('|');
                    allSelectedValueUnit = false;
                    searchValueValueUnit = selectedValsValueUnit.join('|');
                }

            /*    if($('.modalAddDashboardWizardChoiceCnt[data-selected="true"]').attr('data-modelsel') !== 'any')
                {
                    selectedValsModel = $('.modalAddDashboardWizardChoiceCnt[data-selected="true"]').attr('data-modelsel').split('|');
                    allSelectedModel = false;
                    searchValueModel = selectedValsModel.join('|');
                }

                if($('.modalAddDashboardWizardChoiceCnt[data-selected="true"]').attr('data-brokersel') !== 'any')
                {
                    selectedValsBroker = $('.modalAddDashboardWizardChoiceCnt[data-selected="true"]').attr('data-brokersel').split('|');
                    allSelectedBroker = false;
                    searchValueBroker = selectedValsBroker.join('|');
                }

                if($('.modalAddDashboardWizardChoiceCnt[data-selected="true"]').attr('data-valuenameNsel') !== 'any')
                {
                    selectedValsValueName = $('.modalAddDashboardWizardChoiceCnt[data-selected="true"]').attr('data-valuenameNsel').split('|');
                    allSelectedValueName = false;
                    searchValueValueName = selectedValsValueName.join('|');
                }

                if($('.modalAddDashboardWizardChoiceCnt[data-selected="true"]').attr('data-valuetypeNsel') !== 'any')
                {
                    selectedValsValueType = $('.modalAddDashboardWizardChoiceCnt[data-selected="true"]').attr('data-valuetypeNsel').split('|');
                    allSelectedValueType = false;
                    searchValueValueType = selectedValsValueType.join('|');
                }
                        */
            }
            
            globalSqlFilter = [
                {
                    "field": "high_level_type",
                    "value": searchValueHighLevelType,
                    "active": "false",
                    "selectedVals": selectedValsHighLevelType,
                    "allSelected": allSelectedHighLevelType
                },
                {
                    "field": "nature",
                    "value": searchValueNature,
                    "active": "false",
                    "selectedVals": selectedValsNature,
                    "allSelected": allSelectedNature
                },
                {
                    "field": "sub_nature",
                    "value": searchValueSubnature,
                    "active": "false",
                    "selectedVals": selectedValsSubnature,
                    "allSelected": allSelectedSubnature
                },
                {
                    "field": "low_level_type",
                    "value": searchValueLowLevelType,
                    "active": "false",
                    "selectedVals": selectedValsLowLevelType,
                    "allSelected": allSelectedLowLevelType
                },
                {
                    "field": "unique_name_id",
                    "value": "",
                    "active": "false",
                    "selectedVals": [],
                    "allSelected": true
                },
                {
                    "field": "instance_uri",
                    "value": "",
                    "active": "false",
                    "selectedVals": [],
                    "allSelected": true
                },
                {
                    "field": "unit",
                    "value": searchValueUnit,
                    "active": "false",
                    "selectedVals": selectedValsUnit,
                    "allSelected": allSelectedUnit
                },
                {
                    "field": "healthiness",
                    "value": searchValueHealth,
                    "active": "false",
                    "selectedVals": selectedValsHealth,
                    "allSelected": allSelectedHealth
                },
                {
                    "field": "ownership",
                    "value": searchValueOwnership,
                    "active": "false",
                    "selectedVals": selectedValsOwnership,
                    "allSelected": allSelectedOwnership
                },
                {
                    "field": "value_unit",
                    "value": searchValueValueUnit,
                    "active": "false",
                    "selectedVals": selectedValsValueUnit,
                    "allSelected": allSelectedValueUnit
                },
                {
                    "field": "broker_name",
                    "value": searchValueBroker,
                    "active": "false",
                    "selectedVals": selectedValsBroker,
                    "allSelected": allSelectedBroker
                },
                {
                    "field": "value_name",
                    "value": searchValueValueName,
                    "active": "false",
                    "selectedVals": selectedValsValueName,
                    "allSelected": allSelectedValueName
                },
                {
                    "field": "value_type",
                    "value": searchValueValueType,
                    "active": "false",
                    "selectedVals": selectedValsValueType,
                    "allSelected": allSelectedValueType
                },
                {
                    "field": "model_name",
                    "value": searchValueModelName,
                    "active": "false",
                    "selectedVals": selectedValsModelName,
                    "allSelected": allSelectedModelName
                }
            ];
            
            for(n = 0; n < 23; n++)
            {
                switch(n)
                {
                    case 0:
                        widgetWizardTable.column(0).search(searchValueHighLevelType, false, false);
                        break;
                        
                    case 1:
                        widgetWizardTable.column(n).search(searchValueNature, true, false); 
                        break;
                        
                    case 2:
                        widgetWizardTable.column(n).search(searchValueSubnature, true, false); 
                        break;    
                        
                    case 3:
                        widgetWizardTable.column(n).search(searchValueLowLevelType, true, false); 
                        break;

                    case 7:
                        widgetWizardTable.column(n).search(searchValueModelName, true, false);
                        break;

                    case 8:
                        widgetWizardTable.column(n).search(searchValueBroker, true, false);
                        break;

                    case 9:
                        widgetWizardTable.column(n).search(searchValueValueName, true, false);
                        break;

                    case 10:
                        widgetWizardTable.column(n).search(searchValueValueType, true, false);
                        break;

                    case 11:
                        widgetWizardTable.column(n).search(searchValueUnit, true, false);
                        break;

                    case 15:
                        widgetWizardTable.column(n).search(searchValueHealth, true, false);
                        break;

                    case 12:
                        widgetWizardTable.column(n).search(searchValueValueUnit, true, false);
                        break;

                    case 21:
                        widgetWizardTable.column(n).search(searchValueOwnership, true, false);
                        break;
                        
                    default://Ci cadono anche 4 e 5
                        break;
                }
            }

            $(".input_search_bar").val("");
            custom_search_per_column = [];
            
            widgetWizardTable.draw();

            for(var n = 0; n < globalSqlFilter.length; n++)
            {
                if (n !== 4 && n != 5) 
                {
                    populateSelectMenus("", "", null, "", n, false, true, FreezeMap, bounds);
                }
            }
            
            //Rimozione avviso righe incompatibili
            $('#wizardNotCompatibleRowsAlert').hide();
        }//Fine funzione reset filter
        
        function resetFilterForced()
        {
            widgetWizardSelectedRows = {};
            choosenWidgetIconName = null;
            $('.addWidgetWizardIconClickClass[data-selected=true]').attr('data-selected', false);
            widgetWizardSelectedRowsTable.clear().draw(false);
            validityConditions.widgetTypeSelected = false;
            validityConditions.brokerAndNrRowsTogether = true;
            validityConditions.atLeastOneRowSelected = false;
            validityConditions.actuatorFieldsEmpty = true;
            validityConditions.canProceed = false;
            checkTab1Conditions();
            countSelectedRows();
            $('#actuatorEntityNameCell .wizardActInputCnt').val('');
            $('#actuatorValueTypeCell .wizardActInputCnt').val('');
            $('#actuatorMinBaseValueCell .wizardActInputCnt').val('');
            $('#actuatorMaxBaseValueCell .wizardActInputCnt').val('');
            
            widgetWizardTable.search('').draw();
            widgetWizardSelectedRowsTable.search('').draw();
            
            validityConditions = {
                dashTemplateSelected: true,
                dashboardTitleOk: false,
                widgetTypeSelected: false,
                brokerAndNrRowsTogether: true,
                atLeastOneRowSelected: false,
                actuatorFieldsEmpty: true
            };

            for(var layerKey in gisLayersOnMap)
            {
                addWidgetWizardMapRef.removeLayer(gisLayersOnMap[layerKey]);
            }
            clearAllMarkers();
            deselectAllIcons();
            
            var selectedValsHighLevelType = [];
            var allSelectedHighLevelType = true;
            var searchValueHighLevelType = "";
            
            globalSqlFilter = [
                {
                    "field": "high_level_type",
                    "value": searchValueHighLevelType,
                    "active": "false",
                    "selectedVals": selectedValsHighLevelType,
                    "allSelected": allSelectedHighLevelType
                },
                {
                    "field": "nature",
                    "value": "",
                    "active": "false",
                    "selectedVals": [],
                    "allSelected": true
                },
                {
                    "field": "sub_nature",
                    "value": "",
                    "active": "false",
                    "selectedVals": [],
                    "allSelected": true
                },
                {
                    "field": "low_level_type",
                    "value": "",
                    "active": "false",
                    "selectedVals": [],
                    "allSelected": true
                },
                {
                    "field": "unique_name_id",
                    "value": "",
                    "active": "false",
                    "selectedVals": [],
                    "allSelected": true
                },
                {
                    "field": "instance_uri",
                    "value": "",
                    "active": "false",
                    "selectedVals": [],
                    "allSelected": true
                },
                {
                    "field": "unit",
                    "value": "",
                    "active": "false",
                    "selectedVals": [],
                    "allSelected": true
                },
                {
                    "field": "healthiness",
                    "value": "",
                    "active": "false",
                    "selectedVals": [],
                    "allSelected": true
                },
                {
                    "field": "ownership",
                    "value": "",
                    "active": "false",
                    "selectedVals": [],
                    "allSelected": true
                },
                {
                    "field": "value_unit",
                    "value": "",
                    "active": "false",
                    "selectedVals": [],
                    "allSelected": true
                },
                {
                    "field": "broker_name",
                    "value": "",
                    "active": "false",
                    "selectedVals": [],
                    "allSelected": true
                },
                {
                    "field": "value_name",
                    "value": "",
                    "active": "false",
                    "selectedVals": [],
                    "allSelected": true
                },
                {
                    "field": "value_type",
                    "value": "",
                    "active": "false",
                    "selectedVals": [],
                    "allSelected": true
                },
                {
                    "field": "model_name",
                    "value": "",
                    "active": "false",
                    "selectedVals": [],
                    "allSelected": true
                }
            ];
            
            selectedValsHighLevelType = selectedValsHighLevelType.join('|');
            
            for (n = 0; n < 23; n++)
            {
                if((n != 4)&&(n != 5)) 
                {
                    widgetWizardTable.column(n).search("", true, false);
                }
            }
            
            widgetWizardTable.draw();

            for(var n = 0; n < globalSqlFilter.length; n++)
            {
                if (n !== 4 && n != 5)
                {
                    populateSelectMenus("", "", null, "", n, false, true, FreezeMap, bounds);
                }
            }
            
            //Rimozione avviso righe incompatibili
            $('#wizardNotCompatibleRowsAlert').hide();
        }//Fine funzione reset filter

        widgetWizardPageLength = 8;

        //Creazione tabella GUI righe selezionate
        widgetWizardSelectedRowsTable = $('#widgetWizardSelectedRowsTable').DataTable({
            "bLengthChange": false,
            "bInfo": false,
            "paging": true,
            "language": {search: ""},
            "pageLength": 8,
            aaSorting: [[0, 'asc']],
			"ajax": {
                async: true, 
                url: "../controllers/dashboardWizardControllerOS.php?initSynVarPresel=true&northEastPointLat=true",
                data: {
                    dashUsername: "<?= $_SESSION['loggedUsername'] ?>",
                    dashUserRole: "<?= $_SESSION['loggedRole'] ?>",
                    organization: "<?= $_SESSION['loggedOrganization'] ?>",
		            northEastPointLat: "<?= $_SESSION['northEastPointLat'] ?>",
                    poiFlag: getPOIFlag(),
					synMode: "<?=$synMode?$synMode:0?>"
                }
            },
            "createdRow": function (row, data, index) {
                if (data[15] != null) {
                    $(row).attr('data-rowId', data[15]);
                } else {
                    $(row).attr('data-rowId', data[17]);
                }
                if (data[18] != null) {
                    $(row).attr('data-widgetCompatible', data[18]);
                } else {
                    $(row).attr('data-widgetCompatible', data[18]);
                }

                <?php if($synMode):?>
                    if(typeof data[18] !== "undefined" && data[18] == true){
                        widgetWizardSelectedRows['row' + data[15]] = {unit:data[9]};
                        console.log(widgetWizardSelectedRows['row' + data[15]].unit);
                    }
                <?php endif; ?>
                //widgetWizardSelectedRows['row' + data[15]] = data[17];

                $(row).find('.widgetWizardSelectedRowsDelBtn').click(function ()
                {
                    var delesectedUnit = widgetWizardSelectedRows['row' + $(this).parents('tr').attr('data-rowid')].unit;
               //     gisLayersOnMap[widgetWizardSelectedRows['row' + $(this).parents('tr').attr('data-rowid')].servicetype].clearLayers();
                    if (widgetWizardSelectedRows['row' + $(this).parents('tr').attr('data-rowid')].instance_uri == "any") {
                      //  gisLayersOnMap[widgetWizardSelectedRows['row' + $(this).parents('tr').attr('data-rowid')].serviceType].clearLayers();
                        addWidgetWizardMapRef.removeLayer(gisLayersOnMap[widgetWizardSelectedRows['row' + $(this).parents('tr').attr('data-rowid')].servicetype]);
                    } else if ((widgetWizardSelectedRows['row' + $(this).parents('tr').attr('data-rowid')].instance_uri == "MyPOI") && (widgetWizardSelectedRows['row' + $(this).parents('tr').attr('data-rowid')].sub_nature == "Any")){
                        gisLayersOnMap[widgetWizardSelectedRows['row' + $(this).parents('tr').attr('data-rowid')].servicetype].clearLayers();
                    }
                    else {
                        clearMarker($(this).parents('tr').attr('data-rowid'));
                    }
                  //  addWidgetWizardMapRef.removeLayer(gisLayersOnMap[widgetWizardSelectedRows['row' + $(this).parents('tr').attr('data-rowid')].servicetype]);
                    delete widgetWizardSelectedRows['row' + $(this).parents('tr').attr('data-rowid')];
                    
                    widgetWizardSelectedRowsTable.row('[data-rowid=' + $(this).parents('tr').attr('data-rowid') + ']').remove().draw(false);
                    $('#widgetWizardTable tbody tr[data-rowid=' + $(this).parents('tr').attr('data-rowid') + ']').removeClass('selected');
                    $('#widgetWizardTable tbody tr[data-rowid=' + $(this).parents('tr').attr('data-rowid') + ']').attr('data-selected', 'false');

                    checkAtLeastOneRowSelected();
                    checkBrokerAndNrRowsTogether();
                    checkTab1Conditions();
                    countSelectedRows();
                    if (
                        ($('.addWidgetWizardIconClickClass[data-selected="true"]').attr("data-mainwidget") == "widgetBarSeries" || 
                         $('.addWidgetWizardIconClickClass[data-selected="true"]').attr("data-mainwidget") == "widgetCurvedLineSeries" || 
                         $('.addWidgetWizardIconClickClass[data-selected="true"]').attr("data-mainwidget") == "widgetPieChart") &&
                          ObjectSize(widgetWizardSelectedRows) > 1) {
                        // if ((wizardSelectedRowsObj[Object.keys(wizardSelectedRowsObj)[Object.keys(wizardSelectedRowsObj).length -1]].value_unit == wizardSelectedRowsObj[Object.keys(wizardSelectedRowsObj)[0]].value_unit)) {
                        // OK
                        //   } else {
                        // custom check if widgetBarSeries: check if current selected rows has the same value_unit of first selected row. If not, mark current selected row as incompatible
                        updateBarSeriesWidgetSelectedRows(widgetWizardSelectedRows, Object.keys(widgetWizardSelectedRows)[0]);
                        //   }
                    } else {
                        updateWidgetCompatibleRows();
                    }
                    
                    updateSelectedUnits('remove', delesectedUnit);
                    
                    updateIconsFromSelectedRows();
                });
            },
            "columnDefs": [
				{
                    "targets": 15,
                    "searchable": false,
                    "render": function (data, type, row, meta) {
                        return '<i class="fa fa-close widgetWizardSelectedRowsDelBtn"></i>';
                    }
                }
                <?php if($synMode):?>
                    ,
                    {
                        "targets": 12,
                        "searchable": true,
                        "render": function (data, type, row, meta) {

                        //    if(typeof row[12] === "undefined" || row[12] != 'false' || row[12] != 'true'){
                        //        return data;
                        //    }
                            var imageUrl = null;
                            if (row[12]) {
                                if (row[12] === 'true') {
                                    imageUrl = "<i class='fa fa-circle' style='font-size:16px;color:#33cc33'></i>";
                                } else {
                                    imageUrl = "<i class='fa fa-circle' style='font-size:16px;color:#ff3300'></i>";
                                }
                            } else {
                                imageUrl = "<i class='fa fa-circle' style='font-size:16px;color:#ff3300'></i>";
                            }
                            return imageUrl;
                        }
                    }
                <?php endif; ?>
            ] ,
            fnDrawCallback: function(){
                $('#widgetWizardSelectedRowsTable td').each(function(){
                    $(this).attr("title",$(this).text());
                });
                $("#widgetWizardSelectedRowsTable td").each(function(){
                    $('body>.tooltip').remove();
                    $(this).tooltip({container:'body'});
                    if($(this).find("span").length > 0){
                        $(this).attr("data-title",$(this).find("span").text());
                        $(this).attr("data-original-title",$(this).find("span").text());

                    }
                });
                
            },
            initComplete: function () {

            },
        });

        function getPOIFlag () {
            return noPOIFlag;
        }

        function getPOINature () {
            return poiNature;
        }

        function getPOISubNature () {
            return poiSubNature;
        }

        function buildFilterSearchArray (searchString) {
            if (searchString.includes("|")) {
                return searchString.split("|");
            } else {
                return searchString;
            }
        }


        var using_search_bar = false;
        var custom_search_per_column = [];

        //Creazione tabella GUI del wizard
        widgetWizardTable = $('#widgetWizardTable').DataTable({
            "bLengthChange": false,
            "bInfo": false,
            "language": {search: ""},
            aaSorting: [[0, 'asc']],
            "processing": true,
            "serverSide": true,
            
            "pageLength": widgetWizardPageLength,
            "ajax": {
                async: true, 
                //"type": "POST",
                url: "../controllers/dashboardWizardControllerOS.php?initWidgetWizard=true&northEastPointLat=true",
                data: function ( d ) {
                    d.dashUsername = "<?= $_SESSION['loggedUsername'] ?>";
                    d.dashUserRole = "<?= $_SESSION['loggedRole'] ?>";
                    d.organization = "<?= $_SESSION['loggedOrganization'] ?>";
		            d.northEastPointLat = "<?= $_SESSION['northEastPointLat'] ?>";
                    d.poiFlag = getPOIFlag();
					d.synMode = "<?=$synMode?$synMode:0?>";
                    d.search_bar = using_search_bar;
                    using_search_bar = false;
                    d.custom_search_per_column = custom_search_per_column;
                    
                    //d.globalSqlFilter2 = globalSqlFilter;
                }
             /*   data: function(d){
                    d.dashUsername = "<?= $_SESSION['loggedUsername'] ?>",
                    d.dashUserRole = "<?= $_SESSION['loggedRole'] ?>",
                    d.organization = "<?= $_SESSION['loggedOrganization'] ?>",
                    d.northEastPointLatt = "<?= $_SESSION['northEastPointLat'] ?>",
                    d.poiFlag = getPOIFlag()
                }   */
            },
            'createdRow': function (row, data, dataIndex) {
                $(row).attr('data-rowId', data[18]);
                $(row).attr('data-high_level_type', data[0]);
                $(row).attr('data-nature', data[1]);
                $(row).attr('data-sub_nature', data[2]);
                $(row).attr('data-low_level_type', data[3]);
                    $(row).attr('data-unique_name_id', data[4]);
                $(row).attr('data-instance_uri', data[5]);
                $(row).attr('data-value_name',data[25]);
                $(row).attr('data-value_type',data[26]);
                $(row).attr('data-device_model_name',data[27]);
                $(row).attr('data-broker_name',data[28]);
                $(row).attr('data-unit', data[11]);
                $(row).attr('data-servicetype', data[2]);
                $(row).attr('data-get_instances', data[20]);
                $(row).attr('data-sm_based', data[25]);
                $(row).attr('data-parameters', data[17]);
                $(row).attr('data-selected', 'false');
                $(row).attr('data-last_value', data[14]);
                $(row).attr('data-latitude', data[23]);
                $(row).attr('data-longitude', data[24]);
                $(row).attr('data-organizations', data[22]);
                $(row).attr('last_date',data[13]);
                $(row).attr('data-valueUnit',data[12]);
                $(row).attr('ownership',data[21]);
				if(data.length > 19) $(row).attr('data-last_check',data[19]);
				if(data.length > 15) $(row).attr('data-healthiness',data[15]);
            },
            "columnDefs": [
                        
                {
                    "targets": 0,
                    "searchable": true,
                    "render": function (data, type, row, meta) {
                        return '<button style="margin-right:2px;" class="dt">DT</button>'+'<span>'+row[0]+'</span>';
                    }
                },
                {
                    "targets": 15,
                    "searchable": true,
                    "render": function (data, type, row, meta) {

                        var imageUrl = null;
                        if (row[15]) {
                            if (row[15] === 'true') {
                                imageUrl = "<i class='fa fa-circle' style='font-size:16px;color:#33cc33'></i>";
                            } else {
                                imageUrl = "<i class='fa fa-circle' style='font-size:16px;color:#ff3300'></i>";
                            }
                        } else {
                            imageUrl = "<i class='fa fa-circle' style='font-size:16px;color:#ff3300'></i>";
                        }
                        return imageUrl;
                    }
                },
                {
                    "targets": 16,
                    "searchable": true,
                    "visible": false
                },
                {
                //    "targets": [5, 12, 13, 15, 17, 18, 19, 20, 21, 22, 23],
                    "targets": invisibleCols,
                    "visible": false
                }
            ],
            fnDrawCallback: function(){
                $('#widgetWizardTable td').each(function(){
                    $(this).attr("title",$(this).text());
                });

                $("#widgetWizardTable td").each(function(){
                    $('body>.tooltip').remove();
                    $(this).tooltip({container:'body'});
                    if($(this).find("span").length > 0){
                        $(this).attr("data-title",$(this).find("span").text());
                        $(this).attr("data-original-title",$(this).find("span").text());

                    }
                });
            },
            initComplete: function () {

                $("#widgetWizardTable_wrapper").css("overflow-x", "auto");
                $("#widgetWizardTable_wrapper").css("overflow-y", "hidden");
                // HIGH-LEVEL TYPE COLUMN
                this.api().columns([0]).every(function () {
                    var select = $('<select id="highLevelTypeSelect" style="color: black;" multiple="multiple"></select>')
                            .appendTo($("#highLevelTypeColumnFilter"))
                            .on('change', function () 
                            {
                                /*widgetWizardSelectedRows = {};
                                widgetWizardSelectedRowsTable.clear().draw(false);
                                validityConditions.atLeastOneRowSelected = false;
                                checkTab1Conditions();
                                countSelectedRows();*/

                                var search = [];
                                $.each($('#highLevelTypeSelect option:selected'), function () {
                                    if ($(this).val() != null && $(this).val() != "null") {
                                        search.push($(this).val());
                                    }
                                });
                                var nOptions = 0;
                                $.each($('#highLevelTypeSelect option'), function () {
                                    nOptions++;
                                });
                                
                                globalSqlFilter[0].allSelected = (search.length == nOptions && nOptions == highLevelTypeSelectStartOptions);
                                if(search.length == nOptions && nOptions == highLevelTypeSelectStartOptions)
                                    search = [];

                                globalSqlFilter[0].selectedVals = search;

                                if (search != "") {
                                    search = search.join('|');
                                }

                                globalSqlFilter[0].value = search;
                                if (search == '' && !globalSqlFilter[0].allSelected) {
                                    search = 'oiunqauhalknsufhvnoqwpnvfv';
                                }

                                // MODIFICA per ORG BUTTON
                                var hltSelectedFilter = buildFilterSearchArray(globalSqlFilter[0]['value']);
                            //    if (globalSqlFilter[0]['allSelected'] == false && globalSqlFilter[0]['value'] != 'POI') {
                                if (globalSqlFilter[0]['allSelected'] == false && (!hltSelectedFilter.includes('POI') && !hltSelectedFilter.includes('IoT Device Model')  && !hltSelectedFilter.includes('Mobile Device Model') && !hltSelectedFilter.includes('Data Table Model'))) {
                                    noPOIFlag = 1;
                                } else {
                                    noPOIFlag = hltSelectedFilter;
                                }
                                widgetWizardTable.ajax.reload();

                                widgetWizardTable.column(0).search(search, false, false, true).draw(noPOIFlag);
                                globalSqlFilter[0].value = search;

                                // Chiamata a funzione per popolare menù multi-select di filtraggio
                                for (var n = 0; n < globalSqlFilter.length; n++)
                                {
                                    if (n !== 4 && n != 5)
                                    {
                                        populateSelectMenus("high_level_type", search, select, "#highLevelTypeColumnFilter", n, false, true, FreezeMap, bounds);
                                    }
                                }
                                
                                checkTab1Conditions();
                                countSelectedRows();
                            });

                    highLevelTypeSelectStartOptions = 0;
                 /*   $.getJSON('../controllers/dashboardWizardControllerOS.php?filterDistinct=true',
                            {
                                filter: "high_level_type",
                                filterOrg: orgFilter
                            },
                            function (data) {
                                var options = '';
                                for (var x = 0; x < data.table.length; x++) {
                                    options = '<option value="' + data.table[x].high_level_type + '" selected="selected">' + data.table[x].high_level_type + '</option>';
                                    select.append(options);
                                    highLevelTypeSelectStartOptions++;
                                }

                                $('#highLevelTypeSelect').multiselect({
                                    includeSelectAllOption: true,
                                    maxHeight: 165,
                                    onChange: function () {
                                    }
                                }).multiselect('selectAll', true).multiselect('updateButtonText');

                            });*/

                    $.ajax({
                        url: '../controllers/dashboardWizardControllerOS.php?filterDistinct=true',
                        type: "GET",
                        async: true,
                        dataType: 'json',
                        data: {
                            filter: "high_level_type",
                            filterOrg: orgFilter,
							synMode: "<?=$synMode?$synMode:0?>"
                        },
                        success: function (data)
                        {
                            var options = '';
                            for (var x = 0; x < data.table.length; x++) {
                                options = '<option value="' + data.table[x].high_level_type + '" selected="selected">' + data.table[x].high_level_type + '</option>';
                                select.append(options);
                                highLevelTypeSelectStartOptions++;
                            }

                            $('#highLevelTypeSelect').multiselect({
                                includeSelectAllOption: true,
                                maxHeight: 165,
                                onChange: function () {
                                }
                            }).multiselect('selectAll', true).multiselect('updateButtonText');

                        },
                        error: function (data)
                        {
                            console.log("ERROR: " + JSON.stringify(data));
                        }
                    });

                });

                // NATURE COLUMN
                this.api().columns([1]).every(function () {       // CHANGE
                    var column = this;
                    var select = $('<select id="natureSelect" style="color: black;" multiple="multiple"></select>') 
                            .appendTo($("#natureColumnFilter"))
                            .on('change', function () {
                                /*widgetWizardSelectedRows = {};
                                widgetWizardSelectedRowsTable.clear().draw(false);
                                validityConditions.atLeastOneRowSelected = false;
                                checkTab1Conditions();
                                countSelectedRows();*/

                                var search = [];
                                $.each($('#natureSelect option:selected'), function () {   // CHANGE
                                    if ($(this).val() != null && $(this).val() != "null") {
                                        search.push($(this).val());
                                    }
                                });
                                var nOptions = 0;
                                $.each($('#natureSelect option'), function () {
                                    nOptions++;
                                });

                                globalSqlFilter[1].allSelected = (search.length == nOptions && nOptions == natureSelectStartOptions);
                                if (search.length == nOptions && nOptions == natureSelectStartOptions)
                                    search = [];
                                globalSqlFilter[1].selectedVals = search;
                                search = search.join('|');

                                if (search.charAt(0) == '|') { // CLEAR FIRST CHARACTER IF "|"
                                    search = search.substr(1);
                                }

                                globalSqlFilter[1].value = search;
                                if (search == '' && !globalSqlFilter[1].allSelected) {
                                    search = 'oiunqauhalknsufhvnoqwpnvfv';
                                }

                                var poiFlag = false;
                                var natureQuery = "";
                                var natureSelectedFilter = buildFilterSearchArray(globalSqlFilter[1]['value']);
                                if (Array.isArray(natureSelectedFilter)) {
                                    for (k=0; k < natureSelectedFilter.length; k++) {
                                        if (poiNatureArray.includes(natureSelectedFilter[k])) {
                                            poiFlag = true;
                                            if (natureQuery == "") {
                                                natureQuery = "'" + natureSelectedFilter[k] + "'";
                                            } else {
                                                natureQuery = natureQuery + " OR nature = '" + natureSelectedFilter[k] + "'";
                                            }
                                        }
                                    }
                                } else {
                                    if (poiNatureArray.includes(globalSqlFilter[1]['value'])) {
                                        poiFlag = true;
                                        natureQuery = "'" + natureSelectedFilter + "'";
                                    } else {
                                        poiFlag = false;
                                    }
                                }

                            //    if (globalSqlFilter[1]['allSelected'] == false && !poiNatureArray.includes(globalSqlFilter[1]['value'])) {
                                if (globalSqlFilter[1]['allSelected'] == false && poiFlag == false) {
                                    noPOIFlag = 1;
                                    poiNature = "";
                                } else {
                                //    noPOIFlag = -1;
                                //    poiNature = globalSqlFilter[1]['value'];
                                    noPOIFlag = globalSqlFilter[0]['value'];
                                    poiNature = natureQuery;
                                }
                                widgetWizardTable.ajax.reload();

                                widgetWizardTable.column(1).search(search, false, false).draw();
                                globalSqlFilter[1].value = search;

                                // Chiamata a funzione per popolare menù multi-select di filtraggio
                                for (var n = 0; n < globalSqlFilter.length; n++) {
                                    if (n !== 4 && n != 5) {
                                        populateSelectMenus("nature", search, select, "#natureColumnFilter", n, false, true, FreezeMap, bounds);
                                    }
                                }
                                
                                checkTab1Conditions();
                                countSelectedRows();
                            });

                    natureSelectStartOptions = 0;
                    $.getJSON('../controllers/dashboardWizardControllerOS.php?filterDistinct=true',
                            {
                                filter: "nature",     // CHANGE
                                filterOrg: orgFilter,
								synMode: "<?=$synMode?$synMode:0?>"
                            },
                            function (data) {
                                var options = '';
                                for (var x = 0; x < data.table.length; x++) {
                                    //   options += '<option value="' + data.table[x].nature + '">' + data.table[x].nature + '</option>';     // CHANGE

                                    options = '<option value="' + data.table[x].nature + '" selected="selected">' + data.table[x].nature + '</option>';
                                    //  $(option).appendTo(select);
                                    select.append(options);
                                    natureSelectStartOptions++;
                                }
                                $('#natureSelect').multiselect({
                                    maxHeight: 165,
                                    includeSelectAllOption: true
                                }).multiselect('selectAll', true).multiselect('updateButtonText');
                            });
                    //   });

                });

                // SUBNATURE COLUMN
                this.api().columns([2]).every(function () {       // CHANGE

                    var column = this;
                    var select = $('<select id="subnatureSelect" style="color: black;" multiple="multiple"></select>')
                            .appendTo($("#subnatureColumnFilter"))
                            .on('change', function () {
                                /*widgetWizardSelectedRows = {};
                                widgetWizardSelectedRowsTable.clear().draw(false);
                                validityConditions.atLeastOneRowSelected = false;
                                checkTab1Conditions();
                                countSelectedRows();*/

                                var search = [];
                                $.each($('#subnatureSelect option:selected'), function () {   // CHANGE
                                    if ($(this).val() != null && $(this).val() != "null") {
                                        search.push($(this).val());
                                    }
                                });
                                var nOptions = 0;
                                $.each($('#subnatureSelect option'), function () {
                                    nOptions++;
                                });

                                globalSqlFilter[2].allSelected = (search.length == nOptions && nOptions == subNatureSelectStartOptions);
                                if (search.length == nOptions && nOptions == subNatureSelectStartOptions)
                                    search = [];
                                globalSqlFilter[2].selectedVals = search;
                                search = search.join('|');

                                if (search.charAt(0) == '|') { // CLEAR FIRST CHARACTER IF "|"
                                    search = search.substr(1);
                                }

                                globalSqlFilter[2].value = search;
                                if (search == '' && !globalSqlFilter[2].allSelected) {
                                    search = 'oiunqauhalknsufhvnoqwpnvfv';
                                }
                                if (search.charAt(0) == '|') {
                                    search = search.substring(1);
                                }

                             /*   if (globalSqlFilter[2]['allSelected'] == false && !poiSubNatureArray.includes(globalSqlFilter[2]['value'])) {
                                    noPOIFlag = 1;
                                    poiSubNature = "";
                                } else {
                                    noPOIFlag = 0;
                                    poiSubNature = globalSqlFilter[2]['value'];
                                }
                                widgetWizardTable.ajax.reload();    */

                                var poiFlag = false;
                                var subnatureQuery = "";
                                var subnatureSelectedFilter = buildFilterSearchArray(globalSqlFilter[2]['value']);
                                if (Array.isArray(subnatureSelectedFilter)) {
                                    for (k=0; k < subnatureSelectedFilter.length; k++) {
                                        if (poiSubNatureArray.includes(subnatureSelectedFilter[k])) {
                                            poiFlag = true;
                                            if (subnatureQuery == "") {
                                                subnatureQuery = "'" + subnatureSelectedFilter[k] + "'";
                                            } else {
                                                subnatureQuery = subnatureQuery + " OR sub_nature = '" + subnatureSelectedFilter[k] + "'";
                                            }
                                        }
                                    }
                                } else {
                                    if (poiSubNatureArray.includes(globalSqlFilter[2]['value'])) {
                                        poiFlag = true;
                                        subnatureQuery = "'" + subnatureSelectedFilter + "'";
                                    } else {
                                        poiFlag = false;
                                    }
                                }

                                //    if (globalSqlFilter[1]['allSelected'] == false && !poiNatureArray.includes(globalSqlFilter[1]['value'])) {
                                if (globalSqlFilter[2]['allSelected'] == false && poiFlag == false) {
                                    noPOIFlag = 1;
                                    poiSubNature = "";
                                } else {
                                    // noPOIFlag = -1;
                                    //    poiNature = globalSqlFilter[1]['value'];
                                    noPOIFlag = globalSqlFilter[0]['value'];
                                    poiSubNature = subnatureQuery;
                                }
                                widgetWizardTable.ajax.reload();

                                widgetWizardTable.column(2).search(search, false, false).draw();     // CHANGE
                                globalSqlFilter[2].value = search;

                                // Chiamata a funzione per popolare menù multi-select di filtraggio
                                for (var n = 0; n < globalSqlFilter.length; n++) {
                                    if (n !== 4 && n != 5) {
                                        populateSelectMenus("sub_nature", search, select, "#subnatureColumnFilter", n, false, true, FreezeMap, bounds);
                                    }
                                }
                                
                                checkTab1Conditions();
                                countSelectedRows();

                            });

                    subNatureSelectStartOptions = 0;
                    $.getJSON('../controllers/dashboardWizardControllerOS.php?filterDistinct=true',
                            {
                                filter: "sub_nature",     // CHANGE
                                filterOrg: orgFilter,
								synMode: "<?=$synMode?$synMode:0?>"
                            },
                            function (data) {
                                var options = '';
                                for (var x = 0; x < data.table.length; x++) {
                                    //   options += '<option value="' + data.table[x].nature + '">' + data.table[x].nature + '</option>';     // CHANGE

                                    options = '<option value="' + data.table[x].sub_nature + '" selected="selected">' + data.table[x].sub_nature + '</option>';         // CHANGE
                                    //  $(option).appendTo(select);
                                    select.append(options);
                                    subNatureSelectStartOptions++;
                                }
                                $('#subnatureSelect').multiselect({
                                    maxHeight: 165,
                                    includeSelectAllOption: true
                                }).multiselect('selectAll', true).multiselect('updateButtonText');
                                ;
                            });
                    //   });

                });

                // LOW-LEVEL TYPE COLUMN
                this.api().columns([3]).every(function () {       // CHANGE

                    var column = this;
                    var select = $('<select id="lowLevelTypeSelect" style="color: black;" multiple="multiple"></select>')    // CHANGE
                            //   .appendTo( $(column.footer()).empty() )
                            .appendTo($("#lowLevelTypeColumnFilter"))
                            .on('change', function () {
                                /*widgetWizardSelectedRows = {};
                                widgetWizardSelectedRowsTable.clear().draw(false);
                                validityConditions.atLeastOneRowSelected = false;
                                checkTab1Conditions();
                                countSelectedRows();*/

                                var search = [];
                                $.each($('#lowLevelTypeSelect option:selected'), function () {   // CHANGE
                                    if ($(this).val() != null && $(this).val() != "null") {
                                        search.push($(this).val());
                                    }
                                });
                                var nOptions = 0;
                                $.each($('#lowLevelTypeSelect option'), function () {
                                    nOptions++;
                                });

                                globalSqlFilter[3].allSelected = (search.length == nOptions && nOptions == lowLevelTypeSelectStartOptions);
                                if (search.length == nOptions && nOptions == lowLevelTypeSelectStartOptions)
                                    search = [];
                                globalSqlFilter[3].selectedVals = search;
                                search = search.join('|');

                                if (search.charAt(0) == '|') { // CLEAR FIRST CHARACTER IF "|"
                                    search = search.substr(1);
                                }

                                globalSqlFilter[3].value = search;
                                if (search == '' && !globalSqlFilter[3].allSelected) {
                                    search = 'oiunqauhalknsufhvnoqwpnvfv';
                                }
                                widgetWizardTable.column(3).search(search, false, false).draw();     // CHANGE
                                globalSqlFilter[3].value = search;

                                // Chiamata a funzione per popolare menù multi-select di filtraggio
                                for (var n = 0; n < globalSqlFilter.length; n++) {
                                    if (n !== 4 && n != 5) {
                                        populateSelectMenus("low_level_type", search, select, "#lowLevelTypeColumnFilter", n, false, true, FreezeMap, bounds);
                                    }
                                }
                                
                                checkTab1Conditions();
                                countSelectedRows();

                            });

                    lowLevelTypeSelectStartOptions = 0;
                    $.getJSON('../controllers/dashboardWizardControllerOS.php?filterDistinct=true',
                            {
                                filter: "low_level_type",     // CHANGE
                                filterOrg: orgFilter,
								synMode: "<?=$synMode?$synMode:0?>"
                            },
                            function (data) {
                                var options = '';
                                for (var x = 0; x < data.table.length; x++) 
                                {
                                    options = '<option value="' + data.table[x].low_level_type + '" selected="selected">' + data.table[x].low_level_type + '</option>';         // CHANGE
                                    /*if((data.table[x].low_level_type !== 'actuatorcanceller')&&(data.table[x].low_level_type !== 'actuatordeleted')&&(data.table[x].low_level_type !== 'actuatordeletiondate')&&(data.table[x].low_level_type !== 'creationdate')&&(data.table[x].low_level_type !== 'entitycreator')&&(data.table[x].low_level_type !== 'entitydesc'))
                                    {
                                        select.append(options);
                                        lowLevelTypeSelectStartOptions++;
                                    }*/
                                    
                                    select.append(options);
                                    lowLevelTypeSelectStartOptions++;
                                }
                                $('#lowLevelTypeSelect').multiselect({
                                    maxHeight: 165,
                                    includeSelectAllOption: true
                                }).multiselect('selectAll', true).multiselect('updateButtonText');
                            });

                });

                // UNIT <-> DATA TYPE COLUMN
                this.api().columns([11]).every(function () {       // UNIT - DATA_TYPE

                    var column = this;
                    var select = $('<select id="unitSelect" style="color: black;" multiple="multiple"></select>')
                            //   .appendTo( $(column.footer()).empty() )
                            .appendTo($("#unitColumnFilter"))
                            .on('change', function () {
                                /*widgetWizardSelectedRows = {};
                                widgetWizardSelectedRowsTable.clear().draw(false);
                                validityConditions.atLeastOneRowSelected = false;
                                checkTab1Conditions();
                                countSelectedRows();*/

                                var search = [];
                                $.each($('#unitSelect option:selected'), function () {
                                    if ($(this).val() != null && $(this).val() != "null") {
                                        search.push($(this).val());
                                    }
                                });
                                var nOptions = 0;
                                $.each($('#unitSelect option'), function () {
                                    nOptions++;
                                });

                                globalSqlFilter[6].allSelected = (search.length == nOptions && nOptions == unitSelectStartOptions);
                                if (search.length == nOptions && nOptions == unitSelectStartOptions)
                                    search = [];
                                globalSqlFilter[6].selectedVals = search;
                                search = search.join('|');

                                if (search.charAt(0) == '|') {  // CLEAR FIRST CHARACTER IF "|"
                                    search = search.substr(1);
                                }

                                globalSqlFilter[6].value = search;
                                if (search == '' && !globalSqlFilter[6].allSelected) {
                                    search = 'oiunqauhalknsufhvnoqwpnvfv';
                                }

                           /*     // MODIFICA per ORG BUTTON
                                var dataTypeSelectedFilter = buildFilterSearchArray(globalSqlFilter[6]['value']);
                                //    if (globalSqlFilter[0]['allSelected'] == false && globalSqlFilter[0]['value'] != 'POI') {
                                if (globalSqlFilter[6]['allSelected'] == false && !dataTypeSelectedFilter.includes('map')) {
                                    noPOIFlag = 1;
                                } else {
                                    noPOIFlag = 0;
                                }
                                widgetWizardTable.ajax.reload();*/

                                widgetWizardTable.column(11).search(search, false, false).draw();
                                globalSqlFilter[6].value = search;

                                // Chiamata a funzione per popolare menù multi-select di filtraggio
                                for (var n = 0; n < globalSqlFilter.length; n++) {
                                    if (n !== 4 && n != 5) {
                                        populateSelectMenus("unit", search, select, "#unitColumnFilter", n, false, true, FreezeMap, bounds);
                                    }
                                }
                                
                                checkTab1Conditions();
                                countSelectedRows();
                            });

                    unitSelectStartOptions = 0;
                    $.getJSON('../controllers/dashboardWizardControllerOS.php?filterDistinct=true',
                            {
                                filter: "unit",
                                filterOrg: orgFilter,
                                ajax: 'true',
								synMode: "<?=$synMode?$synMode:0?>"
                            },
                            function (data) {
                                var options = '';
                                for (var x = 0; x < data.table.length; x++) {
                                    options = '<option value="' + data.table[x].unit + '" selected="selected">' + data.table[x].unit + '</option>';         // CHANGE
                                    select.append(options);
                                    unitSelectStartOptions++;
                                }
                                unitSelect = $('#unitSelect').multiselect({
                                    maxHeight: 165,
                                    includeSelectAllOption: true,
                                }).multiselect('selectAll', true).multiselect('updateButtonText');
                            });

                });

                // HEALTHINESS COLUMN
                this.api().columns([15]).every(function () {       // HEALTHINESS

                    var column = this;
                    var select = $('<select id="healthinessSelect" style="color: black;" multiple="multiple"></select>')
                            //   .appendTo( $(column.footer()).empty() )
                            .appendTo($("#healthinessColumnFilter"))
                            .on('change', function () {
                                
                                /*
                                    widgetWizardSelectedRows = {};
                                    widgetWizardSelectedRowsTable.clear().draw(false);
                                    validityConditions.atLeastOneRowSelected = false;
                                    checkTab1Conditions();
                                    countSelectedRows();
                                */

                                var search = [];
                                $.each($('#healthinessSelect option:selected'), function () {
                                    if ($(this).val() != null && $(this).val() != "null") {
                                        search.push($(this).val());
                                    }
                                });
                                var nOptions = 0;
                                $.each($('#healthinessSelect option'), function () {
                                    nOptions++;
                                });

                                globalSqlFilter[7].allSelected = (search.length == nOptions && nOptions == healthinessSelectStartOptions);
                                if (search.length == nOptions && nOptions == healthinessSelectStartOptions)
                                    search = [];
                                globalSqlFilter[7].selectedVals = search;
                                search = search.join('|');

                                if (search.charAt(0) == '|') {  // CLEAR FIRST CHARACTER IF "|"
                                    search = search.substr(1);
                                }

                                globalSqlFilter[7].value = search;
                                if (search == '' && !globalSqlFilter[7].allSelected) {
                                    search = 'oiunqauhalknsufhvnoqwpnvfv';
                                }

                            /*    // MODIFICA per ORG BUTTON
                                var healthinessSelectedFilter = buildFilterSearchArray(globalSqlFilter[7]['value']);
                                //    if (globalSqlFilter[0]['allSelected'] == false && globalSqlFilter[0]['value'] != 'POI') {
                                if (globalSqlFilter[7]['allSelected'] == false && !healthinessSelectedFilter.includes('true') && !healthinessSelectedFilter.includes('false')) {
                                    noPOIFlag = 1;
                                } else {
                                    noPOIFlag = 0;
                                }
                                widgetWizardTable.ajax.reload();*/


                                widgetWizardTable.column(15).search(search, false, false).draw();
                                globalSqlFilter[7].value = search;

                                // Chiamata a funzione per popolare menù multi-select di filtraggio
                                for (var n = 0; n < globalSqlFilter.length; n++) {
                                    if (n !== 4 && n != 5) {
                                        populateSelectMenus("healthiness", search, select, "#healthinessColumnFilter", n, false, true, FreezeMap, bounds);
                                    }
                                }
                                
                                checkTab1Conditions();
                                countSelectedRows();

                            });

                    healthinessSelectStartOptions = 0;
                    $.getJSON('../controllers/dashboardWizardControllerOS.php?filterDistinct=true',
                            {
                                filter: "healthiness",
                                filterOrg: orgFilter,
                                ajax: 'true',
								synMode: "<?=$synMode?$synMode:0?>"
                            },
                            function (data) {
                                var options = '';
                                var attrib = '';
                                for (var x = 0; x < data.table.length; x++) {
                                    if (data.table[x].healthiness === 'true') {
                                        attrib = 'healthy';
                                    } else {
                                        attrib = 'unhealthy';
                                    }
                                    options = '<option value="' + data.table[x].healthiness + '" selected="selected">' + data.table[x].healthiness + '</option>';         // CHANGE
                                    //    options = '<option value="' + attrib + '" selected="selected">' + attrib + '</option>';         // CHANGE
                                    select.append(options);
                                    healthinessSelectStartOptions++;
                                }
                                unitSelect = $('#healthinessSelect').multiselect({
                                    maxHeight: 165,
                                    includeSelectAllOption: true,
                                    // enableFiltering: true
                                }).multiselect('selectAll', true).multiselect('updateButtonText');
                            });

                });

                // OWNERSHIP COLUMN
                this.api().columns([21]).every(function ()
                {     
                    var select = $('<select id="ownershipSelect" style="color: black;" multiple="multiple"></select>')
                            .appendTo($("#ownershipColumnFilter"))
                            .on('change', function () {
                                /*widgetWizardSelectedRows = {};
                                widgetWizardSelectedRowsTable.clear().draw(false);
                                validityConditions.atLeastOneRowSelected = false;
                                checkTab1Conditions();
                                countSelectedRows();*/

                                var search = [];
                                $.each($('#ownershipSelect option:selected'), function () {
                                    if ($(this).val() != null && $(this).val() != "null") {
                                        search.push($(this).val());
                                    }
                                });
                                var nOptions = 0;
                                $.each($('#ownershipSelect option'), function () {
                                    nOptions++;
                                });

                                globalSqlFilter[8].allSelected = (search.length == nOptions && nOptions == ownershipSelectStartOptions);
                                if (search.length == nOptions && nOptions == ownershipSelectStartOptions)
                                    search = [];
                                globalSqlFilter[8].selectedVals = search;
                                search = search.join('|');

                                if (search.charAt(0) == '|') {  // CLEAR FIRST CHARACTER IF "|"
                                    search = search.substr(1);
                                }

                                globalSqlFilter[8].value = search;
                                if (search == '' && !globalSqlFilter[8].allSelected) {
                                    search = 'oiunqauhalknsufhvnoqwpnvfv';
                                }

                            /*    // MODIFICA per ORG BUTTON
                                var ownershipSelectedFilter = buildFilterSearchArray(globalSqlFilter[8]['value']);
                                //    if (globalSqlFilter[0]['allSelected'] == false && globalSqlFilter[0]['value'] != 'POI') {
                                if (globalSqlFilter[8]['allSelected'] == false && !ownershipSelectedFilter.includes('public') && !ownershipSelectedFilter.includes('private')) {
                                    noPOIFlag = 1;
                                } else {
                                    noPOIFlag = 0;
                                }
                                widgetWizardTable.ajax.reload();*/

                                widgetWizardTable.column(21).search(search, false, false).draw();
                                globalSqlFilter[8].value = search;

                                // Chiamata a funzione per popolare menù multi-select di filtraggio
                                for (var n = 0; n < globalSqlFilter.length; n++) {
                                    if (n !== 4 && n != 5) {
                                        populateSelectMenus("ownership", search, select, "#ownershipColumnFilter", n, false, true, FreezeMap, bounds);
                                    }
                                }
                                
                                checkTab1Conditions();
                                countSelectedRows();

                            });

                    ownershipSelectStartOptions = 0;
                    $.getJSON('../controllers/dashboardWizardControllerOS.php?filterDistinct=true',
                            {
                                filter: "ownership",
                                filterOrg: orgFilter,
                                ajax: 'true',
								synMode: "<?=$synMode?$synMode:0?>"
                            },
                            function (data) {
                                var options = '';
                                var attrib = '';
                                for (var x = 0; x < data.table.length; x++) {
                                    if (data.table[x].ownership === 'true') {
                                        attrib = 'healthy';
                                    } else {
                                        attrib = 'unhealthy';
                                    }
                                    options = '<option value="' + data.table[x].ownership + '" selected="selected">' + data.table[x].ownership + '</option>';         // CHANGE
                                    //    options = '<option value="' + attrib + '" selected="selected">' + attrib + '</option>';         // CHANGE
                                    select.append(options);
                                    ownershipSelectStartOptions++;
                                }
                                unitSelect = $('#ownershipSelect').multiselect({
                                    maxHeight: 165,
                                    includeSelectAllOption: true,
                                    // enableFiltering: true
                                }).multiselect('selectAll', true).multiselect('updateButtonText');
                            });

                });

                // VALUE_UNIT
                this.api().columns([12]).every(function ()
                {
                    var select = $('<select id="valueUnitSelect" style="color: black;" multiple="multiple"></select>')
                        .appendTo($("#valueUnitColumnFilter"))
                        .on('change', function () {
                            /*widgetWizardSelectedRows = {};
                            widgetWizardSelectedRowsTable.clear().draw(false);
                            validityConditions.atLeastOneRowSelected = false;
                            checkTab1Conditions();
                            countSelectedRows();*/

                            var search = [];
                            $.each($('#valueUnitSelect option:selected'), function () {
                                if ($(this).val() != null && $(this).val() != "null") {
                                    search.push($(this).val());
                                }
                            });
                            var nOptions = 0;
                            $.each($('#valueUnitSelect option'), function () {
                                nOptions++;
                            });

                            globalSqlFilter[9].allSelected = (search.length == nOptions && nOptions == valueUnitSelectStartOptions);
                            if (search.length == nOptions && nOptions == valueUnitSelectStartOptions)
                                search = [];
                            globalSqlFilter[9].selectedVals = search;
                            search = search.join('|');

                            if (search.charAt(0) == '|') {  // CLEAR FIRST CHARACTER IF "|"
                                search = search.substr(1);
                            }

                            globalSqlFilter[9].value = search;
                            if (search == '' && !globalSqlFilter[9].allSelected) {
                                search = 'oiunqauhalknsufhvnoqwpnvfv';
                            }
                            widgetWizardTable.column(12).search(search, false, false).draw();
                            globalSqlFilter[9].value = search;

                            // Chiamata a funzione per popolare menù multi-select di filtraggio
                            for (var n = 0; n < globalSqlFilter.length; n++) {
                                if (n !== 4 && n != 5) {
                                    populateSelectMenus("value_unit", search, select, "#valueUnitColumnFilter", n, false, true, FreezeMap, bounds);
                                }
                            }

                            checkTab1Conditions();
                            countSelectedRows();

                        });

                    valueUnitSelectStartOptions = 0;
                    $.getJSON('../controllers/dashboardWizardControllerOS.php?filterDistinct=true',
                        {
                            filter: "value_unit",
                            filterOrg: orgFilter,
                            ajax: 'true',
							synMode: "<?=$synMode?$synMode:0?>"
                        },
                        function (data) {
                            var options = '';
                            var attrib = '';
                            for (var x = 0; x < data.table.length; x++) {
                                options = '<option value="' + data.table[x].value_unit + '" selected="selected">' + data.table[x].value_unit + '</option>';         // CHANGE
                                //    options = '<option value="' + attrib + '" selected="selected">' + attrib + '</option>';         // CHANGE
                                select.append(options);
                                valueUnitSelectStartOptions++;
                            }
                            unitSelect = $('#valueUnitSelect').multiselect({
                                maxHeight: 165,
                                includeSelectAllOption: true,
                                // enableFiltering: true
                            }).multiselect('selectAll', true).multiselect('updateButtonText');
                        });

                });

                // BROKER
                this.api().columns([8]).every(function ()
                {
                    var select = $('<select id="brokerSelect" style="color: black;" multiple="multiple"></select>')
                        .appendTo($("#brokerColumnFilter"))
                        .on('change', function () {
                            /*widgetWizardSelectedRows = {};
                            widgetWizardSelectedRowsTable.clear().draw(false);
                            validityConditions.atLeastOneRowSelected = false;
                            checkTab1Conditions();
                            countSelectedRows();*/

                            var search = [];
                            $.each($('#brokerSelect option:selected'), function () {
                                if ($(this).val() != null && $(this).val() != "null") {
                                    search.push($(this).val());
                                }
                            });
                            var nOptions = 0;
                            $.each($('#brokerSelect option'), function () {
                                nOptions++;
                            });

                            globalSqlFilter[10].allSelected = (search.length == nOptions && nOptions == brokerSelectStartOptions);
                            if (search.length == nOptions && nOptions == brokerSelectStartOptions)
                                search = [];
                            globalSqlFilter[10].selectedVals = search;
                            search = search.join('|');

                            if (search.charAt(0) == '|') {  // CLEAR FIRST CHARACTER IF "|"
                                search = search.substr(1);
                            }

                            globalSqlFilter[10].value = search;
                            if (search == '' && !globalSqlFilter[10].allSelected) {
                                search = 'oiunqauhalknsufhvnoqwpnvfv';
                            }
                            widgetWizardTable.column(8).search(search, false, false).draw();
                            globalSqlFilter[10].value = search;

                            // Chiamata a funzione per popolare menù multi-select di filtraggio
                            for (var n = 0; n < globalSqlFilter.length; n++) {
                                if (n !== 4 && n != 5) {
                                    populateSelectMenus("broker_name", search, select, "#brokerColumnFilter", n, false, true, FreezeMap, bounds);
                                }
                            }

                            checkTab1Conditions();
                            countSelectedRows();

                        });

                    brokerSelectStartOptions = 0;
                    $.getJSON('../controllers/dashboardWizardControllerOS.php?filterDistinct=true',
                        {
                            filter: "broker_name",
                            filterOrg: orgFilter,
                            ajax: 'true'
                        },
                        function (data) {
                            var options = '';
                            var attrib = '';
                            for (var x = 0; x < data.table.length; x++) {
                                options = '<option value="' + data.table[x].broker_name + '" selected="selected">' + data.table[x].broker_name + '</option>';         // CHANGE
                                //    options = '<option value="' + attrib + '" selected="selected">' + attrib + '</option>';         // CHANGE
                                select.append(options);
                                brokerSelectStartOptions++;
                            }
                            $('#brokerSelect').multiselect({
                                maxHeight: 165,
                                includeSelectAllOption: true,
                                // enableFiltering: true
                            }).multiselect('selectAll', true).multiselect('updateButtonText');
                        });

                });

                // VALUE NAME
                this.api().columns([9]).every(function ()
                {
                    var select = $('<select id="valueNameSelect" style="color: black;" multiple="multiple"></select>')
                        .appendTo($("#valueNameColumnFilter"))
                        .on('change', function () {
                            /*widgetWizardSelectedRows = {};
                            widgetWizardSelectedRowsTable.clear().draw(false);
                            validityConditions.atLeastOneRowSelected = false;
                            checkTab1Conditions();
                            countSelectedRows();*/

                            var search = [];
                            $.each($('#valueNameSelect option:selected'), function () {
                                if ($(this).val() != null && $(this).val() != "null") {
                                    search.push($(this).val());
                                }
                            });
                            var nOptions = 0;
                            $.each($('#valueNameSelect option'), function () {
                                nOptions++;
                            });

                            globalSqlFilter[11].allSelected = (search.length == nOptions && nOptions == valueNameSelectStartOptions);
                            if (search.length == nOptions && nOptions == valueNameSelectStartOptions)
                                search = [];
                            globalSqlFilter[11].selectedVals = search;
                            search = search.join('|');

                            if (search.charAt(0) == '|') {  // CLEAR FIRST CHARACTER IF "|"
                                search = search.substr(1);
                            }

                            globalSqlFilter[11].value = search;
                            if (search == '' && !globalSqlFilter[11].allSelected) {
                                search = 'oiunqauhalknsufhvnoqwpnvfv';
                            }
                            widgetWizardTable.column(9).search(search, false, false).draw();
                            globalSqlFilter[11].value = search;

                            // Chiamata a funzione per popolare menù multi-select di filtraggio
                            for (var n = 0; n < globalSqlFilter.length; n++) {
                                if (n !== 4 && n != 5) {
                                    populateSelectMenus("value_name", search, select, "#valueNameColumnFilter", n, false, true, FreezeMap, bounds);
                                }
                            }

                            checkTab1Conditions();
                            countSelectedRows();

                        });

                    valueNameSelectStartOptions = 0;
                    $.getJSON('../controllers/dashboardWizardControllerOS.php?filterDistinct=true',
                        {
                            filter: "value_name",
                            filterOrg: orgFilter,
                            ajax: 'true'
                        },
                        function (data) {
                            var options = '';
                            var attrib = '';
                            for (var x = 0; x < data.table.length; x++) {
                                options = '<option value="' + data.table[x].value_name + '" selected="selected">' + data.table[x].value_name + '</option>';         // CHANGE
                                //    options = '<option value="' + attrib + '" selected="selected">' + attrib + '</option>';         // CHANGE
                                select.append(options);
                                valueNameSelectStartOptions++;
                            }
                            $('#valueNameSelect').multiselect({
                                maxHeight: 165,
                                includeSelectAllOption: true,
                                // enableFiltering: true
                            }).multiselect('selectAll', true).multiselect('updateButtonText');
                        });

                });

                // VALUE TYPE
                this.api().columns([10]).every(function ()
                {
                    var select = $('<select id="valueTypeSelect" style="color: black;" multiple="multiple"></select>')
                        .appendTo($("#valueTypeColumnFilter"))
                        .on('change', function () {
                            /*widgetWizardSelectedRows = {};
                            widgetWizardSelectedRowsTable.clear().draw(false);
                            validityConditions.atLeastOneRowSelected = false;
                            checkTab1Conditions();
                            countSelectedRows();*/

                            var search = [];
                            $.each($('#valueTypeSelect option:selected'), function () {
                                if ($(this).val() != null && $(this).val() != "null") {
                                    search.push($(this).val());
                                }
                            });
                            var nOptions = 0;
                            $.each($('#valueTypeSelect option'), function () {
                                nOptions++;
                            });

                            globalSqlFilter[12].allSelected = (search.length == nOptions && nOptions == valueTypeSelectStartOptions);
                            if (search.length == nOptions && nOptions == valueTypeSelectStartOptions)
                                search = [];
                            globalSqlFilter[12].selectedVals = search;
                            search = search.join('|');

                            if (search.charAt(0) == '|') {  // CLEAR FIRST CHARACTER IF "|"
                                search = search.substr(1);
                            }

                            globalSqlFilter[12].value = search;
                            if (search == '' && !globalSqlFilter[11].allSelected) {
                                search = 'oiunqauhalknsufhvnoqwpnvfv';
                            }
                            widgetWizardTable.column(10).search(search, false, false).draw();
                            globalSqlFilter[12].value = search;

                            // Chiamata a funzione per popolare menù multi-select di filtraggio
                            for (var n = 0; n < globalSqlFilter.length; n++) {
                                if (n !== 4 && n != 5) {
                                    populateSelectMenus("value_type", search, select, "#valueTypeColumnFilter", n, false, true, FreezeMap, bounds);
                                }
                            }

                            checkTab1Conditions();
                            countSelectedRows();

                        });

                    valueTypeSelectStartOptions = 0;
                    $.getJSON('../controllers/dashboardWizardControllerOS.php?filterDistinct=true',
                        {
                            filter: "value_type",
                            filterOrg: orgFilter,
                            ajax: 'true'
                        },
                        function (data) {
                            var options = '';
                            var attrib = '';
                            for (var x = 0; x < data.table.length; x++) {
                                options = '<option value="' + data.table[x].value_type + '" selected="selected">' + data.table[x].value_type + '</option>';         // CHANGE
                                //    options = '<option value="' + attrib + '" selected="selected">' + attrib + '</option>';         // CHANGE
                                select.append(options);
                                valueTypeSelectStartOptions++;
                            }
                            $('#valueTypeSelect').multiselect({
                                maxHeight: 165,
                                includeSelectAllOption: true,
                                // enableFiltering: true
                            }).multiselect('selectAll', true).multiselect('updateButtonText');
                        });

                });


                // MODEL_NAME
                this.api().columns([7]).every(function ()
                {
                    var select = $('<select id="modelNameSelect" style="color: black;" multiple="multiple"></select>')
                        .appendTo($("#modelNameColumnFilter"))
                        .on('change', function () {
                            //widgetWizardSelectedRows = {};
                            //widgetWizardSelectedRowsTable.clear().draw(false);
                            //validityConditions.atLeastOneRowSelected = false;
                            //checkTab1Conditions();
                            //countSelectedRows();

                            var search = [];
                            $.each($('#modelNameSelect option:selected'), function () {
                                if ($(this).val() != null && $(this).val() != "null") {
                                    search.push($(this).val());
                                }
                            });
                            var nOptions = 0;
                            $.each($('#modelNameSelect option'), function () {
                                nOptions++;
                            });

                            globalSqlFilter[13].allSelected = (search.length == nOptions && nOptions == modelNameSelectStartOptions);
                            if (search.length == nOptions && nOptions == modelNameSelectStartOptions)
                                search = [];
                            globalSqlFilter[13].selectedVals = search;
                            search = search.join('|');

                            if (search.charAt(0) == '|') {  // CLEAR FIRST CHARACTER IF "|"
                                search = search.substr(1);
                            }

                            globalSqlFilter[13].value = search;
                            if (search == '' && !globalSqlFilter[13].allSelected) {
                                search = 'oiunqauhalknsufhvnoqwpnvfv';
                            }
                            widgetWizardTable.column(7).search(search, false, false).draw();
                            globalSqlFilter[13].value = search;

                            // Chiamata a funzione per popolare menù multi-select di filtraggio
                            for (var n = 0; n < globalSqlFilter.length; n++) {
                                if (n !== 4 && n != 5) {
                                    populateSelectMenus("model_name", search, select, "#modelNameColumnFilter", n, false, true, FreezeMap, bounds);
                                }
                            }

                            checkTab1Conditions();
                            countSelectedRows();

                        });

                    modelNameSelectStartOptions = 0;
                    $.getJSON('../controllers/dashboardWizardControllerOS.php?filterDistinct=true',
                        {
                            filter: "model_name",
                            filterOrg: orgFilter,
                            ajax: 'true'
                        },
                        function (data) {
                            var options = '';
                            var attrib = '';
                            for (var x = 0; x < data.table.length; x++) {
                                options = '<option value="' + data.table[x].model_name + '" selected="selected">' + data.table[x].model_name + '</option>';         // CHANGE
                                //    options = '<option value="' + attrib + '" selected="selected">' + attrib + '</option>';         // CHANGE
                                select.append(options);
                                modelNameSelectStartOptions++;
                            }
                            $('#modelNameSelect').multiselect({
                                maxHeight: 165,
                                includeSelectAllOption: true,
                                // enableFiltering: true
                            }).multiselect('selectAll', true).multiselect('updateButtonText');
                        });

                });
                

                
                this.api()
                    .columns()
                    .every(function (index) {
                        
                        let column = widgetWizardTable.column(index);
                        let title = column.footer().textContent;
        
                        // Create input element
                        let input = document.createElement('input');
                        input.classList.add('input_search_bar');
                        input.placeholder = title;
                        column.footer().replaceChildren(input);
        
                        // Event listener for user input
                        input.addEventListener('keyup', () => {
                            
                            let column = widgetWizardTable.column(index);
                            
                            if (column.search() !== this.value) {
                                //using_search_bar  = true;
                                console.log("input.value",input.value)
                                $(".tooltip").remove();
                                custom_search_per_column[column.index()] = input.value;
                                column.ajax.reload();
                                $(".tooltip").remove();
                            }
                        });
                });
                
            }
        });
        
        //Settaggio righe selezionate e incompatibili quando si cambia pagina della tabella selected rows
        $('#widgetWizardSelectedRowsTable').on('draw.dt', function () {
            $("#widgetWizardSelectedRowsTable_wrapper").css("overflow-x", "auto");
            $("#widgetWizardSelectedRowsTable_wrapper").css("overflow-y", "hidden");
            if(Object.keys(widgetWizardSelectedRows).length > 0)
            {
                $('#widgetWizardSelectedRowsTable tbody tr').each(function (i) {
                    var rowId = 'row' + $(this).attr('data-rowid');
                    //if(widgetWizardSelectedRows[rowId].widgetCompatible)
                    if($(this).attr('data-widgetCompatible'))
                    {
                        if($(this).hasClass('odd'))
                        {
                            $(this).css("background-color", "#f9f9f9");
                        }
                        else
                        {
                            $(this).css("background-color", "#ffffff");
                        }

                        $(this).attr("data-widgetCompatible", "true");
                    }
                    else
                    {
                        $(this).css("background-color", "#ffb3b3");
                        $(this).attr("data-widgetCompatible", "false");
                    }
                });
            }
        });
        
        //Settaggio righe selezionate quando si cambia pagina
        $('#widgetWizardTable').on('draw.dt', function () {
            $('#widgetWizardTable tbody tr').each(function (i) {
                var rowId = 'row' + $(this).attr('data-rowid');
                if (widgetWizardSelectedRows.hasOwnProperty(rowId))
                {
                    $(this).addClass('selected');
                    $(this).attr("data-selected", "true");
                }
            });
        });

		<?php if(!$synMode && !isCreationWizardUI()) { ?>
        // GESTORE CLICK SU TABELLA PER SELEZIONARE LA RIGA. 
        $('#widgetWizardTable tbody').on('click', 'tr', function ()
        {
			currentMarkerId = $(this).attr('data-rowid');
            //Evidenza grafica di riga selezionata
            if($(this).hasClass('selected'))
            {
				$(this).removeClass('selected');
                var delesectedUnit = widgetWizardSelectedRows['row' + $(this).attr('data-rowid')].unit;
                delete widgetWizardSelectedRows['row' + $(this).attr('data-rowid')];

                widgetWizardSelectedRowsTable.row('[data-rowid=' + $(this).attr('data-rowid') + ']').remove().draw(false);
                
                //Aggiornamento unità selezionate
                updateSelectedUnits('remove', delesectedUnit);
                widgetWizardSelectedSingleRow = null;
               
            } 
            else
            {
				//aggiunto da Bernardo Tiezzi
                if(widgetWizardSelectedSingleRow!==null){
                    $(widgetWizardSelectedSingleRow).removeClass('selected');
                    var delesectedUnit = widgetWizardSelectedRows['row' + $(widgetWizardSelectedSingleRow).attr('data-rowid')].unit;
                    delete widgetWizardSelectedRows['row' + $(widgetWizardSelectedSingleRow).attr('data-rowid')];
                
                    widgetWizardSelectedRowsTable.row('[data-rowid=' + $(widgetWizardSelectedSingleRow).attr('data-rowid') + ']').remove().draw(false);
                
                    //Aggiornamento unità selezionate
                    updateSelectedUnits('remove', delesectedUnit);
                    console.log('data selected ' + $(widgetWizardSelectedSingleRow).attr("data-selected"));
                    $(widgetWizardSelectedSingleRow).attr('data-selected', 'false');
                    try
                    {
                        if (gisLayersOnMap[$(widgetWizardSelectedSingleRow).attr("data-servicetype")] != null && gisLayersOnMap[$(widgetWizardSelectedSingleRow).attr("data-servicetype")] != undefined) {
                            gisLayersOnMap[$(widgetWizardSelectedSingleRow).attr("data-servicetype")].clearLayers();
                        } else {
                            clearAllMarkers();
                        }
                    }
                    catch(e)
                    {
                        console.log("Colta eccezione mappa: " + e.message);
                    }
                    
                    widgetWizardSelectedSingleRow = null;
                }//Fine aggiunto da Bernardo Tiezzi
                $(this).addClass('selected');
                widgetWizardSelectedSingleRow = this;
                widgetWizardSelectedRows['row' + $(this).attr('data-rowid')] = 
                {
                    high_level_type: $(this).attr('data-high_level_type'),
                    nature: $(this).attr('data-nature'),
                    sub_nature: $(this).attr('data-sub_nature'), //Questa è da mandare a ServiceMap
                    low_level_type: $(this).attr('data-low_level_type'), //Ora si chiama Value type
                    unique_name_id: $(this).attr('data-unique_name_id'), //Ora si chiama Value name
                    instance_uri: $(this).attr('data-instance_uri'),
                    unit: $(this).attr('data-unit'),
                    device_model_name: $(this).attr('data-device_model_name'),
                    broker_name: $(this).attr('data-broker_name'),
                    value_name: $(this).attr('data-value_name'),
                    value_type: $(this).attr('data-value_type'),
                    servicetype: $(this).attr('data-servicetype'),//Doppione?
                    sm_based: $(this).attr('data-sm_based'),
                    parameters: $(this).attr('data-parameters'),
                    widgetCompatible: true,
                    get_instances: $(this).attr('data-get_instances'),
                    last_value: $(this).attr('data-last_value'),
                    value_unit: $(this).attr('data-valueunit')
                };
                
                widgetWizardSelectedRowsTable.row.add([
                    $(this).find('td').eq(0).html(),
                    $(this).find('td').eq(1).html(),
                    $(this).find('td').eq(2).html(),
                    $(this).find('td').eq(5).html(),
                    $(this).find('td').eq(6).html(),
                    $(this).find('td').eq(7).html(),
                    $(this).find('td').eq(8).html(),
                    $(this).find('td').eq(9).html(),
                    $(this).find('td').eq(10).html(),
                    $(this).find('td').eq(11).html(),
                    $(this).find('td').eq(12).html(),
                    $(this).find('td').eq(13).html(),
                    $(this).find('td').eq(14).html(),
                    $(this).find('td').eq(17).html(),
                    $(this).find('td').eq(18).html(),
                    $(this).attr('data-rowid'),
                    true
                ]).draw(false);		
                
                //Aggiornamento unità selezionate
                updateSelectedUnits('add', null);
            }
            
            countSelectedRows();
            checkBrokerAndNrRowsTogether();
            checkAtLeastOneRowSelected();
            checkTab1Conditions();
            
            //Aggiunta/rimozione pins su mappa
            bounds = addWidgetWizardMapRef.getBounds();
            var serviceType = $(this).attr("data-servicetype");
            var uniqueNameId = $(this).attr("data-unique_name_id");
            var instanceUri = $(this).attr("data-instance_uri");
            var getInstances = $(this).attr("data-get_instances");
            var latitudeWiz = $(this).attr("data-latitude");
            var longitudeWiz = $(this).attr("data-longitude");
            var northEastPointLat = bounds._northEast.lat;
            var northEastPointLng = bounds._northEast.lng;
            var southWestPointLat = bounds._southWest.lat;
            var southWestPointLng = bounds._southWest.lng;

            var showFlag = false;
            var myPOIId, myPOIlat, myPOIlng = null;

            // CAMBIA COLORE (TOGGLE PER SELEZIONE/DESELEZIONE) on Click
            if($(this).attr("data-selected") === "false")
            {
                showFlag = true;
            } 
            else
            {
                showFlag = false;
            }

            if(showFlag == true)
            {
                if(instanceUri === "any + status") {
                    var urlKbToCall = "<?= $superServiceMapUrlPrefix ?>api/v1/?serviceUri=http://www.disit.org/km4city/resource/" + uniqueNameId + "&format=json&realtime=false&fullCount=false";
                    if ("<?= $_SESSION['loggedRole'] ?>" == "RootAdmin") {
                        urlKbToCall = "<?= $superServiceMapUrlPrefix ?>api/v1/?serviceUri=" + getInstances + "&format=json&realtime=false&fullCount=false";
                    } else {
                        if (orgName != null && orgName != '') {
                            var baseUrl = orgKbUrl;
                            urlKbToCall = baseUrl + "?serviceUri=" + getInstances + "&format=json&realtime=false&fullCount=false";
                        }
                    }
                    $.ajax({
                        url: "<?= $superServiceMapProxy ?>" + urlKbToCall,
                        type: "GET",
                        async: true,
                        dataType: 'json',
                        data: {},
                        success: function (geoData)
                        {
                            var LatPos = null;
                            var LongPos = null;
                            var fatherNode = null;
                            if (geoData.hasOwnProperty("BusStop"))
                            {
                                fatherNode = geoData.BusStop;
                                if(!FreezeMap){
                                    LatPos=geoData.BusStop.features[0].geometry.coordinates[1];
                                    LongPos=geoData.BusStop.features[0].geometry.coordinates[0];
                                }                                
                            } else
                            {
                                if (geoData.hasOwnProperty("Sensor"))
                                {
                                    fatherNode = geoData.Sensor;
                                    if(!FreezeMap){
                                        LatPos=geoData.Sensor.features[0].geometry.coordinates[1];
                                        LongPos=geoData.Sensor.features[0].geometry.coordinates[0];
                                    }
                                } else
                                {
                                    //Prevedi anche la gestione del caso in cui non c'è nessuna di queste tre, sennò il widget rimane appeso.
                                    fatherNode = geoData.Service;
                                    if(!FreezeMap){
                                        LatPos=geoData.Service.features[0].geometry.coordinates[1];
                                        LongPos=geoData.Service.features[0].geometry.coordinates[0];
                                    }
                                }
                            }

                        /*    if (fatherNode.features[0].properties != null) {
                                if (fatherNode.features[0].properties.organization == "CAPELON" && fatherNode.features[0].properties.serviceUri.includes("%3A")) {
                                    fatherNode.features[0].properties.serviceUri = fatherNode.features[0].properties.serviceUri.replaceAll("%3A", "%253A");
                                }
                            }*/

                            gisLayersOnMap[serviceType] = L.geoJSON(fatherNode, {
                                pointToLayer: addWidgetWizardCreateCustomMarker
                            }).addTo(addWidgetWizardMapRef);
                            if(!FreezeMap){
                                var Zoom = addWidgetWizardMapRef.getZoom();//serve per mantenere lo zoom della mappa
                                addWidgetWizardMapRef.setView(L.latLng(LatPos, LongPos), Zoom);
                            }

                        },
                        error: function (data)
                        {
                            console.log("ERROR in retrieving GeoData by Km4City SmartCity API: " + JSON.stringify(data));
                        }
                    });
                    $(this).attr('data-selected', 'true');
                } else if (instanceUri === "any") {
                    if ($(this).attr("data-high_level_type") === "MicroApplication" || $(this).attr("data-high_level_type") === "Special Widget") {
                        if (latitudeWiz != null && latitudeWiz != undefined && longitudeWiz != null && longitudeWiz != undefined) {

                            //    var latlngForMarker = "[" + latitudeWiz + ", "

                            var mapPinImg = '../img/gisMapIcons/generic.png';
                            var markerIcon = L.icon({
                                iconUrl: mapPinImg,
                                iconAnchor: [16, 37]
                            });

                            var genericMarker = L.marker([latitudeWiz, longitudeWiz], {icon: markerIcon}).addTo(addWidgetWizardMapRef);
                            //    L.marker([60.170437, 24.938215]).addTo(addWidgetWizardMapRef);
                            $(this).attr('data-selected', 'true');
                            addWidgetWizardMapMarkers[$(this).attr('data-rowid')] = genericMarker;
                            if (FreezeMap !== true) {
                                addWidgetWizardMapRef.setView(L.latLng(latitudeWiz, longitudeWiz), addWidgetWizardMapRef.getZoom());
                            }
                        }
                    } else {
                        var urlKbToCall = "https://servicemap.disit.org/WebAppGrafo/api/v1/?selection=" + southWestPointLat + ";" + southWestPointLng + ";" + northEastPointLat + ";" + northEastPointLng + "&categories=" + serviceType + "&format=json&fullCount=false&maxResults=500";
                        if ("<?= $_SESSION['loggedRole'] ?>" == "RootAdmin") {
                            urlKbToCall = "<?= $superServiceMapUrlPrefix ?>api/v1/?selection=" + southWestPointLat + ";" + southWestPointLng + ";" + northEastPointLat + ";" + northEastPointLng + "&categories=" + serviceType + "&format=json&fullCount=false&maxResults=500";
                        } else if (orgName != null && orgName != '') {
                            var baseUrl = orgKbUrl;
                            urlKbToCall = baseUrl + "?selection=" + southWestPointLat + ";" + southWestPointLng + ";" + northEastPointLat + ";" + northEastPointLng + "&categories=" + serviceType + "&format=json&fullCount=false&maxResults=500";
                        }
                        if ($(this).attr("data-high_level_type") === "IoT Device Model" || $(this).attr("data-high_level_type") === "Data Table Model" || $(this).attr("data-high_level_type") === "Mobile Device Model") {
                            urlKbToCall = urlKbToCall + "&model=" + uniqueNameId;
                        }
                        $.ajax({
                            url: "<?= $superServiceMapProxy ?>" + urlKbToCall,
                            type: "GET",
                            async: true,
                            dataType: 'json',
                            data: {},
                            success: function (geoData) {
                                var fatherNode = null;
                                if (geoData.hasOwnProperty("BusStops")) {
                                    fatherNode = geoData.BusStops;
                                } else {
                                    if (geoData.hasOwnProperty("SensorSites")) {
                                        fatherNode = geoData.SensorSites;
                                    } else {
                                        //Prevedi anche la gestione del caso in cui non c'è nessuna di queste tre, sennò il widget rimane appeso.
                                        fatherNode = geoData.Services;
                                    }
                                }

                            /*    if (fatherNode.features[0].properties != null) {
                                    if (fatherNode.features[0].properties.organization == "CAPELON" && fatherNode.features[0].properties.serviceUri.includes("%3A")) {
                                        fatherNode.features[0].properties.serviceUri = fatherNode.features[0].properties.serviceUri.replaceAll("%3A", "%253A");
                                    }
                                }*/

                                gisLayersOnMap[serviceType] = L.geoJSON(fatherNode, {
                                    pointToLayer: addWidgetWizardCreateCustomMarker
                                }).addTo(addWidgetWizardMapRef);

                            },
                            error: function (data) {
                                console.log("ERROR in retrieving GeoData by Km4City SmartCity API: " + JSON.stringify(data));
                            }
                        });
                    }
                    $(this).attr('data-selected', 'true');
                } else if (instanceUri === "single_marker") {
                    getInstances = encodeURI(getInstances);    // URI_ENCODE_FIX
                    var urlSensorKbToCall = "https://servicemap.disit.org/WebAppGrafo/api/v1/?serviceUri=" + getInstances + "&format=json&realtime=false&fullCount=false";
                    if ("<?= $_SESSION['loggedRole'] ?>" == "RootAdmin") {
                        urlSensorKbToCall = "<?= $superServiceMapUrlPrefix ?>api/v1/?serviceUri=" + getInstances + "&format=json&realtime=false&fullCount=false";
                    } else {
                        if (orgName != null && orgName != '') {
                            var baseUrl = orgKbUrl;
                            urlSensorKbToCall = baseUrl + "?serviceUri=" + getInstances + "&format=json&realtime=false&fullCount=false";
                        }
                    }
                    urlSensorKbToCall = "<?= $superServiceMapUrlPrefix ?>api/v1/?serviceUri=" + getInstances + "&format=json&realtime=false&fullCount=false";
                    $.ajax({
                        url: "<?= $superServiceMapProxy ?>" + urlSensorKbToCall,
                        type: "GET",
                        async: true,
                        dataType: 'json',
                        data: {},
                        success: function (geoData)
                        {
                            var LatPos = null;
                            var LongPos = null;
                            var fatherNode = null;
                            if (geoData.hasOwnProperty("BusStop"))
                            {
                                fatherNode = geoData.BusStop;
                                if(!FreezeMap){
                                    LatPos=geoData.BusStop.features[0].geometry.coordinates[1];
                                    LongPos=geoData.BusStop.features[0].geometry.coordinates[0];
                                }
                            } else
                            {
                                if (geoData.hasOwnProperty("Sensor"))
                                {
                                    fatherNode = geoData.Sensor;
                                    if(!FreezeMap){
                                        LatPos=geoData.Sensor.features[0].geometry.coordinates[1];
                                        LongPos=geoData.Sensor.features[0].geometry.coordinates[0];
                                    }
                                    
                                
                                } else
                                {
                                    //Prevedi anche la gestione del caso in cui non c'è nessuna di queste tre, sennò il widget rimane appeso.
                                    fatherNode = geoData.Service;
                                    if(!FreezeMap){
                                        LatPos=geoData.Service.features[0].geometry.coordinates[1];
                                        LongPos=geoData.Service.features[0].geometry.coordinates[0];
                                    }
                                    
                                
                                }
                            }

                         /*   if (fatherNode.features[0].properties != null) {
                                if (fatherNode.features[0].properties.organization == "CAPELON" && fatherNode.features[0].properties.serviceUri.includes("%3A")) {
                                    fatherNode.features[0].properties.serviceUri = fatherNode.features[0].properties.serviceUri.replaceAll("%3A", "%253A");
                                }
                            }*/

                            gisLayersOnMap[serviceType] = L.geoJSON(fatherNode, {
                                pointToLayer: addWidgetWizardCreateCustomMarker
                            }).addTo(addWidgetWizardMapRef);
                            if(!FreezeMap){
                                var Zoom = addWidgetWizardMapRef.getZoom();
                            
                                addWidgetWizardMapRef.setView(L.latLng(LatPos, LongPos), Zoom);
                            }
                        },
                        error: function (data)
                        {
                            console.log("ERROR in retrieving GeoData by Km4City SmartCity API: " + JSON.stringify(data));
                        }
                    });
                    $(this).attr('data-selected', 'true');
                }  else if (instanceUri === "MyPOI") {
                    var myPOIParameters = $(this).attr('data-parameters');
                    if (myPOIParameters === null || myPOIParameters === undefined) {
                        myPOIId = "All";
                    } else if (myPOIParameters != "All") {
                        myPOIId = myPOIParameters.split("datamanager/api/v1/poidata/")[1];
                    } else {
                        myPOIId = myPOIParameters;
                    }

                    $.ajax({
                        url: "../controllers/myPOIProxy.php",
                        type: "GET",
                        data: {
                            myPOIId: myPOIId,
                        },
                        async: true,
                        dataType: 'json',
                        success: function(geoData)
                        {
                            var fatherNode = null;
                            if (geoData.hasOwnProperty("BusStop"))
                            {
                                fatherNode = geoData.BusStop;
                            } else
                            {
                                if (geoData.hasOwnProperty("Sensor"))
                                {
                                    fatherNode = geoData.Sensor;
                                } else
                                {
                                    //Prevedi anche la gestione del caso in cui non c'è nessuna di queste tre, sennò il widget rimane appeso.
                                    fatherNode = geoData;
                                }
                            }

                            gisLayersOnMap[serviceType] = L.geoJSON(fatherNode, {
                                pointToLayer: addWidgetWizardCreateCustomMarker
                            }).addTo(addWidgetWizardMapRef);
                            if (FreezeMap !== true) {
                                addWidgetWizardMapRef.setView(L.latLng(fatherNode.geometry.coordinates[1], fatherNode.geometry.coordinates[0]), addWidgetWizardMapRef.getZoom());
                            }

                        },
                        error: function (data)
                        {
                            console.log("ERROR in retrieving GeoData by Km4City SmartCity API: " + JSON.stringify(data));
                        }
                    });
                    $(this).attr('data-selected', 'true');
                } else if (instanceUri === "MyKPI") {
                    var myKPIParameters = $(this).attr('data-parameters');
                  /*  var myKPIId = myKPIParameters.split("__")[0];
                    var myKPIlat = myKPIParameters.split("__")[1].split(";")[0];
                    var myKPIlng = myKPIParameters.split("__")[1].split(";")[1];    */
                    if (myKPIParameters.includes("datamanager/api/v1/poidata/")) {
                        var myKPIId = myKPIParameters.split("datamanager/api/v1/poidata/")[1];
                    } else {
                        var myKPIId = myKPIParameters;
                    }
                    $.ajax({
                        url: "../controllers/myPOIProxy.php",
                        type: "GET",
                        data: {
                            myPOIId: myKPIId,
                        },
                        async: true,
                        dataType: 'json',
                        success: function (geoData) {
                            var fatherNode = null;
                            if (geoData.hasOwnProperty("BusStop")) {
                                fatherNode = geoData.BusStop;
                            } else {
                                if (geoData.hasOwnProperty("Sensor")) {
                                    fatherNode = geoData.Sensor;
                                } else {
                                    //Prevedi anche la gestione del caso in cui non c'è nessuna di queste tre, sennò il widget rimane appeso.
                                    fatherNode = geoData;
                                }
                            }

                            gisLayersOnMap[serviceType] = L.geoJSON(fatherNode, {
                                pointToLayer: addWidgetWizardCreateCustomMarker
                            }).addTo(addWidgetWizardMapRef);
                            if (FreezeMap !== true) {
                                addWidgetWizardMapRef.setView(L.latLng(fatherNode.geometry.coordinates[1], fatherNode.geometry.coordinates[0]), addWidgetWizardMapRef.getZoom());
                            }

                        },
                        error: function (data) {
                            console.log("ERROR in retrieving GeoData by Km4City SmartCity API: " + JSON.stringify(data));
                        }
                    });
                //    }
                    $(this).attr('data-selected', 'true');
                } else if (instanceUri === "MyData") {
                    var myDataParameters = $(this).attr('data-parameters');
                    /*  var myKPIId = myKPIParameters.split("__")[0];
                      var myKPIlat = myKPIParameters.split("__")[1].split(";")[0];
                      var myKPIlng = myKPIParameters.split("__")[1].split(";")[1];    */
                    if (myDataParameters.includes("datamanager/api/v1/poidata/")) {
                        var myDataId = myDataParameters.split("datamanager/api/v1/poidata/")[1];
                        $.ajax({
                            url: "../controllers/myPOIProxy.php",
                            type: "GET",
                            data: {
                                myPOIId: myDataId,
                            },
                            async: true,
                            dataType: 'json',
                            success: function (geoData) {
                                var fatherNode = null;
                                if (geoData.hasOwnProperty("BusStop")) {
                                    fatherNode = geoData.BusStop;
                                } else {
                                    if (geoData.hasOwnProperty("Sensor")) {
                                        fatherNode = geoData.Sensor;
                                    } else {
                                        //Prevedi anche la gestione del caso in cui non c'è nessuna di queste tre, sennò il widget rimane appeso.
                                        fatherNode = geoData;
                                    }
                                }

                                gisLayersOnMap[serviceType] = L.geoJSON(fatherNode, {
                                    pointToLayer: addWidgetWizardCreateCustomMarker
                                }).addTo(addWidgetWizardMapRef);

                            },
                            error: function (data) {
                                console.log("ERROR in retrieving GeoData by Km4City SmartCity API: " + JSON.stringify(data));
                            }
                        });
                    }
                    $(this).attr('data-selected', 'true');
                } else if (latitudeWiz != null && latitudeWiz != undefined && longitudeWiz != null && longitudeWiz != undefined) {

                //    var latlngForMarker = "[" + latitudeWiz + ", "

                    var mapPinImg = '../img/gisMapIcons/generic.png';
                    var markerIcon = L.icon({
                        iconUrl: mapPinImg,
                        iconAnchor: [16, 37]
                    });

                    var genericMarker = L.marker([latitudeWiz, longitudeWiz], {icon: markerIcon}).addTo(addWidgetWizardMapRef);
                //    L.marker([60.170437, 24.938215]).addTo(addWidgetWizardMapRef);
                    $(this).attr('data-selected', 'true');
                    addWidgetWizardMapMarkers[$(this).attr('data-rowid')] = genericMarker;
                    if (FreezeMap !== true) {
                        addWidgetWizardMapRef.setView(L.latLng(latitudeWiz, longitudeWiz), addWidgetWizardMapRef.getZoom());
                    }
                }
            }
            else
            {
                var stopFlag = 1;
                $(this).attr('data-selected', 'false');
                try
                {
                    if (instanceUri != null && instanceUri != undefined) {
                        if (instanceUri == "any" || (instanceUri.toLowerCase() == "mypoi" && $(this).attr("data-sub_nature") == "Any")) {
                            if ($(this).attr("data-high_level_type") == "Special Widget") {
                                clearMarker(currentMarkerId);
                            } else {
                                gisLayersOnMap[serviceType].clearLayers();
                            }
                        } else {
                            clearMarker(currentMarkerId);
                        }
                    } else {
                        clearMarker(currentMarkerId);
                    }
                }
                catch(e)
                {
                    console.log("Colta eccezione mappa: " + e.message);
                }
            }

            updateIconsFromSelectedRows();
            updateWidgetCompatibleRows();
        });
		<?php } else { // handler for row click in synMode ?>
		$('#widgetWizardTable tbody').on('click', 'tr', function ()
        {
			currentMarkerId = $(this).attr('data-rowid');
            //Evidenza grafica di riga selezionata
            if($(this).hasClass('selected'))
            {
				$(this).removeClass('selected');
                var delesectedUnit = widgetWizardSelectedRows['row' + $(this).attr('data-rowid')].unit;
                delete widgetWizardSelectedRows['row' + $(this).attr('data-rowid')];

                widgetWizardSelectedRowsTable.row('[data-rowid=' + $(this).attr('data-rowid') + ']').remove().draw(false);
                
                //Aggiornamento unità selezionate
                updateSelectedUnits('remove', delesectedUnit);
            } 
            else
            {
				$(this).addClass('selected');
            

                widgetWizardSelectedRows['row' + $(this).attr('data-rowid')] =
                {
                    high_level_type: $(this).attr('data-high_level_type'),
                    nature: $(this).attr('data-nature'),
                    sub_nature: $(this).attr('data-sub_nature'), //Questa è da mandare a ServiceMap
                    low_level_type: $(this).attr('data-low_level_type'), //Ora si chiama Value type
                    unique_name_id: $(this).attr('data-unique_name_id'), //Ora si chiama Value name
                    instance_uri: $(this).attr('data-instance_uri'),
                    unit: $(this).attr('data-unit'),
                    device_model_name: $(this).attr('data-device_model_name'),
                    broker_name: $(this).attr('data-broker_name'),
                    value_name: $(this).attr('data-value_name'),
                    value_type: $(this).attr('data-value_type'),
                    servicetype: $(this).attr('data-servicetype'),//Doppione?
                    sm_based: $(this).attr('data-sm_based'),
                    parameters: $(this).attr('data-parameters'),
                    widgetCompatible: true,
                    get_instances: $(this).attr('data-get_instances'),
                    last_value: $(this).attr('data-last_value'),
                    value_unit: $(this).attr('data-valueunit')
                };

                console.log(widgetWizardSelectedRows,$(this).attr('data-unit'), $(this),widgetWizardSelectedRows['row' + $(this).attr('data-rowid')]);

                <?php if(isCreationWizardUI()):?>
                    widgetWizardSelectedRowsTable.row.add([
                        $(this).find('td').eq(0).html(),
                        $(this).find('td').eq(1).html(),
                        $(this).find('td').eq(2).html(),
                        $(this).find('td').eq(3).html(),
                        $(this).find('td').eq(4).html(),
                        $(this).find('td').eq(5).html(),
                        $(this).find('td').eq(6).html(),
                        $(this).find('td').eq(7).html(),
                        $(this).find('td').eq(8).html(),
                        $(this).find('td').eq(9).html(),
                        $(this).find('td').eq(10).html(),
                        $(this).find('td').eq(11).html(),
                        $(this).find('td').eq(12).html(),
                        $(this).find('td').eq(13).html(),
                        $(this).find('td').eq(14).html(),
                        $(this).find('td').eq(16).html(),
                        $(this).find('td').eq(17).html(),
                        $(this).attr('data-rowid'),
                        true
                    ]).draw(false);
                <?php else: ?>
                    widgetWizardSelectedRowsTable.row.add([
                        $(this).find('td').eq(0).html(),
                        $(this).find('td').eq(1).html(),
                        $(this).find('td').eq(2).html(),
                        $(this).find('td').eq(3).html(),
                        $(this).find('td').eq(4).html(),
                        $(this).find('td').eq(5).html(),
                        $(this).find('td').eq(6).html(),
                        $(this).find('td').eq(7).html(),
                        $(this).find('td').eq(8).html(),
                        $(this).find('td').eq(9).html(),
                        $(this).find('td').eq(10).html(),
                        $(this).find('td').eq(11).html(),
                        $(this).find('td').eq(12).html(),
                        $(this).find('td').eq(13).html(),
                        $(this).find('td').eq(14).html(),
                        $(this).attr('data-rowid'),
                        true
                    ]).draw(false);
                <?php endif; ?>

                
                    
                console.log(widgetWizardSelectedRows['row' + $(this).attr('data-rowid')]);
				
                
                //Aggiornamento unità selezionate
                updateSelectedUnits('add', null);
            }
            
            countSelectedRows();
            checkBrokerAndNrRowsTogether();
            checkAtLeastOneRowSelected();
            checkTab1Conditions(widgetWizardSelectedRows);
            
            //Aggiunta/rimozione pins su mappa
            var bounds = addWidgetWizardMapRef.getBounds();
            var serviceType = $(this).attr("data-servicetype");
            var uniqueNameId = $(this).attr("data-unique_name_id");
            var instanceUri = $(this).attr("data-instance_uri");
            var getInstances = $(this).attr("data-get_instances");
            var latitudeWiz = $(this).attr("data-latitude");
            var longitudeWiz = $(this).attr("data-longitude");
            var northEastPointLat = bounds._northEast.lat;
            var northEastPointLng = bounds._northEast.lng;
            var southWestPointLat = bounds._southWest.lat;
            var southWestPointLng = bounds._southWest.lng;

            var showFlag = false;
            var myPOIId, myPOIlat, myPOIlng = null;

            // CAMBIA COLORE (TOGGLE PER SELEZIONE/DESELEZIONE) on Click
            if($(this).attr("data-selected") === "false")
            {
                showFlag = true;
            } 
            else
            {
                showFlag = false;
            }

            if(showFlag == true)
            {
                if(instanceUri === "any + status") {
                    var urlKbToCall = "https://servicemap.disit.org/WebAppGrafo/api/v1/?serviceUri=http://www.disit.org/km4city/resource/" + uniqueNameId + "&format=json&realtime=false&fullCount=false";
                    if ("<?= $_SESSION['loggedRole'] ?>" == "RootAdmin") {
                        urlKbToCall = "<?= $superServiceMapUrlPrefix ?>api/v1/?serviceUri=" + getInstances + "&format=json&realtime=false&fullCount=false";
                    } else {
                        if (orgName != null && orgName != '') {
                            var baseUrl = orgKbUrl;
                            urlKbToCall = baseUrl + "?serviceUri=" + getInstances + "&format=json&realtime=false&fullCount=false";
                        }
                    }
                    $.ajax({
                        url: "<?= $superServiceMapProxy ?>" + urlKbToCall,
                        type: "GET",
                        async: true,
                        dataType: 'json',
                        data: {},
                        uniqueNameId: uniqueNameId,
                        success: function (geoData)
                        {
                            var LatPos = null;
                            var LongPos = null;
                            var fatherNode = null;
							if (geoData && geoData.hasOwnProperty("BusStop"))
                            {
                                fatherNode = geoData.BusStop;
                                if(!FreezeMap){
                                    LatPos=geoData.BusStop.features[0].geometry.coordinates[1];
                                    LongPos=geoData.BusStop.features[0].geometry.coordinates[0];
                                }
                            } else
                            {
								if (geoData && geoData.hasOwnProperty("Sensor"))
                                {
                                    fatherNode = geoData.Sensor;
                                    if(!FreezeMap){
                                        LatPos=geoData.Sensor.features[0].geometry.coordinates[1];
                                        LongPos=geoData.Sensor.features[0].geometry.coordinates[0];
                                    }
                                } else
                                {
                                    //Prevedi anche la gestione del caso in cui non c'è nessuna di queste tre, sennò il widget rimane appeso.
                                    fatherNode = geoData.Service;
                                    if(!FreezeMap){
                                        LatPos=geoData.Service.features[0].geometry.coordinates[1];
                                        LongPos=geoData.Service.features[0].geometry.coordinates[0];
                                    }
                                }
                            }

                        /*    if (fatherNode.features[0].properties != null) {
                                if (fatherNode.features[0].properties.organization == "CAPELON" && fatherNode.features[0].properties.serviceUri.includes("%3A")) {
                                    fatherNode.features[0].properties.serviceUri = fatherNode.features[0].properties.serviceUri.replaceAll("%3A", "%253A");
                                }
                            }*/

                            gisLayersOnMap[serviceType] = L.geoJSON(fatherNode, {
                                pointToLayer: addWidgetWizardCreateCustomMarker
                            }).addTo(addWidgetWizardMapRef);
                            if(!FreezeMap){
                                var Zoom = addWidgetWizardMapRef.getZoom();//serve per mantenere lo zoom della mappa
                                addWidgetWizardMapRef.setView(L.latLng(LatPos, LongPos), Zoom);
                            }

                        },
                        error: function (data)
                        {
                            console.log("ERROR in retrieving GeoData by Km4City SmartCity API: " + JSON.stringify(data));
                        }
                    });
                    $(this).attr('data-selected', 'true');
                } else if (instanceUri === "any") {
                    if ($(this).attr("data-high_level_type") === "MicroApplication" || $(this).attr("data-high_level_type") === "Special Widget") {
                        if (latitudeWiz != null && latitudeWiz != undefined && longitudeWiz != null && longitudeWiz != undefined) {

                            //    var latlngForMarker = "[" + latitudeWiz + ", "

                            var mapPinImg = '../img/gisMapIcons/generic.png';
                            var markerIcon = L.icon({
                                iconUrl: mapPinImg,
                                iconAnchor: [16, 37]
                            });

                            var genericMarker = L.marker([latitudeWiz, longitudeWiz], {icon: markerIcon}).addTo(addWidgetWizardMapRef);
                            //    L.marker([60.170437, 24.938215]).addTo(addWidgetWizardMapRef);
                            $(this).attr('data-selected', 'true');
                            addWidgetWizardMapMarkers[$(this).attr('data-rowid')] = genericMarker;
                            if (FreezeMap !== true) {
                                addWidgetWizardMapRef.setView(L.latLng(latitudeWiz, longitudeWiz), addWidgetWizardMapRef.getZoom());
                            }
                        }
                    } else {
                        var urlKbToCall = "https://servicemap.disit.org/WebAppGrafo/api/v1/?selection=" + southWestPointLat + ";" + southWestPointLng + ";" + northEastPointLat + ";" + northEastPointLng + "&categories=" + serviceType + "&format=json&fullCount=false&maxResults=500";
                        if ("<?= $_SESSION['loggedRole'] ?>" == "RootAdmin") {
                            urlKbToCall = "<?= $superServiceMapUrlPrefix ?>api/v1/?selection=" + southWestPointLat + ";" + southWestPointLng + ";" + northEastPointLat + ";" + northEastPointLng + "&categories=" + serviceType + "&format=json&fullCount=false&maxResults=500";
                        } else if (orgName != null && orgName != '') {
                            var baseUrl = orgKbUrl;
                            urlKbToCall = baseUrl + "?selection=" + southWestPointLat + ";" + southWestPointLng + ";" + northEastPointLat + ";" + northEastPointLng + "&categories=" + serviceType + "&format=json&fullCount=false&maxResults=500";
                        }
                        $.ajax({
                            url: "<?= $superServiceMapProxy ?>" + urlKbToCall,
                            type: "GET",
                            async: true,
                            dataType: 'json',
                            data: {},
                            uniqueNameId: uniqueNameId,
                            success: function (geoData) {
                                if(!geoData) return;
								var fatherNode = null;
								if (geoData.hasOwnProperty("BusStops")) {
                                    fatherNode = geoData.BusStops;
                                } else {
									if (geoData.hasOwnProperty("SensorSites")) {
                                        fatherNode = geoData.SensorSites;
                                    } else {
                                        //Prevedi anche la gestione del caso in cui non c'è nessuna di queste tre, sennò il widget rimane appeso.
                                        fatherNode = geoData.Services;
                                    }
                                }

                             /*   if (fatherNode.features[0].properties != null) {
                                    if (fatherNode.features[0].properties.organization == "CAPELON" && fatherNode.features[0].properties.serviceUri.includes("%3A")) {
                                        fatherNode.features[0].properties.serviceUri = fatherNode.features[0].properties.serviceUri.replaceAll("%3A", "%253A");
                                    }
                                }*/

                                gisLayersOnMap[serviceType] = L.geoJSON(fatherNode, {
                                    pointToLayer: addWidgetWizardCreateCustomMarker
                                }).addTo(addWidgetWizardMapRef);

                            },
                            error: function (data) {
                                console.log("ERROR in retrieving GeoData by Km4City SmartCity API: " + JSON.stringify(data));
                            }
                        });
                        $(this).attr('data-selected', 'true');
                    }
                } else if (instanceUri === "single_marker") {
                    getInstances = encodeURI(getInstances);    // URI_ENCODE_FIX
                    var urlSensorKbToCall = "https://servicemap.disit.org/WebAppGrafo/api/v1/?serviceUri=" + getInstances + "&format=json&realtime=false&fullCount=false";
                    if ("<?= $_SESSION['loggedRole'] ?>" == "RootAdmin") {
                        urlSensorKbToCall = "<?= $superServiceMapUrlPrefix ?>api/v1/?serviceUri=" + getInstances + "&format=json&realtime=false&fullCount=false";
                    } else {
                        if (orgName != null && orgName != '') {
                            var baseUrl = orgKbUrl;
                            urlSensorKbToCall = baseUrl + "?serviceUri=" + getInstances + "&format=json&realtime=false&fullCount=false";
                        }
                    }
                    urlSensorKbToCall = "<?= $superServiceMapUrlPrefix ?>api/v1/?serviceUri=" + getInstances + "&format=json&realtime=false&fullCount=false";
                    $.ajax({
                        url: "<?= $superServiceMapProxy ?>" + urlSensorKbToCall,
                        type: "GET",
                        async: true,
                        dataType: 'json',
                        data: {},
                        uniqueNameId: uniqueNameId,
                        success: function (geoData)
                        {
                            if(!geoData) return;
							var LatPos = null;
                            var LongPos = null;
                            var fatherNode = null;
							if (geoData.hasOwnProperty("BusStop"))
                            {
                                fatherNode = geoData.BusStop;
                                if(!FreezeMap){
                                    LatPos=geoData.BusStop.features[0].geometry.coordinates[1];
                                    LongPos=geoData.BusStop.features[0].geometry.coordinates[0];
                                }
                            } else
                            {
								if (geoData.hasOwnProperty("Sensor"))
                                {
                                    fatherNode = geoData.Sensor;
                                    if(!FreezeMap){
                                        LatPos=geoData.Sensor.features[0].geometry.coordinates[1];
                                        LongPos=geoData.Sensor.features[0].geometry.coordinates[0];
                                    }
                                } else
                                {
                                    //Prevedi anche la gestione del caso in cui non c'è nessuna di queste tre, sennò il widget rimane appeso.
                                    fatherNode = geoData.Service;
                                    if(!FreezeMap){
                                        LatPos=geoData.Service.features[0].geometry.coordinates[1];
                                        LongPos=geoData.Service.features[0].geometry.coordinates[0];
                                    }
                                }
                            }

                        /*    if (fatherNode.features[0].properties != null) {
                                if (fatherNode.features[0].properties.organization == "CAPELON" && fatherNode.features[0].properties.serviceUri.includes("%3A")) {
                                    fatherNode.features[0].properties.serviceUri = fatherNode.features[0].properties.serviceUri.replaceAll("%3A", "%253A");
                                }
                            }*/

                            gisLayersOnMap[serviceType] = L.geoJSON(fatherNode, {
                                pointToLayer: addWidgetWizardCreateCustomMarker
                            }).addTo(addWidgetWizardMapRef);
                            if(!FreezeMap){
                                var Zoom = addWidgetWizardMapRef.getZoom();

                                addWidgetWizardMapRef.setView(L.latLng(LatPos, LongPos), Zoom);
                            }

                        },
                        error: function (data)
                        {
                            console.log("ERROR in retrieving GeoData by Km4City SmartCity API: " + JSON.stringify(data));
                        }
                    });
                    $(this).attr('data-selected', 'true');
                }  else if (instanceUri === "MyPOI") {
                    var myPOIParameters = $(this).attr('data-parameters');
                    if (myPOIParameters === null || myPOIParameters === undefined) {
                        myPOIId = "All";
                    } else if (myPOIParameters != "All") {
                        myPOIId = myPOIParameters.split("datamanager/api/v1/poidata/")[1];
                    } else {
                        myPOIId = myPOIParameters;
                    }

                    $.ajax({
                        url: "../controllers/myPOIProxy.php",
                        type: "GET",
                        data: {
                            myPOIId: myPOIId,
                        },
                        async: true,
                        dataType: 'json',
                        uniqueNameId: uniqueNameId,
                        success: function(geoData)
                        {
                            if(!geoData) return;
							var fatherNode = null;
							if (geoData.hasOwnProperty("BusStop"))
                            {
                                fatherNode = geoData.BusStop;
                            } else
                            {
								if (geoData.hasOwnProperty("Sensor"))
                                {
                                    fatherNode = geoData.Sensor;
                                } else
                                {
                                    //Prevedi anche la gestione del caso in cui non c'è nessuna di queste tre, sennò il widget rimane appeso.
                                    fatherNode = geoData;
                                }
                            }

                            gisLayersOnMap[serviceType] = L.geoJSON(fatherNode, {
                                pointToLayer: addWidgetWizardCreateCustomMarker
                            }).addTo(addWidgetWizardMapRef);

                        },
                        error: function (data)
                        {
                            console.log("ERROR in retrieving GeoData by Km4City SmartCity API: " + JSON.stringify(data));
                        }
                    });
                    $(this).attr('data-selected', 'true');
                } else if (instanceUri === "MyKPI") {
                    var myKPIParameters = $(this).attr('data-parameters');
                  /*  var myKPIId = myKPIParameters.split("__")[0];
                    var myKPIlat = myKPIParameters.split("__")[1].split(";")[0];
                    var myKPIlng = myKPIParameters.split("__")[1].split(";")[1];    */
                    if (myKPIParameters.includes("datamanager/api/v1/poidata/")) {
                        var myKPIId = myKPIParameters.split("datamanager/api/v1/poidata/")[1];
                    } else {
                        var myKPIId = myKPIParameters;
                    }
                    $.ajax({
                        url: "../controllers/myPOIProxy.php",
                        type: "GET",
                        data: {
                            myPOIId: myKPIId,
                        },
                        async: true,
                        dataType: 'json',
                        uniqueNameId: uniqueNameId,
                        success: function (geoData) {
                            if(!geoData) return;
							var fatherNode = null;
							if (geoData.hasOwnProperty("BusStop")) {
                                fatherNode = geoData.BusStop;
                            } else {
								if (geoData.hasOwnProperty("Sensor")) {
                                    fatherNode = geoData.Sensor;
                                } else {
                                    //Prevedi anche la gestione del caso in cui non c'è nessuna di queste tre, sennò il widget rimane appeso.
                                    fatherNode = geoData;
                                }
                            }

                            gisLayersOnMap[serviceType] = L.geoJSON(fatherNode, {
                                pointToLayer: addWidgetWizardCreateCustomMarker
                            }).addTo(addWidgetWizardMapRef);
                            if (FreezeMap !== true) {
                                addWidgetWizardMapRef.setView(L.latLng(fatherNode.geometry.coordinates[1], fatherNode.geometry.coordinates[0]), addWidgetWizardMapRef.getZoom());
                            }

                        },
                        error: function (data) {
                            console.log("ERROR in retrieving GeoData by Km4City SmartCity API: " + JSON.stringify(data));
                        }
                    });
                //    }
                    $(this).attr('data-selected', 'true');
                } else if (instanceUri === "MyData") {
                    var myDataParameters = $(this).attr('data-parameters');
                    /*  var myKPIId = myKPIParameters.split("__")[0];
                      var myKPIlat = myKPIParameters.split("__")[1].split(";")[0];
                      var myKPIlng = myKPIParameters.split("__")[1].split(";")[1];    */
                    if (myDataParameters.includes("datamanager/api/v1/poidata/")) {
                        var myDataId = myDataParameters.split("datamanager/api/v1/poidata/")[1];
                        $.ajax({
                            url: "../controllers/myPOIProxy.php",
                            type: "GET",
                            data: {
                                myPOIId: myDataId,
                            },
                            async: true,
                            dataType: 'json',
                            uniqueNameId: uniqueNameId,
                            success: function (geoData) {
                                if(!geoData) return;
								var fatherNode = null;
								if (geoData.hasOwnProperty("BusStop")) {
                                    fatherNode = geoData.BusStop;
                                } else {
									if (geoData.hasOwnProperty("Sensor")) {
                                        fatherNode = geoData.Sensor;
                                    } else {
                                        //Prevedi anche la gestione del caso in cui non c'è nessuna di queste tre, sennò il widget rimane appeso.
                                        fatherNode = geoData;
                                    }
                                }

                                gisLayersOnMap[serviceType] = L.geoJSON(fatherNode, {
                                    pointToLayer: addWidgetWizardCreateCustomMarker
                                }).addTo(addWidgetWizardMapRef);

                            },
                            error: function (data) {
                                console.log("ERROR in retrieving GeoData by Km4City SmartCity API: " + JSON.stringify(data));
                            }
                        });
                    }
                    $(this).attr('data-selected', 'true');
                } else if (latitudeWiz != null && latitudeWiz != undefined && longitudeWiz != null && longitudeWiz != undefined) {

                    //    var latlngForMarker = "[" + latitudeWiz + ", "

                    var mapPinImg = '../img/gisMapIcons/generic.png';
                    var markerIcon = L.icon({
                        iconUrl: mapPinImg,
                        iconAnchor: [16, 37]
                    });

                    var genericMarker = L.marker([latitudeWiz, longitudeWiz], {icon: markerIcon}).addTo(addWidgetWizardMapRef);
                    //    L.marker([60.170437, 24.938215]).addTo(addWidgetWizardMapRef);
                    $(this).attr('data-selected', 'true');
                    addWidgetWizardMapMarkers[$(this).attr('data-rowid')] = genericMarker;
                    if (FreezeMap !== true) {
                        addWidgetWizardMapRef.setView(L.latLng(latitudeWiz, longitudeWiz), addWidgetWizardMapRef.getZoom());
                    }
                }
            }
            else
            {
                var stopFlag = 1;
                $(this).attr('data-selected', 'false');
                try
                {
                    if (instanceUri != null && instanceUri != undefined) {	// VERIFICA CON INSPECTOR !
                        if (instanceUri == "any" || (instanceUri.toLowerCase() == "mypoi" && $(this).attr("data-sub_nature") == "Any")) {
                            if ($(this).attr("data-high_level_type") == "Special Widget") {
                                clearMarker(currentMarkerId);
                            } else {
                                gisLayersOnMap[serviceType].clearLayers();
                            }
                        } else {
                            clearMarker(currentMarkerId);
                        }
                    } else {
                        clearMarker(currentMarkerId);
                    }
                }
                catch(e)
                {
                    console.log("Colta eccezione mappa: " + e.message);
                }
            }

            updateIconsFromSelectedRows();
            if (($('.addWidgetWizardIconClickClass[data-selected="true"]').attr("data-mainwidget") == "widgetBarSeries" || $('.addWidgetWizardIconClickClass[data-selected="true"]').attr("data-mainwidget") == "widgetCurvedLineSeries" || $('.addWidgetWizardIconClickClass[data-selected="true"]').attr("data-mainwidget") == "widgetPieChart") && ObjectSize(widgetWizardSelectedRows) > 1) {
                // if ((wizardSelectedRowsObj[Object.keys(wizardSelectedRowsObj)[Object.keys(wizardSelectedRowsObj).length -1]].value_unit == wizardSelectedRowsObj[Object.keys(wizardSelectedRowsObj)[0]].value_unit)) {
                // OK
                //   } else {
                // custom check if widgetBarSeries: check if current selected rows has the same value_unit of first selected row. If not, mark current selected row as incompatible
                updateBarSeriesWidgetSelectedRows(widgetWizardSelectedRows, Object.keys(widgetWizardSelectedRows)[0]);
                //   }
            } else {
                updateWidgetCompatibleRows();
            }
        });
		<?php } // end of handler for row click in synMode ?>

        //Flusso main ************************************************************************

        //Associazione del click del bottone di reset filtro alla funzione corrispondente
        $("#resetButton").click(resetFilter);

        //Creazione mappa e riarrangiamento a bruta forza delle opzioni di tabella in testa alla stessa nel div #widgetWizardTableCommandsContainer
        setTimeout(function () {
            var fatherGeoJsonNode = null;
            var addWidgetWizardMapDiv = "addWidgetWizardMapCnt2";

            $("#link_start_wizard").click(function ()
            {
                choosenWidgetIconName = null;
                widgetWizardSelectedRows = {};
                widgetWizardSelectedRowsTable.clear().draw(false);
            });
            
            choosenWidgetIconName = null;					// VERIFICA CON INSPECTOR !
            
            <?php if(!$synMode || isCreationWizardUI()) { ?>widgetWizardSelectedRows = {}; widgetWizardSelectedRowsTable.clear().draw(false);<?php } ?>

            <?php if(isCreationWizardUI()):?>
            $("#addWidgetWizard").on('shown.bs.modal', function () {	// VERIFICA CON INSPECTOR !
                <?php endif; ?>
                if($('#dataAndWidgets').is(':visible'))
                {
                    try
                    {
                        if ((orgGpsCentreLatLng == null) || (orgGpsCentreLatLng == '')) {
                            addWidgetWizardMapRef = L.map(addWidgetWizardMapDiv).setView(L.latLng(43.769710, 11.255751), 11);
                        } else {
                         //   var setOrgViewLatLng = "[" + orgGpsCentreLatLng + "]";
                            var orgLat = orgGpsCentreLatLng.split(",")[0];
                            var orgLng = orgGpsCentreLatLng.split(",")[1];
                            if ((orgZoomLevel != null) && (orgZoomLevel != '')) {
                                addWidgetWizardMapRef = L.map(addWidgetWizardMapDiv).setView(L.latLng(orgLat, orgLng), orgZoomLevel);
                            } else {
                                addWidgetWizardMapRef = L.map(addWidgetWizardMapDiv).setView(L.latLng(orgLat, orgLng), 11);
                            }
                        } 

                        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                            attribution: '&copy; <a href="https://openstreetmap.org">OpenStreetMap</a> contributors',
                            maxZoom: 18,
                            closePopupOnClick: false
                        }).addTo(addWidgetWizardMapRef);
                        addWidgetWizardMapRef.attributionControl.setPrefix('');
                    } catch (e)
                    {
                        console.log("Mappa già istanziata");
                    }
                }
                
                $('.nav-tabs a[href="#dataAndWidgets"]').on('shown.bs.tab', function () 
                {
                    selectedTabIndex = 1;
                    if(location.href.includes("dashboard_configdash.php"))
                    {
                        $('#addWidgetWizardPrevBtn').addClass('disabled');
                    }
                    else
                    {
                        $('#addWidgetWizardPrevBtn').removeClass('disabled');
                    }
                    $('#addWidgetWizardNextBtn').removeClass('disabled');
                    
                    //Gestione pulsanti prev e next
                    $('#addWidgetWizardPrevBtn').off('click');
                    $('#addWidgetWizardPrevBtn').click(function()
                    {
                        if(selectedTabIndex > firstTabIndex)
                        {
                            $('.nav-tabs > .active').prev('li').find('a').trigger('click');
                         //   $('#bTab').hide();

                        }
                    });

                    $('#addWidgetWizardNextBtn').off('click');
                    $('#addWidgetWizardNextBtn').click(function()
                    {
                        if(selectedTabIndex < parseInt(tabsQt - 1))
                        {
                            $('.nav-tabs > .active').next('li').find('a').trigger('click');
                        }
                    });
                    
                    checkTab1Conditions();
                    
                    try
                    {
                        //addWidgetWizardMapRef = L.map(addWidgetWizardMapDiv).setView(L.latLng(43.769710, 11.255751), 11);

                        if ((orgGpsCentreLatLng == null) || (orgGpsCentreLatLng == '')) {
                            addWidgetWizardMapRef = L.map(addWidgetWizardMapDiv).setView(L.latLng(43.769710, 11.255751), 11);
                        } else {
                         //   var setOrgViewLatLng = "[" + orgGpsCentreLatLng + "]";
                            var orgLat = orgGpsCentreLatLng.split(",")[0];
                            var orgLng = orgGpsCentreLatLng.split(",")[1];
                            if ((orgZoomLevel != null) && (orgZoomLevel != '')) {
                                addWidgetWizardMapRef = L.map(addWidgetWizardMapDiv).setView(L.latLng(orgLat, orgLng), orgZoomLevel);
                            } else {
                                addWidgetWizardMapRef = L.map(addWidgetWizardMapDiv).setView(L.latLng(orgLat, orgLng), 11);
                            }
                        }

                        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                            attribution: '&copy; <a href="https://openstreetmap.org">OpenStreetMap</a> contributors',
                            maxZoom: 18,
                            closePopupOnClick: false
                        }).addTo(addWidgetWizardMapRef);
                        addWidgetWizardMapRef.attributionControl.setPrefix('');
                    } catch (e)
                    {
                        console.log("Mappa già istanziata");
                    }
                });
                <?php if(isCreationWizardUI()):?>
            });
            <?php endif; ?>

            //Riarrangiamento a bruta forza delle opzioni di tabella in testa alla stessa nel div #widgetWizardTableCommandsContainer
            $("#widgetWizardTable_paginate").appendTo("#widgetWizardTableCommandsContainer");
            $("#widgetWizardTable_paginate").addClass("col-xs-12");
            $("#widgetWizardTable_paginate").addClass("col-md-4");
            $('#widgetWizardTable_filter').appendTo("#widgetWizardTableCommandsContainer");
            $("#widgetWizardTable_filter").addClass("col-xs-12");
            $("#widgetWizardTable_filter").addClass("col-md-3");
            $("#widgetWizardTable_filter input").attr("placeholder", "Search");
            $("#widgetWizardTable_paginate .pagination").css("margin-top", "0px !important");
            $("#widgetWizardTable_paginate .pagination").css("margin-bottom", "0px !important");

            $("#widgetWizardSelectedRowsTable_paginate").appendTo("#widgetWizardSelectedRowsTableCommandsContainer");
            $("#widgetWizardSelectedRowsTable_paginate").addClass("col-xs-12");
            $("#widgetWizardSelectedRowsTable_paginate").addClass("col-md-4");
            $("#widgetWizardSelectedRowsTable_paginate").addClass("col-md-offset-5");
            $('#widgetWizardSelectedRowsTable_filter').appendTo("#widgetWizardSelectedRowsTableCommandsContainer");
            $("#widgetWizardSelectedRowsTable_filter").addClass("col-xs-12");
            $("#widgetWizardSelectedRowsTable_filter").addClass("col-md-3");
			//$("#widgetWizardSelectedRowsTable_paginate").addClass("col-md-offset-1");
            $("#widgetWizardSelectedRowsTable_filter input").attr("placeholder", "Search");
            $("#widgetWizardSelectedRowsTable_paginate .pagination").css("margin-top", "0px !important");
            $("#widgetWizardSelectedRowsTable_paginate .pagination").css("margin-bottom", "0px !important");			
        }, 750);
        
        //Distinzione fra caso inclusione in dashboard_configdash.php e inclusione in dashboards.php
        //Caso dashboard_configdash.php
        if(location.href.includes("dashboard_configdash"))
        {
            console.log("Creazione widgets");
            $('#addWidgetWizardConfirmBtn').click(function ()
            {
                //Mandiamo solo le selected rows compatibili
                var widgetWizardSelectedRowsCompatible = {};
                
                for(var key in widgetWizardSelectedRows)
                {
                    if(widgetWizardSelectedRows[key].widgetCompatible)
                    {
                        widgetWizardSelectedRowsCompatible[key] = widgetWizardSelectedRows[key];
                    }
                }
                
                $('#modalAddWidgetWizardAvailabilityMsg').hide();
                widgetWizardMapSelection = addWidgetWizardMapRef.getBounds().getSouthWest().lat + ";" + addWidgetWizardMapRef.getBounds().getSouthWest().lng + ";" + addWidgetWizardMapRef.getBounds().getNorthEast().lat + ";" + addWidgetWizardMapRef.getBounds().getNorthEast().lng;

                $.ajax({
                    url: "../controllers/widgetAndDashboardInstantiator.php",
                    data: {
                        operation: "addWidget",
                        dashboardId: "<?php if (isset($_REQUEST['dashboardId'])) {echo escapeForJS($_REQUEST['dashboardId']);} else {echo 1;} ?>",
                        dashboardAuthorName: "<?php if (isset($_REQUEST['dashboardAuthorName'])){echo escapeForJS($_REQUEST['dashboardAuthorName']);} else {echo 1;} ?>",
                        dashboardEditorName: "<?php if (isset($_REQUEST['dashboardEditorName'])){echo escapeForJS($_REQUEST['dashboardEditorName']);}else{echo 1;} ?>",
                        dashboardTitle: '<?php if (isset($_REQUEST['dashboardTitle'])){echo escapeForJS($_REQUEST['dashboardTitle']);}else{echo 1;} ?>',
                     //   dashboardTitle: dashTitleEscaped,
                        widgetType: choosenWidgetIconName,
                        actuatorTargetWizard: $('#actuatorTargetWizard').val(),
                        actuatorTargetInstance: $('#actuatorTargetInstance').val(),
                        actuatorEntityName: $('#actuatorEntityName').val(),
                        actuatorValueType: $('#actuatorValueType').val(),
                        actuatorMinBaseValue: $('#actuatorMinBaseValue').val(),
                        actuatorMaxImpulseValue: $('#actuatorMaxImpulseValue').val(),
                        widgetWizardSelectedRows: widgetWizardSelectedRowsCompatible,
                        selection: widgetWizardMapSelection,
                        mapCenterLat: addWidgetWizardMapRef.getCenter().lat,
                        mapCenterLng: addWidgetWizardMapRef.getCenter().lng,
                        mapZoom: addWidgetWizardMapRef.getZoom()
                    },
                    type: "POST",
                    async: true,
                    //dataType: 'json',
                    success: function (data)
                    {
                        if(data === 'Ok')
                        {
                            location.reload();
                        } else
                        {
                            alert("Error during dashboard update, please try again");
                            console.log(data);
                        }
                    },
                    error: function (errorData)
                    {
                        alert("Error during dashboard update, please try again");
                        console.log(errorData);
                    }
                });
            });
        }
        else//Caso dashboards.php
        {
            console.log("Creazione dashboard");
            
            $('.modalAddDashboardWizardChoiceCnt').click(function(i)
            {
                //In ogni caso nascondiamo campi per attuatori new e mostriamo tabelle
                $('#actuatorTargetCell .wizardActLbl').hide();
                $('#actuatorTargetCell .wizardActInputCnt').hide();
                $('#actuatorEntityNameCell .wizardActLbl').hide();
                $('#actuatorEntityNameCell .wizardActInputCnt').hide();
                $('#actuatorValueTypeCell .wizardActLbl').hide();
                $('#actuatorValueTypeCell .wizardActInputCnt').hide();
                $('#actuatorMinBaseValueCell .wizardActLbl').hide();
                $('#actuatorMinBaseValueCell .wizardActInputCnt').hide();
                $('#actuatorMaxBaseValueCell .wizardActLbl').hide();
                $('#actuatorMaxBaseValueCell .wizardActInputCnt').hide();
                $('#actuatorTargetWizard').val(-1);
                $('#actuatorEntityName').val('');
                $('#actuatorValueType').val('');
                $('#actuatorMinBaseValue').val('');
                $('#actuatorMaxImpulseValue').val('');
                $('#widgetWizardActuatorFieldsRow').hide();
                $('.hideIfActuatorNew').show();

                if($(this).attr('data-widgettype') === 'none') {
                    $('#dashboardDirectStatus').val('yes');
                 //   $('#dataAndWidgets').hide();
                    $('#bTab').hide();
                } else {
                    $('#dashboardDirectStatus').val('no');
                    $('#bTab').show();
                }
                
                //In ogni caso leviamo il bordino di widget selezionato a quello che eventualmente ce l'ha e deselezioniamola
                $('.addWidgetWizardIconClickClass[data-selected=true]').css('border', 'none');
                $('.addWidgetWizardIconClickClass[data-selected=true]').attr('data-selected', 'false');
                
                $('#wizardNotCompatibleRowsAlert').hide();
                
                if($(this).attr('data-selected') === 'false')
                {
                    if($(this).attr('data-widgettype') !== 'any')       // Aggiungere il caso "none" per dashboard empty ??
                    {
                        $('.addWidgetWizardIconsCnt').hide();
                        $('.dashTemplateHide').hide();
                    }
                    else
                    {
                        $('.addWidgetWizardIconsCnt').show();
                        $('.dashTemplateHide').show();
                    }

                    $('.modalAddDashboardWizardChoiceCnt').attr('data-selected', 'false');
                    $('.modalAddDashboardWizardChoiceCnt').removeClass('modalAddDashboardWizardChoiceCntSelected');
                    $(this).attr('data-selected', 'true');
                    $(this).addClass('modalAddDashboardWizardChoiceCntSelected');
                    choosenDashboardTemplateName = $(this).attr('data-templatename');
                    choosenDashboardTemplateIcon = $(this).attr('data-widgettype');
                    $('#dashboardTemplateStatus').val('ok');
                    
                    if(($('#dashboardTemplateStatus').val() === 'ok')&&($('#inputTitleDashboardStatus').val() === 'ok'))
                    {
                        $('#bTab a').attr("data-toggle", "tab");
                        $('#addWidgetWizardNextBtn').removeClass('disabled');
                    /*    if ($(this)[0].innerText == "Empty Dashboard Empty dashboard"){
                            $('#cTab a').attr("data-toggle", "tab");
                        } else {
                            $('#cTab a').attr("data-toggle", "no");
                        }*/
                    }
                    else
                    {
                        $('#bTab a').attr("data-toggle", "no");
                        if ($('#modalAddDashboardWizardTemplateMsg')[0].outerText != "Template choosen OK" || $('#modalAddDashboardWizardTitleAlreadyUsedMsg')[0].outerText != "Dashboard title OK") {
                            $('#addWidgetWizardNextBtn').addClass('disabled');
                        }
                    }
                    
                    $('#modalAddDashboardWizardTemplateMsg').css("color", "white");
                    $('#modalAddDashboardWizardTemplateMsg div.col-xs-12').html('<? _("Template choosen OK")?>');
                    
                    //Qui dentro c'è la logica che preseleziona high_level_type, nature... in base al template di dashboard desiderato
                    resetFilter();
                    
                    //Selezione del tipo di widget
                    if($(this).attr("data-widgetType") !== 'any')
                    {
                        //Selezioniamo direttamente noi il tipo di widget, col click programmatico non ce la fa col tempo
                        choosenWidgetIconName = $(this).attr("data-widgetType");
                        $('.addWidgetWizardIconClickClass[data-iconname="' + $(this).attr("data-widgetType") + '"]').attr('data-selected', true);
                        validityConditions.widgetTypeSelected = true;
                    }
                    
                    validityConditions.dashTemplateSelected = true;

                    

                    /*if($(this).attr("data-highLevelTypeVisible") === 'true')
                    {
                        widgetWizardTable.column(0).visible(true);
                    }
                    else
                    {
                        widgetWizardTable.column(0).visible(false);
                    }
                    
                    if($(this).attr("data-natureVisible") === 'true')
                    {
                        widgetWizardTable.column(1).visible(true);
                    }
                    else
                    {
                        widgetWizardTable.column(1).visible(false);
                    }
                    
                    if($(this).attr("data-subnatureVisible") === 'true')
                    {
                        widgetWizardTable.column(2).visible(true);
                    }
                    else
                    {
                        widgetWizardTable.column(2).visible(false);
                    }
                    
                    if($(this).attr("data-valueTypeVisible") === 'true')
                    {
                        widgetWizardTable.column(3).visible(false);
                    }
                    else
                    {
                        widgetWizardTable.column(3).visible(false);
                    }
                    
                    if($(this).attr("data-valueNameVisible") === 'true')
                    {
                        
                        widgetWizardTable.column(4).visible(false);
                    }
                    else
                    {
                        widgetWizardTable.column(4).visible(false);
                    }
                    
                    if($(this).attr("data-dataTypeVisible") === 'true')
                    {
                        widgetWizardTable.column(11).visible(true);
                    }
                    else
                    {
                        widgetWizardTable.column(11).visible(false);
                    }
                    
                    if($(this).attr("data-lastDateVisible") === 'true')
                    {
                        widgetWizardTable.column(8).visible(true);
                    }
                    else
                    {
                        widgetWizardTable.column(8).visible(false);
                    }
                    
                    if($(this).attr("data-lastValueVisible") === 'true')
                    {
                        widgetWizardTable.column(9).visible(true);
                    }
                    else
                    {
                        widgetWizardTable.column(9).visible(false);
                    }
                    
                    if($(this).attr("data-healthinessVisible") === 'true')
                    {
                        widgetWizardTable.column(10).visible(true);
                    }
                    else
                    {
                        widgetWizardTable.column(10).visible(false);
                    }
                    
                    if($(this).attr("data-lastCheckVisible") === 'true')
                    {
                        widgetWizardTable.column(14).visible(true);
                    }
                    else
                    {
                        widgetWizardTable.column(14).visible(false);
                    }

                    if($(this).attr("data-valueUnitVisible") === 'true')
                    {
                        widgetWizardTable.column(17).visible(true);
                    }
                    else
                    {
                        widgetWizardTable.column(17).visible(false);
                    }

                    if($(this).attr("data-ownershipVisible") === 'true')
                    {
                        widgetWizardTable.column(16).visible(true);
                    }
                    else
                    {
                        widgetWizardTable.column(16).visible(false);
                    }
                    widgetWizardTable.column(20).visible(false);
                    */
                    
                    if(($(this).attr('data-highlevelsel').split('|').length > 1) || ($(this).attr('data-highlevelsel') === 'any'))
                    {
                        $('#highLevelTypeColumnFilter').show();
                    }
                    else
                    {
                        $('#highLevelTypeColumnFilter').hide();
                    }
                    
                }
                else
                {
                    if($(this).attr('data-widgettype') === 'none') {
                       // $('#bTab').show();
                    }
                    $('.modalAddDashboardWizardChoiceCnt').attr('data-selected', 'false');
                    $('.modalAddDashboardWizardChoiceCnt').removeClass('modalAddDashboardWizardChoiceCntSelected');
                    $(this).attr('data-selected', 'false');
                    $(this).removeClass('modalAddDashboardWizardChoiceCntSelected');
                    choosenDashboardTemplateName = null;
                    choosenDashboardTemplateIcon = null;
                    choosenWidgetIconName = null;
                    $('.addWidgetWizardIconClickClass').attr('data-selected', false);
                    $('.addWidgetWizardIconsCnt').show();
                    $('.dashTemplateHide').show();
                    $('#dashboardTemplateStatus').val('empty');
                    
                    if(($('#dashboardTemplateStatus').val() === 'ok')&&($('#inputTitleDashboardStatus').val() === 'ok'))
                    {
                        $('#bTab a').attr("data-toggle", "tab");
                        $('#addWidgetWizardNextBtn').removeClass('disabled');
                    }
                    else
                    {
                        $('#bTab a').attr("data-toggle", "no");
                        $('#addWidgetWizardNextBtn').addClass('disabled');
                    }
                    
                    $('#modalAddDashboardWizardTemplateMsg').css("color", "rgb(243, 207, 88)");
                    $('#modalAddDashboardWizardTemplateMsg div.col-xs-12').html('<? _("You must choose one template")?>');
                    
                    //resetFilterForced();
                    validityConditions.dashTemplateSelected = false;
                    validityConditions.widgetTypeSelected = false;
                    checkActuatorFieldsEmpty();
                    checkAtLeastOneRowSelected();
                    checkBrokerAndNrRowsTogether();
                    switch($('#inputTitleDashboardStatus').val())
                    {
                        case 'empty':
                            $('#wrongConditionsDiv').append('<div class="col-xs-12 titleAlert"><div class="col-xs-12 centerWithFlex"><i class="fa fa-exclamation-triangle validityConditionIcon"></i></div><div class="col-xs-12 centerWithFlex"><span class="validityConditionLbl"><?php echo _("Dashboard title can\'t be empty"); ?></span></div></div>');
                            validityConditions.canProceed = false;
                            break;

                        case 'alreadyUsed':
                            $('#wrongConditionsDiv').append('<div class="col-xs-12 titleAlert"><div class="col-xs-12 centerWithFlex"><i class="fa fa-exclamation-triangle validityConditionIcon"></i></div><div class="col-xs-12 centerWithFlex"><span class="validityConditionLbl"><?php echo _("Dashboard title already in use"); ?></span></div></div>');
                            validityConditions.canProceed = false;
                            break;
                            
                        case 'tooLong':
                            $('#wrongConditionsDiv').append('<div class="col-xs-12 titleAlert"><div class="col-xs-12 centerWithFlex"><i class="fa fa-exclamation-triangle validityConditionIcon"></i></div><div class="col-xs-12 centerWithFlex"><span class="validityConditionLbl"><?php echo _("Dashboard title longer than 300 chars"); ?></span></div></div>');
                            validityConditions.canProceed = false;
                            break;    

                        default:
                            $('.titleAlert').remove();
                            break;
                    }
                    
                    widgetWizardTable.column(0).visible(true);
                    widgetWizardTable.column(1).visible(true);
                    widgetWizardTable.column(2).visible(true);
                    widgetWizardTable.column(3).visible(true);
                    widgetWizardTable.column(4).visible(true);
                    widgetWizardTable.column(11).visible(true);
					widgetWizardTable.column(8).visible(true);
                    widgetWizardTable.column(9).visible(true);
                    
                    widgetWizardTable.column(10).visible(true);
                    widgetWizardTable.column(14).visible(true);
                    widgetWizardTable.column(16).visible(true);
                    //widgetWizardTable.column(17).visible(true);
                }

            });
            
            $('#addWidgetWizardConfirmBtn').click(function ()
            {
                $.ajax({
                    url: "../controllers/checkDashboardLimits.php",
                    data:
                        {

                        },
                    type: "POST",
                    async: true,
                    dataType: 'json',
                    success: function (data) {
                        if (data.detail === 'DashboardLimitsOk') {

                            var myMapCenterLat, myMapCenterLng, myMapZoom = null;

                            //Mandiamo solo le selected rows compatibili
                            var widgetWizardSelectedRowsCompatible = {};

                            for(var key in widgetWizardSelectedRows)
                            {
                                if(widgetWizardSelectedRows[key].widgetCompatible)
                                {
                                    widgetWizardSelectedRowsCompatible[key] = widgetWizardSelectedRows[key];
                                }
                            }

                            if(((choosenDashboardTemplateName !== 'emptyDashboard')&&(choosenDashboardTemplateName !== 'fullyCustom'))||((choosenDashboardTemplateName === 'fullyCustom')&&(validityConditions.widgetTypeSelected)&&(validityConditions.atLeastOneRowSelected)))
                            {
                                myMapCenterLat = addWidgetWizardMapRef.getCenter().lat;
                                myMapCenterLng = addWidgetWizardMapRef.getCenter().lng;
                                myMapZoom = addWidgetWizardMapRef.getZoom();
                            }

                            $('#modalAddWidgetWizardAvailabilityMsg').hide();

                            $.ajax({
                                url: "../controllers/widgetAndDashboardInstantiator.php",
                                data: {
                                    operation: "addDashboard",
                                    dashboardTemplate: choosenDashboardTemplateName,
                                    dashboardTitle: $('#inputTitleDashboard').val(),
                                    //   dashboardTitle: dashTitleEscaped,
                                    dashboardAuthorName: "<?php if (isset($_SESSION['loggedUsername'])){echo $_SESSION['loggedUsername'];} else {echo 1;} ?>",
                                    dashboardEditorName: "<?php if (isset($_SESSION['loggedUsername'])){echo $_SESSION['loggedUsername'];}else{echo 1;} ?>",
                                    widgetType: choosenWidgetIconName,
                                    actuatorTargetWizard: $('#actuatorTargetWizard').val(),
                                    actuatorTargetInstance: $('#actuatorTargetInstance').val(),
                                    actuatorEntityName: $('#actuatorEntityName').val(),
                                    actuatorValueType: $('#actuatorValueType').val(),
                                    actuatorMinBaseValue: $('#actuatorMinBaseValue').val(),
                                    actuatorMaxImpulseValue: $('#actuatorMaxImpulseValue').val(),
                                    widgetWizardSelectedRows: widgetWizardSelectedRowsCompatible,
                                    selection: widgetWizardMapSelection,
                                    mapCenterLat: myMapCenterLat,
                                    mapCenterLng: myMapCenterLng,
                                    mapZoom: myMapZoom
                                },
                                type: "POST",
                                async: true,
                                dataType: 'json',
                                success: function (data)
                                {
                                    if(data.detail === 'Ok')
                                    {
                                        if(data['detail'] === 'Ok')
                                        {
                                            location.href = "dashboards.php?linkId=dashboardsLink&newDashId=" + data['newDashId'] + "&newDashAuthor=" + "<?= $_SESSION['loggedUsername'] ?>" + "&newDashTitle=" + encodeURI($('#inputTitleDashboard').val());
                                        }
                                        else
                                        {
                                            alert('<? _("Error during dashboard creation, please try again"); ?>');
                                        }
                                    }
                                    else
                                    {
                                        alert('<? _("Error during dashboard creation, please try again"); ?>');
                                    }
                                },
                                error: function(errorData)
                                {
                                    console.log("Error: " + errorData.callResult);
                                    alert('<? _("Error during dashboard creation, please try again"); ?>');
                                }
                            });
                        }
                        else {
                            $('#modalCheckDashLimits').modal('show');
                            $('#limitsDashKoMsg').show();
                            console.log("Dashboard Limits Exceeded.");
                            setTimeout(function () {
                                $('#limitsDashKoMsg').show();
                                $('#checkDashLimitsModalBody').modal('hide');
                            }, 2500);
                        }
                    },
                    error: function (errorData) {
                        console.log("Limits over!");
                        $('#modalCheckDashLimits').modal('show');
                        $('#limitsDashKoMsg').show();
                        console.log("Dashboard Limits Exceeded.");
                        setTimeout(function () {
                            $('#limitsDashKoMsg').show();
                            $('#checkDashLimitsModalBody').modal('hide');
                        }, 2500);
                    }
                });

            });
        }
        
        //Gestione pulsanti prev e next
        $('#addWidgetWizardPrevBtn').addClass('disabled');
        $('#addWidgetWizardNextBtn').addClass('disabled');
        
        $('#addWidgetWizardPrevBtn').off('click');
        $('#addWidgetWizardPrevBtn').click(function()
        {
            if(selectedTabIndex > firstTabIndex)
            {
                $('.nav-tabs > .active').prev('li').find('a').trigger('click');
            }
        });

        $('#addWidgetWizardNextBtn').off('click');
        $('#addWidgetWizardNextBtn').click(function()
        {
            if(selectedTabIndex < parseInt(tabsQt - 1))
            {
                switch(selectedTabIndex)
                {
                    case 0:
                        if(($('#dashboardTemplateStatus').val() === 'ok')&&($('#inputTitleDashboardStatus').val() === 'ok'))
                        {
                            if($('#dashboardDirectStatus').val() === 'yes') {
                                $('#cTab a').attr("data-toggle", "tab");
                                $('.nav-tabs > .active').next('li').next('li').find('a').trigger('click');
                            } else {
                                $('.nav-tabs > .active').next('li').find('a').trigger('click');
                            }
                        }
                        if (loggedRole == "RootAdmin") {
                            $('.dt').hide();
                        }
                        break;
                        
                    case 1:
                        $('.nav-tabs > .active').next('li').find('a').trigger('click');
                        break;

                    case 2:
                        $('.nav-tabs > .active').next('li').find('a').trigger('click');
                        break;
                        
                }
            }
        });
        
        $('#addWidgetWizard').on('hidden.bs.modal', function () 
        {
            if(location.href.includes("dashboard_configdash"))
            {
                //Ritorno al primo tab
                $('#bTab a').trigger('click');
                
                //Deselect del widget selezionato (sennò con attuatori resetFilter e basta non sembra funzionare)
                $('.addWidgetWizardIconClickClass[data-selected="true"]').trigger('click');
                
                //Reset tab widgets
                resetFilter();
            }
            else
            {
                //Ritorno al primo tab
                $('#aTab a').trigger('click');

                //Reset tab general properties dashboard (la cascata di eventi resetta il tab centrale)
                $('#inputTitleDashboard').val('');
                $('#inputTitleDashboard').trigger('input');
                $('.modalAddDashboardWizardChoiceCnt[data-selected="true"]').trigger('click');
                
                //widgetWizardTable.search('').draw();
                //widgetWizardSelectedRowsTable.search('').draw();
            }
            
            //Reset campi custom attuatori
            $('#actuatorEntityName').val('');
            $('#actuatorValueType').val('');
            
            //Rimozione avviso righe incompatibili
            $('#wizardNotCompatibleRowsAlert').hide();
        });
		
		$('#save').on('click',function(){
			var sel = []; 
			widgetWizardSelectedRowsTable.rows().every( function ( rowIdx, tableLoop, rowLoop ) { console.log(this.data()[15]); sel.push(this.data()[15]); } );
			$.ajax({
				url: "../controllers/dashboardWizardControllerOS.php",
				data: { doSynVarPresel: true, sel: sel.join(",") },
				success: function (data) { $("#wizardTab1MsgCnt").html("Selection saved."); },
				error: function (data) { $("#wizardTab1MsgCnt").html("Error! Your selection was not saved."); }
			});
		});
                
                                
                /////////
                 var filteredData = "";
                 var valuename_filter = "<?php echo $valuename_filter; ?>";
                 var type_filter = "<?php echo $type; ?>";
                 console.log('type_filter: '+ type_filter);
                 if (valuename_filter !==""){
                     if (type_filter ==="my-kpi"){
                         var valuename_filter0 = widgetWizardTable.column(15).search(valuename_filter, true, false).draw();
                     }else{
                        var valuename_filter0 = widgetWizardTable.column(4).search(valuename_filter).draw();
                    }
                        $('*[type="search"]').val(valuename_filter);
                    }
              
                /////////
                $( "#widgetWizardTable_filter" ).keypress(function() {
                        //console.log('OK');
                     if (valuename_filter !==""){
                         if (type_filter ==="my-kpi"){
                         var valuename_filter0 = widgetWizardTable.column(15).search('').draw();
                     }else{
                         var valuename_filter0 = widgetWizardTable.column(4).search('').draw();
                             
                         }
                    }
                  });
        
    });
</script>   
