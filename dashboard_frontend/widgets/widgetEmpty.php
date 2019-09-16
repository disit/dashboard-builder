<?php
/* Dashboard Builder.
   Copyright (C) 2018 DISIT Lab https://www.disit.org - University of Florence

   This program is free software; you can redistribute it and/or
   modify it under the terms of the GNU General Public License
   as published by the Free Software Foundation; either version 2
   of the License, or (at your option) any later version.
   This program is distributed in the hope that it will be useful,
   but WITHOUT ANY WARRANTY; without even the implied warranty of
   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
   GNU General Public License for more details.
   You should have received a copy of the GNU General Public License
   along with this program; if not, write to the Free Software
   Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA. */
   include('../config.php');
   header("Cache-Control: private, max-age=$cacheControlMaxAge");
?>

<script type='text/javascript'>
    
    //Ogni "main" lato client di un widget è semple incluso nel risponditore ad evento ready del documento, così siamo sicuri di operare sulla pagina già caricata
    $(document).ready(function <?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w']))?>(firstLoad, metricNameFromDriver, widgetTitleFromDriver, widgetHeaderColorFromDriver, widgetHeaderFontColorFromDriver, fromGisExternalContent, fromGisExternalContentServiceUri, fromGisExternalContentField, fromGisExternalContentRange, /*randomSingleGeoJsonIndex,*/ fromGisMarker, fromGisMapRef, fromGisFakeId)  
    {
        <?php
            $titlePatterns = array();
            $titlePatterns[0] = '/_/';
            $titlePatterns[1] = '/\'/';
            $replacements = array();
            $replacements[0] = ' ';
            $replacements[1] = '&apos;';
            $title = $_REQUEST['title_w'];
            $link = mysqli_connect($host, $username, $password);
            if (checkWidgetNameInDashboard($link, $_REQUEST['name_w'], $_REQUEST['id_dashboard']) === false) {
                eventLog("Returned the following ERROR in widgetEmpty.php for the widget ".escapeForHTML($_REQUEST['name_w'])." is not instantiated or allowed in this dashboard.");
                exit();
            }
        ?>
                
        var headerHeight = 25;
        var hostFile = "<?= escapeForJS($_REQUEST['hostFile']) ?>";
        var widgetName = "<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w']))?>";
        var widgetContentColor = "<?= escapeForJS($_REQUEST['color_w']) ?>";
        var fontSize = "<?= escapeForJS($_REQUEST['fontSize']) ?>";
        var fontColor = "<?= escapeForJS($_REQUEST['fontColor']) ?>";
        var timeToReload = <?= sanitizeInt('frequency_w') ?>;
        var embedWidget = <?= $_REQUEST['embedWidget']=='true'?'true':'false' ?>;
        var embedWidgetPolicy = '<?= escapeForJS($_REQUEST['embedWidgetPolicy']) ?>';
        var showTitle = "<?= escapeForJS($_REQUEST['showTitle']) ?>";
        var showHeader = null;
        var hasTimer = "<?= escapeForJS($_REQUEST['hasTimer']) ?>";
        var styleParameters, metricName, udm, udmPos, appId, flowId, nrMetricType,
            sm_field, sizeRowsWidget, sm_based, rowParameters, fontSize, countdownRef, widgetTitle, widgetHeaderColor, 
            widgetHeaderFontColor, showHeader, widgetParameters, chartColor, dataLabelsFontSize, dataLabelsFontColor, chartLabelsFontSize, chartLabelsFontColor = null;
        
        //Definizioni di funzione
        
        //Tipicamente questa funzione viene invocata dopo che sono stati scaricati i dati per il widget (se ne ha bisogno) e ci va dentro la logica che costruisce il contenuto del widget
        function populateWidget()
        {
            
        }
        
        // Funzione che risponde all'evento resize del widget, indotto o dal ridimensionatore manuale dell'editor di dashboard oppure dalla dashboard stessa in modalità responsive
        function resizeWidget()
	{
            setWidgetLayout(hostFile, widgetName, widgetContentColor, widgetHeaderColor, widgetHeaderFontColor, showHeader, headerHeight, hasTimer);    
	}
        //Fine definizioni di funzione 
        
        //Inizio del main script
        
        /*IMPORTANTE - Chiamata al modulo server che reperisce i parametri di costruzione del widget dal database (tipicamente
        * da tabella Config_widget_dashboard, la quale memorizza un record per ogni istanza di widget. Tale record viene scritto
        * quando il widget viene creato
        */
        $.ajax({
                url: "../controllers/getWidgetParams.php",
                type: "GET",
                data: {
                    widgetName: "<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>"
                },
                async: true,
                dataType: 'json',
                success: function(widgetData) 
                {
                    //Parametri di costruzione del widget (struttura e aspetto)
                    showTitle = widgetData.params.showTitle;
                    widgetContentColor = widgetData.params.color_w;
                    fontSize = widgetData.params.fontSize;
                    fontColor = widgetData.params.fontColor;
                    timeToReload = widgetData.params.frequency_w;
                    hasTimer = widgetData.params.hasTimer;
                    chartColor = widgetData.params.chartColor;
                    dataLabelsFontSize = widgetData.params.dataLabelsFontSize; 
                    dataLabelsFontColor = widgetData.params.dataLabelsFontColor; 
                    chartLabelsFontSize = widgetData.params.chartLabelsFontSize; 
                    chartLabelsFontColor = widgetData.params.chartLabelsFontColor;
                    appId = widgetData.params.appId;
                    flowId = widgetData.params.flowId;
                    nrMetricType = widgetData.params.nrMetricType;
                    sm_based = widgetData.params.sm_based;
                    rowParameters = widgetData.params.rowParameters;
                    sm_field = widgetData.params.sm_field;
                    
                    if(((embedWidget === true)&&(embedWidgetPolicy === 'auto'))||((embedWidget === true)&&(embedWidgetPolicy === 'manual')&&(showTitle === "no"))||((embedWidget === false)&&(showTitle === "no")))
                    {
                        showHeader = false;
                    }
                    else
                    {
                        showHeader = true;
                    } 

                    metricName = "<?= escapeForJS($_REQUEST['id_metric']) ?>";
                    widgetTitle = widgetData.params.title_w;
                    widgetHeaderColor = widgetData.params.frame_color_w;
                    widgetHeaderFontColor = widgetData.params.headerFontColor;
                    sizeRowsWidget = parseInt(widgetData.params.size_rows);
                    styleParameters = JSON.parse(widgetData.params.styleParameters);
                    widgetParameters = JSON.parse(widgetData.params.parameters);
                    
                    setWidgetLayout(hostFile, widgetName, widgetContentColor, widgetHeaderColor, widgetHeaderFontColor, showHeader, headerHeight, hasTimer);
        
                    $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_div').parents('li.gs_w').off('resizeWidgets');
                    $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_div').parents('li.gs_w').on('resizeWidgets', resizeWidget);

                    if(firstLoad === false)
                    {
                        showWidgetContent(widgetName);
                    }
                    else
                    {
                        setupLoadingPanel(widgetName, widgetContentColor, firstLoad);
                    }
                    
                    populateWidget();    
                },
                error: function(errorData)
                {
                    
                }
        });
        
        //Risponditore ad evento resize
        $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>").on('customResizeEvent', function(event){
            resizeWidget();
        });
        
        //Usata solo per widget con grafico Highchart al proprio interno (non è il nostro caso)
        $(document).on('resizeHighchart_' + widgetName, function(event)
        {
            showHeader = event.showHeader;
        });
        
        //Quando viene modificata la frequenza di refresh del widget, esso viene ricaricato
        $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>").off('updateFrequency');
        $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>").on('updateFrequency', function(event){
            clearInterval(countdownRef);
            timeToReload = event.newTimeToReload;
            countdownRef = startCountdown(widgetName, timeToReload, <?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>, metricNameFromDriver, widgetTitleFromDriver, widgetHeaderColorFromDriver, widgetHeaderFontColorFromDriver, fromGisExternalContent, fromGisExternalContentServiceUri, fromGisExternalContentField, fromGisExternalContentRange, fromGisMarker, fromGisMapRef, fromGisFakeId);
        });
        
        //Avvio del conto alla rovescia per il ricaricamento periodico del widget
        countdownRef = startCountdown(widgetName, timeToReload, <?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>, metricNameFromDriver, widgetTitleFromDriver, widgetHeaderColorFromDriver, widgetHeaderFontColorFromDriver, fromGisExternalContent, fromGisExternalContentServiceUri, fromGisExternalContentField, fromGisExternalContentRange, fromGisMarker, fromGisMapRef, fromGisFakeId);
       
});//Fine document ready 
</script>

