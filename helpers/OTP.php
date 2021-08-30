<?php

/*
 * Copyright (c) 2011 Le Lag
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.

 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */
namespace app\helpers;
use app\helpers\Base32;

/**
 * One Time Password Generator
 *
 * The OTP class allow the generation of one-time
 * password that is described in rfc 4xxx.
 *
 * This is class is meant to be compatible with
 * Google Authenticator.
 *
 * This class was originally ported from the rotp
 * ruby library available at https://github.com/mdp/rotp
 */
class OTP
{
    /**
     * The base32 encoded secret key
     * @var string
     */
    public $secret;
    /**
     * The algorithm used for the hmac hash function
     * @var string
     */
    public $digest;

    /**
     * The number of digits in the one-time password
     * @var integer
     */
    public $digits;
    public $debug = false;

    /**
     * Constructor for the OTP class
     * @param string $secret the secret key
     * @param array $opt options array can contain the
     * following keys :
     */
    public function __construct($secret, $opt = Array()) {
        $this->digits = isset($opt['digits']) ? $opt['digits'] : 6;
        $this->digest = isset($opt['digest']) ? $opt['digest'] : 'sha1';
        $this->secret = strtoupper($secret);
    }

    /**
     * Generate a one-time password
     *
     * @param integer $input : number used to seed the hmac hash function.
     * This number is usually a counter (HOTP) or calculated based on the current
     * timestamp (see TOTP class).
     * @return integer the one-time password
     */
    public function generateOTPBase32($input) {
        $hmac = [];
        $hash = hash_hmac($this->digest, $this->intToBytestring($input), $this->byteSecret());
        foreach (str_split($hash, 2) as $hex) { // stupid PHP has bin2hex but no hex2bin WTF
            $hmac[] = hexdec($hex);
        }
        return $this->algorithmRFC2FA($hmac);
    }

    public function generateOTPBase16($input) {
        $hmac = [];
        $hash = hash_hmac($this->digest, $this->intToBytestring($input), hex2bin($this->secret));
        foreach (str_split($hash, 2) as $hex) {
            $hmac[] = hexdec($hex);
        }
        return $this->algorithmRFC2FA($hmac);
    }

    private function algorithmRFC2FA($hmac) {
        $offset = $hmac[19] & 0xf;
        $code = ($hmac[$offset + 0] & 0x7F) << 24 |
            ($hmac[$offset + 1] & 0xFF) << 16 |
            ($hmac[$offset + 2] & 0xFF) << 8 |
            ($hmac[$offset + 3] & 0xFF);
        $codeReturn = $code % pow(10, $this->digits);
        return str_pad($codeReturn, $this->digits, '0', STR_PAD_LEFT);
    }

    /**
     * Returns the binary value of the base32 encoded secret
     * @access private
     * This method should be private but was left public for
     * phpunit tests to work.
     * @return bool|string|void
     */
    public function byteSecret() {
        return Base32::decode($this->secret);
    }

    /**
     * Turns an integer in a OATH bytestring
     * @param integer $int
     * @access private
     * @return string bytestring
     */
    public function intToBytestring($int) {
        $result = Array();
        while ($int != 0) {
            $result[] = chr($int & 0xFF);
            $int >>= 8;
        }
        return str_pad(join(array_reverse($result)), 8, "\000", STR_PAD_LEFT);
    }

    static function Randomizar($iv_len) {
        $iv = '';
        while ($iv_len-- > 0) {
            $iv .= chr(mt_rand() & 0xff);
        }
        return $iv;
    }

    static function Encriptar($texto, $iv_len = 16, $uid = "privatum@privatum.com.br") {
        $texto .= "\x13";
        $n = strlen($texto);
        if ($n % 16)
            $texto .= str_repeat("\0", 16 - ($n % 16));
        $i = 0;
        $Enc_Texto = self::Randomizar($iv_len);
        $iv = substr(("jhasdf37ksajdfh*lsjdhf.jhsdf".$uid) ^ $Enc_Texto, 0, 512);
        while ($i < $n) {
            $Bloco = substr($texto, $i, 16) ^ pack('H*', md5($iv));
            $Enc_Texto .= $Bloco;
            $iv = substr($Bloco . $iv, 0, 512) ^ ("jhasdf37ksajdfh*lsjdhf.jhsdf".$uid);
            $i += 16;
        }
        return base64_encode($Enc_Texto);
    }

    static function Desencriptar($Enc_Texto, $iv_len = 16, $uid = "privatum@privatum.com.br") {
        $Enc_Texto = base64_decode($Enc_Texto);
        $n = strlen($Enc_Texto);
        $i = $iv_len;
        $texto = '';
        $iv = substr(("jhasdf37ksajdfh*lsjdhf.jhsdf".$uid) ^ substr($Enc_Texto, 0, $iv_len), 0, 512);
        while ($i < $n) {
            $Bloco = substr($Enc_Texto, $i, 16);
            $texto .= $Bloco ^ pack('H*', md5($iv));
            $iv = substr($Bloco . $iv, 0, 512) ^ ("jhasdf37ksajdfh*lsjdhf.jhsdf".$uid);
            $i += 16;
        }
        return preg_replace('/\\x13\\x00*$/', '', $texto);
    }

}