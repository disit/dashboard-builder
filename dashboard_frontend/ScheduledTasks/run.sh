#!/bin/sh
cd /var/www/html/dashboardSmartCity/ScheduledTasks
if [ -f running.txt ]; then
  echo still running
  exit
fi

date > running.txt
php FeedDasbhoardWizard.php > feed.log 2>&1
php FeedDashboardWizard205.php > feed205.log 2>&1 
#php IOT_Sensor_FeedDashboardWizard.php > feed-iot.log 2>&1 
#php FeedTwitterExternalContent.php > twitter.log 2>&1
php FeedDashboardWizard_Previ_meteo.php > meteo.log 2>&1
php Heatmap_FeedDashboardWizard.php > heatmap.log 2>&1
#php HealthinessCheck.php > health.log 2>&1
rm running.txt
