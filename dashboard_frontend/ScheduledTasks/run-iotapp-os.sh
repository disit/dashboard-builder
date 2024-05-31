#!/bin/sh
cd /var/www/html/dashboardSmartCity/ScheduledTasks
if [ -f running2-os.txt ]; then
  echo still running
  exit
fi

date > running2-os.txt
#php IOT_Sensor_Feed_DashboardWizard.php > feed-iot-new.log 2>&1
php -d memory_limit=2G IOT_Device_FeedAndUpdate_DashboardWizard-OS.php > feed-iot-os.log 2>&1
#date >> running2.txt
#php IOT_App_FeedDashboardWizard.php > feed-iot-app.log 2>&1 
#date >> running2.txt
#php Personal_Data_FeedDashboardWizard.php > feed-personaldata.log 2>&1 
#php IOT_Actuator_FeedDashboardWizard.php > feed-iot-act.log 2>&1
#date >> running2.txt
#php IOT_Actuator_FeedDashboardWizard205.php > feed-iot-act205.log 2>&1
date >> running2-os.txt
php -d memory_limit=2G Synoptic_Update_DashboardWizard-OS.php > synoptic-update-os.log 2>&1
#date >> running2.txt
#php BIM_FeedDashboardWizard.php > bim.log 2>&1
rm running2-os.txt
