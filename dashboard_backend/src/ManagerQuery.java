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

import java.text.Normalizer;
import java.util.ArrayList;
import java.util.Arrays;
import java.util.List;
import java.util.Map;
import java.util.logging.Logger;

import org.apache.commons.io.IOUtils;
import org.apache.http.HttpEntity;
import org.apache.http.HttpResponse;
import org.apache.http.client.HttpClient;
import org.apache.http.client.methods.HttpGet;
import org.apache.http.client.methods.HttpPost;
import org.apache.http.impl.client.DefaultHttpClient;
import org.apache.http.params.BasicHttpParams;
import org.apache.http.params.HttpConnectionParams;
import org.apache.http.params.HttpParams;
import org.apache.http.util.EntityUtils;
import org.json.JSONArray;
import org.json.JSONObject;
//import org.json.JSONValue;
//import org.json.simple.parser.JSONParser;
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
import java.io.InputStream;
import java.io.InputStreamReader;
import java.io.Serializable;
import java.sql.ResultSet;
import java.sql.Timestamp;
import java.text.DateFormat;
import java.text.SimpleDateFormat;
import java.util.Calendar;
import java.util.logging.Level;

public class ManagerQuery implements Runnable, Serializable {

  private String idProc = null;
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
  private AlarmManager almMng;

  private final Logger logger = Logger.getLogger(ManagerQuery.class.getName());

  public ManagerQuery(String id, String desc, String query, String queryType, String metricType, String processType, Long freq, String dataSourceId, Map<String, String[]> map_dbAccess, AlarmManager alarmMng) {
    // TODO Auto-generated constructor stub
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
    this.almMng = alarmMng;
  }

  public void run() {
    this.started = true;
    try {
      Thread.sleep((int) (Math.random() * 10000)); //aspetta tempo random 0-10 secondi
    } catch (InterruptedException ex) {
    }
    while (this.started) {
      switch (this.processType) {
        case "JVNum1":
          executeQueryJVNum1();
          break;
        case "JVPerc":
          executeQueryJVPerc();
          break;
        case "JVRidesAtaf":
          executeQueryJVRidesAtaf();
          break;
        case "JVSceOnNodes":
          executeQueryJVSce();
          break;
        case "jVPark":
          executeQueryJVFreePark();
          break;
        case "JVWifiOp":
          executeQueryJVWifiOp();
          break;
        case "JVSmartDs":
          executeQueryJVSmartDs();
          break;
        case "JVTwRet":
          executeQueryJVTwRet();
          break;
        default:
            break;
      }
      try {
        System.out.println(this.idProc+": Sleep " + sleepTime);
        Thread.sleep(this.sleepTime);
      } catch (InterruptedException exp) {
        //System.out.println(exp.getMessage());
        Utility.WriteExcepLog(this.logger, exp);
      }
    }
  }

