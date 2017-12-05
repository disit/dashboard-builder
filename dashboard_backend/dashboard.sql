-- MySQL dump 10.13  Distrib 5.7.12, for Win64 (x86_64)
--
-- ------------------------------------------------------
-- Server version	5.5.58-0+deb8u1

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `Config_dashboard`
--

DROP TABLE IF EXISTS `Config_dashboard`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Config_dashboard` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `name_dashboard` varchar(100) CHARACTER SET latin1 DEFAULT NULL,
  `title_header` varchar(45) CHARACTER SET latin1 DEFAULT NULL,
  `subtitle_header` varchar(45) CHARACTER SET latin1 DEFAULT NULL,
  `color_header` varchar(40) CHARACTER SET latin1 DEFAULT NULL,
  `width` int(11) DEFAULT NULL,
  `height` int(11) DEFAULT NULL,
  `num_rows` int(11) DEFAULT NULL,
  `num_columns` int(11) DEFAULT NULL,
  `user` varchar(100) CHARACTER SET latin1 DEFAULT NULL,
  `status_dashboard` int(11) DEFAULT NULL,
  `creation_date` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `color_background` varchar(40) CHARACTER SET latin1 DEFAULT NULL,
  `external_frame_color` varchar(40) CHARACTER SET latin1 DEFAULT NULL,
  `headerFontColor` varchar(40) CHARACTER SET latin1 DEFAULT '#ffffff',
  `headerFontSize` int(3) DEFAULT '45',
  `logoFilename` varchar(45) CHARACTER SET latin1 DEFAULT NULL,
  `logoLink` varchar(400) CHARACTER SET latin1 DEFAULT NULL,
  `widgetsBorders` varchar(3) CHARACTER SET latin1 DEFAULT 'yes',
  `widgetsBordersColor` varchar(40) CHARACTER SET latin1 DEFAULT '#dddddd',
  `reference` int(11) DEFAULT '0',
  `visibility` varchar(45) CHARACTER SET latin1 DEFAULT 'public',
  `headerVisible` int(1) DEFAULT '1',
  `embeddable` varchar(3) DEFAULT 'no',
  `authorizedPagesJson` text,
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Config_widget_dashboard`
--

DROP TABLE IF EXISTS `Config_widget_dashboard`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Config_widget_dashboard` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `name_w` varchar(150) CHARACTER SET latin1 DEFAULT NULL,
  `id_dashboard` int(11) DEFAULT NULL,
  `id_metric` varchar(150) CHARACTER SET latin1 DEFAULT NULL,
  `type_w` varchar(150) CHARACTER SET latin1 DEFAULT NULL,
  `n_row` int(11) DEFAULT NULL,
  `n_column` int(11) DEFAULT NULL,
  `size_rows` int(11) DEFAULT NULL,
  `size_columns` int(11) DEFAULT NULL,
  `title_w` varchar(100) CHARACTER SET latin1 DEFAULT NULL,
  `color_w` varchar(40) CHARACTER SET latin1 DEFAULT NULL,
  `frequency_w` varchar(100) CHARACTER SET latin1 DEFAULT NULL,
  `temporal_range_w` varchar(100) CHARACTER SET latin1 DEFAULT NULL,
  `municipality_w` varchar(100) CHARACTER SET latin1 DEFAULT NULL,
  `infoMessage_w` text CHARACTER SET latin1,
  `link_w` varchar(1024) CHARACTER SET latin1 DEFAULT NULL,
  `parameters` text CHARACTER SET latin1,
  `udm` varchar(45) CHARACTER SET latin1 DEFAULT NULL,
  `udmPos` varchar(50) CHARACTER SET latin1 DEFAULT NULL,
  `fontSize` int(3) DEFAULT NULL,
  `fontColor` varchar(40) CHARACTER SET latin1 DEFAULT NULL,
  `frame_color_w` varchar(40) CHARACTER SET latin1 DEFAULT NULL,
  `controlsPosition` varchar(25) CHARACTER SET latin1 DEFAULT NULL,
  `showTitle` varchar(3) CHARACTER SET latin1 DEFAULT NULL,
  `controlsVisibility` varchar(15) CHARACTER SET latin1 DEFAULT NULL,
  `zoomFactor` float DEFAULT NULL,
  `defaultTab` int(2) DEFAULT NULL,
  `zoomControlsColor` varchar(40) CHARACTER SET latin1 DEFAULT NULL,
  `scaleX` double DEFAULT NULL,
  `scaleY` double DEFAULT NULL,
  `headerFontColor` varchar(40) CHARACTER SET latin1 DEFAULT NULL,
  `styleParameters` text CHARACTER SET latin1,
  `infoJson` text CHARACTER SET latin1,
  `serviceUri` varchar(600) CHARACTER SET latin1 DEFAULT NULL,
  `viewMode` varchar(50) CHARACTER SET latin1 DEFAULT NULL,
  `hospitalList` text CHARACTER SET latin1,
  `lastSeries` text CHARACTER SET latin1,
  `notificatorRegistered` varchar(3) CHARACTER SET latin1 DEFAULT 'no',
  `notificatorEnabled` varchar(3) CHARACTER SET latin1 DEFAULT 'no',
  `oldParameters` text,
  `enableFullscreenTab` varchar(3) DEFAULT 'no',
  `enableFullscreenModal` varchar(3) DEFAULT 'no',
  `fontFamily` varchar(100) NOT NULL DEFAULT 'Auto',
  PRIMARY KEY (`Id`),
  KEY `Config_widget_dashboard_ibfk_1` (`id_dashboard`),
  CONSTRAINT `Config_widget_dashboard_ibfk_1` FOREIGN KEY (`id_dashboard`) REFERENCES `Config_dashboard` (`Id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `DashboardsViewPermissions`
