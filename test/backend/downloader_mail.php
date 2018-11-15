<?php
  $links_file = "links.txt";
//  $storage_path = "/volume2/Downloads_new";
  $base_path = "/var/www/html";
  //$storage_path = "../";
  $storage_path = "/var/www/downloads";
  $file4aria = "input.txt";
  $bin_dir = "/usr/bin/";
  $aria2c = "aria2c";
  $current_dir = dirname(__FILE__);

  $textarea = $_GET['links'];
  $filename = $_GET['file_name'];

  echo $textarea . PHP_EOL;
  echo '<br>';

  $file4aria = pathcombine($current_dir, $file4aria);
  $aria2c = pathcombine($bin_dir, $aria2c);
  //$storage_path = pathcombine($current_dir, $storage_path);

  if (is_dir($storage_path))
  {
    echo "Dir $storage_path is exist so we can download to that";
    echo '<br>';
  }
  else{
    exit("Dir ($storage_path) isn't exist ");
  }

// ======================================================================================================== //

// Передадим каждую строку в массив $lines разделив строку $argument по символу переноса строки. Теперь к строкам можем получить доступ через $lines[x]
$lines = explode("\n", $textarea);

// If number of lines more than 1 so we will use filename from textarea.
// Else if field filename is filled so we will use its value
   $number_of_lines = count($lines);
   if ($number_of_lines > 1) {
      $use_textarea_filename = true;
   }

// Разделим полученные строки на имя файла и ссылку
foreach($lines as $line){
    echo "A line is $line" . PHP_EOL;
    echo '<br>';
    $line_split = explode(";", $line);
    $link = $line_split[0];

    // If number of lines more than 1 so we will use filename from textarea.
    // Else if field filename is filled  so we will use its value
    if ($use_textarea_filename) {
        $filename = $line_split[1];
    }
//    else{
//        if (!(empty($filename))) ($filename = );
//    }

    if (strpos($link, 'yadi.sk') !== false) {
        echo 'This link from yadi.sk';
        echo '<br>';
        StartDownloadYandex($link, $filename, $storage_path);
    }

    elseif (strpos($link, 'mail.ru') !== false){
        echo 'This link from mail.ru';
        echo '<br>';
        StartDownloadMail($link, $filename);
    }

    else{
        echo 'This link is incorrect';
        exit("This link ($link) is incorrect");
        echo '<br>';
    }
}
// ======================================================================================================== //
function StartDownloadMail($link, $filename)
{
  global $file4aria, $storage_path;
    $storage_path = pathcombine($storage_path, $filename);
    if (file_exists($file4aria)) unlink($file4aria);

    echo "Start create input file for Aria2c Downloader..." . PHP_EOL;
    //$link = $textarea;
    $base_url = "";
    $id = "";
    if ($files = GetAllFiles($link)) {
        foreach ($files as $file) {
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
}

// ======================================================================================================== //


function StartDownloadYandex($link,$filename,$storage_path)
{
    echo "Link is $link and Filename is $filename";
    echo '<br>';
    $filename = pathcombine($storage_path, $filename);
    $command = "nohup sh -c  'wget $(/usr/bin/yadisk-direct \"{$link}\") -O \"{$filename}\".zip && mkdir \"{$filename}\" && unzip \"{$filename}\".zip -d \"{$filename}\"' 2>&1 &";
//    $command = "wget $(/usr/bin/yadisk-direct \"{$link}\") -O \"{$filename}\".zip";
    echo "Command is $command";
    echo '<br>';
    exec("{$command}");
    echo "Task for download from this link $link is started and work in background";
    echo '<br>';
}
// su -s /bin/sh nginx -c "touch /var/www/downloads/test"
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
