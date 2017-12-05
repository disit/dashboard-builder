# dashboard_backend
This component is a java console application that reads the metrics definitions in the "Descriptions" table, 
performs the query on the data source and it inserts the data in the "Data" table.

## Requirements
- java7 or above, running on linux or windows environments.
## Build
- build the jar file using a java development environment (e.g. NetBeans)
## Import DB tables
- create a schema "Dashboard" on MySQL
- import in the MySQL DB the tables defined in the dashboard.sql file using for example "mysql -u user -p password -D Dashboard < dashboard.sql"
- the dashboard user admin/admin is predefined and it allows to login in the front-end
## Run & Configure
- before starting the process for the first time add at least one datasource and one metric using the dashboard frontend tool.
- to run the process use "java -jar DashboardProcess.jar" in a folder where is present the config.properties file where are present the credentials to access to the DB.
- an example of config.properties file is available in the root folder, it should be modified at least to setup the url to the DB as well as the user and password.

## Notes
- currently the process query for the metric definitions only at start, 
so if a new metric is added using the frontend the process need to be restarted.

