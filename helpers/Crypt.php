<?php


namespace app\helpers;


use Yii;
use yii\db\Exception;
use yii\web\UnauthorizedHttpException;

class Crypt{

    const PATH_KEYS = __DIR__."/../keys/";
    public $private_key;
    public $public_key;
    public $public_key_hex;
    public $private_key_hex;
    public $key_pair;

    public function __construct($private_key = false)
    {
        $this->private_key = false;
        $this->key_pair = false;
        $this->private_key_hex =false;
        $this->public_key = false;
        $this->public_key_hex = false;

        if(!Strings::isBinary($private_key)){
            $private_key =  sodium_hex2bin($private_key);
        }

        if($private_key){
            $this->private_key = $private_key;
            $this->generatePubkey();
            $this->private_key_hex = sodium_bin2hex($this->private_key);
            $this->key_pair = sodium_crypto_box_keypair_from_secretkey_and_publickey($this->private_key, $this->public_key);
        }
    }

    public static function getOurSecret(): string{
        return  Strings::byteArrayToString(Yii::$app->params['secret']);
    }

    private function generatePubkey(){
        if(is_resource($this->private_key)) {
            $this->private_key = (stream_get_contents($this->private_key));
        }
        $this->public_key = sodium_crypto_box_publickey_from_secretkey($this->private_key);
        $this->public_key_hex = sodium_bin2hex($this->public_key);
    }


    /**
     * @throws Exception
     */
    public static function basicEncryption(array $array)
    {
        $array["checksum"] = hash("sha256",json_encode($array));
        $array["client"] = (isset(getallheaders()["X-Forwarded-For"]))?getallheaders()["X-Forwarded-For"]:$_SERVER["REMOTE_ADDR"];
        return self::easyEncrypt(json_encode($array), self::getOurSecret());
    }

    /**
     * @throws Exception
     * @throws \Exception
     */
    public static function basicDecryption($string):?array{
        $array = json_decode(self::easyDecrypt($string, self::getOurSecret()), true);
        $checksum = $array["checksum"];
        $client = $array["client"];
        unset($array["checksum"]);
        unset($array["client"]);
        $new_checksum = hash("sha256",json_encode($array));
        $new_client = (isset(getallheaders()["X-Forwarded-For"]))?getallheaders()["X-Forwarded-For"]:$_SERVER["REMOTE_ADDR"];

        if($checksum !== $new_checksum){throw new UnauthorizedHttpException("fail on compare checksum This package no longer has integrity",401);}

//        if($client !== $new_client){throw new UnauthorizedHttpException("fail on compare client This package no longer has AuthZ. UserIP: ".$client." Token client IP: ".$new_client,401);}
        return $array;
    }

    public static function encryptPayload($payload, $private_key, $dpo_pub_key){
        $nonce = random_bytes(SODIUM_CRYPTO_SECRETBOX_NONCEBYTES);
        $crypt = new self($private_key);

        $kA = sodium_crypto_scalarmult($crypt->private_key, sodium_hex2bin($dpo_pub_key));
        $ciphertext_crypto_secretbox = sodium_crypto_secretbox(json_encode($payload), $nonce, $kA);
        $ciphertext_secretbox = bin2hex($ciphertext_crypto_secretbox);

        return ["nonce" => sodium_bin2hex($nonce), "secretBox" => "$ciphertext_secretbox"];
    }

    public static function easyEncrypt(string $message, string $key): string
    {
        if (mb_strlen($key, '8bit') !== SODIUM_CRYPTO_SECRETBOX_KEYBYTES) {
            $key = Strings::convertTo32Bit($key);
        }
        $nonce = random_bytes(SODIUM_CRYPTO_SECRETBOX_NONCEBYTES);

        $cipher = base64_encode(
            $nonce.
            sodium_crypto_secretbox(
                $message,
                $nonce,
                $key
            )
        );
        sodium_memzero($message);
        sodium_memzero($key);
        return $cipher;
    }