  private void executeQueryJVNum1() {
    try {
      int sumFixed = -1;

      if (this.queryType.equals("SPARQL")) {
        Repository repo = buildSparqlRepository();
        repo.initialize();
        RepositoryConnection con = repo.getConnection();

        TupleQuery tupleQueryNum = con.prepareTupleQuery(QueryLanguage.SPARQL, this.query);
        TupleQueryResult resultNum = tupleQueryNum.evaluate();

        if (this.idProc.equals("Events_Day")) {
          DateFormat df = new SimpleDateFormat("yyyy-MM-dd");
          Date data_inizio = new Date();
          String data_inizio_fixed = df.format(data_inizio);
          if (resultNum != null) {
            int count = 0;
            while (resultNum.hasNext()) {
              BindingSet bindingSetEvent = resultNum.next();
              String valueOfEDate = "";
              String valueOfSDate = "";
              if (bindingSetEvent.getValue("endDate") != null) {
                valueOfEDate = bindingSetEvent.getValue("endDate").stringValue();
              }
              if (bindingSetEvent.getValue("startDate") != null) {
                valueOfSDate = bindingSetEvent.getValue("startDate").stringValue();
              }
              if ((!valueOfEDate.equals("")) || (valueOfEDate.equals("") && (valueOfSDate.equals(data_inizio_fixed)))) {
                count++;
              }
            }
            sumFixed = count;
          }
        } else {
          if (resultNum != null) {
            while (resultNum.hasNext()) {
              BindingSet bindingSetEvent = resultNum.next();

              String sum = "0";
              if (bindingSetEvent.getValue("sum") != null) {
                sum = bindingSetEvent.getValue("sum").stringValue();
              }
              sumFixed = Integer.parseInt(sum);
            }
          }
        }
      } else if (this.queryType.equals("SQL")) {

        DBAccess mysql_access = new DBAccess(this.map_dbAcc.get("AlarmEmail"));
        mysql_access.setConnectionMySQL(this.map_dbAcc.get(this.dataSourceId));
        ResultSet resultSet = mysql_access.readDataBase(this.query);
        if (resultSet != null) {
          while (resultSet.next()) {
            String sum = resultSet.getString(1);
            if (sum != null) {
              sumFixed = Integer.parseInt(sum);
            }
          }
        }
        mysql_access.close();
      }

      if (sumFixed != -1) {
        //this.almMng.updateStatusOnMeasuredValue(sumFixed);
        this.almMng.updateStatusOnMeasuredValue(Utility.round(new Integer(sumFixed).doubleValue(), 2));
        System.out.println(this.idProc + " " + sumFixed);
        DBAccess mysql_access2 = new DBAccess(this.map_dbAcc.get("AlarmEmail"));
        mysql_access2.setConnectionMySQL(this.map_dbAcc.get("Dashboard"));
        DateFormat df2 = new SimpleDateFormat("yyyy-MM-dd HH:mm:ss");
        Date data_attuale = new Date();
        String data_attuale_fixed = df2.format(data_attuale);
        //System.out.println(data_attuale_fixed);
        String query_insert = "INSERT INTO Dashboard.Data"
                + "(IdMetric_data, computationDate, value_num, value_perc1, value_perc2, value_perc3, value_text, quant_perc1, quant_perc2, quant_perc3, tot_perc1, tot_perc2, tot_perc3) VALUES"
                + "(\"" + this.idProc + "\",\"" + data_attuale_fixed + "\",\"" + sumFixed + "\",null ,null,null, \"\",null,null,null,null,null,null)";

        mysql_access2.writeDataBaseData(query_insert);
        mysql_access2.close();
      }
    } catch (Exception exp) {
      exp.printStackTrace();
      ErrorEmailSetUp(this.idProc, this.descrip, exp);
    }
  }

