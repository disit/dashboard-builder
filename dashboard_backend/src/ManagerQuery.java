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

import java.util.Map;
import java.util.logging.Logger;
import org.apache.http.HttpResponse;
import org.apache.http.client.HttpClient;
import org.apache.http.client.methods.HttpGet;
import org.apache.http.impl.client.DefaultHttpClient;
import org.apache.http.params.BasicHttpParams;
import org.apache.http.params.HttpConnectionParams;
import org.apache.http.params.HttpParams;
import org.json.JSONArray;
import org.json.JSONObject;
import org.openrdf.query.BindingSet;
import org.openrdf.query.QueryLanguage;
import org.openrdf.query.TupleQuery;
import org.openrdf.query.TupleQueryResult;
import org.openrdf.repository.Repository;
import org.openrdf.repository.RepositoryConnection;
import org.openrdf.repository.sparql.SPARQLRepository;
import virtuoso.sesame2.driver.VirtuosoRepository; 
import utility.Configuration;
import utility.Utility;
import java.util.Date;
import java.io.BufferedReader;
import java.io.InputStreamReader;
import java.io.Serializable;
import java.sql.ResultSet;
import java.sql.SQLException;
import java.sql.Timestamp;
import java.text.DateFormat;
import java.text.SimpleDateFormat;
import java.util.logging.Level;
import com.google.gson.Gson;
import java.io.DataOutputStream;
import java.io.FileInputStream;
import java.io.FileNotFoundException;
import java.io.IOException;
import java.net.HttpURLConnection;
import java.net.MalformedURLException;
import java.net.URL;
import java.net.URLEncoder;
import java.sql.Connection;
import java.sql.DriverManager;
import java.sql.Statement;
import java.util.Calendar;
import java.util.GregorianCalendar;
import java.util.Properties;
import org.disit.model.*;

public class ManagerQuery implements Runnable, Serializable 
{
  protected String idProc = null;
  private String descrip = null;
  private String query = null;
  private String queryType = null;
  private String metricType = null;
  private String processType = null;
  private String sparqlEndpoint = null;
  private String sparqlType = null;
  private int maxTime = 0;
  private String km4cVersion = null;
  private String dataSourceId = null;
  private Map<String, String[]> map_dbAcc = null;
  private Configuration conf = null;
  private boolean started;
  private Long sleepTime;
  //private AlarmManager almMng;
  //private Integer thresholdTime;
  private long sameDataAlarmCount;

  private final Logger logger = Logger.getLogger(ManagerQuery.class.getName());

  public ManagerQuery(String id, String desc, String query, String queryType, String metricType, String processType, Long freq, String dataSourceId, Map<String, String[]> map_dbAccess /*AlarmManager alarmMng,*/ /*Integer thresholdTime,*/) 
  {
    this.map_dbAcc = map_dbAccess;
    this.conf = Configuration.getInstance();
    this.sparqlEndpoint = conf.get("sparqlEndpoint", (map_dbAccess.get("Km4CityRDF")[0]));
    this.sparqlType = conf.getSet("sparqlType", "virtuoso");
    this.maxTime = 3 * 60 * (sparqlType.equals("virtuoso") ? 1000 : 1); //3 min
    this.km4cVersion = conf.getSet("km4cVersion", "new");
    this.idProc = id;
    this.descrip = desc;
    this.query = query;
    this.queryType = queryType;
    this.metricType = metricType;
    this.processType = processType;
    this.sleepTime = freq;
    this.dataSourceId = dataSourceId;
    //this.almMng = alarmMng;
    //this.thresholdTime = thresholdTime;
  }

  public void run() 
  {
     double lastMetricValue;
     this.started = true;
     
    try 
    {
      Thread.sleep((int) (Math.random() * 5000)); //aspetta tempo random 0-5 secondi
    } 
    catch (InterruptedException ex) 
    {
       
    }
    
    while(this.started) 
    {
      switch (this.processType) 
      {
        case "JVNum1":
          lastMetricValue = executeQueryJVNum();
          if(this.checkConstantDataForTooMuchTime())
          {
             System.out.println("Dati costanti per troppi campionamenti per metrica: " + this.idProc);
             notifyEvent("Same data for too much time", "Process: " + this.idProc);
          }
          else
          {
             this.checkMetricWidgetsRules(lastMetricValue);
          }
          break;
          
        case "JVPerc":
          lastMetricValue = executeQueryJVPerc();
          if(this.checkConstantDataForTooMuchTime())
          {
             System.out.println("Dati costanti per troppi campionamenti per metrica: " + this.idProc);
             notifyEvent("Same data for too much time", "Process: " + this.idProc);
          }
          else
          {
             this.checkMetricWidgetsRules(lastMetricValue);
          }
          break;
          
        case "JVTable":
          executeQueryJVTable();
          if(this.checkConstantDataForTooMuchTime())
          {
             notifyEvent("Same data for too much time", "Process: " + this.idProc);
          }
          else
          {
             //CI AGGIUNGERAI LA SUA VERSIONE DEL METODO DI CONTROLLO SOGLIA
          }
          break;
          
        case "JVRidesAtaf":
          executeQueryJVRidesAtaf();
          if(this.checkConstantDataForTooMuchTime())
          {
             System.out.println("Dati costanti per troppi campionamenti per metrica: " + this.idProc);
             notifyEvent("Same data for too much time", "Process: " + this.idProc);
          }
          else
          {
             //CI AGGIUNGERAI LA SUA VERSIONE DEL METODO DI CONTROLLO SOGLIA
          }
          break;
          
        case "JVSceOnNodes":
          executeQueryJVSce();
          if(this.checkConstantDataForTooMuchTime())
          {
             System.out.println("Dati costanti per troppi campionamenti per metrica: " + this.idProc);
             notifyEvent("Same data for too much time", "Process: " + this.idProc);
          }
          else
          {
             //CI AGGIUNGERAI LA SUA VERSIONE DEL METODO DI CONTROLLO SOGLIA
          }
          break;
          
        case "jVPark":
          lastMetricValue = executeQueryJVFreePark();
          if(this.checkConstantDataForTooMuchTime())
          {
             System.out.println("Dati costanti per troppi campionamenti per metrica: " + this.idProc);
             notifyEvent("Same data for too much time", "Process: " + this.idProc);
          }
          else
          {
             this.checkMetricWidgetsRules(lastMetricValue);
          }
          break;
          
        case "JVWifiOp":
          lastMetricValue = executeQueryJVWifiOp();
          if(this.checkConstantDataForTooMuchTime())
          {
             System.out.println("Dati costanti per troppi campionamenti per metrica: " + this.idProc);
             notifyEvent("Same data for too much time", "Process: " + this.idProc);
          }
          else
          {
             this.checkMetricWidgetsRules(lastMetricValue);
          }
          break;
          
        case "JVSmartDs":
          executeQueryJVSmartDs();
          if(this.checkConstantDataForTooMuchTime())
          {
             System.out.println("Dati costanti per troppi campionamenti per metrica: " + this.idProc);
             notifyEvent("Same data for too much time", "Process: " + this.idProc);
          }
          else
          {
             //CI AGGIUNGERAI LA SUA VERSIONE DEL METODO DI CONTROLLO SOGLIA
          }
          break;
          
        case "JVTwRet":
          executeQueryJVTwRet();
          if(this.checkConstantDataForTooMuchTime())
          {
             System.out.println("Dati costanti per troppi campionamenti per metrica: " + this.idProc);
             notifyEvent("Same data for too much time", "Process: " + this.idProc);
          }
          else
          {
             //CI AGGIUNGERAI LA SUA VERSIONE DEL METODO DI CONTROLLO SOGLIA
          }
          break;
          
        default:
            break;
      }
      
      try 
      {
        System.out.println(this.idProc + ": Sleep " + sleepTime);
        Thread.sleep(this.sleepTime);
      }
      catch (InterruptedException exp) 
      {
        //System.out.println(exp.getMessage());
        Utility.WriteExcepLog(this.logger, exp);
      }
    }
  }
  
