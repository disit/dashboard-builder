<?PHP
// 
// Topic: Configuration Options
// 
// These variables can be manually edited in the PHP file if necessary.
// 
//   $enable_jsonp - Only enable <JSONP requests> if you really need to. If you
//     install this script on the same server as the page you're calling it
//     from, plain JSON will work. Defaults to false.
//   $enable_native - You can enable <Native requests>, but you should only do
//     this if you also whitelist specific URLs using $valid_url_regex, to avoid
//     possible XSS vulnerabilities. Defaults to false.
//   $valid_url_regex - This regex is matched against the url parameter to
//     ensure that it is valid. This setting only needs to be used if either
//     $enable_jsonp or $enable_native are enabled. Defaults to '/.*/' which
//     validates all URLs.
// 
// ############################################################################

// Change these configuration options if needed, see above descriptions for info.
$enable_jsonp    = false;
$enable_native   = false;
$valid_url_regex = '/.*/';

// ############################################################################

$url = $_GET['url'];

if ( !$url ) {
  
  // Passed url not specified.
  $contents = 'ERROR: url not specified';
  $status = array( 'http_code' => 'ERROR' );
  
} else if ( !preg_match( $valid_url_regex, $url ) ) {
  
  // Passed url doesn't match $valid_url_regex.
  $contents = 'ERROR: invalid url';
  $status = array( 'http_code' => 'ERROR' );
  
} else {
  $ch = curl_init( $url );
  
  if ( strtolower($_SERVER['REQUEST_METHOD']) == 'post' ) {
    curl_setopt( $ch, CURLOPT_POST, true );
    curl_setopt( $ch, CURLOPT_POSTFIELDS, $_POST );
  }
  
  
  curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, true );
  curl_setopt( $ch, CURLOPT_HEADER, false );
  curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
  curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY); 
  curl_setopt($ch, CURLOPT_USERPWD, 'admin:twv2016!yh');
  
  
  list( $contents ) = preg_split( '/([\r\n][\r\n])\\1/', curl_exec( $ch ), 2 );
  
  $status = curl_getinfo( $ch );
  
  curl_close( $ch );
}
  
  // $data will be serialized into JSON data.
  $data = array();
    
  // Propagate all cURL request / response info to the JSON data object.
 
    $data['status'] = array();
    $data['status']['http_code'] = $status['http_code'];
  
  
  // Set the JSON data object contents, decoding it from JSON if possible.
  $decoded_json = json_decode( $contents );
  $data['contents'] = $decoded_json ? $decoded_json : $contents;
  
  // Generate appropriate content-type header.
 
  header( 'Content-type: application/' . ( 'json' ) );
  
  // Get JSONP callback.
  $jsonp_callback = $enable_jsonp && isset($_GET['callback']) ? $_GET['callback'] : null;
  
  // Generate JSON/JSONP string
  $json = json_encode( $data );
  
  print $json;

?>