#!/bin/sh
cd /var/www/html/dashboardSmartCity/ScheduledTasks
if [ -f running-last-10m-iot.txt ]; then
  echo still running
  exit
fi

date > running-last-10m-iot.txt
php -d memory_limit=3G IOT_Last10min_Feed_DashboardWizard.php > feed-iot-last10m.log 2>&1
rm running-last-10m-iot.txt
