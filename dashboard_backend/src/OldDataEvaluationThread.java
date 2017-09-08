
import java.io.DataOutputStream;
import java.io.FileInputStream;
import java.io.FileNotFoundException;
import java.io.IOException;
import java.io.Serializable;
import java.net.HttpURLConnection;
import java.net.MalformedURLException;
import java.net.URL;
import java.net.URLEncoder;
import java.sql.Connection;
import java.sql.DriverManager;
import java.sql.ResultSet;
import java.sql.SQLException;
import java.sql.Statement;
import java.text.DateFormat;
import java.text.ParseException;
import java.text.SimpleDateFormat;
import java.util.Calendar;
import java.util.Date;
import java.util.GregorianCalendar;
import java.util.Properties;
import java.util.logging.Level;
import java.util.logging.Logger;
import org.disit.model.Parameters;
import org.disit.model.Rule;
import org.disit.model.SingleRule;
import utility.Utility;

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

public class OldDataEvaluationThread implements Runnable, Serializable  
{
   private String metricName;
   private long oldDataEvalTime;
   private boolean started;
   private Properties conf;
   private String metricType;
   private String dataSourceId;
   private final Logger logger = Logger.getLogger(OldDataEvaluationThread.class.getName());
   
   public OldDataEvaluationThread() 
   {
      this.conf = new Properties();
      FileInputStream in = null;
      try 
      {
         in = new FileInputStream("./config.properties");
         this.conf.load(in);
         in.close();
      } 
      catch (FileNotFoundException ex) 
      {
         Logger.getLogger(OldDataEvaluationThread.class.getName()).log(Level.SEVERE, null, ex);
      }
      catch (IOException ex) 
      {
         Logger.getLogger(OldDataEvaluationThread.class.getName()).log(Level.SEVERE, null, ex);
      }
   }

   public OldDataEvaluationThread(String metricName, String metricType, String dataSourceId) 
   {
      this.metricName = metricName;
      this.metricType = metricType;
      this.dataSourceId = dataSourceId;
      
      this.conf = new Properties();
      FileInputStream in = null;
      try 
      {
         in = new FileInputStream("./config.properties");
         this.conf.load(in);
         in.close();
      } 
      catch (FileNotFoundException ex) 
      {
         Logger.getLogger(OldDataEvaluationThread.class.getName()).log(Level.SEVERE, null, ex);
      }
      catch (IOException ex) 
      {
         Logger.getLogger(OldDataEvaluationThread.class.getName()).log(Level.SEVERE, null, ex);
      }
   }
   
   public String getDataSourceId() 
   {
      return dataSourceId;
   }

   public void setDataSourceId(String dataSourceId) 
   {
      this.dataSourceId = dataSourceId;
   }
   
   public String getMetricType() 
   {
      return metricType;
   }

   public void setMetricType(String metricType) 
   {
      this.metricType = metricType;
   }
   
   public long getOldDataEvalTime() 
   {
      return oldDataEvalTime;
   }
   
   public boolean isStarted() 
   {
      return started;
   }

   public void setStarted(boolean started) 
   {
      this.started = started;
   }

   public void setOldDataEvalTime(long oldDataEvalTime) 
   {
      this.oldDataEvalTime = oldDataEvalTime;
   }

   public String getMetricName() 
   {
      return metricName;
   }

   public void setMetricName(String metricName) 
   {
      this.metricName = metricName;
   }
   
   public Properties getConf() 
   {
      return this.conf;
   }

   public void setConf(Properties conf) 
   {
      this.conf = conf;
   }
   
   private void notifyEvent(String eventType)
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
      String generatorOriginalName = this.metricName;
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
         
         params = String.format("apiUsr=%s&apiPwd=%s&appName=%s&operation=%s&generatorOriginalName=%s&generatorOriginalType=%s&containerName=%s&eventType=%s&eventTime=%s",
         URLEncoder.encode(apiUsr, charset),
         URLEncoder.encode(apiPwd, charset),
         URLEncoder.encode(appName, charset),
         URLEncoder.encode(operation, charset),
         URLEncoder.encode(generatorOriginalName, charset),
         URLEncoder.encode(generatorOriginalType, charset),
         URLEncoder.encode(containerName, charset),
         URLEncoder.encode(eventType, charset),
         URLEncoder.encode(eventTime, charset));
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
      String generatorOriginalName = "Database access object";
      String generatorOriginalType = "Database";
      String containerName = "Process";
      
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

