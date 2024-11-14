<?php
    require_once 'utils_web.php';
    require_once 'utils.php';

    class Device {
        private $key_device = '_analytics_wg';
        private $key_uid = '_uid_wg';
        private $token_device = '_analytics_wg_token';
        private $error_get_device_id = 'Error de seguridad. No se encuentra un dispositivo valido';
        private $error_user_agent = 'Error de seguridad. No se encuentra userAgent';
        private $error_ip = 'Error de seguridad. No se encuentra una IP valida';
        //private $url_register_device = "https://us-central1-mi-claro-temp.cloudfunctions.net/register_device";
        //private $url_consult_device = "https://us-central1-mi-claro-temp.cloudfunctions.net/consult_device";
        private $url_register_device = "https://us-central1-mi-claro-cbbda.cloudfunctions.net/register_device";

        private $session_time = 120;

        private $skip = false;
        private $_headers = false;
        private $msgSkip = array("error"=>"0","response"=>"ok");
        
        function _set_headers($headers){
            $this->_headers = $headers;
        }

        function gen_uuid() {
            try {
                $uuidGenerate = sprintf( '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
                    random_int( 0, 0xffff ), random_int( 0, 0xffff ),
            
                    random_int( 0, 0xffff ),
            
                    random_int( 0, 0x0fff ) | 0x4000,

                    random_int( 0, 0x3fff ) | 0x8000,
            
                    random_int( 0, 0xffff ), random_int( 0, 0xffff ), random_int( 0, 0xffff )
                );
                return $uuidGenerate;
            } catch (Exception $e) {
                return null;
            }         
        }
        
        function gen_device_id() {
            if (!isset($_COOKIE[$this->key_device])) {
                // Caduca en 60 segundos
                $uuid = $this->gen_uuid();
                if(is_null($uuid)){
                    return '';
                }
                // Options array for the cookie
                $options = array(
                    'expires' => time() + 2 * $this->session_time,
                    'path' => '/REGISTROPPT/',
                    'domain' => '', // default domain
                    'secure' => true, // should be true if using HTTPS
                    'httponly' => true,
                    'samesite' => 'Lax' // or 'Strict', depending on your needs
                );
                // Set cookie with HttpOnly and SameSite attributes
                setcookie($this->key_device, $uuid, $options);
                setcookie($this->key_uid, uniqid(), $options);
        
                return $uuid;
            } else {
                $uuidValue = $_COOKIE[$this->key_device];
                if($this->is_valid_gen_uuid($uuidValue)){
                    return $uuidValue;
                }
                
                return '';
            }
        }

        private function set_info_webapp($output)
        {
            $userAgent = "MiClaroApp/1.0 (empty; error; <php/7>)";

            if (!($output["name"] == "" && $output["version"] == "" && $output["platform"] == "")) {
                $userAgent = "MiClaroApp/" . $output["sdk"] . " (" . $output["brand"] . "; " . $_SERVER['HTTP_USER_AGENT'] . "; <" . $output["platform"] . "/web>)";
            }

            $ip = "";
            if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
            } elseif (!empty($_SERVER['HTTP_CLIENT_IP'])) {
                $ip = $_SERVER['HTTP_CLIENT_IP'];
            } else {
                $ip = $_SERVER['REMOTE_ADDR'];
            }

            return array('HTTP_USER_AGENT' => $userAgent, "HTTP_CLIENT_IP" => $ip);
        }

        function get_user_agent(){
            $dta_bw = UtilsWeb::getBrowser();
            return $this->set_info_webapp($dta_bw);
        }

        function get_device_token($srv,$dv_id, $isFirstTime = false){
            try {
                if($this->skip){
                    return $this->msgSkip;
                }
    
                $dta_bw = UtilsWeb::getBrowser();
                $info_app = $this->set_info_webapp($dta_bw);
    
                //$uid = $this->get_device_id();
                $uid = $dv_id;
                if ($uid == ""){
                    return array("error"=>1, "response"=> $this->error_get_device_id);
                }
                if ($info_app['HTTP_USER_AGENT'] == ""){
                    return array("error"=>1, "response"=> $this->error_user_agent);
                }
                if ($info_app['HTTP_CLIENT_IP'] == ""){
                    return array("error"=>1, "response"=> $this->error_ip);
                }
    
                $data = array(
                    "ip" => $info_app['HTTP_CLIENT_IP'],
                    "userAgent" => $info_app['HTTP_USER_AGENT'],
                    "uid" => $uid,
                    "tipo" => "WEB"
                );
    
                $result = $this->post($this->url_register_device,$data);
                $result =  json_decode($result, true);
                if (json_last_error() != JSON_ERROR_NONE) {
                    throw new Exception('Error al codificar la información.');
                }
                $error = $result["error"];
                $response = $result["response"];
    
                $debug = array();
                if ($error == 0 || TRUE === $isFirstTime) {
                    $token = $response["token"];
                    $options = array(
                        'expires' => time() + 2 * $this->session_time,
                        'path' => '/REGISTROPPT/',
                        'domain' => '', // default domain
                        'secure' => true, // should be true if using HTTPS
                        'httponly' => true,
                        'samesite' => 'Lax' // or 'Strict', depending on your needs
                    );
                    setcookie($this->token_device, $token, $options);
                    $debug["token"] = $token;
                    $debug["session_time"] = time() + 2 * $this->session_time;
                    $debug["cookie"] = $this->token_device;
                    //$debug["cookie_srv"] = $_COOKIE[$this->token_device];
                }
    
                return $result;
            }  catch (Exception $e) {
                return array(
                    "error" => 1,
                    "response" => $e->getMessage()
                );
            }       
        }
   
        function post($url,$data){
            try {
                $postdata = Utils::getJSONSafeValue( array('data' => $data), true );
                if(is_null($postdata)){
                    throw new Exception('Data vacia.');
                }
                
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
                curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
                $result = curl_exec($ch);
                curl_close($ch);
                
                return $result;
            } catch (Exception $e) {
                return "";
            }        
        }
        function is_valid_gen_uuid($uuid) {
            $uuidRegex = '/^[0-9a-f]{4}[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}[0-9a-f]{4}[0-9a-f]{4}$/i';
            
            if (!preg_match($uuidRegex, $uuid)) {
                return false;
            }
        
            $parts = explode('-', $uuid);
        
            if (count($parts) !== 5) {
                return false;
            }

            if (hexdec($parts[2]) >> 12 !== 4) {
                return false;
            }
        
            if ((hexdec($parts[3]) & 0xc000) >> 14 !== 2) {
                return false;
            }
        
            return true;
        }
    }
?>