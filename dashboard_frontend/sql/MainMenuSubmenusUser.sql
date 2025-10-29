USE Dashboard;
CREATE TABLE MainMenuSubmenusUser(
  submenu       INT             NOT NULL,
  user     VARCHAR(500)    NOT NULL,
  PRIMARY KEY (submenu, user),
);