<?php
header("Content-Type: text/html; charset=utf-8");

#include_once('functions.inc.php');

if (isset($argv[1]) && isset($argv[2])) {
  $file = $argv[1];
  $sum = $argv[2];
  $newdir = $argv[3];
  $version = $argv[4];
}
else { exit ("Argument Error."); }

if (empty($file))
{ exit("File not found."); }

if (!file_exists($file))
{	exit("Cant read File "+$file); }

if (file_exists($file)) {
  $xml = simplexml_load_file($file); }
else { exit("Can't open $file"); }

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

$label = $xml->tr->td->span->a->attributes()->href;
$label = preg_replace( "/.*#/", "", $label );
$label = preg_replace( "/\..*/", "", $label );
#$newfilename = "$newdir/$label.json";
$newfilename = '../lib/api.json';

echo "Writing to '\e[1;31m$newfilename\e[0m' with the following arguments:

\tfile=$file  items=$sum

Please wait.
";

#$includes = "../autogen/$newfilename";
#$includes = "import suggestions from '$includes';";
#file_put_contents("../lib/_index.json","\n$includes", FILE_APPEND);
#file_put_contents("$newfilename","[");

for ($i=0; $i < $sum; $i++) {
  $text = (string)$xml->tr[$i]->td->span->a; # function name
  $braces = (string)$xml->tr[$i]->td->span; # braces
  $type = (string)$xml->tr[$i]->td->span[1]->span->a; # type like bool
  if (empty($type)) {
    $type = (string)$xml->tr[$i]->td->span->span->span->a; } # alt if type is empty
  if (empty($type)) { $type = "Unknown"; }
  $description = (string)$xml->tr[$i]->td[1]; # the what for which

  $text = clean_string( $text );
  $braces = clean_string( $braces );
  $type = clean_string( $type );
  $description = clean_string( $description, 'str' );
  $description = preg_replace( "/\s+/", " ", $description );
  $description = str_replace("\"", '', $description);


  $content = "
  {
    \"text\": \"$text$braces\",
    \"type\": \"$type\",
    \"description\": \"$description\",
    \"label\": \"$label\",
    \"version\": \"$version\"
  },";
  file_put_contents($newfilename,$content, FILE_APPEND);
}
#file_put_contents($newfilename,"\n]", FILE_APPEND);
echo "Finished processing. Cleaning up ... ";

unlink ( $file );
