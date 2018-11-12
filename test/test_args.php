#!/usr/bin/php
<?php
// loop through each element in the $argv array
foreach($argv as $value)
{
  $arr = explode("\n", $value);
  //$value = $value . PHP_EOL;
  foreach($arr as $element){
    echo "$element";
  }
}
?>
