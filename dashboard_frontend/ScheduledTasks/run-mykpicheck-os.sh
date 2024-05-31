#!/bin/sh
cd /var/www/html/dashboardSmartCity/ScheduledTasks
if [ -f running-mykpicheck-os.txt ]; then
  echo still running
  exit
fi

date > running-mykpicheck-os.txt
php -d memory_limit=2G MyKPICheck-OS.php > mykpicheck-os.log 2>&1
rm running-mykpicheck-os.txt

