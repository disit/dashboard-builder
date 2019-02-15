<?php
class WidgetDbRow 
{
    public $name_w; 
    public $id_dashboard; 
    public $id_metric; 
    public $type_w; 
    public $n_row; 
    public $n_column; 
    public $size_rows; 
    public $size_columns; 
    public $title_w; 
    public $color_w; 
    public $frequency_w; 
    public $temporal_range_w; 
    public $municipality_w; 
    public $infoMessage_w;
    public $link_w; 
    public $parameters; 
    public $frame_color_w; 
    public $udm; 
    public $udmPos; 
    public $fontSize; 
    public $fontColor; 
    public $controlsPosition; 
    public $showTitle; 
    public $controlsVisibility; 
    public $zoomFactor; 
    public $defaultTab; 
    public $zoomControlsColor; 
    public $scaleX; 
    public $scaleY; 
    public $headerFontColor; 
    public $styleParameters; 
    public $infoJson; 
    public $serviceUri; 
    public $viewMode; 
    public $hospitalList; 
    public $notificatorRegistered; 
    public $notificatorEnabled; 
    public $enableFullscreenTab; 
    public $enableFullscreenModal; 
    public $fontFamily; 
    public $entityJson; 
    public $attributeName; 
    public $creator; 
    public $lastEditor; 
    public $canceller; 
    public $lastEditDate; 
    public $cancelDate; 
    public $actuatorTarget; 
    public $actuatorEntity; 
    public $actuatorAttribute; 
    public $chartColor; 
    public $dataLabelsFontSize; 
    public $dataLabelsFontColor; 
    public $chartLabelsFontSize; 
    public $chartLabelsFontColor;
    public $sm_based;
    public $rowParameters;
    public $sm_field;
    public $wizardRowIds;
    public $icon;
    
    function getWizardRowIds() 
    {
        return $this->wizardRowIds;
    }
    
    function setWizardRowIds($wizardRowIds) 
    {
        $this->wizardRowIds = $wizardRowIds;
    }
    
    function getSm_field() 
    {
        return $this->sm_field;
    }
    
    function setSm_field($sm_field) 
    {
        $this->sm_field = $sm_field;
    }
    
    function getSm_based() 
    {
        return $this->sm_based;
    }

    function getRowParameters() 
    {
        return $this->rowParameters;
    }

    function setSm_based($sm_based) 
    {
        $this->sm_based = $sm_based;
    }

    function setRowParameters($rowParameters) 
    {
        $this->rowParameters = $rowParameters;
    }

    function returnManagedStringForDb($original)
    {
        if($original == NULL)
        {
            return "NULL";
        }
        else
        {
            return "'" . $original . "'";
        }
    }
    
    function returnManagedNumberForDb($original)
    {
        if($original == NULL)
        {
            return "NULL";
        }
        else
        {
            return $original;
        }
    }
    
    function getName_w() {
        return $this->name_w;
    }

    function getId_dashboard() {
        return $this->id_dashboard;
    }

    function getId_metric() {
        return $this->id_metric;
    }

    function getType_w() {
        return $this->type_w;
    }

    function getN_row() {
        return $this->n_row;
    }

    function getN_column() {
        return $this->n_column;
    }

    function getSize_rows() {
        return $this->size_rows;
    }

    function getSize_columns() {
        return $this->size_columns;
    }

    function getTitle_w() {
        return $this->title_w;
    }

    function getColor_w() {
        return $this->color_w;
    }

    function getFrequency_w() {
        return $this->frequency_w;
    }

    function getTemporal_range_w() {
        return $this->temporal_range_w;
    }

    function getMunicipality_w() {
        return $this->municipality_w;
    }

    function getInfoMessage_w() {
        return $this->infoMessage_w;
    }

    function getLink_w() {
        return $this->link_w;
    }

    function getParameters() {
        return $this->parameters;
    }

    function getFrame_color_w() {
        return $this->frame_color_w;
    }

    function getUdm() {
        return $this->udm;
    }

    function getUdmPos() {
        return $this->udmPos;
    }