   @Override
   public void run() 
   {
      this.started = true;
      String query = "";
      System.out.println("Old data evaluation thread for metric " + this.metricName + " has started.");
      
      while(this.started) 
      {
        //Controllo per dati troppo vecchi su questa metrica
         Connection conn;
         try 
         {
            conn = DriverManager.getConnection(this.conf.getProperty("url"), this.conf.getProperty("user"), this.conf.getProperty("psw"));
            Statement stm = conn.createStatement();

            query = "SELECT Data.computationDate, Descriptions.oldDataEvalTime FROM Dashboard.Data LEFT JOIN Dashboard.Descriptions ON Data.IdMetric_data = Descriptions.IdMetric WHERE Data.IdMetric_data = '" + this.metricName + "' ORDER BY Data.computationDate DESC LIMIT 1";
            ResultSet rs = stm.executeQuery(query);

            if(rs != null) 
            {
                try
                {
                  if(rs.next()) 
                  {
                    String lastCompDateString = rs.getString("computationDate");
                    this.oldDataEvalTime = rs.getLong("Descriptions.oldDataEvalTime");

                    Calendar nowCal = new GregorianCalendar();
                    long nowMillis = nowCal.getTimeInMillis();
                    DateFormat df = new SimpleDateFormat("yyyy-MM-dd HH:mm:ss");
                    Date lastCompDate;

                    try 
                    {
                       lastCompDate = df.parse(lastCompDateString);
                       Calendar lastCompDateCal = new GregorianCalendar();
                       lastCompDateCal.setTime(lastCompDate);
                       long lastCompDateMillis = lastCompDateCal.getTimeInMillis();

                       if(Math.abs(nowMillis - lastCompDateMillis) > this.oldDataEvalTime)
                       {
                          System.out.println("DATI TROPPO VECCHI PER METRICA: " + this.metricName);
                          notifyEvent("Data too old");
                       }                     
                    } 
                    catch (ParseException ex) 
                    {
                      Logger.getLogger(OldDataEvaluationThread.class.getName()).log(Level.SEVERE, null, ex);
                    }
                  }
                }
                catch(SQLException ex) 
                {
                   Logger.getLogger(ManagerQuery.class.getName()).log(Level.SEVERE, null, ex);
                }
             }
             conn.close();
         } 
         catch (SQLException exp) 
         {
            Logger.getLogger(ManagerQuery.class.getName()).log(Level.SEVERE, null, exp);
            
            DateFormat df = new SimpleDateFormat("yyyy-MM-dd HH:mm:ss");
            Date data_attuale = new Date();
            String data_fixed = df.format(data_attuale);
            String msgBody = "You're receiving this email from Km4City Dashboard because an error has occurred when the metrics calculation software has tried ";
            msgBody += "to read data from the database. This error is generated by " + exp.getClass().getSimpleName() + " exception thrown at " + data_fixed;
            msgBody += "\n\nError Details:";
            msgBody += "\nDescription: Could not execute following query: " + query;
            msgBody += "\nException: " + exp.getMessage().replace("\n", " ");
            if(exp.getCause()!= null)
            {
               msgBody += "\nCause: " + exp.getCause().getMessage().replace("\n", " ");
            }
            msgBody += "\nError Trace: See LogFile.log for deatils";
            msgBody += "\nJava Class: " + this.getClass().getName();
            msgBody += "\nDate: " + data_fixed; 
            
            this.notifyEvent("DB reading KO", msgBody);
         }

        try 
        {
          System.out.println("Old data evaluation thread for metric " + this.metricName + " will now sleep for " + oldDataEvalTime);
          Thread.sleep(this.oldDataEvalTime);
        }
        catch (InterruptedException exp) 
        {
          Utility.WriteExcepLog(this.logger, exp);
          this.notifyEvent("Thread sleep KO", exp.getMessage().replace("\n", " "));
        }
      }
   }
}
