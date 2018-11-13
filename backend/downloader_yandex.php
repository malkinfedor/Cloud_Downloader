<?php
  $links_file = "links.txt";
  //$storage_path = "/volume2/Downloads_new";
  $storage_path = "../";
  $bin_dir = "/usr/bin/";
  $yadisk_direct = "yadisk-direct";
  $current_dir = dirname(__FILE__);

  $argument1 = $_GET['links'];
  $filename = $_GET['file_name'];

  //echo $argument1 . PHP_EOL;;
  //echo htmlentities('<p>' . $argument1 . '</p>');
  //echo '<br>';

  // ======================================================================================================== //


  $filename = pathcombine($storage_path, $filename);
  $yadisk_direct = pathcombine($bin_dir, $yadisk_direct);

  $arr = explode("\n", $argument1);
  $first_link = explode(";", $arr[0]);
  //echo "Link is $first_link[0]";
  //echo "FileName is $first_link[1]";

//  if (empty($first_link[1])) {
//      echo "Arr1 is empty";
//  }
//  else{
//      echo "Arr1 is NOT empty";
//  }

  StartDownload();

  //echo htmlentities('<p>' . "Downloading in background job..." . '</p>');
  //echo '<br>';
  //echo "Downloading in background job..." . PHP_EOL;

  // ======================================================================================================== //


  function StartDownload()
  {
    global $filename, $argument1, $arr;
    //if ($arr[1])) unlink($file4aria);


    // Check dir with this name doesn't exif or exit with code 1
    // $command = "nohup wget $(/usr/bin/yadisk-direct \"{$arr[0]}\") -O \"{$filename}\".zip 2>&1 & "; // > /dev/null
    $command = "nohup sh -c  'wget $(/usr/bin/yadisk-direct \"{$arr[0]}\") -O \"{$filename}\".zip && mkdir \"{$filename}\" && unzip \"{$filename}\".zip -d \"{$filename}\"' 2>&1 &";
	//passthru("{$command}");
	exec("{$command}");
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
