<?php
namespace First;
use First\Whiteips;
use First\FixedBitNotation;

class Helper
{

    public function white_ips_check()
    {

        $guest_ip=$_SERVER["REMOTE_ADDR"];
        $boolean= '';
        //引入白名單
        $ip_Arr_Obj = new Whiteips();
        $ip_Arr = $ip_Arr_Obj->white_ips_arr();
        //白名單使用者回傳1　不是則回傳0
        if (in_array($guest_ip,$ip_Arr)) {
            $boolean = 1;
        }else{
            $boolean = 0;
        }

        return $boolean;

    }

    //2FA input = text 驗證
    public function checkCode($secret, $code)
    {
        $time = floor(time() / 30);
        for ($i = -1; $i <= 1; ++$i) {
            if ($this->codesEqual($this->getCode($secret, $time + $i), $code)) {
                return true;
            }
        }

        return false;
    }

    private function codesEqual($known, $given)
    {
        if (strlen($given) !== strlen($known)) {
            return false;
        }

        $res = 0;

        $knownLen = strlen($known);

        for ($i = 0; $i < $knownLen; ++$i) {
            $res |= (ord($known[$i]) ^ ord($given[$i]));
        }

        return $res === 0;
    }

    public function getCode($secret, $time = null)
    {
        if (!$time) {
            $time = floor(time() / 30);
        }

        $base32 = new FixedBitNotation(5, 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567', true, true);
        $secret = $base32->decode($secret);

        $time = pack('N', $time);
        $time = str_pad($time, 8, chr(0), STR_PAD_LEFT);

        $hash = hash_hmac('sha1', $time, $secret, true);
        $offset = ord(substr($hash, -1));
        $offset = $offset & 0xF;

        $truncatedHash = self::hashToInt($hash, $offset) & 0x7FFFFFFF;
        $pinValue = str_pad($truncatedHash % $this->pinModulo, 6, '0', STR_PAD_LEFT);

        return $pinValue;
    }

}