  private void executeQueryJVPerc() {
    // TODO Auto-generated method stub

    try {
      double percent = -1;
      int sumFixed = 0;
      int tot = 0;

      if (this.queryType.equals("SPARQL")) {
        Repository repo = buildSparqlRepository();
        repo.initialize();
        RepositoryConnection con = repo.getConnection();

        if (this.idProc.equals("Meteo_Rt") || this.idProc.equals("Park_Rt") || this.idProc.equals("Sensors_Rt")) {
          TupleQuery tupleQueryPerc = con.prepareTupleQuery(QueryLanguage.SPARQL, this.query);
          TupleQueryResult resultPerc = tupleQueryPerc.evaluate();
          if (resultPerc != null) {
            while (resultPerc.hasNext()) {
              BindingSet bindingSetPerc = resultPerc.next();
              String sum = bindingSetPerc.getValue("sum").stringValue();
              sumFixed = Integer.parseInt(sum);
            }
          }

          String[] parts_metricType = this.metricType.split("\\/");
          tot = Integer.parseInt(parts_metricType[1]);

        } else if (this.idProc.equals("Ataf_Rt")) {
          String[] queries = this.query.split("\\|");

          TupleQuery tupleQueryPerc2 = con.prepareTupleQuery(QueryLanguage.SPARQL, queries[1].trim());
          TupleQueryResult resultPerc2 = tupleQueryPerc2.evaluate();
          if (resultPerc2 != null) {
            while (resultPerc2.hasNext()) {
              BindingSet bindingSetEvent2 = resultPerc2.next();
              String sum = bindingSetEvent2.getValue("sum").stringValue();
              sumFixed = Integer.parseInt(sum);
            }
          }

          String[] dataSources = this.dataSourceId.split("\\|");
          DBAccess mysql_access = new DBAccess();
          mysql_access.setConnectionMySQL(this.map_dbAcc.get(dataSources[0].trim()));
          ResultSet resultSet = mysql_access.readDataBase(queries[0].trim());
          while (resultSet.next()) {
            tot = Integer.parseInt(resultSet.getString("Sum"));
          }
          mysql_access.close();

        } else if (this.idProc.equals("Services_Duplicate")) {

          TupleQuery tupleQueryPerc3 = con.prepareTupleQuery(QueryLanguage.SPARQL, this.query);
          TupleQueryResult resultPerc3 = tupleQueryPerc3.evaluate();
          if (resultPerc3 != null) {
            while (resultPerc3.hasNext()) {
              BindingSet bindingSetEvent = resultPerc3.next();
              String totServ = bindingSetEvent.getValue("totServ").stringValue();
              String perc = bindingSetEvent.getValue("result").stringValue();
              percent = Double.parseDouble(perc);
              tot = Integer.parseInt(totServ);
            }
          }
        }
      } else if (this.queryType.equals("SQL")) {
        String[] queries2 = this.query.split("\\|");
        DBAccess mysql_access = new DBAccess(this.map_dbAcc.get("AlarmEmail"));
        mysql_access.setConnectionMySQL(this.map_dbAcc.get(this.dataSourceId));
        ResultSet resultSet = mysql_access.readDataBase(queries2[1].trim());
        while (resultSet.next()) {
          String tot_extract = resultSet.getString(1);
          if (tot_extract != null) {
            tot = Integer.parseInt(tot_extract);
          }
        }

        //mysql_access.close();
        //mysql_access.setConnectionMySQL(this.db,"Km4CityApp");
        resultSet = mysql_access.readDataBase(queries2[0].trim());
        while (resultSet.next()) {
          String sum = resultSet.getString(1);
          if (sum != null) {
            sumFixed = Integer.parseInt(sum);
          }
        }
        mysql_access.close();
      }

      if (tot != 0) {
        if (!(this.idProc.equals("Services_Duplicate"))) {
          percent = (sumFixed * 100.0) / tot;
        }

        this.almMng.updateStatusOnMeasuredValue(Utility.round(percent, 2));
        System.out.println(this.idProc + " " + percent);

        DBAccess mysql_access2 = new DBAccess(this.map_dbAcc.get("AlarmEmail"));
        mysql_access2.setConnectionMySQL(this.map_dbAcc.get("Dashboard"));
        DateFormat df = new SimpleDateFormat("yyyy-MM-dd HH:mm:ss");
        Date data_attuale = new Date();
        String data_attuale_fixed = df.format(data_attuale);
        String query_insert = "INSERT INTO Dashboard.Data"
                + "(IdMetric_data, computationDate, value_num, value_perc1, value_perc2, value_perc3, value_text, quant_perc1, quant_perc2, quant_perc3, tot_perc1, tot_perc2, tot_perc3) VALUES"
                + "(\"" + this.idProc + "\",\"" + data_attuale_fixed + "\",null,\"" + percent + "\" ,null,null, \"\",\"" + sumFixed + "\",null,null,\"" + tot + "\",null,null)";
        mysql_access2.writeDataBaseData(query_insert);
        mysql_access2.close();
      }

    } catch (Exception exp) {
      ErrorEmailSetUp(this.idProc, this.descrip, exp);
    }
  }