    public static function easyDecrypt($encrypted, string $key): string
    {
        if (mb_strlen($key, '8bit') !== SODIUM_CRYPTO_SECRETBOX_KEYBYTES) {
            $key = Strings::convertTo32Bit($key);
        }

        if(is_resource($encrypted)) {
            $encrypted = (stream_get_contents($encrypted));
        }

        $decoded = base64_decode($encrypted);
        $nonce = mb_substr($decoded, 0, SODIUM_CRYPTO_SECRETBOX_NONCEBYTES, '8bit');
        $ciphertext = mb_substr($decoded, SODIUM_CRYPTO_SECRETBOX_NONCEBYTES, null, '8bit');

        $plain = sodium_crypto_secretbox_open(
            $ciphertext,
            $nonce,
            $key
        );
        if (!is_string($plain)) {
            throw new Exception('Invalid MAC');
        }
        sodium_memzero($ciphertext);
        sodium_memzero($key);
        return $plain;
    }

    /**
     * @param $data
     * @param $pbk
     * @return string
     * Criptografando o payload com a chave publica
     */
    public static function encryptByPublicK($data, $pbk){
        if(is_resource($pbk)) {
            $pbk = (stream_get_contents($pbk));
        }
        if(!Strings::isBinary($pbk)){
            $pbk =  sodium_hex2bin($pbk);
        }

        return sodium_bin2hex(sodium_crypto_box_seal($data, $pbk));
    }

    public static function decryptPayloadByPrivate($payload, $private_key){
        $crypt = new self($private_key);

        $encryptedSecretBox = hex2bin($payload['encryptedSecretBoxHex']);
        $plain_text = sodium_crypto_box_seal_open($encryptedSecretBox, ($crypt->key_pair));
        if($plain_text===false){
            \Yii::error("Fail to decryptPayload", "api");
            throw new UnauthorizedHttpException("Fail to decryptPayload");
        }
        return $plain_text;
    }

    public static function encryptFileBySecret($path, $pass){
        $password = $pass;
        $inputFile = $path;
        $encryptedFile = $path.'.enc';
        $chunkSize = 4096;

        $alg = SODIUM_CRYPTO_PWHASH_ALG_DEFAULT;
        $opsLimit = SODIUM_CRYPTO_PWHASH_OPSLIMIT_MODERATE;
        $memLimit = SODIUM_CRYPTO_PWHASH_MEMLIMIT_MODERATE;
        $salt = random_bytes(SODIUM_CRYPTO_PWHASH_SALTBYTES);

        $secretKey = sodium_crypto_pwhash(
            SODIUM_CRYPTO_SECRETSTREAM_XCHACHA20POLY1305_KEYBYTES,
            $password,
            $salt,
            $opsLimit,
            $memLimit,
            $alg
        );

        $fdIn = fopen($inputFile, 'rb');
        $fdOut = fopen($encryptedFile, 'wb');

        fwrite($fdOut, pack('C', $alg));
        fwrite($fdOut, pack('P', $opsLimit));
        fwrite($fdOut, pack('P', $memLimit));
        fwrite($fdOut, $salt);

        [$stream, $header] = sodium_crypto_secretstream_xchacha20poly1305_init_push($secretKey);

        fwrite($fdOut, $header);

        $tag = SODIUM_CRYPTO_SECRETSTREAM_XCHACHA20POLY1305_TAG_MESSAGE;
        do {
            $chunk = fread($fdIn, $chunkSize);
            if (feof($fdIn)) {
                $tag = SODIUM_CRYPTO_SECRETSTREAM_XCHACHA20POLY1305_TAG_FINAL;
            }

            $encryptedChunk = sodium_crypto_secretstream_xchacha20poly1305_push($stream, $chunk, '', $tag);
            fwrite($fdOut, $encryptedChunk);
        } while ($tag !== SODIUM_CRYPTO_SECRETSTREAM_XCHACHA20POLY1305_TAG_FINAL);

        fclose($fdOut);
        fclose($fdIn);
        unlink($path);
        return $chunkSize;
    }

