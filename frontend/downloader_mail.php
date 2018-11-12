<?php
  $links_file = "links.txt";
//  $storage_path = "/volume2/Downloads_new";
  $storage_path = "./";
  $file4aria = "input.txt";
  $bin_dir = "/usr/bin/";
  $aria2c = "aria2c";
  $current_dir = dirname(__FILE__);
  //  $current_dir = '/volume1/web/Downloader';

$argument1 = $_GET['links'];

echo $argument1 . PHP_EOL;;

// ======================================================================================================== //

  $file4aria = pathcombine($current_dir, $file4aria);
  $aria2c = pathcombine($bin_dir, $aria2c);

  if (is_dir($storage_path))
  {
    echo "Dir is true";
  }
  else{
    echo "Dir is false" . PHP_EOL;
    exit("Dir isn't exist ($storage_path)");
  }

  if (file_exists($file4aria)) unlink($file4aria);

  echo "Start create input file for Aria2c Downloader..." . PHP_EOL;
echo $argument1;
	$link = $argument1;
    $base_url = "";
    $id = "";
    if($files = GetAllFiles($link))
    {
      foreach ($files as $file)
      {
        $line = $file->download_link . PHP_EOL;
        $line .= "  out=" . $file->output . PHP_EOL;
        $line .= "  referer=" . $link . PHP_EOL;
        $line .= "  dir=" . $storage_path . PHP_EOL;

        file_put_contents($file4aria, $line, FILE_APPEND);
      }
    }
//   }

  echo "Running Aria2c for download..." . PHP_EOL;
  StartDownload();
  @unlink($file4aria);

  echo "Done!" . PHP_EOL;

  // ======================================================================================================== //

  class CMFile
  {
    public $name = "";
    public $output = "";
    public $link = "";
    public $download_link = "";

    function __construct($name, $output, $link, $download_link)
    {
      $this->name = $name;
      $this->output = $output;
      $this->link = $link;
      $this->download_link = $download_link;
    }
  }

  // ======================================================================================================== //

  function GetAllFiles($link, $folder = "")
  {
    global $base_url, $id;

    $page = get(pathcombine($link, $folder));
    if ($page === false) { echo "Error $link\r\n"; return false; }
    if (($mainfolder = GetMainFolder($page)) === false) { echo "Cannot get main folder $link\r\n"; return false; }

    if (!$base_url) $base_url = GetBaseUrl($page);
    if (!$id && preg_match('~\/public\/(.*)~', $link, $match)) $id = $match[1];

    $cmfiles = array();
    if ($mainfolder["name"] == "/") $mainfolder["name"] = "";
    foreach ($mainfolder["list"] as $item)
    {
      if ($item["type"] == "folder")
      {
        $files_from_folder = GetAllFiles($link, pathcombine($folder, rawurlencode(basename($item["name"]))));

        if (is_array($files_from_folder))
        {
          foreach ($files_from_folder as $file)
          {
            if ($mainfolder["name"] != "")
              $file->output = $mainfolder["name"] . "/" . $file->output;
          }
          $cmfiles = array_merge($cmfiles, $files_from_folder);
        }
      }
      else
      {
        $fileurl = pathcombine($folder, rawurlencode($item["name"]));
        // Старые ссылки содержат название файла в id
        if (strpos($id, $fileurl) !== false) $fileurl = "";
        $cmfiles[] = new CMFile($item["name"],
                                pathcombine($mainfolder["name"], $item["name"]),
                                pathcombine($link, $fileurl),
                                pathcombine($base_url, $id, $fileurl));
      }
    }

    return $cmfiles;
  }

  // ======================================================================================================== //

  //function StartDownload($link,$filename)
  function StartDownload()
  {
    global $aria2c, $file4aria;
    $command = "\"{$aria2c}\" --file-allocation=none --check-certificate=false --max-connection-per-server=10 --split=10 --max-concurrent-downloads=10 --summary-interval=0 --continue --user-agent=\"Mozilla/5.0 (compatible; Firefox/3.6; Linux)\" --input-file=\"{$file4aria}\"";
    passthru("{$command}");
  }

  // ======================================================================================================== //

  function GetMainFolder($page)
  {
    if (preg_match('~"folder":\s+(\{.*?"id":\s+"[^"]+"\s+\})\s+}~s', $page, $match)) return json_decode($match[1], true);
    else return false;
  }

  // ======================================================================================================== //

  function GetBaseUrl($page)
  {
    if (preg_match('~"weblink_get":.*?"url":\s*"(https:[^"]+)~s', $page, $match)) return $match[1];
    else return false;
  }

  // ======================================================================================================== //

  function get($url)
  {
    $proxy = null; //"127.0.0.1:8888";

    $http["method"] = "GET";
    if ($proxy) { $http["proxy"] = "tcp://" . $proxy; $http["request_fulluri"] = true; }
    $options['http'] = $http;
    $context = stream_context_create($options);
    $body = @file_get_contents($url, NULL, $context);
    return $body;
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

  // ======================================================================================================== //
?>
