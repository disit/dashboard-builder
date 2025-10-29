USE Dashboard;
CREATE TABLE ACLProfiles(
  ID       INT    NOT NULL AUTO_INCREMENT,
  profilename     VARCHAR(500)    NOT NULL UNIQUE,
  authIDs  TEXT NULL,
  PRIMARY KEY (ID)
);