    function getFontSize() {
        return $this->fontSize;
    }

    function getFontColor() {
        return $this->fontColor;
    }

    function getControlsPosition() {
        return $this->controlsPosition;
    }

    function getShowTitle() {
        return $this->showTitle;
    }

    function getControlsVisibility() {
        return $this->controlsVisibility;
    }

    function getZoomFactor() {
        return $this->zoomFactor;
    }

    function getDefaultTab() {
        return $this->defaultTab;
    }

    function getZoomControlsColor() {
        return $this->zoomControlsColor;
    }

    function getScaleX() {
        return $this->scaleX;
    }

    function getScaleY() {
        return $this->scaleY;
    }

    function getHeaderFontColor() {
        return $this->headerFontColor;
    }

    function getStyleParameters() {
        return $this->styleParameters;
    }

    function getInfoJson() {
        return $this->infoJson;
    }

    function getServiceUri() {
        return $this->serviceUri;
    }

    function getViewMode() {
        return $this->viewMode;
    }

    function getHospitalList() {
        return $this->hospitalList;
    }

    function getNotificatorRegistered() {
        return $this->notificatorRegistered;
    }

    function getNotificatorEnabled() {
        return $this->notificatorEnabled;
    }

    function getEnableFullscreenTab() {
        return $this->enableFullscreenTab;
    }

    function getEnableFullscreenModal() {
        return $this->enableFullscreenModal;
    }

    function getFontFamily() {
        return $this->fontFamily;
    }

    function getEntityJson() {
        return $this->entityJson;
    }

    function getAttributeName() {
        return $this->attributeName;
    }

    function getCreator() {
        return $this->creator;
    }

    function getLastEditor() {
        return $this->lastEditor;
    }

    function getCanceller() {
        return $this->canceller;
    }

    function getLastEditDate() {
        return $this->lastEditDate;
    }

    function getCancelDate() {
        return $this->cancelDate;
    }

    function getActuatorTarget() {
        return $this->actuatorTarget;
    }

    function getActuatorEntity() {
        return $this->actuatorEntity;
    }

    function getActuatorAttribute() {
        return $this->actuatorAttribute;
    }

    function getChartColor() {
        return $this->chartColor;
    }

    function getDataLabelsFontSize() {
        return $this->dataLabelsFontSize;
    }

    function getDataLabelsFontColor() {
        return $this->dataLabelsFontColor;
    }

    function getChartLabelsFontSize() {
        return $this->chartLabelsFontSize;
    }

    function getChartLabelsFontColor() {
        return $this->chartLabelsFontColor;
    }

    function setName_w($name_w) {
        $this->name_w = $name_w;
    }

    function setId_dashboard($id_dashboard) {
        $this->id_dashboard = $id_dashboard;
    }

    function setId_metric($id_metric) {
        $this->id_metric = $id_metric;
    }

    function setType_w($type_w) {
        $this->type_w = $type_w;
    }

    function setN_row($n_row) {
        $this->n_row = $n_row;
    }

    function setN_column($n_column) {
        $this->n_column = $n_column;
    }

    function setSize_rows($size_rows) {
        $this->size_rows = $size_rows;
    }

    function setSize_columns($size_columns) {
        $this->size_columns = $size_columns;
    }

    function setTitle_w($title_w) {
        $this->title_w = $title_w;
    }

    function setColor_w($color_w) {
        $this->color_w = $color_w;
    }

    function setFrequency_w($frequency_w) {
        $this->frequency_w = $frequency_w;
    }

    function setTemporal_range_w($temporal_range_w) {
        $this->temporal_range_w = $temporal_range_w;
    }

    function setMunicipality_w($municipality_w) {
        $this->municipality_w = $municipality_w;
    }

    function setInfoMessage_w($infoMessage_w) {
        $this->infoMessage_w = $infoMessage_w;
    }

    function setLink_w($link_w) {
        $this->link_w = $link_w;
    }

    function setParameters($parameters) {
        $this->parameters = $parameters;
    }

    function setFrame_color_w($frame_color_w) {
        $this->frame_color_w = $frame_color_w;
    }

