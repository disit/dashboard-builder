# Crete New Columns
ALTER TABLE `Dashboard`.`DashboardWizard` 
ADD COLUMN `value_name` VARCHAR(256) NULL DEFAULT NULL AFTER `oldEntry`,
ADD COLUMN `value_type` VARCHAR(256) NULL DEFAULT NULL AFTER `value_name`,
ADD COLUMN `device_model_name` VARCHAR(256) NULL DEFAULT NULL AFTER `value_type`,
ADD COLUMN `broker_name` VARCHAR(128) NULL DEFAULT NULL AFTER `device_model_name`;

# Update for HLT
# Complex Event
UPDATE Dashboard.DashboardWizard SET value_name = unique_name_id WHERE high_level_type = 'Complex Event';
# External Service
UPDATE Dashboard.DashboardWizard SET value_name = sub_nature, value_type = 'ExternalContent' WHERE high_level_type = 'External Service';
# KPI
UPDATE Dashboard.DashboardWizard SET value_type = low_level_type, value_name = unique_name_id WHERE high_level_type = 'KPI';
#MicroApplication
UPDATE Dashboard.DashboardWizard SET value_name = sub_nature, value_type = 'ExternalContent' WHERE high_level_type = 'MicroApplication';
# Special Widget
UPDATE Dashboard.DashboardWizard SET value_name = unique_name_id WHERE high_level_type = 'Special Widget';
# Synoptic
UPDATE Dashboard.DashboardWizard SET value_name = unique_name_id, device_model_name = low_level_type WHERE high_level_type = 'Synoptic';
# wfs
UPDATE Dashboard.DashboardWizard SET value_name = unique_name_id WHERE high_level_type = 'wfs';

# Update Dashboard Template with New HLT
UPDATE `Dashboard`.`DashboardTemplates` SET `highLevelTypeSelection`='POI|Sensor|Heatmap|MyPOI|Traffic Flow|Data Table Device|IoT Device|Sensor Device|Mobile Device|Data Table Variable|IoT Device Variable|Mobile Device Variable|Data Table Model|IoT Device Model|Mobile Device Model' WHERE `id`='1';
UPDATE `Dashboard`.`DashboardTemplates` SET `highLevelTypeSelection`='POI|Sensor|Heatmap|MyPOI|Traffic Flow|Data Table Device|IoT Device|Sensor Device|Mobile Device|Data Table Variable|IoT Device Variable|Mobile Device Variable|Data Table Model|IoT Device Model|Mobile Device Model' WHERE `id`='2';
UPDATE `Dashboard`.`DashboardTemplates` SET `highLevelTypeSelection`='Sensor|Sensor-Actuator|Data Table Variable|IoT Device Variable|Mobile Device Variable' WHERE `id`='8';
