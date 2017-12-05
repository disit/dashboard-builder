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
import java.sql.PreparedStatement;
import java.sql.ResultSet;
import java.sql.Statement;
import java.text.DateFormat;
import java.text.SimpleDateFormat;
import java.util.Calendar;
import java.util.Date;
import java.util.GregorianCalendar;
import java.util.Properties;
import java.util.logging.Level;
import java.util.logging.Logger;
import utility.Utility;

public class DBAccess 
{
  private Connection connect = null;
  private Statement statement = null;
  private PreparedStatement preparedStatement = null;
  private ResultSet resultSet = null;
  private static final Logger logger = Logger.getLogger(DBAccess.class.getName());
  private String[] paramsEmail = null;

  public DBAccess() 
  {

  }

  public DBAccess(String[] params) 
  {
    paramsEmail = params;
  }

  public void setConnection(String[] accessValues) 
  {
    String url = (accessValues[0].trim());
    if(accessValues[1]!=null)
      url += "/" + (accessValues[1].trim());
    String username = accessValues[2]!=null ? accessValues[2].trim() : null;
    String password = accessValues[3]!=null ? accessValues[3].trim() : null;

    try 
    {
      Class.forName("org.apache.phoenix.jdbc.PhoenixDriver");
      Class.forName("com.mysql.jdbc.Driver");
      connect = DriverManager.getConnection(url, username, password);
    } 
    catch (Exception exp) 
    {
      Utility.WriteExcepLog(logger, exp);
      this.notifyEvent("DB connection KO", Utility.exceptionMessage(exp, this.getClass().getName(), "Could not connect to database having the following url: " + url));
      //Utility.SendEmail(paramsEmail, msgBody, "[EXCEPTION] Km4city dashboard");
    }
  }
  
  public ResultSet readDataBase(String query, ManagerQuery mq) 
  {
    if(connect==null)
      return null;
    try 
    {
      statement = connect.createStatement();
      resultSet = statement.executeQuery(query);
      return resultSet;
    } 
    catch (Exception exp) 
    {
      System.out.println("Errore in metodo readDataBase metrica: "+(mq!=null ? mq.idProc : "???"));
      exp.printStackTrace();
      Utility.WriteExcepLog(logger, exp);
      
      this.notifyEvent("DB reading KO", Utility.exceptionMessage(exp, this.getClass().getName(), "Metric "+(mq!=null ? mq.idProc : "???")+" could not execute query: " + query));
      return null;
    }
  }

  public ResultSet readDataBase(String query) 
  {
    return readDataBase(query, null);
  }
  
  public void writeDataBaseData(String query) 
  {
    try 
    {
      statement = connect.createStatement();
      statement.executeUpdate(query);
    } 
    catch (Exception exp) 
    {
      System.out.println("Errore in metodo writeDataBase");
      Utility.WriteExcepLog(logger, exp);
      this.notifyEvent("DB reading KO", Utility.exceptionMessage(exp, this.getClass().getName(), "Could not execute following query: " + query));
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
         return;
      } 
      catch (IOException ex) 
      {
         Logger.getLogger(ManagerQuery.class.getName()).log(Level.SEVERE, null, ex);
         return;
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

  public void close() 
  {
    try 
    {
      if (resultSet != null) 
      {
        resultSet.close();
      }
      if (statement != null) 
      {
        statement.close();
      }
      if (connect != null) 
      {
        connect.close();
      }
    } 
    catch (Exception exp) 
    {
      System.out.println(exp.getMessage());
      Utility.WriteExcepLog(logger, exp);
    }
  }

  public ResultSet getResultSet() 
  {
    return resultSet;
  }
  
  /*public ResultSet readCodCorsa() {
   try {
   statement = connect.createStatement();
   // Result set get the result of the SQL query
   resultSet = statement.executeQuery("SELECT COUNT(DISTINCT cod_corsa) AS Sum FROM SiiMobility.Code_corsa where cod_linea ='6' OR cod_linea ='17' OR cod_linea ='4'");
   //writeResultSetDescriptions(resultSet);
   return resultSet;
   } catch (Exception e) {
   System.out.println(e.getMessage());
   return null;
   }
   }

   public ResultSet readNodesSce() {
   try {
   statement = connect.createStatement();
   // Result set get the result of the SQL query
   resultSet = statement.executeQuery("SELECT a1.ID, a1.DATE, a1.IP_ADDRESS, a1.SCHEDULER_INSTANCE_ID as IdNode "
   + "FROM quartz.QRTZ_NODES a1 INNER JOIN (SELECT max(id) maxId, IP_ADDRESS FROM quartz.QRTZ_NODES GROUP BY IP_ADDRESS) a2 ON a1.IP_ADDRESS = a2.IP_ADDRESS AND a1.id = a2.maxId HAVING date(a1.DATE) >= NOW() - INTERVAL 1 day ORDER BY a1.DATE DESC");
   //writeResultSetDescriptions(resultSet);
   return resultSet;
   } catch (Exception e) {
   System.out.println(e.getMessage());
   return null;
   }
   }*/
}
