#!/bin/sh
cd /var/www/html/dashboardSmartCity/ScheduledTasks
if [ -f running-health.txt ]; then
  echo still running
  exit
fi

date > running-health.txt
php HealthinessCheck-OS.php > health-os.log 2>&1
rm running-health-os.txt
