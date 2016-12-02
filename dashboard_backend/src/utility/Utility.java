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

package utility;

import java.util.Date;
import java.util.Properties;
import java.util.logging.FileHandler;
import java.util.logging.Level;
import java.util.logging.LogRecord;
import java.util.logging.Logger;
import java.util.logging.SimpleFormatter;

import javax.mail.Message;
import javax.mail.MessagingException;
import javax.mail.Session;
import javax.mail.Transport;
import javax.mail.internet.InternetAddress;
import javax.mail.internet.MimeMessage;

public class Utility {

  private static void addHandlerToLogger(Logger logger) {
    // TODO Auto-generated method stub
    FileHandler fh;
    try {
      fh = new FileHandler("./LogFile.log", true);
      logger.addHandler(fh);
      SimpleFormatter formatter = new SimpleFormatter();
      fh.setFormatter(formatter);
    } catch (Exception exp) {
      System.out.println(exp.getMessage());

    }
  }

  private static void closeHandlersToLogger(Logger logger) {
    // TODO Auto-generated method stub
    for (int i = 0; i < logger.getHandlers().length; i++) {
      logger.getHandlers()[i].close();
    }
  }

  public static void WriteInfoLog(Logger logger, String infoText) {
    addHandlerToLogger(logger);
    logger.info(new String(infoText + "\n"));
    closeHandlersToLogger(logger);
  }

  public static void WriteExcepLog(Logger logger, Exception excep) {
    addHandlerToLogger(logger);
    logger.log(Level.SEVERE, "an exception was thrown:", excep);
    closeHandlersToLogger(logger);
  }

  public static void SendEmail(String[] paramsEmail, String bodyText) {
    SendEmail(paramsEmail, bodyText, null);
  }
  
  public static void SendEmail(String[] paramsEmail, String bodyText, String subject) {
    // TODO Auto-generated method stub
    try {
      Properties props = new Properties();
      props.put("mail.smtp.host", paramsEmail[0].trim());
      props.put("mail.smtp.ssl.enable", paramsEmail[4].trim());
      props.put("mail.smtp.auth", paramsEmail[5].trim());
      if (paramsEmail[3].length() != 0) {
        props.put("mail.smtp.port", paramsEmail[3].trim());
      }
      Session session = Session.getDefaultInstance(props, null);
      Message msg = new MimeMessage(session);
      msg.setHeader("X-Mailer", "msgsend");
      msg.setSentDate(new Date());
      msg.setFrom(new InternetAddress(paramsEmail[6].trim()));
      if (paramsEmail[7].contains(",")) {
        String[] splitArray = paramsEmail[7].split(",");
        for (int i = 0; i < splitArray.length; i++) {
          msg.addRecipient(Message.RecipientType.TO,
                  new InternetAddress(splitArray[i].trim(), false));
        }
      } else {
        msg.addRecipient(Message.RecipientType.TO,
                new InternetAddress(paramsEmail[7].trim(), false));
      }
      msg.setSubject(subject != null ? subject : "[ALERT] Km4City Dashboard notify email");
      msg.setText(bodyText);

      if ((paramsEmail[1].length() != 0) && (paramsEmail[2].length() != 0)) {
        Transport.send(msg, paramsEmail[1].trim(), paramsEmail[2].trim());
      } else {
        Transport.send(msg);
      }
      System.out.println("Mail was sent successfully.");
    } catch (MessagingException exp) {
      //System.out.println(exp.getMessage());
      Utility.WriteInfoLog(Logger.getLogger(Utility.class.getName()), Utility.class.getName() + ".java - Error sending alarm email");
      Utility.WriteExcepLog(Logger.getLogger(Utility.class.getName()), exp);
    }
  }

  public static double round(double value, int places) {
    if (places < 0) {
      throw new IllegalArgumentException();
    }

    long factor = (long) Math.pow(10, places);
    value = value * factor;
    long tmp = Math.round(value);
    return (double) tmp / factor;
  }
}
