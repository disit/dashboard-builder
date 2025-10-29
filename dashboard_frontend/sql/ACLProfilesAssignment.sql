USE Dashboard;
CREATE TABLE `ACLProfilesAssignment` (
  `profileID`  INT          NOT NULL,
  `user`       VARCHAR(500) NOT NULL,
  PRIMARY KEY (`profileID`,`user`),
  FOREIGN KEY (`profileID`)  
    REFERENCES `ACLProfiles`(`ID`)
    ON DELETE CASCADE
    ON UPDATE CASCADE
)
