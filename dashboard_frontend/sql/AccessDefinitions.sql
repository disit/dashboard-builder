USE Dashboard;
CREATE TABLE AccessDefinitions (
  ID       INT             NOT NULL AUTO_INCREMENT,
  authname VARCHAR(500)    NOT NULL UNIQUE,
  org      TEXT            NULL,
  menuID   INT             NULL,
  dashboardID VARCHAR(500) NULL,
  collectionID VARCHAR(500) NULL,
  maxbyday INT NULL,
  maxbymonth INT NULL,
  maxtotalaccesses INT NULL,
  PRIMARY KEY (ID)
);
