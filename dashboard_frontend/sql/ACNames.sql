CREATE TABLE ACNames (
  name VARCHAR(500) NOT NULL,
  PRIMARY KEY (name)
);
USE Dashboard;

INSERT IGNORE INTO ACNames (name)
  SELECT profilename FROM ACLProfiles
UNION
  SELECT authname     FROM AccessDefinitions;

ALTER TABLE ACLProfiles
  ADD CONSTRAINT fk_ACLProfiles_ACNames
    FOREIGN KEY (profilename) REFERENCES ACNames(name);

ALTER TABLE AccessDefinitions
  ADD CONSTRAINT fk_AccessDefs_ACNames
    FOREIGN KEY (authname)    REFERENCES ACNames(name);