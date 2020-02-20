/* Dashboard Builder.
   Copyright (C) 2017 DISIT Lab http://www.disit.org - University of Florence

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
import java.net.HttpURLConnection;
import java.net.MalformedURLException;
import java.net.URL;
import java.net.URLEncoder;
import java.util.Calendar;
import java.util.GregorianCalendar;
import java.util.Properties;
import java.util.logging.Level;

public class Launcher2 implements Serializable 
{
  private String typeProcess = null;
  private ResultSet listProcess = null;
  private ManagerQuery managerQV = null;
  private OldDataEvaluationThread oldDataEvaluationThread = null;
  private static final String THREAD_NAME = "managerQuery";
  private Map<String, String[]> map_dbAccess = null;
  private final Logger logger = Logger.getLogger(Launcher2.class.getName());

  public Launcher2(ResultSet resultsetMet, Map<String, String[]> db_conf) 
  {
    this.map_dbAccess = db_conf;
    this.listProcess = resultsetMet;
  }

  public void init(String id, String desc, String query, String queryType, String metricType, String processType, Long freq, String dataSourceId, Map<String, String[]> map_dbAccess /*AlarmManager alarmMng,*/ /*Integer thresholdTime,*/)
  {
    this.managerQV = new ManagerQuery(id, desc, query, queryType, metricType, processType, freq, dataSourceId, map_dbAccess /*alarmMng,*/ /*thresholdTime,*/);
    this.oldDataEvaluationThread = new OldDataEvaluationThread(id, metricType, dataSourceId);
  }

  public void startManagerQuery() 
  {
    if (getAccessParameters()) 
    {
      if (this.listProcess != null) 
      {
        try 
        {
          while (this.listProcess.next()) 
          {
            try 
            {
              if((this.listProcess.getString("status").trim()).equals("Attivo")) 
              {
                if(!((this.listProcess.getString("processType").trim()).equals("API")) && this.listProcess.getString("queryType") != null) 
                {
                  String id = this.listProcess.getString("IdMetric").trim();
                  String desc = this.listProcess.getString("description");
                  String status = this.listProcess.getString("status").trim();
                  String query = this.listProcess.getString("query");
                  
                  if (query != null) 
                  {
                    query = query.trim();
                  }
                  
                  String dataSourceId = this.listProcess.getString("dataSource");
                  String queryType = this.listProcess.getString("queryType").trim();
                  String metricType = this.listProcess.getString("metricType").trim();
                  String processType = this.listProcess.getString("processType").trim();
                  Long freq = Long.parseLong(this.listProcess.getString("frequency"));
                  String sameDataAlarmCount = this.listProcess.getString("sameDataAlarmCount");
                  
                  /*Integer threshold = null;
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
                  }*/
                  
                  //AlarmManager alarmMng = new AlarmManager(id, threshold, thresholdEval, thresholdEvalCount, thresholdTime, desc, this.map_dbAccess.get("AlarmEmail"));
                  init(id, desc, query, queryType, metricType, processType, freq, dataSourceId, this.map_dbAccess /*alarmMng,*/ /*thresholdTime,*/);
                  Thread threadManagerQuery = new Thread(this.managerQV, THREAD_NAME+"_"+id);
                  threadManagerQuery.start();
                  
                  if(this.listProcess.getString("oldDataEvalTime")!=null) {
                    Thread threadOldDataEvaluation = new Thread(this.oldDataEvaluationThread, THREAD_NAME+"_OLD_"+id);
                    threadOldDataEvaluation.start();
                  }
                }
              }
            } 
            catch (Exception exp) 
            {
              exp.printStackTrace();
              
              Utility.WriteExcepLog(logger, exp);
              
              String msgBody=Utility.exceptionMessage(exp, this.getClass().getName(), "Could not acquire the set of metrics to be computed or launch the metrics processing threads");
              this.notifyEvent(this.getClass().getSimpleName(), "Java object", "Process", "Metrics threads launch KO", msgBody);    
              //Utility.SendEmail(this.map_dbAccess.get("AlarmEmail"), msgBody);
            }
          }
        } 
        catch (SQLException ex) 
        {
          Logger.getLogger(Launcher2.class.getName()).log(Level.SEVERE, null, ex);
        }
      }
    }
  }
  
  private void notifyEvent(String generatorOriginalName, String generatorOriginalType, String containerName, String eventType, String furtherDetails)
   {
      Properties conf2 = new Properties();
     
      try 
      {
         FileInputStream in = new FileInputStream("./config.properties");
         conf2.load(in);
         in.close();
      } 
      catch (FileNotFoundException ex) 
      {
         Logger.getLogger(ManagerQuery.class.getName()).log(Level.SEVERE, null, ex);
      }
      catch (IOException ex) 
      {
         Logger.getLogger(ManagerQuery.class.getName()).log(Level.SEVERE, null, ex);
      }
      
      String url = conf2.getProperty("notificatorRestInterfaceUrl");
      if(url.equals(""))
        return;
      String charset = java.nio.charset.StandardCharsets.UTF_8.name();
      String apiUsr = conf2.getProperty("notificatorRestInterfaceUsr");
      String apiPwd = conf2.getProperty("notificatorRestInterfacePwd");
      String operation = "notifyEvent";
      
      Calendar date = new GregorianCalendar();
      SimpleDateFormat sdf = new SimpleDateFormat("yyyy-MM-dd HH:mm:ss");
      String eventTime = sdf.format(date.getTime());
      String appName = "Dashboard Data Process";
      String params = null;
      
		URL obj = null;
      HttpURLConnection con = null;
      try 
      {
         obj = new URL(url);
         con = (HttpURLConnection) obj.openConnection();
         
         con.setRequestMethod("POST");
         con.setRequestProperty("Accept-Charset", charset);
         con.setRequestProperty("Content-Type", "application/x-www-form-urlencoded;charset=" + charset);
         
         params = String.format("apiUsr=%s&apiPwd=%s&appName=%s&operation=%s&generatorOriginalName=%s&generatorOriginalType=%s&containerName=%s&eventType=%s&eventTime=%s&furtherDetails=%s",
         URLEncoder.encode(apiUsr, charset),
         URLEncoder.encode(apiPwd, charset),
         URLEncoder.encode(appName, charset),
         URLEncoder.encode(operation, charset),
         URLEncoder.encode(generatorOriginalName, charset),
         URLEncoder.encode(generatorOriginalType, charset),
         URLEncoder.encode(containerName, charset),
         URLEncoder.encode(eventType, charset),
         URLEncoder.encode(eventTime, charset),
         URLEncoder.encode(furtherDetails, charset));
      } 
      catch (MalformedURLException ex) 
      {
         Logger.getLogger(ManagerQuery.class.getName()).log(Level.SEVERE, null, ex);
      } 
      catch (IOException ex) 
      {
         Logger.getLogger(ManagerQuery.class.getName()).log(Level.SEVERE, null, ex);
      }

		// Questo rende la chiamata una POST
		con.setDoOutput(true);
		DataOutputStream wr = null;
      
      try 
      {
         wr = new DataOutputStream(con.getOutputStream());
         wr.writeBytes(params);
         wr.flush();
         wr.close();
         
         int responseCode = con.getResponseCode();
         String responseMessage = con.getResponseMessage();
      } 
      catch (IOException ex) 
      {
         Logger.getLogger(ManagerQuery.class.getName()).log(Level.SEVERE, null, ex);
      }
   }

  public boolean getAccessParameters() 
  {
    try 
    {
      DBAccess db_access = new DBAccess();
      String queryDataSources = "SELECT * FROM DataSource";
      db_access.setConnection(this.map_dbAccess.get("Dashboard"));
      db_access.readDataBase(queryDataSources);
      ResultSet resultSetDataSources = db_access.getResultSet();
      if (resultSetDataSources != null) 
      {
        while (resultSetDataSources.next()) 
        {
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
      } 
      else 
      {
        return false;
      }
    } 
    catch (Exception exp) 
    {
      String msgBody = Utility.exceptionMessage(exp, this.getClass().getName(), "Could not acquire the set of metrics to be computed or launch the metrics processing threads");
      Utility.WriteInfoLog(logger, this.getClass().getName() + ".java - Error reading databases access parameters");
      Utility.WriteExcepLog(logger, exp);
      this.notifyEvent(this.getClass().getSimpleName(), "Java object", "Process", "Set of metrics acquisition KO", msgBody);
      return false;
    }
  }

  public ManagerQuery getManagerQuery() 
  {
    return this.managerQV;
  }

  public void setManagerQuery(ManagerQuery managerQV) 
  {
    this.managerQV = managerQV;
  }
}