    function setUdm($udm) {
        $this->udm = $udm;
    }

    function setUdmPos($udmPos) {
        $this->udmPos = $udmPos;
    }

    function setFontSize($fontSize) {
        $this->fontSize = $fontSize;
    }

    function setFontColor($fontColor) {
        $this->fontColor = $fontColor;
    }

    function setControlsPosition($controlsPosition) {
        $this->controlsPosition = $controlsPosition;
    }

    function setShowTitle($showTitle) {
        $this->showTitle = $showTitle;
    }

    function setControlsVisibility($controlsVisibility) {
        $this->controlsVisibility = $controlsVisibility;
    }

    function setZoomFactor($zoomFactor) {
        $this->zoomFactor = $zoomFactor;
    }

    function setDefaultTab($defaultTab) {
        $this->defaultTab = $defaultTab;
    }

    function setZoomControlsColor($zoomControlsColor) {
        $this->zoomControlsColor = $zoomControlsColor;
    }

    function setScaleX($scaleX) {
        $this->scaleX = $scaleX;
    }

    function setScaleY($scaleY) {
        $this->scaleY = $scaleY;
    }

    function setHeaderFontColor($headerFontColor) {
        $this->headerFontColor = $headerFontColor;
    }

    function setStyleParameters($styleParameters) {
        $this->styleParameters = $styleParameters;
    }

    function setInfoJson($infoJson) {
        $this->infoJson = $infoJson;
    }

    function setServiceUri($serviceUri) {
        $this->serviceUri = $serviceUri;
    }

    function setViewMode($viewMode) {
        $this->viewMode = $viewMode;
    }

    function setHospitalList($hospitalList) {
        $this->hospitalList = $hospitalList;
    }

    function setNotificatorRegistered($notificatorRegistered) {
        $this->notificatorRegistered = $notificatorRegistered;
    }

    function setNotificatorEnabled($notificatorEnabled) {
        $this->notificatorEnabled = $notificatorEnabled;
    }

    function setEnableFullscreenTab($enableFullscreenTab) {
        $this->enableFullscreenTab = $enableFullscreenTab;
    }

    function setEnableFullscreenModal($enableFullscreenModal) {
        $this->enableFullscreenModal = $enableFullscreenModal;
    }

    function setFontFamily($fontFamily) {
        $this->fontFamily = $fontFamily;
    }

    function setEntityJson($entityJson) {
        $this->entityJson = $entityJson;
    }

    function setAttributeName($attributeName) {
        $this->attributeName = $attributeName;
    }

    function setCreator($creator) {
        $this->creator = $creator;
    }

    function setLastEditor($lastEditor) {
        $this->lastEditor = $lastEditor;
    }

    function setCanceller($canceller) {
        $this->canceller = $canceller;
    }

    function setLastEditDate($lastEditDate) {
        $this->lastEditDate = $lastEditDate;
    }

    function setCancelDate($cancelDate) {
        $this->cancelDate = $cancelDate;
    }

    function setActuatorTarget($actuatorTarget) {
        $this->actuatorTarget = $actuatorTarget;
    }

    function setActuatorEntity($actuatorEntity) {
        $this->actuatorEntity = $actuatorEntity;
    }

    function setActuatorAttribute($actuatorAttribute) {
        $this->actuatorAttribute = $actuatorAttribute;
    }

    function setChartColor($chartColor) {
        $this->chartColor = $chartColor;
    }

    function setDataLabelsFontSize($dataLabelsFontSize) {
        $this->dataLabelsFontSize = $dataLabelsFontSize;
    }

    function setDataLabelsFontColor($dataLabelsFontColor) {
        $this->dataLabelsFontColor = $dataLabelsFontColor;
    }

    function setChartLabelsFontSize($chartLabelsFontSize) {
        $this->chartLabelsFontSize = $chartLabelsFontSize;
    }

    function setChartLabelsFontColor($chartLabelsFontColor) {
        $this->chartLabelsFontColor = $chartLabelsFontColor;
    }

    function getIcon()
    {
        return $this->icon;
    }

