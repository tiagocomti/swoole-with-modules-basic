<?php


namespace app\helpers;


class TOTP extends OTP
{
    /**
     * The interval in seconds for a one-time password timeframe
     * Defaults to 30
     * @var integer
     */
    public $interval;
    public $timecode;

    /**
     * TOTP constructor.
     * @param $s
     * @param array $opt
     * @example Array('interval' => 30, 'digits' => 6)
     */
    public function __construct($s, $opt = Array()) {
        if(Strings::isBinary($s) === false){$s = self::Desencriptar($s);}
        $this->interval = isset($opt['interval']) ? $opt['interval'] : 30;
        parent::__construct(self::Desencriptar($s), $opt);
    }

    /**
     *  Get the password for a specific timestamp value
     *
     *  @param integer $timestamp the timestamp which is timecoded and
     *  used to seed the hmac hash function.
     *  @return integer the One Time Password
     */
    public function atBase32($timestamp) {
        return $this->generateOTPBase32($this->timecode($timestamp));
    }

    public function atBase16($timestamp) {
        return $this->generateOTPBase16($this->timecode($timestamp));
    }

    /**
     *  Get the password for the current timestamp value
     *
     *  @return integer the current One Time Password
     */
    public function nowBase32() {
        return $this->generateOTPBase32($this->timecode(time()));
    }

    public function nowBase16() {
        return $this->generateOTPBase16($this->timecode(time()));
    }

    /**
     * Transform a timestamp in a counter based on specified internal
     *
     * @param integer $timestamp
     * @return integer the timecode
     */
    protected function timecode($timestamp)
    {
        $this->timecode = (int)((((int)$timestamp * 1000) / ($this->interval * 1000)));
        if ($this->debug) {
            echo "Momento informado = " . date('H:i:s', $this->timecode) . "(epoch {$this->timecode})\n";
        }
        if ($this->debug) var_dump(__METHOD__, $timestamp, $this->timecode, "(int) ( (((int) $timestamp * 1000) / ($this->interval * 1000)));");
        return $this->timecode;
    }

    public static function validate($secret, $token, $interval = 30, $digits = 6)
    {
        if (empty($token)) {
            return false;
        }
        $otpSeed = new self(self::Desencriptar($secret), Array('interval' => $interval, 'digits' => $digits));
        $time = time();
        for ($iOtp = 0; $iOtp < 90; $iOtp += 30) {
            $otp = $otpSeed->atBase32($time - $iOtp);
            if ($otp == trim($token)) {
                return true;
            }
        }
        return false;
    }
}