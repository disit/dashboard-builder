#!/bin/sh
cd /var/www/html/dashboardSmartCity/ScheduledTasks
if [ -f running2.txt ]; then
  echo still running
  exit
fi

date > running2.txt
php IOT_Sensor_FeedDashboardWizard.php > feed-iot.log 2>&1
php IOT_App_FeedDashboardWizard.php > feed-iot-app.log 2>&1 
php Personal_Data_FeedDashboardWizard.php > feed-personaldata.log 2>&1 
php IOT_Actuator_FeedDashboardWizard.php > feed-iot-act.log 2>&1
php Synoptic_Update_DashboardWizard.php > synoptic-update.log 2>&1
rm running2.txt