  private void executeQueryJVFreePark() {
    try {
      Repository repo = buildSparqlRepository();
      repo.initialize();
      RepositoryConnection con = repo.getConnection();
      TupleQuery tupleQueryParkFree = con.prepareTupleQuery(QueryLanguage.SPARQL, this.query);
      TupleQueryResult resultEvent = tupleQueryParkFree.evaluate();

      double percent = -1;
      int free_total = 0;
      int total_capacity = 0;

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
          mysql_access.setConnectionMySQL(this.map_dbAcc.get("Dashboard"));
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
          mysql_access2.setConnectionMySQL(this.map_dbAcc.get("Dashboard"));
          String LastCompDateQuery = "SELECT MAX(computationDate) as lastCompDate, value_num, value_perc1, value_perc2, value_perc3, value_text FROM Dashboard.Data WHERE IdMetric_data=\"" + this.idProc + "\" ORDER BY computationDate DESC";
          ResultSet resultSet = mysql_access2.readDataBase(LastCompDateQuery);
          Timestamp lastTimestamp = null;
          Double[] lastValues = new Double[(resultSet.getMetaData().getColumnCount()) - 1];
          while (resultSet.next()) {
            lastTimestamp = resultSet.getTimestamp("lastCompDate");
            lastValues[0] = Double.parseDouble((resultSet.getString("value_perc1").trim()));
          }
          mysql_access2.close();
          this.almMng.updateStatusOnComputingDate(lastTimestamp, lastValues);
        }
      }
    } catch (Exception exp) {
      ErrorEmailSetUp(this.idProc, this.descrip, exp);
    }
  }

  private void executeQueryJVRidesAtaf() {
    // TODO Auto-generated method stub
    try {
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
          mysql_access.setConnectionMySQL(this.map_dbAcc.get("Dashboard"));
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
          mysql_access2.setConnectionMySQL(this.map_dbAcc.get("Dashboard"));
          String LastCompDateQuery = "SELECT MAX(computationDate) as lastCompDate, value_num, value_perc1, value_perc2, value_perc3, value_text FROM Dashboard.Data WHERE IdMetric_data=\"" + this.idProc + "\" ORDER BY computationDate DESC";
          ResultSet resultSet = mysql_access2.readDataBase(LastCompDateQuery);
          Timestamp lastTimestamp = null;
          Double[] lastValues = new Double[(resultSet.getMetaData().getColumnCount()) - 1];
          while (resultSet.next()) {
            lastTimestamp = resultSet.getTimestamp("lastCompDate");
            lastValues[0] = Double.parseDouble((resultSet.getString("value_perc1").trim()));
            lastValues[1] = Double.parseDouble((resultSet.getString("value_perc2").trim()));
            lastValues[2] = Double.parseDouble((resultSet.getString("value_perc3").trim()));
          }
          mysql_access2.close();
          this.almMng.updateStatusOnComputingDate(lastTimestamp, lastValues);
        }
      }
    } catch (Exception exp) {
      ErrorEmailSetUp(this.idProc, this.descrip, exp);
    }
  }

  private void executeQueryJVTwRet() {
    // TODO Auto-generated method stub
    try {

      int num_total = 0;
      int num_twitte = -1;
      int num_retwitte = -1;
      String[] queries = this.query.split("\\|");

      DBAccess mysql_access = new DBAccess(this.map_dbAcc.get("AlarmEmail"));
      mysql_access.setConnectionMySQL(this.map_dbAcc.get(this.dataSourceId));
      ResultSet resultSet = mysql_access.readDataBase(queries[1].trim());
      while (resultSet.next()) {
        String count = resultSet.getString("count");
        if (count != null) {
          num_total = Integer.parseInt(count);
          System.out.println("Numero totale twitte/retwitte: " + num_total);
        }
      }
      ResultSet resultSet3 = mysql_access.readDataBase(queries[0].trim());
      while (resultSet3.next()) {
        String count = resultSet3.getString("count");
        if (count != null) {
          num_retwitte = Integer.parseInt(count);
          System.out.println("Numero totale retwitte: " + num_retwitte);
        }
      }
      mysql_access.close();

      if (num_total != 0 && num_retwitte != -1) {
        this.almMng.updateStatusOnMeasuredValue(Utility.round(new Integer(num_total).doubleValue(), 2));

        DBAccess mysql_access2 = new DBAccess(this.map_dbAcc.get("AlarmEmail"));
        mysql_access2.setConnectionMySQL(this.map_dbAcc.get("Dashboard"));
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
    } catch (Exception exp) {
      ErrorEmailSetUp(this.idProc, this.descrip, exp);
    }
  }

  private void executeQueryJVWifiOp() {
    // TODO Auto-generated method stub
    try {
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
      String apiUrl = "http://servicemap.disit.org/WebAppGrafo/api/?selection=COMUNE%20di%20FIRENZE&categories=Wifi&format=json&maxResults=1000";
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
      mysql_access.setConnectionMySQL(this.map_dbAcc.get(this.dataSourceId));
      ResultSet resultSet2 = mysql_access.readDataBase(queries[1].trim());
      while (resultSet2.next()) {
        num_oper = Integer.parseInt(resultSet2.getString("sum"));
      }
      mysql_access.close();

      if (num_oper != -1 && num_total != 0) {
        double percent = (num_oper * 100.0) / num_total;
        System.out.println("Percentuale Wifi operativi: " + percent);
        this.almMng.updateStatusOnMeasuredValue(Utility.round(percent, 2));

        DBAccess mysql_access2 = new DBAccess(this.map_dbAcc.get("AlarmEmail"));
        mysql_access2.setConnectionMySQL(this.map_dbAcc.get("Dashboard"));
        Date data_attuale = new Date();
        DateFormat df = new SimpleDateFormat("yyyy-MM-dd HH:mm:ss");
        String data_attuale_fixed = df.format(data_attuale);
        String query_insert = "INSERT INTO Dashboard.Data"
                + "(IdMetric_data, computationDate, value_num, value_perc1, value_perc2, value_perc3, value_text, quant_perc1, quant_perc2, quant_perc3, tot_perc1, tot_perc2, tot_perc3) VALUES"
                + "(\"" + this.idProc + "\",\"" + data_attuale_fixed + "\", null,\"" + percent + "\",null,null, \"\",\"" + num_oper + "\",null,null,\"" + num_total + "\",null,null)";
        mysql_access2.writeDataBaseData(query_insert);
        mysql_access2.close();
      }
    } catch (Exception exp) {
      ErrorEmailSetUp(this.idProc, this.descrip, exp);
    }
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
      mysql_access.setConnectionMySQL(this.map_dbAcc.get(this.dataSourceId));
      ResultSet resultSet = mysql_access.readDataBase(queries[0].trim());
      int j = 0;
      if (resultSet != null) {
        while (resultSet.next()) {
          id_nodes[j] = resultSet.getString("IdNode").trim();
          j++;
        }

        for (int i = 0; i < id_nodes.length; i++) {
          if (id_nodes[i] != null) {
            String queryNode = (queries[1].trim()).replace("%%nodei%%", id_nodes[i]);
            ResultSet resultSet2 = mysql_access.readDataBase(queryNode);
            while (resultSet2.next()) {
              String value = resultSet2.getString("value");
              String ipAddress = resultSet2.getString("value");
              if (value != null) 
              {
                //Reperimento di cpuload(i)
                value_nodes[i] = Double.parseDouble(value);
                System.out.println("Value node: " + value_nodes[i]);
                
                String url = "http://" + queryNode + "/sce/cpuinfo.php";
                System.out.println("URL chiamato: " + url);

		/*URL obj = new URL(url);
		HttpURLConnection con = (HttpURLConnection) obj.openConnection();

		
		con.setRequestMethod("GET");

		
		con.setRequestProperty("User-Agent", USER_AGENT);

		int responseCode = con.getResponseCode();
		System.out.println("\nSending 'GET' request to URL : " + url);
		System.out.println("Response Code : " + responseCode);

		BufferedReader in = new BufferedReader(
		        new InputStreamReader(con.getInputStream()));
		String inputLine;
		StringBuffer response = new StringBuffer();

		while ((inputLine = in.readLine()) != null) {
			response.append(inputLine);
		}
		in.close();

		
		System.out.println(response.toString());*/
                
                
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

        this.almMng.updateStatusOnMeasuredValue(Utility.round(result, 2));
        DBAccess mysql_access2 = new DBAccess(this.map_dbAcc.get("AlarmEmail"));
        mysql_access2.setConnectionMySQL(this.map_dbAcc.get("Dashboard"));
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

    } catch (Exception exp) {
      ErrorEmailSetUp(this.idProc, this.descrip, exp);
    }
  }

  private void executeQueryJVSmartDs() {
    // TODO Auto-generated method stub
    try {
      String objective = null;
      double percent_green = -1;
      double percent_white = -1;
      double percent_red = -1;

      DBAccess mysql_access = new DBAccess(this.map_dbAcc.get("AlarmEmail"));
      mysql_access.setConnectionMySQL(this.map_dbAcc.get(this.dataSourceId));
      ResultSet resultSet = mysql_access.readDataBase(this.query);
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
        this.almMng.updateStatusOnMeasuredValue(Utility.round(percentTot, 2));

        DBAccess mysql_access2 = new DBAccess(this.map_dbAcc.get("AlarmEmail"));
        mysql_access2.setConnectionMySQL(this.map_dbAcc.get("Dashboard"));
        DateFormat df = new SimpleDateFormat("yyyy-MM-dd HH:mm:ss");
        Date data_attuale = new Date();
        String data_attuale_fixed = df.format(data_attuale);
        String query_insert = "INSERT INTO Dashboard.Data"
                + "(IdMetric_data, computationDate, value_num, value_perc1, value_perc2, value_perc3, value_text, quant_perc1, quant_perc2, quant_perc3, tot_perc1, tot_perc2, tot_perc3) VALUES"
                + "(\"" + this.idProc + "\",\"" + data_attuale_fixed + "\", null,\"" + percent_green + "\",\"" + percent_white + "\",\"" + percent_red + "\",\"" + objective + "\", null,null,null,null,null,null)";
        mysql_access2.writeDataBaseData(query_insert);
        mysql_access2.close();
      }
    } catch (Exception exp) {
      ErrorEmailSetUp(this.idProc, this.descrip, exp);
    }
  }

  private void ErrorEmailSetUp(String id, String description, Exception exception) {
    DateFormat df = new SimpleDateFormat("yyyy-MM-dd HH:mm:ss");
    Date actualDate = new Date();
    String actualDate_fixed = df.format(actualDate);
    String msgBody = "You're receiving this email from Km4City Dashboard because an error has occurred when the metrics calculation software has tried ";
    msgBody += "to compute the Metric \"" + id + " - " + description + "\". This error is generated by " + exception.getClass().getSimpleName() + " exception thrown at " + actualDate_fixed;
    msgBody += "\n\nError Details:";
    msgBody += "\nDescription: Could not calculate the numeric values or percentages of the Metric \"" + id + " - " + description + "\"";
    msgBody += "\nException: " + exception.getMessage();//.replace("\n", " ");
    //msgBody += "\nCause: "+exception.getCause().getMessage();//.replace("\n", " ");
    msgBody += "\nError Trace: See LogFile.log for deatils";
    msgBody += "\nJava Class: " + this.getClass().getName();
    msgBody += "\nDate: " + actualDate_fixed;
    msgBody += "\n\nThis message is generated by the Km4City Dashboard. For support: info@disit.org";
    msgBody += "\nPlease do not reply to this message.";
    Utility.WriteExcepLog(this.logger, exception);
    Utility.SendEmail(this.map_dbAcc.get("AlarmEmail"), msgBody, "[EXCEPTION] Km4City Dashboard");
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
