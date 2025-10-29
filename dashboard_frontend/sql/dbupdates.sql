USE Dashboard;

ALTER TABLE AccessDefinitions
  ADD COLUMN dashboardID VARCHAR(500) NULL,
  ADD COLUMN collectionID VARCHAR(500) NULL,
  ADD COLUMN maxbyday INT NULL,
  ADD COLUMN maxbymonth INT NULL,
  ADD COLUMN maxtotalaccesses INT NULL;

ALTER TABLE AccessDefinitions
ADD CONSTRAINT uk_authname UNIQUE (authname);