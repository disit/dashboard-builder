<?php

include '../config.php';

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

foreach ($tablesCreationMap as $tab => $query) {
    echo "<br>" . "---Checking New Tables..." . "<br>";
    $result = $conn->query("SHOW TABLES LIKE '$tab'");
    if ($result->num_rows > 0) {
        echo "Table '$tab' already exists in the Database."."<br>";
        continue;
    }

    if ($conn->query($query) === TRUE) {
        echo "Table '$tab' has been successfully created."."<br>";
    } else {
        echo "Error in creating table '$tab': " . $conn->error . "<br>";
    }
}

foreach ($tablesUpdateMap as $table => $columns) {
    echo "<br>" . "---Adding New Tables Columns..." . "<br>";
    $result = $conn->query("SHOW TABLES LIKE '$table'");
    if ($result->num_rows == 0) {
        echo "Error: table '$table' does not exists in the Database."."<br>";
        continue;
    }

    foreach ($columns as $column => $details) {
        if (!isset($details['type'])) {
            echo "Error: no data type has been specified for column '$column' in table '$table'"."<br>";
            continue;
        }

        $result = $conn->query("SHOW COLUMNS FROM `$table` LIKE '$column'");

        if ($result->num_rows == 0) {
            $queryCol = "ALTER TABLE `$table` ADD `$column` {$details['type']} {$details['extra']}";
            if ($conn->query($queryCol) === TRUE) {
                echo "The Column '$column' has been successfully added to Table '$table'"."<br>";
            } else {
                echo "Error in adding column '$column' to table '$table': " . $conn->error . "<br>";
            }
        } else {
            echo "Table '$table' already has the column '$column'"."<br>";
        }
    }
}

foreach ($tablesSchemaUpdateMap as $table => $columns) {
    echo "<br>" . "---Checking Updates to Tables Schema..." . "<br>";
    $result = $conn->query("SHOW TABLES LIKE '$table'");
    if ($result->num_rows == 0) {
        echo "Error: table '$table' does not exists in the Database."."<br>";
        continue;
    }

    foreach ($columns as $column => $details) {
        if (!isset($details['edit'])) {
            echo "Error: no modifications have been specified for column '$column' in table '$table'"."<br>";
            continue;
        }

        $queryCol = "ALTER TABLE `$table` MODIFY `$column` {$details['edit']}";
        if ($conn->query($queryCol) === TRUE) {
            echo "The Column '$column' has been successfully modified in Table '$table'"."<br>";
        } else {
            echo "Error in modifying column '$column' in table '$table': " . $conn->error . "<br>";
        }
    }
}

foreach ($tableRecordUpdateMap as $table => $queries) {
    echo  "<br>" . "---Updating/Inserting Records..." . "<br>";
    $result = $conn->query("SHOW TABLES LIKE '$table'");
    if ($result->num_rows == 0) {
        echo "Error: table '$table' does not exists in the Database."."<br>";
        continue;
    }
    $column = $queries['column'];
    $value = $queries['value'];
    $updateQuery = $queries['update'];
    $insertQuery = $queries['insert'];

    if ($conn->query($updateQuery) === TRUE) {
        echo "Update query info: " . $conn->info . "<br>";  // Debug information
        if (strpos($conn->info, 'Rows matched: 1') !== false) {
            if (strpos($conn->info, 'Changed: 1') !== false) {
                echo "Record in table '$table' has been successfully updated." . "<br>";
            } else if (strpos($conn->info, 'Changed: 0') !== false) {
                echo "Record in table '$table' was already updated and has not been modified." . "<br>";
            }
        } else {
            // Check if the record already exists
            $checkQuery = "SELECT * FROM `$table` WHERE `$column` = '$value'";
            $checkResult = $conn->query($checkQuery);

            if ($checkResult && $checkResult->num_rows == 0) {
                if ($conn->query($insertQuery) === TRUE) {
                    echo "Record has been successfully inserted into table '$table'."."<br>";
                } else {
                    echo "Error in inserting record into table '$table': " . $conn->error . "<br>";
                }
            } else {
                echo "Record already exists in table '$table'."."<br>";
            }
        }
    } else {
        echo "Error in updating record in table '$table': " . $conn->error . "<br>";
    }
}

echo "<br>" . "End of Update Script." . "<br>";
$conn->close();
