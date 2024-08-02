<?php

$envFileContent = parse_ini_file("conf/environment.ini");
$activeEnv = $envFileContent["environment"]["value"];

$currentPath = getcwd();

$filesList = scandir("conf/");
$j = 0;

$variables = [];

$configVarsStr = isset($_GET['configVarsStr']) ? explode(',', $_GET['configVarsStr']) : [];

for ($i = 0; $i < count($filesList); $i++) {
    if (($filesList[$i] != ".") && ($filesList[$i] != "..") && ($filesList[$i] != "environment.ini")) {
        $fileContent = parse_ini_file("conf/" . $filesList[$i]);

        foreach ($fileContent as $key => $value) {
            if (($key != "fileDesc") && ($key != "customForm")) {
                if (is_array($value)) {
                    $varName = $key;
                    $env = getenv("DBB_" . strtoupper($key));
                    if ($env === FALSE)
                        $variables[$varName] = $fileContent[$key][$activeEnv];
                    else
                        $variables[$varName] = $env;
                }
            }
        }
        $j++;
    }
}

$filteredVariables = array_filter($variables, function($key) use ($configVarsStr) {
    return in_array($key, $configVarsStr);
}, ARRAY_FILTER_USE_KEY);

echo json_encode($filteredVariables);