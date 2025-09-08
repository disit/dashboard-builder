#!/bin/sh
cd /var/www/html/dashboardSmartCity/ScheduledTasks
if [ -f running-10min.txt ]; then
  echo still running
  exit
fi

date > running-10min.txt
php -d memory_limit=2G HealthinessCheck_CarPark_Predictions.php > health-carpark-pred.log 2>&1
rm running-10min.txt
