<?php
$argument1 = $_GET['links'];


// Получим каждую строку в массив $arr, к нему можем получить доступ через $arr[x]
$arr = explode("\n", $argument1);
  
  // Разделим полученные строки на имя файла и ссылку
  foreach($arr as $element){
    echo "A line is $element" . PHP_EOL;
    $line = explode(";", $element);
    $link = $line[0];
    $filename = $line[1];
    StartDownload($link, $filename);
  }

function StartDownload($link,$filename)
{
    echo "Link is $link and Filename is $filename";
    //$command = "\"{$aria2c}\" --file-allocation=none --check-certificate=false --max-connection-per-server=10 --split=10 --max-concurrent-downloads=10 --summary-interval=0 --continue --user-agent=\"Mozilla/5.0 (compatible; Firefox/3.6; Linux)\" --input-file=\"{$file4aria}\"";
    //passthru("{$command}");
}


?>
