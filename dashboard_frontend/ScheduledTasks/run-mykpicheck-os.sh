#!/bin/sh
cd /var/www/html/dashboardSmartCity/ScheduledTasks
if [ -f running-mykpicheck.txt ]; then
  echo still running
  exit
fi

date > running-mykpicheck.txt
php MyKPICheck-OS.php > mykpicheck-os.log 2>&1
rm running-mykpicheck-os.txt

