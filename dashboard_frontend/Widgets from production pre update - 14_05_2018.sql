-- MySQL dump 10.13  Distrib 5.7.17, for Win64 (x86_64)
--
-- Host: 192.168.0.37    Database: Dashboard
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
-- Table structure for table `Widgets`
--

DROP TABLE IF EXISTS `Widgets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Widgets` (
  `id` int(9) NOT NULL AUTO_INCREMENT,
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
  `widgetCategory` varchar(45) DEFAULT 'dataViewer',
  `isNodeRedSender` varchar(3) DEFAULT 'no',
  `domainType` text,
  `defaultParameters` text,
  `hasTimer` varchar(3) DEFAULT 'yes',
  `hasChartColor` varchar(3) DEFAULT 'no',
  `hasDataLabels` varchar(3) DEFAULT 'no',
  `hasChartLabels` varchar(3) DEFAULT 'no',
  PRIMARY KEY (`id`),
  UNIQUE KEY `id_type_widget_UNIQUE` (`id_type_widget`)
) ENGINE=MyISAM AUTO_INCREMENT=47 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Widgets`
--

LOCK TABLES `Widgets` WRITE;
/*!40000 ALTER TABLE `Widgets` DISABLE KEYS */;
INSERT INTO `Widgets` VALUES (1,'widgetBarContent','widgetBarContent.php',1,50,1,50,'Percentuale','',1,1,1,NULL,'dataViewer','no',NULL,'{\n	\"chartColor\": \"rgba(51, 204, 255,1)\"\n}','yes','yes','yes','yes'),(2,'widgetColumnContent','widgetColumnContent.php',1,50,1,50,'Percentuale','',0,1,1,NULL,'dataViewer','no',NULL,'{\n	\"chartColor\": \"rgba(51, 204, 255,1)\"\n}','yes','yes','yes','yes'),(3,'widgetEvents','widgetEvents.php',1,50,1,50,'Testuale','List_Events_FI_Day',0,1,1,NULL,'dataViewer','no',NULL,'{}','yes','no','no','no'),(4,'widgetGaugeChart','widgetGaugeChart.php',1,50,1,50,'Intero|Percentuale|Float','',1,1,1,NULL,'dataViewer','no',NULL,'{\n	\"chartColor\": \"rgba(51, 204, 255,1)\"\n}','yes','yes','yes','yes'),(5,'widgetRadarSeries','widgetRadarSeries.php',1,50,1,50,'Series','',0,1,1,NULL,'dataViewer','no',NULL,'{}','yes','no','no','no'),(6,'widgetFirstAid','widgetFirstAid.php',1,50,1,50,'Testuale','FirstAid',0,1,1,NULL,'dataViewer','no',NULL,'{}','yes','no','no','no'),(45,'widgetNumericKeyboard','widgetNumericKeyboard.php',1,50,1,50,'[\'Float\', \'Integer\']','',0,0,0,NULL,'actuator','yes','[\'singleNumericValue\']','{}','no','no','no','no'),(8,'widgetTimeTrendCompare','widgetTimeTrendCompare.php',1,50,1,50,'Intero|Percentuale|Float','',0,1,1,NULL,'dataViewer','no',NULL,'{\n	\"chartColor\": \"rgba(51, 204, 255,1)\"\n}','yes','yes','yes','yes'),(9,'widgetPieChart','widgetPieChart.php',1,50,1,50,'Series','',1,1,1,NULL,'dataViewer','no',NULL,'{}','yes','no','no','no'),(10,'widgetAlarms','widgetAlarms.php',1,50,1,50,'Testuale','Alarms',0,1,1,NULL,'dataViewer','no',NULL,'{}','yes','no','no','no'),(11,'widgetPrevMeteo','widgetPrevMeteo.php',1,50,1,50,'Testuale','Previ_Meteo',0,1,1,NULL,'dataViewer','no',NULL,'{}','yes','no','no','no'),(44,'widgetOperatorEventsList','widgetOperatorEventsList.php',1,50,1,50,'Testuale','OperatorEventsList',0,1,1,NULL,'dataViewer','no',NULL,'{}','yes','no','no','no'),(13,'widgetSingleContent','widgetSingleContent.php',1,50,1,50,'Intero|Percentuale|Float|Testuale','',0,1,1,NULL,'dataViewer','no',NULL,'{}','yes','no','no','no'),(14,'widgetSmartDS','widgetSmartDS.php',1,50,1,50,'Percentuale','',0,1,1,NULL,'dataViewer','no',NULL,'{}','yes','no','no','no'),(15,'widgetSpeedometer','widgetSpeedometer.php',1,50,1,50,'Intero|Percentuale|Float','',1,1,1,NULL,'dataViewer','no',NULL,'{\n	\"chartColor\": \"rgba(51, 204, 255,1)\"\n}','yes','yes','yes','yes'),(16,'widgetTimeTrend','widgetTimeTrend.php',1,50,1,50,'Intero|Percentuale|Float|isAlive','',0,1,1,NULL,'dataViewer','no',NULL,'{\n	\"chartColor\": \"rgba(51, 204, 255,1)\"\n}','yes','yes','yes','yes'),(17,'widgetTrendMentions','widgetTrendMentions.php',1,50,1,50,'Testuale','MentionsTrends_FI_Day',0,1,1,NULL,'dataViewer','no',NULL,'{}','yes','no','no','no'),(18,'widgetStateRideAtaf','widgetStateRideAtaf.php',1,50,1,50,'Percentuale','',0,1,1,'{\n	\"dimMap\" : [\n		{\n			\"cols\" : 4,\n			\"rows\" : 4\n		},\n		{\n			\"cols\" : 5,\n			\"rows\" : 5\n		},\n		{\n			\"cols\" : 6,\n			\"rows\" : 6\n		},\n		{\n			\"cols\" : 7,\n			\"rows\" : 7\n		},\n		{\n			\"cols\" : 8,\n			\"rows\" : 8\n		}\n	]\n}','dataViewer','no',NULL,'{}','yes','no','no','no'),(20,'widgetButton','widgetButton.php',1,50,1,50,'Button','',1,0,1,NULL,'dataViewer','no',NULL,'{}','no','no','no','no'),(21,'widgetEvacuationPlans','widgetEvacuationPlans.php',1,50,1,50,'Testuale','EvacuationPlans',0,1,1,NULL,'dataViewer','no',NULL,'{}','yes','no','no','no'),(22,'widgetProcess','widgetProcess.php',1,50,1,50,'Testuale','Process',0,1,1,NULL,'dataViewer','no',NULL,'{}','yes','no','no','no'),(23,'widgetProtezioneCivile','widgetProtezioneCivile.php',1,50,1,50,'Testuale','ProtezioneCivile',0,1,1,'','dataViewer','no',NULL,'{}','yes','no','no','no'),(24,'widgetExternalContent','widgetExternalContent.php',1,50,1,50,'Testuale','ExternalContent',0,1,1,'','dataViewer','no','[\'webContent\']','{}','no','no','no','no'),(25,'widgetTable','widgetTable.php',1,50,1,50,'Series','',0,1,1,NULL,'dataViewer','no',NULL,'{}','yes','no','no','no'),(26,'widgetLineSeries','widgetLineSeries.php',1,50,1,50,'Series','',0,1,1,NULL,'dataViewer','no',NULL,'{}','yes','no','no','no'),(27,'widgetCurvedLineSeries','widgetCurvedLineSeries.php',1,50,1,50,'Series','',0,1,1,NULL,'dataViewer','no',NULL,'{}','yes','no','no','no'),(28,'widgetBarSeries','widgetBarSeries.php',1,50,1,50,'Series','',0,1,1,NULL,'dataViewer','no',NULL,'{}','yes','no','no','no'),(29,'widgetTrafficEvents','widgetTrafficEvents.php',1,50,1,50,'Testuale','TrafficEvents',0,1,1,NULL,'dataViewer','no',NULL,'{}','yes','no','no','no'),(30,'widgetResources','widgetResources.php',1,50,1,50,'Testuale','Resources',0,1,1,NULL,'dataViewer','no',NULL,'{}','yes','no','no','no'),(31,'widgetNetworkAnalysis','widgetNetworkAnalysis.php',1,50,1,50,'Testuale','NetworkAnalysis',0,1,1,NULL,'dataViewer','no',NULL,'{}','yes','no','no','no'),(32,'widgetClock','widgetClock.php',1,50,1,50,'Testuale','Clock',0,1,1,NULL,'dataViewer','no',NULL,'{}','no','no','no','no'),(33,'widgetSeparator','widgetSeparator.php',1,50,1,50,'Separator','',1,0,1,NULL,'dataViewer','no',NULL,'{}','no','no','no','no'),(34,'widgetSelector','widgetSelector.php',1,50,1,50,'Testuale','Selector',0,1,1,NULL,'dataViewer','no',NULL,'{}','no','no','no','no'),(36,'widgetServerStatus','widgetServerStatus.php',1,50,1,50,'isAlive','',0,1,1,NULL,'dataViewer','no',NULL,'{}','yes','no','no','no'),(37,'widgetKnob','widgetKnob.php',1,50,1,50,'[\'Float\', \'Integer\']','',0,0,0,NULL,'actuator','yes','[\'contRange\']','{}','no','no','no','no'),(38,'widgetSelectorWeb','widgetSelectorWeb.php',1,50,1,50,'Testuale','SelectorWeb',0,1,1,NULL,'dataViewer','no',NULL,'{}','no','no','no','no'),(39,'widgetOnOffButton','widgetOnOffButton.php',1,50,1,50,'[\'Float\', \'Integer\', \'Boolean\', \'String\']','',0,0,1,NULL,'actuator','yes','[\'onOff\']','{}','no','no','no','no'),(40,'widgetTrafficLight','widgetTrafficLight.php',1,50,1,50,'TrafficLight','TrafficLight',0,0,0,NULL,'dataViewer','no',NULL,'{}','yes','no','no','no'),(42,'widgetSpeedLimit','widgetSpeedLimit.php',1,50,1,50,'SpeedLimit|Intero','',0,0,0,NULL,'dataViewer','no',NULL,'{}','yes','no','no','no'),(43,'widgetImpulseButton','widgetImpulseButton.php',1,50,1,50,'[\'Float\', \'Integer\', \'Boolean\', \'String\']','',0,0,0,NULL,'actuator','yes','[\'impulse\']','{}','no','no','no','no'),(46,'widgetGeolocator','widgetGeolocator.php',1,50,1,50,'Geolocator','',0,0,0,NULL,'actuator','yes','[\'geolocator\']','{}','no','no','no','no');
/*!40000 ALTER TABLE `Widgets` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2018-05-14 15:36:24
