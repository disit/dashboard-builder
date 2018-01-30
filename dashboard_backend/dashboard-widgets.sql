-- MySQL dump 10.13  Distrib 5.7.12, for Win64 (x86_64)
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
-- Dumping data for table `Widgets`
--

LOCK TABLES `Widgets` WRITE;
/*!40000 ALTER TABLE `Widgets` DISABLE KEYS */;
INSERT INTO `Widgets` VALUES ('widgetBarContent','widgetBarContent.php',2,50,2,50,'Percentuale','',1,1,1,NULL),('widgetColumnContent','widgetColumnContent.php',2,50,2,50,'Percentuale','',0,1,1,NULL),('widgetEvents','widgetEvents.php',2,50,2,50,'Testuale','List_Events_FI_Day',0,1,1,NULL),('widgetGaugeChart','widgetGaugeChart.php',2,50,2,50,'Intero|Percentuale|Float','',1,1,1,NULL),('widgetRadarSeries','widgetRadarSeries.php',2,50,2,50,'Series','',0,1,1,NULL),('widgetFirstAid','widgetFirstAid.php',2,50,2,50,'Testuale','FirstAid',0,1,1,NULL),('widgetGenericContent','widgetGenericContent.php',2,50,2,50,'Intero|Percentuale|Testuale|Float','',0,3,1,NULL),('widgetTimeTrendCompare','widgetTimeTrendCompare.php',2,50,2,50,'Intero|Percentuale|Float','',0,1,1,NULL),('widgetPieChart','widgetPieChart.php',2,50,2,50,'Percentuale|Series','',1,1,1,NULL),('widgetAlarms','widgetAlarms.php',2,50,4,50,'Testuale','Alarms',0,1,1,NULL),('widgetPrevMeteo','widgetPrevMeteo.php',2,50,2,50,'Testuale','Previ_Meteo',0,1,1,NULL),('widgetSce','widgetSce.php',2,50,2,50,'SCE','',0,3,1,'{\n	\"dimMap\":[\n	   {\"rows\":4, \"cols\":4},\n	   {\"rows\":5, \"cols\":5},\n	   {\"rows\":6, \"cols\":6}\n	]\n  }'),('widgetSingleContent','widgetSingleContent.php',2,50,2,50,'Intero|Percentuale|Float|Testuale','',0,1,1,NULL),('widgetSmartDS','widgetSmartDS.php',2,50,2,50,'Percentuale','',0,1,1,NULL),('widgetSpeedometer','widgetSpeedometer.php',2,50,2,50,'Intero|Percentuale|Float','',1,1,1,NULL),('widgetTimeTrend','widgetTimeTrend.php',2,50,2,50,'Intero|Percentuale|Float|isAlive','',0,1,1,NULL),('widgetTrendMentions','widgetTrendMentions.php',2,50,2,50,'Testuale','MentionsTrends_FI_Day',0,1,1,NULL),('widgetStateRideAtaf','widgetStateRideAtaf.php',2,50,2,50,'Percentuale','',0,1,1,'{\n	\"dimMap\" : [\n		{\n			\"cols\" : 4,\n			\"rows\" : 4\n		},\n		{\n			\"cols\" : 5,\n			\"rows\" : 5\n		},\n		{\n			\"cols\" : 6,\n			\"rows\" : 6\n		},\n		{\n			\"cols\" : 7,\n			\"rows\" : 7\n		},\n		{\n			\"cols\" : 8,\n			\"rows\" : 8\n		}\n	]\n}'),('widgetServiceMap','widgetServiceMap.php',2,50,2,50,'Map','',0,1,1,NULL),('widgetButton','widgetButton.php',1,50,1,50,'Button','',1,0,1,NULL),('widgetEvacuationPlans','widgetEvacuationPlans.php',2,50,4,50,'Testuale','EvacuationPlans',0,1,1,NULL),('widgetProcess','widgetProcess.php',2,50,2,50,'Testuale','Process',0,1,1,NULL),('widgetProtezioneCivile','widgetProtezioneCivile.php',2,50,2,50,'Testuale','ProtezioneCivile',0,1,1,''),('widgetExternalContent','widgetExternalContent.php',2,50,2,50,'Testuale','ExternalContent',0,1,1,''),('widgetTable','widgetTable.php',2,50,2,50,'Series','',0,1,1,NULL),('widgetLineSeries','widgetLineSeries.php',2,50,2,50,'Series','',0,1,1,NULL),('widgetCurvedLineSeries','widgetCurvedLineSeries.php',2,50,2,50,'Series','',0,1,1,NULL),('widgetBarSeries','widgetBarSeries.php',2,50,2,50,'Series','',0,1,1,NULL),('widgetTrafficEvents','widgetTrafficEvents.php',2,50,4,50,'Testuale','TrafficEvents',0,1,1,NULL),('widgetResources','widgetResources.php',2,50,4,50,'Testuale','Resources',0,1,1,NULL),('widgetNetworkAnalysis','widgetNetworkAnalysis.php',2,50,4,50,'Testuale','NetworkAnalysis',0,1,1,NULL),('widgetClock','widgetClock.php',2,50,4,50,'Testuale','Clock',0,1,1,NULL),('widgetSeparator','widgetSeparator.php',1,50,1,50,'Separator','',1,0,1,NULL),('widgetSelector','widgetSelector.php',2,50,2,50,'Testuale','Selector',0,1,1,NULL);
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

-- Dump completed on 2017-12-05 19:08:14
