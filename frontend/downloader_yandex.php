<?php
  $links_file = "links.txt";
  //$storage_path = "/volume2/Downloads_new";
  $storage_path = "./";

  $bin_dir = "/usr/bin/";
  $yadisk_direct = "yadisk-direct";
  $current_dir = dirname(__FILE__);

  $argument1 = $_GET['links'];
  $filename = $_GET['file_name'];

  echo $argument1 . PHP_EOL;;

  // ======================================================================================================== //


  $filename = pathcombine($storage_path, $filename);
  $yadisk_direct = pathcombine($bin_dir, $yadisk_direct);

  $arr = explode("\n", $argument1);
  $first_link = explode(";", $arr[0]);
  StartDownload();

  echo "Downloading in background job..." . PHP_EOL;

  // ======================================================================================================== //


  function StartDownload()
  {
    global $filename, $argument1, $arr;
    echo $arr[0];
    echo $filename;
    $command = "nohup wget $(/usr/bin/yadisk-direct \"{$arr[0]}\") -O \"{$filename}\" 2>&1 & "; // > /dev/null
	passthru("{$command}");
	//exec("{$command}");
  }

  // ======================================================================================================== //


 function pathcombine()
  {
    $result = "";
    foreach (func_get_args() as $arg)
    {
        if ($arg !== '')
        {
          if ($result && substr($result, -1) != "/") $result .= "/";
          $result .= $arg;
        }
    }
    return $result;
  }


?>
