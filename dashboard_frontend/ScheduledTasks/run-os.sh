#!/bin/sh
cd /var/www/html/dashboardSmartCity/ScheduledTasks
if [ -f running.txt ]; then
  echo still running
  exit
fi

date > running.txt

#php FeedDashboardWizard_Previ_meteo.php > meteo-os.log 2>&1
php Heatmap_FeedDashboardWizard-OS.php > heatmap-os.log 2>&1

rm running.txt
