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

import java.io.FileInputStream;
import java.io.IOException;
import java.io.InputStream;
import java.sql.ResultSet;
import java.util.HashMap;
import java.util.Map;
import java.util.Properties;
import java.util.logging.Logger;

import utility.Utility;

public class ConfigManager {

  private Map<String, String[]> mapProperties = null;
  private final Logger logger = Logger.getLogger(ConfigManager.class.getName());

  public ConfigManager() {
    this.mapProperties = new HashMap<String, String[]>();
  }

  public boolean loadConfigFile() {
    Properties prop = new Properties();
    InputStream input = null;
    String[] property = null;
    try {

      input = new FileInputStream("./config.properties");

      if (input != null) {
        // load a properties file
        prop.load(input);

        // get the property value and print it out
				/*property= new String[4];
         property[0]=prop.getProperty("url");
         property[1]=prop.getProperty("database");
         property[2]=prop.getProperty("user");
         property[3]=prop.getProperty("psw");*/
        this.mapProperties.put("Dashboard", new String[]{prop.getProperty("url"), prop.getProperty("database"), prop.getProperty("user"), prop.getProperty("psw")});

        this.mapProperties.put("AlarmEmail", new String[]{prop.getProperty("mailhost"), prop.getProperty("mailuser"), prop.getProperty("mailpsw"), prop.getProperty("mailport"),
          prop.getProperty("mailssl"), prop.getProperty("mailauth"), prop.getProperty("mailFrom"), prop.getProperty("mailTo")});

        input.close();

        return true;
      } else {
        return false;
      }
    } catch (Exception exp) {
      Utility.WriteExcepLog(logger, exp);
      //System.out.println(ex.getMessage());
      return false;
    }
  }

  public Map<String, String[]> getMapProperties() {
    return this.mapProperties;
  }
}
