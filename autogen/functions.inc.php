<?php

// TODO: Get File via cURL, ignore SSL
function get_web_page( $name, $url )
{
    $options = array(
        CURLOPT_RETURNTRANSFER => true, 		// return web page
        CURLOPT_HEADER         => false,    		// don't return headers
        CURLOPT_FOLLOWLOCATION => true,     // follow redirects
        CURLOPT_ENCODING       => "",       		// handle all encodings
        CURLOPT_USERAGENT      => "spider", 	// who am i
        CURLOPT_AUTOREFERER    => true,     	// set referer on redirect
        CURLOPT_CONNECTTIMEOUT => 120,      // timeout on connect
        CURLOPT_TIMEOUT        => 120,      		// timeout on response
        CURLOPT_MAXREDIRS      => 10,       		// stop after 10 redirects
        CURLOPT_SSL_VERIFYPEER => false     	// Disabled SSL Cert checks
    );

    $ch      		= curl_init( $url );
    curl_setopt_array( $ch, $options );
    $content 	= curl_exec( $ch );
    $err    		= curl_errno( $ch );
    $errmsg  	= curl_error( $ch );
    $header  	= curl_getinfo( $ch );
    curl_close( $ch );

    $header['errno']   	= $err;
    $header['errmsg']  	= $errmsg;
    $header['content']	= $content;
    return $header;
}

// check File
function check_file($file) {
  if (isset($argv[0]))
  { exit("\nArgument not set.\n"); }
  if (empty($file))
  { exit("\nFile not found.\n"); }
  if (!file_exists($file))
  { exit("\nCan't open $file\n"); }
  return true;
}

// return first sentence of string
function firstSentence($string) {
      $sentences = explode(".", $string);
      if (substr($sentences, -1) == '.' || substr($sentences, -1) == '?') {
        return ltrim($sentences[0]);
      } else {return ltrim($sentences[0]).".";}
}

// Check there's at least one item in the array before accessing it
function is_array_empty($array) {
  if (count($array) > 0) { return true; }
  return false;
}

// gets the absolute path, eg. cuts relatives away
function get_absolute_path($path) {
  $path = str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, $path);
  $parts = array_filter(explode(DIRECTORY_SEPARATOR, $path), 'strlen');
  $absolutes = array();
  foreach ($parts as $part) {
    if ('.' == $part) continue;
    if ('..' == $part) {
      array_pop($absolutes);
    } else {
    $absolutes[] = $part;
    }
  }
  return implode(DIRECTORY_SEPARATOR, $absolutes);
}

// cleans up strings from a lot of stuff
function clean_string($item, $switch = 'preg')
{
  switch ($switch) {
    case 'preg':
        $item = preg_replace( "/\s+/", "", $item );
        $item = preg_replace( "/.*#/", "", $item );
        $item = preg_replace( "/\..*/", "", $item );
    break;
    case 'str':
        $item = str_replace(array("\r", "\n"), '', $item);
        $item = str_replace("\"", '', $item);
    break;
    case 'esc': # removes escapes to statisfy json
        $item = str_replace('\u2026', '...', $item);
        $item = str_replace('\u2192', '', $item);
    break;
  }
  return $item;
}
