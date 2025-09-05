USE Dashboard;
CREATE TABLE `DashboardLinkMenu` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `linkUrl` varchar(300) NOT NULL,
  `icon` varchar(200) DEFAULT '',
  `text` varchar(200) DEFAULT '',
  `openMode` varchar(45) DEFAULT 'newTab',
  `iconColor` varchar(45) DEFAULT '#FFFFFF',
  `menuOrder` int(2) DEFAULT 0,
  `dashboardId` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `DashboardLinkMenu_idfk` (`dashboardId`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;






