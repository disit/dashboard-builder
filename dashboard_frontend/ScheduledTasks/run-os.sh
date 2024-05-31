#!/bin/sh
cd /var/www/html/dashboardSmartCity/ScheduledTasks
if [ -f running-os.txt ]; then
  echo still running
  exit
fi

date > running-os.txt

#php FeedDashboardWizard_Previ_meteo.php > meteo-os.log 2>&1
php -d memory_limit=2G Heatmap_FeedDashboardWizard-OS.php > heatmap-os.log 2>&1

rm running-os.txt
