#!/bin/sh
cd /var/www/html/dashboardSmartCity/ScheduledTasks
if [ -f running-last-10m-iot.txt ]; then
  echo still running
  exit
fi

date > running-last-10m-iot.txt
php IOT_Last10min_Feed_DashboardWizard-OS.php > feed-iot-last10m-os.log 2>&1
rm running-last-10m-iot-os.txt