<div class="widget" id="<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_div">
    <div class='ui-widget-content'>
        <!-- Inclusione del modulo comune che costruisce la testata del widget, JS incluso -->
        <?php include '../widgets/widgetHeader.php'; ?>
        
        <!-- Inclusione del modulo comune che costruisce il menu constestaule di gestione del widget -->
        <?php include '../widgets/widgetCtxMenu.php'; ?>
        
        <!-- Schermata di loading -->
        <div id="<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_loading" class="loadingDiv">
            <div class="loadingTextDiv">
                <p>Loading data, please wait</p>
            </div>
            <div class ="loadingIconDiv">
                <i class='fa fa-spinner fa-spin'></i>
            </div>
        </div>
        
        <!-- Contenitore esterno del contenuto del widget -->
        <div id="<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_content" class="content">
            
            <!-- Modulo comune per la gestione dei dimensionatori del widget in edit dashboard -->
            <?php include '../widgets/commonModules/widgetDimControls.php'; ?>
            
            <!-- Pannello che viene mostrato quando non ci sono dati disponibili per il widget in esame -->
            <div id="<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_noDataAlert" class="noDataAlert">
                <div id="<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_noDataAlertText" class="noDataAlertText">
                    No data available
                </div>
                <div id="<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_noDataAlertIcon" class="noDataAlertIcon">
                    <i class="fa fa-times"></i>
                </div>
            </div>
            
            <!-- Dentro questo DIV ci va il contenuto vero e proprio (e specifico) del widget (si chiama _chartContainer solo per legacy, non contiene necessariamente un grafico) -->
            <div id="<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_chartContainer" class="chartContainer">
                
            </div>
        </div>
    </div>	
</div> 
