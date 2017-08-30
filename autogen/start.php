<?php
/*
  This code is written at Factorio 0.15.33
  I can not guarantee, that it will do so
  in future versions. Keep that in mind.
*/
include_once('functions.inc.php');
include_once('settings.inc.php');
include_once('html2xml-09b.php');

error_reporting(DEBUG ? 1 : E_STRICT); // TODO: Implement prober debug mode +feature

if (true) { // TODO implement local filehandling id:2
  echo "\nDownloading latest Document: ";
  $curl = curl_init("http://lua-api.factorio.com/latest/Classes.html");
  curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
  $doc_file = curl_exec($curl);
  echo curl_error($curl) ? exit("\n\t".curl_error($curl)) : "No error";
  curl_close($curl);

  echo "\nDownloading latest Builtins: ";
  $curl = curl_init("http://lua-api.factorio.com/latest/Builtin-Types.html");
  curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
  $built_file = curl_exec($curl);
  echo curl_error($curl) ? exit("\n\t".curl_error($curl)) : "No error";
  curl_close($curl);
} else {
  $file = $pathtodocs.DIRECTORY_SEPARATOR."Classes.html";
  $file = get_absolute_path($file);
}

$target = $pathtotarget.'classes.xml';
$builtins = $pathtotarget.'builtins.xml';
$target = get_absolute_path($target);
$builtins = get_absolute_path($builtins);

$json_file = '../lib/api.json'; // TODO: Should Debugmode only use local files?
if (DEBUG) { $json_file = 'test.json'; echo "\n\n\t\t DEBUG MODE ENABLED\n\t\tSaving in 'test.json'\n"; unlink($json_file); }

echo "\nGenerating Document XML from HTML. This may take a while ...";
$result = html2xml($doc_file);
$handle = fopen($target, "wb");
fwrite($handle, $result);
fclose($handle);

echo "\nGenerating Builtins XML from HTML. This may take a while ...";
$result = html2xml($built_file);
$handle = fopen($builtins, "wb");
fwrite($handle, $result);
fclose($handle);

echo "\n\nChecking Files: ";
check_file($target);
check_file($builtins);
echo "No error";

echo "Parsing XMLs";
$xml = simplexml_load_file($builtins);
$loop_div = $xml->body->div[1]->children()->count();
$builtins_arr = array();
for ($i=0; $i<$loop_div; $i++) {
  $builtins_arr[$i] = $xml->body->div[1]->div[$i]->span->a;
  $builtins_arr[$i] = clean_string( $builtins_arr[$i] );
}

// Load XML, Get the Version and set the vars for the Loops
$xml = simplexml_load_file($target);
$version = (string)$xml->body->div->span[1];
$loop_div = $xml->body->p[2]->children()->count();
$loop_tr = $xml->body->p[2]->div[0]->div->table->children()->count();

$content = array(); $j = 0; // TESTNG: use json_encode instead of variable id:1 +Testing

// Loop over both Files
for ($i=0; $i<$loop_div; $i++) {      // Class loop

// Get the CLASS
  $label = (string)$xml->body->p[2]->div[$i]->div->span->a;
  $label = clean_string( $label );

  for ($k=0; $k<=$loop_tr; $k++) {    // Method loop

// Get the actual NAME
    $text = (string)$xml->body->p[2]->div[$i]->div->table->tr[$k]->td[0]->span->a;
    $text .= (string)$xml->body->p[2]->div[$i]->div->table->tr[$k]->td[0]->span; // TESTING: find a better solution for bracets +testing
    $text = str_replace ( ')' , ') )' , $text );
    $text = explode(" )", $text);
    $text = clean_string($text);
    $text = clean_string($text, 'esc');

// Get the TYPE
    $type[0] = 'void'; // TESTING: Find a better Solution to figure out the type of a suggestion id:0 +testing
    $type[1] = (string)$xml->body->p[2]->div[$i]->div->table->tr[$k]->td[0]->span->span->a;
    $type[2] = (string)$xml->body->p[2]->div[$i]->div->table->tr[$k]->td[0]->span->span->span->a;
    $type[3] = (string)$xml->body->p[2]->div[$i]->div->table->tr[$k]->td[0]->span[1]->span->a;
    foreach($builtins_arr as $builtins) {
      foreach($type as $types) {
        if($builtins == $types) { $type[0] = (string)$builtins; break 2; }
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
echo "\nEncoding json: ";
$json = array_values($content);
$json = json_encode($json, JSON_PRETTY_PRINT);


echo json_last_error_msg();

if (DEBUG) {
  echo "\nWriting into $json_file";
  file_put_contents($json_file, $json);
} else {
  echo "\nRenaming $json_file to OLD".$json_file;
  rename($json_file, 'OLD'.$json_file);
  echo " Writing into $json_file.";
  file_put_contents($json_file, $json);
  echo "\n... Cleanup ...\n";
  unlink($target);
  unlink($builtins);
}
echo "\n\t\tDONE!\n";