    function setIcon($icon)
    {
        $this->icon = $icon;
    }

    function __construct($name_w, $id_dashboard, $id_metric, $type_w, $n_row, $n_column, $size_rows, $size_columns, $title_w, $color_w, $frequency_w, $temporal_range_w, $municipality_w, $infoMessage_w, $link_w, $parameters, $frame_color_w, $udm, $udmPos, $fontSize, $fontColor, $controlsPosition, $showTitle, $controlsVisibility, $zoomFactor, $defaultTab, $zoomControlsColor, $scaleX, $scaleY, $headerFontColor, $styleParameters, $infoJson, $serviceUri, $viewMode, $hospitalList, $notificatorRegistered, $notificatorEnabled, $enableFullscreenTab, $enableFullscreenModal, $fontFamily, $entityJson, $attributeName, $creator, $lastEditor, $canceller, $lastEditDate, $cancelDate, $actuatorTarget, $actuatorEntity, $actuatorAttribute, $chartColor, $dataLabelsFontSize, $dataLabelsFontColor, $chartLabelsFontSize, $chartLabelsFontColor, $sm_based, $rowParameters, $sm_field, $wizardRowIds, $icon)
    {
        $this->name_w = $name_w;
        $this->id_dashboard = $id_dashboard;
        $this->id_metric = $id_metric;
        $this->type_w = $type_w;
        $this->n_row = $n_row;
        $this->n_column = $n_column;
        $this->size_rows = $size_rows;
        $this->size_columns = $size_columns;
        $this->title_w = $title_w;
        $this->color_w = $color_w;
        $this->frequency_w = $frequency_w;
        $this->temporal_range_w = $temporal_range_w;
        $this->municipality_w = $municipality_w;
        $this->infoMessage_w = $infoMessage_w;
        $this->link_w = $link_w;
        $this->parameters = $parameters;
        $this->frame_color_w = $frame_color_w;
        $this->udm = $udm;
        $this->udmPos = $udmPos;
        $this->fontSize = $fontSize;
        $this->fontColor = $fontColor;
        $this->controlsPosition = $controlsPosition;
        $this->showTitle = $showTitle;
        $this->controlsVisibility = $controlsVisibility;
        $this->zoomFactor = $zoomFactor;
        $this->defaultTab = $defaultTab;
        $this->zoomControlsColor = $zoomControlsColor;
        $this->scaleX = $scaleX;
        $this->scaleY = $scaleY;
        $this->headerFontColor = $headerFontColor;
        $this->styleParameters = $styleParameters;
        $this->infoJson = $infoJson;
        $this->serviceUri = $serviceUri;
        $this->viewMode = $viewMode;
        $this->hospitalList = $hospitalList;
        $this->notificatorRegistered = $notificatorRegistered;
        $this->notificatorEnabled = $notificatorEnabled;
        $this->enableFullscreenTab = $enableFullscreenTab;
        $this->enableFullscreenModal = $enableFullscreenModal;
        if ($fontFamily != null) {
            $this->fontFamily = $fontFamily;
        } else {
            $this->fontFamily = "Auto";
        }
        $this->entityJson = $entityJson;
        $this->attributeName = $attributeName;
        $this->creator = $creator;
        $this->lastEditor = $lastEditor;
        $this->canceller = $canceller;
        $this->lastEditDate = $lastEditDate;
        $this->cancelDate = $cancelDate;
        $this->actuatorTarget = $actuatorTarget;
        $this->actuatorEntity = $actuatorEntity;
        $this->actuatorAttribute = $actuatorAttribute;
        $this->chartColor = $chartColor;
        $this->dataLabelsFontSize = $dataLabelsFontSize;
        $this->dataLabelsFontColor = $dataLabelsFontColor;
        $this->chartLabelsFontSize = $chartLabelsFontSize;
        $this->chartLabelsFontColor = $chartLabelsFontColor;
        $this->sm_based = $sm_based;
        $this->rowParameters = $rowParameters;
        $this->sm_field = $sm_field;
        $this->wizardRowIds = $wizardRowIds;
        $this->icon = $icon;
    }

}