  private void checkMetricWidgetsRules(double lastMetricValue)
  {
     Parameters parameters;
     Rule rule; 
     Gson gson = new Gson();
     boolean notifyEvents;
     //String min;
     //String max;
     String op;
     String thr1;
     String thr2;
     String desc;
     String widgetId;
     String widgetName;
     String widgetTitle;
     String dashboardId;
     String dashboardTitle;
     
     Properties conf2 = new Properties();
     FileInputStream in = null;
     try 
     {
         in = new FileInputStream("./config.properties");
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
     
     //Reperimento elenco delle regole dagli widget che mostrano la metrica in esame
     Connection conn;
     try 
     {
        conn = DriverManager.getConnection(conf2.getProperty("url"), conf2.getProperty("user"), conf2.getProperty("psw"));
        Statement stm = conn.createStatement();
        
        String getRulesQuery = "SELECT widgets.Id AS widgetId, widgets.name_w AS widgetName, widgets.title_w AS widgetTitle, widgets.id_dashboard AS dashboardId, widgets.parameters AS parameters, " + 
                               "dashboards.name_dashboard AS dashboardTitle " +
                               "FROM Dashboard.Config_widget_dashboard AS widgets " +
                               "INNER JOIN Dashboard.Config_dashboard AS dashboards " +
                               "ON dashboards.Id = widgets.id_dashboard " +
                               "WHERE widgets.type_w IN('widgetBarContent', 'widgetColunmContent', 'widgetGaugeChart', 'widgetSingleContent', 'widgetSpeedometer', 'widgetTimeTrend', 'widgetTimeTrendCompare') " +//Questo elenco è temporaneo, verrà ampliato via via che gestiamo più widgets
                               "AND widgets.parameters IS NOT NULL " +
                               "AND widgets.parameters <> '{}'" +
                               "AND widgets.id_metric = '" + this.idProc + "'";
     
        ResultSet rs = stm.executeQuery(getRulesQuery);
        
        if(rs != null) 
        {
            try
            {
                while(rs.next())
                {
                  parameters = gson.fromJson(rs.getString("parameters"), Parameters.class);
                  if((parameters == null) || (parameters.getThresholdObject() == null)) 
                  {
                    System.out.println("Widget " + rs.getString("widgetId") + " has invalid parameters " + rs.getString("parameters"));
                    this.notifyEvent("Invalid parameters", "Widget name: " + rs.getString("widgetName") + " - Widget id: " + rs.getString("widgetId"));
                    continue;
                  }
                  
                  for(SingleRule singleRule : parameters.getThresholdObject()) 
                  {
                      rule = new Rule(singleRule.getNotifyEvents(), singleRule.getOp(), singleRule.getThr1(), singleRule.getThr2(), singleRule.getDesc(), String.valueOf(rs.getInt("widgetId")), rs.getString("widgetName"), rs.getString("widgetTitle"), this.idProc, String.valueOf(rs.getInt("dashboardId")), rs.getString("dashboardTitle"));
                      notifyEvents = singleRule.getNotifyEvents();
                      //Ma sono usati questi valori? Sembra di no (forse erano nel println?), provare a rimuoverli (commenta)
                      op = singleRule.getOp();
                      thr1 = singleRule.getThr1();
                      thr2 = singleRule.getThr2();
                      desc = singleRule.getDesc();
                      widgetId = String.valueOf(rs.getInt("widgetId"));
                      widgetName = rs.getString("widgetName");
                      widgetTitle = rs.getString("widgetTitle");
                      dashboardId = String.valueOf(rs.getInt("dashboardId"));
                      dashboardTitle = rs.getString("dashboardTitle");
                      
                      //System.out.println("Thr1: " + rule.getThr1() + " - Thr2: " + rule.getThr2());

                      if(this.checkThrViolation(lastMetricValue, rule)&&notifyEvents)
                      {
                         System.out.println("Notifica eseguita");
                         this.notifyEvent(lastMetricValue, rule);
                      }
                      else
                      {
                         System.out.println("Notifica NON eseguita");
                      }
                  } 
               }
               conn.close();
            }
            catch(SQLException ex) 
            {
               Logger.getLogger(ManagerQuery.class.getName()).log(Level.SEVERE, null, ex);
            }
         }
     } 
     catch (SQLException ex) 
     {
        Logger.getLogger(ManagerQuery.class.getName()).log(Level.SEVERE, null, ex);
     }
  }
  
  public boolean checkConstantDataForTooMuchTime() 
  {
     String metricData = "";
     int oldDataInt = -1;
     int newDataInt = -1;
     double oldDataDouble = -1;
     double newDataDouble = -1;
     String oldDataString = "";
     String newDataString = "";
    
     Properties conf2 = new Properties();
     FileInputStream in = null;
     try 
     {
         in = new FileInputStream("./config.properties");
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
     
     Connection conn;
     try 
     {
        conn = DriverManager.getConnection(conf2.getProperty("url"), conf2.getProperty("user"), conf2.getProperty("psw"));
        Statement stm = conn.createStatement();
        
        //SELEZIONA IL METRIC TYPE E, IN BASE AD ESSO, USA IL CAMPO GIUSTO DELLA TABELLA DEI DATI.
        switch(this.metricType)
        {
           case "Intero":
              metricData = "value_num";
              break;
              
           case "Float":
              metricData = "value_num";
              break;
              
           case "Testuale":
              metricData = "value_text";
              break;
              
           case "Series":
              metricData = "series";
              break;
        }
        
        if(this.metricType.contains("Percentuale"))
        {
           metricData = "value_perc1";
        }
        
        String localQuery1 = "SELECT Descriptions.sameDataAlarmCount FROM Dashboard.Descriptions WHERE Descriptions.IdMetric = '" + this.idProc + "' AND sameDataAlarmCount IS NOT NULL";
        ResultSet rs1;
        try 
        {
           rs1 = stm.executeQuery(localQuery1);
           if(rs1 != null) 
           {
              try
              {
                 if(rs1.next()) {
                    this.sameDataAlarmCount = rs1.getLong("sameDataAlarmCount");

                    String localQuery2 = "SELECT Data." + metricData + " " +
                                         "FROM Dashboard.Data " +
                                         "WHERE Data.IdMetric_data = '" + this.idProc + "' " +
                                         "ORDER BY Data.computationDate DESC " +
                                         "LIMIT " + this.sameDataAlarmCount;

                    ResultSet rs2 = stm.executeQuery(localQuery2);

                    if(rs2 != null) 
                    {
                       try
                       {
                          while(rs2.next())
                             {
                                 if(rs2.isFirst())
                                 {
                                   switch(this.metricType)
                                   {
                                      case "Intero":
                                         oldDataInt = rs2.getInt(metricData);
                                         newDataInt = rs2.getInt(metricData);
                                         break;

                                      case "Float":
                                         oldDataDouble = rs2.getDouble(metricData);
                                         newDataDouble = rs2.getDouble(metricData);
                                         break;

                                      case "Testuale":
                                         oldDataString = rs2.getString(metricData);
                                         newDataString = rs2.getString(metricData);
                                         break;

                                      case "Series":
                                         oldDataString = rs2.getString(metricData);
                                         newDataString = rs2.getString(metricData);
                                         break;
                                   }

                                   if(this.metricType.contains("Percentuale"))
                                   {
                                      oldDataDouble = rs2.getDouble(metricData);
                                      newDataDouble = rs2.getDouble(metricData);
                                   }
                                 }
                                 else
                                 {
                                   switch(this.metricType)
                                   {
                                      case "Intero":
                                         oldDataInt = newDataInt;
                                         newDataInt = rs2.getInt(metricData);
                                         if(oldDataInt != newDataInt)
                                         {
                                            return false;
                                         }
                                         break;

                                      case "Float":
                                         oldDataDouble = newDataDouble;
                                         newDataDouble = rs2.getDouble(metricData);
                                         if(oldDataDouble != newDataDouble)
                                         {
                                            return false;
                                         }
                                         break;

                                      case "Testuale":
                                         oldDataString = newDataString;
                                         newDataString = rs2.getString(metricData);
                                         if(!oldDataString.equals(newDataString))
                                         {
                                            return false;
                                         }
                                         break;

                                      case "Series":
                                         oldDataString = newDataString;
                                         newDataString = rs2.getString(metricData);
                                         if(!oldDataString.equals(newDataString))
                                         {
                                            return false;
                                         }
                                         break;
                                   }

                                   if(this.metricType.contains("Percentuale"))
                                   {
                                      oldDataDouble = newDataDouble;
                                      newDataDouble = rs2.getDouble(metricData);
                                      if(oldDataDouble != newDataDouble)
                                      {
                                         return false;
                                      }
                                   }
                                 }
                             }
                            //Se si esce dal ciclo senza aver mai ritornato, allora i dati sono costanti
                            return true;
                      }
                      catch(SQLException ex) 
                      {
                          Logger.getLogger(ManagerQuery.class.getName()).log(Level.SEVERE, null, ex);
                          return false;
                      }
                   }
                   else
                   {
                      return false;
                   }
                } 
                else 
                {
                   return false;
                }
              }
              catch(SQLException ex) 
              {
                  Logger.getLogger(ManagerQuery.class.getName()).log(Level.SEVERE, null, ex);
                  return false;
              }
           }
           else
           {
              return false;
           }
        }
        catch(SQLException ex) 
        {
           Logger.getLogger(ManagerQuery.class.getName()).log(Level.SEVERE, null, ex);
           return false;
        }
      }
      catch(SQLException ex) 
      {
         Logger.getLogger(ManagerQuery.class.getName()).log(Level.SEVERE, null, ex);
         return false;
      }
  }

   private boolean checkThrViolation(Double value, Rule rule)
   {
      boolean result = false;
      
      switch(rule.getOp())
      {
         case "less":
            if(value < Double.parseDouble(rule.getThr1()))
            {
               result = true;
            }
            break;
            
         case "lessEqual":
            if(value <= Double.parseDouble(rule.getThr1()))
            {
               result = true;
            }
            break;
            
         case "greater":
            if(value > Double.parseDouble(rule.getThr1()))
            {
               result = true;
            }
            break;
            
         case "greaterEqual":
            if(value >= Double.parseDouble(rule.getThr1()))
            {
               result = true;
            }
            break;
            
         case "equal":
            if(value == Double.parseDouble(rule.getThr1()))
            {
               result = true;
            }
            break;
            
         case "notEqual":
            if(value != Double.parseDouble(rule.getThr1()))
            {
               result = true;
            }
            break;
            
         case "intervalOpen":
            if((value > Double.parseDouble(rule.getThr1()))&&(value < Double.parseDouble(rule.getThr2())))
            {
               result = true;
            }
            break;
            
         case "intervalClosed":
            
            if((value >= Double.parseDouble(rule.getThr1()))&&(value <= Double.parseDouble(rule.getThr2())))
            {
               result = true;
            }
            break;
            
         case "intervalLeftOpen":
            if((value > Double.parseDouble(rule.getThr1()))&&(value <= Double.parseDouble(rule.getThr2())))
            {
               result = true;
            }
            break;
            
         case "intervalRightOpen":
            if((value >= Double.parseDouble(rule.getThr1()))&&(value < Double.parseDouble(rule.getThr2())))
            {
               result = true;
            }
            break;   
      }
      
      return result;
   }
   
   //Per mandare eventi sulle metriche (dati costanti da troppo tempo, ...)
   private void notifyEvent(String eventType, String furtherDetails)
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
      String charset = java.nio.charset.StandardCharsets.UTF_8.name();
      String apiUsr = conf2.getProperty("notificatorRestInterfaceUsr");
      String apiPwd = conf2.getProperty("notificatorRestInterfacePwd");
      String operation = "notifyEvent";
      String generatorOriginalName = this.idProc;
      String generatorOriginalType = this.metricType;
      String containerName = this.dataSourceId;
      
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
   
   //Per mandare eventi sugli widget, cioè sulle soglie d'allarme poste sui valori.
   private void notifyEvent(double valueNum, Rule rule)
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
      String charset = java.nio.charset.StandardCharsets.UTF_8.name();
      String apiUsr = conf2.getProperty("notificatorRestInterfaceUsr");
      String apiPwd = conf2.getProperty("notificatorRestInterfacePwd");
      String operation = "notifyEvent";
      String generatorOriginalName = rule.getWidgetTitle();
      String generatorOriginalType = rule.getMetricName();
      String containerName = rule.getDashboardTitle();
      String eventType = rule.getEventType();
      String value = Double.toString(valueNum);
      
      Calendar date = new GregorianCalendar();
      SimpleDateFormat sdf = new SimpleDateFormat("yyyy-MM-dd HH:mm:ss");
      String eventTime = sdf.format(date.getTime());
      String appName = "Dashboard Manager";
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
         
         params = String.format("apiUsr=%s&apiPwd=%s&appName=%s&operation=%s&generatorOriginalName=%s&generatorOriginalType=%s&containerName=%s&eventType=%s&eventTime=%s&value=%s",
         URLEncoder.encode(apiUsr, charset),
         URLEncoder.encode(apiPwd, charset),
         URLEncoder.encode(appName, charset),
         URLEncoder.encode(operation, charset),
         URLEncoder.encode(generatorOriginalName, charset),
         URLEncoder.encode(generatorOriginalType, charset),
         URLEncoder.encode(containerName, charset),
         URLEncoder.encode(eventType, charset),
         URLEncoder.encode(eventTime, charset),
         URLEncoder.encode(value, charset));
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
  
  private double executeQueryJVNum() 
  {
     double sumFixed = -1;
    try 
    {
      if(this.queryType.equals("SPARQL")) 
      {
        Repository repo = buildSparqlRepository();
        repo.initialize();
        RepositoryConnection con = repo.getConnection();

        TupleQuery tupleQueryNum = con.prepareTupleQuery(QueryLanguage.SPARQL, this.query);
        TupleQueryResult resultNum = tupleQueryNum.evaluate();

        if(this.idProc.equals("Events_Day")) 
        {
          DateFormat df = new SimpleDateFormat("yyyy-MM-dd");
          Date data_inizio = new Date();
          String data_inizio_fixed = df.format(data_inizio);
          if (resultNum != null) 
          {
            int count = 0;
            while(resultNum.hasNext()) 
            {
              BindingSet bindingSetEvent = resultNum.next();
              String valueOfEDate = "";
              String valueOfSDate = "";
              if (bindingSetEvent.getValue("endDate") != null) 
              {
                valueOfEDate = bindingSetEvent.getValue("endDate").stringValue();
              }
              if (bindingSetEvent.getValue("startDate") != null) 
              {
                valueOfSDate = bindingSetEvent.getValue("startDate").stringValue();
              }
              if ((!valueOfEDate.equals("")) || (valueOfEDate.equals("") && (valueOfSDate.equals(data_inizio_fixed)))) 
              {
                count++;
              }
            }
            sumFixed = count;
          }
        } 
        else 
        {
          if (resultNum != null) 
          {
            while (resultNum.hasNext()) 
            {
              BindingSet bindingSetEvent = resultNum.next();

              String sum = "0";
              if (bindingSetEvent.getValue("sum") != null) 
              {
                sum = bindingSetEvent.getValue("sum").stringValue();
              }
              sumFixed = Double.parseDouble(sum);
            }
          }
        }
      } 
      else if(this.queryType.equals("SQL")) 
      {
        DBAccess mysql_access = new DBAccess(this.map_dbAcc.get("AlarmEmail"));
        mysql_access.setConnection(this.map_dbAcc.get(this.dataSourceId));
        ResultSet resultSet = mysql_access.readDataBase(this.query,this);
        if (resultSet != null) 
        {
          while (resultSet.next()) 
          {
            String sum = resultSet.getString(1);
            if (sum != null) 
            {
              sumFixed = Double.parseDouble(sum);
            }
          }
        }
        mysql_access.close();
      }

      if (sumFixed != -1) 
      {
        //this.almMng.updateStatusOnMeasuredValue(Utility.round(sumFixed, 2));
        System.out.println(this.idProc + " " + sumFixed);
        DBAccess mysql_access2 = new DBAccess(this.map_dbAcc.get("AlarmEmail"));
        mysql_access2.setConnection(this.map_dbAcc.get("Dashboard"));
        DateFormat df2 = new SimpleDateFormat("yyyy-MM-dd HH:mm:ss");
        Date data_attuale = new Date();
        String data_attuale_fixed = df2.format(data_attuale);
        //System.out.println(data_attuale_fixed);
        String query_insert = "INSERT INTO Dashboard.Data"
                + "(IdMetric_data, computationDate, value_num, quant_perc1) VALUES"
                + "(\"" + this.idProc + "\",\"" + data_attuale_fixed + "\"," + ((int)sumFixed) + ","+ sumFixed +")";

        mysql_access2.writeDataBaseData(query_insert);
        mysql_access2.close();
      }
    } 
    catch(Exception exp) 
    {
      System.out.println("Exception for metric: "+this.idProc);
      exp.printStackTrace();
      
      Utility.WriteExcepLog(this.logger, exp);
      String msgBody = Utility.exceptionMessage(exp, this.getClass().getName(), this.idProc+" - "+this.descrip);
      this.notifyEvent("Import data error", msgBody);
      //ErrorEmailSetUp(this.idProc, this.descrip, exp);
    }
    
    return sumFixed;
  }

  private double executeQueryJVPerc() 
  {
    double percent = -1;
    int sumFixed = 0;
    int tot = 0;
    
    try 
    {
      if (this.queryType.equals("SPARQL")) 
      {
        Repository repo = buildSparqlRepository();
        repo.initialize();
        RepositoryConnection con = repo.getConnection();

        if (this.idProc.equals("Meteo_Rt") || this.idProc.equals("Park_Rt") || this.idProc.equals("Sensors_Rt")) 
        {
          TupleQuery tupleQueryPerc = con.prepareTupleQuery(QueryLanguage.SPARQL, this.query);
          TupleQueryResult resultPerc = tupleQueryPerc.evaluate();
          if (resultPerc != null) 
          {
            while (resultPerc.hasNext()) 
            {
              BindingSet bindingSetPerc = resultPerc.next();
              String sum = bindingSetPerc.getValue("sum").stringValue();
              sumFixed = Integer.parseInt(sum);
            }
          }

          String[] parts_metricType = this.metricType.split("\\/");
          tot = Integer.parseInt(parts_metricType[1]);

        } 
        else if (this.idProc.equals("Ataf_Rt")) 
        {
          String[] queries = this.query.split("\\|");

          if(queries.length>1) 
          {
            TupleQuery tupleQueryPerc2 = con.prepareTupleQuery(QueryLanguage.SPARQL, queries[1].trim());
            TupleQueryResult resultPerc2 = tupleQueryPerc2.evaluate();
            if (resultPerc2 != null) 
            {
              while (resultPerc2.hasNext()) 
              {
                BindingSet bindingSetEvent2 = resultPerc2.next();
                String sum = bindingSetEvent2.getValue("sum").stringValue();
                sumFixed = Integer.parseInt(sum);
              }
            }

            String[] dataSources = this.dataSourceId.split("\\|");
            DBAccess mysql_access = new DBAccess();
            mysql_access.setConnection(this.map_dbAcc.get(dataSources[0].trim()));
            ResultSet resultSet = mysql_access.readDataBase(queries[0].trim(),this);
            while (resultSet.next()) 
            {
              tot = Integer.parseInt(resultSet.getString("Sum"));
            }
            mysql_access.close();
          } 
          else 
          {
            System.out.println("ATAF_RT: manca seconda query");
          }
        } 
        else if(this.idProc.equals("Services_Duplicate")) 
        {
          TupleQuery tupleQueryPerc3 = con.prepareTupleQuery(QueryLanguage.SPARQL, this.query);
          TupleQueryResult resultPerc3 = tupleQueryPerc3.evaluate();
          if (resultPerc3 != null) 
          {
            while (resultPerc3.hasNext()) 
            {
              BindingSet bindingSetEvent = resultPerc3.next();
              String totServ = bindingSetEvent.getValue("totServ").stringValue();
              String perc = bindingSetEvent.getValue("result").stringValue();
              percent = Double.parseDouble(perc);
              tot = Integer.parseInt(totServ);
            }
          }
        } 
        else
        {
          if(!this.query.contains("|")) 
          {
            TupleQuery tupleQueryPerc = con.prepareTupleQuery(QueryLanguage.SPARQL, this.query);
            TupleQueryResult resultPerc = tupleQueryPerc.evaluate();
            if (resultPerc != null) 
            {
              String p1 = resultPerc.getBindingNames().get(0);
              String p2 = null;
              if(resultPerc.getBindingNames().size()>1)
              {
                 p2 = resultPerc.getBindingNames().get(1);
              }
                
              if (resultPerc.hasNext()) 
              {
                BindingSet bindingSetEvent = resultPerc.next();
                String v1 = bindingSetEvent.getValue(p1).stringValue();
                if(p2==null) 
                {
                  if(this.metricType.contains("/")) 
                  {
                    sumFixed = Integer.parseInt(v1);
                    tot = Integer.parseInt(this.metricType.split("\\/")[1]);
                  } 
                  else 
                  {
                    percent = Double.parseDouble(v1);
                  }
                } 
                else 
                {
                  String v2 = bindingSetEvent.getValue(p2).stringValue();
                  sumFixed = Integer.parseInt(v1);
                  tot = Integer.parseInt(v2);
                }
              }
            }            
          } 
          else 
          {
            String[] queries = this.query.split("\\|");
            TupleQuery tupleQueryPerc1 = con.prepareTupleQuery(QueryLanguage.SPARQL, queries[0].trim());
            TupleQueryResult resultPerc1 = tupleQueryPerc1.evaluate();
            if (resultPerc1 != null) 
            {
              String p1 = resultPerc1.getBindingNames().get(0);
              if (resultPerc1.hasNext()) 
              {
                BindingSet bindingSetEvent = resultPerc1.next();
                String v1 = bindingSetEvent.getValue(p1).stringValue();
                sumFixed = Integer.parseInt(v1);
              }
            }            
            
            //TBD gestire caso in cui seconda query e' SQL e non SPARQL
            TupleQuery tupleQueryPerc2 = con.prepareTupleQuery(QueryLanguage.SPARQL, queries[1].trim());
            TupleQueryResult resultPerc2 = tupleQueryPerc2.evaluate();
            if (resultPerc2 != null) 
            {
              String p1 = resultPerc2.getBindingNames().get(0);
              if (resultPerc2.hasNext()) 
              {
                BindingSet bindingSetEvent = resultPerc2.next();
                String v1 = bindingSetEvent.getValue(p1).stringValue();
                tot = Integer.parseInt(v1);
              }
            }                        
          }
        }
      } 
      else if (this.queryType.equals("SQL")) 
      {
        String[] queries2 = this.query.split("\\|");
        DBAccess mysql_access = new DBAccess(this.map_dbAcc.get("AlarmEmail"));
        mysql_access.setConnection(this.map_dbAcc.get(this.dataSourceId));
        if(queries2.length > 1) 
        {
          ResultSet resultSet = mysql_access.readDataBase(queries2[1].trim(),this);
          while (resultSet.next()) 
          {
            String tot_extract = resultSet.getString(1);
            if (tot_extract != null) 
            {
              tot = Integer.parseInt(tot_extract);
            }
          }
        }

        ResultSet resultSet = mysql_access.readDataBase(queries2[0].trim(),this);
        while (resultSet.next()) 
        {
          String value = resultSet.getString(1);
          if (value != null) 
          {
            if(tot!=0)
            {
               sumFixed = Integer.parseInt(value);
            } 
            else if(resultSet.getMetaData().getColumnCount() > 1) 
            {
              tot = resultSet.getInt(2);
              sumFixed = resultSet.getInt(1);
            }
            else 
            {
              if(this.metricType.contains("/")) 
              {
                sumFixed = resultSet.getInt(1);
                tot = Integer.parseInt(this.metricType.split("\\/")[1]);
              } 
              else 
              {
                percent = resultSet.getDouble(1);
              }
            }
          }
        }
        mysql_access.close();
      }

      if (!(this.idProc.equals("Services_Duplicate")) && tot!=0 ) 
      {
        percent = (sumFixed * 100.0) / tot;
      }

      if(percent>=0) 
      {
        //this.almMng.updateStatusOnMeasuredValue(Utility.round(percent, 2));
        System.out.println(this.idProc + " " + percent);

        DBAccess mysql_access2 = new DBAccess(this.map_dbAcc.get("AlarmEmail"));
        mysql_access2.setConnection(this.map_dbAcc.get("Dashboard"));
        DateFormat df = new SimpleDateFormat("yyyy-MM-dd HH:mm:ss");
        Date data_attuale = new Date();
        String data_attuale_fixed = df.format(data_attuale);
        String query_insert = "INSERT INTO Dashboard.Data"
                + "(IdMetric_data, computationDate, value_perc1, quant_perc1, tot_perc1) VALUES"
                + "(\"" + this.idProc + "\",\"" + data_attuale_fixed + "\",\"" + percent + "\",\"" + sumFixed + "\",\"" + tot + "\")";
        mysql_access2.writeDataBaseData(query_insert);
        mysql_access2.close();
      }
    } 
    catch(Exception exp) 
    {
      Utility.WriteExcepLog(this.logger, exp);
      String msgBody = Utility.exceptionMessage(exp, this.getClass().getName(), this.idProc+" - "+this.descrip);      
      this.notifyEvent("Import data error", msgBody);
    }
    
    return percent;
  }

  private void executeQueryJVTable() 
  {
    try 
    {
      String labels1 = "";
      String labels2 = "";
      String desc = "";
      String series = "";

      if (this.queryType.equals("SPARQL")) 
      {
        Repository repo = buildSparqlRepository();
        repo.initialize();
        RepositoryConnection con = repo.getConnection();

        TupleQuery tupleQuery = con.prepareTupleQuery(QueryLanguage.SPARQL, this.query);
        TupleQueryResult result = tupleQuery.evaluate();

        if (result != null) {
          desc = result.getBindingNames().get(0);
          for(int i=1; i<result.getBindingNames().size(); i++) {
            if(!labels1.isEmpty())
              labels1 += ",";
            labels1 += "\""+result.getBindingNames().get(i)+"\"";
          }
          while (result.hasNext()) {
            BindingSet binding = result.next();

            if(!series.isEmpty())
              series += ", ";
            series += "[";
            for(int i=0; i<result.getBindingNames().size(); i++) {
              String n = result.getBindingNames().get(i);
              String v = "";
              if (binding.getValue(n) != null) {
                v = binding.getValue(n).stringValue();
              }
              if(i==0) {
                if(!labels2.isEmpty())
                  labels2 += ", ";
                labels2 += "\""+v+"\"";
              } else {
                if(i>1)
                  series += ", ";
                if(v.matches("-?\\d+(\\.\\d+)?"))
                  series += v;
                else
                  series += "\""+v+"\"";
              }
            }
            series += "]";
          }
        }
      } 
      else if (this.queryType.equals("SQL")) 
      {
        DBAccess mysql_access = new DBAccess(this.map_dbAcc.get("AlarmEmail"));
        mysql_access.setConnection(this.map_dbAcc.get(this.dataSourceId));
        ResultSet resultSet = mysql_access.readDataBase(this.query,this);
        if(resultSet != null)
        {
          int nCols = resultSet.getMetaData().getColumnCount();
          if(nCols > 0)
          {
             desc = resultSet.getMetaData().getColumnLabel(1);
          }
            
          for(int i=2; i<=nCols; i++) 
          {
            if(!labels1.isEmpty())
            {
               labels1 += ",";
            }
              
            labels1 += "\""+resultSet.getMetaData().getColumnLabel(i)+"\"";
          }
            
          while (resultSet.next()) 
          {
            if(!series.isEmpty())
            {
               series += ", ";
            }
              
            series += "[";
            for(int i=1; i<=nCols; i++) 
            {
              String v = resultSet.getString(i);
              if(i==1)
              {
                if(!labels2.isEmpty())
                {
                   labels2 += ", ";
                }
                  
                labels2 += "\""+v+"\"";
              } 
              else 
              {
                if(i>2)
                {
                   series += ", ";
                }
                  
                if(v.matches("-?\\d+(\\.\\d+)?"))
                {
                   series += v;
                }
                else
                {
                   series += "\""+v+"\"";
                }
              }
            }
            series += "]";
          }
        }
        mysql_access.close();
      }

      if (!labels1.isEmpty()) {
        //this.almMng.updateStatusOnMeasuredValue(sumFixed);
        String json = "{\"firstAxis\": {\"desc\": \"\",\"labels\": ["+labels1+"]},\"secondAxis\": {\"desc\": \""+desc+"\",\"labels\":["+labels2+"],\"series\":["+series+"]}}";
        System.out.println(this.idProc + " " + json);
        DBAccess mysql_access2 = new DBAccess(this.map_dbAcc.get("AlarmEmail"));
        mysql_access2.setConnection(this.map_dbAcc.get("Dashboard"));
        DateFormat df2 = new SimpleDateFormat("yyyy-MM-dd HH:mm:ss");
        Date data_attuale = new Date();
        String data_attuale_fixed = df2.format(data_attuale);
        //System.out.println(data_attuale_fixed);
        String query_insert = "INSERT INTO Dashboard.Data"
                + "(IdMetric_data, computationDate, series) VALUES"
                + "(\"" + this.idProc + "\",\"" + data_attuale_fixed + "\",'" + json.replace("'", "\\'") +"')";

        mysql_access2.writeDataBaseData(query_insert);
        mysql_access2.close();
      }
    } 
    catch (Exception exp) 
    {
      exp.printStackTrace();
      Utility.WriteExcepLog(this.logger, exp);
      String msgBody = Utility.exceptionMessage(exp, this.getClass().getName(), this.idProc+" - "+this.descrip);
      this.notifyEvent("Import data error", msgBody);
    }
  }

  private double executeQueryJVFreePark() 
  {
    double percent = -1;
    int free_total = 0;
    int total_capacity = 0; 
    
    try 
    {
      Repository repo = buildSparqlRepository();
      repo.initialize();
      RepositoryConnection con = repo.getConnection();
      TupleQuery tupleQueryParkFree = con.prepareTupleQuery(QueryLanguage.SPARQL, this.query);
      TupleQueryResult resultEvent = tupleQueryParkFree.evaluate();

      if (resultEvent != null) {
        while (resultEvent.hasNext()) {
          BindingSet bindingSetEvent = resultEvent.next();
          String free = bindingSetEvent.getValue("free").stringValue();
          String capacity = bindingSetEvent.getValue("capacity").stringValue();
          free_total = free_total + Integer.parseInt(free);
          total_capacity = total_capacity + Integer.parseInt(capacity);
        }

        if (total_capacity != 0) {
          percent = (free_total * 100.0) / total_capacity;
          DBAccess mysql_access = new DBAccess(this.map_dbAcc.get("AlarmEmail"));
          mysql_access.setConnection(this.map_dbAcc.get("Dashboard"));
          System.out.println("Posti liberi parcheggi di : " + free_total);
          System.out.println("Capacità parcheggi di : " + total_capacity);
          System.out.println("Precentuale posti liberi parcheggi di : " + percent);
          DateFormat df = new SimpleDateFormat("yyyy-MM-dd HH:mm:ss");
          Date data_attuale = new Date();
          String data_attuale_fixed = df.format(data_attuale);
          String query_insert = "INSERT INTO Dashboard.Data"
                  + "(IdMetric_data, computationDate, value_num, value_perc1, value_perc2, value_perc3, value_text, quant_perc1, quant_perc2, quant_perc3, tot_perc1, tot_perc2, tot_perc3) VALUES"
                  + "(\"" + this.idProc + "\",\"" + data_attuale_fixed + "\",null,\"" + percent + "\" ,null,null, \"\",\"" + free_total + "\",null,null,\"" + total_capacity + "\",null,null)";
          mysql_access.writeDataBaseData(query_insert);
          mysql_access.close();
        } else {
          DBAccess mysql_access2 = new DBAccess(this.map_dbAcc.get("AlarmEmail"));
          mysql_access2.setConnection(this.map_dbAcc.get("Dashboard"));
          String LastCompDateQuery = "SELECT MAX(computationDate) as lastCompDate, value_num, value_perc1, value_perc2, value_perc3, value_text FROM Dashboard.Data WHERE IdMetric_data=\"" + this.idProc + "\" ORDER BY computationDate DESC";
          ResultSet resultSet = mysql_access2.readDataBase(LastCompDateQuery,this);
          Timestamp lastTimestamp = null;
          Double[] lastValues = new Double[(resultSet.getMetaData().getColumnCount()) - 1];
          while (resultSet.next()) {
            lastTimestamp = resultSet.getTimestamp("lastCompDate");
            lastValues[0] = Double.parseDouble((resultSet.getString("value_perc1").trim()));
          }
          mysql_access2.close();
          //this.almMng.updateStatusOnComputingDate(lastTimestamp, lastValues);
        }
      }
    } 
    catch (Exception exp) 
    {
      Utility.WriteExcepLog(this.logger, exp);
      String msgBody = Utility.exceptionMessage(exp, this.getClass().getName(), this.idProc+" - "+this.descrip);      
      this.notifyEvent("Import data error", msgBody);
    }
    
    return percent;
  }

  private void executeQueryJVRidesAtaf() 
  {
    try 
    {
      Repository repo = buildSparqlRepository();
      repo.initialize();
      RepositoryConnection con = repo.getConnection();
      TupleQuery tupleQueryRitAntAvm = con.prepareTupleQuery(QueryLanguage.SPARQL, this.query);
      TupleQueryResult resultEvent = tupleQueryRitAntAvm.evaluate();

      double percent_inOrario = -1;
      double percent_inAnticipo = -1;
      double percent_inRitardo = -1;
      int num_inOrario = -1;
      int num_inAnticipo = -1;
      int num_inRitardo = -1;
      int num_total = 0;

      if (resultEvent != null) {
        while (resultEvent.hasNext()) {
          BindingSet bindingSetRides = resultEvent.next();
          String state = bindingSetRides.getValue("state").stringValue().trim();
          String sum = bindingSetRides.getValue("sum").stringValue();
          if (state.equals("In orario")) {
            num_inOrario = Integer.parseInt(sum);
            num_total = num_total + Integer.parseInt(sum);
          } else if (state.equals("Ritardo")) {
            num_inRitardo = Integer.parseInt(sum);
            num_total = num_total + Integer.parseInt(sum);
          } else if (state.equals("Anticipo")) {
            num_inAnticipo = Integer.parseInt(sum);
            num_total = num_total + Integer.parseInt(sum);
          }
        }
        if (num_total != 0 && num_inOrario != -1 && num_inRitardo != -1 && num_inAnticipo != -1) {
          percent_inOrario = (num_inOrario * 100.0) / num_total;
          percent_inAnticipo = (num_inAnticipo * 100.0) / num_total;
          percent_inRitardo = (num_inRitardo * 100.0) / num_total;
          DBAccess mysql_access = new DBAccess(this.map_dbAcc.get("AlarmEmail"));
          mysql_access.setConnection(this.map_dbAcc.get("Dashboard"));
          System.out.println("Percentuale in orario : " + percent_inOrario);
          System.out.println("Percentuale in anticipo : " + percent_inAnticipo);
          System.out.println("Percentuale in ritardo : " + percent_inRitardo);
          DateFormat df = new SimpleDateFormat("yyyy-MM-dd HH:mm:ss");
          Date data_attuale = new Date();
          String data_attuale_fixed = df.format(data_attuale);
          String query_insert = "INSERT INTO Dashboard.Data"
                  + "(IdMetric_data, computationDate, value_num, value_perc1, value_perc2, value_perc3, value_text, quant_perc1, quant_perc2, quant_perc3, tot_perc1, tot_perc2, tot_perc3) VALUES"
                  + "(\"" + this.idProc + "\",\"" + data_attuale_fixed + "\", null,\"" + percent_inOrario + "\"  ,\"" + percent_inAnticipo + "\",\"" + percent_inRitardo + "\", \"\",\"" + num_inOrario + "\",\"" + num_inAnticipo + "\",\"" + num_inRitardo + "\",\"" + num_total + "\",\"" + num_total + "\",\"" + num_total + "\")";
          mysql_access.writeDataBaseData(query_insert);
          mysql_access.close();
        } else {
          DBAccess mysql_access2 = new DBAccess(this.map_dbAcc.get("AlarmEmail"));
          mysql_access2.setConnection(this.map_dbAcc.get("Dashboard"));
          String LastCompDateQuery = "SELECT MAX(computationDate) as lastCompDate, value_num, value_perc1, value_perc2, value_perc3, value_text FROM Dashboard.Data WHERE IdMetric_data=\"" + this.idProc + "\" ORDER BY computationDate DESC";
          ResultSet resultSet = mysql_access2.readDataBase(LastCompDateQuery,this);
          Timestamp lastTimestamp = null;
          Double[] lastValues = new Double[(resultSet.getMetaData().getColumnCount()) - 1];
          while (resultSet.next()) {
            lastTimestamp = resultSet.getTimestamp("lastCompDate");
            lastValues[0] = Double.parseDouble((resultSet.getString("value_perc1").trim()));
            lastValues[1] = Double.parseDouble((resultSet.getString("value_perc2").trim()));
            lastValues[2] = Double.parseDouble((resultSet.getString("value_perc3").trim()));
          }
          mysql_access2.close();
          //this.almMng.updateStatusOnComputingDate(lastTimestamp, lastValues);
        }
      }
    } 
    catch (Exception exp) 
    {
      String msgBody = Utility.exceptionMessage(exp, this.getClass().getName(), this.idProc+" - "+this.descrip);      
      this.notifyEvent("Import data error", msgBody);
    }
  }

  private void executeQueryJVTwRet() 
  {
    try 
    {
      int num_total = 0;
      int num_twitte = -1;
      int num_retwitte = -1;
      String[] queries = this.query.split("\\|");

      DBAccess mysql_access = new DBAccess(this.map_dbAcc.get("AlarmEmail"));
      mysql_access.setConnection(this.map_dbAcc.get(this.dataSourceId));
      ResultSet resultSet = mysql_access.readDataBase(queries[1].trim(),this);
      while (resultSet.next()) {
        String count = resultSet.getString("count");
        if (count != null) {
          num_total = Integer.parseInt(count);
          System.out.println("Numero totale twitte/retwitte: " + num_total);
        }
      }
      ResultSet resultSet3 = mysql_access.readDataBase(queries[0].trim(),this);
      while (resultSet3.next()) {
        String count = resultSet3.getString("count");
        if (count != null) {
          num_retwitte = Integer.parseInt(count);
          System.out.println("Numero totale retwitte: " + num_retwitte);
        }
      }
      mysql_access.close();

      if (num_total != 0 && num_retwitte != -1) {
        //this.almMng.updateStatusOnMeasuredValue(Utility.round(new Integer(num_total).doubleValue(), 2));

        DBAccess mysql_access2 = new DBAccess(this.map_dbAcc.get("AlarmEmail"));
        mysql_access2.setConnection(this.map_dbAcc.get("Dashboard"));
        num_twitte = num_total - num_retwitte;
        System.out.println("Numero totale twitte: " + num_twitte);

        DateFormat df = new SimpleDateFormat("yyyy-MM-dd HH:mm:ss");
        Date data_attuale = new Date();
        String data_attuale_fixed = df.format(data_attuale);
        String query_insert = "INSERT INTO Dashboard.Data"
                + "(IdMetric_data, computationDate, value_num, value_perc1, value_perc2, value_perc3, value_text, quant_perc1, quant_perc2, quant_perc3, tot_perc1, tot_perc2, tot_perc3) VALUES"
                + "(\"" + idProc + "\",\"" + data_attuale_fixed + "\",\"" + num_twitte + "\",null,null,null, \"\",\"" + num_retwitte + "\",null,null,null,null,null)";
        mysql_access2.writeDataBaseData(query_insert);
        mysql_access2.close();
      }
    } 
    catch (Exception exp) 
    {
      Utility.WriteExcepLog(this.logger, exp);
      String msgBody = Utility.exceptionMessage(exp, this.getClass().getName(), this.idProc+" - "+this.descrip);      
      this.notifyEvent("Import data error", msgBody);
    }
  }

  private double executeQueryJVWifiOp() 
  {
    double percent = -1;
    
    try 
    {
      String[] queries = this.query.split("\\|");
		//query = "SELECT min(latitude) as minlat, min(longitude) as minlng, max(latitude) as maxlat, max(longitude) as maxlng FROM sensors.sensors where network_name like 'FirenzeWiFi' and type='wifi'";
		/*double minlat = -1;
       double minlng = -1;
       double maxlat = -1;
       double maxlng = -1;
       DBAccess mysql_access = new DBAccess(this.map_dbAcc.get("AlarmEmail"));
       mysql_access.setConnectionMySQL(this.map_dbAcc.get(this.dataSourceId));
       ResultSet resultSet = mysql_access.readDataBase(queries[0].trim());
       while (resultSet.next()) {
       minlat = Double.parseDouble(resultSet.getString("minlat"));
       minlng = Double.parseDouble(resultSet.getString("minlng"));
       maxlat = Double.parseDouble(resultSet.getString("maxlat"));
       maxlng = Double.parseDouble(resultSet.getString("maxlng"));
       }*/
      //mysql_access.close();

      HttpParams httpParameters = new BasicHttpParams();
      // Set the timeout in milliseconds until a connection is established.
      // The default value is zero, that means the timeout is not used.
      HttpConnectionParams.setConnectionTimeout(httpParameters, 20000);
      // Set the default socket timeout (SO_TIMEOUT)
      // in milliseconds which is the timeout for waiting for data.
      HttpConnectionParams.setSoTimeout(httpParameters, 20000);
      HttpClient httpClient = new DefaultHttpClient(httpParameters);
      //String apiUrl = "http://servicemap.disit.org/WebAppGrafo/api/?selection=" + minlat + "%3B" + minlng + "%3B" + maxlat + "%3B" + maxlng + "%3B&categories=Wifi&format=json&maxResults=1000";
      //la query che determina l'area dei firenzewifi ritorna una area enorme che non copre la sola firenze
      Configuration conf = Configuration.getInstance();
      String apiUrl = conf.get("servicemapApiBaseUrl", "http://servicemap.disit.org/WebAppGrafo/api/")+"?selection=COMUNE%20di%20FIRENZE&categories=Wifi&format=json&maxResults=1000";
      System.out.println(apiUrl);
      HttpGet httpGet = new HttpGet(apiUrl);
      httpGet.addHeader("Content-Type", "application/json");
      HttpResponse response = httpClient.execute(httpGet);
      String result = null;
      if (response.getStatusLine().getStatusCode() != 200) {
        throw new RuntimeException("Failed : HTTP error code : "
                + response.getStatusLine().getStatusCode());
      }
      BufferedReader br = new BufferedReader(new InputStreamReader((response.getEntity().getContent())));
      StringBuilder sb = new StringBuilder();
      String output;
      while ((output = br.readLine()) != null) {
        sb.append(output + "\n");
      }
      //System.out.println(sb.toString());
      JSONObject job = new JSONObject(sb.toString());
      JSONObject job2 = job.getJSONObject("Servizi");
      JSONArray jsonarray = job2.getJSONArray("features");
      int num_total = 0;
      if(jsonarray.length() > 0) {
        JSONObject elemlast = jsonarray.getJSONObject((jsonarray.length()) - 1);
        num_total = Integer.parseInt(elemlast.get("id").toString());
      }

      //query = "SELECT count(distinct MAC_address) as Sum FROM sensors.sensors where network_name='FirenzeWiFi' and type='wifi'";
      int num_oper = -1;
      //mysql_access.setConnectionMySQL(this.map_dbAcc.get(this.dataSourceId));
      DBAccess mysql_access = new DBAccess(this.map_dbAcc.get("AlarmEmail"));
      mysql_access.setConnection(this.map_dbAcc.get(this.dataSourceId));
      ResultSet resultSet2 = mysql_access.readDataBase(queries[1].trim(),this);
      while (resultSet2.next()) {
        num_oper = Integer.parseInt(resultSet2.getString("sum"));
      }
      mysql_access.close();

      if (num_oper != -1 && num_total != 0) {
        percent = (num_oper * 100.0) / num_total;
        System.out.println("Percentuale Wifi operativi: " + percent);
        //this.almMng.updateStatusOnMeasuredValue(Utility.round(percent, 2));
        DBAccess mysql_access2 = new DBAccess(this.map_dbAcc.get("AlarmEmail"));
        mysql_access2.setConnection(this.map_dbAcc.get("Dashboard"));
        Date data_attuale = new Date();
        DateFormat df = new SimpleDateFormat("yyyy-MM-dd HH:mm:ss");
        String data_attuale_fixed = df.format(data_attuale);
        String query_insert = "INSERT INTO Dashboard.Data"
                + "(IdMetric_data, computationDate, value_num, value_perc1, value_perc2, value_perc3, value_text, quant_perc1, quant_perc2, quant_perc3, tot_perc1, tot_perc2, tot_perc3) VALUES"
                + "(\"" + this.idProc + "\",\"" + data_attuale_fixed + "\", null,\"" + percent + "\",null,null, \"\",\"" + num_oper + "\",null,null,\"" + num_total + "\",null,null)";
        mysql_access2.writeDataBaseData(query_insert);
        mysql_access2.close();
      }
    } 
    catch (Exception exp) 
    {
      Utility.WriteExcepLog(this.logger, exp);
      String msgBody = Utility.exceptionMessage(exp, this.getClass().getName(), this.idProc+" - "+this.descrip);      
      this.notifyEvent("Import data error", msgBody);
    }
    return percent;
  }

  private void executeQueryJVSce() 
  {
    try 
    {
      String[] queries = this.query.split("\\|");
      String id_nodes[] = new String[10];
      for (int i = 0; i < 10; i++) {
        id_nodes[i] = null;
      }
      double value_nodes[] = new double[10];
      for (int i = 0; i < 10; i++) {
        value_nodes[i] = -1;
      }
      int num_nodes = 0;
      double result = -1;
      double cpu_load_total = 0;

      DBAccess mysql_access = new DBAccess(this.map_dbAcc.get("AlarmEmail"));
      mysql_access.setConnection(this.map_dbAcc.get(this.dataSourceId));
      ResultSet resultSet = mysql_access.readDataBase(queries[0].trim(),this);
      int j = 0;
      if (resultSet != null) {
        while (resultSet.next()) {
          id_nodes[j] = resultSet.getString("IdNode").trim();
          j++;
        }

        for (int i = 0; i < id_nodes.length; i++) {
          if (id_nodes[i] != null) {
            String queryNode = (queries[1].trim()).replace("%%nodei%%", id_nodes[i]);
            ResultSet resultSet2 = mysql_access.readDataBase(queryNode,this);
            while (resultSet2.next()) {
              String value = resultSet2.getString("value");
              String ipAddress = resultSet2.getString("value");
              if (value != null) 
              {
                //Reperimento di cpuload(i)
                value_nodes[i] = Double.parseDouble(value);
                System.out.println("Value node: " + value_nodes[i]);
                num_nodes++;
              }
            }
          }
        }

        if (this.idProc.equals("Sce_CPU")) 
        {
          if (num_nodes != 0) 
          {
            for (int i = 0; i < value_nodes.length; i++) 
            {
              if (value_nodes[i] != -1) 
              {
                cpu_load_total = cpu_load_total + value_nodes[i];
              }
            }
            result = cpu_load_total / num_nodes;
            System.out.println("Avg Cpu load: " + result);
          }
        } 
        else if (this.idProc.equals("Sce_Mem")) 
        {
          double mem_total = 0;
          if (num_nodes != 0) 
          {
            for (int i = 0; i < value_nodes.length; i++) 
            {
              if (value_nodes[i] != -1) 
              {
                mem_total = mem_total + value_nodes[i];
              }
            }
            result = mem_total;
            System.out.println("Total memory: " + result);
          }
        }
      }
      mysql_access.close();

      if (result != -1) {

        //this.almMng.updateStatusOnMeasuredValue(Utility.round(result, 2));
        DBAccess mysql_access2 = new DBAccess(this.map_dbAcc.get("AlarmEmail"));
        mysql_access2.setConnection(this.map_dbAcc.get("Dashboard"));
        Date data_attuale = new Date();
        DateFormat df = new SimpleDateFormat("yyyy-MM-dd HH:mm:ss");
        String data_attuale_fixed = df.format(data_attuale);
        String query_insert = null;
        if (this.idProc.equals("Sce_CPU")) {
          query_insert = "INSERT INTO Dashboard.Data"
                  + "(IdMetric_data, computationDate, value_num, value_perc1, value_perc2, value_perc3, value_text, quant_perc1, quant_perc2, quant_perc3, tot_perc1, tot_perc2, tot_perc3) VALUES"
                  + "(\"" + this.idProc + "\",\"" + data_attuale_fixed + "\", null,\"" + result + "\"  ,null,null, \"\",\"" + cpu_load_total + "\",null,null,\"" + num_nodes + "\",null,null)";
        } else if (this.idProc.equals("Sce_Mem")) {
          query_insert = "INSERT INTO Dashboard.Data"
                  + "(IdMetric_data, computationDate, value_num, value_perc1, value_perc2, value_perc3, value_text, quant_perc1, quant_perc2, quant_perc3, tot_perc1, tot_perc2, tot_perc3) VALUES"
                  + "(\"" + this.idProc + "\",\"" + data_attuale_fixed + "\", null,\"" + result + "\"  ,null,null, \"\",null,null,null,null,null,null)";
        }
        mysql_access2.writeDataBaseData(query_insert);
        mysql_access2.close();
      }

    } 
    catch (Exception exp) 
    {
      Utility.WriteExcepLog(this.logger, exp);
      String msgBody = Utility.exceptionMessage(exp, this.getClass().getName(), this.idProc+" - "+this.descrip);      
      this.notifyEvent("Import data error", msgBody);
    }
  }

  private void executeQueryJVSmartDs() 
  {
    try 
    {
      String objective = null;
      double percent_green = -1;
      double percent_white = -1;
      double percent_red = -1;

      DBAccess mysql_access = new DBAccess(this.map_dbAcc.get("AlarmEmail"));
      mysql_access.setConnection(this.map_dbAcc.get(this.dataSourceId));
      ResultSet resultSet = mysql_access.readDataBase(this.query,this);
      while (resultSet.next()) {
        objective = resultSet.getString("objective");
        String green = resultSet.getString("green");
        if (green != null) {
          percent_green = Double.parseDouble(green);
        }
        String white = resultSet.getString("white");
        if (white != null) {
          percent_white = Double.parseDouble(white);
        }
        String red = resultSet.getString("red");
        if (red != null) {
          percent_red = Double.parseDouble(red);
        }
        String end_exec = resultSet.getString("end_exec");
        System.out.println("Processo SmartDS: " + objective);
        System.out.println("Processo SmartDS: " + green);
        System.out.println("Processo SmartDS: " + white);
        System.out.println("Processo SmartDS: " + red);
        System.out.println("Processo SmartDS: " + end_exec);
      }
      mysql_access.close();

      if (percent_green != -1 && percent_white != -1 && percent_red != -1) {
        double percentTot = percent_green + percent_white + percent_red;
        //this.almMng.updateStatusOnMeasuredValue(Utility.round(percentTot, 2));

        DBAccess mysql_access2 = new DBAccess(this.map_dbAcc.get("AlarmEmail"));
        mysql_access2.setConnection(this.map_dbAcc.get("Dashboard"));
        DateFormat df = new SimpleDateFormat("yyyy-MM-dd HH:mm:ss");
        Date data_attuale = new Date();
        String data_attuale_fixed = df.format(data_attuale);
        String query_insert = "INSERT INTO Dashboard.Data"
                + "(IdMetric_data, computationDate, value_num, value_perc1, value_perc2, value_perc3, value_text, quant_perc1, quant_perc2, quant_perc3, tot_perc1, tot_perc2, tot_perc3) VALUES"
                + "(\"" + this.idProc + "\",\"" + data_attuale_fixed + "\", null,\"" + percent_green + "\",\"" + percent_white + "\",\"" + percent_red + "\",\"" + objective + "\", null,null,null,null,null,null)";
        mysql_access2.writeDataBaseData(query_insert);
        mysql_access2.close();
      }
    } 
    catch (Exception exp) 
    {
      Utility.WriteExcepLog(this.logger, exp);
      String msgBody = Utility.exceptionMessage(exp, this.getClass().getName(), this.idProc+" - "+this.descrip);      
      this.notifyEvent("Import data error", msgBody);
    }
  }
  
  private Repository buildSparqlRepository() {
    Repository r;
    String[] ds = this.dataSourceId.split("\\|");
    String[] data = null;

    for(int i=0; i<ds.length; i++) {
      data = this.map_dbAcc.get(ds[i]);
      if(data!=null && data[4].equalsIgnoreCase("RDFstore"))
        break;
    }
    String url = data[0];
    if(url.startsWith("jdbc:virtuoso:")) {
      String user = data[2];
      String passwd = data[3];
      r = new VirtuosoRepository(url, user, passwd);
    }
    else
      r = new SPARQLRepository(url);
    return r;
  }
}
