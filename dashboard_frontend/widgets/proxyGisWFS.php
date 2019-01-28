<?php
$url = $_SERVER['REQUEST_URI'];
$data = explode("?url=",$url);

/*foreach ($url as $url1){
    $url2 = str_replace('<', '&lt;', $url1);
echo $url2;
}*/
$data_url = file_get_contents($data[1]);

echo($data_url);
?>