<?php
header("Content-Type: text/html; charset=utf-8");

#include_once('functions.inc.php');

if (isset($argv[1]) && isset($argv[2])) {
  $file = $argv[1];
  $sum = $argv[2];
  $newdir = $argv[3];
}
else { exit ("No Args given."); }

if (empty($file))
{ exit("No File given ..."); }

if (!file_exists($file))
{	exit("Cant read File "+$file); }

if (file_exists($file)) {
  $xml = simplexml_load_file($file); }
else { exit("Konnte $file nicht öffnen."); }

$url = 'http://lua-api.factorio.com/latest/';

function clean_string($name, $switch = 'preg')
{
  switch ($switch) {
    case 'preg':
        $name = preg_replace( "/\s+/", "", $name );
        break;
    case 'str':
        $name = str_replace(array("\r", "\n"), '', $name);
        break;
  }
  return $name;
}

$newfilename = $xml->tr->td->span->a->attributes()->href;
$newfilename = preg_replace( "/.*#/", "", $newfilename );
$newfilename = preg_replace( "/\..*/", "", $newfilename );
$newfilename = "$newdir/$newfilename.cson";

echo "Writing to '\e[1;31m$newfilename\e[0m' with the following arguments:

\tfile=$file  items=$sum

Please wait.
";

file_put_contents("$newfilename","'.source.lua':\n");

for ($i=0; $i < $sum; $i++) {
  $prefix = $xml->tr[$i]->td->span->a; # functino name
  $bodyshort = $xml->tr[$i]->td->span; # braces
#  $bodylong = preg_replace("/(f)/", 'function', $bodyshort);
  $bodylong = $bodyshort;
  $urlsuffix = $xml->tr[$i]->td->span->a->attributes()->href;
  $name = preg_replace( "/.*#/", "", $urlsuffix );
  $description = $xml->tr[$i]->td[1];

  $prefix = clean_string( $prefix );
  $bodyshort = clean_string( $bodyshort );
  $bodylong = clean_string( $bodylong );
  $description = clean_string( $description, 'str' );


  $content = "
  '$name':
    'prefix': '$prefix$bodyshort'
    'body': '$prefix$bodylong'
    'description': '$description'
    'descriptionMoreURL': '$url$urlsuffix'
  ";
  file_put_contents($newfilename,$content, FILE_APPEND);
}
echo "Finished processing. Cleaning up ... ";

unlink ( $file );

?>
