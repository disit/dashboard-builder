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

import java.sql.ResultSet;
import java.sql.SQLException;
import java.text.DateFormat;
import java.text.SimpleDateFormat;
import java.util.Date;
import java.util.Map;
import java.util.logging.Logger;
import org.openrdf.query.TupleQueryResult;
import utility.Utility;
import java.io.*;
import java.util.logging.Level;

public class Launcher2 implements Serializable {

  private String typeProcess = null;
  private ResultSet listProcess = null;
  private ManagerQuery managerQV = null;
  private static final String THREAD_NAME = "managerQuery";
  private Map<String, String[]> map_dbAccess = null;
  private final Logger logger = Logger.getLogger(Launcher2.class.getName());

  public Launcher2(ResultSet resultsetMet, Map<String, String[]> db_conf) {
    this.map_dbAccess = db_conf;
    this.listProcess = resultsetMet;
  }

  public void init(String id, String desc, String query, String queryType, String metricType, String processType, Long freq, String dataSourceId, Map<String, String[]> map_dbAccess, AlarmManager alarmMng) {
    this.managerQV = new ManagerQuery(id, desc, query, queryType, metricType, processType, freq, dataSourceId, map_dbAccess, alarmMng);
  }

  public void startManagerQuery() {
    if (getAccessParameters()) {
      if (this.listProcess != null) {
        try {
          while (this.listProcess.next()) {
            try {
              if ((this.listProcess.getString("status").trim()).equals("Attivo")) {
                if (!((this.listProcess.getString("processType").trim()).equals("API"))) {
                  
                  String id = this.listProcess.getString("IdMetric").trim();
                  String desc = this.listProcess.getString("description");
                  //String status = this.listProcess.getString("status").trim();
                  String query = this.listProcess.getString("query");
                  if (query != null) {
                    query = query.trim();
                  }
                  String dataSourceId = this.listProcess.getString("dataSource");
                  String queryType = this.listProcess.getString("queryType").trim();
                  String metricType = this.listProcess.getString("metricType").trim();
                  String processType = this.listProcess.getString("processType").trim();
                  Long freq = Long.parseLong(this.listProcess.getString("frequency"));
                  Integer threshold = null;
                  if (this.listProcess.getString("threshold") != null) {
                    threshold = Integer.parseInt(this.listProcess.getString("threshold"));
                  }
                  Integer thresholdEvalCount = null;
                  if (this.listProcess.getString("thresholdEvalCount") != null) {
                    thresholdEvalCount = Integer.parseInt(this.listProcess.getString("thresholdEvalCount"));
                  }
                  String thresholdEval = null;
                  if (this.listProcess.getString("thresholdEval") != null) {
                    thresholdEval = this.listProcess.getString("thresholdEval");
                  }
                  Integer thresholdTime = null;
                  if (this.listProcess.getString("thresholdTime") != null) {
                    thresholdTime = Integer.parseInt(this.listProcess.getString("thresholdTime"));
                  }
                  //System.out.println(id);
                  Thread threadManagerQuery;
                  AlarmManager alarmMng = new AlarmManager(id, threshold, thresholdEval, thresholdEvalCount, thresholdTime, desc, this.map_dbAccess.get("AlarmEmail"));
                  init(id, desc, query, queryType, metricType, processType, freq, dataSourceId, this.map_dbAccess, alarmMng);
                  threadManagerQuery = new Thread(this.managerQV, THREAD_NAME);
                  threadManagerQuery.start();
                }
                
              }
            } catch (Exception exp) {
              exp.printStackTrace();
              
              Utility.WriteExcepLog(logger, exp);
              
              DateFormat df = new SimpleDateFormat("yyyy-MM-dd HH:mm:ss");
              Date data_attuale = new Date();
              String data_fixed = df.format(data_attuale);
              String msg = exp.getMessage();
              if (msg != null) {
                msg = msg.replace("\n", " ");
              }
              String cause = exp.getCause().getMessage();
              if (cause != null) {
                cause = cause.replace("\n", " ");
              }
              String msgBody = "You're receiving this email from Km4City Dashboard because an error has occurred when the metrics calculation software has tried ";
              msgBody += "to launch the metrics processing threads. This error is generated by " + exp.getClass().getSimpleName() + " exception thrown at " + data_fixed;
              msgBody += "\n\nError Details:";
              msgBody += "\nDescription: Could not acquire the set of metrics to be computed or launch the metrics processing threads";
              msgBody += "\nException: " + msg;
              msgBody += "\nCause: " + cause;
              msgBody += "\nError Trace: See LogFile.log for deatils";
              msgBody += "\nJava Class: " + this.getClass().getName();
              msgBody += "\nDate: " + data_fixed;
              msgBody += "\n\nThis message is generated by the Km4City Dashboard. For support: info@disit.org";
              msgBody += "\nPlease do not reply to this message.";
              Utility.SendEmail(this.map_dbAccess.get("AlarmEmail"), msgBody);
            }
          }
        } catch (SQLException ex) {
          Logger.getLogger(Launcher2.class.getName()).log(Level.SEVERE, null, ex);
        }
      }
    }
  }

  public boolean getAccessParameters() {
    try {
      DBAccess db_access = new DBAccess();
      String queryDataSources = "SELECT* FROM DataSource";
      db_access.setConnectionMySQL(this.map_dbAccess.get("Dashboard"));
      db_access.readDataBase(queryDataSources);
      ResultSet resultSetDataSources = db_access.getResultSet();
      if (resultSetDataSources != null) {
        while (resultSetDataSources.next()) {
          String[] mapValue = new String[(resultSetDataSources.getMetaData().getColumnCount()) - 1];
          mapValue[0] = resultSetDataSources.getString("url");
          mapValue[1] = resultSetDataSources.getString("database");
          mapValue[2] = resultSetDataSources.getString("username");
          mapValue[3] = resultSetDataSources.getString("password");
          mapValue[4] = resultSetDataSources.getString("databaseType");
          this.map_dbAccess.put(resultSetDataSources.getString("Id"), mapValue);
        }
        db_access.close();
        return true;
      } else {
        return false;
      }
    } catch (Exception exp) {

      DateFormat df = new SimpleDateFormat("yyyy-MM-dd HH:mm:ss");
      Date data_attuale = new Date();
      String data_fixed = df.format(data_attuale);
      String msgBody = "You're receiving this email from Km4City Dashboard because an error has occurred when the metrics calculation software has tried ";
      msgBody += "to acquire the set of metrics to be computed or get the access parameters of data sources. This error is generated by " + exp.getClass().getSimpleName() + " exception thrown at " + data_fixed;
      msgBody += "\n\nError Details:";
      msgBody += "\nDescription: Could not acquire the set of metrics to be computed or launch the metrics processing threads";
      msgBody += "\nException: " + exp.getMessage().replace("\n", " ");
      msgBody += "\nCause: " + exp.getCause().getMessage().replace("\n", " ");
      msgBody += "\nError Trace: See LogFile.log for deatils";
      msgBody += "\nJava Class: " + this.getClass().getName();
      msgBody += "\nDate: " + data_fixed;
      msgBody += "\n\nThis message is generated by the Km4City Dashboard. For support: info@disit.org";
      msgBody += "\nPlease do not reply to this message.";
      Utility.WriteInfoLog(logger, this.getClass().getName() + ".java - Error reading databases access parameters");
      Utility.WriteExcepLog(logger, exp);
      return false;
    }
  }

  public ManagerQuery getManagerQuery() {
    return this.managerQV;
  }

  public void setManagerQuery(ManagerQuery managerQV) {
    this.managerQV = managerQV;
  }
}
