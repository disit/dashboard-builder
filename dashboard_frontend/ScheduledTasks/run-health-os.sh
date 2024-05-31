#!/bin/sh
cd /var/www/html/dashboardSmartCity/ScheduledTasks
if [ -f running-health-os.txt ]; then
  echo still running
  exit
fi

date > running-health-os.txt
php -d memory_limit=2G HealthinessCheck-OS.php > health-os.log 2>&1
rm running-health-os.txt
