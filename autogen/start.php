<?php
/*
  This code is written at Factorio 0.15.34
  I can not guarantee, that it will work
  in future versions. Keep that in mind.
*/
include_once('functions.inc.php');
include_once('settings.inc.php');
include_once('html2xml-09b.php');

error_reporting(DEBUG ? 1 : E_STRICT); // FEATURE Implement proper debug mode

if (DEBUG) { // TODO Should Debugmode only use local files?
  $json_file = 'test.json';
  echo "\n\t\t.:: DEBUG MODE ENABLED ::.\n\t\tSaving in 'test.json'\n\n";
  unlink($json_file);
} else {
  $json_file = '../lib/api.json';
}

if (true) { // TODO implement local filehandling
  echo "\n Downloading latest Builtins: ";
  $built_file = get_web_page("https://lua-api.factorio.com/latest/Builtin-Types.html");
  echo "\n Downloading latest Document: ";
  $doc_file = get_web_page("https://lua-api.factorio.com/latest/Classes.html");
} else {
#  $file = $pathtodocs.DIRECTORY_SEPARATOR."Classes.html";
#  $file = get_absolute_path($file);
  exit('Unsupported atm.');
}

$target = $pathtotarget.'classes.xml';
$builtins = $pathtotarget.'builtins.xml';
$target = get_absolute_path($target);
$builtins = get_absolute_path($builtins);

echo "\n\n Generating Builtins XML from HTML.\n";
$result = html2xml($built_file, true);
$handle = fopen($builtins, "wb");
fwrite($handle, $result);
fclose($handle);

echo "\n Generating Document XML from HTML. This may take a while ...\n";
/*
$result = html2xml($doc_file, true);
$handle = fopen($target, "wb");
fwrite($handle, $result);
fclose($handle);
*/
echo "\n\n Checking Files: ";
check_file($target);
check_file($builtins);
echo " Existing and not empty.";

echo "\n\n\t\t.:: Parsing XMLs ::.\n";
// get all the Builtin-Types
echo "\n\t - Builtins";
$xml = simplexml_load_file($builtins);
$loop_div = $xml->body->div[1]->children()->count();
$builtins_arr = array();
for ($i=0; $i<$loop_div; $i++) {
  $builtins_arr[$i] = $xml->body->div[1]->div[$i]->span->a;
  $builtins_arr[$i] = clean_string( $builtins_arr[$i] );
}

// Load XML, Get the Version and set the vars for the Loops
echo "\n\t - Classes";
$xml = simplexml_load_file($target);
$version = (string)$xml->body->div->span[1];
$loop_div = $xml->body->p[2]->children()->count();
$content = array(); $j = 0; // TESTING: use json_encode instead of variable
$loop_tr = $xml->body->p[2]->div[0]->table->children()->count();

echo "\n\n $version\n\n   Classes:  $loop_div\n     Methods:  $loop_tr\n";

// Loop over both Files
for ($i=1; $i<$loop_div; $i++) {      // Class loop (i=0, they shifted again)

// Get the CLASS
  $label = (string)$xml->body->p[2]->div[$i]->div->span->a;
  $label = clean_string( $label );

// Calculate the amount of loops for the current table
// TESTING doesnt seem to get all methods ...
// BUG fails hard
//$loop_tr = $xml->body->p[2]->div[$i]->div->table->children()->count();


echo "\n\t$i: $label";
//echo "\n\tlabel: $label: ";



  for ($k=0; $k<$loop_tr; $k++) {    // Method loop

// Get the actual NAME
    $text = (string)$xml->body->p[2]->div[$i]->div->table->tr[$k]->td[0]->span->a; // BUG returns wrong anchorname for MoreURL
    $text .= (string)$xml->body->p[2]->div[$i]->div->table->tr[$k]->td[0]->span; // TESTING find a better solution for bracets
    $text = str_replace ( ')' , ') )' , $text );
    $text = explode(" )", $text);
    $text = clean_string($text);
    $text = str_replace ( '=' , '= ' , $text );

if (DEBUG) {echo "\n\t\t$text[0]";}

// Get the TYPE
    $type[0] = 'void'; // TESTING Find a better Solution to figure out the type of a suggestion
    $type[1] = (string)$xml->body->p[2]->div[$i]->div->table->tr[$k]->td[0]->span->span->a;
    $type[2] = (string)$xml->body->p[2]->div[$i]->div->table->tr[$k]->td[0]->span->span->span->a;
    $type[3] = (string)$xml->body->p[2]->div[$i]->div->table->tr[$k]->td[0]->span[1]->span->a;
    foreach($builtins_arr as $builtin) {
      foreach($type as $types) {
        if($builtin == $types) { $type[0] = (string)$builtin; break 2; }
      }
    }

// Get the DESCRIPTION
    $description = (string)$xml->body->p[2]->div[$i]->div->table->tr[$k]->td[1];
    #$description = firstSentence($description); # keep it short if needed
    $description = ltrim(rtrim($description));
    if (!$description) { $description = 'No Description available.'; }

// Add to the array
    $content[$j] = array( 'text' => $text[0], 'type' => $type[0], 'description' => $description, 'label' => $label, 'version' => $version);
    $j++;
  }
}

// generate the json File and save it
echo "\n\n Encoding json: ";
$json = array_values(array_unique($content, SORT_REGULAR));
$json = json_encode($json, JSON_PRETTY_PRINT);
echo json_last_error_msg();

// write to file
if (DEBUG) {
  echo "\n\n Writing into $json_file";
  file_put_contents($json_file, $json);
} else {
  echo "\n\n\n\t\t.:: Backup ::.\n";
  $version = str_replace('Factorio ', '', $version);
  $old_file = str_replace('.json', '_', $json_file);
  echo "\n Renaming old File to\t $old_file$version.json";
  rename($json_file, $old_file.$version.'.json');
  echo "\n Writing into\t\t $json_file.";
  file_put_contents($json_file, $json);
  echo "\n\n\n\t\t.:: Cleanup ::.\n";
//  unlink($target);
//  unlink($builtins);
  echo "\n Deleting $target and $builtins";
}
echo "\n\n\n\t\t.:: DONE! ::.\n";