--

DROP TABLE IF EXISTS `DashboardsViewPermissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `DashboardsViewPermissions` (
  `IdDashboard` int(11) NOT NULL,
  `username` varchar(100) COLLATE utf8_bin NOT NULL,
  KEY `IdDashboard` (`IdDashboard`),
  CONSTRAINT `dashboardsviewpermissions_ibfk_1` FOREIGN KEY (`IdDashboard`) REFERENCES `Config_dashboard` (`Id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Data`
--

DROP TABLE IF EXISTS `Data`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Data` (
  `IdMetric_data` varchar(45) NOT NULL,
  `computationDate` datetime NOT NULL,
  `value_num` double DEFAULT NULL,
  `value_perc1` double DEFAULT NULL,
  `value_perc2` double DEFAULT NULL,
  `value_perc3` double DEFAULT NULL,
  `value_text` varchar(300) DEFAULT NULL,
  `quant_perc1` int(11) DEFAULT NULL,
  `quant_perc2` int(11) DEFAULT NULL,
  `quant_perc3` int(11) DEFAULT NULL,
  `tot_perc1` int(11) DEFAULT NULL,
  `tot_perc2` int(11) DEFAULT NULL,
  `tot_perc3` int(11) DEFAULT NULL,
  `series` text,
  PRIMARY KEY (`IdMetric_data`,`computationDate`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `DataSource`
--

DROP TABLE IF EXISTS `DataSource`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `DataSource` (
  `Id` varchar(45) NOT NULL,
  `url` varchar(200) DEFAULT NULL,
  `database` varchar(100) DEFAULT NULL,
  `username` varchar(100) DEFAULT NULL,
  `password` varchar(100) DEFAULT NULL,
  `databaseType` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Descriptions`
--

DROP TABLE IF EXISTS `Descriptions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Descriptions` (
  `IdMetric` varchar(45) CHARACTER SET latin1 NOT NULL,
  `description` longtext CHARACTER SET latin1,
  `status` varchar(45) CHARACTER SET latin1 DEFAULT NULL,
  `query` longtext CHARACTER SET latin1,
  `query2` longtext CHARACTER SET latin1,
  `queryType` varchar(300) CHARACTER SET latin1 DEFAULT NULL,
  `metricType` varchar(100) CHARACTER SET latin1 DEFAULT NULL,
  `frequency` varchar(100) CHARACTER SET latin1 DEFAULT NULL,
  `processType` varchar(45) CHARACTER SET latin1 DEFAULT NULL,
  `area` varchar(300) CHARACTER SET latin1 DEFAULT NULL,
  `source` varchar(300) CHARACTER SET latin1 DEFAULT NULL,
  `description_short` longtext CHARACTER SET latin1,
  `dataSource` varchar(100) CHARACTER SET latin1 DEFAULT NULL,
  `threshold` double DEFAULT NULL,
  `thresholdEval` varchar(45) CHARACTER SET latin1 DEFAULT NULL,
  `thresholdEvalCount` int(11) DEFAULT NULL,
  `thresholdTime` int(11) DEFAULT NULL,
  `storingData` int(11) DEFAULT NULL,
  `municipalityOption` int(11) DEFAULT NULL,
  `timeRangeOption` int(11) DEFAULT NULL,
  `colorDefault` varchar(45) CHARACTER SET latin1 DEFAULT NULL,
  `field1Desc` varchar(45) CHARACTER SET latin1 DEFAULT NULL,
  `field2Desc` varchar(45) CHARACTER SET latin1 DEFAULT NULL,
  `field3Desc` varchar(45) CHARACTER SET latin1 DEFAULT NULL,
  `oldData` int(1) DEFAULT '0',
  `metricGroup` int(11) DEFAULT NULL,
  `sameDataAlarmCount` int(11) DEFAULT NULL,
  `oldDataEvalTime` int(11) DEFAULT NULL,
  `hasNegativeValues` int(1) DEFAULT '0',
  `process` varchar(45) DEFAULT 'DashboardProcess',
  PRIMARY KEY (`IdMetric`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `JobAreas`
--

DROP TABLE IF EXISTS `JobAreas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `JobAreas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `schedulerId` int(11) NOT NULL,
  `name` varchar(45) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `schedulerId` (`schedulerId`),
  CONSTRAINT `jobareas_ibfk_1` FOREIGN KEY (`schedulerId`) REFERENCES `Schedulers` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `MetricGroups`
--

DROP TABLE IF EXISTS `MetricGroups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `MetricGroups` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `desc` varchar(100) COLLATE utf8_bin DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Schedulers`
--

DROP TABLE IF EXISTS `Schedulers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Schedulers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(45) NOT NULL,
  `ip` varchar(15) NOT NULL,
  `user` varchar(45) NOT NULL,
  `pass` varchar(45) NOT NULL,
  `hasJobAreas` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Users`
--

DROP TABLE IF EXISTS `Users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Users` (
  `IdUser` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(100) CHARACTER SET latin1 DEFAULT NULL,
  `password` varchar(32) CHARACTER SET latin1 DEFAULT NULL,
  `name` varchar(100) CHARACTER SET latin1 DEFAULT NULL,
  `surname` varchar(100) CHARACTER SET latin1 DEFAULT NULL,
  `organization` varchar(200) CHARACTER SET latin1 DEFAULT NULL,
  `email` varchar(200) CHARACTER SET latin1 DEFAULT NULL,
  `reg_data` timestamp NULL DEFAULT NULL,
  `status` int(11) DEFAULT NULL,
  `admin` varchar(32) NOT NULL DEFAULT 'Manager',
  `activationHash` varchar(32) CHARACTER SET latin1 DEFAULT NULL,
  PRIMARY KEY (`IdUser`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `Users` WRITE;
/*!40000 ALTER TABLE `Users` DISABLE KEYS */;
INSERT INTO `Users` VALUES ('0', 'admin', '21232f297a57a5a743894a0e4a801fc3', 'Admin', 'Admin', NULL, 'admin@email.it', '2017-01-01 10:10:10', '1', 'ToolAdmin', NULL);
/*!40000 ALTER TABLE `Users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `UsersPools`
--

DROP TABLE IF EXISTS `UsersPools`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `UsersPools` (
  `poolId` int(11) NOT NULL AUTO_INCREMENT,
  `poolName` varchar(45) COLLATE utf8_bin DEFAULT NULL,
  PRIMARY KEY (`poolId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `UsersPoolsRelations`
--

DROP TABLE IF EXISTS `UsersPoolsRelations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `UsersPoolsRelations` (
  `username` varchar(100) NOT NULL,
  `poolId` int(11) NOT NULL,
  `isAdmin` int(11) NOT NULL,
  PRIMARY KEY (`username`,`poolId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Widgets`
--

DROP TABLE IF EXISTS `Widgets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Widgets` (
  `id_type_widget` varchar(150) NOT NULL,
  `source_php_widget` varchar(150) DEFAULT NULL,
  `min_row` int(11) DEFAULT NULL,
  `max_row` int(11) DEFAULT NULL,
  `min_col` int(11) DEFAULT NULL,
  `max_col` int(11) DEFAULT NULL,
  `widgetType` varchar(150) DEFAULT NULL,
  `unique_metric` varchar(150) NOT NULL,
  `numeric_rangeOption` int(11) DEFAULT NULL,
  `number_metrics_widget` int(11) DEFAULT NULL,
  `color_widgetOption` int(11) DEFAULT NULL,
  `dimMap` text,
  PRIMARY KEY (`id_type_widget`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

LOCK TABLES `Widgets` WRITE;
/*!40000 ALTER TABLE `Widgets` DISABLE KEYS */;
INSERT INTO `Widgets` VALUES ('widgetBarContent','widgetBarContent.php',2,50,2,50,'Percentuale','',1,1,1,NULL),('widgetColumnContent','widgetColumnContent.php',2,50,2,50,'Percentuale','',0,1,1,NULL),('widgetEvents','widgetEvents.php',2,50,2,50,'Testuale','List_Events_FI_Day',0,1,1,NULL),('widgetGaugeChart','widgetGaugeChart.php',2,50,2,50,'Intero|Percentuale|Float','',1,1,1,NULL),('widgetRadarSeries','widgetRadarSeries.php',2,50,2,50,'Series','',0,1,1,NULL),('widgetFirstAid','widgetFirstAid.php',2,50,2,50,'Testuale','FirstAid',0,1,1,NULL),('widgetGenericContent','widgetGenericContent.php',2,50,2,50,'Intero|Percentuale|Testuale|Float','',0,3,1,NULL),('widgetTimeTrendCompare','widgetTimeTrendCompare.php',2,50,2,50,'Intero|Percentuale|Float','',0,1,1,NULL),('widgetPieChart','widgetPieChart.php',2,50,2,50,'Percentuale|Series','',1,1,1,NULL),('widgetAlarms','widgetAlarms.php',2,50,4,50,'Testuale','Alarms',0,1,1,NULL),('widgetPrevMeteo','widgetPrevMeteo.php',2,50,2,50,'Testuale','Previ_Meteo',0,1,1,NULL),('widgetSce','widgetSce.php',2,50,2,50,'SCE','',0,3,1,'{\n	\"dimMap\":[\n	   {\"rows\":4, \"cols\":4},\n	   {\"rows\":5, \"cols\":5},\n	   {\"rows\":6, \"cols\":6}\n	]\n  }'),('widgetSingleContent','widgetSingleContent.php',2,50,2,50,'Intero|Percentuale|Float|Testuale','',0,1,1,NULL),('widgetSmartDS','widgetSmartDS.php',2,50,2,50,'Percentuale','',0,1,1,NULL),('widgetSpeedometer','widgetSpeedometer.php',2,50,2,50,'Intero|Percentuale|Float','',1,1,1,NULL),('widgetTimeTrend','widgetTimeTrend.php',2,50,2,50,'Intero|Percentuale|Float|isAlive','',0,1,1,NULL),('widgetTrendMentions','widgetTrendMentions.php',2,50,2,50,'Testuale','MentionsTrends_FI_Day',0,1,1,NULL),('widgetStateRideAtaf','widgetStateRideAtaf.php',2,50,2,50,'Percentuale','',0,1,1,'{\n	\"dimMap\" : [\n		{\n			\"cols\" : 4,\n			\"rows\" : 4\n		},\n		{\n			\"cols\" : 5,\n			\"rows\" : 5\n		},\n		{\n			\"cols\" : 6,\n			\"rows\" : 6\n		},\n		{\n			\"cols\" : 7,\n			\"rows\" : 7\n		},\n		{\n			\"cols\" : 8,\n			\"rows\" : 8\n		}\n	]\n}'),('widgetServiceMap','widgetServiceMap.php',2,50,2,50,'Map','',0,1,1,NULL),('widgetButton','widgetButton.php',1,50,1,50,'Button','',1,0,1,NULL),('widgetEvacuationPlans','widgetEvacuationPlans.php',2,50,4,50,'Testuale','EvacuationPlans',0,1,1,NULL),('widgetProcess','widgetProcess.php',2,50,2,50,'Testuale','Process',0,1,1,NULL),('widgetProtezioneCivile','widgetProtezioneCivile.php',2,50,2,50,'Testuale','ProtezioneCivile',0,1,1,''),('widgetExternalContent','widgetExternalContent.php',2,50,2,50,'Testuale','ExternalContent',0,1,1,''),('widgetTable','widgetTable.php',2,50,2,50,'Series','',0,1,1,NULL),('widgetLineSeries','widgetLineSeries.php',2,50,2,50,'Series','',0,1,1,NULL),('widgetCurvedLineSeries','widgetCurvedLineSeries.php',2,50,2,50,'Series','',0,1,1,NULL),('widgetBarSeries','widgetBarSeries.php',2,50,2,50,'Series','',0,1,1,NULL),('widgetTrafficEvents','widgetTrafficEvents.php',2,50,4,50,'Testuale','TrafficEvents',0,1,1,NULL),('widgetResources','widgetResources.php',2,50,4,50,'Testuale','Resources',0,1,1,NULL),('widgetNetworkAnalysis','widgetNetworkAnalysis.php',2,50,4,50,'Testuale','NetworkAnalysis',0,1,1,NULL),('widgetClock','widgetClock.php',2,50,4,50,'Testuale','Clock',0,1,1,NULL),('widgetSeparator','widgetSeparator.php',1,50,1,50,'Separator','',1,0,1,NULL),('widgetSelector','widgetSelector.php',2,50,2,50,'Testuale','Selector',0,1,1,NULL);
/*!40000 ALTER TABLE `Widgets` ENABLE KEYS */;
UNLOCK TABLES;


/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

