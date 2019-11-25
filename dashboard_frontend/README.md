# dashboard_frontend
This component is a PHP web application

## Requirements
- PHP 5.7 (not tested with PHP 7)
- MySQL 5.5 or above
## Install
- copy the front_end folder in a web accessible directory (i.e. /var/www/html) and rename it as you like
## Import DB tables
- see details in the dashboard_backend component
## Configure
- edit the ini files in the conf directory, environment.ini file indicate which environment is running (dev, test, prod) and states which parameter to get from the other ini files. To configure the db see the general.ini and database.ini, details on the configuration is reported in 
- change the permissions to the img folder to allow the webserver to write in this folder. (e.g. "chwon -R www-data.www-data img")
