-- MySQL dump 10.13  Distrib 5.7.12, for Win64 (x86_64)
--
-- Host: 192.168.0.37    Database: Dashboard
-- ------------------------------------------------------
-- Server version	5.5.49-0+deb8u1

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
  `name_dashboard` varchar(100) DEFAULT NULL,
  `title_header` varchar(45) DEFAULT NULL,
  `subtitle_header` varchar(45) DEFAULT NULL,
  `color_header` varchar(45) DEFAULT NULL,
  `width` int(11) DEFAULT NULL,
  `height` int(11) DEFAULT NULL,
  `num_rows` int(11) DEFAULT NULL,
  `num_columns` int(11) DEFAULT NULL,
  `user` int(11) DEFAULT NULL,
  `status_dashboard` int(11) DEFAULT NULL,
  `creation_date` timestamp NULL DEFAULT NULL,
  `remains_width` int(11) DEFAULT NULL,
  `remains_height` int(11) DEFAULT NULL,
  `color_background` varchar(45) DEFAULT NULL,
  `external_frame_color` varchar(45) DEFAULT NULL,
  `headerFontColor` varchar(45) DEFAULT '#ffffff',
  `headerFontSize` int(3) DEFAULT '45',
  `logoFilename` varchar(45) DEFAULT NULL,
  `logoLink` varchar(400) DEFAULT NULL,
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Config_widget_dashboard`
--

DROP TABLE IF EXISTS `Config_widget_dashboard`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Config_widget_dashboard` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `name_w` varchar(150) DEFAULT NULL,
  `id_dashboard` int(11) DEFAULT NULL,
  `id_metric` varchar(150) DEFAULT NULL,
  `type_w` varchar(150) DEFAULT NULL,
  `n_row` int(11) DEFAULT NULL,
  `n_column` int(11) DEFAULT NULL,
  `size_rows` int(11) DEFAULT NULL,
  `size_columns` int(11) DEFAULT NULL,
  `title_w` varchar(100) DEFAULT NULL,
  `color_w` varchar(45) DEFAULT NULL,
  `frequency_w` varchar(100) DEFAULT NULL,
  `temporal_range_w` varchar(100) DEFAULT NULL,
  `municipality_w` varchar(100) DEFAULT NULL,
  `infoMessage_w` text,
  `link_w` varchar(1024) DEFAULT NULL,
  `parameters` text,
  `udm` varchar(45) DEFAULT NULL,
  `fontSize` int(3) DEFAULT NULL,
  `fontColor` varchar(7) DEFAULT NULL,
  `frame_color_w` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;
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
  `value_num` int(11) DEFAULT NULL,
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
  `IdMetric` varchar(45) NOT NULL,
  `description` longtext,
  `status` varchar(45) DEFAULT NULL,
  `query` longtext,
  `query2` longtext,
  `queryType` varchar(300) DEFAULT NULL,
  `metricType` varchar(100) DEFAULT NULL,
  `frequency` varchar(100) DEFAULT NULL,
  `processType` varchar(45) DEFAULT NULL,
  `area` varchar(300) DEFAULT NULL,
  `source` varchar(300) DEFAULT NULL,
  `description_short` longtext,
  `dataSource` varchar(100) DEFAULT NULL,
  `threshold` int(11) DEFAULT NULL,
  `thresholdEval` varchar(45) DEFAULT NULL,
  `thresholdEvalCount` int(11) DEFAULT NULL,
  `thresholdTime` int(11) DEFAULT NULL,
  `storingData` int(11) DEFAULT NULL,
  `municipalityOption` int(11) DEFAULT NULL,
  `timeRangeOption` int(11) DEFAULT NULL,
  `colorDefault` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`IdMetric`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
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
  `username` varchar(100) DEFAULT NULL,
  `password` varchar(100) DEFAULT NULL,
  `name` varchar(100) DEFAULT NULL,
  `surname` varchar(100) DEFAULT NULL,
  `email` varchar(200) DEFAULT NULL,
  `reg_data` timestamp NULL DEFAULT NULL,
  `status` int(11) DEFAULT NULL,
  `ret_code` varchar(50) DEFAULT NULL,
  `admin` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`IdUser`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
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

--
-- Table structure for table `Widgets_metrics_association`
--

DROP TABLE IF EXISTS `Widgets_metrics_association`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Widgets_metrics_association` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `id_type_widget` varchar(150) DEFAULT NULL,
  `id_metric_widget` varchar(45) DEFAULT NULL,
  `source_php_widget` varchar(150) DEFAULT NULL,
  `size_rows_widget` int(11) DEFAULT NULL,
  `size_columns_widget` int(11) DEFAULT NULL,
  `color_widgetOption` int(11) DEFAULT NULL,
  `number_metrics_widget` int(11) DEFAULT NULL,
  `parameters` text,
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=169 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2016-12-02 18:55:47
