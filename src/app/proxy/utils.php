<?php 

class Utils {

    /**
     * Ignora url especificas para excluir del saneamiento
     */
    public static $ignore = array( '&amp;' );
    public static $replacement = array( '&' );
    public static $keyApp = "PUZ66Q9J";
    public static $iv = '0123456789ABCDEF0123456789ABCDEF';

    /**
     * Obtiene un valor de manera segura, escapando sus caracteres
     * @see https://cwe.mitre.org/data/definitions/838.html
     * @see https://stackoverflow.com/questions/110575/do-htmlspecialchars-and-mysql-real-escape-string-keep-my-php-code-safe-from-inje
     * @api
     */
    public static function getSafeURL( $rawData, $return_transfer = false, $encoding = 'UTF-8' ) {
        $sanitize_string = mb_convert_encoding($rawData, 'UTF-8', $encoding);
        if( $return_transfer ){
            return self::out( htmlspecialchars($sanitize_string, ENT_NOQUOTES, 'ISO-8859-1') );
        }
        echo self::out( htmlspecialchars($sanitize_string, ENT_NOQUOTES, 'ISO-8859-1') );
    }

    /**
     * Obtiene un valor de manera segura usando json_encode, escapando sus caracteres
     * @see https://cwe.mitre.org/data/definitions/838.html
     * @see https://stackoverflow.com/questions/110575/do-htmlspecialchars-and-mysql-real-escape-string-keep-my-php-code-safe-from-inje
     * @api
     */
    public static function getJSONSafeValue($json_data, $return_transfer = false, $encoding = 'UTF-8') {
        try {
            $json_result = json_encode($json_data, JSON_HEX_QUOT | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS);
            if ($json_result === false) {
                throw new Exception('JSON encoding error: ' . json_last_error_msg());
            }
            
            if ($return_transfer) {
                return $json_result;
            }
            echo $json_result;
        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * Aplicamos excepciones a el escape de las urls
     * @see https://cwe.mitre.org/data/definitions/838.html
     * @see https://stackoverflow.com/questions/110575/do-htmlspecialchars-and-mysql-real-escape-string-keep-my-php-code-safe-from-inje
     * @api
     */
    public static function out( $data ) {
        return str_replace( self::$ignore, self::$replacement, $data );
    }

    public static function pkcs7_pad($data) {
        $blockSize = 16;
        $padSize   = $blockSize - (strlen($data) % $blockSize);
        return $data . str_repeat(chr($padSize), $padSize);
    }
    public static function encryptBody($bodydec){
        self::$iv   = self::keyEncBody();
        $key        = self::$keyApp;
        $cipher     = "AES-128-CBC";
        $ciphertext = openssl_encrypt(self::pkcs7_pad($bodydec), $cipher, $key, $options=OPENSSL_RAW_DATA, hex2bin(self::$iv));
        $hmac       = hash_hmac('sha256', $ciphertext, $key, $as_binary=true);
        return base64_encode( hex2bin(self::$iv).$hmac.$ciphertext );
    }

    public static function decryptBody($bodyenc) {
        self::$iv   = self::keyEncBody();
        $key        = self::$keyApp;
        $c          = base64_decode($bodyenc);
        $cipher     = "AES-128-CBC";
        $ivlen      = openssl_cipher_iv_length($cipher);
        $hmac       = substr($c, $ivlen, $sha2len=32);
        $ciphertext = substr($c, $ivlen+$sha2len);
        $decrypted  = openssl_decrypt($ciphertext, $cipher, $key, $options=OPENSSL_RAW_DATA, hex2bin(self::$iv));
        return preg_replace('/[\x00-\x1F\x7F]/u', '', $decrypted);      
    }

    public static function keyEncBody(){
        return base64_decode("YTY5OGY5ZWRmNjcwNDE1MmRlOGZjNWVkYzZkYWM0ZmY=");
    }

    /**
     * Obtiene un valor de manera segura, escapando sus caracteres
     * @see https://cwe.mitre.org/data/definitions/838.html
     * @see https://stackoverflow.com/questions/110575/do-htmlspecialchars-and-mysql-real-escape-string-keep-my-php-code-safe-from-inje
     * @api
     */
    public static function getSafeValue( $rawData, $return_transfer = false, $encoding = 'UTF-8', $mode = 'htmlspecialchars', $flags = ENT_NOQUOTES ) {
        $sanitize_string = mb_convert_encoding($rawData, 'UTF-8', $encoding);
        if( $return_transfer ){
            return self::out( $mode($sanitize_string, $flags, 'ISO-8859-1') );
        }
        echo self::out( $mode($sanitize_string, $flags, 'ISO-8859-1') );
    }
}