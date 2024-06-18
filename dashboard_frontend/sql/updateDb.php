<?php

include '../config.php';

if ( isset( $_SERVER ) && isset( $_SERVER['REQUEST_METHOD'] ) ) {
    echo 'This script must be run from the command line';
    exit;
} 

require '../sso/autoload.php';
use Jumbojett\OpenIDConnectClient;
error_reporting(E_ERROR);
session_start();

$conn = new mysqli($host, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Read data from JSON files
$tablesCreationMap = json_decode(file_get_contents('tablesCreationUpdate.json'), true);
$tablesUpdateMap = json_decode(file_get_contents('tableColumnsUpdate.json'), true);
$tablesSchemaUpdateMap = json_decode(file_get_contents('tableSchemaUpdate.json'), true);
$tableRecordUpdateMap = json_decode(file_get_contents('tableRecordUpdate.json'), true);

$NL = "\n";
//$NL = "<br>";

foreach ($tablesCreationMap as $tab => $query) {
    echo $NL . "---Checking New Tables..." . $NL;
    $result = $conn->query("SHOW TABLES LIKE '$tab'");
    if ($result->num_rows > 0) {
        echo "Table '$tab' already exists in the Database.".$NL;
        continue;
    }

    if ($conn->query($query) === TRUE) {
        echo "Table '$tab' has been successfully created.".$NL;
    } else {
        echo "Error in creating table '$tab': " . $conn->error . $NL;
    }
}

foreach ($tablesUpdateMap as $table => $columns) {
    echo $NL . "---Adding New Tables Columns..." . $NL;
    $result = $conn->query("SHOW TABLES LIKE '$table'");
    if ($result->num_rows == 0) {
        echo "Error: table '$table' does not exists in the Database.".$NL;
        continue;
    }

    // Check if $columns is an array of objects or a single object
    if (!isset($columns[0])) {
        $columns = [$columns];
    }

    foreach ($columns as $columnDetails) {
        foreach ($columnDetails as $column => $details) {
            if (!isset($details['type'])) {
                echo "Error: no data type has been specified for column '$column' in table '$table'".$NL;
                continue;
            }

            $result = $conn->query("SHOW COLUMNS FROM `$table` LIKE '$column'");

            if ($result->num_rows == 0) {
                $queryCol = "ALTER TABLE `$table` ADD `$column` {$details['type']} {$details['extra']}";
                if ($conn->query($queryCol) === TRUE) {
                    echo "The Column '$column' has been successfully added to Table '$table'".$NL;
                } else {
                    echo "Error in adding column '$column' to table '$table': " . $conn->error . $NL;
                }
            } else {
                echo "Table '$table' already has the column '$column'".$NL;
            }
        }
    }
}

foreach ($tablesSchemaUpdateMap as $table => $columns) {
    echo $NL . "---Checking Updates to Tables Schema..." . $NL;
    $result = $conn->query("SHOW TABLES LIKE '$table'");
    if ($result->num_rows == 0) {
        echo "Error: table '$table' does not exists in the Database.".$NL;
        continue;
    }

    // Check if $columns is an array of objects or a single object
    if (!isset($columns[0])) {
        $columns = [$columns];
    }

    foreach ($columns as $columnDetails) {
        foreach ($columnDetails as $column => $details) {
            if (!isset($details['edit'])) {
                echo "Error: no modifications have been specified for column '$column' in table '$table'".$NL;
                continue;
            }

            $queryCol = "ALTER TABLE `$table` MODIFY `$column` {$details['edit']}";
            if ($conn->query($queryCol) === TRUE) {
                echo "The Column '$column' has been successfully modified in Table '$table'".$NL;
            } else {
                echo "Error in modifying column '$column' in table '$table': " . $conn->error . $NL;
            }
        }
    }
}

foreach ($tableRecordUpdateMap as $table => $queries) {
    echo  $NL . "---Updating/Inserting Records..." . $NL;
    $result = $conn->query("SHOW TABLES LIKE '$table'");
    if ($result->num_rows == 0) {
        echo "Error: table '$table' does not exists in the Database.".$NL;
        continue;
    }

    // Check if $queries is an array of objects or a single object
    if (!isset($queries[0])) {
        $queries = [$queries];
    }

    foreach ($queries as $queryDetails) {
        $column = $queryDetails['column'];
        $value = $queryDetails['value'];
        $updateQuery = $queryDetails['update'];
        $insertQuery = $queryDetails['insert'];

        if ($conn->query($updateQuery) === TRUE) {
            echo "Update query info: " . $conn->info . $NL;  // Debug information
            if (strpos($conn->info, 'Rows matched: 1') !== false) {
                if (strpos($conn->info, 'Changed: 1') !== false) {
                    echo "Record in table '$table' has been successfully updated." . $NL;
                } else if (strpos($conn->info, 'Changed: 0') !== false) {
                    echo "Record in table '$table' was already updated and has not been modified." . $NL;
                }
            } else {
                // Check if the record already exists
                $checkQuery = "SELECT * FROM `$table` WHERE `$column` = '$value'";
                $checkResult = $conn->query($checkQuery);

                if ($checkResult && $checkResult->num_rows == 0) {
                    if ($conn->query($insertQuery) === TRUE) {
                        echo "Record has been successfully inserted into table '$table'.".$NL;
                    } else {
                        echo "Error in inserting record into table '$table': " . $conn->error . $NL;
                    }
                } else {
                    echo "Record already exists in table '$table'.".$NL;
                }
            }
        } else {
            echo "Error in updating record in table '$table': " . $conn->error . $NL;
        }
    }
}

echo $NL . "End of Update Script." . $NL;
$conn->close();
