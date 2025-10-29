USE Dashboard;
CREATE TABLE ACL (
  ID       INT             NOT NULL AUTO_INCREMENT,
  defID    INT             NOT NULL,
  user     VARCHAR(500)    NOT NULL,
  PRIMARY KEY (ID),
  FOREIGN KEY (defID)      
    REFERENCES AccessDefinitions(ID)
    ON DELETE CASCADE
    ON UPDATE CASCADE
);
