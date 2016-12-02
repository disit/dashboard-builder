/* Dashboard Builder.
   Copyright (C) 2016 DISIT Lab http://www.disit.org - University of Florence

   This program is free software; you can redistribute it and/or
   modify it under the terms of the GNU General Public License
   as published by the Free Software Foundation; either version 2
   of the License, or (at your option) any later version.
   This program is distributed in the hope that it will be useful,
   but WITHOUT ANY WARRANTY; without even the implied warranty of
   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
   GNU General Public License for more details.
   You should have received a copy of the GNU General Public License
   along with this program; if not, write to the Free Software
   Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA. */

import java.io.IOException;
import java.sql.Connection;
import java.sql.DriverManager;
import java.sql.SQLException;
import java.text.DateFormat;
import java.text.SimpleDateFormat;
import java.util.Date;
import java.util.Map;
import java.util.logging.Logger;

import utility.Utility;

import java.sql.ResultSet;

public class Main {

  private static final Logger logger = Logger.getLogger(Main.class.getName());

  public static void main(String[] args) {
    // TODO Auto-generated method stub
    ConfigManager db_conf = new ConfigManager();
    if (db_conf.loadConfigFile()) {
      DBAccess db_access = new DBAccess(db_conf.getMapProperties().get("AlarmEmail"));
      String queryDescriptions = "SELECT* FROM Descriptions";
      db_access.setConnectionMySQL(db_conf.getMapProperties().get("Dashboard"));
      if (db_access.readDataBase(queryDescriptions) != null) {
        ResultSet ResultSetMetrics = db_access.getResultSet();
        Launcher2 launcher = new Launcher2(ResultSetMetrics, db_conf.getMapProperties());
        launcher.startManagerQuery();
        db_access.close();
      }
    } else {
      //System.out.println("Error reading configuration file");
      Utility.WriteInfoLog(logger, Main.class.getName() + ".java - Error reading configuration file");
    }
  }
}
