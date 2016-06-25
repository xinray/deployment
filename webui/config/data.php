<?php

$file_path="/Users/ray/Desktop/work/deployment/webui/config/productconfig.json";
$myfile = fopen($file_path, "r");
$data = fread($myfile,filesize($file_path));

return json_decode($data, true);