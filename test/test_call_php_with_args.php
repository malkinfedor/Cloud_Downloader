<?php
$argument1 = $_GET['links'];


  // Получим каждую строку в массив $arr, к нему можем получить доступ через $arr[x]
  $lines = explode("\n", $argument1);
  $lenthgt = count($lines);
  echo $lenthgt;
  echo '<br>';
  // Разделим полученные строки на имя файла и ссылку
  foreach($lines as $line){
    echo "A line is $line" . PHP_EOL;
    echo '<br>';
    $line_split = explode(";", $line);
    $link = $line_split[0];
    $filename = $line_split[1];
    $a = 'How are you?';
    if (strpos($link, 'yadi.sk') !== false) {
     echo 'This link from yadi.sk';
     echo '<br>';
     StartDownloadYandex($link, $filename);
    }
    elseif (strpos($link, 'mail.ru') !== false){
        echo 'This link from mail.ru';
        echo '<br>';
    }
    else{
        echo 'This link is incorrect';
        exit("This link ($link) is incorrect");
        echo '<br>';
    }


  }

function StartDownloadYandex($link,$filename)
{
    echo "Link is $link and Filename is $filename";
    echo '<br>';
    $command = "nohup sh -c  'wget $(/usr/bin/yadisk-direct \"{$link}\") -O \"{$filename}\".zip && mkdir \"{$filename}\" && unzip \"{$filename}\".zip -d \"{$filename}\"' 2>&1 &";
//    $command = "wget $(/usr/bin/yadisk-direct \"{$link}\") -O \"{$filename}\".zip";
    echo "Command is $command";
    echo '<br>';
    exec("{$command}");
    echo "Task for download from this link $link is started and work in background";
    echo '<br>';

    //passthru("{$command}");
    //$command = "\"{$aria2c}\" --file-allocation=none --check-certificate=false --max-connection-per-server=10 --split=10 --max-concurrent-downloads=10 --summary-interval=0 --continue --user-agent=\"Mozilla/5.0 (compatible; Firefox/3.6; Linux)\" --input-file=\"{$file4aria}\"";

}

function StartDownloadMail($link,$filename)
{
    echo "Link is $link and Filename is $filename";
    echo '<br>';
//    $command = "nohup sh -c  'wget $(/usr/bin/yadisk-direct \"{$link}\") -O \"{$filename}\".zip && mkdir \"{$filename}\" && unzip \"{$filename}\".zip -d \"{$filename}\"' 2>&1 &";
    $command = "wget $(/usr/bin/yadisk-direct \"{$link}\") -O \"{$filename}\".zip";
    echo "Command is $command";
    echo '<br>';
    exec("{$command}");
    echo "Task for download from this link $link is started and work in background";
    echo '<br>';

    //passthru("{$command}");
    //$command = "\"{$aria2c}\" --file-allocation=none --check-certificate=false --max-connection-per-server=10 --split=10 --max-concurrent-downloads=10 --summary-interval=0 --continue --user-agent=\"Mozilla/5.0 (compatible; Firefox/3.6; Linux)\" --input-file=\"{$file4aria}\"";

}

?>
