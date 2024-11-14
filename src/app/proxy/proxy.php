<?php
include("utils.php");
include("uuid.php");
header("Strict-Transport-Security: max-age=31536000; includeSubDomains; preload");
header('X-Frame-Options: DENY');
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');

$URLGLOBAL = "https://apiselfservice.co/";

$device = new Device();

$dev_id = $device->gen_device_id();
$result = $device->get_device_token($_SERVER, $dev_id, true);

function getDeviceId($serv)
{
   try{
      $uid = "";
      if (isset($_COOKIE["_analytics_wg_token"])) {
         $uid = $_COOKIE["_analytics_wg_token"];
      } else if (isset($_COOKIE["analytics_wg"])) {
         $uid = $_COOKIE["analytics_wg"];
      }
   
      if(empty($uid)){
         $uid = $GLOBALS['dev_id'];
      }
   
      $Key = "eb4d00dc92d0d80993efeee3889bd26f";
      $iv = "d11da23d36fda0b2";
      $encryptionMethod = "AES-256-CBC";
   
      $dta_bw = UtilsWeb::getBrowser();
      $ref = $serv['HTTP_USER_AGENT'];
      $brand = $dta_bw["brand"];
      $sdk = $dta_bw["sdk"];
      $so = $dta_bw["platform"];
      $dtaEncr = (object) array("sov" => "web", "sdk" => $sdk, "brand" => $brand, "uid" => $uid, "so" => $so, "ref" => $ref);

      $encodeData = json_encode($dtaEncr);
      if (json_last_error() !== JSON_ERROR_NONE) {
         throw new Exception("Error al codificar la información.");
      }

      $encryptedMessage = openssl_encrypt($encodeData, $encryptionMethod, $Key, 0, $iv);
      $idDivice = str_replace("*suma*", "+", $encryptedMessage);
      return $idDivice;
   } catch (Exception $e) {
      return "";
   }
}

function isBase64($string) {
   if (!preg_match('/^[a-zA-Z0-9\/\r\n+]*={0,2}$/', $string)) {
       return false;
   }
   
   if (strlen($string) % 4 !== 0) {
       return false;
   }

   $decoded = base64_decode($string, true);
   
   if ($decoded === false) {
       return false;
   }

   if (base64_encode($decoded) !== $string) {
       return false;
   }

   return true;
}

function validate_request($bs64){
   try {
      $isBase64 = isBase64($bs64);
      if(!$isBase64){
         return "";
      }

      $params = json_decode(urldecode(base64_decode($bs64)), true);
      if(json_last_error() != JSON_ERROR_NONE){
         return "";
      }

      if(!isset($params["Metodo"], $params["Params"], $params["Headers"])){
         return "";
      }

      $required_keys = array(
         "Cache-Control",
         "X-MC-SO",
         "X-MC-MAIL",
         "X-SESSION-ID",
         "X-MC-LINE",
         "X-MC-LOB",
         "X-MC-ENCBODY",
      );

      $headersRequest = $params["Headers"];
      $new_headers = array();
      foreach ($headersRequest as $key) {
         $ky = explode(":", $key)[0];
         if (in_array($ky, $required_keys)) {
            array_push($new_headers, $key);
         }
      }
      $params["Headers"] = $new_headers;
      return $params;
   } catch (Exception $e) {
      return "";
   }
}


if ($_SERVER['REQUEST_METHOD'] === 'GET') {
   try {
      $url = $_SERVER["REQUEST_URI"];
      $url = explode("?", $url);
      $urlextra = base64_decode($url[1]);
   
      $device->_set_headers(array());
      $diviceId = getDeviceId($_SERVER);
      $deviceUAData = $device->get_user_agent();
      if (is_null($deviceUAData) || empty($deviceUAData) || empty($diviceId)) {
         throw new Exception("Error al obtener la información.");
      }

      $jsonEncodedData = json_encode($deviceUAData);
      if (json_last_error() !== JSON_ERROR_NONE) {
         throw new Exception("Error al codificar la información.");
      }

      $userAgent = base64_encode($jsonEncodedData);
   
      $headers = array();
   
      array_push($headers, 'X-MC-DEVICE-ID: ' . $diviceId);
      array_push($headers, 'X-MC-SO: web');
      array_push($headers, "Content-Type:text/plain");
      array_push($headers, 'X-MC-USER-AGENT: ' . $userAgent);
      $curl = curl_init();

      curl_setopt_array($curl, array(
         CURLOPT_RETURNTRANSFER => 1,
         CURLOPT_URL => $URLGLOBAL . str_replace("--", "?", $urlextra),
         CURLOPT_HTTPHEADER => $headers
      ));
   
   $server_output = curl_exec($curl);
   $server_output = curl_exec($curl);
   // Close request to clear up some resources
      $server_output = curl_exec($curl);
   // Close request to clear up some resources
      curl_close($curl);
      echo $server_output;
   } catch (Exception $e) {
      echo "";
   }
} else {
   try {
      $request = validate_request(file_get_contents('php://input'));

      if(empty($request)){
         echo "";
      }else{
         $params = $request;

         $headers_rq = isset($params["Headers"]) ? $params["Headers"] : array();
         $device->_set_headers($headers_rq);

         $ch = curl_init();

         $diviceId = getDeviceId($_SERVER);

         $deviceUAData = $device->get_user_agent();
         if (is_null($deviceUAData) || empty($deviceUAData) || empty($diviceId)) {
            throw new Exception("Error al obtener la información.");
         }

         $jsonEncodedData = json_encode($deviceUAData);
         if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception("Error al codificar la información.");
         }

         $userAgent = base64_encode($jsonEncodedData);

         if (count(explode("/", $params["Metodo"])) == 1) {
            curl_setopt($ch, CURLOPT_URL, $URLGLOBAL . "soap/" . $params["Metodo"]);
         } else {
            curl_setopt($ch, CURLOPT_URL, $URLGLOBAL . $params["Metodo"]);
         }
         
         foreach ($params["Headers"] as $key => $value) {
            if (strrpos($value, 'X-MC-SO:') !== false) {
               unset($params["Headers"][$key]);
            }
         }
         sort($params["Headers"]);
         array_push($params["Headers"], 'X-MC-DEVICE-ID: ' . $diviceId);
         array_push($params["Headers"], 'X-MC-SO: web');
         array_push($params["Headers"], 'X-MC-APP-V: 17.2.2');
         array_push($params["Headers"], "Content-Type:text/plain");
         array_push($params["Headers"], 'X-MC-USER-AGENT: ' . $userAgent);
         $headers = $params["Headers"];

         curl_setopt($ch, CURLOPT_POST, 1);

         
         $payload = json_encode($params["Params"]);
         $body = $payload;
         $decryptData = false;
         foreach ($headers as $key) {
            $ky = explode(":", $key)[0];
            $kVal = explode(":", $key)[1]; 
            if ($ky == 'X-MC-ENCBODY' && $kVal == 1) {
               $decryptData = true;
               $body = Utils::encryptBody($payload);
            }
         }

         curl_setopt($ch, CURLOPT_POSTFIELDS,  $body);

         curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
         curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
         $server_output = curl_exec($ch);

         curl_close($ch);

         $json_output = Utils::getSafeValue($server_output, true);
         $res = json_decode($json_output);
         if($decryptData){
            $res = json_decode(Utils::decryptBody($json_output), true);
         }
         
         echo Utils::getJSONSafeValue($res, true);
      }
   } catch (Exception $e) {
      echo "";
   }
}
