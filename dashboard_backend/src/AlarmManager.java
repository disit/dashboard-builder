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

import java.util.logging.*;
import utility.Utility;
import java.util.ArrayList;
import java.util.Date;

public class AlarmManager {

  private String idMetric;
  private String description;
 // private Integer threshold;
  //private String thresholdEval;
  //private Integer thresholdEvalLimit;
  //private Integer thresholdEvalCount;
  //private Integer thresholdTime;
  private String[] paramsEmail;
  private Date startTime;
  private int countResetTimer;
  private final Logger logger = Logger.getLogger(AlarmManager.class.getName());

  public AlarmManager(String id, /*Integer thresholdValue, String thresholdEval, Integer thresholdEvalLim, Integer thresholdTime,*/ String descrip, String[] params) {
    this.idMetric = id;
    this.description = descrip;
    /*this.threshold = thresholdValue;
    this.thresholdEval = thresholdEval;
    this.thresholdEvalLimit = thresholdEvalLim;
    this.thresholdTime = thresholdTime;
    this.thresholdEvalCount = 0;*/
    this.paramsEmail = params;
    this.startTime = null;
    this.countResetTimer = 0;
  }

  /*public void updateStatusOnMeasuredValue(double measuredValue) {

    if ((this.thresholdEval != null) && (this.threshold != null) && (this.thresholdEvalLimit != null)) {
      if (thresholdEvaluation(this.thresholdEval, measuredValue, Utility.round(new Integer(this.threshold).doubleValue(), 2))) {
        this.thresholdEvalCount++;
      } else {
        this.thresholdEvalCount = 0;
      }

      if (this.thresholdEvalCount > this.thresholdEvalLimit) {
        Date actualDate = new Date();
        DateFormat df = new SimpleDateFormat("yyyy-MM-dd HH:mm:ss");
        String actualDateDf = df.format(actualDate);
        ArrayList<Double> listValues = new ArrayList<>();
        listValues.add(measuredValue);
        WriteLog(listValues, null);
        NotifyEmail(listValues, actualDateDf, null);
        this.thresholdEvalCount = 0;
      }
    }
  }*/

  /*public void updateStatusOnComputingDate(Timestamp lastCompTimeStamp, Double[] lastCalcValues) {

    if ((lastCompTimeStamp != null) && (this.thresholdTime != null) && (this.thresholdEval != null)) {
      Date actualDate = new Date();
      long diff = Math.abs(actualDate.getTime() - lastCompTimeStamp.getTime());
      try {
        if (thresholdEvaluation(this.thresholdEval, Utility.round(new Long(diff).doubleValue(), 2), Utility.round(new Integer(this.thresholdTime).doubleValue(), 2))) {
          long startTimeDiff = 0;
          if (this.startTime == null) {
            this.startTime = new Date();
          } else {
            startTimeDiff = actualDate.getTime() - this.startTime.getTime();
          }
          if (((this.countResetTimer == 0) && (startTimeDiff == 0)) || (startTimeDiff > this.thresholdTime)) {
            DateFormat df = new SimpleDateFormat("yyyy-MM-dd HH:mm:ss");
            String actualDateDf = df.format(actualDate);
            String lastCompDateDf = df.format(new Date(lastCompTimeStamp.getTime()));
            ArrayList<Double> listValues = new ArrayList<>();
            for (int i = 0; i < lastCalcValues.length; i++) {
              if (lastCalcValues[i] != null) {
                listValues.add(lastCalcValues[i]);
              }
            }
            WriteLog(listValues, lastCompDateDf);
            NotifyEmail(listValues, actualDateDf, lastCompDateDf);
            if (startTimeDiff != 0) {
              this.startTime = null;
              this.countResetTimer++;
            }
          }
        } else {
          this.startTime = null;
          this.countResetTimer = 0;
        }
      } catch (IllegalArgumentException exp) {
        Utility.WriteExcepLog(logger, exp);
      }
    }
  }*/

  private void WriteLog(ArrayList<Double> listValues, String lastCompDate) 
  {
    if (lastCompDate != null) 
    {
      Utility.WriteInfoLog(this.logger, new String("Metric:" + this.idMetric + " - Last computed values: " + listValues.toString() + " at: " + lastCompDate /*+ " - Alarm threshold: " + this.thresholdTime + " ms"*/));
    } 
    else 
    {
      Utility.WriteInfoLog(this.logger, new String("Metric:" + this.idMetric + " - Last computed values: " + listValues.toString() /*+ " - Alarm threshold: " + this.threshold*/));
    }
  }

  /*private void NotifyEmail(ArrayList<Double> listValues, String date, String lastCompDate) {

    String msgBody = "You're receiving this email from Km4City Dashboard because the Metric \"" + this.idMetric + " - " + this.description + "\" has entered in the ALARM state at " + date;
    msgBody += "\n\nAlarm Details:";
    if (lastCompDate != null) {
      msgBody += "\n\nType: Computed values for the metric are too old";
      msgBody += "\nDescription: The numerical values or percentage of the metric have not been computed for ";
      msgBody += TimeUnit.MINUTES.convert(this.thresholdTime, TimeUnit.MILLISECONDS) + " minutes";
      msgBody += "\nLast computed values: " + listValues.toString() + " at: " + lastCompDate;
      msgBody += "\nAlarm threshold: " + this.thresholdTime + " ms (" + TimeUnit.MINUTES.convert(this.thresholdTime, TimeUnit.MILLISECONDS) + " minutes)";
      msgBody += "\nDate: " + date;
    } else {
      msgBody += "\nType: Computed values for the metrics have exceeded or reached the alarm threshold";
      if (listValues.size() > 1) {
        msgBody += "\nDescription: The numerical values or percentages of the metric are " + this.thresholdEval + " " + this.threshold;
      } else {
        msgBody += "\nDescription: The numerical value or percentage of the metric is " + this.thresholdEval + " " + this.threshold;
      }
      msgBody += "\nLast computed values: " + listValues.toString();
      msgBody += "\nAlarm threshold: " + this.threshold;
      msgBody += "\nDate: " + date;
    }

    msgBody += "\n\nThis message is generated by the Km4City Dashboard. For support: info@disit.org";
    msgBody += "\nPlease do not reply to this message.";
    Utility.SendEmail(paramsEmail, msgBody, "[ALERT] Km4City metric \""+this.idMetric+"\"");
  }*/

  /*private boolean thresholdEvaluation(String type, double value, double thresholdValue) {
    boolean result = false;
    if (type.equals("=")) {
      if (value == thresholdValue) {
        result = true;
      }
    } else if (type.equals("<")) {
      if (value < thresholdValue) {
        result = true;
      }
    } else if (type.equals("<=")) {
      if (value <= thresholdValue) {
        result = true;
      }
    } else if (type.equals(">")) {
      if (value > thresholdValue) {
        result = true;
      }
    } else if (type.equals(">=")) {
      if (value >= thresholdValue) {
        result = true;
      }
    } else if (type.equals("!=")) {
      if (value != thresholdValue) {
        result = true;
      }
    }
    return result;
  }*/

  private int safeLongToInt(long l) {
    if (l < Integer.MIN_VALUE || l > Integer.MAX_VALUE) {
      throw new IllegalArgumentException(l + " cannot be cast to int without changing its value.");
    }
    return (int) l;
  }
}
