#!/bin/sh
cd /var/www/html/dashboardSmartCity/ScheduledTasks
if [ -f running3.txt ]; then
  echo still running
  exit
fi

date > running3.txt
php Personal_Data_FeedDashboardWizard-OS.php > feed-personaldata-os.log 2>&1 
rm running3-os.txt