    public static function decryptFileBySecret($encryptedFile,$password, $chunkSize = 4096){
        $decrypt = '';

        $fdIn = fopen($encryptedFile, 'rb');

        $alg = unpack('C', fread($fdIn, 1))[1];
        $opsLimit = unpack('P', fread($fdIn, 8))[1];
        $memLimit = unpack('P', fread($fdIn, 8))[1];
        $salt = fread($fdIn, SODIUM_CRYPTO_PWHASH_SALTBYTES);

        $header = fread($fdIn, SODIUM_CRYPTO_SECRETSTREAM_XCHACHA20POLY1305_HEADERBYTES);

        $secretKey = sodium_crypto_pwhash(
            SODIUM_CRYPTO_SECRETSTREAM_XCHACHA20POLY1305_KEYBYTES,
            $password,
            $salt,
            $opsLimit,
            $memLimit,
            $alg
        );

        $stream = sodium_crypto_secretstream_xchacha20poly1305_init_pull($header, $secretKey);
        do {
            $chunk = fread($fdIn, $chunkSize + SODIUM_CRYPTO_SECRETSTREAM_XCHACHA20POLY1305_ABYTES);
            $res = sodium_crypto_secretstream_xchacha20poly1305_pull($stream, $chunk);

            if ($res === false) {
                break;
            }

            [$decrypted_chunk, $tag] = $res;
            $decrypt = $decrypted_chunk;
        } while (!feof($fdIn) && $tag !== SODIUM_CRYPTO_SECRETSTREAM_XCHACHA20POLY1305_TAG_FINAL);
        $ok = feof($fdIn);

        fclose($fdIn);

        if (!$ok) {
            die('Invalid/corrupted input');
        }
        return $decrypt;
    }

    /**
     * @throws \SodiumException
     */
    public static function decryptByPrivateK($data, $privateKey){
        if(is_resource($data)) {
            $data = (stream_get_contents($data));
        }

        if(is_resource($privateKey)) {
            $privateKey = (stream_get_contents($privateKey));
        }

        if(!Strings::isBinary($privateKey)){
            $privateKey =  sodium_hex2bin($privateKey);
        }
        $crypt = new self($privateKey);
        return (sodium_crypto_box_seal_open(sodium_hex2bin($data), $crypt->key_pair));
    }

    public static function generateKeyPair($salt){
        $coreKeyPair = hex2bin(hash('sha512',"$salt".bin2hex(random_bytes(64))));// = \Sodium\crypto_box_keypair()
        $corePrivateKey = sodium_crypto_box_secretkey($coreKeyPair);
        $corePublicKey = sodium_crypto_box_publickey_from_secretkey($corePrivateKey);
        $class = new self();
        $class->private_key = $corePrivateKey;
        $class->public_key = $corePublicKey;
        $class->private_key_hex = sodium_bin2hex($corePrivateKey);
        $class->public_key_hex = sodium_bin2hex($corePublicKey);
        return $class;
    }

    public static function safeWriteInFile(string $path, string $content, $secret){
        $myfile = fopen($path, "w+");
        fwrite($myfile, ($content));
        fclose($myfile);

        return self::encryptFileBySecret($path, $secret);
    }

    public static function getOurKeyPair(){
        if (!file_exists(self::PATH_KEYS)) {
            mkdir(self::PATH_KEYS, 0700, true);
        }

        if(!file_exists(self::PATH_KEYS."server.key.enc")){
            $keypair = Crypt::generateKeyPair(Password::generateApiKey(time()));
            Crypt::safeWriteInFile(self::PATH_KEYS."server.key",$keypair->private_key,Crypt::getOurSecret());
            return $keypair;
        }else{
            $private_key = Crypt::decryptFileBySecret(self::PATH_KEYS."server.key.enc", Crypt::getOurSecret());
            $keypair = new Crypt($private_key);
            return $keypair;
        }
    }



}